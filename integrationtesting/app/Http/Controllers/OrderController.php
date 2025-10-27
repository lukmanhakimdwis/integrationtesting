<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('product')->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get(['id','name','price','stok']);
        return view('orders.create', compact('products'));
    }

    public function store(Request $request, PaymentService $payments)
    {
        $validated = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['required','integer','min:1'],
        ]);

        $product = Product::findOrFail((int) $validated['product_id']);
        $qty = (int) $validated['qty'];

        [$order, $product, $trx] = DB::transaction(function () use ($product, $qty, $payments) {
            if ($product->stok < $qty) {
                abort(422, 'Stok tidak cukup');
            }
            $order = Order::create([
                'product_id' => $product->id,
                'qty'        => $qty,
                'status'     => 'pending',
            ]);
            // proses bayar -> paid
            $amount = $qty * $product->price;
            $trxId = $payments->charge($order, $amount);
            $product->decrement('stok', $qty);
            $order->update(['status' => 'paid', 'trx_id' => $trxId]);
            return [$order, $product->fresh(), $trxId];
        });

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'order' => $order->only(['id','status','qty','trx_id']), 'stok' => $product->stok]);
        }
        return redirect()->route('orders.index')->with('ok', 'Order berhasil dibuat. TRX ' . $trx);
    }

    public function edit(Order $order)
    {
        $products = Product::orderBy('name')->get(['id','name','price','stok']);
        return view('orders.edit', compact('order','products'));
    }

    public function update(Request $request, Order $order, PaymentService $payments)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['required','integer','min:1'],
            'status'     => ['required','in:pending,paid'],
        ]);

        // Batasi: hanya pending yang bisa diubah qty/produk
        if ($order->status !== 'pending' && ($data['product_id'] != $order->product_id || $data['qty'] != $order->qty)) {
            return back()->withErrors(['msg' => 'Order paid tidak bisa diubah item/qty.'])->withInput();
        }

        return DB::transaction(function () use ($order, $data, $payments) {
            // Jika masih pending, update item & qty
            if ($order->status === 'pending') {
                $order->product_id = (int) $data['product_id'];
                $order->qty = (int) $data['qty'];
            }

            // Transisi status
            if ($order->status === 'pending' && $data['status'] === 'paid') {
                $product = Product::findOrFail($order->product_id);
                if ($product->stok < $order->qty) {
                    return back()->withErrors(['msg' => 'Stok tidak cukup'])->withInput();
                }
                $amount = $order->qty * $product->price;
                $trxId = $payments->charge($order, $amount);
                $product->decrement('stok', $order->qty);
                $order->status = 'paid';
                $order->trx_id = $trxId;
            }

            $order->save();
            return redirect()->route('orders.index')->with('ok', 'Order diperbarui.');
        });
    }

    public function destroy(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Jika paid -> pulihkan stok dan hapus log pembayaran
            if ($order->status === 'paid') {
                $productId = $order->product_id;
                $qty = $order->qty;
                $order->product()->increment('stok', $qty);
                PaymentLog::where('order_id', $order->id)->delete();
            }
            $order->delete();
            return redirect()->route('orders.index')->with('ok', 'Order dihapus.');
        });
    }
}

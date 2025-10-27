<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'price' => ['required','integer','min:0'],
            'stok'  => ['required','integer','min:0'],
        ]);

        Product::create($data);
        return redirect()->route('products.index')->with('ok', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:100'],
            'price' => ['required','integer','min:0'],
            'stok'  => ['required','integer','min:0'],
        ]);

        $product->update($data);
        return redirect()->route('products.index')->with('ok', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Catatan: jika produk sudah digunakan order, constraint FK akan mencegah delete.
        $product->delete();
        return redirect()->route('products.index')->with('ok', 'Produk dihapus.');
    }
}

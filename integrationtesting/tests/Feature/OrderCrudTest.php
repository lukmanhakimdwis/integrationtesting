<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Order;
use App\Models\PaymentLog;

class OrderCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order_via_form_paid_and_decrease_stock(): void
    {
        Http::fake(['api.payment.local/*' => Http::response(['trx_id' => 'trx_ui_form'], 200)]);
        $p = Product::factory()->create(['price' => 10000, 'stok' => 5]);
        $resp = $this->post('/orders', ['product_id' => $p->id, 'qty' => 2]);
        $resp->assertRedirect('/orders');
        $this->assertDatabaseHas('orders', ['product_id' => $p->id, 'qty' => 2, 'status' => 'paid', 'trx_id' => 'trx_ui_form']);
        $this->assertSame(3, $p->fresh()->stok);
    }

    public function test_delete_paid_order_restores_stock_and_removes_logs(): void
    {
        Http::fake(['api.payment.local/*' => Http::response(['trx_id' => 'trx_del_1'], 200)]);
        $p = Product::factory()->create(['price' => 10000, 'stok' => 5]);
        $this->post('/orders', ['product_id' => $p->id, 'qty' => 2])->assertRedirect('/orders');
        $o = Order::first();
        $this->assertNotNull($o);
        $this->assertDatabaseHas('payment_logs', ['order_id' => $o->id]);

        $resp = $this->delete("/orders/{$o->id}");
        $resp->assertRedirect('/orders');
        $this->assertSame(5, $p->fresh()->stok);
        $this->assertDatabaseMissing('payment_logs', ['order_id' => $o->id]);
        $this->assertDatabaseMissing('orders', ['id' => $o->id]);
    }

    public function test_update_pending_order_to_paid_through_edit(): void
    {
        Http::fake(['api.payment.local/*' => Http::response(['trx_id' => 'trx_edit_paid'], 200)]);
        $p = Product::factory()->create(['price' => 10000, 'stok' => 5]);
        // Buat pending secara manual
        $o = Order::create(['product_id' => $p->id, 'qty' => 2, 'status' => 'pending']);

        $resp = $this->put("/orders/{$o->id}", ['product_id' => $p->id, 'qty' => 2, 'status' => 'paid']);
        $resp->assertRedirect('/orders');
        $this->assertDatabaseHas('orders', ['id' => $o->id, 'status' => 'paid', 'trx_id' => 'trx_edit_paid']);
        $this->assertSame(3, $p->fresh()->stok);
    }
}

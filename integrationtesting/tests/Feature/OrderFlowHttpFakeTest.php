<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class OrderFlowHttpFakeTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_flow_success_with_http_fake(): void
    {
        Http::fake(['api.payment.local/*' => Http::response(['trx_id' => 'trx_httpfake_123'], 200)]);
        $product = Product::factory()->create(['price' => 200000, 'stok' => 10]);
        $resp = $this->postJson('/orders', ['product_id' => $product->id, 'qty' => 2]);
        $resp->assertOk()->assertJsonPath('order.status', 'paid');
        $this->assertDatabaseHas('orders', [
            'product_id' => $product->id, 'qty' => 2, 'status' => 'paid', 'trx_id' => 'trx_httpfake_123'
        ]);
    }
}

<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class FormOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_order_flow_redirects_and_creates_order(): void
    {
        Http::fake(['api.payment.local/*' => Http::response(['trx_id' => 'trx_ui_001'], 200)]);
        $product = Product::factory()->create(['price' => 200000, 'stok' => 5]);

        $resp = $this->post('/orders', ['product_id' => $product->id, 'qty' => 2]);
        $resp->assertRedirect('/orders');

        $this->assertDatabaseHas('orders', ['product_id' => $product->id, 'qty' => 2, 'status' => 'paid', 'trx_id' => 'trx_ui_001']);
    }
}

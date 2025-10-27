<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Contracts\PaymentGateway;
use App\Models\Product;

class OrderFlowMockeryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_order_flow_success_with_mockery_top_down(): void
    {
        $product = Product::factory()->create(['price' => 200000, 'stok' => 10]);
        $mock = \Mockery::mock(PaymentGateway::class);
        $mock->shouldReceive('charge')
            ->once()
            ->withArgs(function ($orderId, $amount) {
                return is_int($orderId) && $amount === 400000;
            })
            ->andReturn('trx_mockery_td_456');
        $this->app->instance(PaymentGateway::class, $mock);

        $resp = $this->postJson('/orders', ['product_id' => $product->id, 'qty' => 2]);
        $resp->assertOk()->assertJsonPath('order.trx_id', 'trx_mockery_td_456');
        $this->assertDatabaseHas('orders', ['product_id' => $product->id, 'qty' => 2, 'status' => 'paid', 'trx_id' => 'trx_mockery_td_456']);
    }
}

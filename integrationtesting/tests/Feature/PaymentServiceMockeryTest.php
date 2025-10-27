<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Contracts\PaymentGateway;
use App\Services\PaymentService;
use App\Models\Order;

class PaymentServiceMockeryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_service_records_log_and_calls_gateway_once(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);
        $mock = \Mockery::mock(PaymentGateway::class);
        $mock->shouldReceive('charge')->once()->with($order->id, 123456)->andReturn('trx_mockery_bu_789');
        $this->app->instance(PaymentGateway::class, $mock);

        app(PaymentService::class)->charge($order, 123456);
        $this->assertDatabaseHas('payment_logs', ['order_id' => $order->id, 'amount' => 123456, 'trx_id' => 'trx_mockery_bu_789']);
    }
}

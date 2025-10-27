<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Order;
use App\Models\PaymentLog;

class PaymentService
{
    public function __construct(private PaymentGateway $gateway) {}

    public function charge(Order $order, int $amount): string
    {
        $trxId = $this->gateway->charge($order->id, $amount);
        PaymentLog::create([
            'order_id' => $order->id,
            'amount'   => $amount,
            'trx_id'   => $trxId,
        ]);
        return $trxId;
    }
}

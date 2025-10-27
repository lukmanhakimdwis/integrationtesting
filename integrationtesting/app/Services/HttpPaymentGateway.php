<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Facades\Http;

class HttpPaymentGateway implements PaymentGateway
{
    public function charge(int $orderId, int $amount): string
    {
        $response = Http::post('https://api.payment.local/charges', [
            'order_id' => $orderId,
            'amount' => $amount,
        ])->throw();

        $data = $response->json();
        return $data['trx_id'] ?? $data['id'] ?? 'trx_fake_' . $orderId;
    }
}

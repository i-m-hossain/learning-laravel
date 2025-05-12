<?php
namespace App\Services\PaymentGateWays;

use App\Contracts\PaymentGateway;

class PayPalGateway implements PaymentGateway
{
    public function charge(float $amount): string
    {
        return "Charged {$amount} via PayPal";
    }
}
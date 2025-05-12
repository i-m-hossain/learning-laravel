<?php
namespace App\Services\PaymentGateWays;

use App\Contracts\PaymentGateway;

class StripeGateway implements PaymentGateway{

    public function charge(float $amount): string
    {
        return "Charged {$amount} via Stripe"; 
    }
}
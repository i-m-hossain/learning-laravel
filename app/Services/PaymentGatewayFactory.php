<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Services\PaymentGateWays\BkashGateWay;
use App\Services\PaymentGateWays\PayPalGateway;
use App\Services\PaymentGateWays\StripeGateway;

class PaymentGatewayFactory
{
    public function create(string $type): PaymentGateway
    {
        return match ($type) {
            'stripe' => app(StripeGateway::class),
            'paypal' => app(PayPalGateway::class),
            'bkash' => app(BkashGateWay::class),
            default => throw new \InvalidArgumentException("Unknown gateway: $type")
        };
    }
}

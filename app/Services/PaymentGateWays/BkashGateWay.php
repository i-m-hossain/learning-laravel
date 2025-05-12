<?php

namespace App\Services\PaymentGateWays;

use App\Contracts\PaymentGateway;

class BkashGateWay implements PaymentGateway
{
    public function charge(float $amount): string
    {
        return "charged $amount via bkash";
    }

}
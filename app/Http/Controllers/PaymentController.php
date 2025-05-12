<?php

namespace App\Http\Controllers;
use App\Services\PaymentGatewayFactory;



class PaymentController extends Controller
{
    public function handlePayment(string $gateway, PaymentGatewayFactory $factory)
    {
        $gateway = $factory->create($gateway);
        return $gateway->charge(request()->amount);
    }
}

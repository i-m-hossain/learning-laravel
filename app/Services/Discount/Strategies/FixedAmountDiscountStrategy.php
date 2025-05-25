<?php

namespace App\Services\Discount\Strategies;

class FixedAmountDiscountStrategy implements DiscountStrategyInterface{

    private float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function calculate(float $price, array $context = []): float
    {
        return min($this->amount, $price); // Cannot discount more than the price
    }

    public function getDescription(): string
    {
        return "₹{$this->amount} off";
    }
}
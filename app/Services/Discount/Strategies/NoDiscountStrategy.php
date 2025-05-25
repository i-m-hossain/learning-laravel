<?php 

namespace App\Services\Discount\Strategies;

class NoDiscountStrategy implements DiscountStrategyInterface
{
    public function calculate(float $price, array $context = []): float
    {
        return 0; // No discount
    }

    public function getDescription(): string
    {
        return "No discount";
    }
}
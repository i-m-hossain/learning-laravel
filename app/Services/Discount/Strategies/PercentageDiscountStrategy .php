<?php 

namespace App\Services\Discount\Strategies;

class PercentageDiscountStrategy implements DiscountStrategyInterface{

    private float $percentage;

    public function __construct(float $percentage)
    {
        $this->percentage = $percentage;
    }

    public function calculate(float $price, array $context = []): float
    {
        return $price * $this->percentage / 100;
    }

    public function getDescription(): string
    {
        return "{$this->percentage}% off";
    }
}
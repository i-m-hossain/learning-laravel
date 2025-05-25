<?php

namespace App\Services\Discount;

use App\Services\Discount\Strategies\DiscountStrategyInterface;
use App\Services\Discount\Strategies\NoDiscountStrategy;

class DiscountCalculator
{
    private DiscountStrategyInterface $strategy;

    public function __construct(?DiscountStrategyInterface $strategy = null)
    {
        $this->strategy = $strategy ?? new NoDiscountStrategy();
    }

    public function setStrategy(DiscountStrategyInterface $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function calculate(float $price, array $context = []): float
    {
        return $this->strategy->calculate($price, $context);
    }

    public function getDiscountedPrice(float $price, array $context = []): float
    {
        $discount = $this->calculate($price, $context);
        return $price - $discount;
    }

    public function getDescription(): string
    {
        return $this->strategy->getDescription();
    }
}

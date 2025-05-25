<?php

namespace App\Services\Discount\Strategies;

class BuyXGetYDiscountStrategy  implements DiscountStrategyInterface
{
    private int $buyQuantity;
    private int $freeQuantity;

    public function __construct(int $buyQuantity, int $freeQuantity)
    {
        $this->buyQuantity = $buyQuantity;
        $this->freeQuantity = $freeQuantity;
    }

    public function calculate(float $price, array $context = []): float
    {
        $quantity = $context['quantity'] ?? 1;

        // Calculate how many free items the customer gets
        $sets = floor($quantity / ($this->buyQuantity + $this->freeQuantity));
        $freeItems = $sets * $this->freeQuantity;

        // Free items can't exceed the actual quantity
        $freeItems = min($freeItems, $quantity);

        return $freeItems * $price;
    }

    public function getDescription(): string
    {
        return "Buy {$this->buyQuantity} get {$this->freeQuantity} free";
    }
}

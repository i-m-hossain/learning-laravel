<?php
namespace App\Services\Discount\Strategies;
interface DiscountStrategyInterface{
    public function calculate(float $price, array $context = []): float;
    public function getDescription(): string;
}
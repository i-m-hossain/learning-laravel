<?php

namespace App\Services\Discount\Factories;

use App\Models\Product;
use App\Services\Discount\Strategies\DiscountStrategyInterface;
use App\Services\Discount\Strategies\PercentageDiscountStrategy;
use App\Services\Discount\Strategies\FixedAmountDiscountStrategy;
use App\Services\Discount\Strategies\BuyXGetYDiscountStrategy;
use App\Services\Discount\Strategies\NoDiscountStrategy;

class DiscountStrategyFactory
{
    /**
     * Create the appropriate discount strategy based on product attributes
     */
    public function createFromProduct(Product $product, int $quantity): DiscountStrategyInterface
    {
        // Check for product category-specific strategies
        if ($product->category_id === 1) { // Electronics
            return new PercentageDiscountStrategy(10);
        } elseif ($product->category_id === 2) { // Clothing
            return new BuyXGetYDiscountStrategy(1, 1);
        } elseif ($product->price > 1000) { // Expensive items
            return new FixedAmountDiscountStrategy(100);
        }

        // Default strategy when no conditions are met
        return new NoDiscountStrategy();
    }

    /**
     * Alternative factory method for creating strategies from promotion codes
     */
    public function createFromPromotionCode(string $promoCode): DiscountStrategyInterface
    {
        // This could check a database or promotion service
        switch ($promoCode) {
            case 'WELCOME10':
                return new PercentageDiscountStrategy(10);
            case 'FLAT100':
                return new FixedAmountDiscountStrategy(100);
            case 'BOGO':
                return new BuyXGetYDiscountStrategy(1, 1);
            default:
                return new NoDiscountStrategy();
        }
    }
}

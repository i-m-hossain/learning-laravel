<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ensure orders and products exist
        if (Order::count() === 0 || Product::count() === 0) {
            \App\Models\User::factory()->count(3)->create();
            \App\Models\Order::factory()->count(10)->create();
            \App\Models\Product::factory()->count(10)->create();
        }

        $orderIds = Order::pluck('id')->toArray();
        $productIds = Product::pluck('id')->toArray();

        foreach (range(1, 50) as $i) {
            OrderItem::create([
                'order_id' => $faker->randomElement($orderIds),
                'product_id' => $faker->randomElement($productIds),
                'quantity' => $faker->numberBetween(1, 5),
                'price' => $faker->randomFloat(2, 5, 200),
            ]);
        }
    }
}

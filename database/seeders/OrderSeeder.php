<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Make sure you have users first, or generate them if needed
        if (User::count() === 0) {
            \App\Models\User::factory()->count(5)->create();
        }

        $userIds = User::pluck('id')->toArray();

        foreach (range(1, 20) as $i) {
            Order::create([
                'user_id' => $faker->randomElement($userIds),
                'total_amount' => $faker->randomFloat(2, 10, 500),
                'status' => $faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
                'payment_status' => $faker->randomElement(['paid', 'unpaid', 'failed']),
                'shipping_address' => $faker->address,
                'billing_address' => $faker->address,
                'payment_method' => $faker->randomElement(['card', 'cash', 'paypal']),
            ]);
        }
    }
}

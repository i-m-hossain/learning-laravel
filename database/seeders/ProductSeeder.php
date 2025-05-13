<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create categories if none exist
        if (Category::count() === 0) {
            Category::factory()->count(5)->create();
        }

        $categoryIds = Category::pluck('id')->toArray();

        foreach (range(1, 30) as $i) {
            Product::create([
                'name' => $faker->words(3, true),
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 10, 500),
                'stock' => $faker->numberBetween(0, 100),
                'category_id' => $faker->randomElement($categoryIds),
                'sku' => strtoupper($faker->unique()->bothify('SKU###??')),
                'is_active' => $faker->boolean(80),
            ]);
        }
    }
}

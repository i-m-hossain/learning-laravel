<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $mainCategories = [
            ['name' => 'Electronics', 'description' => 'Electronic gadgets and devices'],
            ['name' => 'Books', 'description' => 'All genres of books'],
            ['name' => 'Clothing', 'description' => 'Apparel for men and women'],
            ['name' => 'Home & Kitchen', 'description' => 'Home appliances and kitchenware'],
            ['name' => 'Beauty', 'description' => 'Beauty and personal care products'],
        ];

        foreach ($mainCategories as $main) {
            $parent = Category::create([
                'name' => $main['name'],
                'slug' => Str::slug($main['name']),
                'description' => $main['description'],
                'parent_id' => null
            ]);

            // Create 2 subcategories for each
            for ($i = 1; $i <= 2; $i++) {
                $childName = $main['name'] . " Sub $i";
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'description' => $childName . " description",
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}

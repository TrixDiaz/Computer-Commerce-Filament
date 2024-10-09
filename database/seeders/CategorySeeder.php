<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptops', 'slug' => 'laptops'],
            ['name' => 'Desktops', 'slug' => 'desktops'],
            ['name' => 'Tablets', 'slug' => 'tablets'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
            ['name' => 'Components', 'slug' => 'components',],
        ];

        foreach ($categories as $category) {
            $cat = Category::create($category);
            // Attach random brands to each category
            $cat->brands()->attach(Brand::inRandomOrder()->take(rand(1, 3))->pluck('id'));
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Dell XPS 13', 'slug' => 'dell-xps-13', 'description' => 'Powerful ultrabook', 'price' => 1299.99, 'stock_quantity' => 50, 'sku' => 'DELL-XPS-13'],
            ['name' => 'HP Spectre x360', 'slug' => 'hp-spectre-x360', 'description' => 'Convertible laptop', 'price' => 1399.99, 'stock_quantity' => 30, 'sku' => 'HP-SPECTRE-X360'],
            ['name' => 'Apple MacBook Pro', 'slug' => 'apple-macbook-pro', 'description' => 'Professional-grade laptop', 'price' => 1999.99, 'stock_quantity' => 25, 'sku' => 'APPLE-MBP'],
            ['name' => 'Lenovo ThinkPad X1', 'slug' => 'lenovo-thinkpad-x1', 'description' => 'Business laptop', 'price' => 1499.99, 'stock_quantity' => 40, 'sku' => 'LENOVO-X1'],
            ['name' => 'Asus ROG Zephyrus', 'slug' => 'asus-rog-zephyrus', 'description' => 'Gaming laptop', 'price' => 2199.99, 'stock_quantity' => 20, 'sku' => 'ASUS-ROG-ZEPHYRUS'],
        ];

        foreach ($products as $product) {
            $prod = Product::create(array_merge($product, [
                'brand_id' => Brand::inRandomOrder()->first()->id,
                'category_id' => Category::inRandomOrder()->first()->id,
                'is_active' => true,
                'images' => json_encode(['default.jpg']),
            ]));
        }
    }
}

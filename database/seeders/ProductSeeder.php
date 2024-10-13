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
            ['name' => 'Dell XPS 13', 'slug' => 'dell-xps-13', 'description' => 'Powerful ultrabook', 'price' => 1299.99, 'original_price' => 1599.99, 'stock_quantity' => 50, 'sku' => 'DELL-XPS-13'],
            ['name' => 'HP Spectre x360', 'slug' => 'hp-spectre-x360', 'description' => 'Convertible laptop', 'price' => 1399.99, 'original_price' => 1699.99, 'stock_quantity' => 30, 'sku' => 'HP-SPECTRE-X360'],
            ['name' => 'Apple MacBook Pro', 'slug' => 'apple-macbook-pro', 'description' => 'Professional-grade laptop', 'price' => 1999.99, 'original_price' => 2299.99, 'stock_quantity' => 25, 'sku' => 'APPLE-MBP'],
            ['name' => 'Lenovo ThinkPad X1', 'slug' => 'lenovo-thinkpad-x1', 'description' => 'Business laptop', 'price' => 1499.99, 'original_price' => 1799.99, 'stock_quantity' => 40, 'sku' => 'LENOVO-X1'],
            ['name' => 'Asus ROG Zephyrus', 'slug' => 'asus-rog-zephyrus', 'description' => 'Gaming laptop', 'price' => 2199.99, 'original_price' => 2499.99, 'stock_quantity' => 20, 'sku' => 'ASUS-ROG-ZEPHYRUS'],
            ['name' => 'Samsung Galaxy S21', 'slug' => 'samsung-galaxy-s21', 'description' => 'Flagship smartphone', 'price' => 799.99, 'original_price' => 999.99, 'stock_quantity' => 60, 'sku' => 'SAMSUNG-S21'],
            ['name' => 'Apple iPhone 13', 'slug' => 'apple-iphone-13', 'description' => 'Latest iPhone model', 'price' => 899.99, 'original_price' => 1099.99, 'stock_quantity' => 55, 'sku' => 'APPLE-IPHONE-13'],
            ['name' => 'Samsung Galaxy S22', 'slug' => 'samsung-galaxy-s22', 'description' => 'Flagship smartphone', 'price' => 999.99, 'original_price' => 1199.99, 'stock_quantity' => 50, 'sku' => 'SAMSUNG-S22'],
            ['name' => 'Apple iPhone 14', 'slug' => 'apple-iphone-14', 'description' => 'Latest iPhone model', 'price' => 1099.99, 'original_price' => 1299.99, 'stock_quantity' => 45, 'sku' => 'APPLE-IPHONE-14'],
            ['name' => 'Samsung Galaxy S23', 'slug' => 'samsung-galaxy-s23', 'description' => 'Flagship smartphone', 'price' => 1199.99, 'original_price' => 1399.99, 'stock_quantity' => 40, 'sku' => 'SAMSUNG-S23'],
            ['name' => 'Apple iPhone 15', 'slug' => 'apple-iphone-15', 'description' => 'Latest iPhone model', 'price' => 1299.99, 'original_price' => 1499.99, 'stock_quantity' => 35, 'sku' => 'APPLE-IPHONE-15'],
            ['name' => 'Samsung Galaxy S24', 'slug' => 'samsung-galaxy-s24', 'description' => 'Flagship smartphone', 'price' => 1399.99, 'original_price' => 1599.99, 'stock_quantity' => 30, 'sku' => 'SAMSUNG-S24'],
            ['name' => 'Apple iPhone 16', 'slug' => 'apple-iphone-16', 'description' => 'Latest iPhone model', 'price' => 1499.99, 'original_price' => 1699.99, 'stock_quantity' => 25, 'sku' => 'APPLE-IPHONE-16'],
            ['name' => 'Samsung Galaxy S25', 'slug' => 'samsung-galaxy-s25', 'description' => 'Flagship smartphone', 'price' => 1599.99, 'original_price' => 1799.99, 'stock_quantity' => 20, 'sku' => 'SAMSUNG-S25'],
            ['name' => 'Apple iPhone 17', 'slug' => 'apple-iphone-17', 'description' => 'Latest iPhone model', 'price' => 1699.99, 'original_price' => 1899.99, 'stock_quantity' => 15, 'sku' => 'APPLE-IPHONE-17'],
            ['name' => 'Samsung Galaxy S26', 'slug' => 'samsung-galaxy-s26', 'description' => 'Flagship smartphone', 'price' => 1799.99, 'original_price' => 1999.99, 'stock_quantity' => 10, 'sku' => 'SAMSUNG-S26'],
            ['name' => 'Apple iPhone 18', 'slug' => 'apple-iphone-18', 'description' => 'Latest iPhone model', 'price' => 1899.99, 'original_price' => 2099.99, 'stock_quantity' => 5, 'sku' => 'APPLE-IPHONE-18'],
            ['name' => 'Samsung Galaxy S27', 'slug' => 'samsung-galaxy-s27', 'description' => 'Flagship smartphone', 'price' => 1999.99, 'original_price' => 2199.99, 'stock_quantity' => 0, 'sku' => 'SAMSUNG-S27'],
            ['name' => 'Apple iPhone 19', 'slug' => 'apple-iphone-19', 'description' => 'Latest iPhone model', 'price' => 2099.99, 'original_price' => 2299.99, 'stock_quantity' => 0, 'sku' => 'APPLE-IPHONE-19'],
            ['name' => 'Samsung Galaxy S28', 'slug' => 'samsung-galaxy-s28', 'description' => 'Flagship smartphone', 'price' => 2199.99, 'original_price' => 2399.99, 'stock_quantity' => 0, 'sku' => 'SAMSUNG-S28'],
            ['name' => 'Apple iPhone 20', 'slug' => 'apple-iphone-20', 'description' => 'Latest iPhone model', 'price' => 2299.99, 'original_price' => 2499.99, 'stock_quantity' => 0, 'sku' => 'APPLE-IPHONE-20'],
            ['name' => 'Samsung Galaxy S29', 'slug' => 'samsung-galaxy-s29', 'description' => 'Flagship smartphone', 'price' => 2399.99, 'original_price' => 2599.99, 'stock_quantity' => 0, 'sku' => 'SAMSUNG-S29'],
            ['name' => 'Apple iPhone 21', 'slug' => 'apple-iphone-21', 'description' => 'Latest iPhone model', 'price' => 2499.99, 'original_price' => 2699.99, 'stock_quantity' => 0, 'sku' => 'APPLE-IPHONE-21'],
            ['name' => 'Samsung Galaxy S30', 'slug' => 'samsung-galaxy-s30', 'description' => 'Flagship smartphone', 'price' => 2599.99, 'original_price' => 2799.99, 'stock_quantity' => 0, 'sku' => 'SAMSUNG-S30'],
            ['name' => 'Apple iPhone 22', 'slug' => 'apple-iphone-22', 'description' => 'Latest iPhone model', 'price' => 2699.99, 'original_price' => 2899.99, 'stock_quantity' => 0, 'sku' => 'APPLE-IPHONE-22'],
            ['name' => 'Samsung Galaxy S31', 'slug' => 'samsung-galaxy-s31', 'description' => 'Flagship smartphone', 'price' => 2799.99, 'original_price' => 2999.99, 'stock_quantity' => 0, 'sku' => 'SAMSUNG-S31'],
            ['name' => 'Apple iPhone 23', 'slug' => 'apple-iphone-23', 'description' => 'Latest iPhone model', 'price' => 2899.99, 'original_price' => 3099.99, 'stock_quantity' => 0, 'sku' => 'APPLE-IPHONE-23'],
        ];

        foreach ($products as $product) {
            $prod = Product::create(array_merge($product, [
                'brand_id' => Brand::inRandomOrder()->first()->id,
                'category_id' => Category::inRandomOrder()->first()->id,
                'is_active' => true,
                'images' => null,
            ]));
        }
    }
}

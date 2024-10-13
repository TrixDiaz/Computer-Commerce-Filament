<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $customers = User::all();
        $orders = Order::all();
        foreach ($products as $product) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customers->random()->id,
                    'order_id' => $orders->random()->id,
                    'rating' => rand(1, 5),
                    'comment' => 'This is a sample review comment.',
                    'is_approved' => true,
                ]);
            }
        }
    }
}

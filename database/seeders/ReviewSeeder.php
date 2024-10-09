<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $products = Product::all();
        $customers = User::all();

        foreach ($products as $product) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $customers->random()->id,
                    'rating' => rand(1, 5),
                    'comment' => 'This is a sample review comment.',
                    'is_approved' => true,
                ]);
            }
        }
    }
}

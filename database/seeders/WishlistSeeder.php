<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();
            
            foreach ($products as $product) {
                Wishlist::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }
}

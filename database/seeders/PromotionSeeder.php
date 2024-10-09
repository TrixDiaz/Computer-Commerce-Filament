<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\Product;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [
            ['name' => 'Summer Sale', 'description' => 'Get 15% off on selected items', 'type' => 'percentage', 'value' => 15.00, 'start_date' => now(), 'end_date' => now()->addMonths(3)],
            ['name' => 'Clearance', 'description' => '$50 off on orders over $500', 'type' => 'fixed', 'value' => 50.00, 'start_date' => now(), 'end_date' => now()->addMonths(1)],
        ];

        foreach ($promotions as $promotion) {
            $promo = Promotion::create($promotion);
            
            // Attach random products to each promotion
            $promo->products()->attach(Product::inRandomOrder()->take(rand(2, 5))->pluck('id'));
        }
    }
}

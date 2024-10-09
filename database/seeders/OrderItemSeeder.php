<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $products = Product::inRandomOrder()->take(rand(1, 3))->get();

            foreach ($products as $product) {
                $quantity = rand(1, 5);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                ]);

                $order->total_amount += $product->price * $quantity;
            }

            $order->save();
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = User::all();
        
        for ($i = 0; $i < 100; $i++) {
            $user = $faker->randomElement($users);
            $billingAddress = $user->addresses()->where('type', 'billing')->first();
            $shippingAddress = $user->addresses()->where('type', 'shipping')->first();

            Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => $faker->randomFloat(2, 10, 1000),
                'status' => Order::STATUS_PENDING,
                'billing_address_id' => $billingAddress->id,
                'shipping_address_id' => $shippingAddress->id,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}

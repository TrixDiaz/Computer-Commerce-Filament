<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Address;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            $billingAddress = $user->addresses()->where('type', 'billing')->first();
            $shippingAddress = $user->addresses()->where('type', 'shipping')->first();

            Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => 0,
                'status' => 'pending',
                'billing_address_id' => $billingAddress->id,
                'shipping_address_id' => $shippingAddress->id,
            ]);
        }
    }
}

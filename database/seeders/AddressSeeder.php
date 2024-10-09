<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run()
    {
        $customers = User::all();

        foreach ($customers as $customer) {
            Address::create([
                'user_id' => $customer->id,
                'address_line_1' => '123 Main St',
                'city' => 'Anytown',
                'state' => 'CA',
                'postal_code' => '12345',
                'country' => 'USA',
                'is_default' => true,
                'type' => 'billing',
            ]);

            Address::create([
                'user_id' => $customer->id,
                'address_line_1' => '456 Elm St',
                'city' => 'Othertown',
                'state' => 'NY',
                'postal_code' => '67890',
                'country' => 'USA',
                'is_default' => true,
                'type' => 'shipping',
            ]);
        }
    }
}

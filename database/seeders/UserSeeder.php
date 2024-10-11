<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create the original 3 users
        $customers = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'admin@example.com', 'password' => Hash::make('password')],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'user@example.com', 'password' => Hash::make('password')],
            ['first_name' => 'Bob', 'last_name' => 'Johnson', 'email' => 'test@example.com', 'password' => Hash::make('password')],
        ];

        foreach ($customers as $customer) {
            User::create($customer);
        }

        // Create 197 additional fake users
        $faker = Faker::create();

        for ($i = 0; $i < 197; $i++) {
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
            ]);
        }
    }
}

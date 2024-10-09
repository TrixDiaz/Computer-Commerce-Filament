<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $customers = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'admin@example.com', 'password' => bcrypt('password')],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'user@example.com', 'password' => bcrypt('password')],
            ['first_name' => 'Bob', 'last_name' => 'Johnson', 'email' => 'test@example.com', 'password' => bcrypt('password')],
        ];

        foreach ($customers as $customer) {
            User::create($customer);
        }
    }
}

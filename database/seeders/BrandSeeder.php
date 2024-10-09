<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            ['name' => 'Dell', 'slug' => 'dell', 'description' => 'Dell computers and accessories'],
            ['name' => 'HP', 'slug' => 'hp', 'description' => 'HP computers and printers'],
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Apple computers and devices'],
            ['name' => 'Lenovo', 'slug' => 'lenovo', 'description' => 'Lenovo laptops and tablets'],
            ['name' => 'Asus', 'slug' => 'asus', 'description' => 'Asus computers and components'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}

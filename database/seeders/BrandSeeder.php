<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $brands = [
            ['name' => 'Dell', 'slug' => 'dell'],
            ['name' => 'HP', 'slug' => 'hp'],
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Lenovo', 'slug' => 'lenovo'],
            ['name' => 'Asus', 'slug' => 'asus'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}

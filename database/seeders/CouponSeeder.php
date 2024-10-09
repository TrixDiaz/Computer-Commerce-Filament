<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run()
    {
        $coupons = [
            ['code' => 'SUMMER10', 'type' => 'percentage', 'value' => 10.00, 'start_date' => now(), 'end_date' => now()->addMonths(3)],
            ['code' => 'WELCOME20', 'type' => 'fixed', 'value' => 20.00, 'start_date' => now(), 'end_date' => now()->addYear()],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}

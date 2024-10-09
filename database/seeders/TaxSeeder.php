<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tax;

class TaxSeeder extends Seeder
{
    public function run()
    {
        $taxes = [
            ['name' => 'Standard VAT', 'rate' => 20.00],
            ['name' => 'Reduced VAT', 'rate' => 5.00],
            ['name' => 'Zero VAT', 'rate' => 0.00],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }
    }
}

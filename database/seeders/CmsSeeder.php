<?php

namespace Database\Seeders;

use App\Models\Cms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $cmsData = [
            [
                'section' => 'banner',
                'title' => 'Welcome to Our Site',
                'content' => 'Discover amazing products and services',
                'image_url' => 'https://example.com/banner.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'section' => 'announcement',
                'title' => 'Special Offer',
                'content' => 'Get 20% off on all products this week!',
                'image_url' => null,
                'order' => 2,
                'is_active' => true,
            ],
            // Add more seed data for other sections (hero, heading, carousel, promo, faq, newsletter, footer)
        ];

        foreach ($cmsData as $data) {
            Cms::create($data);
        }
    }
}

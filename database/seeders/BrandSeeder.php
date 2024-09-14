<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => '🛒 Amazon',
                'slug' => 'amazon',
                'position' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '💳 Visa',
                'slug' => 'visa',
                'position' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '🍏 Apple',
                'slug' => 'apple',
                'position' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '☕ Starbucks',
                'slug' => 'starbucks',
                'position' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '🍗 Chick-fil-A',
                'slug' => 'chick-fil-a',
                'position' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        Brand::insert($brands);
    }
}

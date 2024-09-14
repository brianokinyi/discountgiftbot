<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\GiftCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Webpatser\Countries\Countries;

class GiftCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GiftCard::factory()->count(4)->create();
    }
}

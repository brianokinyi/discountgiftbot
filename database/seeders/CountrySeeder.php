<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'capital' => 'Washington DC',
                'citizenship' => 'American',
                'country_code' => '840',
                'currency' => 'US dollar',
                'currency_code' => 'USD',
                'currency_sub_unit' => 'cent',
                'full_name' => 'United States of America',
                'iso_3166_2' => 'US',
                'iso_3166_3' => 'USA',
                'name' => 'United States',
                'region_code' => '019',
                'sub_region_code' => '021',
                'eea' => false,
                'calling_code' => '1',
                'currency_symbol' => '$',
                'currency_decimals' => '2',
                'flag' => '🇺🇸',
                'position' => 0
            ],
            [
                'capital' => 'London',
                'citizenship' => 'British',
                'country_code' => '826',
                'currency' => 'pound sterling',
                'currency_code' => 'GBP',
                'currency_sub_unit' => 'penny (pl. pence)',
                'full_name' => 'United Kingdom of Great Britain and Northern Ireland',
                'iso_3166_2' => 'GB',
                'iso_3166_3' => 'GBR',
                'name' => 'United Kingdom',
                'region_code' => '150',
                'sub_region_code' => '154',
                'eea' => false,
                'calling_code' => '44',
                'currency_symbol' => '£',
                'currency_decimals' => '2',
                'flag' => '🇬🇧',
                'position' => 1
            ],
            [
                'capital' => 'Ottawa',
                'citizenship' => 'Canadian',
                'country_code' => '124',
                'currency' => 'Canadian dollar',
                'currency_code' => 'CAD',
                'currency_sub_unit' => 'cent',
                'full_name' => 'Canada',
                'iso_3166_2' => 'CA',
                'iso_3166_3' => 'CAN',
                'name' => 'Canada',
                'region_code' => '019',
                'sub_region_code' => '021',
                'eea' => false,
                'calling_code' => '1',
                'currency_symbol' => '$',
                'currency_decimals' => '2',
                'flag' => '🇨🇦',
                'position' => 2
            ],
            [
                'capital' => 'Canberra',
                'citizenship' => 'Australian',
                'country_code' => '036',
                'currency' => 'Australian dollar',
                'currency_code' => 'AUD',
                'currency_sub_unit' => 'cent',
                'full_name' => 'Commonwealth of Australia',
                'iso_3166_2' => 'AU',
                'iso_3166_3' => 'AUS',
                'name' => 'Australia',
                'region_code' => '009',
                'sub_region_code' => '053',
                'eea' => false,
                'calling_code' => '61',
                'currency_symbol' => '$',
                'currency_decimals' => '2',
                'flag' => '🇦🇺',
                'position' => 3
            ]
        ];

        Country::insert($countries);
    }
}

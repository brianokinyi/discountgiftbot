<?php

namespace Database\Seeders;

use App\Models\Coin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coins = [
            [
                'code' => 'BTC',
                'name' => 'Bitcoin',    
                'priority' => 0,
                'logo' => '₿',
                'network' => 'btc'
            ],
            [
                'code' => 'ETH',
                'name' => 'Ethereum',    
                'priority' => 1,
                'logo' => 'Ξ',
                'network' => 'eth'
            ],
            [
                'code' => 'LTC',
                'name' => 'Litecoin',    
                'priority' => 2,
                'logo' => 'Ł',
                'network' => 'ltc'
            ],
            [
                'code' => 'USDTTRC20',
                'name' => 'Tether USD (Tron)',    
                'priority' => 3,
                'logo' => '💵',
                'network' => 'trx'
            ],
        ];

        Coin::insert($coins);
    }
}

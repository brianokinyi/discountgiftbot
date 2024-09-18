<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Country;
use App\Models\Denomination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GiftCard>
 */
class GiftCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::inRandomOrder()->firstOrFail()->id,
            'country_id' => Country::inRandomOrder()->firstOrFail()->id,
            'denomination_id' => Denomination::inRandomOrder()->firstOrFail()->id,
            'in_stock' => $this->faker->boolean()
        ];
    }
}

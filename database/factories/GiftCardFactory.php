<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Webpatser\Countries\Countries;

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
            'country_id' => Countries::inRandomOrder()->firstOrFail()->id,
            'value' => $this->faker->randomElement([50, 100, 200, 500]),
            'discount' => $this->faker->randomElement([50, 55, 60]),
            'in_stock' => $this->faker->boolean()
        ];
    }
}

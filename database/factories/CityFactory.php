<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'city_name' => fake('ar_SA')->city(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\College;
use App\Models\University;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<College>
 */
class CollegeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'كلية '.fake('ar_SA')->randomElement(['الهندسة', 'العلوم', 'إدارة الأعمال', 'الحاسبات', 'الطب', 'القانون', 'التربية']),
            'university_id' => University::factory(),
            'city_id' => City::inRandomOrder()->first()->id,
            'dean_name' => fake('ar_SA')->name(),
            'dean_email' => fake()->unique()->safeEmail(),
            'dean_mobile' => '05'.fake()->numerify('########'),
            'dean_phone' => '01'.fake()->numerify('########'),
        ];
    }
}

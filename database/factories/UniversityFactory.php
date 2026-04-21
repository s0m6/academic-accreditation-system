<?php

namespace Database\Factories;

use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<University>
 */
class UniversityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake('ar_SA')->company().' للعلوم',
            'type' => fake()->randomElement(['government', 'private']),
            'accreditation_officer_id' => User::factory()->create(['role' => 'accreditation_officer', 'email_verified_at' => now()])->id,
            'president_name' => fake('ar_SA')->name(),
            'president_email' => fake()->unique()->safeEmail(),
            'president_mobile' => '05'.fake()->numerify('########'),
            'president_phone' => '01'.fake()->numerify('########'),
        ];
    }
}

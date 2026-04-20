<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Evaluator;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Evaluator>
 */
class EvaluatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'name' => fake('ar_SA')->name(),
                'role' => 'evaluator',
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
            ]),
            'city_id' => City::inRandomOrder()->first()?->id ?? City::factory(),
            'general_specialty' => fake()->randomElement(['علوم الحاسب', 'نظم المعلومات', 'هندسة البرمجيات', 'الذكاء الاصطناعي', 'الأمن السيبراني', 'إدارة الأعمال', 'المحاسبة', 'القانون', 'الطب البشري', 'الهندسة المدنية']),
            'detailed_specialty' => fake('ar_SA')->realText(50),
            'academic_rank' => fake()->randomElement(['Professor', 'Associate Professor', 'Assistant Professor', 'Lecturer', 'Expert']),
            'current_university_id' => University::inRandomOrder()->first()?->id ?? University::factory(),
        ];
    }
}

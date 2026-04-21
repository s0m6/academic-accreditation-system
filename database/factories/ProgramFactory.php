<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program_name' => 'برنامج '.fake('ar_SA')->randomElement(['علوم الحاسب', 'نظم المعلومات', 'هندسة البرمجيات', 'الأمن السيبراني', 'إدارة الأعمال', 'الطب البشري']),
            'degree_level' => fake()->randomElement(['diploma', 'bachelor', 'master', 'phd']),
            'program_details' => json_encode([]),
            'department_id' => Department::factory(),
        ];
    }
}

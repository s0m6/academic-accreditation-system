<?php

namespace Database\Factories;

use App\Models\College;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'قسم '.fake('ar_SA')->randomElement(['علوم الحاسب', 'نظم المعلومات', 'الرياضيات', 'الفيزياء', 'المحاسبة', 'الإدارة', 'اللغة العربية']),
            'college_id' => College::factory(),
            'head_name' => fake('ar_SA')->name(),
            'head_email' => fake()->unique()->safeEmail(),
            'head_mobile' => '05'.fake()->numerify('########'),
            'head_phone' => '01'.fake()->numerify('########'),
        ];
    }
}

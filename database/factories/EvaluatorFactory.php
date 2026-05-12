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
        $specialties = [
            'علوم الحاسب' => ['قواعد البيانات', 'هياكل البيانات', 'نظم التشغيل', 'الحوسبة السحابية', 'هندسة الخوارزميات'],
            'نظم المعلومات' => ['تحليل النظم', 'إدارة قواعد البيانات', 'أمن نظم المعلومات', 'التجارة الإلكترونية', 'نظم دعم القرار'],
            'هندسة البرمجيات' => ['هندسة المتطلبات', 'تصميم البرمجيات', 'اختبار البرمجيات', 'إدارة مشاريع البرمجيات', 'هندسة الجودة'],
            'الذكاء الاصطناعي' => ['تعلم الآلة', 'معالجة اللغات الطبيعية', 'الرؤية الحاسوبية', 'الروبوتات', 'الأنظمة الخبيرة'],
            'الأمن السيبراني' => ['التشفير', 'أمن الشبكات', 'التحقيق الجنائي الرقمي', 'إدارة المخاطر السيبرانية', 'اختبار الاختراق'],
            'إدارة الأعمال' => ['الموارد البشرية', 'التسويق', 'الإدارة المالية', 'سلاسل الإمداد', 'إدارة العمليات'],
            'المحاسبة' => ['المحاسبة المالية', 'محاسبة التكاليف', 'المراجعة والتدقيق', 'المحاسبة الضريبية', 'المحاسبة الإدارية'],
            'القانون' => ['القانون الجنائي', 'القانون المدني', 'القانون التجاري', 'القانون الدولي', 'قانون العمل'],
            'الطب البشري' => ['الجراحة العامة', 'طب الأطفال', 'الأمراض الباطنية', 'النساء والتوليد', 'طب الطوارئ'],
            'الهندسة المدنية' => ['هندسة الطرق', 'هندسة الجسور', 'الهندسة الإنشائية', 'هندسة الموارد المائية', 'ميكانيكا التربة'],
        ];

        $generalSpecialty = fake()->randomElement(array_keys($specialties));

        return [
            'user_id' => User::factory()->state([
                'name' => fake('ar_SA')->name(),
                'role' => 'evaluator',
                'password' => Hash::make('123456789'),
                'email_verified_at' => now(),
                'mobile' => fake()->phoneNumber(),
                'phone' => fake()->phoneNumber(),
            ]),
            'city_id' => City::inRandomOrder()->first()->id,
            'general_specialty' => $generalSpecialty,
            'detailed_specialty' => fake()->randomElement($specialties[$generalSpecialty]),
            'academic_rank' => fake()->randomElement(['Professor', 'Associate Professor', 'Assistant Professor', 'Lecturer', 'Expert']),
            'current_university_id' => University::inRandomOrder()->first()?->id ?? University::factory(),
        ];
    }
}

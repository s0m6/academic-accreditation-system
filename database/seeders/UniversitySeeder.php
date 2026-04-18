<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = [
            ['name' => 'جامعة صنعاء', 'type' => 'government'],
            ['name' => 'جامعة عدن', 'type' => 'government'],
            ['name' => 'جامعة العلوم والتكنولوجيا', 'type' => 'private'],
            ['name' => 'جامعة تعز', 'type' => 'government'],
            ['name' => 'الجامعة الوطنية', 'type' => 'private'],
            ['name' => 'جامعة حضرموت', 'type' => 'government'],
        ];

        foreach ($universities as $uni) {
            University::create([
                'name' => $uni['name'],
                'type' => $uni['type'],
                'president_name' => 'د. '.fake('ar_SA')->name(),
                'president_email' => fake()->unique()->safeEmail(),
                'president_mobile' => '77'.fake()->numerify('#######'),
                'president_phone' => '0'.fake()->numerify('#######'),
                // نتركه null حالياً لأنه لا يوجد مسؤولين اعتماد مسجلين لربطهم
                'accreditation_officer_id' => null,
            ]);
        }
    }
}

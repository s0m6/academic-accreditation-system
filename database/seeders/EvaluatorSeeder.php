<?php

namespace Database\Seeders;

use App\Models\Evaluator;
use App\Models\EvaluatorConflict;
use App\Models\University;
use Illuminate\Database\Seeder;

class EvaluatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = University::all();

        if ($universities->isEmpty()) {
            $this->command->error('No universities found. Please run UniversitySeeder first.');

            return;
        }

        $evaluators = Evaluator::factory()
            ->count(30)
            ->create();

        $evaluators->each(function ($evaluator) use ($universities) {
            $conflictReasons = [
                'عمل سابق في الجامعة أو أحد مراكزها البحثية',
                'عضو سابق أو حالي في مجلس الكلية أو اللجان الأكاديمية',
                'تعاون بحثي مشترك قائم مع أعضاء هيئة التدريس بالجامعة',
                'وجود صلة قرابة مع أحد منسوبي الكلية أو البرنامج المعني',
                'خريج سابق من أحد برامج الدراسات العليا بالجامعة',
                'المشاركة في لجان تحكيم ترقيات علمية أو مناقشة رسائل علمية بالجامعة',
                'تقديم خدمات استشارية سابقة أو حالية للجامعة',
                'انتداب سابق للتدريس أو التدريب في الجامعة',
            ];

            // Get several universities for conflicts (between 1 and 3, but not all)
            // We exclude the evaluator's current university
            $conflictUniversities = $universities->where('id', '!=', $evaluator->current_university_id)
                ->random(rand(1, min(3, $universities->count() - 1)));

            foreach ($conflictUniversities as $university) {
                EvaluatorConflict::create([
                    'evaluator_id' => $evaluator->id,
                    'university_id' => $university->id,
                    'conflict_text' => fake()->randomElement($conflictReasons),
                ]);
            }
        });
    }
}

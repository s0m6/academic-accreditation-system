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

        Evaluator::factory()
            ->count(50)
            ->create()
            ->each(function ($evaluator) use ($universities) {
                // Get several universities for conflicts (between 1 and 3, but not all)
                // We exclude the evaluator's current university
                $conflictUniversities = $universities->where('id', '!=', $evaluator->current_university_id)
                    ->random(rand(1, min(3, $universities->count() - 1)));

                foreach ($conflictUniversities as $university) {
                    EvaluatorConflict::create([
                        'evaluator_id' => $evaluator->id,
                        'university_id' => $university->id,
                        'conflict_text' => 'تعارض مصالح بسبب العمل السابق أو التعاون الأكاديمي',
                    ]);
                }
            });
    }
}

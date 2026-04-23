<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\College;
use App\Models\Department;
use App\Models\Program;
use App\Models\University;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $university = University::whereNotNull('accreditation_officer_id')->first();

        if (! $university) {
            $this->command->info('No university linked to an accreditation officer found. Skipping academic structure seeding.');

            return;
        }

        $structure = [
            [
                'college_name' => 'كلية علوم الحاسب والمعلومات',
                'departments' => [
                    [
                        'name' => 'قسم علوم الحاسب',
                        'programs' => ['برنامج بكالوريوس علوم الحاسب', 'برنامج ماجستير علوم الحاسب'],
                    ],
                    [
                        'name' => 'قسم هندسة البرمجيات',
                        'programs' => ['برنامج بكالوريوس هندسة البرمجيات', 'برنامج ماجستير هندسة البرمجيات'],
                    ],
                ],
            ],
            [
                'college_name' => 'كلية الهندسة',
                'departments' => [
                    [
                        'name' => 'قسم الهندسة المدنية',
                        'programs' => ['برنامج بكالوريوس الهندسة المدنية', 'برنامج ماجستير الهندسة المدنية'],
                    ],
                    [
                        'name' => 'قسم الهندسة الكهربائية',
                        'programs' => ['برنامج بكالوريوس الهندسة الكهربائية', 'برنامج ماجستير الهندسة الكهربائية'],
                    ],
                ],
            ],
            [
                'college_name' => 'كلية إدارة الأعمال',
                'departments' => [
                    [
                        'name' => 'قسم المحاسبة',
                        'programs' => ['برنامج بكالوريوس المحاسبة', 'برنامج ماجستير المحاسبة'],
                    ],
                    [
                        'name' => 'قسم إدارة الأعمال',
                        'programs' => ['برنامج بكالوريوس إدارة الأعمال', 'برنامج ماجستير إدارة الأعمال'],
                    ],
                ],
            ],
        ];

        $programDetails = [
            'language' => 'arabic',
            'website_url' => 'http://localhost/academic-accreditation-system/public/accreditation-officer/programs',
            'credit_hours' => 5,
            'study_duration' => '4 سنوات',
            'establishment_date' => '2026-04-21',
        ];

        foreach ($structure as $collegeData) {
            $college = College::factory()->create([
                'name' => $collegeData['college_name'],
                'university_id' => $university->id,
                'city_id' => $university->city_id ?? City::inRandomOrder()->first()?->id ?? City::factory(),
            ]);

            foreach ($collegeData['departments'] as $deptData) {
                $department = Department::factory()->create([
                    'name' => $deptData['name'],
                    'college_id' => $college->id,
                ]);

                foreach ($deptData['programs'] as $programName) {
                    Program::factory()->create([
                        'program_name' => $programName,
                        'department_id' => $department->id,
                        'program_details' => $programDetails,
                    ]);
                }
            }
        }
    }
}

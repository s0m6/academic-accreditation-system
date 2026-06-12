<?php

namespace Database\Seeders;

use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccreditationOfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates an Accreditation Officer account linked to a university.
     */
    public function run(): void
    {
        // إنشاء حساب مسؤول الاعتماد
        // Creates the Accreditation Officer account
        $user = User::updateOrCreate(
            ['email' => 'm@m.com'],
            [
                'name' => 'محمود الرميمة',
                'email_verified_at' => now(),
                'password' => Hash::make('123456789'),
                'role' => 'accreditation_officer',
                'mobile' => '770000000',
                'phone' => '01000000',
            ]
        );

        // ربط المسؤول بأول جامعة متوفرة في النظام
        // Links the officer to the first available university
        $university = University::first();
        if ($university) {
            $university->update([
                'accreditation_officer_id' => $user->id,
            ]);
        }
    }
}

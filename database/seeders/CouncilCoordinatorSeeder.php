<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CouncilCoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinators = [
            [
                'name' => 'سالم بسيس',
                'email' => 's@s.com',
                'mobile' => '770000001',
                'phone' => '01000001',
            ],
            [
                'name' => 'محمد سعيد',
                'email' => 'mohammed.coord@council.com',
                'mobile' => '770000002',
                'phone' => '01000002',
            ],
            [
                'name' => 'فاطمة علي',
                'email' => 'fatima.coord@council.com',
                'mobile' => '770000003',
                'phone' => '01000003',
            ],
            [
                'name' => 'خالد حسن',
                'email' => 'khaled.coord@council.com',
                'mobile' => '770000004',
                'phone' => '01000004',
            ],
        ];

        foreach ($coordinators as $coordinator) {
            User::updateOrCreate(
                ['email' => $coordinator['email']],
                [
                    'name' => $coordinator['name'],
                    'mobile' => $coordinator['mobile'],
                    'phone' => $coordinator['phone'],
                    'role' => 'council_coordinator',
                    'password' => Hash::make('123456789'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

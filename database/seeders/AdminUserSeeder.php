<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'أحمد بالحارث',
            'email' => 'a@a.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'role' => 'council_secretariat',
            'mobile' => '777123456',
            'phone' => '01234567',

        ]);
    }
}

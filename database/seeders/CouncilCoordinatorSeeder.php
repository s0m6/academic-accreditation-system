<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouncilCoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'محمد أحمد العتيبي',
            'سارة علي القحطاني',
            'فهد إبراهيم الزهراني',
        ];

        foreach ($names as $index => $name) {
            \App\Models\User::factory()
                ->coordinator()
                ->create([
                    'name' => $name,
                    'email' => 'coordinator' . ($index + 1) . '@example.com',
                ]);
        }
    }
}

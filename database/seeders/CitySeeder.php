<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = ['صنعاء', 'عدن', 'تعز', 'حضرموت', 'إب', 'الحديدة'];

        foreach ($cities as $city) {
            City::create(['city_name' => $city]);
        }
    }
}

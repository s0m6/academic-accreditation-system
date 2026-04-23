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
        $cities = [
            'صنعاء',
            'عدن',
            'تعز',
            'حضرموت',
            'إب',
            'الحديدة',
            'أبين',
            'المهرة',
            'المحويت',
            'ذمار',
            'البيضاء',
            'حجة',
            'الجوف',
            'صعدة',
            'شبوة',
            'مأرب',
            'ريمة',
            'لحج',
            'عمران',
            'الضالع',
            'سقطرى',
        ];
        foreach ($cities as $city) {
            City::create(['city_name' => $city]);
        }
    }
}

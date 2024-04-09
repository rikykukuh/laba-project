<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CityTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = ['Jakarta', 'Bandung', 'Semarang', 'Yogyakarta', 'Surabaya', 'Serang', 'Bali', 'Lombok', 'Kupang', 'Makassar'];

        foreach ($cities as $method) {
            City::create([
                'name' => $method,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Site;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SiteTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $sites = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi'];

        foreach ($sites as $method) {
            Site::create([
                'name' => $method,
                'note' => $faker->sentence(8),
            ]);
        }
    }
}

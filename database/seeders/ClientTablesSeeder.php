<?php

namespace Database\Seeders;

use App\Models\Client;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ClientTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 25; $i++) {
            Client::create([
                'name' => $faker->name,
                'address' => $faker->sentence(5),
                'phone_number' => $faker->phoneNumber,
                'city_id' => rand(1, 10),
            ]);
        }
    }
}

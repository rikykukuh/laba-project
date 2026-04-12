<?php

namespace Database\Seeders;

use App\Models\Customer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class CustomerTablesSeeder extends Seeder
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
            Customer::create([
                'name' => $faker->name,
                'address' => $faker->sentence(5),
                'phone_number' => $faker->phoneNumber,
                'city_id' => rand(1, 10),
            ]);
        }
    }
}

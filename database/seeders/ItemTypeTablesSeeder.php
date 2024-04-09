<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sites = ['Laptop', 'HP', 'PC', 'Notebook', 'Tablet', 'Smartwatch', 'Kulkas', 'TV LED', 'Charger Laptop', 'Charger Hp'];

        foreach ($sites as $method) {
            ItemType::create([
                'name' => $method,
            ]);
        }
    }
}

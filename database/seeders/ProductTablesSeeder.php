<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $sites = ['Laptop', 'HP', 'PC', 'Notebook', 'Tablet', 'Smartwatch', 'Kulkas', 'TV LED', 'Charger Laptop', 'Charger Hp'];
        //
        // foreach ($sites as $method) {
        //     $min = 1000000;
        //     $max = 10000000;
        //     $roundingUnit = 1000000;
        //     $threshold = 0.5;
        //
        //     // Generate a random price between the specified range
        //     $randomPrice = mt_rand($min, $max);
        //
        //     // Determine the lower and upper bounds for the rounding unit
        //     $lowerBound = floor($randomPrice / $roundingUnit) * $roundingUnit;
        //     $upperBound = ceil($randomPrice / $roundingUnit) * $roundingUnit;
        //
        //     // Calculate the midpoint threshold value
        //     $midPoint = $lowerBound + ($roundingUnit * $threshold);
        //
        //     // Round down or up based on the threshold
        //     if ($randomPrice < $midPoint) {
        //         $roundedPrice = $lowerBound;
        //     } else {
        //         $roundedPrice = $upperBound;
        //     }
        //
        //     $type = rand(0, 1);
        //
        //     Product::create([
        //         'name' => $method,
        //         'type' => $type,
        //         'price' => $type == 0 ? $roundedPrice : null,
        //     ]);
        // }

        Product::insert([
            ['name' => 'Ganti Resleting', 'price' => 1, 'type' => 1],
            ['name' => 'Ganti Roda Koper', 'price' => 1, 'type' => 1],
            ['name' => 'Jahit Tas', 'price' => 1, 'type' => 1],
            ['name' => 'Umum', 'price' => 1, 'type' => 1],
            ['name' => 'Semir Tas', 'price' => 100000, 'type' => 0],
            ['name' => 'Gesper A', 'price' => 90000, 'type' => 0],
            ['name' => 'Gesper B', 'price' => 130000, 'type' => 0],
            ['name' => 'Sarung Tas', 'price' => 75000, 'type' => 0],
        ]);
    }
}

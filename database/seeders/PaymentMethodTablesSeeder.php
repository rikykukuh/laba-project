<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $paymentMethods = ['Cash', 'Debit', 'Credit card', 'QRIS'];
        //
        // foreach ($paymentMethods as $method) {
        //     PaymentMethod::create([
        //         'name' => $method,
        //     ]);
        // }

        DB::table('payment_methods')->insert([
            ['id' => 1, 'name' => 'Cash'],
            ['id' => 2, 'name' => 'Transfer/QRIS'],
            ['id' => 3, 'name' => 'Debit'],
            ['id' => 4, 'name' => 'Credit Card'],
            ['id' => 5, 'name' => 'Dompet Digital'],
        ]);
    }
}

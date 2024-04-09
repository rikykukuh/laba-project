<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = ['Cash', 'Debit', 'Credit card', 'QRIS'];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create([
                'name' => $method,
            ]);
        }
    }
}

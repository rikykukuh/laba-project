<?php

namespace Database\Seeders;

use App\Models\PaymentMerchant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMerchantTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $payment_merchants = [
        //     [
        //         'name' => 'BCA',
        //         'payment_method_id' => 1,
        //     ],
        //     [
        //         'name' => 'BTN',
        //         'payment_method_id' => 2,
        //     ],
        //     [
        //         'name' => 'BRI',
        //         'payment_method_id' => 1,
        //     ],
        //     [
        //         'name' => 'Mandiri',
        //         'payment_method_id' => 1,
        //     ],
        //     [
        //         'name' => 'BNI',
        //         'payment_method_id' => 4,
        //     ],
        //     [
        //         'name' => 'Kredivo',
        //         'payment_method_id' => 2,
        //     ],
        //     [
        //         'name' => 'OVO',
        //         'payment_method_id' => 2,
        //     ],
        //     [
        //         'name' => 'GoPay',
        //         'payment_method_id' => 2,
        //     ],
        //     [
        //         'name' => 'ShopeePay',
        //         'payment_method_id' => 2,
        //     ],
        //     [
        //         'name' => 'AdaKami',
        //         'payment_method_id' => 3,
        //     ],
        // ];
        //
        // foreach ($payment_merchants as $payment_merchant) {
        //     PaymentMerchant::create($payment_merchant);
        // }

        DB::table('payment_merchants')->insert([
            ['payment_method_id' => 1, 'name' => '-'],
            ['payment_method_id' => 2, 'name' => 'BCA'],
            ['payment_method_id' => 2, 'name' => 'BRI'],
            ['payment_method_id' => 2, 'name' => 'Mandiri'],
            ['payment_method_id' => 3, 'name' => 'BCA'],
            ['payment_method_id' => 3, 'name' => 'BRI'],
            ['payment_method_id' => 3, 'name' => 'Mandiri'],
            ['payment_method_id' => 4, 'name' => 'BCA'],
            ['payment_method_id' => 4, 'name' => 'Visa'],
            ['payment_method_id' => 4, 'name' => 'Mastercard'],
            ['payment_method_id' => 5, 'name' => 'Ovo'],
            ['payment_method_id' => 5, 'name' => 'Gopay'],
        ]);
    }
}

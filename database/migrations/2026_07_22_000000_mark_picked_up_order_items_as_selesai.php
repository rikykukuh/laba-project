<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MarkPickedUpOrderItemsAsSelesai extends Migration
{
    public function up()
    {
        DB::table('order_items')
            ->whereNull('deleted_at')
            ->whereIn('order_id', function ($query) {
                $query->select('id')
                    ->from('orders')
                    ->where('transaction_type', 0)
                    ->where('status', 'DIAMBIL')
                    ->whereNull('deleted_at');
            })
            ->update([
                'state' => 'selesai',
                'updated_at' => now(),
            ]);
    }

    public function down()
    {
        // State sebelumnya tidak dapat diketahui kembali dengan aman.
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLastSequenceToSitesTable extends Migration
{
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->unsignedBigInteger('last_sequence')->default(0)->after('code');
        });

        $lastSequences = [];

        DB::table('orders')
            ->select('id', 'site_id', 'number_ticket')
            ->whereNotNull('site_id')
            ->orderBy('id')
            ->chunkById(500, function ($orders) use (&$lastSequences) {
                foreach ($orders as $order) {
                    if (!preg_match('/(\d+)$/', (string) $order->number_ticket, $matches)) {
                        continue;
                    }

                    $siteId = (int) $order->site_id;
                    $sequence = (int) $matches[1];
                    $lastSequences[$siteId] = max($lastSequences[$siteId] ?? 0, $sequence);
                }
            });

        foreach ($lastSequences as $siteId => $lastSequence) {
            DB::table('sites')->where('id', $siteId)->update([
                'last_sequence' => $lastSequence,
            ]);
        }
    }

    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('last_sequence');
        });
    }
}

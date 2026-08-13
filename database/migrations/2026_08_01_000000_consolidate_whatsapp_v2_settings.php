<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConsolidateWhatsappV2Settings extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('whatsapp_v2_settings')) {
            return;
        }

        DB::transaction(function () {
            $settingToKeep = DB::table('whatsapp_v2_settings')
                ->orderByRaw("CASE WHEN api_key IS NOT NULL AND api_key <> '' THEN 0 ELSE 1 END")
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();

            if (!$settingToKeep) {
                DB::table('whatsapp_v2_settings')->insert([
                    'base_url' => 'https://apiwa.pancalaba.id/api/v1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return;
            }

            DB::table('whatsapp_v2_settings')
                ->where('id', '<>', $settingToKeep->id)
                ->delete();
        });
    }

    public function down()
    {
        // Duplicate settings are intentionally not restored.
    }
}

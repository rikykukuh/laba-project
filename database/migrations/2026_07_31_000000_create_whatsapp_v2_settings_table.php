<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappV2SettingsTable extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_v2_settings', function (Blueprint $table) {
            $table->id();
            $table->text('api_key')->nullable();
            $table->string('base_url', 2048)->default('https://apiwa.pancalaba.id/api/v1');
            $table->timestamps();
        });

        DB::table('whatsapp_v2_settings')->insert([
            'id' => 1,
            'base_url' => 'https://apiwa.pancalaba.id/api/v1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_v2_settings');
    }
}

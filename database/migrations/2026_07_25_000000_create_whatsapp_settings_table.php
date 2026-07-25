<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->text('device_token')->nullable();
            $table->text('account_token')->nullable();
            $table->string('send_endpoint', 2048)->default('https://api.fonnte.com/send/');
            $table->string('get_devices_endpoint', 2048)->default('https://api.fonnte.com/get-devices');
            $table->string('add_device_endpoint', 2048)->default('https://api.fonnte.com/add-device');
            $table->string('disconnect_endpoint', 2048)->default('https://api.fonnte.com/disconnect');
            $table->string('country_code', 4)->default('62');
            $table->timestamps();
        });

        DB::table('whatsapp_settings')->insert([
            'id' => 1,
            'send_endpoint' => 'https://api.fonnte.com/send/',
            'get_devices_endpoint' => 'https://api.fonnte.com/get-devices',
            'add_device_endpoint' => 'https://api.fonnte.com/add-device',
            'disconnect_endpoint' => 'https://api.fonnte.com/disconnect',
            'country_code' => '62',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_settings');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQrEndpointToWhatsappSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('whatsapp_settings', function (Blueprint $table) {
            $table->string('qr_endpoint', 2048)
                ->default('https://api.fonnte.com/qr')
                ->after('send_endpoint');
        });
    }

    public function down()
    {
        Schema::table('whatsapp_settings', function (Blueprint $table) {
            $table->dropColumn('qr_endpoint');
        });
    }
}

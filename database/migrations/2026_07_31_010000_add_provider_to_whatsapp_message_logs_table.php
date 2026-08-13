<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderToWhatsappMessageLogsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('whatsapp_message_logs', 'provider')) {
            Schema::table('whatsapp_message_logs', function (Blueprint $table) {
                $table->string('provider', 30)->nullable()->after('target')->index();
            });
        }

        if (!Schema::hasColumn('whatsapp_message_logs', 'sender_device')) {
            Schema::table('whatsapp_message_logs', function (Blueprint $table) {
                $table->string('sender_device', 100)->nullable()->after('provider');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('whatsapp_message_logs', 'provider')) {
            Schema::table('whatsapp_message_logs', function (Blueprint $table) {
                $table->dropIndex(['provider']);
                $table->dropColumn('provider');
            });
        }

        if (Schema::hasColumn('whatsapp_message_logs', 'sender_device')) {
            Schema::table('whatsapp_message_logs', function (Blueprint $table) {
                $table->dropColumn('sender_device');
            });
        }
    }
}

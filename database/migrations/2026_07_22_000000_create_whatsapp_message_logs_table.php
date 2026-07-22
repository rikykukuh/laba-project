<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhatsappMessageLogsTable extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->unsignedBigInteger('sent_by')->nullable()->index();
            $table->string('target', 30);
            $table->text('message');
            $table->string('status', 30)->index();
            $table->string('provider_message_id')->nullable();
            $table->string('request_id')->nullable();
            $table->text('provider_response')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_message_logs');
    }
}

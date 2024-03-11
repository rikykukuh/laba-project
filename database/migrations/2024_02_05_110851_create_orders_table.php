<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('client_id');
            $table->integer('total');
            $table->decimal('payment', 10, 2);
            $table->string('status')->nullable();
            $table->string('number_ticket')->nullable();
            $table->decimal('uang_muka', 10, 2)->nullable();
            $table->string('picked_by')->nullable();
            $table->datetime('picked_at')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('sisa_pembayaran', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

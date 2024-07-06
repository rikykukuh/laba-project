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
            $table->string('name')->nullable();
            $table->integer('site_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->decimal('bruto', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('netto', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('transaction_type')->nullable();
            $table->integer('payment_id')->nullable();
            $table->string('status')->nullable();
            $table->string('number_ticket')->nullable();
            $table->decimal('uang_muka', 10, 2)->nullable();
            $table->string('picked_by')->nullable();
            $table->date('estimate_service_done')->nullable();
            $table->date('estimate_take_item')->nullable();
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

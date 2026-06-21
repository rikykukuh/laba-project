<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryColumnsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_delivery')) {
                $table->boolean('is_delivery')->default(false)->after('complain');
            }

            if (!Schema::hasColumn('orders', 'address')) {
                $table->string('address')->nullable()->after('is_delivery');
            }

            if (!Schema::hasColumn('orders', 'link_map_address')) {
                $table->string('link_map_address')->nullable()->after('address');
            }

            if (!Schema::hasColumn('orders', 'driver_id')) {
                $table->integer('driver_id')->nullable()->after('link_map_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'driver_id')) {
                $table->dropColumn('driver_id');
            }

            if (Schema::hasColumn('orders', 'link_map_address')) {
                $table->dropColumn('link_map_address');
            }

            if (Schema::hasColumn('orders', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('orders', 'is_delivery')) {
                $table->dropColumn('is_delivery');
            }
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('business_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop', function (Blueprint $table) {
            //
        });
    }
}

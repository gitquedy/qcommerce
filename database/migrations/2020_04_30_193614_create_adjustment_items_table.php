<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adjustment_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('adjustment_id');
            $table->foreign('adjustment_id')->references('id')->on('adjustments')->onDelete('cascade');
            $table->unsignedBigInteger('sku_id');
            $table->foreign('sku_id')->references('id')->on('sku')->onDelete('cascade');
            $table->string('sku_code');
            $table->string('sku_name');
            $table->text('image')->nullable();
            $table->float('quantity', 10, 2);
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjustment_items');
    }
}

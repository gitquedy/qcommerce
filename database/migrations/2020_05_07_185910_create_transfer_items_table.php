<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transfer_id');
            $table->foreign('transfer_id')->references('id')->on('transfers')->onDelete('cascade');
            $table->unsignedBigInteger('sku_id');
            $table->foreign('sku_id')->references('id')->on('sku')->onDelete('cascade');
            $table->string('sku_code');
            $table->string('sku_name');
            $table->text('image')->nullable();
            $table->float('quantity', 10, 2);
            $table->unsignedBigInteger('from_warehouse_id')->nullable();
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('SET NULL');
            $table->unsignedBigInteger('to_warehouse_id')->nullable();
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('SET NULL');
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
        Schema::dropIfExists('transfer_items');
    }
}

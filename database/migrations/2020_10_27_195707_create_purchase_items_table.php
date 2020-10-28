<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('purchases_id');
            $table->foreign('purchases_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->unsignedBigInteger('sku_id');
            $table->foreign('sku_id')->references('id')->on('sku')->onDelete('cascade');
            $table->string('sku_code');
            $table->string('sku_name');
            $table->text('image')->nullable();
            $table->float('unit_price', 10, 2);
            $table->integer('quantity');
            $table->float('discount', 10, 2);
            $table->float('subtotal', 10, 2);
            $table->float('real_unit_price', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_items');
    }
}

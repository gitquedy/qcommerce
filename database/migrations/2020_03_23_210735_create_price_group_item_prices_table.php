<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceGroupItemPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_group_item_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('price_group_id');
            $table->foreign('price_group_id')->references('id')->on('price_groups')->onDelete('cascade');
            $table->unsignedBigInteger('sku_id');
            $table->foreign('sku_id')->references('id')->on('sku')->onDelete('cascade');
            $table->float('price', 15, 2);
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
        Schema::dropIfExists('price_group_item_prices');
    }
}

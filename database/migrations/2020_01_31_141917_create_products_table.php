<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site');
            $table->string('SellerSku');
            $table->string('SkuId');
            $table->text('name');
            $table->string('item_id')->nullable();
            $table->string('primary_category')->nullable();
            $table->unsignedInteger('seller_sku_id')->nullable();
            $table->unsignedInteger('shop_id')->nullable();
            $table->binary('short_description')->nullable();
            $table->binary('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->text('Images')->nullable();
            $table->string('Url')->nullable();
            $table->string('package_width')->nullable();
            $table->string('color_family')->nullable();
            $table->string('package_height')->nullable();
            $table->float('special_price', 15, 2)->nullable();
            $table->float('price', 15, 2)->nullable();
            $table->string('package_length')->nullable();
            $table->string('package_weight')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('max_delivery_time')->nullable();
            $table->string('min_delivery_time')->nullable();
            $table->string('Available')->nullable();
            $table->string('Status')->nullable();
            $table->integer('seen')->nullable()->default(0);
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
        Schema::dropIfExists('products');
    }
}

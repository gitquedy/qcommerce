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

            //fk
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')->references('id')->on('shop')->onDelete('cascade');
            $table->unsignedInteger('seller_sku_id')->nullable();
            $table->string('site');
            $table->string('SellerSku');
            $table->string('SkuId');
            $table->text('name');
            $table->string('item_id');
            $table->integer('quantity')->default(0);
            $table->text('Images')->nullable();
            $table->string('Url')->nullable();
            $table->float('price', 15, 2)->nullable();
            $table->float('special_price', 15, 2)->nullable();
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

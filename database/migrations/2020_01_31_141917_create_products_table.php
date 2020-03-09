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
            $table->string('item_id');
            $table->unsignedInteger('shop_id')->nullable();
            $table->text('Images')->nullable();
            $table->string('Url')->nullable();
            $table->float('price', 15, 2)->nullable();
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

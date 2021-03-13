<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pos_settings_id');
            $table->foreign('pos_settings_id')->references('id')->on('pos_settings')->onDelete('cascade');
            $table->integer('so')->default(1);
            $table->integer('qu')->default(1);
            $table->integer('po')->default(1);
            $table->integer('tr')->default(1);
            $table->integer('do')->default(1);
            $table->integer('pay')->default(1);
            $table->integer('re')->default(1);
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
        Schema::dropIfExists('order_refs');
    }
}

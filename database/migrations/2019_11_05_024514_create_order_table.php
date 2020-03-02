<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ordersn')->nullable();
            $table->string('tracking_no')->nullable();
            $table->string('site')->nullable();
            $table->string('customer_last_name')->nullable();
            $table->string('price');
            $table->string('payment_method');
            $table->string('customer_first_name');
            $table->string('shipping_fee');
            $table->string('items_count');
            $table->string('status');
            $table->unsignedInteger('shipping_fee_reconciled')->default(0);
            $table->boolean('printed')->default(0);
            $table->boolean('packed')->default(0);
            $table->unsignedInteger('shop_id');
            $table->string('seen')->nullable();
            $table->string('created_at');
            $table->string('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}

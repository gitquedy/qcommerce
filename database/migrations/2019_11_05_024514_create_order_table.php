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
            
            //fk
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')->references('id')->on('shop')->onDelete('cascade');

            $table->string('ordersn');
            $table->string('tracking_no')->nullable();
            $table->string('site')->nullable();
            $table->string('customer_last_name')->nullable();
            $table->float('price', 10, 2);
            $table->string('payment_method');
            $table->string('customer_first_name');
            $table->string('shipping_fee');
            $table->string('items_count');
            $table->string('status');
            $table->unsignedBigInteger('shipping_fee_reconciled')->default(0);
            $table->boolean('printed')->default(0);
            $table->boolean('packed')->default(0);
            $table->boolean('returned')->default(0);
            $table->boolean('payout')->default(0);
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

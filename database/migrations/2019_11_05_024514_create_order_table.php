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
            $table->string('tracking_no')->nullable();
            $table->string('voucher_platform');
            $table->string('voucher');
            $table->string('voucher_seller');
            $table->string('voucher_code');
            $table->string('gift_option');
            $table->string('customer_last_name');
            $table->string('promised_shipping_times');
            $table->string('price');
            $table->string('national_registration_number');
            $table->string('payment_method');
            $table->string('customer_first_name');
            $table->string('shipping_fee');
            $table->string('branch_number');
            $table->string('tax_code');
            $table->string('items_count');
            $table->string('status');
            $table->boolean('printed')->default(0);
            $table->string('extra_attributes');
            $table->string('gift_message');
            $table->string('remarks');
            $table->string('delivery_info');
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

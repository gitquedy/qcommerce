<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->string('sales_prefix')->default('SALE');
            $table->string('quote_prefix')->default('QUOTE');
            $table->string('purchase_prefix')->default('PO');
            $table->string('transfer_prefix')->default('TR');
            $table->string('delivery_prefix')->default('DO');
            $table->string('payment_prefix')->default('PAY');
            $table->string('return_prefix')->default('SR');
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
        Schema::dropIfExists('pos_settings');
    }
}

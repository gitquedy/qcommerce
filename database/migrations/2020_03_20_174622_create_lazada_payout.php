<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLazadaPayout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lazada_payout', function (Blueprint $table) {
            $table->bigIncrements('id');
            //fk
            $table->unsignedBigInteger('shop_id');
            $table->foreign('shop_id')->references('id')->on('shop')->onDelete('cascade');
            $table->string('statement_number');

            $table->float('subtotal1', 10, 2);
            $table->float('subtotal2', 10, 2);
            $table->float('fees_total', 10, 2);
            $table->float('refunds', 10, 2);
            $table->float('other_revenue_total', 10, 2);
            $table->float('guarantee_deposit', 10, 2);
            $table->float('opening_balance', 10, 2);
            $table->float('item_revenue', 10, 2);
            $table->float('shipment_fee_credit', 10, 2);
            $table->float('shipment_fee', 10, 2);
            $table->float('fees_on_refunds_total', 10, 2);
            $table->string('payout');
            $table->float('closing_balance', 10, 2);
            $table->integer('paid');
            $table->integer('reconciled')->default(0);
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
        Schema::dropIfExists('lazada_payout');
    }
}

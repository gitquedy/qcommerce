<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLazadaPayoutFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lazada_payout_fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_no');
            $table->string('transaction_date');
            $table->float('amount');
            $table->string('paid_status');
            $table->string('payment_ref_id')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('reference');
            $table->string('fee_name')->nullable();
            $table->string('trans_type')->nullable();
            $table->string('statement');
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
        Schema::dropIfExists('lazada_payout_fees');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_no');
            //fk
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');

            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->string('billing_period');
            $table->float('amount', 10, 2);
            $table->tinyInteger('paid_status')->default(0)->comment('0=unpaid, 1=paid, 2=failed, 3=cancelled, 4=suspended');
            $table->date('payment_date')->nullable();
            $table->date('next_payment_date')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->string('profile_id')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('payer_firstname')->nullable();
            $table->string('payer_lastname')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('country_code')->nullable();

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
        Schema::dropIfExists('billing');
    }
}

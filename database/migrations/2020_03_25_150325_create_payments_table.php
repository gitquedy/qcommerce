<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payable_id');
            $table->string('payable_type');
            // $table->unsignedBigInteger('sales_id');
            // $table->foreign('sales_id')->references('id')->on('sales')->onDelete('cascade');
            // $table->unsignedBigInteger('customer_id')->nullable();
            // $table->foreign('customer_id')->references('id')->on('customer')->onDelete('SET NULL');
            $table->unsignedBigInteger('people_id')->nullable();
            $table->string('people_type')->nullable();
            $table->string('reference_no');
            $table->date('date');
            $table->string('payment_type');
            $table->string('cheque_no')->nullable();
            $table->string('gift_card_no')->nullable();
            $table->string('cc_no')->nullable();
            $table->string('cc_holder')->nullable();
            $table->string('cc_month')->nullable();
            $table->string('cc_year')->nullable();
            $table->string('cc_type')->nullable();
            $table->float('amount', 10, 2);
            $table->text('attachment')->nullable();
            $table->string('status');
            $table->text('note')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('payments');
    }
}

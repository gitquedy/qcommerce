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

            $table->unsignedBigInteger('package_id');
            $table->foreign('package_id')->references('id')->on('package')->onDelete('cascade');
            
            $table->tinyInteger('paid_status')->default(0);
            
            $table->string('payment_transaction_id')->nullable();
            $table->string('payment_type')->nullable();

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

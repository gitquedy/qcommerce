<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customer');
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->date('date');
            $table->string('reference_no');
            $table->text('note')->nullable();
            $table->string('status');
            $table->float('total', 10, 2);
            $table->float('discount', 10, 2)->default(0);
            $table->float('grand_total', 10, 2);
            $table->float('paid', 10, 2);
            $table->string('payment_status');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by');
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
        Schema::dropIfExists('sales');
    }
}

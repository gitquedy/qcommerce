<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('SET NULL');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('SET NULL');;
            $table->string('supplier_name');
            $table->date('date');
            $table->string('reference_no');
            $table->text('note')->nullable();
            $table->string('status');
            $table->float('total', 10, 2);
            $table->float('discount', 10, 2)->default(0);
            $table->float('grand_total', 10, 2);
            $table->float('paid', 10, 2);
            $table->string('payment_status');
            $table->float('shipping_fee', 10, 2)->default(0);
            $table->float('other_fees', 10, 2)->default(0);
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
        Schema::dropIfExists('purchases');
    }
}

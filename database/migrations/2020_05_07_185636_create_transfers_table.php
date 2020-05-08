<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->date('date');
            $table->string('reference_no');
            $table->unsignedBigInteger('from_warehouse_id')->nullable();
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('SET NULL');
            $table->unsignedBigInteger('to_warehouse_id')->nullable();
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('SET NULL');
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
        Schema::dropIfExists('transfers');
    }
}

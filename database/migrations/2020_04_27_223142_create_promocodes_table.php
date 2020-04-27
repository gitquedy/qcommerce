<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('uses')->unsigned()->nullable();
            $table->integer('max_uses')->unsigned()->nullable();
            $table->integer('max_uses_business')->unsigned()->nullable();
            $table->integer('discount_amount'); 
            $table->string('discount_type');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
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
        Schema::dropIfExists('promocodes');
    }
}

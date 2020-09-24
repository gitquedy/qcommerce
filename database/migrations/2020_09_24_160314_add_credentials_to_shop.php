<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCredentialsToShop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop', function (Blueprint $table) {
            $table->string('pro_authentication_type')->nullable();
            $table->string('pro_username')->nullable();
            $table->string('pro_password')->nullable();
            $table->unsignedTinyInteger('pro_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop', function (Blueprint $table) {
            $table->dropColumn('pro_authentication_type');
            $table->dropColumn('pro_username');
            $table->dropColumn('pro_password');
            $table->dropColumn('pro_status');
        });
    }
}


//php artisan migrate:refresh --path=/database/migrations/2020_09_24_160314_add_credentials_to_shop.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package', function (Blueprint $table) {
            $table->bigIncrements('id');

            // bool 1 or 0 
            // unsigned integer 0 = unlimited

            $table->unsignedInteger('no_of_orders');
            $table->unsignedInteger('no_of_shops');
            $table->unsignedInteger('no_of_users');
            $table->unsignedInteger('no_of_warehouse');

            // lazada,shopee,woocomerce separated by comma -> 0 if ALl
            $table->string('sales_channel');

            // recon
            $table->boolean('return');
            $table->boolean('payment');
            $table->boolean('shipping_fee');

            // inventory
            $table->boolean('inventory');
            $table->boolean('sync_inventory');
            $table->boolean('stock_transfer');
            $table->boolean('purchase_order');

            // offline sales
            $table->boolean('offline_sales');
            $table->boolean('customer_management');

            // reports
            $table->boolean('stock_alert');
            $table->boolean('out_of_stock');
            $table->boolean('daily_sales');
            $table->boolean('top_selling_products');

            $table->float('price', 15, 2);
            $table->float('annual_price', 15, 2);
            $table->string('name');
            $table->string('image')->default('default-image.jpg');
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('package');
    }
}

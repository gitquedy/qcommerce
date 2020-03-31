    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('icon')->default('award');
            $table->float('monthly_cost', 15, 2);
            $table->float('promo_monthly_cost', 15, 2)->default(0);
            $table->float('yearly_cost', 15, 2);
            $table->float('promo_yearly_cost', 15, 2)->default(0);
            $table->date('promo_start')->nullable();
            $table->date('promo_end')->nullable();
            $table->integer('order_processing')->nullable();
            $table->string('sales_channels')->nullable();
            $table->integer('users')->nullable();
            $table->integer('accounts_marketplace')->nullable();
            $table->boolean('return_recon')->default(false);
            $table->boolean('payment_recon')->default(false);
            $table->boolean('shipping_overcharge_recon')->default(false);
            $table->boolean('inventory_management')->default(false);
            $table->boolean('sync_inventory')->default(false);
            $table->integer('no_of_warehouse')->default(1);
            $table->boolean('stock_transfer')->default(false);
            $table->boolean('purchase_orders')->default(false);
            $table->boolean('add_sales')->default(false);
            $table->boolean('customers_management')->default(false);
            $table->boolean('stock_alert_monitoring')->default(false);
            $table->boolean('out_of_stock')->default(false);
            $table->boolean('daily_sales')->default(false);
            $table->boolean('top_selling_products')->default(false);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('plans');
    }
}

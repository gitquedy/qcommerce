<?php

use App\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::create([
			'name' => 'FREE',
			'icon' => 'bookmark',
			'monthly_cost' => 0.00,
			'yearly_cost' => 0.00,
			'order_processing' => 50,
			'sales_channels' => 'Lazada/Shopee',
			'users' => 1,
			'accounts_marketplace' => 1,
			'return_recon' => 0,
			'payment_recon' => 0,
			'shipping_overcharge_recon' => 0,
			'inventory_management' => 0,
			'sync_inventory' => 0,
			'no_of_warehouse' => 1,
			'stock_transfer' => 0,
			'purchase_orders' => 0,
			'add_sales' => 0,
			'customers_management' => 0,
			'stock_alert_monitoring' => 0,
			'out_of_stock' => 0,
			'daily_sales' => 0,
			'top_selling_products' => 0
        ]);
        Plan::create([
			'name' => 'SME',
			'icon' => 'shopping-bag',
			'monthly_cost' => 350.00,
			'yearly_cost' => 4200.00,
			'order_processing' => 100,
			'sales_channels' => 'Lazada/Shopee',
			'users' => 1,
			'accounts_marketplace' => 2,
			'return_recon' => 0,
			'payment_recon' => 0,
			'shipping_overcharge_recon' => 0,
			'inventory_management' => 0,
			'sync_inventory' => 0,
			'no_of_warehouse' => 1,
			'stock_transfer' => 0,
			'purchase_orders' => 0,
			'add_sales' => 0,
			'customers_management' => 0,
			'stock_alert_monitoring' => 0,
			'out_of_stock' => 0,
			'daily_sales' => 0,
			'top_selling_products' => 1
        ]);
        Plan::create([
			'name' => 'Business',
			'icon' => 'briefcase',
			'monthly_cost' => 1000.00,
			'yearly_cost' => 12000.00,
			'order_processing' => 300,
			'sales_channels' => 'Lazada/Shopee',
			'users' => 5,
			'accounts_marketplace' => 5,
			'return_recon' => 1,
			'payment_recon' => 1,
			'shipping_overcharge_recon' => 1,
			'inventory_management' => 1,
			'sync_inventory' => 1,
			'no_of_warehouse' => 2,
			'stock_transfer' => 1,
			'purchase_orders' => 0,
			'add_sales' => 0,
			'customers_management' => 0,
			'stock_alert_monitoring' => 1,
			'out_of_stock' => 1,
			'daily_sales' => 1,
			'top_selling_products' => 1
        ]);
        Plan::create([
			'name' => 'Enterprise',
			'icon' => 'shopping-cart',
			'monthly_cost' => 2500.00,
			'yearly_cost' => 30000.00,
			'order_processing' => 1000,
			'sales_channels' => 'Lazada/Shopee/Woocommerce',
			'users' => 10,
			'accounts_marketplace' => 10,
			'return_recon' => 1,
			'payment_recon' => 1,
			'shipping_overcharge_recon' => 1,
			'inventory_management' => 1,
			'sync_inventory' => 1,
			'no_of_warehouse' => 3,
			'stock_transfer' => 1,
			'purchase_orders' => 1,
			'add_sales' => 1,
			'customers_management' => 1,
			'stock_alert_monitoring' => 1,
			'out_of_stock' => 1,
			'daily_sales' => 1,
			'top_selling_products' => 1,
        ]);
        Plan::create([
			'name' => 'Corporate',
			'icon' => 'database',
			'monthly_cost' => 5000.00,
			'yearly_cost' => 60000.00,
			'order_processing' => 0,
			'sales_channels' => 'All',
			'users' => 0,
			'accounts_marketplace' => 0,
			'return_recon' => 1,
			'payment_recon' => 1,
			'shipping_overcharge_recon' => 1,
			'inventory_management' => 1,
			'sync_inventory' => 1,
			'no_of_warehouse' => 0,
			'stock_transfer' => 1,
			'purchase_orders' => 1,
			'add_sales' => 1,
			'customers_management' => 1,
			'stock_alert_monitoring' => 1,
			'out_of_stock' => 1,
			'daily_sales' => 1,
			'top_selling_products' => 1
        ]);
    }
}

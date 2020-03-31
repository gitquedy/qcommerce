<?php

use Illuminate\Database\Seeder;
use App\Package;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Package::create([
            'name' =>  'FREE',
        	'no_of_orders' => 50,
            'no_of_shops' =>  1,
            'no_of_users' =>  1,
            'no_of_warehouse' =>  1,
            'sales_channel' =>  'lazada,shopee',
            'return' =>  0,
            'payment' =>  0,
            'shipping_fee' => 0,
            'inventory' =>  0,
            'sync_inventory' =>  0,
            'stock_transfer' =>  0,
            'purchase_order' =>  0,
            'offline_sales' =>  0,
            'customer_management' =>  0,
            'stock_alert' =>  0,
            'out_of_stock' =>  0,
            'daily_sales' =>  1,
            'top_selling_products' =>  1,
            'price' =>  0,
            'annual_price' => 0,
            'image' =>  'https://app.powersell.com/vendor/root/images/logos/pgk_smart.png',
            'status' =>  1,
        ]);

        Package::create([
            'name' =>  'SME',
            'no_of_orders' => 100,
            'no_of_shops' =>  2,
            'no_of_users' =>  1,
            'no_of_warehouse' =>  1,
            'sales_channel' =>  'lazada,shopee',
            'return' =>  0,
            'payment' =>  0,
            'shipping_fee' => 0,
            'inventory' =>  0,
            'sync_inventory' =>  0,
            'stock_transfer' =>  0,
            'purchase_order' =>  0,
            'offline_sales' =>  0,
            'customer_management' =>  0,
            'stock_alert' =>  0,
            'out_of_stock' =>  0,
            'daily_sales' =>  1,
            'top_selling_products' =>  1,
            'price' =>  350,
            'annual_price' => 3000,
            'image' =>  'https://app.powersell.com/vendor/root/images/logos/pgk_smart.png',
            'status' =>  1,
        ]);

        Package::create([
            'name' =>  'BUSINESS',
            'no_of_orders' => 300,
            'no_of_shops' =>  5,
            'no_of_users' =>  5,
            'no_of_warehouse' =>  2,
            'sales_channel' =>  'lazada,shopee',
            'return' =>  1,
            'payment' =>  1,
            'shipping_fee' => 1,
            'inventory' =>  1,
            'sync_inventory' =>  1,
            'stock_transfer' =>  1,
            'purchase_order' =>  0,
            'offline_sales' =>  0,
            'customer_management' =>  0,
            'stock_alert' =>  1,
            'out_of_stock' =>  1,
            'daily_sales' =>  1,
            'top_selling_products' =>  1,
            'price' =>  1000,
            'annual_price' => 10000,
            'image' =>  'https://app.powersell.com/vendor/root/images/logos/pgk_smart.png',
            'status' =>  1,
        ]);


        Package::create([
            'name' =>  'ENTERPRISE',
            'no_of_orders' => 1000,
            'no_of_shops' =>  10,
            'no_of_users' =>  10,
            'no_of_warehouse' =>  3,
            'sales_channel' =>  'lazada,shopee,woocomerce',
            'return' =>  1,
            'payment' =>  1,
            'shipping_fee' => 1,
            'inventory' =>  1,
            'sync_inventory' =>  1,
            'stock_transfer' =>  1,
            'purchase_order' =>  1,
            'offline_sales' =>  1,
            'customer_management' =>  1,
            'stock_alert' =>  1,
            'out_of_stock' =>  1,
            'daily_sales' =>  1,
            'top_selling_products' =>  1,
            'price' =>  2500,
            'annual_price' => 25000,
            'image' =>  'https://app.powersell.com/vendor/root/images/logos/pgk_smart.png',
            'status' =>  1,
        ]);


        Package::create([
            'name' =>  'CORPORATE',
            'no_of_orders' => 0,
            'no_of_shops' =>  0,
            'no_of_users' =>  0,
            'no_of_warehouse' =>  0,
            'sales_channel' =>  'lazada,shopee,woocomerce',
            'return' =>  1,
            'payment' =>  1,
            'shipping_fee' => 1,
            'inventory' =>  1,
            'sync_inventory' =>  1,
            'stock_transfer' =>  1,
            'purchase_order' =>  1,
            'offline_sales' =>  1,
            'customer_management' =>  1,
            'stock_alert' =>  1,
            'out_of_stock' =>  1,
            'daily_sales' =>  1,
            'top_selling_products' =>  1,
            'price' =>  5000,
            'annual_price' => 50000,
            'image' =>  'https://app.powersell.com/vendor/root/images/logos/pgk_smart.png',
            'status' =>  1,
        ]);

    }
}



<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $table = 'package';

    protected $fillable = ['no_of_orders', 'no_of_shops', 'no_of_users', 'no_of_warehouse', 'sales_channel', 'return', 'payment', 'shipping_fee', 'inventory', 'sync_inventory', 'stock_transfer', 'purchase_order', 'offline_sales', 'customer_management', 'stock_alert', 'out_of_stock', 'daily_sales', 'top_selling_products', 'price', 'name', 'image', 'status', 'annual_price'];

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
		'id', 'name', 'monthly_cost', 'yearly_cost', 'order_processing', 'sales_channels', 'users', 'accounts_marketplace', 'return_recon', 'payment_recon', 'shipping_overcharge_recon', 'inventory_management', 'sync_inventory', 'no_of_warehouse', 'stock_transfer', 'purchase_orders', 'add_sales', 'customers_management', 'stock_alert_monitoring', 'out_of_stock', 'daily_sales', 'top_selling_products'
    ];
}

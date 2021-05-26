<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class WoocommerceCustomer extends Model
{
    protected $table = 'woocommerce_customer';

    protected $fillable = ['shop_id', 'woo_customer_id', 'first_name', 'last_name', 'address', 'email', 'mobile_num', 'orders_count', 'orders_worth', 'created_at', 'updated_at'];

    public function fullName() {
      return $this->first_name . ' ' . $this->last_name;
    }

    public function shop()  {
		  return $this->belongsTo(Shop::class, 'shop_id', 'id');
	  }

    public function orders()  {
		  return $this->hasMany(Order::class, 'woocommerce_customer_id', 'id');
	  }
}

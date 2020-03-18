<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
	protected $table = 'order_item';

    protected $fillable = ['order_id', 'product_id', 'price', 'quantity', 'created_at' , 'updated_at'];

    public $timestamps = false;

    public function order(){
    	return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(){
    	return $this->belongsTo(Products::class, 'product_id', 'id');
    }
}

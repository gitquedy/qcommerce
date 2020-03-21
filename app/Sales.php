<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = 'sales';

	protected $fillable = [
        'business_id', 'customer_id', 'customer_first_name', 'customer_last_name', 'date', 'reference_no', 'note', 'status', 'total', 'discount', 'grand_total', 'paid', 'payment_status', 'created_by', 'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
	}

	public function items(){
		return $this->hasMany('App\SaleItems', 'sales_id');
	}
}
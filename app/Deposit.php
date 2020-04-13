<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'business_id', 'customer_id', 'reference_no', 'date', 'amount', 'note', 'created_by', 'updated_by'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
	}

    public function created_by_name(){
        return $this->belongsTo(User::class, 'created_by', 'id');
	}

    public function updated_by_name(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
	}
}

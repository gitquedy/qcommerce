<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sales_id', 'customer_id', 'reference_no', 'date', 'payment_type', 'cheque_no', 'gift_card_no', 'cc_no', 'cc_holder', 'cc_month', 'cc_year', 'cc_type', 'amount', 'attachment', 'status', 'note', 'created_by', 'updated_by'
    ];


    public function sale(){
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
	}
}

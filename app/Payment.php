<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payable_id', 'payable_type', 'reference_no', 'date', 'payment_type', 'cheque_no', 'gift_card_no', 'cc_no', 'cc_holder', 'cc_month', 'cc_year', 'cc_type', 'amount', 'attachment', 'status', 'note', 'created_by', 'updated_by', 'people_id', 'people_type'
    ];
    
    public function payable(){
        return $this->morphTo();
	}

	public function people(){
		return $this->morphTo();
	}
}

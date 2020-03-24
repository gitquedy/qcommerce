<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LazadaPayout extends Model
{
    //
    protected $table = 'lazada_payout';

    protected $fillable = ['shop_id', 'statement_number', 'subtotal1', 'subtotal2', 'fees_total', 'refunds', 'other_revenue_total', 'guarantee_deposit', 'paid', 'opening_balance', 'item_revenue', 'shipment_fee_credit', 'shipment_fee', 'fees_on_refunds_total', 'payout', 'closing_balance', 'created_at', 'updated_at', 'reconciled'];

    public $timestamps = false;

    public function shop(){
		return $this->belongsTo(Shop::class, 'shop_id', 'id');
	}

    public function getPaidStatusDisplay(){
    	$html = '';
    	if($this->paid == 1){
	        $html = '<div class="chip chip-primary"><div class="chip-body"><div class="chip-text">Paid</div></div></div>';
	    }else if($this->paid == 0){
	        $html = '<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">Not Paid</div></div></div>';
	    }
	    return $html;         
    }

    public function getReconciledStatusDisplay(){
    	$html = '';
    	if($this->reconciled == 1){
	        $html = '<div class="chip chip-primary"><div class="chip-body"><div class="chip-text">Confirmed</div></div></div>';
	    }else if($this->reconciled == 0){
	        $html = '<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">Unconfirmed</div></div></div>';
	    }
	    return $html;         
    }

    public function getPaymentFee(){
        return ($this->item_revenue + $this->shipment_fee_credit) - $this->shipment_fee;
    }
}



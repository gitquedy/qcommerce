<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopeePayout extends Model
{
    protected $table = 'shopee_payout_fees';

    protected $fillable = ['shop_id', 'payout_date', 'amount', 'reconciled', 'created_at','updated_at'];

    public $timestamps = false;

    public function shop(){
		return $this->belongsTo(Shop::class, 'shop_id', 'id');
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
    
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\Shop;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use DB;
use Auth;

class ShippingFee extends Model
{
    protected $table = 'lazada_payout_fees';

    protected $fillable = ['id', 'order_no', 'transaction_date', 'amount', 'paid_status', 'payment_ref_id', 'transaction_type', 'reference', 'fee_name', 'trans_type', 'statement'];
     
    public function order(){
		return $this->belongsTo(Order::class, 'order_no', 'id');
	}
     
}



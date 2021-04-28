<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\Shop;
use Carbon\Carbon;
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\UrlConstants;
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



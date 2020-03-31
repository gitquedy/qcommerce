<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Plan;

class Billing extends Model
{

	protected $table = 'billing';

	protected $fillable = ['invoice_no', 'business_id', 'plan_id', 'paid_status', 'payment_transaction_id', 'payment_type'];    

    public static function getNextInvoiceNumber(){
    	//get last record
		$record = Billing::latest()->first();
		
		if($record){
			$expNum = explode('-', $record->invoice_no);
			if (Carbon::now()->format('Y-m') == Carbon::parse($record->created_at)->format('Y-m')){
				$nextInvoiceNumber = $expNum[0]. '-' . $expNum[1] . '-' . ($expNum[2] + 1);
			} else {
			    $nextInvoiceNumber = date('Y-m').'-1';
			}
		}else{
			$nextInvoiceNumber = date('Y-m').'-1';
		}
		
		return $nextInvoiceNumber;
    }

    public function plan(){
    	return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}

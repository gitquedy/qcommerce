<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Payment;
use Illuminate\Database\Eloquent\Relations\Relation;

class Purchases extends Model
{
    protected $table = 'purchases';

    protected $fillable = ['business_id', 'warehouse_id', 'supplier_id', 'supplier_name', 'date', 'reference_no', 'note', 'status', 'total', 'discount', 'grand_total', 'paid', 'payment_status', 'shipping_fee', 'other_fees', 'created_by', 'updated_by'];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
	}

	public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function payments(){
        // return $this->hasMany(Payment::class, 'sales_id', 'id');
        return $this->morphMany(Payment::class, 'payable', 'payable_type');
    }

    public function warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function items(){
		return $this->hasMany(PurchaseItems::class, 'purchases_id');
	}
}

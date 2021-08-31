<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Utilities;

class Settings extends Model
{
	protected $table = 'settings';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_prefix', 'quote_prefix', 'purchase_prefix', 'transfer_prefix', 'delivery_prefix', 'payment_prefix', 'return_prefix', 'adjustment_prefix', 'inventory_prefix', 'customer_name_format', 'business_id', 'expense_prefix'
    ];

    
    public function order_ref(){
        return $this->belongsTo(OrderRef::class, 'id', 'settings_id');
    }

    public function getReference_so() {
        return $this->sales_prefix.sprintf('%04d', $this->order_ref->so);
    }

    public function getReference_qu() {
        return $this->quote_prefix.sprintf('%04d', $this->order_ref->qu);
    }

    public function getReference_po() {
        return $this->purchase_prefix.sprintf('%04d', $this->order_ref->po);
    }

    public function getReference_tr() {
        return $this->transfer_prefix.sprintf('%04d', $this->order_ref->tr);
    }

    public function getReference_do() {
        return $this->delivery_prefix.sprintf('%04d', $this->order_ref->do);
    }

    public function getReference_pay() {
        return $this->payment_prefix.sprintf('%04d', $this->order_ref->pay);
    }

    public function getReference_re() {
        return $this->return_prefix.sprintf('%04d', $this->order_ref->re);
    }

    public function getReference_adj() {
        return $this->adjustment_prefix.sprintf('%04d', $this->order_ref->adj);
    }

    public function getReference_ir() {
        return $this->inventory_prefix.sprintf('%04d', $this->order_ref->ir);
    }

    public function getReference_exp() {
        return $this->expense_prefix.sprintf('%04d', $this->order_ref->ex);
    }
}

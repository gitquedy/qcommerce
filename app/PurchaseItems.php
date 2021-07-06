<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseItems extends Model
{
    protected $table = 'purchase_items';

    protected $fillable = ['purchases_id', 'warehouse_id', 'sku_id', 'sku_code', 'sku_name', 'image', 'unit_price', 'quantity', 'discount', 'subtotal', 'real_unit_price'];

    // public $timestamps = false;

    public function purchases(){
        return $this->belongsTo(Purchases::class, 'purchases_id', 'id');
	}

    public function sku(){
        return $this->belongsTo(Sku::class, 'sku_id', 'id');
	}

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
	}
}

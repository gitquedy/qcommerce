<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItems extends Model
{
    protected $table = 'sale_items';

	protected $fillable = [
        'sales_id', 'warehouse_id', 'sku_id', 'sku_code', 'sku_name', 'image', 'unit_price', 'quantity', 'discount', 'subtotal', 'real_unit_price'
    ];

    public function sales(){
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
	}

    public function sku(){
        return $this->belongsTo(Sku::class, 'sku_id', 'id');
	}

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
	}
}

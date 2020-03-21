<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleItems extends Model
{
    protected $table = 'sale_items';

	protected $fillable = [
        'sales_id', 'sku_id', 'sku_code', 'sku_name', 'image', 'unit_price', 'quantity', 'discount', 'subtotal', 'real_unit_price'
    ];

    public function sku(){
        return $this->belongsTo(Sku::class, 'sku_id', 'id');
	}
}

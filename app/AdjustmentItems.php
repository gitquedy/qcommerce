<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdjustmentItems extends Model
{
    protected $fillable = [
        'adjustment_id', 'sku_id', 'sku_code', 'sku_name', 'image', 'quantity', 'warehouse_id', 'type'
    ];

    public function adjustment(){
        return $this->belongsTo(Adjustment::class, 'adjustment_id', 'id');
	}

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
	}

    public function sku(){
        return $this->hasOne(Sku::class, 'id', 'sku_id');
	}

    public function warehouse_item(){
        return $this->hasOne(WarehouseItems::class, 'sku_id', 'sku_id')->where('warehouse_id', $this->warehouse_id);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdjustmentItems extends Model
{
    protected $table = 'adjustment_items';

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
        return $this->belongsTo(Sku::class, 'sku_id', 'id');
	}

    public function warehouse_item(){
        return $this->hasOne(WarehouseItems::class, 'sku_id', 'sku_id')->where('warehouse_id', $this->warehouse_id);
    }
}

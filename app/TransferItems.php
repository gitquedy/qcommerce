<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferItems extends Model
{
    protected $fillable = ['adjustment_id', 'sku_id', 'sku_code', 'sku_name', 'image', 'quantity', 'from_warehouse_id', 'to_warehouse_id'];

    public function transfer(){
        return $this->belongsTo(Adjustment::class, 'transfer_id', 'id');
	}

    public function from_warehouse(){
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id', 'id');
	}

    public function to_warehouse(){
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id', 'id');
	}

    public function sku(){
        return $this->hasOne(Sku::class, 'id', 'sku_id');
	}

    public function from_warehouse_item(){
        return $this->hasOne(WarehouseItems::class, 'sku_id', 'sku_id')->where('warehouse_id', $this->from_warehouse_id);
    }

    public function to_warehouse_item(){
        return $this->hasOne(WarehouseItems::class, 'sku_id', 'sku_id')->where('warehouse_id', $this->to_warehouse_id);
    }
}

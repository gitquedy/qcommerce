<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseItems extends Model
{
	protected $fillable = [
        'warehouse_id', 'sku_id', 'quantity', 'rack', 'avg_cost'
    ];

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
	}

    public function sku(){
        return $this->belongsTo(Sku::class, 'sku_id', 'id');
	}

    public function transfer_item(){
        return $this->belongsTo(TransferItems::class, 'sku_id', 'sku_id')->where('to_warehouse_id', $this->warehouse_id);
    }
}

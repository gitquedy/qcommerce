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
        return $this->hasOne(Sku::class, 'id', 'sku_id');
	}
}

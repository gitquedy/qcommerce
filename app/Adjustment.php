<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    protected $fillable = [
        'business_id', 'date', 'reference_no', 'warehouse_id', 'note', 'created_by', 'updated_by'
    ];

    public function items(){
        return $this->hasMany(AdjustmentItems::class, 'adjustment_id', 'id');
    }

    public function warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    public function created_by_name(){
        return $this->belongsTo(User::class, 'created_by', 'id');
	}

    public function updated_by_name(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
	}


    public static function applyItemsOnWarehouse($adjustment_id) {
        //add
        $adjustment = Adjustment::findOrFail($adjustment_id);
        foreach ($adjustment->items as $item) {
            $warehouse_qty = isset($item->warehouse_item->quantity)?$item->warehouse_item->quantity:0;
            if($item->type == 'addition') {
                $sku_qty = $item->sku->quantity + $item->quantity;
                $new_quantity = $warehouse_qty + $item->quantity;
            }
            else if($item->type == 'subtraction'){
                $sku_qty = $item->sku->quantity - $item->quantity;
                $new_quantity = $warehouse_qty - $item->quantity;
            }
            $item->sku->update(['quantity' => $sku_qty]);
            $item->warehouse_item()->updateOrCreate(
                ['warehouse_id' => $item->warehouse_id,
                 'sku_id' => $item->sku->id],
                ['quantity' => $new_quantity]
            );
        }
    }

    public static function restoreItemsOnWarehouse($adjustment_id) {
        //subtract
        $adjustment = Adjustment::findOrFail($adjustment_id);
        foreach ($adjustment->items as $item) {
            $warehouse_qty = isset($item->warehouse_item->quantity)?$item->warehouse_item->quantity:0;
            if($item->type == 'subtraction') {
                $sku_qty = $item->sku->quantity + $item->quantity;
                $new_quantity = $warehouse_qty + $item->quantity;
            }
            else if($item->type == 'addition'){
                $sku_qty = $item->sku->quantity - $item->quantity;
                $new_quantity = $warehouse_qty - $item->quantity;
            }
            $item->sku->update(['quantity' => $sku_qty]);
            $item->warehouse_item()->updateOrCreate(
                ['warehouse_id' => $item->warehouse_id,
                 'sku_id' => $item->sku->id],
                ['quantity' => $new_quantity]
            );
        }
    }
}

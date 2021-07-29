<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sku;
use App\SetItem;

class Adjustment extends Model
{
    protected $table = 'adjustments';

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

            $set_of_item = SetItem::where('sku_single_id', $item->sku_id)->get();
            if ($set_of_item) {
                foreach ($set_of_item as $set) {
                    $sku = Sku::find($set->sku_set_id);
                    $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($item->warehouse_id);
                    $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                        ['warehouse_id' => $item->warehouse_id,
                        'sku_id' => $sku->id],
                        ['quantity' => $warehouse_set_quantity]
                    );
                    $sku->update(['quantity' => $sku->computeSetSkuQuantity()]);
                    $sku->updateProductsandPlatforms();
                }
            }
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

            $set_of_item = SetItem::where('sku_single_id', $item->sku_id)->get();
            if ($set_of_item) {
                foreach ($set_of_item as $set) {
                    $sku = Sku::find($set->sku_set_id);
                    $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($item->warehouse_id);
                    $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                        ['warehouse_id' => $item->warehouse_id,
                        'sku_id' => $sku->id],
                        ['quantity' => $warehouse_set_quantity]
                    );
                    $sku->update(['quantity' => $sku->computeSetSkuQuantity()]);
                    $sku->updateProductsandPlatforms();
                }
            }
        }
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sku;
use App\SetItem;

class Transfer extends Model
{
     protected $fillable = [
        'business_id', 'date', 'reference_no', 'from_warehouse_id', 'to_warehouse_id', 'pricegroup_id', 'status', 'note', 'terms', 'created_by', 'updated_by'
    ];

    public function items(){
        return $this->hasMany(TransferItems::class, 'transfer_id', 'id');
    }

    public function from_warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'from_warehouse_id');
    }

    public function to_warehouse() {
        return $this->hasOne(Warehouse::class, 'id', 'to_warehouse_id');
    }

    public function price_group() {
        return $this->hasOne(PriceGroup::class, 'id', 'pricegroup_id');
    }

    public function created_by_name(){
        return $this->belongsTo(User::class, 'created_by', 'id');
	}

    public function updated_by_name(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
	}

    public static function addItemsOnWarehouse($transfer_id, $return = false) {
        //add
        $transfer = Transfer::findOrFail($transfer_id);
        foreach ($transfer->items as $item) {
            $item_qty = isset($item->quantity)?$item->quantity:0;
            $sku_qty = $item->sku->quantity + $item_qty;
            $item->sku->update(['quantity' => $sku_qty]);
            if (!$return) {
                $warehouse_qty = isset($item->to_warehouse_item->quantity)?$item->to_warehouse_item->quantity:0;
                $new_quantity = $warehouse_qty + $item_qty;
                $item->to_warehouse_item()->updateOrCreate(
                    ['warehouse_id' => $item->to_warehouse_id,
                     'sku_id' => $item->sku->id],
                    ['quantity' => $new_quantity]
                );

                $set_of_item = SetItem::where('sku_single_id', $item->sku_id)->get();
                if ($set_of_item) {
                    foreach ($set_of_item as $set) {
                        $sku = Sku::find($set->sku_set_id);
                        $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($item->to_warehouse_id);
                        $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                            ['warehouse_id' => $item->to_warehouse_id,
                            'sku_id' => $sku->id],
                            ['quantity' => $warehouse_set_quantity]
                        );
                        $sku->update(['quantity' => $sku->computeSetSkuQuantity()]);
                        $sku->updateProductsandPlatforms();
                    }
                }
            }
            else {
                $warehouse_qty = isset($item->from_warehouse_item->quantity)?$item->from_warehouse_item->quantity:0;
                $new_quantity = $warehouse_qty + $item_qty;
                $item->from_warehouse_item()->updateOrCreate(
                    ['warehouse_id' => $item->from_warehouse_id,
                     'sku_id' => $item->sku->id],
                    ['quantity' => $new_quantity]
                );

                $set_of_item = SetItem::where('sku_single_id', $item->sku_id)->get();
                if ($set_of_item) {
                    foreach ($set_of_item as $set) {
                        $sku = Sku::find($set->sku_set_id);
                        $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($item->from_warehouse_id);
                        $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                            ['warehouse_id' => $item->from_warehouse_id,
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

    public static function subtractItemsOnWarehouse($transfer_id, $return = false) {
        //subtract
        $transfer = Transfer::findOrFail($transfer_id);
        foreach ($transfer->items as $item) {
            $item_qty = isset($item->quantity)?$item->quantity:0;
            $sku_qty = $item->sku->quantity - $item_qty;
            $item->sku->update(['quantity' => $sku_qty]);
            if (!$return) {
                $warehouse_qty = isset($item->from_warehouse_item->quantity)?$item->from_warehouse_item->quantity:0;
                $new_quantity = $warehouse_qty - $item_qty;
                $item->from_warehouse_item()->updateOrCreate(
                    ['warehouse_id' => $item->from_warehouse_id,
                     'sku_id' => $item->sku->id],
                    ['quantity' => $new_quantity]
                );

                $set_of_item = SetItem::where('sku_single_id', $item->sku_id)->get();
                if ($set_of_item) {
                    foreach ($set_of_item as $set) {
                        $sku = Sku::find($set->sku_set_id);
                        $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($item->from_warehouse_id);
                        $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                            ['warehouse_id' => $item->from_warehouse_id,
                            'sku_id' => $sku->id],
                            ['quantity' => $warehouse_set_quantity]
                        );
                        $sku->update(['quantity' => $sku->computeSetSkuQuantity()]);
                        $sku->updateProductsandPlatforms();
                    }
                }
            }
            else {
                $warehouse_qty = isset($item->to_warehouse_item->quantity)?$item->to_warehouse_item->quantity:0;
                $new_quantity = $warehouse_qty - $item_qty;
                $item->to_warehouse_item()->updateOrCreate(
                    ['warehouse_id' => $item->to_warehouse_id,
                     'sku_id' => $item->sku->id],
                    ['quantity' => $new_quantity]
                );

                $set_of_item = SetItem::where('sku_single_id', $item->sku_id)->get();
                if ($set_of_item) {
                    foreach ($set_of_item as $set) {
                        $sku = Sku::find($set->sku_set_id);
                        $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($item->to_warehouse_id);
                        $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                            ['warehouse_id' => $item->to_warehouse_id,
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
}

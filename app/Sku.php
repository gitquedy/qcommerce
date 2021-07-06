<?php

namespace App;

use Auth;
use DB;
use App\Shop;
use App\Brand;
use App\Lazop;
use App\Order;
use App\Category;
use App\Products;
use App\Utilities;
use Carbon\Carbon;
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\UrlConstants;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $table = 'sku';
    	
    
    // protected $fillable = ['business_id','item_id', 'shop_id', 'SellerSku', 'primary_category', 'name', 'brand', 'supplier' , 'cost', 'price', 'quantity', 'Available', 'updated_at', 'created_at'];
    protected $fillable = ['business_id','code', 'name', 'brand', 'category', 'supplier', 'cost', 'price' , 'quantity', 'alert_quantity', 'type'];
    
    public function SkuImage() {
        if($this->products->first()) {
            return explode('|',$this->products->first()->Images)[0];
        }
        else {
            return asset('images/pages/no-img.jpg');
        }
    }
    
    public function category(){
		return $this->belongsTo(Category::class, 'category','id');
	}
	
	public function brand(){
		return $this->belongsTo(Brand::class, 'brand','id');
	}

	public function products(){
		return $this->hasMany(Products::class, 'seller_sku_id', 'id');
	}

	public function warehouse_items() {
		return $this->hasMany(WarehouseItems::class, 'sku_id', 'id');
	}

    public function set_items() {
        return $this->hasMany(SetItem::class, 'sku_set_id', 'id');
    }

    public function adjustment_items() {
		return $this->hasMany(AdjustmentItems::class, 'sku_id', 'id');
	}

    public function sale_items() {
		return $this->hasMany(SaleItems::class, 'sku_id', 'id');
	}

    public function transfer_items() {
		return $this->hasMany(TransferItems::class, 'sku_id', 'id');
	}
    
    public static function syncStocks($warehouse_id, $items) {
        foreach ($items as $item) {
            $sku = Sku::where('business_id','=', Auth::user()->business_id)->where('id','=', $item['sku_id'])->first();
            $warehouse_item = WarehouseItems::where('warehouse_id', $warehouse_id)->where('sku_id', $item['sku_id'])->first();
            if($sku){
                $sku->quantity -= $item['quantity'];
                if($warehouse_item == null){
                    $warehouse_item = WarehouseItems::create(
                    ['warehouse_id' => $warehouse_id,
                     'sku_id' => $item->sku->id,
                     'quantity' => 0
                    ]
                    );
                }
                $warehouse_item->quantity -= $item['quantity'];
                $warehouse_item->save();
                $sku->save();
                $sku->updateProductsandPlatforms();
            }
        }
    }

    public static function returnStocks($warehouse_id, $items) {
        foreach ($items as $item) {
            $sku = Sku::where('business_id','=', Auth::user()->business_id)->where('id','=', $item->sku_id)->first();
            $warehouse_item = WarehouseItems::where('warehouse_id', $warehouse_id)->where('sku_id', $item->sku_id)->first();
            if($sku){
                $sku->quantity += $item['quantity'];
                if($warehouse_item == null){
                    $warehouse_item = WarehouseItems::create(
                    ['warehouse_id' => $warehouse_id,
                     'sku_id' => $item->sku->id,
                     'quantity' => 0
                    ]
                    );
                }
                $warehouse_item->quantity += $item['quantity'];
                $warehouse_item->save();
                $sku->save();
                $sku->updateProductsandPlatforms();
            }
        }
    }

    public static function reSyncStocks($sku_ids, $cron = false) {
        if ($cron) {
            $Skus = Sku::whereIn('id', $sku_ids)->get();
        }
        else {
            $Skus = Sku::where('business_id', Auth::user()->business_id)->whereIn('id', $sku_ids)->get();
        }
        foreach ($Skus as $sku) {
            $sku->updateProductsandPlatforms();
        }
    }

    public function updateProductsandPlatforms(){
        foreach ($this->products as $product) {
            $product->updateWarehouseQuantity();
            $product->updatePlatform();
        } // foreach
    }    

    public static function getCsv($columnNames, $rows, $fileName = 'file.csv') {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $fileName,
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $callback = function() use ($columnNames, $rows ) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnNames);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function computeSetQuantity($warehouse_id) {
        if ($this->type == 'set') {
            $set_items = SetItem::get_set_item_query()->where('sku_set_id', $this->id)->where('warehouse_id', $warehouse_id)->get();

            $quantity_array = array();
            foreach ($set_items as $item) {
                $quantity_array[] = (int)($item->single_quantity / $item->set_quantity); //computation for available sku parent quantity based on sku child's quantity per set
            }

            $set_quantity = min($quantity_array); //computation for available sku parent quantity based on sku child's quantity per set

            return $set_quantity;
        }
    }
}

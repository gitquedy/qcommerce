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
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $table = 'sku';
    	
    
    // protected $fillable = ['business_id','item_id', 'shop_id', 'SellerSku', 'primary_category', 'name', 'brand', 'supplier' , 'cost', 'price', 'quantity', 'Available', 'updated_at', 'created_at'];
    protected $fillable = ['business_id','code', 'name', 'brand', 'category', 'supplier', 'cost', 'price' , 'quantity', 'alert_quantity'];
    
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
    
    public static function syncStocks($warehouse_id, $items) {
        foreach ($items as $item) {
            $sku = Sku::where('business_id','=', Auth::user()->business_id)->where('id','=', $item['sku_id'])->first();
            $warehouse_item = WarehouseItems::where('warehouse_id', $warehouse_id)->where('sku_id', $item['sku_id'])->first();
            if($sku){
                $sku->quantity -= $item['quantity'];
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
}

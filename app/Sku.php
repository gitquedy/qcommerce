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
        $user = Auth::user();
        $all_shops = Shop::where('business_id', $user->business_id)->where('warehouse_id', $warehouse_id)->orderBy('updated_at', 'desc')->get();
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        foreach ($items as $id => $item) {
            $sku = Sku::where('business_id','=', $user->business_id)->where('id','=', $id)->first();
            $warehouse_item = WarehouseItems::where('warehouse_id', $warehouse_id)->where('sku_id', $id)->first();
            $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$id)->orderBy('updated_at', 'desc')->get();
            if($sku){
                $sku->quantity -= $item['quantity'];
                $warehouse_item->quantity -= $item['quantity'];
                $warehouse_item->save();
                $sku->save();
                foreach ($Sku_prod as $prod) {
                    $shop_id = $prod->shop_id;
                    $prod = Products::where('id', $prod->id)->first();
                    $prod->quantity = $warehouse_item->quantity;
                    $prod->save();
                        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                        <Request>
                            <Product>
                                <Skus>
                                    <Sku>
                                        <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                        <quantity>'.$prod->quantity.'</quantity>
                                    </Sku>
                                </Skus>
                            </Product>
                        </Request>';
                    if(env('lazada_sku_sync', true)){
                        if($prod->site == 'lazada'){
                            $response = $prod->product_price_quantity_update($xml);
                        }
                    }
                }
            }
        }
    }

    public static function returnStocks($warehouse_id, $items) {
        $user = Auth::user();
        $all_shops = Shop::where('business_id', $user->business_id)->where('warehouse_id', $warehouse_id)->orderBy('updated_at', 'desc')->get();
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        foreach ($items as $item) {
            $sku = Sku::where('business_id','=', $user->business_id)->where('id','=', $item->sku_id)->first();
            $warehouse_item = WarehouseItems::where('warehouse_id', $warehouse_id)->where('sku_id', $item->sku_id)->first();
            $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$item->sku_id)->orderBy('updated_at', 'desc')->get();
            if($sku){
                $sku->quantity += $item['quantity'];
                $warehouse_item->quantity += $item['quantity'];
                $warehouse_item->save();
                $sku->save();
                foreach ($Sku_prod as $prod) {
                    $shop_id = $prod->shop_id;

                    $prod = Products::where('id', $prod->id)->first();
                    $prod->quantity = $warehouse_item->quantity;
                    $prod->save();
                        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                        <Request>
                            <Product>
                                <Skus>
                                    <Sku>
                                        <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                        <Quantity>'.$prod->quantity.'</Quantity>
                                    </Sku>
                                </Skus>
                            </Product>
                        </Request>';
                    if(env('lazada_sku_sync', true)){
                        if($prod->site == 'lazada'){
                            $response = $prod->product_price_quantity_update($xml);
                        }
                    }
                }
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
            foreach ($sku->products as $prod) {
                $warehouse_item = $prod->shop->warehouse->items()->where('sku_id', $sku->id)->first();
                $prod->quantity = isset($warehouse_item->quantity)?$warehouse_item->quantity:0;
                $prod->save();
                if($prod->site == 'lazada'){
                        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                        <Request>
                            <Product>
                                <Skus>
                                    <Sku>
                                        <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                        <Quantity>'.$prod->quantity.'</Quantity>
                                    </Sku>
                                </Skus>
                            </Product>
                        </Request>';

                    if(env('lazada_sku_sync', true)){
                        $response = $prod->product_price_quantity_update($xml);
                    }
                }
                else if($prod->site == 'shopee') {
                    $data = [
                        "item_id" => $prod->item_id,
                        "stock" => $prod->quantity,
                        "partner_id" => env('shopee_partner_id'),
                        "shopid" => $prod->shop->shop_id,
                        "timestamp" => strtotime("now"),
                    ];
                    if(env('lazada_sku_sync', true)){
                        $prod->shopeeUpdateStock($data); // Not Working .. WIP
                    }
                }
            }
        }
    }

    
    
}

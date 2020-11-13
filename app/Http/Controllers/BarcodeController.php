<?php

namespace App\Http\Controllers;

use App\Api;
use App\Http\Controllers\Controller;
use App\Lazop;
use App\Library\lazada\LazopClient;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\UrlConstants;
use App\Order;
use App\Products;
use App\Shop;
use App\Sku;
use App\Utilities;
use App\WarehouseItems;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use Yajra\DataTables\Facades\DataTables;

class BarcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['name'=>"Barcode"]
        ];

        return view('barcode.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function checkBarcode(Request $request)  {
        $result = array('error' => '', 'data' => array());
        $input = $request->data;
        $all_shops = $request->user()->business->shops;
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        $order = Order::whereIn('shop_id',$Shop_array)->where(function($query) use ($input)
                {
                    $query->where('tracking_no','=', $input)
                    ->orWhere('id','=',$input)
                    ->orWhere('ordersn', $input);
                })->get()->first();
        if($order) {
            $items = [];
            $items_sku = [];
            $shop = Shop::whereId($order->shop_id)->get()->first();
            if($order->site == 'lazada'){
                $code = $order->ordersn;    
                $api_key = Lazop::get_api_key();
                $api_secret = Lazop::get_api_secret();
                $accessToken = $shop->access_token;
                $client = new LazopClient("https://api.lazada.com.ph/rest", $api_key, $api_secret);
                $r = new LazopRequest("/order/items/get",'GET');
                $r->addApiParam("order_id", $code);
                $response = $client->execute($r, $accessToken);
                $data = json_decode($response, true);
                foreach ($data['data'] as $item) {
                    if($item['cancel_return_initiator'] == ""){
                        $sku = $item['sku'];
                        if(!in_array($sku, $items_sku)) {
                            array_push($items_sku, $sku);
                            $items[$sku] = array(
                                'sku' => $sku,
                                'pic' => $item['product_main_image'],
                                'name' => $item['name'],
                                'qty' => 1,
                                'unit_price' => $item['item_price'],
                                'sub_total' => $item['item_price'],
                            );
                        }
                        else {
                            $items[$sku]['qty'] += 1;
                            $items[$sku]['sub_total'] += $item['item_price'];
                        }
                    }
                }
                
            } else if ($order->site == 'shopee'){
                $client = $shop->shopeeGetClient();
                $data = $client->order->getOrderDetails(['ordersn_list' => array_values([$order->ordersn])])->getData();
                foreach ($data['orders'][0]['items'] as $item) {
                    $sku = $item['item_sku'];
                    // die(var_dump($item));
                    if(!in_array($sku, $items_sku)) {
                        array_push($items_sku, $sku);
                        $items[$sku] = array(
                            'sku' => $sku,
                            'pic' => '',
                            'name' => $item['item_name'],
                            'qty' => $item['variation_quantity_purchased'],
                            'unit_price' => (int)  $item['variation_original_price'],
                            'sub_total' => (int)  $item['variation_original_price'] * $item['variation_quantity_purchased'],
                        );
                    }
                    else {
                        $items[$sku]['qty'] += $item['variation_quantity_purchased'];
                        $items[$sku]['sub_total'] +=  (int) $item['variation_original_price'];
                    }
                }
            }else{
                $result['error'] = "Invalid Code.";
            }
            $result['data']['order'] = $order;
            $result['data']['items'] = $items;
            
        }
        else {
            $result['error'] = "Invalid Code.";
        }
        return $result;
        
        
        // return $order;
        // return json_encode($order);
    }

    public function viewBarcode(Request $request) {
        $data = self::checkBarcode($request);
        if($data['error'] != "") {
            return array('error' => $data['error']);
        }
        return view('barcode.modal.viewdetails', [
            'order' => $data['data']['order'],
            'items' => $data['data']['items'],
        ]);
    }

    public function packedItems(Request $request) {
        // print json_encode($request->all());die();
        $result = false;
        $order = Order::where('id',$request->order_id)->first();
        if($order->packed == 0){
            $order->packed = 1;
            $order->save();
            foreach ($request->items as $sku => $qty) {
                $shop_id = $request->shop_id;
                $shop = Shop::find($shop_id);
                $warehouse_id = $shop->warehouse_id;
                $prod = Products::where('SellerSku', $sku)->where('shop_id', $shop_id)->first();
                if(isset($prod->seller_sku_id)) {
                    $sku = Sku::whereId($prod->seller_sku_id)->where('business_id', Auth::user()->business_id)->first();
                    $sku->quantity -= $qty;
                    $witem = WarehouseItems::where('warehouse_id', $warehouse_id)->where('sku_id', $prod->seller_sku_id)->first();
                    $warehouse_qty = isset($witem->quantity)?$witem->quantity:0;
                    $new_quantity = $warehouse_qty - $qty;
                    $warehouse_item = WarehouseItems::updateOrCreate(
                        ['warehouse_id' => $warehouse_id,
                         'sku_id' => $sku->id],
                        ['quantity' => $new_quantity]
                    );
                    $prod->quantity = $warehouse_item->quantity;
                    $result = $sku->save();
                    $Sku_prod = Products::with('shop')->where('seller_sku_id','=',$sku->id)->orderBy('updated_at', 'desc')->get();
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
        echo json_encode($result);
        
    }
    

    
}
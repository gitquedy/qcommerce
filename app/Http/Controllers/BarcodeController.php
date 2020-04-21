<?php

namespace App\Http\Controllers;

use App\Api;
use App\Order;
use App\Shop;
use App\Products;
use App\Sku;
use Illuminate\Http\Request;
use App\Lazop;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

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
                    $sku = $item['sku'];
                    if(!in_array($sku, $items_sku)) {
                        array_push($items_sku, $sku);
                        $items[$sku] = array(
                            'sku' => $sku,
                            'pic' => $item['product_main_image'],
                            'name' => $item['name'],
                            'qty' => 1,
                        );
                    }
                    else {
                        $items[$sku]['qty'] += 1;
                    }
                }
                
            } else if ($order->site == 'shopee'){
                $client = $shop->shopeeGetClient();
                $data = $client->order->getOrderDetails(['ordersn_list' => array_values([$order->ordersn])])->getData();
                foreach ($data['orders'][0]['items'] as $item) {
                    $sku = $item['item_sku'];
                    if(!in_array($sku, $items_sku)) {
                        array_push($items_sku, $sku);
                        $items[$sku] = array(
                            'sku' => $sku,
                            'pic' => '',
                            'name' => $item['item_name'],
                            'qty' => $item['variation_quantity_purchased'],
                        );
                    }
                    else {
                        $items[$sku]['qty'] += $item['variation_quantity_purchased'];
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
        $result = false;
        $order = Order::where('id',$request->order_id)->first();
        if($order->packed == 0){
            $order->packed = 1;
            $order->save();
            foreach ($request->items as $sku => $qty) {
                $shop_id = $request->shop_id;
                $access_token = Shop::find($shop_id)->access_token;
                $prod = Products::where('SellerSku', $sku)->first();
                if($prod->seller_sku_id) {
                    $sku = Sku::whereId($prod->seller_sku_id)->first();
                    $sku->quantity -= $qty;
                    $result = $sku->save();
                    $Sku_prod = Products::with('shop')->where('seller_sku_id','=',$sku->id)->orderBy('updated_at', 'desc')->get();
                    foreach ($Sku_prod as $prod) {
                        $shop_id = $prod->shop_id;
                        $access_token = Shop::find($shop_id)->access_token;
                        $prod = Products::where('id', $prod->id)->first();
                        $prod->quantity = $sku->quantity;
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
                                $response = Products::product_update($access_token,$xml);
                            }
                        }
                    }
                }
            }
        }
        echo json_encode($result);
        
    }
    

    
}
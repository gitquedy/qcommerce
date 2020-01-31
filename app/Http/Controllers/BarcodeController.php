<?php

namespace App\Http\Controllers;

use App\Api;
use App\Order;
use App\Shop;
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
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        $order = Order::whereIn('shop_id',$Shop_array)->where(function($query) use ($input)
                {
                    $query->where('tracking_no','=', $input)
                    ->orWhere('id','=',$input);
                })->get()->first();
        if($order) {
            $shop = Shop::whereId($order->shop_id)->get()->first();
            $code = $order->id;
            $api_key = Lazop::get_api_key();
            $api_secret = Lazop::get_api_secret();
            $accessToken = $shop->access_token;
            $client = new LazopClient("https://api.lazada.com.ph/rest", $api_key, $api_secret);
            $r = new LazopRequest("/order/items/get",'GET');
            $r->addApiParam("order_id", $code);
            $response = $client->execute($r, $accessToken);
            $data = json_decode($response, true);
            $items = array();
            $items_sku = array();
            foreach ($data['data'] as $item) {
                $sku = $item['sku'];
                if(!in_array($sku, $items_sku)) {
                    array_push($items_sku, $sku);
                    $items[$sku] = array(
                        'model' => $sku,
                        'pic' => $item['product_main_image'],
                        'name' => $item['name'],
                        'qty' => 1,
                    );
                }
                else {
                    $items[$sku]['qty'] += 1;
                }
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
    

    
}
<?php

namespace App\Http\Controllers;

use App\Api;
use App\Http\Controllers\Controller;
use App\Lazop;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\UrlConstants;
use App\Order;
use App\Products;
use App\Shop;
use App\Sku;
use App\Utilities;
use App\WarehouseItems;
use App\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use Yajra\DataTables\Facades\DataTables;
use Oseintow\Shopify\Facades\Shopify;
use App\Jobs\BarcodePackItems;

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
        $order = Order::whereIn('shop_id',$Shop_array)->where('site', '!=' , 'shopify')->where(function($query) use ($input)
                {
                    $query->where('tracking_no','=', $input)
                    ->orWhere('id','=',$input)
                    ->orWhere('ordersn', '=', $input);
                })->get()->first(); //shopee, lazada if null search in shopify with dif query

        if($order == null){
           $order = DB::table('order')->join('shop', 'shop.id' , '=' , 'order.shop_id')
           ->select('order.*', 'shop.short_name')->where('order.site', 'shopify')->whereIn('order.shop_id',$Shop_array)
           ->where(DB::raw("CONCAT(shop.short_name, order.order_no)"), 'LIKE',  $input )->get()->first();
        }
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
                    if($item['cancel_return_initiator'] == "null-null"){
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
                    $sku = $item['item_id'];
                    // die(var_dump($item));
                    if(!in_array($sku, $items_sku)) {
                        array_push($items_sku, $sku);
                        $pic = OrderItem::join('products', 'order_item.product_id', '=', 'products.id')->where('order_item.order_id', $order->id)->where('products.item_id', $sku)->first()->Images;
                        $items[$sku] = array(
                            'sku' => $sku,
                            'pic' => explode('|', $pic)[0],
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
            }else if($order->site == 'shopify'){
                $order_items = Shopify::setShopUrl($shop->domain)
                ->setAccessToken($shop->access_token)
                ->get("admin/api/2020-07/orders/". $order->ordersn .".json");
                foreach($order_items['line_items'] as $item){
                    $sku = $item->variant_id;
                    $items[$sku] = [
                        'sku' => $sku,
                        'pic' => '',
                        'name' => $item->name,
                        'qty' => $item->quantity,
                        'unit_price' => $item->price,
                        'sub_total' => $item->price * $item->quantity,
                    ];
                }
            } else if ($order->site == 'woocommerce') {
                $client = $shop->woocommerceGetClient();
                $order_items = $client->get('orders/' . $order->ordersn);
                foreach ($order_items->line_items as $item) {
                    $pic = OrderItem::join('products', 'order_item.product_id', '=', 'products.id')->where('order_item.order_id', $order->id)->where('products.item_id', $item->product_id)->first()->Images;
                    $sku = $item->sku;
                    if ($sku == '') {
                        $sku = $item->product_id;
                    }
                    $items[$sku] = [
                        'sku' => $sku,
                        'pic' => $pic,
                        'name' => $item->name,
                        'qty' => $item->quantity,
                        'unit_price' => $item->price,
                        'sub_total' => (double)$item->subtotal,
                    ];
                }
            }
            else{
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
        $packItems = new BarcodePackItems($request->all(), Auth::user()->business_id);
        dispatch($packItems)->onConnection('database');
        return 'true';
    }
    

    
}
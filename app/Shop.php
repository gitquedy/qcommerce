<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\Order;
use App\Products;
use App\Utilities;
use App\ShippingFee;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use Auth;
use DB;
use App\Policies\ShopPolicy;

class Shop extends Model
{
    protected $table = 'shop';

    protected $fillable = ['user_id', 'name', 'short_name', 'refresh_token', 'access_token', 'expires_in', 'active', 'email', 'is_first_time', 'shop_id', 'site'];

    public static $statuses = [
              'shipped', 'ready_to_ship', 'pending', 'delivered', 'returned', 'failed', 'unpaid', 'canceled', 
    ];

    public static $shopee_statuses = ['UNPAID','READY_TO_SHIP','RETRY_SHIP','SHIPPED','TO_CONFIRM_RECEIVE','IN_CANCEL','CANCELLED','TO_RETURN','COMPLETED'];

    protected $policies = [
        Shop::class => ShopPolicy::class,
    ];
    //pending = READY_TO_SHIP
    
    public function products(){
		return $this->belongsTo(Products::class, 'id','shop_id');
	}
    
    public function syncOrders($date = '2018-01-01', $step = '+3 day'){
        try {
            $this->update(['active' => 2]);
            if($this->site == 'lazada'){
                $dates = Utilities::getDaterange($date, Carbon::now()->addDays(1)->format('Y-m-d'), 'c', $step);
                $created_before_increment = 1;
                $orders = [];
                $length = count($dates);
                foreach($dates as $index => $date){
                    $created_before = array_key_exists($created_before_increment, $dates) ? $dates[$created_before_increment] : $date;
                    $created_before_increment += 1;
                    $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                    $r = new LazopRequest('/orders/get','GET');
                    $r->addApiParam('created_after', $date);
                    $r->addApiParam('created_before', $created_before);
                    $r->addApiParam('sort_by','updated_at');
                    $result = $c->execute($r, $this->access_token);
                    $data = json_decode($result, true);
                    if(isset($data['data']['orders'])){
                        $orders = array_merge_recursive($data['data']['orders'], $orders);
                    }
                }
                if($orders){
                    $orders = array_map(function($order){
                        $status = $order['statuses'][0];
                        if(array_key_exists(1,$order['statuses'])){
                             $status = $order['statuses'][1];
                        }
                        $printed = $status == 'ready_to_ship' || $status == 'pending' ? false : true;
                        $order['printed'] = $printed;
                        $order['price'] = Order::tofloat($order['price']);
                        unset($order['statuses']);
                        unset($order['address_billing']);
                        unset($order['address_shipping']);
                        unset($order['order_number']);
                        $order = array_merge($order, ['id' => $order['order_id'], 'status' => $status, 'shop_id' => $this->id, 'site' => 'lazada']);
                        unset($order['order_id']);     
                        $record = Order::updateOrCreate(
                        ['id' => $order['id']], $order);
                        return $order;
                    }, $orders);
                }
            }else if($this->site == 'shopee'){
                $orders = $this->shopeeGetOrdersPerDate($date);
                $this->shopeeSaveOrdersPerSN($orders);
            }
             $this->update(['active' => 1, 'is_first_time' => false]);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
        }
        // return $data;
    }

    public function syncShippingDetails($start_date, $end_date) {
        try {
            if($this->site == 'lazada'){
                $order_ids = array();
                foreach (array(7,8) as $trans_type) {
                    $lazada_payout_fees = [];
                    $c = new LazopClient(UrlConstants::getPH(),Lazop::get_api_key(),Lazop::get_api_secret());
                    $r = new LazopRequest('/finance/transaction/detail/get','GET');
                    $r->addApiParam('trans_type', $trans_type);
                    $r->addApiParam('start_time', $start_date);
                    $r->addApiParam('end_time', $end_date);
                    $result = $c->execute($r, $this->access_token);
                    $data = json_decode($result, true);
                    if(isset($data['data'])){
                        $lpf_ids = array();
                        $lpf_data = array();
                        $result = $data['data'];
                        foreach($result as $lpf) {
                            if(!in_array($lpf['order_no'], $order_ids)) {
                                array_push($order_ids, $lpf['order_no']);
                            }
                            if(!in_array($lpf['order_no'], $lpf_ids)) {
                                $lpf_ids[] = $lpf['order_no'];
                                unset($lpf['shipping_provider']);
                                unset($lpf['WHT_included_in_amount']);
                                unset($lpf['lazada_sku']);
                                unset($lpf['orderItem_no']);
                                unset($lpf['orderItem_status']);
                                unset($lpf['shipping_speed']);
                                unset($lpf['WHT_amount']);
                                unset($lpf['transaction_number']);
                                unset($lpf['seller_sku']);
                                unset($lpf['details']);
                                unset($lpf['VAT_in_amount']);
                                unset($lpf['shipment_type']);
                                $lpf['trans_type'] = $trans_type;
                                $lpf_data[] = $lpf;
                            }
                            else {
                                $key = array_search($lpf['order_no'], $lpf_ids);
                                $lpf_data[$key]['amount'] += $lpf['amount'];
                            }
                        }
                        $lazada_payout_fees = $lpf_data;
                    }
                    if($lazada_payout_fees){
                        foreach ($lazada_payout_fees as $l) {
                            $order_no = $l['order_no'];
                            $fee_name = $l['fee_name'];
                            $record = ShippingFee::updateOrCreate(['order_no' => $order_no, 'fee_name' => $fee_name], $l);
                        }
                    }
                }
                // dd($order_ids);
                $overcharge_ids = array();
                foreach ($order_ids as $id) {
                    $seller = ShippingFee::whereOrderNo($id)->where('trans_type', 7)->first();
                    $customer = ShippingFee::whereOrderNo($id)->where('trans_type', 8)->first();
                    if(isset($seller['amount']) && isset($customer['amount'])) {
                        $seller_fee = abs(round($seller['amount']));
                        $customer_fee = abs(round($customer['amount']));
                        if($seller_fee > $customer_fee) {
                            array_push($overcharge_ids, $id);
                        }
                    }
                }
                $overcharge = Order::whereIn('id', $overcharge_ids)->where('shipping_fee_reconciled', 0)->update(['shipping_fee_reconciled' => 1]);
            } else if($this->site == 'shopee'){
                // code
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
            print json_encode($output);
        }
    }

    public function shopeeGetClient(){
        return new \Shopee\Client(['secret' => Shopee::shopee_app_key(), 'partner_id' => Shopee::shopee_partner_id(),
                    'shopid' => $this->shop_id,
                ]);;
    }

    public function shopeeGetOrdersPerDate($date){
        $client = $this->shopeeGetClient();
        $dates = Utilities::getDaterange($date, Carbon::now()->addDays(1)->format('Y-m-d'), 'Y-m-d', '+1 day');
        $orders  = [];
        $created_before_increment = 1;
        foreach($dates as $date){
            $created_before = array_key_exists($created_before_increment, $dates) ? $dates[$created_before_increment] : $date;
            $created_before_increment += 1;
            $more = true;
            $offset = 0;
            while($more){
                $params = [
                    'create_time_from' => Carbon::createFromFormat('Y-m-d', $date)->timestamp,
                    'create_time_to' => Carbon::createFromFormat('Y-m-d', $created_before)->timestamp,
                    'pagination_entries_per_page' => 100,
                    'pagination_offset' => $offset,
                ];
                $offset += 100;
                $result = $client->order->getOrdersList($params)->getData();
                if(isset($result['orders'])){
                    $more = $result['more'];
                    if(count($result['orders']) > 0){
                        foreach($result['orders'] as $order){
                            $orders[] = $order;
                        }
                    }
                }
            }
        }
        return $orders;
    }

    public function shopeeSaveOrdersPerSN($orders){
        $client = $this->shopeeGetClient();
        $orders = array_chunk($orders,50);
        $ordersn_list = [];
        foreach($orders as $order){
            $ordersn_list = [];
            foreach($order as $o){
                $ordersn_list[] = $o['ordersn'];
            }
            $order_details = $client->order->getOrderDetails(['ordersn_list' => array_values($ordersn_list)])->getData();
            if(isset($order_details['orders'])){
                foreach($order_details['orders'] as $order){
                    $printed = $order['order_status'] == 'READY_TO_SHIP' || $order['order_status'] == 'RETRY_SHIP' || $order['order_status'] == 'UNPAID' ? false : true;
                    $orders_details = [
                        'ordersn' => $order['ordersn'],
                        'payment_method' => $order['payment_method'],
                        'price' => $order['total_amount'],
                        'created_at' => Carbon::createFromTimestamp($order['create_time'])->toDateTimeString(),
                        'updated_at' => Carbon::createFromTimestamp($order['update_time'])->toDateTimeString(),
                        'shop_id' => $this->id,
                        'site' => 'shopee',
                        'items_count' => count($order['items']),
                        'status' => $order['order_status'],
                        'tracking_no' => $order['tracking_no'],
                        'shipping_fee' => $order['actual_shipping_cost'],
                        'customer_first_name' => $order['recipient_address']['name'],
                        'printed' => $printed,
                    ];
                    $record = Order::updateOrCreate(
                    ['ordersn' => $orders_details['ordersn']], $orders_details);
                }
            }
        }
    }

    public function refreshToken(){
        if($this->site == 'lazada'){
            $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
            $r = new LazopRequest('/auth/token/refresh');
            $r->addApiParam('refresh_token', $this->refresh_token);
            $result = $c->execute($r);
            $response = json_decode($result, true);
            if(! isset($response['message'])){
                $data = [
                    'refresh_token' => $response['refresh_token'],
                    'access_token' => $response['access_token'],
                    'expires_in' => Carbon::now()->addDays(6),
                ];
                $this->update($data);
                return true;
            }else{
                return false;
            }
        }
    }

    public function orders($status = null){
        $orders = $this->hasMany(Order::class, 'shop_id', 'id');
        if($status){
            if($this->site == 'lazada'){
                $orders->where('status', $status);
            }else if($this->site == 'shopee'){
                if($status == 'pending'){
                    $orders->where('status','READY_TO_SHIP');
                }
                else if($status == 'ready_to_ship'){
                    $orders->where('status','READY_TO_SHIP');
                }
                else if($status == 'shipped'){
                    $orders->where('status','SHIPPED');
                }
                else if($status == 'delivered'){
                   $orders->where('status','COMPLETED');
                }
            }
            
        }
        return $orders;
    }
    
    public static function get_auth_shops(){
        
        $user_id = Auth::user()->id;
        
        $result = Shop::where('user_id','=',$user_id)->get();
        
        return $result;
        
    }

    public function syncShopeeProducts($date = '2018-01-01', $step = '+10 day'){
        if($this->site == 'shopee'){
            $client = $this->shopeeGetClient();
            $dates = Utilities::getDaterange($date, Carbon::now()->addDays(1)->format('Y-m-d'), 'Y-m-d', $step);
            $products = [];
            $created_before_increment = 1;
            foreach($dates as $date){
                $created_before = array_key_exists($created_before_increment, $dates) ? $dates[$created_before_increment] : $date;
                $created_before_increment += 1;
                $more = true;
                $offset = 0;
                while($more){
                    $params = [
                        'update_time_from' => Carbon::createFromFormat('Y-m-d', $date)->timestamp,
                        'update_time_to' => Carbon::createFromFormat('Y-m-d', $created_before)->timestamp,
                        'pagination_entries_per_page' => 100,
                        'pagination_offset' => $offset,
                    ];
                    $offset += 100;
                    $result = $client->item->getItemsList($params)->getData();
                    if(isset($result['items'])){
                        $more = $result['more'];
                        if(count($result['items']) > 0){
                            foreach($result['items'] as $product){
                                $products[] = $product;
                            }
                        }
                    }else{
                        $more = false;
                    }
                }
            }
            $this->shopeeSaveProductsPerItem($products);
        }
        
    }

    public function shopeeSaveProductsPerItem($products){
        $client = $this->shopeeGetClient();
        foreach($products as $product){
            if(isset($product['item_id'])){
                $product_details = $client->item->getItemDetail(['item_id' => $product['item_id']])->getData();
                if(count($product_details['item']) > 0){
                    $product_details = [
                    'shop_id' => $this->id,
                    'site' => 'shopee',
                    'SkuId' => $product_details['item']['item_sku'],
                    'SellerSku' => $product_details['item']['item_sku'],
                    'item_id' => $product_details['item']['item_id'],
                    'price' => $product_details['item']['price'],
                    'Images' => implode('|', $product_details['item']['images']),
                    'name' => $product_details['item']['name'],
                    'Status' => $product_details['item']['status'],
                    'created_at' => Carbon::createFromTimestamp($product_details['item']['create_time'])->toDateTimeString(),
                    'updated_at' => Carbon::createFromTimestamp($product_details['item']['update_time'])->toDateTimeString(),
                    ];
                    $record = Products::updateOrCreate(
                    ['shop_id' => $product_details['shop_id'], 'item_id' => $product_details['item_id']], $product_details);
                }
            }
        }
    }

    public function totalSales(){
       $datas = ['week' => 0, 'yesterday' => 0, 'today' => 0, 'month' => 0];
       foreach($datas as $key => $val){
            if($key == 'week'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('price');
            }else if($key == 'today'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today())->sum('price');
            }else if($key == 'yesterday'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today()->subDays(1))->sum('price');
            }else if($key == 'month'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->where('created_at', '>=', Carbon::now()->firstOfMonth()->toDateTimeString())->where('created_at', '<=', Carbon::now()->endOfMonth()->toDateTimeString())->sum('price');
            }
       }
       return (object) $datas;
    }

    public function totalOrders(){
        $datas = ['week' => 0, 'yesterday' => 0, 'today' => 0, 'month' => 0];
       foreach($datas as $key => $val){
            if($key == 'week'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
            }else if($key == 'today'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today())->count();
            }else if($key == 'yesterday'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today()->subDays(1))->count();
            }else if($key == 'month'){
                $datas[$key] = $this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->where('created_at', '>=', Carbon::now()->firstOfMonth()->toDateTimeString())->where('created_at', '<=', Carbon::now()->endOfMonth()->toDateTimeString())->count();
            }
       }
       return (object) $datas;
    }

    public function getImgSiteDisplay(){
        return '<img src="'.asset('images/shop/30x30/'. $this->site.'.png').'" alt="'. $this->site .'" style="width:15px; height:15px"> ' . '<span style="padding-left: 5px;font-size:13px">'. $this->short_name .' </span>';
    }
}


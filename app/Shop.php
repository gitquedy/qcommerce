<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\LazadaPayout;
use App\ShopeePayout;
use App\Woocommerce;
use App\Order;
use App\OrderItem;
use App\Products;
use App\Utilities;
use App\ShippingFee;
use Carbon\Carbon;
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\UrlConstants;
use Auth;
use DB;
use App\Policies\ShopPolicy;
use Oseintow\Shopify\Facades\Shopify;
use Automattic\WooCommerce\Client;

class Shop extends Model
{
    protected $table = 'shop';

    protected $fillable = ['business_id', 'name', 'short_name', 'refresh_token', 'access_token', 'expires_in', 'active', 'email', 'is_first_time', 'shop_id', 'site', 'warehouse_id', 'domain', 'pro_authentication_type', 'pro_username', 'pro_password', 'pro_status'
        ];

    public static $statuses = [
              'shipped', 'ready_to_ship', 'pending', 'delivered', 'returned', 'failed', 'unpaid', 'canceled', 
    ];

    public static $shopee_statuses = ['UNPAID','READY_TO_SHIP','RETRY_SHIP','SHIPPED','TO_CONFIRM_RECEIVE','IN_CANCEL','CANCELLED','TO_RETURN','COMPLETED'];

    protected $policies = [
        Shop::class => ShopPolicy::class,
    ];
    //pending = READY_TO_SHIP

    public function business(){
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }
    
    public function products(){
		return $this->hasMany(Products::class, 'shop_id','id');
	}

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
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
            else if($this->site == 'woocommerce'){
                $orders->where('status', $status);
            }
            
        }
        return $orders;
    }

    public function toArray() {
        $data = parent::toArray();

        if($this->products){
            $data['products_count'] = $this->products->count();
        }

        if($this->orders){
            $data['pending'] = $this->orders('pending')->count();
            $data['ready_to_ship'] = $this->orders('ready_to_ship')->count();
            $data['shipped'] = $this->orders('shipped')->count();
            $data['delivered'] = $this->orders('delivered')->count();
        }

        return $data;
    }

    
    public function syncOrders($date = '2018-01-01', $step = '+1 day'){
        try {
            $this->update(['active' => 2]);
            if($this->site == 'lazada'){
                $this->syncLazadaOrders($date);
            }else if($this->site == 'shopee'){
                $this->syncShopeeOrders($date);
            }else if($this->site == 'shopify'){
                $this->syncShopifyOrders($date);
            }else if($this->site == 'woocommerce'){
                $this->syncWoocommerceOrders();
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

    public function syncProducts($date = '2018-01-01'){
        try {
            $this->update(['active' => 2]);
            if($this->site == 'lazada'){
                $data = $this->syncLazadaProducts($date);
            }else if($this->site == 'shopee'){
                $this->syncShopeeProducts($date);
            }else if($this->site == 'shopify'){
                $this->syncShopifyProducts($date);
            }else if($this->site == 'woocommerce'){
                $this->syncWoocommerceProducts();
            }
            $this->update(['active' => 1, 'is_first_time' => false]);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
        }
    }

    public function syncShopeeOrders($date){
        $orders = $this->shopeeGetOrdersPerDate($date);
        $this->shopeeSaveOrdersPerSN($orders);
    }

    public function syncLazadaOrders($date = "2018-01-01"){
        try {
            $orders = [];
            $after = Carbon::parse($date)->format('c');
            $before = Carbon::now()->addDays(2)->format('c');
            $offset = 0;
            $limit = 100;
            $count = 1;
            while($count != 0){
                $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
                $r = new LazopRequest('/orders/get','GET');
                $r->addApiParam('created_after', $after);
                $r->addApiParam('created_before', $before);
                $r->addApiParam('limit', $limit);
                $r->addApiParam('offset', $offset);
                $r->addApiParam('sort_by','updated_at');
                $result = $c->execute($r, $this->access_token);
                $data = json_decode($result, true);

                if(isset($data['data']['count'])){
                    $count = $data['data']['count'];
                    $offset += $count;
                    if(isset($data['data']['orders'])){
                        $orders = array_merge_recursive($data['data']['orders'], $orders);
                    }
                }else{
                    $count = 0;
                }
            }
            if($orders){
                $orders = array_map(function($order){
                    $status = $order['statuses'][0];
                    // if(array_key_exists(1,$order['statuses'])){
                    //      $status = $order['statuses'][1];
                    // }
                    $printed = $status == 'ready_to_ship' || $status == 'pending' ? false : true;
                    $order['ordersn'] = $order['order_id'];
                    $order['printed'] = $printed;
                    $order['price'] = Order::tofloat($order['price']);
                    unset($order['statuses']);
                    unset($order['address_billing']);
                    unset($order['address_shipping']);
                    unset($order['order_number']);
                    $order = array_merge($order, ['status' => $status, 'shop_id' => $this->id, 'site' => 'lazada']);
                    unset($order['order_id']);     
                    $record = Order::updateOrCreate(['ordersn' => $order['ordersn']], $order);
                    $c = $this->lazadaGetClient();
                    $r = new LazopRequest("/order/items/get",'GET');
                    $r->addApiParam("order_id", $order['ordersn']);
                    $response = $c->execute($r, $this->access_token);
                    $data = json_decode($response, true);
                    $item_ids = [];
                    $items = [];
                    foreach ($data['data'] as $item) {
                        $item_id = $item['sku'];
                        $product = Products::where('shop_id', $this->id)->where('SellerSku', $item_id)->first();
                        if($product != null){
                            if(!in_array($item_id, $item_ids)) {
                                array_push($item_ids, $item_id);
                                $items[$item_id] = array(
                                    'order_id' => $record->id,
                                    'product_id' => $product->id,
                                    'quantity' => 1,
                                    'price' => $item['paid_price'],
                                    'created_at' => Carbon::parse($record->created_at)->format('Y-m-d H:i:s'),
                                    'updated_at' => Carbon::parse($record->updated_at)->format('Y-m-d H:i:s'),
                                );
                            }
                            else {
                                $items[$item_id]['quantity'] += 1;
                                $items[$item_id]['price'] += $item['paid_price'];
                            }
                        }
                    } // item
                    foreach($items as $item_detail){
                        OrderItem::updateOrCreate(
                                ['order_id' => $item_detail['order_id'], 'product_id' => $item_detail['product_id']], $item_detail
                            );
                    }
                    return $order;
                }, $orders);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function syncShippingDetails($start_date, $end_date) {
        try {
            if($this->site == 'lazada'){
                $order_ids = array();
                foreach (array(13,7,8) as $trans_type) {
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
                $overcharge = Order::whereIn('ordersn', $overcharge_ids)->where('shipping_fee_reconciled', 0)->update(['shipping_fee_reconciled' => 1]);
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

    public function shopeeGetLogistics(){
        $client = $this->shopeeGetClient();
        return $client->logistics->getLogistics()->getData();
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
                    'update_time_from' => Carbon::createFromFormat('Y-m-d', $date)->timestamp,
                    'update_time_to' => Carbon::createFromFormat('Y-m-d', $created_before)->timestamp,
                    'pagination_entries_per_page' => 100,
                    'pagination_offset' => $offset,
                ];
                $offset += 100;
                $result = $client->order->getOrdersList($params)->getData();
                if (!isset($result['error'])) {
                    if(isset($result['orders'])){
                        $more = $result['more'];
                        if(count($result['orders']) > 0){
                            foreach($result['orders'] as $order){
                                $orders[] = $order;
                            }
                        }
                    }
                }
                else {
                    $more = false;
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
                    foreach($order['items'] as $item){
                        $product = Products::where('shop_id', $this->id)->where('item_id', $item['item_id'])->first();
                        if($product != null){
                            $item_detail = [
                                'order_id' => $record->id,
                                'product_id' => $product->id,
                                'quantity' => $item['variation_quantity_purchased'],
                                'price' => $item['variation_discounted_price'],
                                'created_at' => $record->created_at,
                                'updated_at' => $record->updated_at
                            ];
                            OrderItem::updateOrCreate(
                                ['order_id' => $item_detail['order_id'], 'product_id' => $item_detail['product_id']], $item_detail
                            );
                        }
                    } //items
                } // orders
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
        }else if($this->site == 'shopify'){
            
        }
    }

    public static function get_auth_shops(){
        
        $business_id = Auth::user()->business_id;
        
        $result = Shop::where('business_id','=',$business_id)->get();
        
        return $result;
    }

    public function lazadaGetClient(){
        return new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
    }


    public function syncLazadaProducts(){
        if($this->site == 'lazada'){
            $c = $this->lazadaGetClient();
            $r = new LazopRequest('/products/get','GET');
            $r->addApiParam('created_after', '2018-01-01T00:00:00+08:00');
            $r->addApiParam('created_before', date('Y-m-d').'T00:00:00+08:00');
            $r->addApiParam('limit','20');
            $result = $c->execute($r,$this->access_token);
            $data = json_decode($result, true);
            $products = [];
            $qproduct_item_ids = $this->products->pluck('item_id')->toArray();
            $lproduct_item_ids = [];
            if(isset($data['code']) && $data['code'] == "0"){
                $count = isset($data['data']['total_products'])  ? $data['data']['total_products'] : 0;
                if(isset($data['data']['products'])){
                    $products = $data['data']['products'];
                }
                $offset = 20;
                while($offset <= $count){
                    $r = new LazopRequest('/products/get','GET');
                    $r->addApiParam('created_after', '2018-01-01T00:00:00+08:00');
                    $r->addApiParam('created_before', date('Y-m-d').'T00:00:00+08:00');
                    $r->addApiParam('offset',$offset);
                    $r->addApiParam('limit','20');
                    $result = $c->execute($r,$this->access_token);
                    $data = json_decode($result, true);
                    if(isset($data['code']) && $data['code'] == "0"){
                         if(isset($data['data']['products'])){
                            $products = array_merge($products, $data['data']['products']);
                        }
                    }
                    $offset += 20;
                }
                $product_update_or_create_result = [];
                foreach($products as $product_details){
                    $product_details = [
                    'shop_id' => $this->id,
                    'site' => 'lazada',
                    'SkuId' => $product_details['skus'][0]['SkuId'],
                    'SellerSku' => $product_details['skus'][0]['SellerSku'],
                    'item_id' => $product_details['item_id'],
                    'price' =>  $product_details['skus'][0]['price'],
                    'Images' => implode('|', array_filter($product_details['skus'][0]['Images'])),
                    'name' => $product_details['attributes']['name'],
                    'Status' => $product_details['skus'][0]['Status'],
                    'Url' => $product_details['skus'][0]['Url'],
                    'quantity' => $product_details['skus'][0]['quantity'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    if ($product_details['Status'] == "active") {
                        $lproduct_item_ids[] = $product_details['item_id'];
                        $record = Products::updateOrCreate(['shop_id' => $product_details['shop_id'], 'item_id' => $product_details['item_id']], $product_details);
                    }

                }

                $delete_ids = array_diff($qproduct_item_ids, $lproduct_item_ids);
                $delete = $this->products()->whereIn('item_id', $delete_ids)->delete();


                return $product_update_or_create_result;
            }
        }
    }

    public function syncShopifyOrders($date = '2018-01-01'){
        try {
            $shop = $this;
            $since_id = 0;
            do{
                $params = [
                    'status' => 'any',
                    'limit' => 250,
                    'since_id' => $since_id,
                    'updated_at_min' => Carbon::parse($date)->format('c'),
                    'updated_at_max' => Carbon::now()->addDays(2)->format('c'),
                ];
                $orders = Shopify::setShopUrl($shop->domain)
                                ->setAccessToken($shop->access_token)
                                ->get("admin/api/2020-07/orders.json", $params);

                if(count($orders) != 0){
                    $since_id = $orders->last()->id;
                }

                $orders->each(function($order) use($shop){
                    $printed = count($order->fulfillments) == 0 ? false : true;
                    $orders_details = [
                            'ordersn' => $order->id,
                            'order_no' => $order->order_number,
                            'payment_method' => isset($order->payment_gateway_names[0]) ?  $order->payment_gateway_names[0] : 'Unknown',
                            'price' => $order->total_line_items_price,
                            'shop_id' => $shop->id,
                            'site' => 'shopify',
                            'items_count' => count($order->line_items),
                            'status' => $order->fulfillment_status == 'fulfilled' ? 'closed' : 'open',
                            'tracking_no' => count($order->fulfillments) ? $order->fulfillments[0]->tracking_number : '',
                            'shipping_fee' => $order->total_shipping_price_set->shop_money->amount,
                            'customer_first_name' => isset($order->customer) ? $order->customer->first_name . ' ' . $order->customer->last_name : 'No Customer',
                            'printed' => $printed,
                            'created_at' => Carbon::parse($order->created_at)->toDateTimeString(),
                            'updated_at' => Carbon::parse($order->updated_at)->toDateTimeString(),
                    ];
                    // return $order;
                    $record = Order::updateOrCreate(
                        ['ordersn' => $orders_details['ordersn']], $orders_details);
                    foreach($order->line_items as $item){
                        $product = Products::where('shop_id', $shop->id)->where('item_id', $item->variant_id)->first();
                        if($product != null){
                            $item_detail = [
                                'product_id' => $product->id,
                                'order_id' => $record->id,
                                'product_details' => $product->id,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'created_at' => $record->created_at,
                                'updated_at' => $record->updated_at
                            ];
                            OrderItem::updateOrCreate(
                                ['order_id' => $item_detail['order_id'], 'product_id' => $item_detail['product_id']], $item_detail
                            );
                        }
                    } //items
                }); // orders
            }while(count($orders) != 0);
        } catch (Exception $e) {
            //
        }
    }

    public function syncShopifyProducts($updated_at_min = '2018-01-01'){
        if ($this->site == 'shopify'){
           try {
             $since_id = 0;
            do{
                $params = [                
                    'updated_at_min' => Carbon::parse($updated_at_min)->format('c'),
                    'updated_at_max' => Carbon::now()->addDays(2)->format('c'),
                    'limit' => 250,
                    'since_id' => $since_id
                ];
                $products = Shopify::setShopUrl($this->domain)
                    ->setAccessToken($this->access_token)
                    ->get('admin/products.json', $params);

                if(count($products) != 0){
                    $since_id = $products->last()->id;
                }

                $products->each(function($product){
                    $product_details = [
                        'shop_id' => $this->id,
                        'site' => 'shopify',
                        'SkuId' => $product->variants[0]->product_id,
                        'SellerSku' => $product->variants[0]->sku, 
                        'item_id' => $product->variants[0]->id,
                        'price' =>  $product->variants[0]->price,
                        'inventory_item_id' => $product->variants[0]->inventory_item_id,
                        'Images' => $product->image->src,
                        'name' => $product->title,
                        'Status' => 'active',
                        'quantity' => $product->variants[0]->inventory_quantity,
                        'created_at' => Carbon::createFromTimestamp($product->created_at)->toDateTimeString(),
                        'updated_at' => Carbon::createFromTimestamp($product->updated_at)->toDateTimeString(),
                        ];
                        $record = Products::updateOrCreate(['shop_id' => $product_details['shop_id'], 'item_id' => $product_details['item_id']], $product_details);
                });
            }while(count($products) != 0);
           } catch (Exception $e) {
               //
           }
        }
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

    //Woocommerce
    public function syncWoocommerceProducts() {
        $woocommerce = $this->woocommerceGetClient();

        $page = 1;
        $products = [];
        $all_products = [];
        do{
            try {
                $products = $woocommerce->get('products', array('per_page' => 10, 'page' => $page));
            }catch(HttpClientException $e){
                die("Can't get products: $e");
            }
            $all_products = array_merge($all_products, $products);
            $page++;
        } while (count($products) > 0);

        foreach($all_products as $product) {
            $product = (array)$product;
            $product_details = [
            'shop_id' => $this->id,
            'site' => 'woocommerce',
            'SkuId' => $product['sku'],
            'SellerSku' => $product['sku'],
            'item_id' => $product['id'],
            'price' => $product['price'],
            'Images' => ($product['images']) ? ((array)($product['images'][0]))['src'] : '',
            'name' => $product['name'],
            'Status' => $product['stock_status'],
<<<<<<< HEAD
            'quantity' => ($product['stock_quantity'])? $product['stock_quantity'] : 0,
=======
            'quantity' => (($product['stock_quantity']) ? $product['stock_quantity'] : 0),
>>>>>>> :bug: Fixed dashboard shop performance
            'created_at' => Carbon::createFromTimestamp($product['date_created'])->toDateTimeString(),
            'updated_at' => Carbon::createFromTimestamp($product['date_modified'])->toDateTimeString(), 
            ];
            
            Products::updateOrCreate(['shop_id' => $product_details['shop_id'], 'item_id' => $product_details['item_id']], $product_details);
        }
        return;
    }

    public function syncWoocommerceOrders() {
        $woocommerce = $this->woocommerceGetClient();

        $page = 1;
        $orders = [];
        $all_orders = [];
        do{
            try {
                $orders = $woocommerce->get('orders', array('per_page' => 10, 'page' => $page));
            }catch(HttpClientException $e){
                die("Can't get orders: $e");
            }
            $all_orders = array_merge($all_orders, $orders);
            $page++;
        } while (count($orders) > 0);

        foreach($all_orders as $order) {
            $order = (array)$order;
            $printed = $order['status'] == 'completed' ? true : false;
            $order_details = [
                'ordersn' => $order['id'],
                'order_no' => $order['order_key'],
                'payment_method' => $order['payment_method'],
                'price' => $order['total'],
                'shop_id' => $this->id,
                'site' => 'woocommerce',
                'items_count' => count($order['line_items']),
                'status' => $order['status'],
                // 'tracking_no' => count($order->fulfillments) ? $order->fulfillments[0]->tracking_number : '',
                'shipping_fee' => $order['shipping_total'],
                // 'customer_first_name' => isset($order->customer) ? $order->customer->first_name . ' ' . $order->customer->last_name : 'No Customer',
                'printed' => $printed,
                'created_at' => Carbon::parse($order['date_created'])->toDateTimeString(),
                'updated_at' => Carbon::parse($order['date_modified'])->toDateTimeString(),
            ];
            $record = Order::updateOrCreate(['ordersn' => $order_details['ordersn']], $order_details);
            
            foreach($order['line_items'] as $item) {
                // $item = (array)$item;
                $product = Products::where('shop_id', $this->id)->where('item_id', $item->product_id)->first();
                if($product != null) {
                    $item_detail = [
                        'product_id' => $product->id,
                        'order_id' => $record->id,
                        'product_details' => $product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at
                    ];
                    OrderItem::updateOrCreate(['order_id' => $item_detail['order_id'], 'product_id' => $item_detail['product_id']], $item_detail);
                }
            }
        }
        return;
    }

    public function woocommerceGetClient() {
        return new Client(Woocommerce::getDomain($this->id), Woocommerce::getConsumerKey($this->id), Woocommerce::getConsumerSecret($this->id), [
            'wp_api' => true,
            'version' => 'wc/v3',
            'verify_ssl' => false,
        ]);
    }

    public function shopeeSaveProductsPerItem($products){
        $client = $this->shopeeGetClient();
        $qproduct_item_ids = $this->products->pluck('item_id')->toArray();
        $sproduct_item_ids = [];
        foreach($products as $product){
            if(isset($product['item_id'])){
                $product_details = $client->item->getItemDetail(['item_id' => $product['item_id']])->getData();
                if (isset($product_details['item'])) {
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
                        'quantity' => $product_details['item']['stock'],
                        'created_at' => Carbon::createFromTimestamp($product_details['item']['create_time'])->toDateTimeString(),
                        'updated_at' => Carbon::createFromTimestamp($product_details['item']['update_time'])->toDateTimeString(),
                        ];

                        if (!in_array($product_details['Status'], ['UNLIST', 'DELETED'])) {
                            $sproduct_item_ids[] = $product_details['item_id'];
                            $record = Products::updateOrCreate(['shop_id' => $product_details['shop_id'], 'item_id' => $product_details['item_id']], $product_details);
                        }
                    }
                }
            }
        }
        $delete_ids = array_diff($qproduct_item_ids, $sproduct_item_ids);
        $delete = $this->products()->whereIn('item_id', $delete_ids)->delete();
        return true;
    }

    public function totalSales(){
       $datas = ['week' => 0, 'yesterday' => 0, 'today' => 0, 'month' => 0];
       foreach($datas as $key => $val){
            if($key == 'week'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('price'), 2);
            }else if($key == 'today'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today())->sum('price'), 2);
            }else if($key == 'yesterday'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today()->subDays(1))->sum('price'), 2);
            }else if($key == 'month'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->where('created_at', '>=', Carbon::now()->firstOfMonth()->toDateTimeString())->where('created_at', '<=', Carbon::now()->endOfMonth()->toDateTimeString())->sum('price'), 2);
            }
       }
       return (object) $datas;
    }

    public function totalOrders(){
        $datas = ['week' => 0, 'yesterday' => 0, 'today' => 0, 'month' => 0];
       foreach($datas as $key => $val){
            if($key == 'week'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(), 2);
            }else if($key == 'today'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today())->count(), 2);
            }else if($key == 'yesterday'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->whereDate('created_at', Carbon::today()->subDays(1))->count(), 2);
            }else if($key == 'month'){
                $datas[$key] = number_format($this->orders()->whereNotIn('status', Order::statusNotIncludedInSales())->where('created_at', '>=', Carbon::now()->firstOfMonth()->toDateTimeString())->where('created_at', '<=', Carbon::now()->endOfMonth()->toDateTimeString())->count(), 2);
            }
       }
       return (object) $datas;
    }

    public function getImgSiteDisplay(){
        return '<img src="'.asset('images/shop/30x30/'. $this->site.'.png').'" alt="'. $this->site .'" style="width:15px; height:15px"> ' . '<span style="padding-left: 5px;font-size:13px">'. $this->short_name .' </span>';
    }

    public function getImgSiteDisplayWithFullName(){
        return '<img src="'.asset('images/shop/30x30/'. $this->site.'.png').'" class="m-0" alt="'. $this->site .'" style="width:15px; height:15px"> ' . '<span style="padding-left: 5px;font-size:13px">'. $this->name .' ('. $this->short_name .')</span>';
    }


    public function syncLazadaPayout($date = '2018-01-01'){
        if($this->site == 'lazada'){
            $c = $this->lazadaGetClient();
            $r = new LazopRequest('/finance/payout/status/get', 'GET');
            $r->addApiParam('created_after', $date);
            $result = $c->execute($r, $this->access_token);
            $data = json_decode($result, true);
            if(isset($data['data'])){
                foreach($data['data'] as $payout){
                    $payout['shop_id'] = $this->id;
                    LazadaPayout::updateOrCreate(['shop_id' => $payout['shop_id'], 'statement_number' => $payout['statement_number']],$payout);
                }
            }
        }
    }

    public function syncShopeePayout($date = '2018-01-01'){
        if($this->site == 'shopee'){
            $client = $this->shopeeGetClient();
            $dates = Utilities::getDaterange($date, Carbon::now()->addDays(1)->format('Y-m-d'), 'Y-m-d', '+5 day');
            $transaction_list  = [];
            $created_before_increment = 1;
            // dd($dates);
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
                        'transaction_type' => 'ESCROW_VERIFIED_ADD'
                    ];
                    $offset += 100;
                    $result = $client->shop->getTransactionList($params)->getData();



                    if (!isset($result['error'])) {
                        if(isset($result['transaction_list'])){
                            $more = $result['has_more'];
                            if(count($result['transaction_list']) > 0){
                                foreach($result['transaction_list'] as $transaction){
                                    $transaction_list[] = $transaction;
                                }
                            }
                        }
                    }
                    else {
                        $more = false;
                    }
                }
            }
            foreach($transaction_list as $transaction){
                $transaction['created_at'] = Carbon::createFromTimestamp($transaction['create_time'])->toDateTimeString();
                $transaction['updated_at'] = Carbon::createFromTimestamp($transaction['create_time'])->toDateTimeString();
                $created_at = Carbon::parse($transaction['create_time'])->startOfWeek();
                $transaction['payout_date'] = $created_at->format('M d, Y') . ' - ' . $created_at->addWeek()->format('M d, Y');
                $transaction['shop_id'] = $this->id;
                $record = ShopeePayout::where('payout_date', $transaction['payout_date'])->where('shop_id', $this->id)->first();
                if($record == null){
                    $transaction['transaction_ids'] = $transaction['transaction_id'];
                    ShopeePayout::create($transaction);
                }else{
                    $transaction_ids = explode(',', $record->transaction_ids);
                    if(! in_array($transaction['transaction_id'], $transaction_ids)){
                        $transaction_ids = explode(',', $record->transaction_ids);
                        $transaction_ids[] = $transaction['transaction_id'];
                        $transaction_ids = implode(',', $transaction_ids);
                        $record->update(['amount' => $record->amount + $transaction['amount'], 'transaction_ids' => $transaction_ids]);
                }
            }
            } // foreach
            return $transaction_list;
        }
    }
}


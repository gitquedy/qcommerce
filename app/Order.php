<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Shop;
use App\Lazop;
use App\Utilities;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use DB;
use Auth;

class Order extends Model
{
    protected $table = 'order';

    protected $fillable = [
		    	'id', 'tracking_no', 'customer_last_name', 'price','payment_method','customer_first_name','shipping_fee','items_count','status','shop_id', 'created_at', 'updated_at', 'site', 'printed', 'packed', 'ordersn'
			];

    public $timestamps = false;

    public static $statuses = [
              'shipped', 'ready_to_ship', 'pending', 'delivered', 'returned', 'failed', 'unpaid', 'canceled', 
    ];

    public static $shopee_statuses = [
        'UNPAID','READY_TO_SHIP', 'RETRY_SHIP', 'SHIPPED' ,'COMPLETED', 'TO_CONFIRM_RECEIVE' ,'IN_CANCEL','CANCELLED','TO_RETURN',
     ];
	public function shop(){
		return $this->belongsTo(Shop::class, 'shop_id', 'id');
	}

	public function getActionsDropdown(){
        $nextAction = self::getNextAction();
        $status = $this->status;
        $order_id = $this->id;
        if($this->site == 'lazada'){
            $disabled = ['print_shipping_label' => 'disabled', 'cancel' => 'disabled', 'ready_to_ship' => 'disabled'];
            if($status == 'ready_to_ship'){
                $disabled['print_shipping_label'] = '';
                $disabled['cancel'] = '';
            }else if($status == 'pending'){
                $disabled['print_shipping_label'] = '';
                $disabled['cancel'] = '';
                $disabled['ready_to_ship'] = '';
            }else if($status == 'shipped'){
                $disabled['print_shipping_label'] = '';
            }
            $dropdown = '<div class="btn-group dropup mr-1 mb-1">
                        '. $nextAction .'
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item confirm '. $disabled['ready_to_ship'] .'" href="#" data-href="'. action('OrderController@readyToShip', [$order_id]) .'" data-text="Are you sure to mark '. $order_id .' as ready to ship?" data-text="This Action is irreversible."><i class="fa fa-truck aria-hidden="true""></i> Ready to Ship</a>
                            <a class="dropdown-item '. $disabled['print_shipping_label'] .'" href="'.route('order.print_shipping',array('id'=>$order_id)).'"><i class="fa fa-print aria-hidden="true""></i> Print Shipping Label</a>
                            <a class="dropdown-item confirm '. $disabled['cancel'] .'" href="#" data-href="'. action('OrderController@cancel', [$order_id]) .'" data-text="Are you sure to mark '. $order_id .' as canceled?" data-text="This Action is irreversible." data-input="textarea" data-placeholder="Type your reason here..."><i class="fa fa-window-close-o aria-hidden="true""></i> Cancel Order</a>
                        </div></div>';
        }else{
            $disabled = ['print_shipping_label' => 'disabled', 'cancel' => 'disabled', 'ready_to_ship' => 'disabled'];
            if($status == 'READY_TO_SHIP'){
                if($this->tracking_no != ''){
                    $disabled['print_shipping_label'] = '';
                }else{
                    $disabled['ready_to_ship'] = '';
                    $disabled['cancel'] = '';
                }
            }else if($status == 'SHIPPED'){
                $disabled['print_shipping_label'] = '';
            }
            $dropdown = '<div class="btn-group dropup mr-1 mb-1">
                        '. $nextAction .'
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item modal_button '. $disabled['ready_to_ship'] .'" href="#" data-href="'. action('OrderController@readyToShipShopee', [$order_id]) .'"><i class="fa fa-truck aria-hidden="true""></i> Ready to Ship</a>
                            <a class="dropdown-item '. $disabled['print_shipping_label'] .'" href="'.route('order.print_shipping',array('id'=>$order_id)).'"><i class="fa fa-print aria-hidden="true""></i> Print Shipping Label</a>
                            <a class="dropdown-item modal_button '. $disabled['cancel'] .'" href="#" data-href="'. action('OrderController@cancelModal', [$order_id]) .'"><i class="fa fa-window-close-o aria-hidden="true""></i> Cancel Order</a>
                        </div></div>';
        }
       
        return $dropdown;
    }

    public function getNextAction(){
        $status = $this->status;
        $order_id = $this->id;
        if($this->site == 'lazada'){
            if($status == 'pending'){
            $btn = '<button type="button" class="btn btn-primary confirm" data-href="'. action('OrderController@readyToShip', [$order_id]) .'" data-text="Are you sure to mark '. $order_id .' as ready to ship?" data-text="This Action is irreversible.">Ready to Ship</button>';
            }else if($status == 'ready_to_ship'){
                $btn = '<button type="button" class="btn btn-primary">Print Shipping Label</button>';
            }
            else {
                $btn = '<button type="button" class="btn btn-primary order_view_details" data-order_id="'.$order_id.'" data-action="'.route('barcode.viewBarcode').'" >View detail</button>';
            }
        }else{
            if($status == 'READY_TO_SHIP'){
                if($this->tracking_no == ''){
                    $btn = '<button type="button" class="btn btn-primary modal_button" data-href="'. action('OrderController@readyToShipShopee', [$order_id]) .'">Ready to Ship</button>';
                }else{
                    $btn = '<button type="button" class="btn btn-primary">Print Shipping Label</button>';
                }
            }
            else {
                $btn = '<button type="button" class="btn btn-primary order_view_details" data-order_id="'.$order_id.'" data-action="'.route('barcode.viewBarcode').'" >View detail</button>';
            }
        }
        return $btn;
    }

    public function getOrderDetails(){
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/order/get','GET');
        $r->addApiParam('order_id', $this->id);
        $result =  $c->execute($r, $this->shop->access_token);
        return json_decode($result, true);
    }

    public function getOrderItems(){
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/order/items/get','GET');
        $r->addApiParam('order_id', $this->id);
        $result =  $c->execute($r, $this->shop->access_token);
        return json_decode($result, true);
    }

    public function getItemIds($items){
        $item_ids = [];
        foreach($items['data'] as $item){
            $item_ids[] = $item['order_item_id'];
        }
        return $item_ids;
    }

    public function cancel($item_ids, $msg = null){
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/order/cancel');
        $r->addApiParam('reason_detail', $msg);
        $r->addApiParam('reason_id','15');
        $r->addApiParam('order_item_id',$item_ids[0]);
        $result = $c->execute($r, $this->shop->access_token);
        $this->update(['status' => 'canceled']);
        return json_decode($result, true);
    }

    public function readyToShip($item_ids){
        $item_ids = '[' . implode(', ', $item_ids) . ']';
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/order/rts');
        $r->addApiParam('delivery_type','dropship');
        $r->addApiParam('order_item_ids', $item_ids);
        $r->addApiParam('shipment_provider','Aramax');
        $r->addApiParam('tracking_number','12345678');
        $result = $c->execute($r, $this->shop->access_token);
        $this->update(['status' => 'ready_to_ship']);
        return json_decode($result, true);
    }    

    public function updateTracking(){
        $order = self::getOrderItems();
        $tracking_code = $order['data'][0]['tracking_code'];
        $this->update(['tracking_no' => $tracking_code]);
        return json_decode($result, true);
    }    
    
    public static function get_dashboard_shop_performance($shop, $type="") {
        $query = DB::table('order');
        $query->where('shop_id',$shop);
        $query->where('status', '!=', 'canceled');
        
        
        if($type=='today'){
            $query->whereDate('created_at',"=",date('Y-m-d'));
        }
        if($type=='yesterday'){
            $query->whereDate('created_at',"=", date('Y-m-d', strtotime("-1 day")));
        }

        if($type=='week'){
            $date = date('Y-m-d');
            $ts = strtotime($date);
            $start_t = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
            $end_t = strtotime('next saturday', $start_t);
            $start = date("Y-m-d", $start_t);
            $end = date("Y-m-d", $end_t);
            $query->where('created_at', '>=', $start);
            $query->where('created_at', '<=', $end);
        }

        if($type=='month'){
            $start = date('Y-m-01');
            
            $date=date_create($start);
            
            date_modify($date,"+1 month");
            
            $end = date_format($date,"Y-m-d");
            
              $query->where('created_at', '>=', $start);
              $query->where('created_at', '<=', $end);
            
        }

        $result = $query->get();
        $total = 0;
        
        foreach($result as $r) {
            $total += self::tofloat($r->price);
        }


        return number_format($total, 2);
    }

    private static function tofloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
      
        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }


    public static function get_dashboard_orders($status="",$type=""){
        $shops = Shop::get_auth_shops();
        $shops_array = array();  
        foreach($shops as $shopsVAL){
            $shops_array[] = $shopsVAL->id;
        }
        
        $query = DB::table('order'); 
        if(count($shops_array)>0){
            $query->whereIn('shop_id',$shops_array);
        }else{
            return array();
        }
        
        if($status!=""){
           $query->where('status','=',$status); 
        }
        
        if($type=='month'){
            $start = date('Y-m-01');
            
            $date=date_create($start);
            
            date_modify($date,"+1 month");
            
            $end = date_format($date,"Y-m-d");
            
              $query->where('created_at', '>=', $start);
              $query->where('created_at', '<=', $end);
            
        }
        
        if($type=='today'){
            $query->whereDate('created_at',"=",date('Y-m-d'));
        }
        
        if($type=="two_month"){
            
            $start = date('Y-m-01');
            
            $date=date_create($start);
            
            date_modify($date,"-1 month");
            
            $pre = date_format($date,"Y-m-d");
            
            $date=date_create($start);
            
            date_modify($date,"+1 month");
            
            $end = date_format($date,"Y-m-d");
            
            $query->where('created_at', '>=', $pre);
            $query->where('created_at', '<=', $end);
            
        }
        
        if($type=="last_6_month"){
            
           // $end = date('Y-m-d');
            
            $date=date_create(date('Y-m-d'));
            $end = date_modify($date,"+1 days");
            
            $date=date_create(date('Y-m-01'));
            
            date_modify($date,"-6 month");
            
            $start = date_format($date,"Y-m-d");
            
            
            $query->where('created_at', '>=', $start);
            $query->where('created_at', '<=', $end);
            
        }
        return $query->get();
    }
    
    
    public static function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = array();
        $current = strtotime( $first );
        $last = strtotime( $last );
    
        while( $current <= $last ) {
    
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
    
        return $dates;
    }
    
    
    public static function get_order_items($order_id=""){
        
        $item_ids = array();
        
        $order = Order::find($order_id);
        $access_token = "";
        
        if($order){
            $shop = Shop::find($order->shop_id);
            
            $access_token = $shop->access_token;
            
        }
        
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/order/items/get','GET');
        $r->addApiParam('order_id',$order_id);
        $result = $c->execute($r, $access_token);
        
        $JSOBJ = json_decode($result);
        
        if(isset($JSOBJ->data)){
            foreach($JSOBJ->data as $Items){
                $item_ids[] = $Items->order_item_id;
            }
        }
        
        return array('token'=>$access_token,'item'=>$item_ids);  
    }
    
    
    
    public static function get_shipping_level($order_id=""){
        
        $ati = $_COOKIE['_ati'];
        
        $args = Order::get_order_items($order_id);
        
            $access_token = $args['token'];
            $order_item_ids = json_encode($args['item']);
            
        $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/order/document/get','GET');
        $r->addApiParam('doc_type','shippingLabel');
        $r->addApiParam('ati',$ati);
        $r->addApiParam('order_item_ids',$order_item_ids);
        $result = $c->execute($r, $access_token);
        
        return $result;
        
    }
    
    
    public static function computer(){
        
        $ati = $_COOKIE['_ati'];
        
        $c = new LazopClient('https://api.lazada.com/rest', Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/datamoat/compute_risk');
        $r->addApiParam('time',time());
        $r->addApiParam('appName','QCommerce');
        $r->addApiParam('userId','PHJ2F0W3');
        $r->addApiParam('userIp','111.221.46.219');
        $r->addApiParam('ati',$ati);
        var_dump($c->execute($r));
        
    }
    
    
    public static function login_mota(){
        
        $ati = $_COOKIE['_ati'];
        
        $c = new LazopClient('https://api.lazada.com/rest', Lazop::get_api_key(), Lazop::get_api_secret());
        $r = new LazopRequest('/datamoat/login');
        $r->addApiParam('time',time());
        $r->addApiParam('appName','QCommerce');
        $r->addApiParam('userId','PHJ2F0W3');
        $r->addApiParam('tid','kitzmedia@gmail.com');
        $r->addApiParam('userIp','111.221.46.219');
        $r->addApiParam('ati',$ati);
        $r->addApiParam('loginResult','fail');
        $r->addApiParam('loginMessage','password is not correct');
        var_dump($c->execute($r));
        
    }
}

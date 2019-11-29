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

class Order extends Model
{
    protected $table = 'order';

    protected $fillable = [
		    	'id', 'voucher_platform', 'voucher', 'voucher_seller', 'voucher_code', 'delivery_info',
		    	'gift_option', 'customer_last_name', 'promised_shipping_times', 'price', 'national_registration_number',
		    	'payment_method','customer_first_name','shipping_fee','branch_number','tax_code','items_count',
		    	'status','extra_attributes','gift_message','remarks','shop_id',
			];
    public static $statuses = [
              'shipped', 'ready_to_ship', 'pending', 'delivered', 'returned', 'failed', 'unpaid', 'canceled', 
    ];

	public function shop(){
		return $this->belongsTo(Shop::class, 'shop_id', 'id');
	}

	public function getActionsDropdown(){
        $nextAction = self::getNextAction();
        $status = $this->status;
        $order_id = $this->id;
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
                        <a class="dropdown-item '. $disabled['print_shipping_label'] .'" href="#"><i class="fa fa-print aria-hidden="true""></i> Print Shipping Label</a>
                        <a class="dropdown-item confirm '. $disabled['cancel'] .'" href="#" data-href="'. action('OrderController@cancel', [$order_id]) .'" data-text="Are you sure to mark '. $order_id .' as canceled?" data-text="This Action is irreversible." data-input="textarea" data-placeholder="Type your reason here..."><i class="fa fa-window-close-o aria-hidden="true""></i> Cancel Order</a>
                    </div></div>';
        return $dropdown;
    }

    public function getNextAction(){
        $status = $this->status;
        $order_id = $this->id;
        if($status == 'pending'){
            $btn = '<button type="button" class="btn btn-primary confirm" data-href="'. action('OrderController@readyToShip', [$order_id]) .'" data-text="Are you sure to mark '. $order_id .' as ready to ship?" data-text="This Action is irreversible.">Ready to Ship</button>';
        }else if($status == 'ready_to_ship'){
            $btn = '<button type="button" class="btn btn-primary">Print Shipping Label</button>';
        }else{
            $btn = '<button type="button" class="btn btn-primary">View detail</button>';
        }
        return $btn;
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
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Lazop;
use App\Order;
use App\Utilities;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;

class Shop extends Model
{
    protected $table = 'shop';

    protected $fillable = ['user_id', 'name', 'short_name', 'refresh_token', 'access_token', 'expires_in', 'active', 'email', 'is_first_time'];

    public $timestamps = false;

    public static $statuses = [
              'shipped', 'ready_to_ship', 'pending', 'delivered', 'returned', 'failed', 'unpaid', 'canceled', 
    ];
    
    public function syncOrders($date = '2015-01-01', $step = '+3 day'){
        try {
        $this->update(['active' => 2]);
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
                unset($order['statuses']);
                unset($order['address_billing']);
                unset($order['address_shipping']);
                unset($order['order_number']);  
                $order = array_merge($order, ['id' => $order['order_id'], 'status' => $status, 'shop_id' => $this->id]);
                unset($order['order_id']);     
                $record = Order::updateOrCreate(
                ['id' => $order['id']], $order);
                return $order;
            }, $orders);
        }
        $this->update(['active' => 1, 'is_first_time' => false]);
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
        }
        return $data;
    }

    public function refreshToken(){
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

    public function orders($status = null){
        $orders = $this->hasMany(Order::class, 'shop_id', 'id');
        if($status){
            $orders->where('status', $status);
        }
        return $orders;
    }

    // public static function dummyOrder(){
    //     return ['data' => ['count' => [0], 'orders' => []], 'code' => [], 'request_id' => []];
    // }

    // public function getTotalOrders($status = null){
    //         $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
    //         $r = new LazopRequest('/orders/get','GET');
    //         $r->addApiParam('created_before', Carbon::now()->addYears(3)->format('c'));
    //         if($status){
    //             $r->addApiParam('status', $status);
    //         }
    //         $r->addApiParam('sort_direction','DESC');
    //         $r->addApiParam('update_after', Carbon::now()->subYears(3)->format('c'));
    //         $r->addApiParam('sort_by','updated_at');
    //         $result = $c->execute($r, $this->access_token);
    //         $data = json_decode($result, true);
    //         if(! isset($data['message'])){
    //             $data['data']['orders'] = array_map(function($data){
    //                 $str_status = ucwords(str_replace("_"," ", $data['statuses'][0]));
    //                 return array_merge($data, ['seller' => $this->name, 'status' => $str_status, 'seller_id' => $this->id]);
    //             }, $data['data']['orders']);
    //         }
    //         return $data;
    // }

    // public function searchOrderID(){
    //         $c = new LazopClient(UrlConstants::getPH(), Lazop::get_api_key(), Lazop::get_api_secret());
    //         $r = new LazopRequest('/order/get','GET');
    //         $r->addApiParam("order_id", $this->id);
    //         $result = $c->execute($r, $this->access_token);
    //         $data = json_decode($result, true);
    //         if(! isset($data['message'])){
    //             $data['data']['status'] = ucwords(str_replace("_"," ", $data['data']['statuses'][0]));
    //             $data['data']['seller'] = $this->name;
    //             $data['data']['seller_id'] = $this->id;
    //         }else{
    //             return $this->dummyOrder();
    //         }
    //         $data['data']['orders'] = [$data['data']];
    //         $data['data']['count'] = [1];
    //         return $data;
    // }
}
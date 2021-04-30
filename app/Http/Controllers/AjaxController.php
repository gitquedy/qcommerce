<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Shop;
use App\Products;
use Helper;
use Auth;
use DB;



class AjaxController extends Controller
{
    public function get_notification(){
        $Shop = Shop::get_auth_shops();
        
        $Shop_array = array();
        foreach($Shop as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        

        
        $orders = Order::select('id','created_at', 'site', 'price', 'shop_id')->whereIn('shop_id',$Shop_array)->whereIn('status',['pending', 'READY_TO_SHIP', 'RETRY_SHIP', 'UNPAID','processing', 'on-hold'])->whereNull('seen')->get();

        $Products_unseen = Products::select('id','created_at', 'site', 'shop_id')->whereIn('shop_id',$Shop_array)->where('seen','=',0)->get();
        
        
        
        $max_unseen_product = 0;
        
        foreach($Products_unseen as $Products_unseenVAL){
            if($max_unseen_product<strtotime($Products_unseenVAL['created_at'])){
                $max_unseen_product = strtotime($Products_unseenVAL['created_at']);
            }   
        }
        
        $last_product = date('Y-m-d H:i:s',$max_unseen_product);
        
        $date1=date_create($last_product);
        $date2=date_create(date('Y-m-d H:i:s'));
        $diff=date_diff($date1,$date2);
        
        $last_product_time = '';
        
        if($diff->d>0){
            $last_product_time .= $diff->d." Day";
            if($diff->d>1){
                $last_product_time.="s";
            }
            $last_product_time .= ' ';
        }
   
        if($diff->h>0){
            $last_product_time .= $diff->h." Hour";
            if($diff->h>1){
                $last_product_time.="s";
            }
            $last_product_time .= ' ';
        }
        
        if($diff->i>0){
            $last_product_time .= $diff->i." Minute";
            if($diff->i>1){
                $last_product_time.="s";
            }
            $last_product_time .= ' ';
        }   
        
        $last_product_time .= ' Ago';
        
        
        

        
        
        $max_time = $orders->max('created_at');
        
        $last_order = date('Y-m-d H:i:s',strtotime($max_time));
        
        $date1=date_create($last_order);
        $date2=date_create(date('Y-m-d H:i:s'));
        $diff=date_diff($date1,$date2);
        
        
        $order_last_time = '';
        if($diff->d>0){
            $order_last_time .= $diff->d." Day";
            if($diff->d>1){
                $order_last_time.="s";
            }
            $order_last_time.= ' ';
        }
   
        if($diff->h>0){
            $order_last_time .= $diff->h." Hour";
            if($diff->h>1){
                $order_last_time.="s";
            }
            $order_last_time.= ' ';
        }
        
        if($diff->i>0){
            $order_last_time .= $diff->i." Minute";
            if($diff->i>1){
                $order_last_time.="s";
            }
            $order_last_time.= ' ';
        }   
        
        $order_last_time .= ' Ago';

        $orders_count = $orders->count();
        
        $data['total'] = $orders_count + count($Products_unseen);
        $data['orders'] = $orders_count;
        $data['order_string'] = $order_last_time;
        $data['total_new_products'] = count($Products_unseen);
        $data['last_product_time'] = $last_product_time;
        
        if ($orders) {
            foreach($orders as $order) {
                $order['short_name'] = Shop::where('id', $order->shop_id)->pluck('short_name')->first();
            }
        }

        if ($Products_unseen) {
            foreach($Products_unseen as $product) {
                $product['short_name'] = Shop::where('id', $product->shop_id)->pluck('short_name')->first();
            }
        }

        $data['order_value'] = $orders;
        $data['product_value'] = $Products_unseen;
        
        $resp = array('status'=>'success','data'=>$data);
        
        echo json_encode($resp);
        
        
    }
}


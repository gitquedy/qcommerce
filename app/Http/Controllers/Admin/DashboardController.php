<?php

namespace App\Http\Controllers\Admin;

use DB;
use Helper;
use App\User;
use App\Shop;
use App\Order;
use App\Sales;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request){
    	if(!$request->user()->isAdmin()){
            return redirect(route('dashboard'));
        }

        $colour = Helper::get_colours();
        $total_users = User::count();
        $users_date = User::select(DB::raw('DATE(created_at) as date'))->distinct()->orderBy('created_at', 'desc')->limit(10)->get();
        
        $user_count = array();
        foreach ($users_date as $key => $value) {
            $total_users = User::where(DB::raw('DATE(created_at)'), '<=', $value->date)->count();
            $user_count[$value->date] = $total_users;
        }


        $total_shops = Shop::count();
        $shops_date = Shop::select(DB::raw('DATE(created_at) as date'))->distinct()->orderBy('created_at', 'desc')->limit(10)->get();
        
        $shops_count = array();
        foreach ($shops_date as $key => $value) {
            $total_shops = Shop::where(DB::raw('DATE(created_at)'), '<=', $value->date)->count();
            $shops_count[$value->date] = $total_shops;
        }



        $today = Order::get_dashboard_orders('','today', false);
        $today_sales = 0;
        
        foreach($today as $todayVAL){
            if ($todayVAL->site == 'lazada') {
                $today_sales += (float) str_replace(",","",$todayVAL->price);
            }
            elseif ($todayVAL->site == 'shopee') {
                // $items = OrderItem::select(DB::raw('ROUND(SUM(order_item.price)) as total_price'))->where('order_id', $todayVAL->id)->first();
                $today_sales += (float) str_replace(",","",$todayVAL->price);
            }
        }
        $pos_today = Sales::get_dashboard_sales('', 'today', false);
        foreach($pos_today as $todayVAL){
            $today_sales += (float) str_replace(",","",$todayVAL->grand_total);
        }
        $total_orders_today = count($today) + count($pos_today);
        $total_sales_today = number_format($today_sales);

        /* Hourly sales data and orders*/
        $hour_data = array();
        $hour_orders = array();
        
        $day_hour_start = date('Y-m-d 00:00:00');
        
        $date=date_create($day_hour_start);

        
        for ($i=0; $i <24 ; $i++) { 
            
            if(strtotime(date_format($date,"Y-m-d H:i:s"))<time()){
            
            $total_price = 0;
            $total_orders = 0;
            foreach($today as $KeyTo => $TodayVAL){
                $record_dateT = date('Y-m-d H',strtotime($TodayVAL->created_at));
                $matche_hourT = date_format($date,"Y-m-d H");
                
                
                if($record_dateT==$matche_hourT){
                    $total_price += (float) str_replace(",","",$TodayVAL->price);
                    $total_orders++;
                }
            }
            foreach($pos_today as $KeyTo => $TodayVAL){
                $record_dateT = date('Y-m-d H',strtotime($TodayVAL->created_at));
                $matche_hourT = date_format($date,"Y-m-d H");
                
                
                if($record_dateT==$matche_hourT){
                    $total_price += (float) str_replace(",","",$TodayVAL->grand_total);
                    $total_orders++;
                }
            }
            $hour_data[date_format($date,"h:i A")] = $total_price;
            $hour_orders[date_format($date,"h:i A")] = $total_orders;
            
            
            
            date_modify($date,"+1 hours");
            
            }
        }
        
    	return view('admin.dashboard.index', [
            'total_users' => number_format($total_users),
            'total_user_count' => array_reverse($user_count),
            'total_shops' => number_format($total_shops),
            'total_shops_count' => array_reverse($shops_count),
            'total_orders_today' => number_format($total_orders_today),
            'total_sales_today' => number_format($total_sales_today),
            'hour_data'=> $hour_data,
            'hour_orders'=> $hour_orders,
            'colour'=>$colour,
        ]);
    }
}

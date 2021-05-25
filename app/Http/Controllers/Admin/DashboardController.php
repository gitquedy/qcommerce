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
        $users_date = User::select(DB::raw('DATE(created_at) as date'))->distinct()->orderBy('date', 'desc')->limit(10)->get();
        
        $user_count = array();
        foreach ($users_date as $key => $value) {
            $total_users = User::where(DB::raw('DATE(created_at)'), '<=', $value->date)->count();
            $user_count[$value->date] = $total_users;
        }


        $total_shops = Shop::count();
        $shops_date = Shop::select(DB::raw('DATE(created_at) as date'))->distinct()->orderBy('date', 'desc')->limit(10)->get();
        
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
        $total_sales_today = $today_sales;

        /* Hourly sales data and orders*/
        $OrderSales_data = array();
        $OrderSales_orders = array();
        
        $day_OrderSalesstart = date('Y-m-d');
        
        $date=date_create($day_OrderSalesstart);

        
        for ($i=0; $i < 30 ; $i++) {
            $ordersToday = Order::where(DB::raw('DATE(created_at)'), $date)->get();
            $posSalesToday = Sales::where(DB::raw('DATE(created_at)'), $date)->get();
            $total_price = 0;
            $total_orders = 0;
            foreach ($ordersToday as $key => $value) {
                $total_price += (float) str_replace(",","",$value->price);
                $total_orders++;
            }
            foreach($posSalesToday as $key => $value){
                $total_price += (float) str_replace(",","",$value->grand_total);
                $total_orders++;
            }
            $OrderSales_data[date_format($date,"Y-m-d")] = $total_price;
            $OrderSales_orders[date_format($date,"Y-m-d")] = $total_orders;
            date_modify($date,"-1 day");
        }
    	return view('admin.dashboard.index', [
            'total_users' => number_format($total_users),
            'total_user_count' => array_reverse($user_count),
            'total_shops' => number_format($total_shops),
            'total_shops_count' => array_reverse($shops_count),
            'total_orders_today' => number_format($total_orders_today),
            'total_sales_today' => number_format($total_sales_today),
            'orderSales_datas'=> array_reverse($OrderSales_data),
            'orderSales_orders'=> array_reverse($OrderSales_orders),
            'colour'=>$colour,
        ]);
    }
}

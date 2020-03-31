<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Shop;
use App\Products;
use App\Sku;
use Helper;
use Auth;
use DB;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use App\Lazop;
use App\Utilities;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index(Request $request){
        if($request->user()->isAdmin()){
            return redirect(route('admin.dashboard'));
        }
        $Shop = Shop::get_auth_shops();
        foreach ($Shop as $shop) {
            $shop->shop_info_data_today = Order::get_dashboard_shop_performance($shop->id,'today');
            $shop->shop_info_data_yesterday = Order::get_dashboard_shop_performance($shop->id,'yesterday');
            $shop->shop_info_data_week = Order::get_dashboard_shop_performance($shop->id,'week');
            $shop->shop_info_data_month = Order::get_dashboard_shop_performance($shop->id,'month');
        }
        // print json_encode($Shop);die();
        $colour = Helper::get_colours();
        
        
        
        /* monthly */
        
        $monthly = Order::get_dashboard_orders('','month');
        $monthly_sales = 0;
        foreach($monthly as $monthlyVAL){
            $monthly_sales += (float) str_replace(",","",$monthlyVAL->price);
        }
        
        /* Today */
        
        
        $today = Order::get_dashboard_orders('','today');

        $today_sales = 0;
        
        foreach($today as $todayVAL){
            $today_sales += (float) str_replace(",","",$todayVAL->price);
        }
        
    
        $today_order_count = count($today);
        
        /* pending */
        
        
        $shipped = Order::get_dashboard_orders('pending','');
        $shipped_counter = count($shipped);
        
        /* comperision  */
        $two_month = Order::get_dashboard_orders('','two_month');
        
        $pre_month_days = 0;
        $current_month_days =0;
        
        $pre_month = array();
        $curret_month = array();
        $combine_chart = array();
        

        $date=date_create(date('Y-m-01'));
        date_modify($date,"-1 month");
        $pre_start =date_format($date,"Y-m-d");
        date_modify($date,"+1 month");
        date_modify($date,"-1 days");
        $pre_end = date_format($date,"Y-m-d");
        
        $current_start = date('Y-m-01');
        $date=date_create(date('Y-m-01'));
        date_modify($date,"+1 month");
        date_modify($date,"-1 days");
        $current_end = date_format($date,"Y-m-d");
        
        $pre_range = Helper::dateRange($pre_start,$pre_end);
        $current_range = Helper::dateRange($current_start,$current_end);
        $day_max = max(count($pre_range),count($current_range));
        
        for ($x = 0; $x <= $day_max-1; $x++) {
            $combine_chart['pre'][$x+1] = 0;
            $combine_chart['current'][$x+1] = 0;
        }
        
        foreach($pre_range as $pre_rangeVAL){
            $daily_total = 0;
            foreach($two_month as $two_monthVAL){
                $rec_date = date('Y-m-d',strtotime($two_monthVAL->created_at));
                if($rec_date==$pre_rangeVAL){
                    $daily_total += (float) str_replace(",","",$two_monthVAL->price);
                }
            }
            $key = (float) date('d',strtotime($pre_rangeVAL));
            if(isset($combine_chart['pre'][$key])){
                $combine_chart['pre'][$key] = $daily_total;
            }
        }
        
        foreach($current_range as $current_rangeVAL){
            $daily_total = 0;
            foreach($two_month as $two_monthVAL){
                $rec_date = date('Y-m-d',strtotime($two_monthVAL->created_at));
                if($rec_date==$current_rangeVAL){
                    $daily_total += (float) str_replace(",","",$two_monthVAL->price);
                }
            }
            $key = (float) date('d',strtotime($current_rangeVAL));
            if(isset($combine_chart['pre'][$key])){
                $combine_chart['current'][$key] = $daily_total;
            }
        }
        
        /* Last 6 Month  */
        
        $last_6_month = Order::get_dashboard_orders('','last_6_month');
        
        
        $six_month_data = array();
        
        $date=date_create();
        
        for ($i=0; $i <6 ; $i++) { 
            $m = date_format($date,"m");
            $month_form = date_format($date,"M-Y");
            $total_of_the_month = 0;
            
            foreach($last_6_month as $last_6_monthVAL){
                
                $record_date = date('Y-m',strtotime($last_6_monthVAL->created_at));
                $match_date = date('Y-m',strtotime(date_format($date,"Y-m-d")));
                if($record_date==$match_date){
                    $total_of_the_month += (float) str_replace(",","",$last_6_monthVAL->price);
                }
            }
            
            $six_month_data[$month_form] = $total_of_the_month;
            date_modify($date,"-1 months");
    		
    	}
    	
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
    	    
    	    $hour_data[date_format($date,"h:i A")] = $total_price;
    	    $hour_orders[date_format($date,"h:i A")] = $total_orders;
    	    
    	    
    	    
    	    date_modify($date,"+1 hours");
    	    
    	    }
    	}
    	
    	/* Shop Pie Chart */
    	
    	$Shop_pie = array();
    	
    	foreach($Shop as $ShopVAL){
    	    $today_order = 0;
    	    $monthly_order = 0;
    	    $last_7_days_order = 0;
    	    $last_30_days_order = 0;
    	    $yesterday_orders = 0;
    	    
    	    
    	    $date=date_create();
            date_modify($date,"-1 days");
            $yesterday_date = date_format($date,"Y-m-d");
            

            
    	    
    	    $Shop_pie[$ShopVAL->id] = array('name'=>$ShopVAL->name);
    	    
    	    $end_week = date('Y-m-d');
    	    $date=date_create();
            date_modify($date,"-6 days");
            $start_week = date_format($date,"Y-m-d");
            
            $end_last_30 = date('Y-m-d');
            $date=date_create();
            date_modify($date,"-29 days");
            $start_last_30 = date_format($date,"Y-m-d");
            
            $week_range = Helper::dateRange($start_week,$end_week);
            
            $last_30_range = Helper::dateRange($start_last_30,$end_last_30);
            
    	    
    	    foreach($today as $todayVAL){
    	        if($todayVAL->shop_id==$ShopVAL->id){
    	            $today_order++;
    	        }
    	    }
    	    
    	    
    	    foreach($monthly as $monthlyVAL){
    	        if($monthlyVAL->shop_id==$ShopVAL->id){
    	            $rec_dt = date('Y-m-d',strtotime($monthlyVAL->created_at));
    	            
    	            $monthly_order++;
    	            foreach($week_range as $weekDAte){
    	                
    	                $wmtch = $weekDAte;
    	                if($wmtch==$rec_dt){
    	                    $last_7_days_order++;
    	                }
    	                
    	            }
    	            
    	            if($rec_dt==$yesterday_date){
    	                    $yesterday_orders++;
    	                }
    	            
    	            
    	            
    	        }
    	    }
    	    
    	    
    	    foreach($last_6_month as $last_6_monthVAL){
    	        if($last_6_monthVAL->shop_id==$ShopVAL->id){
    	            foreach($last_30_range as $last_30_rangeVAL){
    	                $rec_dtcc = date('Y-m-d',strtotime($last_6_monthVAL->created_at));
    	                $wmtchcc = $last_30_rangeVAL;
    	                if($rec_dtcc==$wmtchcc){
    	                    $last_30_days_order++;
    	                }
    	            }
    	            
    	        }
    	    }
    	    $Shop_pie[$ShopVAL->id]['today'] = $today_order;
    	    $Shop_pie[$ShopVAL->id]['monthly'] = $monthly_order;
    	    $Shop_pie[$ShopVAL->id]['last7'] = $last_7_days_order;
    	    $Shop_pie[$ShopVAL->id]['last30'] = $last_30_days_order;
    	    $Shop_pie[$ShopVAL->id]['yesterday'] = $yesterday_orders;
            $Shop_pie[$ShopVAL->id]['name'] = $ShopVAL->name . ' (' . ucfirst($ShopVAL->site) . ')';
    	}
    	
    	
       // die(var_dump(['monthly_sales' => number_format($monthly_sales),
       //      'today_sales' =>number_format($today_sales),
       //      'today_order_count' =>number_format($today_order_count),
       //      'shipped_counter'=>number_format($shipped_counter),
       //      'combine_chart'=>$combine_chart,
       //      'monthly'=>$monthly,
       //      'Shop'=>$Shop,
       //      'colour'=>$colour,
       //      'six_month_data'=>array_reverse($six_month_data),
       //      'hour_data'=>$hour_data,
       //      'hour_orders'=>$hour_orders,
       //      'Shop_pie'=>$Shop_pie]));
        
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/user/dashboard', [
            'monthly_sales' => number_format($monthly_sales),
            'today_sales' =>number_format($today_sales),
            'today_order_count' =>number_format($today_order_count),
            'shipped_counter'=>number_format($shipped_counter),
            'combine_chart'=>$combine_chart,
            'monthly'=>$monthly,
            'Shop'=>$Shop,
            'colour'=>$colour,
            'six_month_data'=>array_reverse($six_month_data),
            'hour_data'=>$hour_data,
            'hour_orders'=>$hour_orders,
            'Shop_pie'=>$Shop_pie
        ]);
    }

    // Dashboard - Ecommerce
    public function dashboardEcommerce(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/pages/dashboard-ecommerce', [
            'pageConfigs' => $pageConfigs
        ]);
    }
    
    
    public function testr(Request $request){
        
        print_r($request->input());
        
        die();
        
        
        
        
    //   echo "<pre>";
       
       
        
        $y = Order::get_shipping_level(265350170331295);
        
        print_r($y);
        
        
        
        
       
                
        die();
         
         
         
       
       
       
    //   Products::upload_image();
       
    //   die();
       
       
       
    //   $x = Helper::get_xml(array('tony'=>'stark'));
       
    //   print_r($x);
       

        
        
        // Products::syncProducts();
        
        // die();
        
        
        
    }
}


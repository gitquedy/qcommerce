<?php

namespace App\Http\Controllers;

use App\Order;
use App\Shop;
use Illuminate\Http\Request;
use App\Lazop;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders All"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $orders = Order::select('id')->whereIn('shop_id',$Shop_array)->where('seen','=','no')->get();
        
        foreach($orders as $ordersVAL){
            $tmp_order = Order::find($ordersVAL->id);
            $tmp_order->seen = 'yes';
            $tmp_order->save();
            
        }

    if ( request()->ajax()) {
        
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('created_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           
           
           $shops_id = $shops->pluck('id')->toArray();
           $statuses = $request->get('status', ['shipped']);
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->whereIn('status', $statuses)->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           
           if($request->get('timings')=="Today"){
               $orders->whereDate('created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orders->whereDate('created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orders->where('created_at', '>=', date("Y-m-01"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
            return Datatables::eloquent($orders)
                ->addColumn('shop', function(Order $order) {
                            return $order->shop ? $order->shop->short_name : '';
                                })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('order.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    

    
    
    
    
    public function orders_pending(Request $request) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders Pending"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $orders = Order::select('id')->whereIn('shop_id',$Shop_array)->where('seen','=','no')->get();
        
        foreach($orders as $ordersVAL){
            $tmp_order = Order::find($ordersVAL->id);
            $tmp_order->seen = 'yes';
            $tmp_order->save();
            
        }

        if ( request()->ajax()) {
        
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('created_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           
           
           $shops_id = $shops->pluck('id')->toArray();
           $statuses = array('pending');
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->whereIn('status', $statuses)->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           
           if($request->get('timings')=="Today"){
               $orders->whereDate('created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orders->whereDate('created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orders->where('created_at', '>=', date("Y-m-01"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
            return Datatables::eloquent($orders)
                ->addColumn('shop', function(Order $order) {
                            return $order->shop ? $order->shop->short_name : '';
                                })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('order.pending', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    
    
    
    
    
    
    public function orders_printing(Request $request) {
      $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders For Printing"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $orders = Order::select('id')->whereIn('shop_id',$Shop_array)->where('printed','=','0')->get();
        
        if (request()->ajax()) {
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('created_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           
           
           $shops_id = $shops->pluck('id')->toArray();
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->where('printed', "=", "0")->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           if($request->get('timings')=="Today"){
               $orders->whereDate('created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orders->whereDate('created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orders->where('created_at', '>=', date("Y-m-01"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
            return Datatables::eloquent($orders)
                ->addColumn('shop', function(Order $order) {
                            return $order->shop ? $order->shop->short_name : '';
                                })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('order.printing', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    
    
    
    
    
    
    public function orders_ready_to_ship(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders Ready to ship"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $orders = Order::select('id')->whereIn('shop_id',$Shop_array)->where('seen','=','no')->get();
        
        foreach($orders as $ordersVAL){
            $tmp_order = Order::find($ordersVAL->id);
            $tmp_order->seen = 'yes';
            $tmp_order->save();
            
        }

    if ( request()->ajax()) {
        
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('created_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           
           
           $shops_id = $shops->pluck('id')->toArray();
           $statuses = array('ready_to_ship');
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->whereIn('status', $statuses)->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           
           if($request->get('timings')=="Today"){
               $orders->whereDate('created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orders->whereDate('created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orders->where('created_at', '>=', date("Y-m-01"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
            return Datatables::eloquent($orders)
                ->addColumn('shop', function(Order $order) {
                            return $order->shop ? $order->shop->short_name : '';
                                })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('order.ready_to_ship', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    
    
    
    
    public function orders_shipped(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders Shipped"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $orders = Order::select('id')->whereIn('shop_id',$Shop_array)->where('seen','=','no')->get();
        
        foreach($orders as $ordersVAL){
            $tmp_order = Order::find($ordersVAL->id);
            $tmp_order->seen = 'yes';
            $tmp_order->save();
            
        }

    if ( request()->ajax()) {
        
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('created_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           
           
           $shops_id = $shops->pluck('id')->toArray();
           $statuses = array('shipped');
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->whereIn('status', $statuses)->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           
           if($request->get('timings')=="Today"){
               $orders->whereDate('created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orders->whereDate('created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orders->where('created_at', '>=', date("Y-m-01"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
            return Datatables::eloquent($orders)
                ->addColumn('shop', function(Order $order) {
                            return $order->shop ? $order->shop->short_name : '';
                                })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('order.shipped', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    
    
    public function orders_delivered(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders Delivered"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        
        $orders = Order::select('id')->whereIn('shop_id',$Shop_array)->where('seen','=','no')->get();
        
        foreach($orders as $ordersVAL){
            $tmp_order = Order::find($ordersVAL->id);
            $tmp_order->seen = 'yes';
            $tmp_order->save();
            
        }

    if ( request()->ajax()) {
        
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('created_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           
           
           $shops_id = $shops->pluck('id')->toArray();
           $statuses = array('delivered');
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->whereIn('status', $statuses)->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');
           
           if($request->get('timings')=="Today"){
               $orders->whereDate('created_at', '=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Yesterday"){
                $date=date_create();
                date_modify($date,"-1 days");
               $orders->whereDate('created_at', '=', date_format($date,"Y-m-d"));
           }
           
           if($request->get('timings')=="Last_7_days"){
                $date=date_create();
                date_modify($date,"-7 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="This_Month"){
                $orders->where('created_at', '>=', date("Y-m-01"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
           if($request->get('timings')=="Last_30_days"){
                $date=date_create();
                date_modify($date,"-30 days");
                $orders->where('created_at', '>=', date_format($date,"Y-m-d"));
                $orders->where('created_at', '<=', date('Y-m-d'));
           }
           
            return Datatables::eloquent($orders)
                ->addColumn('shop', function(Order $order) {
                            return $order->shop ? $order->shop->short_name : '';
                                })
                ->addColumn('statusDisplay', function(Order $order) {
                            return ucwords(str_replace('_', ' ', $order->status));
                                })
                ->addColumn('actions', function(Order $order) {
                            return $order->getActionsDropdown();
                                })
                ->addColumn('created_at_formatted', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        
        return view('order.delivered', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => $statuses,
        ]);
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function cancel(Order $order,Request $request){
        try {
            $msg = $request->get('input');
            $items = $order->getOrderItems();
            $item_ids = $order->getItemIds($items);  
            $result = $order->cancel($item_ids, $msg);
            if(isset($result['message'])){
                $output = ['success' => 0,
                        'msg' => $result['message'],
                    ];
            }else{
                $output = ['success' => 1,
                    'msg' => 'Orders '. $order->id .' Canceled',
                ];
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }    

    public function readyToShip(Order $order,Request $request){
        $order_id = $request->get('order_id');
        try {
            $items = $order->getOrderItems();
            $item_ids = $order->getItemIds($items);
            $result = $order->readyToShip($item_ids);
            if(isset($result['message'])){
                $order->updateTracking();
                $output = ['success' => 0,
                        'msg' => $result['message'],
                    ];
            }else{
                $output = ['success' => 1,
                    'msg' => 'Orders '. $order->id .' Ready to Ship',
                ];
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function readyToShipMultiple(Request $request){
        $order_ids = $request->ids;
        $error = 0;
        try {
            $orders = Order::whereIn('id', $order_ids)->get();
            foreach ($orders as &$order) {
              $items = $order->getOrderItems();
              $item_ids = $order->getItemIds($items);
              $tracking_code = $items['data'][0]['tracking_code'];
              $result = $order->readyToShip($item_ids, $tracking_code);
              if(isset($result['message'])){
                  $error++;
                  $output = ['success' => 0,
                          'msg' => $result['message'],
                      ];
              }else{
                  if(!$error){
                    $output = ['success' => 1,
                        'msg' => 'Orders '. $order->id .' Ready to Ship',
                    ];
                  }
              }
            }
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }
    
    
    
    public function print_shipping($order_id="",Request $request){
        
            $order = Order::where('id', "=", $order_id)->first();
            $order->printed = true;
            $order->save();

            $Result = Order::get_shipping_level($order_id);
            $jsOBJ = json_decode($Result);
            
            if($jsOBJ->code==0){
                if(isset($jsOBJ->data->document->file)){
                    $document = base64_decode($jsOBJ->data->document->file);
                    echo $document;
                }
            
            }else{
                $request->session()->flash('flash_error',$Result);
                return redirect('/order');
            }
            
            
        
    }
    
    public function print_shipping_mass(Request $request){
        
        
        
        $ids = json_decode($request->ids);
        
        foreach($ids as $orderVAL){
            $order = Order::where('id', "=", $orderVAL)->first();
            $order->printed = true;
            $order->save();

            $Result =    Order::get_shipping_level($orderVAL);
            $jsOBJ = json_decode($Result);
            
            if($jsOBJ->code==0){
                if(isset($jsOBJ->data->document->file)){
                    $document = base64_decode($jsOBJ->data->document->file);
                    echo $document."<hr/>";
                }
            
            }else{
                echo "For order# ".$orderVAL." Error - ".$Result."<hr/>";
            }
            
        }
        

        
    }



    public function encode_all_tracking_code() {
      $orders = Order::where(function($query){$query->where('tracking_no','=', '')->orWhereNull('tracking_no');})->where('status', '!=', 'pending')->with('shop')->get();
      $total_count = 0;
      $total_success = 0;
      $total_blank = 0;
      $total_failed = 0;
      foreach ($orders as $order) {
        echo "<br>--Updatting Order:".$order->id;
        if($order->shop) {
          $items = $order->getOrderItems();
          if (isset($items['data'])) {
            $item_ids = $order->getItemIds($items);
            $tracking_code = ($items['data'][0]['tracking_code'])?$items['data'][0]['tracking_code']:'';
            $o = Order::where('id', $order->id)->first();
            $o->tracking_no = $tracking_code;
            $o->save();
            echo "  --   Tracking No:".$tracking_code;
            if($tracking_code){
              $total_success++;
            }
            else {
              $total_blank++;
            }
          }
        }
        else {
          echo "  --   Failed Shop not found";
          $total_failed++;
        }
        $total_count++;
      }
      echo "<br><br>----Done <h1>Total Count: $total_count</h1><h3>With Tracking No: $total_success</h3><h3>Blank: $total_blank</h3><h3>Failed: $total_failed</h3>";
    }
    
    
    
    
}

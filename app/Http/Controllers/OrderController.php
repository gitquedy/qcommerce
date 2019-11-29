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
            ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders"]
        ];
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $statuses = Order::$statuses;
        // foreach($all_shops as $shopSync){
        //     $shopSync->syncOrders(Carbon::now()->subDays(2)->format('Y-m-d'), '+1 day');
        // }
        
    if ( request()->ajax()) {
           $shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc');
           if($request->get('shop', 'all') != 'all'){
                $shops->where('id', $request->get('shop'));
           }
           $shops_id = $shops->pluck('id')->toArray();
           $statuses = $request->get('status', ['shipped']);
           $orders = Order::with('shop')->whereIn('shop_id', $shops_id)->whereIn('status', $statuses)->orderByRaw('CASE WHEN status = "pending" THEN 1 WHEN status = "ready_to_ship" THEN 2 WHEN status = "shipped" THEN 3 else 4 END');

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
                ->addColumn('created_at', function(Order $order) {
                            return Utilities::format_date($order->created_at, 'M d, Y H:i');
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
}

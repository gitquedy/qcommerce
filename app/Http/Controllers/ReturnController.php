<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Shop;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $breadcrumbs = [
          // ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders All"]
      ];
      $shops = $request->user()->shops;
      $shops_id = $shops->pluck('id')->toArray();

      if ( request()->ajax()) {

             $shops = $request->user()->shops;
             if($request->get('shop') != ''){
                $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
             }
             $shops_id = $shops->pluck('id')->toArray();

             $orders = Order::whereIn('shop_id', $shops_id)->whereIn('status', Order::statusesForReturned())->orderBy('updated_at', 'desc');

             if($request->get('tab') == 'not_confirm'){
              $orders->where('returned', false);
             }

             if($request->get('tab') == 'confirm'){
              $orders->where('returned', true);
             }
             
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
                  ->addColumn('idDisplay', function(Order $order) {
                              return $order->getImgAndIdDisplay();
                                  })
                  ->addColumn('actions', function(Order $order) {
                              $text = $order->returned == true ? 'Unconfirm' : 'Confirm';
                              $class = $order->returned == false ? 'text-primary' : 'text-danger';
                              return '<span class="'. $class . ' font-medium-2 text-bold-400 reconcile" data-href="'. action('ReturnController@returnReconcile') .'" data-id="'. $order->OrderID() .'" data-action="'. $text .'">'. $text .'</span>';
                              })
                  ->addColumn('created_at_formatted', function(Order $order) {
                              return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                  })
                  ->addColumn('updated_at_formatted', function(Order $order) {
                                  $created = new Carbon($order->updated_at);
                                  $now = Carbon::now();
                                  return  $created->diffForHumans();
                                  })
                  ->rawColumns(['actions', 'shop', 'idDisplay'])
                  ->make(true);
          }
          
          return view('order.reconciliation.return.index', [
              'breadcrumbs' => $breadcrumbs,
              'all_shops' => $shops,
          ]);
    }

    public function headers(Request $request){
      $shops = $request->user()->shops;
      $shops_id = $shops->pluck('id')->toArray();
      $data = [
        'unconfirmed' => Order::whereIn('shop_id', $shops_id)->whereIn('status', Order::statusesForReturned())->where('returned', false)->count(),
        'confirmed' => Order::whereIn('shop_id', $shops_id)->whereIn('status', Order::statusesForReturned())->where('returned', true)->count(),
      ];
      $data['total'] = $data['unconfirmed'] + $data['confirmed'];
      return response()->json(['data' => $data]);
    }

    public function returnReconcile(Request $request){
        $ids = explode(',',$request->get('ids'));
        $status = $request->get('action', 'Confirm') == 'Confirm' ? 1 : 0;

        Order::whereIn('id', $ids)->orWhereIn('ordersn', $ids)->update(['returned' => $status]);
        return response()->json(['success' => 1, 'msg' => 'Return Reconciliation successfully updated']);
    }
}

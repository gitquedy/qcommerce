<?php

namespace App\Http\Controllers;

use App\Order;
use App\ShippingFee;
use App\Utilities;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Shopee;

class ShippingFeeController extends Controller
{
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['name'=>"Shipping Fee Reconciliation"]
        ];
        $shops = $request->user()->shops;

		 if (request()->ajax()) {

                 $shops = $request->user()->shops;
                 if($request->get('shop') != ''){
                    $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
                 }
                 $shops_id = $shops->pluck('id')->toArray();

                 $orders = Order::whereIn('shop_id', $shops_id)
                                ->where('shipping_fee_reconciled','!=',0)
                                ->with('seller_payout_fees')
                                ->with('customer_payout_fees')
                                ->orderBy('order.updated_at', 'desc');

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
                ->addColumn('overcharge', function (Order $order) {
                            return $order->customer_payout_fees->amount - abs($order->seller_payout_fees->amount);
                })
                ->addColumn('action', function(Order $order) {
                               $text = $order->shipping_fee_reconciled == 1 ? 'Reconcile' : 'Reconcile';
                               $class = $order->shipping_fee_reconciled == 1 ? 'text-primary' : 'text-danger';
                              return '<span class="'. $class . ' font-medium-2 text-bold-400 reconcile" data-href="'. action('ReturnController@returnReconcile') .'" data-id="'. $order->OrderID() .'" data-action="'. $text .'">'. $text .'</span>';
                              })

                ->addColumn('shipping_fee_reconciled', function(Order $order) {
                               if ($order->shipping_fee_reconciled == 1) {
                                  return "Over Charged";
                                }
                                else if($order->shipping_fee_reconciled == 2) {
                                  return "Filed Discpute";
                                }
                                else if($order->shipping_fee_reconciled == 3) {
                                  return "Resolved";
                                }
                                else {
                                  return "Pending";
                                }
                              })
                    ->rawColumns(['action', 'idDisplay'])
	            ->make(true);
	        }

        return view('order.reconciliation.shippingFee.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $shops,
        ]);
    }

    public function headers(Request $request){
      $shops = $request->user()->shops;
      $shops_id = $shops->pluck('id')->toArray();
      $data = [
        'pending' => Order::whereIn('shop_id', $shops_id)->where('shipping_fee_reconciled', 1)->count(),
        'filed' => Order::whereIn('shop_id', $shops_id)->where('shipping_fee_reconciled', 2)->count(),
        'resolved' => Order::whereIn('shop_id', $shops_id)->where('shipping_fee_reconciled', 3)->count(),
        'total' => Order::whereIn('shop_id', $shops_id)->where('shipping_fee_reconciled', '!=', 0)->count(),
      ];
      return response()->json(['data' => $data]);
    }

}

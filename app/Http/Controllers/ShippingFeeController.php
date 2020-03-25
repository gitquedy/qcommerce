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
        $shops = $request->user()->business->shops;

		 if (request()->ajax()) {

                 $shops = $request->user()->business->shops;
                 if($request->get('shop') != ''){
                    $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
                 }
                 $shops_id = $shops->pluck('id')->toArray();

                 $orders = Order::whereIn('shop_id', $shops_id)
                                ->where('shipping_fee_reconciled','!=',0)
                                ->with('seller_payout_fees')
                                ->with('customer_payout_fees')
                                ->orderBy('order.updated_at', 'desc');

                if($request->get('tab') == 'pending'){
                  $orders->where('shipping_fee_reconciled', 1);
                 } else if($request->get('tab') == 'filed'){
                  $orders->where('shipping_fee_reconciled', 2);
                 }else if($request->get('tab') == 'resolved'){
                  $orders->where('shipping_fee_reconciled', 3);
                 }

                $daterange = explode('/', $request->get('daterange'));
                if(count($daterange) == 2){
                    if($daterange[0] == $daterange[1]){
                        $orders->whereDate('created_at', [$daterange[0]]);
                    }else{
                        $orders->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                    }
                }
	            return Datatables::eloquent($orders)
                ->addColumn('idDisplay', function(Order $order) {
                              return $order->getImgAndIdDisplay();
                            })
                ->addColumn('shipping_by_customer', function (Order $order) {
                            return number_format($order->customer_payout_fees->amount);
                })
                ->addColumn('shipping_by_seller', function (Order $order) {
                            return number_format(abs($order->seller_payout_fees->amount));
                })
                ->addColumn('overcharge', function (Order $order) {
                            return number_format(abs($order->seller_payout_fees->amount) - $order->customer_payout_fees->amount);
                })
                // ->addColumn('action', function(Order $order) {
                //                $text = $order->shipping_fee_reconciled == 1 ? 'Reconcile' : 'Reconcile';
                //                $class = $order->shipping_fee_reconciled == 1 ? 'text-primary' : 'text-danger';
                //               return '<span class="'. $class . ' font-medium-2 text-bold-400 reconcile" data-href="'. action('ReturnController@returnReconcile') .'" data-id="'. $order->OrderID() .'" data-action="'. $text .'">'. $text .'</span>';
                //               })

                ->addColumn('action', function(Order $order) {
                              $disabled = ['filed' => 'disabled' , 'resolved' => 'disabled'];
                              $button = '<button type="button" class="btn btn-primary order_view_details" data-order_id="'. $order->OrderID() .'" data-action="'.route('barcode.viewBarcode').'" >View detail</button>';
                              if($order->shipping_fee_reconciled == 1){
                                $disabled['filed'] = '';
                                $disabled['resolved'] = '';
                                $button = '<button type="button" class="btn btn-primary"><a class="text-white" target="_blank" href="https://xform.lazada.com.ph/form/show.do?spm=a2a15.helpcenter-psc-contact.new-navigation.8.2ef25331K09vKG&lang=en">File Dispute</a></button>';
                              }else if($order->shipping_fee_reconciled == 2){
                                $disabled['resolved'] = '';
                              }
                    return  '<div class="btn-group dropup mr-1 mb-1">
                                 '. $button .'
                                  <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                  <span class="sr-only">Toggle Dropdown</span></button>
                                  <div class="dropdown-menu">
                                      <a class="dropdown-item confirm '. $disabled['filed'] .'" href="#" data-text="Are you sure to mark ' . $order->OrderID() .' as filed dispute ?" data-text=""  data-href="'. action('ShippingFeeController@filed', $order->id) .'"><i class="fa fa-file aria-hidden="true"></i> Filed Dispute</a>
                                      <a class="dropdown-item confirm '. $disabled['resolved'] .'" href="#" data-text="Are you sure mark ' . $order->OrderID() .' as resolved?" data-text=""  data-href="'. action('ShippingFeeController@resolved', $order->id) .'"><i class="fa fa-handshake-o aria-hidden="true"></i> Resolved</a>
                                  </div></div>';
                              })

                ->addColumn('shipping_fee_reconciled', function(Order $order) {
                               if ($order->shipping_fee_reconciled == 1) {
                                  $text =  'Over Charged';
                                  $class= "text-danger";
                                }
                                else if($order->shipping_fee_reconciled == 2) {
                                  $text =  "Filed Discpute";
                                  $class= "text-warning";
                                }
                                else if($order->shipping_fee_reconciled == 3) {
                                  $text =  "Resolved";
                                  $class= "text-success";
                                }
                                else {
                                  $text = "Pending";
                                  $class= "text-danger";
                                }
                              return '<span class="'. $class .' font-medium-1 text-bold-400">' . $text .'</span>';
                                  })
                  ->rawColumns(['action', 'idDisplay', 'shipping_fee_reconciled'])
	                  ->make(true);
	        }

        return view('order.reconciliation.shippingFee.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $shops,
        ]);
    }

    public function headers(Request $request){
      $shops = $request->user()->business->shops();
      if($request->get('shop') != ''){
         $shops = $shops->whereIn('id', explode(',', $request->get('shop')));
      }
      $shops_id = $shops->pluck('id')->toArray();
      
      $daterange = explode('/', $request->get('daterange'));

      $pending = Order::whereIn('shop_id', $shops_id);
      $filed = Order::whereIn('shop_id', $shops_id);
      $resolved = Order::whereIn('shop_id', $shops_id);
      $total = Order::whereIn('shop_id', $shops_id);


      if($daterange[0] == $daterange[1]){
          $pending = $pending->whereDate('created_at', [$daterange[0]]);
          $filed = $filed->whereDate('created_at', [$daterange[0]]);
          $resolved = $resolved->whereDate('created_at', [$daterange[0]]);
          $total = $total->whereDate('created_at', [$daterange[0]]);
      }else{
          $pending = $pending->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
          $filed = $filed->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
          $resolved = $resolved->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
          $total = $total->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
      }
      $pending = $pending->where('shipping_fee_reconciled', 1)->count();
      $filed = $filed->where('shipping_fee_reconciled', 2)->count();
      $resolved = $resolved->where('shipping_fee_reconciled', 3)->count();
      $total = $total->where('shipping_fee_reconciled', '!=', 0)->count();

      $data = [
        'pending' => $pending,
        'filed' => $filed,
        'resolved' => $resolved,
        'total' => $total,
      ];
      return response()->json(['data' => $data]);
    }

    public function filed(Order $order){
        try {
          $text = 'filed dispute';
          $order->update(['shipping_fee_reconciled' => 2]);

          $output = ['success' => 1,
              'msg' => 'Order '. $order->orderID() .' successfully '. $text,
          ];
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function resolved(Order $order){
        try {
          $text = 'resolved';
          $order->update(['shipping_fee_reconciled' => 3]);

          $output = ['success' => 1,
              'msg' => 'Order '. $order->orderID() .' successfully '. $text . ' payout',
          ];
            
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function massReconcile(Request $request){
        $ids = explode(',',$request->get('ids'));
        $status = $request->get('action', 'resolved') == 'resolved' ? 3 : 2;
        Order::whereIn('id', $ids)->orWhereIn('ordersn', $ids)->update(['shipping_fee_reconciled' => $status]);
        return response()->json(['success' => 1, 'msg' => 'Shipping Fee Reconciliation successfully updated']);
    }

}

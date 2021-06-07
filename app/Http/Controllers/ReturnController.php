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
        $this->authorize('is_included_in_plan', 'return_recon');
      $breadcrumbs = [
          // ['link'=>"/",'name'=>"Home"],['link'=> action('OrderController@index'), 'name'=>"Orders List"], ['name'=>"Orders All"]
      ];
      $shops = $request->user()->business->shops;
      $shops_id = $shops->pluck('id')->toArray();

      if ( request()->ajax()) {

             $shops = $request->user()->business->shops;
             if($request->get('shop') != ''){
                $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
             }
             $shops_id = $shops->pluck('id')->toArray();

             $orders = Order::whereIn('shop_id', $shops_id)->whereIn('status', Order::statusesForReturned())->orderBy('updated_at', 'desc');

             if($request->get('tab') == 'not_confirm'){
              $orders->where('returned', false);
             } else if($request->get('tab') == 'confirm'){
              $orders->where('returned', true);
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
                  ->addColumn('return_status', function (Order $order) {
                              return ($order->returned)? "Confirmed":"Pending";
                  })
                  ->addColumn('actions', function(Order $order) {
                              $text = $order->returned == true ? 'Unconfirm' : 'Confirm';
                                          $disabled =  $order->payout == true ? '' : '';
                                return  '<div class="btn-group dropup mr-1 mb-1">
                                         <button type="button" class="btn btn-primary order_view_details" data-order_id="'. $order->OrderID() .'" data-action="'.route('barcode.viewBarcode').'" >View detail</button>
                                          <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                          <span class="sr-only">Toggle Dropdown</span></button>
                                          <div class="dropdown-menu">
                                              <a class="dropdown-item confirm'. $disabled .'" href="#" data-text="Are you sure to ' . $text . ' ' . $order->OrderID() .' return?" data-text=""  data-href="'. action('ReturnController@returnReconcileSingle', $order->id) .'"><i class="fa fa-check aria-hidden="true"></i> '. $text .'</a>
                                          </div></div>';
                                          })
                  ->addColumn('statusText', function(Order $order) {
                                  $class = $order->returned == true ? 'text-primary' : 'text-danger';
                                  $text = $order->returned == true ? 'Confirmed' : 'Unconfirmed';
                                  return '<span class="'. $class .' font-medium-1 text-bold-400">' . $text .'</span>';
                                  })
                  ->addColumn('created_at_formatted', function(Order $order) {
                              return Utilities::format_date($order->created_at, 'M. d,Y H:i A');
                                  })
                  ->addColumn('updated_at_formatted', function(Order $order) {
                                  $created = new Carbon($order->updated_at);
                                  $now = Carbon::now();
                                  return  $created->diffForHumans();
                                  })
                  ->rawColumns(['actions', 'shop', 'idDisplay', 'statusText'])
                  ->make(true);
          }
          
          return view('order.reconciliation.return.index', [
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
      $unconfirmed =  Order::whereIn('shop_id', $shops_id)->whereIn('status', Order::statusesForReturned())->where('returned', false);
      $confirmed =  Order::whereIn('shop_id', $shops_id)->whereIn('status', Order::statusesForReturned())->where('returned', true);

      if($daterange[0] == $daterange[1]){
          $unconfirmed = $unconfirmed->whereDate('created_at', [$daterange[0]]);
          $confirmed = $confirmed->whereDate('created_at', [$daterange[0]]);
      }else{
          $unconfirmed = $unconfirmed->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
          $confirmed = $confirmed->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
      }
      $data = [
        'unconfirmed' => $unconfirmed->count(),
        'confirmed' => $confirmed->count(),
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

    public function returnReconcileSingle(Order $order){
        try {
          $text = $order->returned == true ? 'unconfirmed' : 'confirmed';
            if($order->returned == true){
              $order->update(['returned' => false]);
            }else{
              $order->update(['returned' => true]);
            }

              $output = ['success' => 1,
                  'msg' => 'Order '. $order->orderID() .' successfully '. $text . ' return',
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


}

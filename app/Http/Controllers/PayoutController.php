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
use App\LazadaPayout;
use App\ShopeePayout;

class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexLaz(Request $request)
    {
      $breadcrumbs = [];
      $shops = $request->user()->business->shops->where('site', 'lazada');
      $shops_id = $shops->pluck('id')->toArray();

      if ( request()->ajax()) {
             $shops = $request->user()->business->shops;
             if($request->get('shop') != ''){
                $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
             }
             $shop_ids = $shops->pluck('id')->toArray();

             $payout = LazadaPayout::whereIn('shop_id', $shop_ids)->orderBy('created_at', 'desc');

             if($request->get('tab') == 'confirm'){
               $payout->where('reconciled', true);
             }elseif ($request->get('tab') == 'not_confirm') {
               $payout->where('reconciled', false);
             }

             $daterange = explode('/', $request->get('daterange'));
              if(count($daterange) == 2){
                  if($daterange[0] == $daterange[1]){
                      $payout->whereDate('created_at', [$daterange[0]]);
                  }else{
                      $payout->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                  }
              }
             
              return Datatables::eloquent($payout)
              ->addColumn('paidDisplay', function(LazadaPayout $payout) {
                            return $payout->getPaidStatusDisplay();
                })
              ->addColumn('reconciledDisplay', function(LazadaPayout $payout) {
                            return $payout->getReconciledStatusDisplay();
                })
              ->addColumn('shopDisplay', function(LazadaPayout $payout) {
                            return $payout->shop->getImgSiteDisplay();
                })
              ->editColumn('closing_balance', function(LazadaPayout $payout) {
                            return number_format($payout->closing_balance, 2) . ' PHP';
                })

              ->addColumn('actions', function(LazadaPayout $payout) {
                              $text = $payout->reconciled == true ? 'Unconfirm' : 'Confirm';
                              $disabled =  $payout->reconciled == true ? '' : '';
                    return  '<div class="btn-group dropup mr-1 mb-1">
                                 <button type="button" class="btn btn-primary modal_button" data-href="'. action("PayoutController@showLaz", $payout->id) .'"> View detail</button>
                                  <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                                  <span class="sr-only">Toggle Dropdown</span></button>
                                  <div class="dropdown-menu">
                                    <a class="dropdown-item confirm'. $disabled .'" href="#" data-href="'. action('PayoutController@showLaz', $payout->id) .'"><i class="fa fa-eye aria-hidden="true"></i>View Details</a>
                                      <a class="dropdown-item confirm'. $disabled .'" href="#" data-text="Are you sure to ' . $text . ' ' . $payout->statement_number .' payout?" data-text="" data-href="'. action('PayoutController@payoutReconcileSingleLaz', $payout->id) .'"><i class="fa fa-check aria-hidden="true"></i> '. $text .'</a>
                                  </div></div>';
                 })
              ->editColumn('created_at', function(LazadaPayout $payout) {
                            return Carbon::parse($payout->created_at)->format('M d, Y');
              })
              ->rawColumns(['paidDisplay', 'reconciledDisplay', 'shopDisplay', 'actions'])
              ->make(true);
          }
          
          return view('order.reconciliation.payout.lazada.index', [
              'breadcrumbs' => $breadcrumbs,
              'all_shops' => $shops,
          ]);
    }

    public function indexShopee(Request $request)
    {
      $breadcrumbs = [];
      $shops = $request->user()->business->shops->where('site', 'shopee');
      $shops_id = $shops->pluck('id')->toArray();

      if ( request()->ajax()) {
             $shops = $request->user()->business->shops;
             if($request->get('shop') != ''){
                $shops = $shops->whereIn('id', explode(",", $request->get('shop')));
             }
             $shop_ids = $shops->pluck('id')->toArray();

             $payout = ShopeePayout::whereIn('shop_id', $shop_ids)->orderBy('created_at', 'desc');

             if($request->get('tab') == 'confirm'){
               $payout->where('reconciled', true);
             }elseif ($request->get('tab') == 'not_confirm') {
               $payout->where('reconciled', false);
             }

             $daterange = explode('/', $request->get('daterange'));
              if(count($daterange) == 2){
                  if($daterange[0] == $daterange[1]){
                      $payout->whereDate('created_at', [$daterange[0]]);
                  }else{
                      $payout->whereDate('created_at', '>=', $daterange[0])->whereDate('created_at', '<=', $daterange[1]);
                  }
              }
             
              return Datatables::eloquent($payout)
              ->addColumn('reconciledDisplay', function(ShopeePayout $payout) {
                            return $payout->getReconciledStatusDisplay();
                })
              ->addColumn('shopDisplay', function(ShopeePayout $payout) {
                            return $payout->shop->getImgSiteDisplay();
                })
              ->editColumn('amount', function(ShopeePayout $payout) {
                            return number_format($payout->amount, 2) . ' PHP';
                })
              ->addColumn('actions', function(ShopeePayout $payout) {
                              $text = $payout->reconciled == true ? 'Unconfirm' : 'Confirm';
                              $disabled =  $payout->reconciled == true ? '' : '';
                              $class = $payout->reconciled == true ? 'warning' : 'primary';
                    return  '<div class="btn-group dropup mr-1 mb-1">
                                 <button type="button" class="btn btn-'. $class .' confirm" href="#" data-text="Are you sure to ' . $text . ' ' . $payout->statement_number .' payout?" data-text="" data-href="'. action('PayoutController@payoutReconcileSingleShopee', $payout->id) .'"><i class="fa fa-check aria-hidden="true"></i> '. $text .'</button>';
                 })
              ->editColumn('created_at', function(ShopeePayout $payout) {
                            return Carbon::parse($payout->created_at)->format('M d, Y');
              })
              ->rawColumns(['reconciledDisplay', 'shopDisplay', 'actions'])
              ->make(true);
          }
          
          return view('order.reconciliation.payout.shopee.index', [
              'breadcrumbs' => $breadcrumbs,
              'all_shops' => $shops,
          ]);
    }

    public function headersShopee(Request $request){
      $shops = $request->user()->business->shops();
      if($request->get('shop') != ''){
         $shops = $shops->whereIn('id', explode(',', $request->get('shop')));
      }
      $shops_id = $shops->pluck('id')->toArray();

      $daterange = explode('/', $request->get('daterange'));
      $unconfirmed = ShopeePayout::whereIn('shop_id', $shops_id)->where('reconciled', false);
      $confirmed = ShopeePayout::whereIn('shop_id', $shops_id)->where('reconciled', true);

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

    public function headersLaz(Request $request){
      $shops = $request->user()->business->shops();
      if($request->get('shop') != ''){
         $shops = $shops->whereIn('id', explode(',', $request->get('shop')));
      }
      $shops_id = $shops->pluck('id')->toArray();

      $daterange = explode('/', $request->get('daterange'));
      $unconfirmed = LazadaPayout::whereIn('shop_id', $shops_id)->where('reconciled', false);
      $confirmed = LazadaPayout::whereIn('shop_id', $shops_id)->where('reconciled', true);

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

    public function payoutReconcileLaz(Request $request){
        $ids = explode(',',$request->get('ids'));
        $status = $request->get('action', 'Confirm') == 'Confirm' ? 1 : 0;
        LazadaPayout::whereIn('id', $ids)->update(['reconciled' => $status]);
        return response()->json(['success' => 1, 'msg' => 'Payout Reconciliation successfully updated']);
    }

    public function payoutReconcileSingleLaz(LazadaPayout $LazadaPayout){
        try {
          $text = $LazadaPayout->reconciled == true ? 'unconfirmed' : 'confirmed';
            if($LazadaPayout->reconciled == true){
              $LazadaPayout->update(['reconciled' => false]);
            }else{
              $LazadaPayout->update(['reconciled' => true]);
            }
              $output = ['success' => 1,
                  'msg' => 'Payout '. $LazadaPayout->statement_number .' successfully '. $text . ' payout',
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

    public function showLaz(LazadaPayout $LazadaPayout){
      return view('order.reconciliation.payout.lazada.show', compact('LazadaPayout'));
    }


    public function payoutReconcileShopee(Request $request){
        $ids = explode(',',$request->get('ids'));
        $status = $request->get('action', 'Confirm') == 'Confirm' ? 1 : 0;
        ShopeePayout::whereIn('id', $ids)->update(['reconciled' => $status]);
        return response()->json(['success' => 1, 'msg' => 'Payout Reconciliation successfully updated']);
    }

    public function payoutReconcileSingleShopee(ShopeePayout $ShopeePayout){
        try {
          $text = $ShopeePayout->reconciled == true ? 'unconfirmed' : 'confirmed';
            if($ShopeePayout->reconciled == true){
              $ShopeePayout->update(['reconciled' => false]);
            }else{
              $ShopeePayout->update(['reconciled' => true]);
            }
              $output = ['success' => 1,
                  'msg' => 'Payout '. $ShopeePayout->payout_date .' successfully '. $text . ' payout',
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

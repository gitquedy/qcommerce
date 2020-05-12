<?php

namespace App\Http\Controllers\Api\Reconciliation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Shop;
use Validator;
use App\LazadaPayout;
use App\ShopeePayout;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
         $validation = [
            'site' => ['required', 'in:lazada,shopee'],
            'per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'created_from' => ['sometimes', 'required' , 'date', 'date_format:Y-m-d'],
            'created_to' => ['required_with:created_from', 'after:created_from' , 'date' , 'date_format:Y-m-d'],
            'sort_by' => ['in:created_at,updated_at'],
            'sort_direction' => ['in:ASC,DESC'],
            'tab' => ['in:1,0'],
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        $user = $request->user();
        $shops = Shop::where('business_id', $user->business_id);
        if($request->get('shop_ids')){
            $shops->whereIn('id', explode(',', $request->get('shop_ids')));
        }
        $shop_ids = $shops->pluck('id')->toArray();

        if($request->get('site') == 'lazada'){
            $payouts = LazadaPayout::whereIn('shop_id', $shop_ids)->orderBy('created_at', 'desc');
        }else{
            $payouts = ShopeePayout::whereIn('shop_id', $shop_ids)->orderBy('created_at', 'desc');
        }
        
        if($request->get('status')){
            $payouts = $payouts->where('status', $request->get('status'));
        }
        if($request->get('created_from') && $request->get('created_to')){
            $payouts = $payouts->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('tab') !== null){
            $payouts->where('reconciled', $request->get('tab'));
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $payouts = $payouts->orderBy($request->get('sort_by'), $sort_direction);
        }

        $payouts = $payouts->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['payouts' => $payouts];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function reconcile(Request $request){
        $validation = [
            'ids' => ['required'],
            'action' => ['required', 'in:1,0'],
            'site' => ['required', 'in:lazada,shopee'],
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        $ids = explode(',',$request->get('ids'));
        $user = $request->user();
        $shops = Shop::where('business_id', $user->business_id);
        $shop_ids = $shops->pluck('id')->toArray();

        if($request->get('site') == 'lazada'){
            $payouts = LazadaPayout::whereIn('shop_id', $shop_ids)->whereIn('id', $ids)->update(['reconciled' => $request->get('action')]);
            $update_payouts = LazadaPayout::whereIn('shop_id', $shop_ids)->whereIn('id', $ids)->get();
        }else{
            $payouts = ShopeePayout::whereIn('shop_id', $shop_ids)->whereIn('id', $ids)->update(['reconciled' => $request->get('action')]);
            $update_payouts = ShopeePayout::whereIn('shop_id', $shop_ids)->whereIn('id', $ids)->get();
        }
        
        return ResponseBuilder::asSuccess(200)
                  ->withData(['update_payouts' => $update_payouts])
                  ->withMessage('OK')
                  ->build();
    }


    public function headers(Request $request){
        $validation = [
            'site' => ['required', 'in:lazada,shopee'],
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
      }
      $shops = $request->user()->business->shops();
      if($request->get('shop') != ''){
         $shops = $shops->whereIn('id', explode(',', $request->get('shop')));
      }
      $shops_id = $shops->pluck('id')->toArray();

      if($request->get('site') == 'lazada'){
        $unconfirmed = LazadaPayout::whereIn('shop_id', $shops_id)->where('reconciled', false);
        $confirmed = LazadaPayout::whereIn('shop_id', $shops_id)->where('reconciled', true);
      }else{
        $unconfirmed = ShopeePayout::whereIn('shop_id', $shops_id)->where('reconciled', false);
        $confirmed = ShopeePayout::whereIn('shop_id', $shops_id)->where('reconciled', true);
      }

      if($request->get('created_from') && $request->get('created_to')){
          $unconfirmed = $unconfirmed->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
          $confirmed = $confirmed->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
      }

      $data = [
        'unconfirmed' => $unconfirmed->count(),
        'confirmed' => $confirmed->count(),
      ];
      $data['total'] = $data['unconfirmed'] + $data['confirmed'];

      return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }
}

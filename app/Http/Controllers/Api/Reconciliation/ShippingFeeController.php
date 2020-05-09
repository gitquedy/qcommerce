<?php

namespace App\Http\Controllers\Api\Reconciliation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Shop;
use Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ShippingFeeController extends Controller
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
            'tab' => ['in:1,2,3'],
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
        $orders = Order::whereIn('shop_id',$shop_ids)->where('shipping_fee_reconciled','!=',0)->with('seller_payout_fees')->with('customer_payout_fees');

        if($request->get('site')){
            $orders = $orders->where('site', $request->get('site'));
        }
        
        if($request->get('status')){
            $orders = $orders->where('status', $request->get('status'));
        }
        if($request->get('created_from') && $request->get('created_to')){
            $orders = $orders->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('tab') !== null){
            $orders->where('shipping_fee_reconciled', $request->get('tab'));
        }

        $orders = $orders->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['orders' => $orders];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
        
    }

    public function reconcile(Request $request){
        $validation = [
            'order_ids' => ['required'],
            'action' => ['required', 'in:1,2,3'],
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }
        $order_ids = explode(',',$request->get('order_ids'));
        $user = $request->user();
        $shops = Shop::where('business_id', $user->business_id);
        $shop_ids = $shops->pluck('id')->toArray();
        

        Order::whereIn('id', $order_ids)->orWhereIn('ordersn', $order_ids)
        ->whereIn('shop_id',$shop_ids)
        ->where('shipping_fee_reconciled','!=',0)
        ->update(['shipping_fee_reconciled' => $request->get('action')]);

        $updated_orders = Order::whereIn('id', $order_ids)->orWhereIn('ordersn', $order_ids)
        ->whereIn('shop_id',$shop_ids)
        ->where('shipping_fee_reconciled','!=',0)->get();


        return ResponseBuilder::asSuccess(200)
                  ->withData(['updated_orders' => $updated_orders])
                  ->withMessage('OK')
                  ->build();
    }

    public function reconciliation_link(){
        $data['reconciliation_link'] = env('shipping_fee_reconciliation_link');
        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }
}

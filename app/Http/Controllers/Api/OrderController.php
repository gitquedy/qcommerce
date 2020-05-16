<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use App\Shop;
use Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class OrderController extends Controller
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
            'sort_by' => ['in:created_at,updated_at'],
            'sort_direction' => ['in:ASC,DESC'],
            'per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'created_from' => ['sometimes', 'required' , 'date', 'date_format:Y-m-d'],
            'created_to' => ['required_with:created_from', 'after:created_from' , 'date' , 'date_format:Y-m-d'],
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
        $orders = Order::whereIn('shop_id',$shop_ids);

        if($request->get('site')){
            $orders = $orders->where('site', $request->get('site'));
        }
        
        if($request->get('status')){
            $orders = $orders->where('status', $request->get('status'));
        }

        if($request->get('created_from') && $request->get('created_to')){
            $orders = $orders->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $orders = $orders->orderBy($request->get('sort_by'), $sort_direction);
        }

        if($request->get('order_ids')){
            $orders = $orders->whereIn('ordersn', explode(',', $request->get('order_ids')));
        }

        $orders = $orders->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['orders' => $orders];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function statuses(){
        $data = ['lazada' => Order::$statuses, 'shopee' => Order::$shopee_statuses];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function headers(Request $request){
        $data = [];
        $shop_ids =  $request->user()->business->shops->pluck('id')->toArray();
        $lazada_statuses = Order::$statuses;
        foreach($lazada_statuses as $status){
            $orders = Order::whereIn('shop_id', $shop_ids)->where('site', 'lazada');
            $data['lazada'][$status] = $orders->where('status', $status)->count();
        }
        $shopee_statuses = Order::$shopee_statuses;
        foreach($shopee_statuses as $status){
            $orders = Order::whereIn('shop_id', $shop_ids)->where('site', 'shopee');
            $data['shopee'][$status] = $orders->where('status', $status)->count();
        }
        $data['lazada_pending'] = Order::whereIn('shop_id', $shop_ids)->where('site', 'lazada')->whereIn('status', ['pending'])->count();
        $data['shopee_pending'] = Order::whereIn('shop_id', $shop_ids)->where('site', 'shopee')->whereIn('status', ['RETRY_SHIP', 'READY_TO_SHIP'])->count();

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function lazadaRts(Request $request){
        $validation = [
            'ids' => ['required', 'in:lazada,shopee'],
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        $ids = $request->get('ids');

        $shop_ids =  $request->user()->business->shops->pluck('id')->toArray();

        $orders = Order::whereIn('shop_id', $shop_ids)->where('site', 'lazada')->whereIn('ordersn', $ids);

        $success = [];
        $fail = [];

        foreach($orders as $order){
            $items = $order->getOrderItems();
            $item_ids = $order->getItemIds($items);
            $result = $order->readyToShip($item_ids);
            if(isset($result['message'])){
                $order->updateTracking();
                $fail[$order->ordersn] = $order;
                $fail[$order->sn]['message'] = $result['message'];
            }else{
                $success[$order->ordersn] = $order;
                $success[$order->sn]['message'] = $result['message'];
            }
        }
        
        $data['success'] = $success;
        $data['fail'] = $fail;

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }
}

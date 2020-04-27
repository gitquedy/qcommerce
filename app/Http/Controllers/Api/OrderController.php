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
}

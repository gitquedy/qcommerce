<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Products;
use Illuminate\Http\Request;
use App\Shop;
use Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ProductController extends Controller
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
        $products = Products::whereIn('shop_id',$shop_ids);
        if($request->get('status')){
            $products = $products->where('Status', $request->get('status'));
        }
        if($request->get('created_from') && $request->get('created_to')){
            $products = $products->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        $products = $products->paginate(100)->jsonSerialize();
        $data = ['products' => $products];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function statuses(){
        $data = ['lazada' => Products::$lazadaStatuses, 'shopee' => Products::$shopeeStatuses];
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
     * @param  \App\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Products $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Products $products)
    {
        //
    }
}

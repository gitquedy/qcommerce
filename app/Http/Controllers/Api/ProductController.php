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
            'sort_by' => ['in:created_at,updated_at'],
            'sort_direction' => ['in:ASC,DESC'],
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

        if($request->get('site')){
            $products = $products->where('site', $request->get('site'));
        }
        if($request->get('created_from') && $request->get('created_to')){
            $products = $products->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $products = $products->orderBy($request->get('sort_by'), $sort_direction);
        }

        if($request->get('ids')){
            $products = $products->whereIn('id', explode(',', $request->get('ids')));
        }

        if($request->get('seller_sku_ids')){
            $products = $products->whereIn('seller_sku_ids', explode(',', $request->get('seller_sku_ids')));
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
}

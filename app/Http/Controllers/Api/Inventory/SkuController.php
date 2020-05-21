<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Sku;
use Illuminate\Http\Request;
use Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Carbon\Carbon;
use App\Shop;
use App\Products;


class SkuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $validation = [
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

        $skus = Sku::with('products')->where('business_id',$user->business_id);

        if($request->get('id')){
            $skus = $skus->where('id', $request->get('id'));
        }

        if($request->get('created_from') && $request->get('created_to')){
            $skus = $skus->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $skus = $skus->orderBy($request->get('sort_by'), $sort_direction);
        }

        $skus = $skus->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['skus' => $skus];

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
        $validation = [
            'code' => 'required|unique:sku,code,NULL,id,business_id,' . $request->user()->business_id,
            'name' => 'required',
            'brand' => 'nullable',
            'category' => 'nullable',
            'supplier' => 'nullable',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'alert_quantity' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }
        $sku = $request->all();
        $sku['business_id'] = $request->user()->business_id;
        $sku['quantity'] = 0;

        $sku_created = Sku::create($sku);

        $data = ['skus' => $sku_created];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Created')
                  ->build();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sku  $sku
     * @return \Illuminate\Http\Response
     */
    public function show(Sku $sku)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sku  $sku
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sku $sku)
    {
        $validation = [
            'code' => 'required|unique:sku,code,NULL,id,business_id,' . $request->user()->business_id,
            'name' => 'required',
            'brand' => 'nullable',
            'category' => 'nullable',
            'supplier' => 'nullable',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'alert_quantity' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        if($sku->business_id != $request->user()->business_id){
            return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Sku doesnt belong to current users business id')
                  ->build();
        }

        $sku->update($request->all());

        foreach($sku->products as $product){
            $product->update([
                    'seller_sku_id' => $sku->id,
                    'price' => $sku->price,
                    'quantity' => $sku->quantity,
            ]);
            if(env('lazada_sku_sync', true)){
                if($prod->site == 'lazada'){
                    $product->lazada_sync_sku();
                }
            }
        }

        $data = ['skus' => $sku];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Updated')
                  ->build();

        return $sku;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sku  $sku
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Sku $sku)
    {
        if($sku->business_id != $request->user()->business_id){
            return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Sku doesnt belong to current users business id')
                  ->build();
        }

        foreach($sku->products as $products){
            $products->update(['seller_sku_id' => null]);
        }
 
        $sku->delete();

        $data = ['skus' => $sku];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Deleted')
                  ->build();
    }


    public function link(Sku $sku, Request $request){
        $validation = [
            'action' => ['in:1,0'],
            'product_ids' => ['required'],
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
        $shop_ids = $shops->pluck('id')->toArray();

        $action = $request->get('action');

     
        $products = Products::whereIn('shop_id', $shop_ids)->whereIn('id', explode(',', $request->get('product_ids')));

        $linked = [];
        $unlinked = [];
        
        if($action == "1"){ //link

            foreach($products->get() as $product){

                $product->update([
                    'seller_sku_id' => $sku->id,
                    'price' => $sku->price,
                    'quantity' => $sku->quantity,

                ]);
                if(env('lazada_sku_sync', true)){
                    if($prod->site == 'lazada'){
                        $product->lazada_sync_sku();
                    }
                }
                $linked[] = $product;
            }
            
            
        }else if($action == "0"){ //unlink
            foreach($products->get() as $product){
                $product->update(['seller_sku_id' => null]);
                $unlinked[] = $product;
            }
        }
     
        $data = [
            'linked' => $linked,
            'unlinked' => $unlinked,
        ];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Success')
                  ->build();
    }
}

<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Adjustment;
use Illuminate\Http\Request;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Carbon\Carbon;
use Validator;
use App\Settings;
use App\Sku;
use App\AdjustmentItems;
use App\OrderRef;
use DB;

class AdjustmentController extends Controller
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

        $adjustments = Adjustment::where('business_id',$user->business_id);

        if($request->get('id')){
            $adjustments = $adjustments->where('id', $request->get('id'));
        }

        if($request->get('created_from') && $request->get('created_to')){
            $adjustments = $adjustments->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $adjustments = $adjustments->orderBy($request->get('sort_by'), $sort_direction);
        }

        $adjustments = $adjustments->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['adjustments' => $adjustments];

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
            'date' => 'required|date|date_format:Y-m-d H:i:s',
            'reference_no' => 'nullable|string|max:255',
            'warehouse_id' => 'required|exists:App\Warehouse,id,business_id,' . $request->user()->id,
            'note' => 'nullable|string|max:255',
            'sku' => 'required|array',
            'sku.*.id' => 'required|exists:App\Sku,id,business_id,' . $request->user()->id,
            'sku.*.quantity' => 'required|integer|min:1',
            'sku.*.type' => 'required|in:addition,subtraction',
        ];

        $validator = Validator::make($request->all(), $validation, [
            'warehouse_id.exists' => 'The selected warehouse doesnt exists or doesnt belong to current user',
            'sku.*.id.exists' => 'The selected sku doesnt exists or doesnt belong to current user'
        ]);

        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }
        $genref = Settings::where('business_id', $request->user()->business_id)->first();

        $adjustment = $request->all();
        $adjustment['business_id'] = $request->user()->business_id;
        $adjustment['created_by'] = $request->user()->id;
        $adjustment['reference_no'] = ($request->reference_no) ? $request->reference_no : $genref->getReference_adj();

        $adjusment_created = Adjustment::create($adjustment);
        foreach($request->sku as $sku){
            $sku_details = Sku::find($sku['id']);
            if($sku){
                $item['adjustment_id'] = $adjusment_created->id;
                $item['sku_id'] = $sku_details->id;
                $item['quantity'] = $sku['quantity'];
                $item['type'] = $sku['type'];
                $item['sku_code'] = $sku_details->code;
                $item['sku_name'] = $sku_details->name;
                $item['image'] = $sku_details->products->first() ? $sku_details->products->first()->Images : asset('images/pages/no-img.jpg') ;
                $item['warehouse_id'] = $adjusment_created->warehouse_id;
                AdjustmentItems::insert($item);
            }
        }

        Adjustment::applyItemsOnWarehouse($adjusment_created->id);
        Sku::reSyncStocks($adjusment_created->items()->pluck('sku_id'));

        if (! $request->reference_no) {
            $increment = OrderRef::where('settings_id', $genref->id)->update(['adj' => DB::raw('adj + 1')]);
        }

        $items = $adjusment_created->items;
    
        $data = ['adjustment' => $adjusment_created];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Created')
                  ->build();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function show(Adjustment $adjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Adjustment $adjustment)
    {
        $validation = [
            'date' => 'required|date|date_format:Y-m-d H:i:s',
            'reference_no' => 'nullable|string|max:255',
            'warehouse_id' => 'required|exists:App\Warehouse,id,business_id,' . $request->user()->id,
            'note' => 'nullable|string|max:255',
            'sku' => 'required|array',
            'sku.*.id' => 'required|exists:App\Sku,id,business_id,' . $request->user()->id,
            'sku.*.quantity' => 'required|integer|min:1',
            'sku.*.type' => 'required|in:addition,subtraction',
        ];

        $validator = Validator::make($request->all(), $validation, [
            'warehouse_id.exists' => 'The selected warehouse doesnt exists or doesnt belong to current user',
            'sku.*.id.exists' => 'The selected sku doesnt exists or doesnt belong to current user'
        ]);

        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        $update_data = $request->only([
          'date', 'warehouse_id', 'note'
        ]);

        if ($request->reference_no) {
                $update_data['reference_no'] = $request->reference_no;
        }
        $adjustment->update($update_data);

        Adjustment::restoreItemsOnWarehouse($adjustment->id);
        Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));
        $adjustment->items()->delete();

        foreach($request->sku as $sku){
            $sku_details = Sku::find($sku['id']);
            if($sku){
                $item['adjustment_id'] = $adjustment->id;
                $item['sku_id'] = $sku_details->id;
                $item['quantity'] = $sku['quantity'];
                $item['type'] = $sku['type'];
                $item['sku_code'] = $sku_details->code;
                $item['sku_name'] = $sku_details->name;
                $item['image'] = $sku_details->products->first() ? $sku_details->products->first()->Images : asset('images/pages/no-img.jpg') ;
                $item['warehouse_id'] = $adjustment->warehouse_id;
                AdjustmentItems::insert($item);
            }
        }

        Adjustment::applyItemsOnWarehouse($adjustment->id);
        Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));

        $items = $adjustment->items;
    
        $data = ['adjustment' => $adjustment];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Updated')
                  ->build();


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Adjustment $adjustment, Request $request)
    {
      if($adjustment->business_id != $request->user()->business_id){
          return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Warehouse doesnt belong to current users business id')
                  ->build();
      }

      $adjustment->delete();

      $data = ['adjustment' => $adjustment];

      return ResponseBuilder::asSuccess(200)
                ->withData($data)
                ->withMessage('Deleted')
                ->build();
    }
}

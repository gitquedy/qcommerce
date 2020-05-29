<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Transfer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use App\Settings;
use App\Sku;
use App\TransferItems;
use App\OrderRef;
use DB;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class TransferController extends Controller
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

        $transfers = Transfer::with('items','from_warehouse','to_warehouse')->where('business_id',$user->business_id);

        if($request->get('id')){
            $transfers = $transfers->where('id', $request->get('id'));
        }

        if($request->get('created_from') && $request->get('created_to')){
            $transfers = $transfers->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $transfers = $transfers->orderBy($request->get('sort_by'), $sort_direction);
        }

        $transfers = $transfers->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['transfers' => $transfers];

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
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'from_warehouse_id' => 'required|different:to_warehouse_id|exists:App\Warehouse,id,business_id,' . $request->user()->id,
            'to_warehouse_id' => 'required|different:from_warehouse_id|exists:App\Warehouse,id,business_id,' . $request->user()->id,
            'status' => 'required|in:completed,pending,sent',
            'note' => 'nullable|string|max:255',
            'sku' => 'required|array',
            'sku.*.id' => 'required|exists:App\Sku,id,business_id,' . $request->user()->id,
            'sku.*.quantity' => 'required|integer|min:1',
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
        $transfer = $request->all();
        $transfer['business_id'] = $request->user()->business_id;
        $transfer['created_by'] = $request->user()->id;
        $transfer['reference_no'] = ($request->reference_no) ? $request->reference_no : $genref->getReference_tr();

        $transfer_created = Transfer::create($transfer);

        foreach($request->sku as $sku){
            $sku_details = Sku::find($sku['id']);
            if($sku){
                $item['transfer_id'] = $transfer_created->id;
                $item['sku_id'] = $sku_details->id;
                $item['quantity'] = $sku['quantity'];
                $item['sku_code'] = $sku_details->code;
                $item['sku_name'] = $sku_details->name;
                $item['image'] = $sku_details->products->first() ? $sku_details->products->first()->Images : asset('images/pages/no-img.jpg') ;
                $item['from_warehouse_id'] = $transfer_created->from_warehouse_id;
                $item['to_warehouse_id'] = $transfer_created->to_warehouse_id;
                TransferItems::insert($item);
            }
        }

        if(in_array($transfer_created->status, ['completed', 'sent'])) {
            //remove items from_warehouse
            Transfer::subtractItemsOnWarehouse($transfer_created->id);
            if(in_array($transfer_created->status, ['completed'])) {
                //add items to_warehouse
                Transfer::addItemsOnWarehouse($transfer_created->id);
            }
            Sku::reSyncStocks($transfer_created->items()->pluck('sku_id'));
        }

        if (!$request->reference_no) {
            $increment = OrderRef::where('settings_id', $genref->id)->update(['tr' => DB::raw('tr + 1')]);
        }

        $items = $transfer_created->items;

        $data = ['transfer' => $transfer_created];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Created')
                  ->build();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transfer $transfer)
    {
        $validation = [
            'date' => 'required|date|date_format:Y-m-d H:i:s',
            'reference_no' => 'nullable|string|max:255',
            'from_warehouse_id' => 'required|different:to_warehouse_id|exists:App\Warehouse,id,business_id,' . $request->user()->id,
            'to_warehouse_id' => 'required|different:from_warehouse_id|exists:App\Warehouse,id,business_id,' . $request->user()->id,
            'status' => 'required|in:completed,pending,sent',
            'note' => 'nullable|string|max:255',
            'sku' => 'required|array',
            'sku.*.id' => 'required|exists:App\Sku,id,business_id,' . $request->user()->id,
            'sku.*.quantity' => 'required|integer|min:1',
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


        if(in_array($transfer->status, ['completed', 'sent'])) {
            Transfer::addItemsOnWarehouse($transfer->id, true);
            if(in_array($transfer->status, ['completed'])) {
                Transfer::subtractItemsOnWarehouse($transfer->id, true);
            }
            Sku::reSyncStocks($transfer->items()->pluck('sku_id'));
            $transfer->items()->delete();
        }
        $update_data = $request->only([
          'date', 'note', 'from_warehouse_id', 'to_warehouse_id', 'status'
        ]);

        if ($request->reference_no) {
                $update_data['reference_no'] = $request->reference_no;
        }

        $transfer->update($update_data);

        foreach($request->sku as $sku){
            $sku_details = Sku::find($sku['id']);
            if($sku){
                $item['transfer_id'] = $transfer->id;
                $item['sku_id'] = $sku_details->id;
                $item['quantity'] = $sku['quantity'];
                $item['sku_code'] = $sku_details->code;
                $item['sku_name'] = $sku_details->name;
                $item['image'] = $sku_details->products->first() ? $sku_details->products->first()->Images : asset('images/pages/no-img.jpg') ;
                $item['from_warehouse_id'] = $transfer->from_warehouse_id;
                $item['to_warehouse_id'] = $transfer->to_warehouse_id;
                TransferItems::insert($item);
            }
        }

        if(in_array($transfer->status, ['completed', 'sent'])) {
            Transfer::subtractItemsOnWarehouse($transfer->id);
            if(in_array($transfer->status, ['completed'])) {
                Transfer::addItemsOnWarehouse($transfer->id);
            }
            Sku::reSyncStocks($transfer->items()->pluck('sku_id'));
        }

        $items = $transfer->items;

        $data = ['transfer' => $transfer];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Updated')
                  ->build();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer, Request $request)
    {
     if($transfer->business_id != $request->user()->business_id){
          return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Warehouse doesnt belong to current users business id')
                  ->build();
      }

      if(in_array($transfer->status, ['completed', 'sent'])) {
                Transfer::subtractItemsOnWarehouse($transfer->id, true);
                if(in_array($transfer->status, ['completed'])) {
                    Transfer::addItemsOnWarehouse($transfer->id, true);
                }
                Sku::reSyncStocks($transfer->items()->pluck('sku_id'));
            }
            $transfer->items()->delete();
            $transfer->delete();
      $data = ['transfer' => $transfer];

      return ResponseBuilder::asSuccess(200)
                ->withData($data)
                ->withMessage('Deleted')
                ->build();
    }
}

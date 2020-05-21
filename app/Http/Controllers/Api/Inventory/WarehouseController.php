<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Warehouse;
use Illuminate\Http\Request;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Carbon\Carbon;
use Validator;

class WarehouseController extends Controller
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

        $warehouses = Warehouse::where('business_id',$user->business_id);

        if($request->get('id')){
            $warehouses = $warehouses->where('id', $request->get('id'));
        }

        if($request->get('created_from') && $request->get('created_to')){
            $warehouses = $warehouses->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $warehouses = $warehouses->orderBy($request->get('sort_by'), $sort_direction);
        }

        $warehouses = $warehouses->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['warehouses' => $warehouses];

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
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|string|email|max:255',
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        $warehouse = $request->all();
        $warehouse['business_id'] = $request->user()->business_id;
        
        $warehouse_created = Warehouse::create($warehouse);

        $data = ['warehouses' => $warehouse_created];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Created')
                  ->build();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validation = [
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|string|email|max:255',
        ];

        $validator = Validator::make($request->all(), $validation);
        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        if($warehouse->business_id != $request->user()->business_id){
            return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Warehouse doesnt belong to current users business id')
                  ->build();
        }

        $warehouse->update($request->all());

        $data = ['warehouses' => $warehouse];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Updated')
                  ->build();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse, Request $request)
    {
        if($warehouse->business_id != $request->user()->business_id){
            return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Warehouse doesnt belong to current users business id')
                  ->build();
        }

        $warehouse->delete();

        $data = ['warehouses' => $warehouse];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Deleted')
                  ->build();
    }
}

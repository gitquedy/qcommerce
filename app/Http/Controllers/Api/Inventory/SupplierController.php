<?php

namespace App\Http\Controllers\Api\Inventory;

use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Supplier;
use Validator;

class SupplierController extends Controller
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

        $suppliers = Supplier::where('business_id',$user->business_id);

        if($request->get('id')){
            $suppliers = $suppliers->where('id', $request->get('id'));
        }

        if($request->get('created_from') && $request->get('created_to')){
            $suppliers = $suppliers->whereBetween('created_at', [$request->get('created_from'), $request->get('created_to')]);
        }

        if($request->get('sort_by')){
            $sort_direction = $request->get('sort_direction') ? $request->get('sort_direction') : 'desc' ;
            $suppliers = $suppliers->orderBy($request->get('sort_by'), $sort_direction);
        }

        $suppliers = $suppliers->paginate($request->get('per_page'))->jsonSerialize();
        $data = ['suppliers' => $suppliers];

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
            'company' => 'required',
            'contact_person' => 'required',
            'email' => 'nullable|unique:App\Supplier,email'
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        $data = $request->all();
        $data['business_id'] = $request->user()->business_id;

        $supplier_created = Supplier::create($data);

        $data = ['suppliers' => $supplier_created];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Created')
                  ->build();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validation = [
            'company' => 'required',
            'contact_person' => 'required',
            'email' => 'nullable|unique:App\Supplier,email,' . $supplier->id
        ];

        $validator = Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return ResponseBuilder::asError(422)
                  ->withHttpCode(422)
                  ->withDebugData(['error' => $validator->errors()->toArray()])
                  ->withMessage('Invalid Inputs')
                  ->build();
        }

        if($supplier->business_id != $request->user()->business_id){
            return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Sku doesnt belong to current users business id')
                  ->build();
        }

        $supplier_updated = Supplier::update($request->all());

        $data = ['suppliers' => $supplier_updated];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Updated')
                  ->build();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier, Request $request)
    {
        if($supplier->business_id != $request->user()->business_id){
            return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withMessage('Supplier doesnt belong to current users business id')
                  ->build();
        }

 
        $supplier->delete();

        $data = ['supplier' => $supplier];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('Deleted')
                  ->build();
    }
}

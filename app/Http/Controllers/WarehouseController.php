<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use PDF;
use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('WarehouseController@index'), 'name'=>"Warehouse"], ['name'=>"Warehouse List"]
        ];
        if ( request()->ajax()) {
            $user = Auth::user();
            $warehouse = $request->user()->business->warehouse()->orderBy('updated_at', 'desc');
            return Datatables($warehouse)
            ->addColumn('statusDisplay', function(Warehouse $warehouse) {
                return $warehouse->getStatusDisplay();
            })
            ->addColumn('action', function(Warehouse $warehouse) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'. action('WarehouseController@show', $warehouse->id) .'"><i class="fa fa-eye aria-hidden="true""></i> View</a>
                        <a class="dropdown-item" href="'. action('WarehouseController@printInventoryReport', $warehouse->id) .'"><i class="fa fa-print aria-hidden="true""></i> Print Delivery Receipt</a>
                        <a class="dropdown-item" href="'. action('WarehouseController@edit', $warehouse->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                        <a class="dropdown-item modal_button " href="#" data-href="'. action('WarehouseController@delete', $warehouse->id).'" ><i class="fa fa-trash aria-hidden="true""></i> Delete</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['action', 'statusDisplay'])
            ->make(true);
        }
        return view('warehouse.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Warehouse::class);
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('WarehouseController@index'), 'name'=>"Warehouses List"], ['name'=>"Add Warehouse"]
        ];
        return view('warehouse.create', compact('breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(),[
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['business_id'] = $user->business_id;

            $warehouse = Warehouse::create($data);

            $output = ['success' => 1,
                'msg' => 'Warehouse added successfully!',
                'redirect' => action('WarehouseController@index')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        if($warehouse->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to view this warehouse');
        }
        $this->authorize('show', $warehouse);
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('WarehouseController@index'), 'name'=>"Warehouses List"], ['name'=>"View Warehouse"]
        ];
        return view('warehouse.view', compact('breadcrumbs', 'warehouse'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function edit(Warehouse $warehouse)
    {
        if($warehouse->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this warehouse');
        }
        $this->authorize('edit', $warehouse);
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('WarehouseController@index'), 'name'=>"Warehouses List"], ['name'=>"Edit Warehouse"]
        ];
        return view('warehouse.edit', compact('warehouse', 'breadcrumbs'));
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
        if($warehouse->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this warehouse');
        }

       $validator = Validator::make($request->all(),[
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors','error' => $validator->errors()]);
        }
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['business_id'] = Auth::user()->business_id;
            $warehouse = $warehouse->update($data);

            $output = ['success' => 1,
                'msg' => 'Warehouse updated successfully!',
                'redirect' => action('WarehouseController@index')
            ];
            DB::commit();
          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        if($warehouse->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this warehouse');
        }
        try {
            DB::beginTransaction();
            $warehouse->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Warehouse successfully deleted!'
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function delete(Warehouse $warehouse, Request $request){
      if($warehouse->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this warehouse');
      }
        $action = action('WarehouseController@destroy', $warehouse->id);
        $title = 'warehouse ' . $warehouse->name;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function addWarehouseModal(Request $request) {
        $select_id = ($request->id)?$request->id:'select_warehouse';
        return view('warehouse.modal.addWarehouse', compact('select_id'));
    }

    public function addWarehouseAjax(Request $request) {
        $validator = Validator::make($request->all(),[
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable|string|email|max:255',
            'select_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            $data = new Warehouse;
            $data->code = $request->code;
            $data->name = $request->name;
            $data->address = $request->address;
            $data->phone = $request->phone;
            $data->email = $request->email;
            $data->business_id = $user->business_id;
            DB::beginTransaction();
            
            if ($data->save()) {
                $output = ['success' => 1,
                    'select_id' => $request->select_id,
                    'option_id' => $data->id,
                    'option_name' => $data->name,
                    'msg' => 'Warehouse added successfully!'
                ];
                DB::commit();
            }

          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }

    public function printInventoryReport($warehouse_id, Request $request) {
        $warehouse = Warehouse::findOrFail($warehouse_id);
        $company = $request->user()->business->company;
        return PDF::loadview('warehouse.inventoryreport', [
            'warehouse' => $warehouse,
            'company' => $company
        ])->stream();
    }
}

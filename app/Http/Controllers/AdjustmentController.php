<?php

namespace App\Http\Controllers;

use App\Adjustment;
use App\AdjustmentItems;
use App\Business;
use App\Imports\AdjustmentImport;
use App\OrderRef;
use App\Products;
use App\Sales;
use App\Settings;
use App\Shop;
use App\Sku;
use App\Warehouse;
use App\WarehouseItems;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Validator;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('AdjustmentController@index'), 'name'=>"Adjustment"], ['name'=>"Adjustment List"]
        ];
        if ( request()->ajax()) {
           $adjustment = Adjustment::orderBy('updated_at', 'desc');
            return Datatables($adjustment)
            ->addColumn('warehouse_name', function(Adjustment $adjustment) {
                return $adjustment->warehouse->name;
            })
            ->addColumn('created_by_name', function(Adjustment $adjustment) {
                return $adjustment->created_by_name->formatName();
            })
            ->addColumn('updated_by_name', function(Adjustment $adjustment) {
                if($adjustment->updated_by) {
                    return $adjustment->updated_by_name->formatName();
                }
                else{
                    return '--';
                }
            })
            ->addColumn('action', function(Adjustment $adjustment) {
                    $view = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('AdjustmentController@viewAdjustmentModal', $adjustment->id).'"><i class="fa fa-eye" aria-hidden="true"></i> View Adjustment</a>';

                    $edit = '<a class="dropdown-item" href="'. action('AdjustmentController@edit', $adjustment->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';

                    $delete = '<a class="dropdown-item modal_button" href="#" data-href="'. action('AdjustmentController@delete', $adjustment->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$view.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('adjustment.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('AdjustmentController@index'), 'name'=>"Adjustment List"], ['name'=>"Add Adjustment"]
        ];
        $warehouses = $request->user()->business->warehouse->where('status', 1);
        return view('adjustment.create', compact('breadcrumbs','warehouses'));
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
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'warehouse_id' => 'required',
            'note' => 'nullable|string|max:255',
            'adjustment_item_array' => 'required|array',
        ],
        [
            'adjustment_item_array.required' => 'Please add Items.',
        ]);
        if ($request->warehouse_id) {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);
        }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $warehouse = Warehouse::where('business_id', $user->business_id)->where('id', $request->warehouse_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $adjustment = new Adjustment;
            $adjustment->business_id = $user->business_id;
            $adjustment->date = date("Y-m-d H:i:s", strtotime($request->date));
            $adjustment->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_adj();
            $adjustment->warehouse_id = $request->warehouse_id;
            $adjustment->note = $request->note;
            $adjustment->created_by = $user->id;
            $adjustment->save();
            $adjustment_items = [];
            foreach ($request->adjustment_item_array as $item) {
                $adjustment_item = [];
                $adjustment_item['adjustment_id'] = $adjustment->id;
                $adjustment_item['sku_id'] = $item['sku_id'];
                $adjustment_item['sku_code'] = $item['code'];
                $adjustment_item['sku_name'] = $item['name'];
                $adjustment_item['image'] = $item['image'];
                $adjustment_item['quantity'] = $item['quantity'];
                $adjustment_item['warehouse_id'] = $adjustment->warehouse_id;
                $adjustment_item['type'] = $item['type'];
                $adjustment_items[] = $adjustment_item;
                $warehouse_item = WarehouseItems::where('warehouse_id', $adjustment->warehouse_id)->where('sku_id', $item['sku_id'])->first();
                $warehouse_qty = (isset($warehouse_item->quantity))?$warehouse_item->quantity:0;
                if ($item['type'] == "subtraction" && $item['quantity'] > $warehouse_qty) {
                    return response()->json(['msg' => 'Please check for errors' ,'error' => ['adjustment_item_array' => ['Insufficient warehouse quantity for '.$item['name'].' ['.$item['code'].']']]]);
                }
            }
            $adjustment_items_query = AdjustmentItems::insert($adjustment_items);
            Adjustment::applyItemsOnWarehouse($adjustment->id);
            Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));

            if (! $request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['adj' => DB::raw('adj + 1')]);
            }
            $output = ['success' => 1,
                'msg' => 'Adjustment added successfully!',
                'redirect' => action('AdjustmentController@index')
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
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function show(Adjustment $adjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(Adjustment $adjustment, Request $request)
    {
        if($adjustment->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this adjustment');
        }
        $warehouses = $request->user()->business->warehouse->where('status', 1);
        return view('adjustment.edit', compact('adjustment', 'warehouses'));
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
        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'note' => 'nullable|string|max:255',
            'adjustment_item_array' => 'required|array',
        ],
        [
            'adjustment_item_array.required' => 'Please add Items.',
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            foreach ($adjustment->items as $item) {
                $warehouse_qty = (isset($item->warehouse_item->quantity))?$item->warehouse_item->quantity:0;
                if ($item->type == "addition" && $item->quantity > $warehouse_qty) {
                    return response()->json(['msg' => 'Please check for errors' ,'error' => ['adjustment_item_array' => ['Insufficient warehouse quantity for "'.$item->sku->name.' ['.$item->sku->code.']" on warehouse '.$item->warehouse->name]]]);
                }
            }
            Adjustment::restoreItemsOnWarehouse($adjustment->id);
            // Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));
            $adjustment->items()->delete();
            $user = Auth::user();
            $warehouse = Warehouse::where('business_id', $user->business_id)->where('id', $request->warehouse_id)->first();
            $adjustment->date = date("Y-m-d H:i:s", strtotime($request->date));
            if ($request->reference_no) {
                $adjustment->reference_no = $request->reference_no;
            }
            $adjustment->warehouse_id = $request->warehouse_id;
            $adjustment->note = $request->note;
            $adjustment->updated_by = $user->id;
            $adjustment->save();
            $adjustment_items = [];
            foreach ($request->adjustment_item_array as $item) {
                $adjustment_item = [];
                $adjustment_item['adjustment_id'] = $adjustment->id;
                $adjustment_item['sku_id'] = $item['sku_id'];
                $adjustment_item['sku_code'] = $item['code'];
                $adjustment_item['sku_name'] = $item['name'];
                $adjustment_item['image'] = $item['image'];
                $adjustment_item['quantity'] = $item['quantity'];
                $adjustment_item['warehouse_id'] = $adjustment->warehouse_id;
                $adjustment_item['type'] = $item['type'];
                $adjustment_items[] = $adjustment_item;
                $warehouse_item = WarehouseItems::where('warehouse_id', $adjustment->warehouse_id)->where('sku_id', $item['sku_id'])->first();
                $warehouse_qty = (isset($warehouse_item->quantity))?$warehouse_item->quantity:0;
                if ($item['type'] == "subtraction" && $item['quantity'] > $warehouse_qty) {
                    return response()->json(['msg' => 'Please check for errors' ,'error' => ['adjustment_item_array' => ['Insufficient warehouse quantity for '.$item['name'].' ['.$item['code'].']']]]);
                }
            }

            
            $adjustment_items_query = AdjustmentItems::insert($adjustment_items);
            Adjustment::applyItemsOnWarehouse($adjustment->id);
            Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));

            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['adj' => DB::raw('adj + 1')]);
            }
            $output = ['success' => 1,
                'msg' => 'Adjustment updated successfully!',
                'redirect' => action('AdjustmentController@index')
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
     * @param  \App\Adjustment  $adjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Adjustment $adjustment)
    {
        if($adjustment->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this adjustment');
        }
        try {
            DB::beginTransaction();
            foreach ($adjustment->items as $item) {
                $warehouse_qty = (isset($item->warehouse_item->quantity))?$item->warehouse_item->quantity:0;
                if ($item->type == "addition" && $item->quantity > $warehouse_qty) {
                    return response()->json(['success' => 0,
                        'msg' => 'Insufficient warehouse quantity for "'.$item->sku->name.' ['.$item->sku->code.']" on warehouse '.$item->warehouse->name
                    ]);
                }
            }
            Adjustment::restoreItemsOnWarehouse($adjustment->id);
            Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));
            $adjustment->items()->delete();
            $adjustment->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Adjustment successfully deleted!'
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

    public function delete(Adjustment $adjustment, Request $request){
      if($adjustment->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this adjustment');
      }
        $action = action('AdjustmentController@destroy', $adjustment->id);
        $title = 'Adjustment ' . $adjustment->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function viewAdjustmentModal(Adjustment $adjustment, Request $request) {
        $business_id = Auth::user()->business_id;
        return view('adjustment.modal.viewAdjustment', compact('adjustment'));
    }

    public function import() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('AdjustmentController@index'), 'name'=>"Adjustment"], ['name'=>"Import Adjustment"]
        ];
        $warehouses = Warehouse::where('business_id', Auth::user()->business_id)->get();
        return view('adjustment.import', compact('breadcrumbs','warehouses'));
    }

    public function submitImport(Request $request) {
         $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'warehouse_id' => 'required',
            'note' => 'nullable|string|max:255',
        ]);
        if ($request->warehouse_id) {
            $warehouse = Warehouse::findOrFail($request->warehouse_id);
        }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $warehouse = Warehouse::where('business_id', $user->business_id)->where('id', $request->warehouse_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $adjustment = new Adjustment;
            $adjustment->business_id = $user->business_id;
            $adjustment->date = date("Y-m-d H:i:s", strtotime($request->date));
            $adjustment->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_adj();
            $adjustment->warehouse_id = $request->warehouse_id;
            $adjustment->note = $request->note;
            $adjustment->created_by = $user->id;
            $adjustment->save();

            $adjustment_items = [];
            $collection = Excel::toArray(new AdjustmentImport, request()->file('file'));
            foreach ($collection[0] as $import) {
                $Sku = Sku::where('code', $import['sku_code'])->first();
                if(!$Sku) {
                    return response()->json(['msg' => 'Please check for errors' ,'error' => ['adjustment_item_array' => ['Invalid SKU Code ['.$import['sku_code'].']']]]);
                }
                $products = Products::where('seller_sku_id', $Sku->id)->first();
                if($products){
                    $sku_image = $products->Images;
                }
                else {
                    $sku_image = asset('images/pages/no-img.jpg');
                }
                $adjustment_item = [];
                $adjustment_item['adjustment_id'] = $adjustment->id;
                $adjustment_item['sku_id'] = $Sku->id;
                $adjustment_item['sku_code'] = $Sku->code;
                $adjustment_item['sku_name'] = $Sku->name;
                $adjustment_item['image'] = $sku_image;
                $adjustment_item['quantity'] = $import['quantity'];
                $adjustment_item['warehouse_id'] = $adjustment->warehouse_id;
                $adjustment_item['type'] = $import['type'];
                $adjustment_items[] = $adjustment_item;
                $warehouse_item = WarehouseItems::where('warehouse_id', $adjustment->warehouse_id)->where('sku_id', $Sku->id)->first();
                $warehouse_qty = (isset($warehouse_item->quantity))?$warehouse_item->quantity:0;
                if ($import['type'] == "subtraction" && $import['quantity'] > $warehouse_qty) {
                    return response()->json(['msg' => 'Please check for errors' ,'error' => ['adjustment_item_array' => ['Insufficient warehouse quantity for '.$adjustment_item['name'].' ['.$adjustment_item['sku_code'].']']]]);
                }
                else if($import['type'] == "subtraction" && $import['quantity'] <= $warehouse_qty) {
                    $Sku->quantity -= $import['quantity'];
                    $warehouse_item->quantity -= $import['quantity'];
                    $warehouse_item->save();
                }
                else if($import['type'] == "addition") {
                    $Sku->quantity += $import['quantity'];
                    $warehouse_qty += $import['quantity'];
                    $warehouse_items = WarehouseItems::updateOrCreate(
                    ['warehouse_id' => $adjustment->warehouse_id, 'sku_id' => $Sku->id],
                    ['quantity' => $warehouse_qty]
                    );
                }
                $Sku->save();
            }
            $adjustment_items_query = AdjustmentItems::insert($adjustment_items);
            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['adj' => DB::raw('adj + 1')]);
            }
            Sku::reSyncStocks($adjustment->items()->pluck('sku_id'));
            $output = ['success' => 1,
                'msg' => 'Adjustment Imported successfully!',
                'redirect' => action('AdjustmentController@index')
            ];
            DB::commit();
          
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $msg = [];
            foreach ($failures as $failure) {
                 // $failure->row(); // row that went wrong
                 // $failure->attribute(); // either heading key (if using heading row concern) or column index
                 // $failure->errors(); // Actual error messages from Laravel validator
                 // $failure->values(); // The values of the row that has failed.
                 foreach ($failure->errors as $error) {
                    $msg[] = $error." Row ".$failure->row(); 
                 }
             }
            // \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' =>  $msg
                    ];
        }
        return response()->json($output);
    }
}

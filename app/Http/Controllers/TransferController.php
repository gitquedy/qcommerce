<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Business;
use App\Sku;
use App\Shop;
use App\Products;
use App\Transfer;
use App\TransferItems;
use App\Warehouse;
use App\WarehouseItems;
use App\OrderRef;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('TransferController@index'), 'name'=>"Transfer"], ['name'=>"Transfer List"]
        ];
        if ( request()->ajax()) {
           $transfer = Transfer::orderBy('updated_at', 'desc');
            return Datatables($transfer)
            ->editColumn('status', function(Transfer $transfer) {
                switch ($transfer->status) {
                    case 'completed':
                            return '<span class="badge badge-success">Complete</span>';
                        break;
                    case 'pending':
                            return '<span class="badge badge-secondary">Pending</span>';
                        break;
                    case 'sent':
                            return '<span class="badge badge-warning">Sent</span>';
                        break;
                    
                    default:
                            return '<span class="badge badge-primary">Unknown</span>';
                        break;
                }
                return $status;
            })
            ->addColumn('from_warehouse_name', function(Transfer $transfer) {
                return isset($transfer->from_warehouse->name)?$transfer->from_warehouse->name:'[Deleted Warehouse]';
            })
            ->addColumn('to_warehouse_name', function(Transfer $transfer) {
                return isset($transfer->to_warehouse->name)?$transfer->to_warehouse->name:'[Deleted Warehouse]';
            })
            ->addColumn('created_by_name', function(Transfer $transfer) {
                return $transfer->created_by_name->formatName();
            })
            ->addColumn('updated_by_name', function(Transfer $transfer) {
                if($transfer->updated_by) {
                    return $transfer->updated_by_name->formatName();
                }
                else{
                    return '--';
                }
            })
            ->addColumn('action', function(Transfer $transfer) {
                    $view = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('TransferController@viewTransferModal', $transfer->id).'"><i class="fa fa-eye" aria-hidden="true"></i> View Transfer</a>';

                    $edit = '<a class="dropdown-item" href="'. action('TransferController@edit', $transfer->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';

                    $delete = '<a class="dropdown-item modal_button" href="#" data-href="'. action('TransferController@delete', $transfer->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$view.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action', 'status'])
            ->make(true);
        }
        return view('transfer.index', [
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('TransferController@index'), 'name'=>"Transfer List"], ['name'=>"Add Transfer"]
        ];
        // $warehouses = $request->user()->business->warehouse;
        $warehouses = Warehouse::getAvailableWarehouses();
        return view('transfer.create', compact('breadcrumbs','warehouses'));
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
            'from_warehouse_id' => 'required|different:to_warehouse_id|exists:warehouses,id',
            'to_warehouse_id' => 'required|different:from_warehouse_id|exists:warehouses,id',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'transfer_item_array' => 'required|array',
        ],
        [
            'transfer_item_array.required' => 'Please add Items.',
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $transfer = new Transfer;
            $transfer->business_id = $user->business_id;
            $transfer->date = date("Y-m-d H:i:s", strtotime($request->date));
            $transfer->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_tr();
            $transfer->from_warehouse_id = $request->from_warehouse_id;
            $transfer->to_warehouse_id = $request->to_warehouse_id;
            $transfer->status = $request->status;
            $transfer->note = $request->note;
            $transfer->created_by = $user->id;
            $transfer->save();
            $transfer_items = [];
            foreach ($request->transfer_item_array as $id => $item) {
                $transfer_item = [];
                $transfer_item['transfer_id'] = $transfer->id;
                $transfer_item['sku_id'] = $id;
                $transfer_item['sku_code'] = $item['code'];
                $transfer_item['sku_name'] = $item['name'];
                $transfer_item['image'] = $item['image'];
                $transfer_item['quantity'] = $item['quantity'];
                $transfer_item['from_warehouse_id'] = $transfer->from_warehouse_id;
                $transfer_item['to_warehouse_id'] = $transfer->to_warehouse_id;
                $transfer_items[] = $transfer_item;
            }

            $transfer_items_query = TransferItems::insert($transfer_items);
            if(in_array($transfer->status, ['completed', 'sent'])) {
                //remove items from_warehouse
                Transfer::subtractItemsOnWarehouse($transfer->id);
                if(in_array($transfer->status, ['completed'])) {
                    //add items to_warehouse
                    Transfer::addItemsOnWarehouse($transfer->id);
                }
                Sku::reSyncStocks($transfer->items()->pluck('sku_id'));
            }

            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['tr' => DB::raw('tr + 1')]);
            }
            $output = ['success' => 1,
                'msg' => 'Transfer added successfully!',
                'redirect' => action('TransferController@index')
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
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function show(Transfer $transfer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function edit(Transfer $transfer, Request $request)
    {
        if($transfer->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this transfer');
        }
        // $warehouses = $request->user()->business->warehouse;
        $warehouses = Warehouse::getAvailableWarehouses();
        return view('transfer.edit', compact('transfer', 'warehouses'));
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
        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'required|string|max:255',
            'from_warehouse_id' => 'required|different:to_warehouse_id|exists:warehouses,id',
            'to_warehouse_id' => 'required|different:from_warehouse_id|exists:warehouses,id',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'transfer_item_array' => 'required|array',
        ],
        [
            'transfer_item_array.required' => 'Please add Items.',
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            if(in_array($transfer->status, ['completed', 'sent'])) {
                Transfer::addItemsOnWarehouse($transfer->id, true);
                if(in_array($transfer->status, ['completed'])) {
                    foreach ($transfer->items as $item) {
                        $warehouse_qty = (isset($item->to_warehouse_item->quantity))?$item->to_warehouse_item->quantity:0;
                        if ($item->quantity > $warehouse_qty) {
                            return response()->json(['msg' => 'Please check for errors' ,'error' => ['transfer_item_array' => ['Insufficient warehouse quantity for "'.$item->sku->name.' ['.$item->sku->code.']" on warehouse '.$item->to_warehouse->name]]]);
                        }
                    }
                    Transfer::subtractItemsOnWarehouse($transfer->id, true);
                }
                Sku::reSyncStocks($transfer->items()->pluck('sku_id'));
            }
            $transfer->items()->delete();
            $user = Auth::user();
            $transfer->date = date("Y-m-d H:i:s", strtotime($request->date));
            if ($request->reference_no) {
                $transfer->reference_no = $request->reference_no;
            }
            $transfer->from_warehouse_id = $request->from_warehouse_id;
            $transfer->to_warehouse_id = $request->to_warehouse_id;
            $transfer->status = $request->status;
            $transfer->note = $request->note;
            $transfer->updated_by = $user->id;
            $transfer->save();
            $transfer_items = [];
            foreach ($request->transfer_item_array as $id => $item) {
                $transfer_item = [];
                $transfer_item['transfer_id'] = $transfer->id;
                $transfer_item['sku_id'] = $id;
                $transfer_item['sku_code'] = $item['code'];
                $transfer_item['sku_name'] = $item['name'];
                $transfer_item['image'] = $item['image'];
                $transfer_item['quantity'] = $item['quantity'];
                $transfer_item['from_warehouse_id'] = $transfer->from_warehouse_id;
                $transfer_item['to_warehouse_id'] = $transfer->to_warehouse_id;
                $transfer_items[] = $transfer_item;
                if(in_array($transfer->status, ['completed', 'sent'])) {
                    $warehouse_item = WarehouseItems::where('warehouse_id', $transfer->from_warehouse_id)->where('sku_id', $id)->first();
                    $warehouse_qty = (isset($warehouse_item->quantity))?$warehouse_item->quantity:0;
                    if ($item['quantity'] > $warehouse_qty) {
                        return response()->json(['msg' => 'Please check for errors' ,'error' => ['transfer_item_array' => ['Insufficient warehouse quantity for '.$item['name'].' ['.$item['code'].']']]]);
                    }
                }
            }
            
            $transfer_items_query = TransferItems::insert($transfer_items);
            if(in_array($transfer->status, ['completed', 'sent'])) {
                Transfer::subtractItemsOnWarehouse($transfer->id);
                if(in_array($transfer->status, ['completed'])) {
                    Transfer::addItemsOnWarehouse($transfer->id);
                }
                Sku::reSyncStocks($transfer->items()->pluck('sku_id'));
            }

            $output = ['success' => 1,
                'msg' => 'Transfer updated successfully!',
                'redirect' => action('TransferController@index')
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
     * @param  \App\Transfer  $transfer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transfer $transfer)
    {
        if($transfer->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this transfer');
        }
        try {
            DB::beginTransaction();
            foreach ($transfer->items as $item) {
                $warehouse_qty = (isset($item->to_warehouse_item->quantity))?$item->to_warehouse_item->quantity:0;
                if ($item->quantity > $warehouse_qty) {
                    return response()->json(['success' => 0,'msg' => 'Insufficient warehouse quantity for "'.$item->sku->name.' ['.$item->sku->code.']" on warehouse '.$item->to_warehouse->name]);
                }
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
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Transfer successfully deleted!'
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

    public function delete(Transfer $transfer, Request $request){
      if($transfer->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this transfer');
      }
        $action = action('TransferController@destroy', $transfer->id);
        $title = 'Transfer ' . $transfer->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function viewTransferModal(Transfer $transfer, Request $request) {
        $business_id = Auth::user()->business_id;
        return view('transfer.modal.viewTransfer', compact('transfer'));
    }
}

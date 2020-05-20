<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Shop;
use App\Products;
use App\Warehouse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ShopManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\ShopManagementController@index'), 'name'=>"Shop List"], ['name'=>"Shops"]
        ];
        if ( request()->ajax()) {
           $shop = Shop::orderBy('shop.updated_at', 'desc');
            return Datatables::eloquent($shop)
            ->editColumn('site', function(Shop $shop) {
                            return '<img src="'.asset('images/shop/icon/'.$shop->site.'.png').'" style="display:block; width:100%; height:auto;">';
                        })
            ->addColumn('warehouse_name', function(Shop $shop) {
                return isset($shop->warehouse->name)?$shop->warehouse->name:'[Deleted Warehouse]';
            })
            ->addColumn('statusChip', function(Shop $shop) {
                            $html = '';
                            if($shop->active == 1){
                                $html = '<div class="chip chip-primary"><div class="chip-body"><div class="chip-text">Active</div></div></div>';
                            }else if($shop->active == 2){
                                $html = '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">Syncing</div></div></div>';
                            }
                           return $html;
                        })
            ->addColumn('orders', function(Shop $shop) {
                            $html = $shop->orders()->whereDate('created_at','=',date('Y-m-d'))->count();
                            $html = '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">'.$html.'</div></div></div>';
                           return $html;
                        })
            ->addColumn('pending_count', function(Shop $shop) {
                           return '<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('pending')->count().'</div></div></div>';
                        })
            ->addColumn('ready_to_ship_count', function(Shop $shop) {
                           return '<div class="chip chip-warning"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('ready_to_ship')->count().'</div></div></div>';
                        })
            ->addColumn('shipped_count', function(Shop $shop) {
                           return '<div class="chip chip-success"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('shipped')->count().'</div></div></div>';
                        })
            ->addColumn('delivered_count', function(Shop $shop) {
                           return '<div class="chip chip-success"><div class="chip-body"><div class="chip-text">'.
                        $shop->orders('delivered')->count().'</div></div></div>';
                        })
            ->addColumn('products', function(Shop $shop) {
                           $product_count =  Products::where('shop_id','=',$shop->id)->get()->count();
                           return '<div class="chip chip-info"><div class="chip-body"><div class="chip-text">'.$product_count.'</div></div></div>';
                        })
            ->addColumn('action', function(Shop $shop) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item modal_button" href="#" data-href="'. action('Admin\ShopManagementController@edit', $shop->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['site', 'shipped_count', 'pending_count', 'ready_to_ship_count', 'delivered_count', 'statusChip','orders','products', 'action'])
            ->make(true);
        }
        return view('admin.shop.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function edit(Shop $manageshop)
    {
        $shop = $manageshop;
        $warehouses = Warehouse::where('business_id', $shop->business_id)->get();
        return view('admin.shop.edit', compact('shop', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $manageshop)
    {
        $validator = Validator::make($request->all(), [
                'name' => ['required', 'regex:/^[\pL\s\-]+$/u'],
                'short_name' => 'required',
                'warehouse_id' => 'required'
            ], 
        ['name.regex' => 'Only character\'s are allowed']);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors','error' => $validator->errors()]);
        }
        try {
            $manageshop->update($request->only(['short_name', 'name', 'warehouse_id']));

            $output = ['success' => 1,
                'msg' => 'Shop updated successfully!',
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
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $manageshop)
    {
        //
    }
}

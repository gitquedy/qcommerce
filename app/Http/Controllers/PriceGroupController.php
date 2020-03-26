<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\PriceGroup;
use App\PriceGroupItemPrice;
use App\Sku;
use App\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"Settings"], ['name'=>"Price Group"]
        ];
        if ( request()->ajax()) {
            $user = Auth::user();
            $price_group = PriceGroup::where('business_id', $user->business_id)->orderBy('updated_at', 'desc');
           // return $price_group->get();
            return Datatables($price_group)
            ->addColumn('action', function(PriceGroup $price_group) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'. action('PriceGroupController@edit', $price_group->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                        <a class="dropdown-item modal_button " href="#" data-href="'. action('PriceGroupController@delete', $price_group->id).'" ><i class="fa fa-trash aria-hidden="true""></i> Delete</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('price_group.index', [
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PriceGroupController@index'), 'name'=>"Price Group"], ['name'=>"Add Price Group"]
        ];
        $sku = Sku::where('business_id', Auth::user()->business_id)->orderBy('updated_at', 'desc')->get();
        foreach ($sku as &$s) {
          $products = Products::where('seller_sku_id', $s->id)->first();
            if($products){
               $s->image = $products->Images;
            }
            else {
                $s->image = asset('images/pages/no-img.jpg');
            }
        }
        // print json_encode($sku);die();
        return view('price_group.create', compact('breadcrumbs', 'sku'));
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
            'name' => 'required',
            'item_array' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();

            $price_group = new PriceGroup;
            $price_group->business_id = $user->business_id;
            $price_group->name = $request->name;
            $price_group->save();
            $price_group_items = [];
            foreach ($request->item_array as $id => $item) {
                $price_group_item = [];
                $price_group_item['price_group_id'] = $price_group->id;
                $price_group_item['sku_id'] = $id;
                $price_group_item['price'] = $item['price'];
                $price_group_items[] = $price_group_item;
            }
            $price_group_items_query = PriceGroupItemPrice::insert($price_group_items);
            $output = ['success' => 1,
                'msg' => 'Price Group added successfully!',
                'redirect' => action('PriceGroupController@index')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PriceGroup $price_group)
    {
        if($price_group->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this price group');
        }
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CustomerController@index'), 'name'=>"Price Group List"], ['name'=>"Edit Price Group"]
        ];
        $sku = Sku::where('business_id', Auth::user()->business_id)->orderBy('updated_at', 'desc')->get();
        foreach ($sku as &$s) {
            $products = Products::where('seller_sku_id', $s->id)->first();
            if($products){
               $s->image = $products->Images;
            }
            else {
                $s->image = asset('images/pages/no-img.jpg');
            }
            $price_group_item = PriceGroupItemPrice::where('price_group_id', $price_group->id)->where('sku_id', $s->id)->first();
            if($price_group_item) {
                $s->pg_price = $price_group_item->price;
            }
        }
        // print json_encode($sku);die();
        return view('price_group.edit', compact('breadcrumbs', 'price_group', 'sku'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PriceGroup $price_group, Request $request)
    {
         $validator = Validator::make($request->all(),[
            'name' => 'required',
            'item_array' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $price_group->name = $request->name;
            $price_group->save();

            PriceGroupItemPrice::where('price_group_id', $price_group->id)->delete();

            $price_group_items = [];
            foreach ($request->item_array as $id => $item) {
                $price_group_item = [];
                $price_group_item['price_group_id'] = $price_group->id;
                $price_group_item['sku_id'] = $id;
                $price_group_item['price'] = $item['price'];
                $price_group_items[] = $price_group_item;
            }
            $price_group_items_query = PriceGroupItemPrice::insert($price_group_items);
            $output = ['success' => 1,
                'msg' => 'Price Group added successfully!',
                'redirect' => action('PriceGroupController@index')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $price_group = PriceGroup::findOrFail($id);
        if($price_group->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this price group!');
        }
        try {
            DB::beginTransaction();
            $price_group->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Price group successfully deleted!'
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


    
    public function delete(PriceGroup $price_group, Request $request){
      if($price_group->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this price group');
      }
        $action = action('PriceGroupController@destroy', $price_group->id);
        $title = 'Price Group ' . $price_group->name;
        return view('layouts.delete', compact('action' , 'title'));
    }

}

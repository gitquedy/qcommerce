<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Business;
use App\Category;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Imports\SkuImport;
use App\Lazop;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\UrlConstants;
use App\PriceGroupItemPrice;
use App\Products;
use App\Plan;
use App\Shop;
use App\Sku;
use App\SetItem;
use App\Supplier;
use App\Utilities;
use App\WarehouseItems;
use Auth;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Validator;
use Yajra\DataTables\Facades\DataTables;

class SkuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function index(Request $request)
    {        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"List of SKU"]
        ];
        
        if (request()->ajax()) {
            
            $business_id = Auth::user()->business_id;
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            $Sku = Sku::where('business_id','=',$business_id)
                        ->leftjoin('products', 'sku.id', '=', 'products.seller_sku_id')
                        ->select('sku.id', 'sku.business_id','sku.code', 'sku.name', 'sku.brand', 'sku.category', 'sku.supplier', 'sku.cost', 'sku.price', 'sku.quantity', 'sku.alert_quantity', 'sku.type', 'sku.created_at', 'sku.updated_at', DB::raw('GROUP_CONCAT(products.shop_id)'))
                        ->groupBy('sku.id', 'sku.business_id','sku.code', 'sku.name', 'sku.brand', 'sku.category', 'sku.supplier', 'sku.cost', 'sku.price' , 'sku.quantity', 'sku.alert_quantity', 'sku.type', 'sku.created_at', 'sku.updated_at');

            return Datatables::eloquent($Sku)
            ->editColumn('link_shop', function(Sku $SKSU) {
                            $shop_list = array();
                            $SKSU->products->map( function($prod) use (&$shop_list) { $shop_list[] = '<span class="badge btn-outline-primary text-black font-weight-bold">'.$prod->shop->short_name.'</span>'; } );
                            return implode(' ', $shop_list);
                        })
            ->editColumn('cost', function(Sku $SKSU) {
                            return "<p>".$SKSU->cost.'</p><input type="number" min="0" class="form-control" data-defval="'.$SKSU->cost.'" data-name="cost" value="'.$SKSU->cost.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('price', function(Sku $SKSU) {
                            return "<p>".$SKSU->price.'</p><input type="number" min="1" class="form-control" data-defval="'.$SKSU->price.'" data-name="price" value="'.$SKSU->price.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('quantity', function(Sku $SKSU) {
                            $warehouse = request()->warehouse;
                            if($warehouse != "") {
                                $warehouse = $SKSU->warehouse_items()->where('warehouse_id', $warehouse)->first();
                                if($warehouse) {
                                    return '<p>'.$warehouse->quantity.'</p>';
                                }
                                else {
                                    return '<p>0</p>';
                                }
                            }
                            else {
                                return '<p>'.$SKSU->quantity.'</p>';
                            }


                        })
            ->editColumn('alert_quantity', function(Sku $SKSU) {
                            return "<p>".$SKSU->alert_quantity.'</p><input type="number" class="form-control" data-defval="'.$SKSU->alert_quantity.'" data-name="alert_quantity" value="'.$SKSU->alert_quantity.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('type', function(Sku $SKSU) {
                return '<p>'.ucfirst($SKSU->type).'</p>';
            })
            ->addColumn('category_name', function(Sku $SKSU) {
                            $category = Category::find($SKSU->category);
                            if($category){
                               return  $category->name;
                            }
                        })
            ->addColumn('supplier_company', function(Sku $SKSU) {
                            $supplier = Supplier::find($SKSU->supplier);
                            if($supplier){
                               return  $supplier->company;
                            }
                        })
            ->addColumn('image', function(Sku $SKSU) {
                            return $SKSU->SkuImage();
                        })
            ->addColumn('products_count', function(Sku $SKSU) {
                            return Products::where('seller_sku_id', $SKSU->id)->get()->count();
                        })
            ->addColumn('brand_name', function(Sku $SKSU) {
                            $Brand = Brand::find($SKSU->brand);
                            if($Brand){
                               return  $Brand->name;
                            }
                            else {
                                return "";
                            }
                        })
            ->addColumn('action', function(Sku $SKSU) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                            Action<span class="sr-only">Toggle Dropdown</span></button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item fa fa-link" href="'.route('sku.skuproducts',$SKSU->id).'" > Link SKU Products</a>
                            <a class="dropdown-item fa fa-edit" href="'.route('sku.edit',['id'=>$SKSU->id]).'" > Edit</a>
                            <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $SKSU->name .' ?" data-text="This Action is irreversible." data-href="'.route('sku.delete',['id'=>$SKSU->id]).'" > Delete</a>
                            </div>
                            </div>';
                        })
            ->rawColumns(['link_shop','cost','price','quantity','alert_quantity','type','action'])
            ->make(true);
        }
        // $business_id = Auth::user()->business_id;
        // $all_warehouse = Business::find($business_id)->warehouse;
        $user = Auth::user();
        if ($user->business->subscription() !== null) {
            $all_warehouse = $user->business->warehouse()->orderBy('created_at', 'asc')->take($user->business->subscription()->plan->no_of_warehouse)->get();
        }
        else {
            $all_warehouse = $user->business->warehouse()->orderBy('created_at', 'asc')->take(Plan::whereId(1)->value('no_of_warehouse'))->get();
        }
        return view('sku.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_warehouse' => $all_warehouse,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }


    public function unlink(Request $request) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"Unlink SKU"]
        ];
        
        if (request()->ajax()) {
            
            $business_id = Auth::user()->business_id;
            
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            $Sku = Sku::where('business_id','=',$business_id)
                        ->leftjoin('products', 'sku.id', '=', 'products.seller_sku_id')
                        ->select('sku.id', 'sku.business_id','sku.code', 'sku.name', 'sku.brand', 'sku.category', 'sku.supplier', 'sku.cost', 'sku.price', 'sku.quantity', 'sku.alert_quantity', 'sku.type', 'sku.created_at', 'sku.updated_at', DB::raw('GROUP_CONCAT(products.shop_id) as shop_id'))
                        ->groupBy('sku.id', 'sku.business_id','sku.code', 'sku.name', 'sku.brand', 'sku.category', 'sku.supplier', 'sku.cost', 'sku.price' , 'sku.quantity', 'sku.alert_quantity', 'sku.type', 'sku.created_at', 'sku.updated_at')
                        ->havingRaw('shop_id is null');

            return Datatables::eloquent($Sku)
            ->editColumn('link_shop', function(Sku $SKSU) {
                            $shop_list = array();
                            $SKSU->products->map( function($prod) use (&$shop_list) { $shop_list[] = '<span class="badge btn-outline-primary text-black font-weight-bold">'.$prod->shop->short_name.'</span>'; } );
                            return implode(' ', $shop_list);
                        })
            ->editColumn('cost', function(Sku $SKSU) {
                            return "<p>".$SKSU->cost.'</p><input type="number" min="0" class="form-control" data-defval="'.$SKSU->cost.'" data-name="cost" value="'.$SKSU->cost.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('price', function(Sku $SKSU) {
                            return "<p>".$SKSU->price.'</p><input type="number" min="1" class="form-control" data-defval="'.$SKSU->price.'" data-name="price" value="'.$SKSU->price.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('quantity', function(Sku $SKSU) {
                            $warehouse = request()->warehouse;
                            if($warehouse != "") {
                                $warehouse = $SKSU->warehouse_items()->where('warehouse_id', $warehouse)->first();
                                if($warehouse) {
                                    return '<p>'.$warehouse->quantity.'</p>';
                                }
                                else {
                                    return '<p>0</p>';
                                }
                            }
                            else {
                                return '<p>'.$SKSU->quantity.'</p>';
                            }


                        })
            ->editColumn('alert_quantity', function(Sku $SKSU) {
                            return "<p>".$SKSU->alert_quantity.'</p><input type="number" class="form-control" data-defval="'.$SKSU->alert_quantity.'" data-name="alert_quantity" value="'.$SKSU->alert_quantity.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('type', function(Sku $SKSU) {
                return '<p>'.ucfirst($SKSU->type).'</p>';
            })
            ->addColumn('category_name', function(Sku $SKSU) {
                            $category = Category::find($SKSU->category);
                            if($category){
                               return  $category->name;
                            }
                        })
            ->addColumn('supplier_company', function(Sku $SKSU) {
                            $supplier = Supplier::find($SKSU->supplier);
                            if($supplier){
                               return  $supplier->company;
                            }
                        })
            ->addColumn('image', function(Sku $SKSU) {
                            return $SKSU->SkuImage();
                        })
            ->addColumn('products_count', function(Sku $SKSU) {
                            return Products::where('seller_sku_id', $SKSU->id)->get()->count();
                        })
            ->addColumn('brand_name', function(Sku $SKSU) {
                            $Brand = Brand::find($SKSU->brand);
                            if($Brand){
                               return  $Brand->name;
                            }
                            else {
                                return "";
                            }
                        })
            ->addColumn('action', function(Sku $SKSU) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                            Action<span class="sr-only">Toggle Dropdown</span></button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item fa fa-link" href="'.route('sku.skuproducts',$SKSU->id).'" > Link SKU Products</a>
                            <a class="dropdown-item fa fa-edit" href="'.route('sku.edit',['id'=>$SKSU->id]).'" > Edit</a>
                            <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $SKSU->name .' ?" data-text="This Action is irreversible." data-href="'.route('sku.delete',['id'=>$SKSU->id]).'" > Delete</a>
                            </div>
                            </div>';
                        })
            ->rawColumns(['link_shop','cost','price','quantity','alert_quantity','type','action'])
            ->make(true);
        }
        // $business_id = Auth::user()->business_id;
        // $all_warehouse = Business::find($business_id)->warehouse;
        $user = Auth::user();
        if ($user->business->subscription() !== null) {
            $all_warehouse = $user->business->warehouse()->orderBy('created_at', 'asc')->take($user->business->subscription()->plan->no_of_warehouse)->get();
        }
        else {
            $all_warehouse = $user->business->warehouse()->orderBy('created_at', 'asc')->take(Plan::whereId(1)->value('no_of_warehouse'))->get();
        }
        return view('sku.unlink', [
            'breadcrumbs' => $breadcrumbs,
            'all_warehouse' => $all_warehouse,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }
    
    
    public function create(Request $request){
        $user = Auth::user();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"SKU  Create"]
        ];
        
        // $Category = Category::auth_category();
        // $Brand = Brand::auth_brand();
        $Supplier = Supplier::auth_supplier();
        $all_skus = Sku::where('business_id', $request->user()->business_id)->where('type', 'single')->orderBy('updated_at', 'desc')->get();
        
        return view('sku.create', [
            'breadcrumbs' => $breadcrumbs,
            // 'Category'=> $Category,
            // 'Brand'=> $Brand,
            'Supplier' => $Supplier
            ],
            compact ('all_skus')
        );
        
    }
    
    
    public function add(Request $request){
        $request->validate([
            'code' => 'required|unique:sku,code,NULL,id,business_id,'.Auth::user()->business_id,
            'name' => 'required',
            'brand' => 'nullable',
            'category' => 'nullable',
            'supplier' => 'nullable',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'alert_quantity' => 'required|numeric',
            'type' => 'required',
        ]);
        if ($request->type == 'set') {
            $request->validate([
                'sku_name' => 'required|array',
                'sku_name.*' => 'required',
                'set_quantity' => 'required|array',
                'set_quantity.*' => 'required|numeric',
            ]);
        }
        // if ($validator->fails()) {
        //     return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        // }
        $sku = new Sku();
        $sku->business_id = Auth::user()->business_id;
        $sku->code = $request->code;
        $sku->name = $request->name;
        // $sku->brand = $request->brand;
        // $sku->category = $request->category;
        $sku->supplier = $request->suppler;
        $sku->cost = $request->cost;
        $sku->price = $request->price;
        $sku->quantity = 0;
        $sku->alert_quantity = $request->alert_quantity;
        $sku->type = $request->type;
        
        if($sku->save()){
            $request->session()->flash('flash_success', 'Successfully added SKU. Next step is to LINK your shop listing to your SKU');
        }else{
            $request->session()->flash('flash_error',"something Went wrong !");
        }

        //code for adding products in set during sku creation
        if ($request->type == 'set') {
            $quantity_array = array();
            $sku_of_set = Sku::find($sku->id);
            for ($index = 0; $index < count($request->sku_id); $index++) {
                $setitem = new SetItem();
                $item = Sku::where('business_id','=', $sku->business_id)->where('id','=',$request->sku_id[$index])->first();
                $setitem->sku_set_id = $sku->id;
                $setitem->sku_single_id = $request->sku_id[$index];
                $setitem->code = $item->code;
                $setitem->name = $item->name;
                $setitem->unit_price = $item->price;
                $setitem->set_quantity = $request->set_quantity[$index];
                $setitem->save();

                $sku_of_setitem = Sku::find($request->sku_id[$index]);
                $quantity_array[] = (int)($sku_of_setitem->quantity / $request->set_quantity[$index]);
            }
            $quantity_of_set = min($quantity_array);
            $sku_of_set->quantity = $quantity_of_set;
            $sku_of_set->save();
        }

        return redirect('/sku/skuproducts/'.$sku->id);
        
    }
    
    
    public function edit($id="",Request $request){
        
        $business_id = Auth::user()->business_id;
        
        $Sku_check = Sku::where('business_id','=',$business_id)->where('id','=',$id)->get()->count();
        
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"SKU  Create"]
        ];
        
        $Sku = Sku::find($id);
        $all_skus = Sku::with('set_items')->where('business_id', $request->user()->business_id)->where('type', 'single')->orderBy('updated_at', 'desc')->get();
        
        // $Category = Category::auth_category();
        // $Brand = Brand::auth_brand();
        $Supplier = Supplier::auth_supplier();

        
        return view('sku.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Sku'=> $Sku,
            // 'Category'=> $Category,
            // 'Brand'=> $Brand,
            'Supplier' => $Supplier
            ],
            compact ('all_skus')
        );
        
    }
    
    
    public function update(Request $request){
        $business_id = Auth::user()->business_id;
        $Sku_check = Sku::where('business_id','=',$business_id)->where('id','=',$request->id)->get()->count();
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }
        $request->validate([
            'code' => 'required|unique:sku,code,'.$request->id.',id,business_id,'.Auth::user()->business_id,
            'name' => 'required',
            // 'brand' => 'nullable',
            // 'category' => 'nullable',
            'supplier' => 'nullable',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'alert_quantity' => 'required|numeric',
            'type' => 'required',
        ]);
        if ($request->type == 'set') {
            $request->validate([
                'sku_name' => 'required|array',
                'sku_name.*' => 'required',
                'set_quantity' => 'required|array',
                'set_quantity.*' => 'required|numeric',
            ]);
        }
        $sku = Sku::find($request->id);
        $sku->code = $request->code;
        $sku->name = $request->name;
        // $sku->brand = $request->brand;
        // $sku->category = $request->category;
        $sku->supplier = $request->supplier;
        $sku->cost = $request->cost;
        $sku->price = $request->price;
        $sku->alert_quantity = $request->alert_quantity;
        $sku->type = $request->type;

        if ($request->type == 'set') {
            $quantity_array = array();
            for ($index = 0; $index < count($request->sku_id); $index++) {
                $item = Sku::where('business_id','=', $sku->business_id)->where('id','=',$request->sku_id[$index])->first();
                $data = [
                    'sku_set_id' => $sku->id,
                    'sku_single_id' => $request->sku_id[$index],
                    'code' => $item->code,
                    'name' => $item->name,
                    'unit_price' => $item->price,
                    'set_quantity' => $request->set_quantity[$index],
                ];
                SetItem::updateOrCreate(['sku_set_id' => $sku->id, 'sku_single_id' => $request->sku_id[$index]], $data);
                
                $sku_of_setitem = Sku::find($request->sku_id[$index]);
                $quantity_array[] = (int)($sku_of_setitem->quantity / $request->set_quantity[$index]);
            }
            $quantity_of_set = min($quantity_array);
            $sku->quantity = $quantity_of_set;
            foreach ($sku->set_items as $item) {
                if (!in_array($item->sku_single_id, $request->sku_id)) {
                    $item->delete();
                }
            }
        }
        
        if($sku->save()){
            foreach ($sku->products as $product) {
                if ($sku->type == 'single') {
                    $data = [
                        'seller_sku_id' => $sku->id,
                        'price' => $sku->price,
                        // 'SellerSku' => $sku->code,
                        'quantity' => $sku->quantity
                    ];
    
                    $result = $product->update($data);
                    $product->update(['quantity' => $product->getWarehouseQuantity()]);
                }
                else if ($sku->type == 'set') {
                    $sku_set_quantity = $sku->computeSetQuantity($product->shop->warehouse_id);

                    $data = [
                        'seller_sku_id' => $sku->id,
                        'price' => $sku->price,
                        // 'SellerSku' => $sku->code,
                        // 'quantity' => $product->getWarehouseQuantity()
                        'quantity' => $sku_set_quantity
                    ];

                    $result = $product->update($data);
                }

                $response = $product->updatePlatform();
            }
            $request->session()->flash('flash_success', 'Success !');
        }else{
            $request->session()->flash('flash_error',"something Went wrong !");
        }
        return redirect('/sku');
        
    }
    
    public function quickUpdate(Request $request){
        $business_id = Auth::user()->business_id;
        $all_shops = Shop::where('business_id', $request->user()->business_id)->orderBy('updated_at', 'desc')->get();
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }
        $column = $request->name;
        $sku = Sku::where('business_id','=', $business_id)->where('id','=',$request->sku)->first();
        if($sku && $column != 'quantity'){  //quantity is dependent on warehouse
            $sku->$column = $request->val;
            $result = $sku->save();

            if(in_array($column, array('price'))) {
                
                foreach ($sku->products as $product) {
                    $data = [
                        'seller_sku_id' => $sku->id,
                        'price' => $sku->price,
                        // 'SellerSku' => $sku->code,
                        'quantity' => $sku->quantity
                    ];  

                    $result = $product->update($data);

                    $response = $product->updatePlatform();
                }
            }
        }
        else {
            $result = false;
        }
        echo json_encode($result);
    }

    public function syncSkuProducts(Request $request) { //action for checkbox on sku table
        $business_id = Auth::user()->business_id;
        $sku = Sku::where('business_id','=',$business_id)->whereIn('id', $request->ids)->get();
        $all_shops = Shop::where('business_id', $business_id)->orderBy('updated_at', 'desc')->get();
        $Shop_array = array();
        foreach($all_shops as $all_shopsVAL){
            $Shop_array[] = $all_shopsVAL->id;
        }

        foreach($sku as $sku) {
            foreach ($sku->products as $product) {
                $data = [
                    'seller_sku_id' => $sku->id,
                    'price' => $sku->price,
                    // 'SellerSku' => $sku->code,
                    'quantity' => $product->getWarehouseQuantity()
                ];

                $result = $product->update($data);

                $response = $product->updatePlatform();
            }
        }
        $result = array('success' => true, 'msg' => 'Product Syncs Successfully.');
        return $result;
    }

    public function skuproducts(Sku $sku,Request $request){
        
        $all_shops = Shop::where('business_id', $request->user()->business_id)->orderBy('updated_at', 'desc')->get();
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"Link Products"]
        ];

        if ( request()->ajax()) {
            $all_shops = Shop::where('business_id', $request->user()->business_id)->orderBy('updated_at', 'desc')->get();
            $Shop_array = array();
            foreach($all_shops as $all_shopsVAL){
                $Shop_array[] = $all_shopsVAL->id;
            }
           
           $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$sku->id)->orderBy('updated_at', 'desc');
                        
           
            return Datatables::eloquent($Sku_prod)
                        ->addColumn('shop', function(Products $product) {
                            return $product->shop->getImgSiteDisplay();
                                })
                        ->addColumn('image', function(Products $product) {
                            $image_url = '';
                            $imagres = explode("|",$product->Images);
                            if(isset($imagres[0])){
                                $image_url = $imagres[0];
                            }
                            return $image_url;
                        })
                        ->addColumn('action', function(Products $product) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to Unlink '. $product->name .' ?" data-text="This Product will no longer sync to this SKU." data-method="POST" data-href="'.route('sku.removeskuproduct',['id'=>$product->id, 'sku_id'=>$product->seller_sku_id]).'" > Unlink</a>
                        <a class="dropdown-item" href="'.$product->Url.'"  target="_blank" ><i class="fa fa-folder-open aria-hidden="true""></i> View</a>
                    </div></div>';
                                })
                        ->rawColumns(['shop', 'action'])
                        ->make(true);
        }
        return view('sku.listproducts', [
            'sku' => $sku,
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => array(),
        ]);
    }
 
    // public function skuproductsset(Sku $sku,Request $request){
        
    //     $all_skus = Sku::where('business_id', $request->user()->business_id)->where('type', 'single')->orderBy('updated_at', 'desc')->get();
        
    //     $breadcrumbs = [
    //         ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"Link Set"]
    //     ];

    //     if ( request()->ajax()) {
    //         $all_skus = Sku::where('business_id', $request->user()->business_id)->where('type', 'single')->orderBy('updated_at', 'desc')->get();
    //         $Sku_array = array();
    //         foreach($all_skus as $all_skusVAL){
    //             $Sku_array[] = $all_skusVAL->id;
    //         }
           
    //         $Sku_prod = SetItem::whereIn('sku_single_id', $Sku_array)->where('sku_set_id','=',$sku->id)->orderBy('updated_at', 'desc');

           
    //         return Datatables::eloquent($Sku_prod)
    //                     ->addColumn('image', function(SetItem $setitem) {
    //                         $sku_single = Sku::where('id', $setitem->sku_single_id)->first();
    //                         return $sku_single->SkuImage();
    //                     })
    //                     ->addColumn('action', function(SetItem $setitem) {
    //                         return '<div class="btn-group dropup mr-1 mb-1">
    //                 <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
    //                 Action<span class="sr-only">Toggle Dropdown</span></button>
    //                 <div class="dropdown-menu">
    //                     <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to Unlink '. $setitem->name .' ?" data-text="This SKU Product will no longer sync to this SKU Set." data-method="POST" data-href="'.route('sku.removeskuproductset',['id'=>$setitem->id, 'sku_id'=>$setitem->sku->id]).'" > Unlink</a>
    //                     <a class="dropdown-item" href="'.route('sku.skuproducts',$setitem->sku->id).'"  target="_blank" ><i class="fa fa-folder-open aria-hidden="true""></i> View</a>
    //                 </div></div>';
    //                             })
    //                     ->rawColumns(['action'])
    //                     ->make(true);
    //     }
    //     return view('sku.listproductsset', [
    //         'sku' => $sku,
    //         'breadcrumbs' => $breadcrumbs,
    //         'all_skus' => $all_skus,
    //         'statuses' => array(),
    //     ]);
    // }

    public function addproductmodal(Request $request){
        $business_id = Auth::user()->business_id;
        $all_shops = Shop::where('business_id', $request->user()->business_id)->orderBy('updated_at', 'desc')->get();
        $id  = $request->id;
        $title = "this SKU";
        return view('sku.modal.addskuproduct', compact('title', 'id', 'all_shops'));
    }

    // public function addproductsetmodal(Request $request){
    //     $business_id = Auth::user()->business_id;
    //     $all_skus = Sku::where('business_id', $request->user()->business_id)->where('type', 'single')->orderBy('updated_at', 'desc')->get();
    //     $id  = $request->id;
    //     $title = "this SKU";
    //     return view('sku.modal.addskuproductset', compact('title', 'id', 'all_skus'));
    // }

    public function addproduct(Request $request){
        $business_id = Auth::user()->business_id;
        $product = Products::whereId($request->product)->where('shop_id', $request->shop)->first();
        $sku = Sku::where('business_id','=', $business_id)->where('id','=',$request->sku_id)->first();

        $data = [
            'seller_sku_id' => $sku->id,
            'price' => $sku->price,
            // 'SellerSku' => $sku->code,
            'quantity' => $sku->quantity,
        ];  

        $result = $product->update($data);

        if ($sku->type == 'single') {
            $product->update(['quantity' => $product->getWarehouseQuantity()]);
        }
        else if ($sku->type == 'set') {
            $sku_set_quantity = $sku->computeSetQuantity($product->shop->warehouse_id);
            $product->update(['quantity' => $sku_set_quantity]);
            
            $data = [
                'warehouse_id' => $product->shop->warehouse_id,
                'sku_id' => $sku->id,
                'quantity' => $sku_set_quantity
            ];

            $witem = WarehouseItems::updateOrCreate(['warehouse_id' => $product->shop->warehouse_id, 'sku_id' => $sku->id], $data);

            $sku_total_quantity = 0;
            foreach($sku->warehouse_items as $item) {
                $sku_total_quantity += $item->quantity;
            }

            $sku->update(['quantity' => $sku_total_quantity]);
        }

        $response = $product->updatePlatform();
  
        print json_encode($result);
    }

    // public function addproductset(Request $request){
    //     $business_id = Auth::user()->business_id;
    //     $sku_product = Sku::where('business_id','=', $business_id)->where('id','=',$request->sku)->first(); //sku child, sku currently being link to parent
    //     $sku_set = Sku::where('business_id','=', $business_id)->where('id','=',$request->sku_id)->first(); //sku parent
    //     $quantity = $request->quantity; //sku child's quantity per set
    //     // $products = Products::where('seller_sku_id', $sku_set->id)->get(); //linked sku set/parent products

    //     $data = [
    //         'sku_set_id' => $sku_set->id,
    //         'sku_single_id' => $sku_product->id,
    //         'code' => $sku_product->code,
    //         'name' => $sku_product->name,
    //         'unit_price' => $sku_product->price,
    //         // 'SellerSku' => $sku->code,
    //         'set_quantity' => $quantity
    //     ];  

    //     $result = SetItem::updateOrCreate(['sku_set_id' => $sku_set->id, 'sku_single_id' => $sku_product->id], $data);

    //     $sku_set_quantity = $sku_set->computeSetQuantity($sku_set->products->shop->warehouse_id);
    //     $sku_set->update(['quantity' => $sku_set_quantity]); //update sku parent's quantity based on the computation

    //     if (isset($sku_set->products)) {
    //         foreach($sku_set->products as $product) {
    //             $result_prod = $product->update(['quantity' => $sku_set_quantity]); //update parent products' quantity
    //             $response = $product->updatePlatform();
    //         }
    //     }
  
    //     print json_encode($result);
    // }

    public function removeskuproduct(Request $request){
        $ids = array();
        if($request->ids){
            $ids = $request->ids;
        }
        array_push($ids, $request->id);
        $update = Products::whereIn('id', $ids)->update(['seller_sku_id' => null]);
        if($update) {
            // $sku_of_set = Sku::find($sku->id);
            // if (!isset($sku_of_set->products)) {
            //     $quantity_array = array();
            //     foreach($sku_of_set->set_items as $item) {
            //         $sku_of_setitem = Sku::find($item->sku_single_id);
            //         $quantity_array[] = (int)($sku_of_setitem->quantity / $sku_of_set->quantity);
            //     }
            //     $quantity_of_set = min($quantity_array);
            //     $sku_of_set->quantity = $quantity_of_set;
            //     $sku_of_set->save();
            // }
            $return = array('success' => true, 'msg' => "Product Unlink Successfully.");
        }
        print json_encode($return);
    }

    // public function removeskuproductset(Request $request){
    //     $ids = array();
    //     if($request->ids){
    //         $ids = $request->ids;
    //     }
    //     array_push($ids, $request->id);
    //     $update = SetItem::whereIn('id', $ids)->delete();
    //     if($update) {
    //         $return = array('success' => true, 'msg' => "SKU Unlink Successfully.");

    //         $sku = Sku::where('id', $request->sku_id)->first();
    //         $sku_set_quantity = $sku->computeSetQuantity();
    //         $sku->update(['quantity' => $sku_set_quantity]);

    //         if (isset($sku->products)) {
    //             foreach($sku->products as $product) {
    //                 $result = $product->update(['quantity' => $sku_set_quantity]); //update parent products' quantity
    //                 $response = $product->updatePlatform();
    //             }
    //         }
    //     }
    //     print json_encode($return);
    // }

    public function show() {

    }

    public function search($warehouse = 'none', $search, $customer_id = 'none', $withQTY = false)
    {
        if($warehouse != 'none') {
            $sku = Sku::where(function($query) use ($search){
                $query->where('name', 'LIKE', '%'. $search. '%');
                $query->orWhere('code', 'LIKE', '%'. $search. '%');
            });
            $sku->select('sku.*','warehouse_items.quantity');
            if ($withQTY === true) {
                $sku->join('warehouse_items', 'warehouse_items.sku_id', '=', 'sku.id');
                $sku->where('warehouse_items.warehouse_id', $warehouse);
                $sku->where('warehouse_items.quantity', '>=', 1);
            }
            else {
                $sku->leftjoin('warehouse_items', 'warehouse_items.sku_id', '=', 'sku.id');
            }
            $result = $sku->get();
            foreach ($result as &$r) {
                if($withQTY !== true) {
                    $qty = $r->warehouse_items()->where('warehouse_id', $warehouse)->first();
                    $r->quantity = ($qty)?$qty->quantity:0;
                }

                $r->image = $r->SkuImage();
                if ($customer_id != 'none') {
                    $customer = Customer::where('business_id', Auth::user()->business_id)->where('id', $customer_id)->first();
                    $price_group_item = PriceGroupItemPrice::where('price_group_id', $customer->price_group)->where('sku_id', $r->id)->first();
                    $r->price_group_item = $customer->price_group;
                    if($price_group_item) {
                        $r->price = $price_group_item->price;
                        $r->customer = $customer;
                    }
                }
            }
        }
        else {
            $result = [];
        }
        return response()->json($result);
            
    }

    public function searchPurchase($search)
    {
        if($search != '') {
            $sku = Sku::where(function($query) use ($search){
                $query->where('name', 'LIKE', '%'. $search. '%');
                $query->orWhere('code', 'LIKE', '%'. $search. '%');
            });
            $sku->select('sku.*');
            $result = $sku->get();
            foreach ($result as &$r) {
                $r->image = $r->SkuImage();
            }
        }
        else {
            $result = [];
        }
        return response()->json($result);
            
    } 
    
    public function delete($id, Request $request){
        $business_id = Auth::user()->business_id;
        
        $Sku_check = Sku::where('business_id','=',$business_id)->where('id','=',$id)->get()->count();
        
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }
        $link_prod = Products::where('seller_sku_id', $id)->update(['seller_sku_id' => null]);
        $Sku = Sku::find($id);
        
        if($Sku->delete()){
            
            $output = ['success' => 1,
                    'msg' => 'Success',
                ];
            
        }else{
            $output = ['success' => 0,
                        'msg' => "Error !",
                    ];
            
        }
        return response()->json($output);
    }
    
    public function bulkremove(Request $request){
        
        $ids = $request->ids;
        
        foreach($ids as $id){
            $link_prod = Products::where('seller_sku_id', $id)->update(['seller_sku_id' => null]);
            $Brand = Sku::find($id);
            $Brand->delete();
            
        }
        
        
        $output = ['success' => 1,
                        'msg' => "success",
                    ];
        echo json_encode($output);
        
    }

    public function import() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"Import SKU"]
        ];
        return view('sku.import', [
            'breadcrumbs' => $breadcrumbs
            ]);
    }

    public function export(Request $request){
        $skus = Sku::select('code','name','brand','category','supplier','cost','price','alert_quantity')->where('business_id', $request->user()->business_id)->get()->toArray();
        $columns = ['code','name','brand','category','supplier','cost','price','alert_quantity'];

        return sku::getCsv($columns, $skus, 'Sku ' . Carbon::now(). '.csv');
    }

    public function submitImport(Request $request) {
        try {
            if(Excel::import(new SkuImport,request()->file('file'))) {
                $output = ['success' => 1,
                    'msg' => 'SKU Imported successfully!',
                    'redirect' => action('SkuController@index')
                ];
            }
          
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $msg = [];
            foreach ($failures as $failure) {
                 $failure->row(); // row that went wrong
                 $failure->attribute(); // either heading key (if using heading row concern) or column index
                 $failure->errors(); // Actual error messages from Laravel validator
                 $failure->values(); // The values of the row that has failed.
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

    // public function ajaxlistsku(Request $request) {
    //     $output = Sku::where('shop_id',$request->shop_id)->get();
    //     echo json_encode($output);
    // }

}

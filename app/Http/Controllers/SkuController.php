<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Customer;
use App\Http\Controllers\Controller;
use App\Imports\SkuImport;
use App\Lazop;
use App\Library\lazada\LazopClient;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\UrlConstants;
use App\PriceGroupItemPrice;
use App\Products;
use App\Shop;
use App\Sku;
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
            
               
           $Sku = Sku::where('business_id','=',$business_id)->orderBy('updated_at', 'desc');
           
           
            return Datatables::eloquent($Sku)
            ->editColumn('cost', function(Sku $SKSU) {
                            return "<p>".$SKSU->cost.'</p><input type="number" min="0" class="form-control" data-defval="'.$SKSU->cost.'" data-name="cost" value="'.$SKSU->cost.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('price', function(Sku $SKSU) {
                            return "<p>".$SKSU->price.'</p><input type="number" min="1" class="form-control" data-defval="'.$SKSU->price.'" data-name="price" value="'.$SKSU->price.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('quantity', function(Sku $SKSU) {
                            return "<p>".$SKSU->quantity.'</p><input type="number" min="0" class="form-control" data-defval="'.$SKSU->quantity.'" data-name="quantity" value="'.$SKSU->quantity.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('alert_quantity', function(Sku $SKSU) {
                            return "<p>".$SKSU->alert_quantity.'</p><input type="number" class="form-control" data-defval="'.$SKSU->alert_quantity.'" data-name="alert_quantity" value="'.$SKSU->alert_quantity.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
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
            ->rawColumns(['cost','price','quantity','alert_quantity','action'])
            ->make(true);
        }
        
        return view('sku.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }
    
    
    public function create(){
        $user = Auth::user();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"SKU  Create"]
        ];
        
        // $Category = Category::auth_category();
        // $Brand = Brand::auth_brand();
        $Supplier = Supplier::auth_supplier();
        
        return view('sku.create', [
            'breadcrumbs' => $breadcrumbs,
            // 'Category'=> $Category,
            // 'Brand'=> $Brand,
            'Supplier' => $Supplier
            ]);
        
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
        ]);
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
        
        if($sku->save()){
            $request->session()->flash('flash_success', 'Successfully added SKU. Next step is to LINK your shop listing to your SKU');
        }else{
            $request->session()->flash('flash_error',"something Went wrong !");
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
        
        // $Category = Category::auth_category();
        // $Brand = Brand::auth_brand();
        $Supplier = Supplier::auth_supplier();

        
        return view('sku.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Sku'=> $Sku,
            // 'Category'=> $Category,
            // 'Brand'=> $Brand,
            'Supplier' => $Supplier
            ]);
        
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
        ]);
        $sku = Sku::find($request->id);
        $sku->code = $request->code;
        $sku->name = $request->name;
        // $sku->brand = $request->brand;
        // $sku->category = $request->category;
        $sku->supplier = $request->supplier;
        $sku->cost = $request->cost;
        $sku->price = $request->price;
        $sku->alert_quantity = $request->alert_quantity;
        
        if($sku->save()){
            $Sku_prod = Products::with('shop')->where('seller_sku_id','=',$sku->id)->orderBy('updated_at', 'desc')->get();
            foreach ($Sku_prod as $prod) {
                $shop_id = $prod->shop_id;
                $access_token = $prod->shop->access_token;
                $prod->price = $sku->price;
                $prod->save();
                    $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                    <Request>
                        <Product>
                            <Skus>
                                <Sku>
                                    <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                    <price>'.$prod->price.'</price>
                                </Sku>
                            </Skus>
                        </Product>
                    </Request>';
                if(env('lazada_sku_sync', true)){
                    if($prod->site == 'lazada'){
                        $response = $prod->product_price_quantity_update($xml);
                    }
                }
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
        $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$request->sku)->orderBy('updated_at', 'desc')->get();
        $column = $request->name;
        $sku = Sku::where('business_id','=', $business_id)->where('id','=',$request->sku)->first();
        if($sku && $column != 'quantity'){  //quantity is dependent on warehouse
            $sku->$column = $request->val;
            $result = $sku->save();

            if(in_array($column, array('price'))) {
                
                foreach ($Sku_prod as $prod) {
                    $shop_id = $prod->shop_id;

                    $prod = Products::where('id', $prod->id)->first();
                    $prod->$column = $request->val;
                    $prod->save();
                        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                        <Request>
                            <Product>
                                <Skus>
                                    <Sku>
                                        <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                        <'.$column.'>'.$prod->$column.'</'.$column.'>
                                    </Sku>
                                </Skus>
                            </Product>
                        </Request>';
                    if(env('lazada_sku_sync', true)){
                        if($prod->site == 'lazada'){
                            $response = $prod->product_price_quantity_update($xml);
                        }
                    }
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

        foreach($sku as $s) {
            $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$s->id)->orderBy('updated_at', 'desc')->get();

            foreach ($Sku_prod as $prod) {
                $witem = $prod->shop->warehouse->items()->where('sku_id', $s->id)->first();
                $prod->price = $s->price;
                $prod->quantity = isset($witem->quantity)?$witem->quantity:0;
                $prod->save();
                    $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                    <Request>
                        <Product>
                            <Skus>
                                <Sku>
                                    <SellerSku>'.$prod->SellerSku.'</SellerSku>
                                    <Price>'.$prod->price.'</Price>
                                    <Quantity>'.$prod->quantity.'</Quantity>
                                </Sku>
                            </Skus>
                        </Product>
                    </Request>';
                if(env('lazada_sku_sync', true)){
                    if($prod->site == 'lazada'){
                        $response = $prod->product_price_quantity_update($xml);
                    }
                }
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
                        <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to Unlink '. $product->name .' ?" data-text="This Product will no longer sync to this SKU." data-method="POST" data-href="'.route('sku.removeskuproduct',['id'=>$product->id]).'" > Unlink</a>
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

    public function addproductmodal(Request $request){
        $business_id = Auth::user()->business_id;
        $all_shops = Shop::where('business_id', $request->user()->business_id)->orderBy('updated_at', 'desc')->get();
        $id  = $request->id;
        $title = "this SKU";
        return view('sku.modal.addskuproduct', compact('title', 'id', 'all_shops'));
    }

    public function addproduct(Request $request){
        $business_id = Auth::user()->business_id;
        $prod = Products::whereId($request->product)->where('shop_id', $request->shop)->first();
        $access_token = Shop::find($prod->shop_id)->access_token;
        $sku = Sku::where('business_id','=', $business_id)->where('id','=',$request->sku_id)->first();
        $prod->seller_sku_id = $request->sku_id;
        $prod->price = $sku->price;
        $prod->quantity = $sku->quantity;
        $result = $prod->save();
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
        <Request>
            <Product>
                <Skus>
                    <Sku>
                        <SellerSku>'.$prod->SellerSku.'</SellerSku>
                        <price>'.$prod->price.'</price>
                        <quantity>'.$prod->quantity.'</quantity>
                    </Sku>
                </Skus>
            </Product>
        </Request>';
        if(env('lazada_sku_sync', true)){
            if($prod->site == 'lazada'){
                $response = $prod->product_price_quantity_update($xml);
            }
        }
        print json_encode($result);
    }

    public function removeskuproduct(Request $request){
        $ids = array();
        if($request->ids){
            $ids = $request->ids;
        }
        array_push($ids, $request->id);
        $update = Products::whereIn('id', $ids)->update(['seller_sku_id' => null]);
        if($update) {
            $return = array('success' => true, 'msg' => "Product Unlink Successfully.");
        }
        print json_encode($return);
    }

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
            if ($warehouse != 'none') {
                if ($withQTY) {
                    $sku->join('warehouse_items', 'warehouse_items.sku_id', '=', 'sku.id');
                    $sku->where('warehouse_items.warehouse_id', $warehouse);
                    $sku->where('warehouse_items.quantity', '>=', 1);
                }
                else {
                    $sku->leftjoin('warehouse_items', 'warehouse_items.sku_id', '=', 'sku.id');
                }
            }
            $result = $sku->get();
            foreach ($result as &$r) {
                if($warehouse != 'none' && !$withQTY) {
                    $qty = $r->warehouse_items()->where('warehouse_id', $warehouse)->first()->quantity;
                    $r->quantity = ($qty)?$qty:0;
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

}

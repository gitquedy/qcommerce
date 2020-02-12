<?php

namespace App\Http\Controllers;

use App\Sku;
use App\Products;
use App\Category;
use App\Brand;
use App\Shop;
use Illuminate\Http\Request;
use App\Lazop;
use Carbon\Carbon;
use App\Library\lazada\LazopRequest;
use App\Library\lazada\LazopClient;
use App\Library\lazada\UrlConstants;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Helper;
use Auth;

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
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"list of SKU"]
        ];
        
        if ( request()->ajax()) {
            
            $user_id = Auth::user()->id;
            
               
           $Sku = Sku::where('user_id','=',$user_id)->orderBy('updated_at', 'desc');
           
           
            return Datatables::eloquent($Sku)
            ->editColumn('cost', function(Sku $SKSU) {
                            return "<p>".$SKSU->cost.'</p><input type="number" class="form-control" data-defval="'.$SKSU->cost.'" data-name="cost" value="'.$SKSU->cost.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('price', function(Sku $SKSU) {
                            return "<p>".$SKSU->price.'</p><input type="number" class="form-control" data-defval="'.$SKSU->price.'" data-name="price" value="'.$SKSU->price.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
                        })
            ->editColumn('quantity', function(Sku $SKSU) {
                            return "<p>".$SKSU->quantity.'</p><input type="number" class="form-control" data-defval="'.$SKSU->quantity.'" data-name="quantity" value="'.$SKSU->quantity.'" data-sku_id="'.$SKSU->id.'" style="display:none;">';
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
            ->addColumn('brand_name', function(Sku $SKSU) {
                            $Brand = Brand::find($SKSU->brand);
                            if($Brand){
                               return  $Brand->name;
                            }
                        })
            ->addColumn('action', function(Sku $SKSU) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                            Action<span class="sr-only">Toggle Dropdown</span></button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item fa fa-link" href="'.route('sku.skuproducts',['id'=>$SKSU->id]).'" > Link SKU Products</a>
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
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"SKU  Create"]
        ];
        
        $Category = Category::auth_category();
        $Brand = Brand::auth_brand();
        
        return view('sku.create', [
            'breadcrumbs' => $breadcrumbs,
            'Category'=>$Category,
            'Brand'=>$Brand
            ]);
        
    }
    
    
    public function add(Request $request){
        
        $sku = new Sku();
        $sku->user_id = Auth::user()->id;
        $sku->code = $request->code;
        $sku->name = $request->name;
        $sku->brand = $request->brand;
        $sku->category = $request->category;
        $sku->cost = $request->cost;
        $sku->price = $request->price;
        $sku->quantity = $request->quantity;
        $sku->alert_quantity = $request->alert_quantity;
        
        if($sku->save()){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/sku/skuproducts/'.$sku->id);
        
    }
    
    
    public function edit($id="",Request $request){
        
        $user_id = Auth::user()->id;
        
        $Sku_check = Sku::where('user_id','=',$user_id)->where('id','=',$id)->get()->count();
        
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"SKU  Create"]
        ];
        
        $Sku = Sku::find($id);
        
        $Category = Category::auth_category();
        $Brand = Brand::auth_brand();
        
        return view('sku.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Sku'=>$Sku,
            'Category'=>$Category,
            'Brand'=>$Brand
            ]);
        
    }
    
    
    public function update(Request $request){
        
        $user_id = Auth::user()->id;
        
        $Sku_check = Sku::where('user_id','=',$user_id)->where('id','=',$request->id)->get()->count();
        
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }
        
        $sku = Sku::find($request->id);
        $sku->code = $request->code;
        $sku->name = $request->name;
        $sku->brand = $request->brand;
        $sku->category = $request->category;
        $sku->cost = $request->cost;
        $sku->price = $request->price;
        $sku->quantity = $request->quantity;
        $sku->alert_quantity = $request->alert_quantity;
        
        if($sku->save()){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/sku');
        
    }
    
    public function quickUpdate(Request $request){
        $user_id = Auth::user()->id;
        $column = $request->name;
        $sku = Sku::where('user_id','=', $user_id)->where('id','=',$request->sku)->first();
        if($sku){
            $sku->$column = $request->val;
            $result = $sku->save();
        }
        else {
            $result = false;
        }
        echo json_encode($result);
        

    }

    public function skuproducts($id="",Request $request){
        $user_id = Auth::user()->id;
        $Sku_check = Sku::where('user_id','=',$user_id)->where('id','=',$id)->get()->count();
        
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }

        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SkuController@index'), 'name'=>"SKU"], ['name'=>"List Products"]
        ];

        if ( request()->ajax()) {
            $user_id = Auth::user()->id;

            $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
            $Shop_array = array();
            foreach($all_shops as $all_shopsVAL){
                $Shop_array[] = $all_shopsVAL->id;
            }
               
           $Sku_prod = Products::with('shop')->whereIn('shop_id', $Shop_array)->where('seller_sku_id','=',$id)->orderBy('updated_at', 'desc');
                        
           
            return Datatables::eloquent($Sku_prod)
                        ->addColumn('shop', function(Products $product) {
                            return $product->shop ? $product->shop->short_name : '';
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
                        <a class="dropdown-item" href="'.$product->Url.'"  target="_blank" ><i class="fa fa-folder-open aria-hidden="true""></i> View</a>
                        <a class="dropdown-item" href="'.route('product.edit',array('id'=>$product->id)).'" ><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                    </div></div>';
                                })
                        ->make(true);
        }
        
        return view('sku.listproducts', [
            'id' => $id,
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => $all_shops,
            'statuses' => array(),
        ]);
    }

    public function addproductmodal(Request $request){
        $user_id = Auth::user()->id;
        $all_shops = Shop::where('user_id', $request->user()->id)->orderBy('updated_at', 'desc')->get();
        $id  = $request->id;
        $title = "this SKU";
        return view('sku.modal.addskuproduct', compact('title', 'id', 'all_shops'));
    }

    public function addproduct(Request $request){
        $product = Products::whereId($request->product)->where('shop_id', $request->shop)->first();
        $product->seller_sku_id = $request->sku_id;

        print json_encode($product->save());
    }

    public function removeskuproduct(Request $request){
        $ids = $request->ids;
        array_push($ids, $request->id);
        $update = Products::whereIn('id', $ids)->update(['seller_sku_id' => null]);

    }
    
    public function delete($id, Request $request){
        $user_id = Auth::user()->id;
        
        $Sku_check = Sku::where('user_id','=',$user_id)->where('id','=',$id)->get()->count();
        
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
    
    

    
    
    
    
    
    
    
    
    



}

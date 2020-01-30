<?php

namespace App\Http\Controllers;

use App\Sku;
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
                    <a class="dropdown-item fa fa-edit" href="'.route('sku.edit',['id'=>$SKSU->id]).'" > Edit</a>
                    <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $SKSU->name .' ?" data-text="This Action is irreversible." data-href="'.route('sku.delete',['id'=>$SKSU->id]).'" > Delete</a>
                    </div>
                    </div>';
                                })
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
        
        return redirect('/sku');
        
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
    
    
    public function delete($id="",Request $request){
        
        $user_id = Auth::user()->id;
        
        $Sku_check = Sku::where('user_id','=',$user_id)->where('id','=',$id)->get()->count();
        
        if($Sku_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/sku');
        }
        
        $Sku = Sku::find($id);
        
        if($Sku->delete()){
            
            $output = ['success' => 1,
                    'msg' => 'success',
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
            
            $Brand = Sku::find($id);
            $Brand->delete();
            
        }
        
        
        $output = ['success' => 1,
                        'msg' => "success",
                    ];
        echo json_encode($output);
        
    }
    
    

    
    
    
    
    
    
    
    
    



}

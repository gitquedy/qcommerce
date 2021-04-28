<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Shop;
use App\Sku;
use Illuminate\Http\Request;
use App\Lazop;
use Carbon\Carbon;
use App\Library\Lazada\lazop\LazopRequest;
use App\Library\Lazada\lazop\LazopClient;
use App\Library\Lazada\lazop\UrlConstants;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Helper;
use Auth;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('BrandController@index'), 'name'=>"Brand"], ['name'=>"list of Brand"]
        ];
        
        

        
    if ( request()->ajax()) {
        
        $business_id = Auth::user()->business_id;
        
           
           $brand = Brand::where('business_id','=',$business_id)->orderBy('updated_at', 'desc');
           
           
            return Datatables::eloquent($brand)
            ->addColumn('action', function(Brand $brand) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                    <a class="dropdown-item fa fa-edit" href="'.route('brand.edit',['id'=>$brand->id]).'" > Edit</a>
                    <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $brand->name .' ?" data-text="This Action is irreversible." data-href="'.route('brand.delete',['id'=>$brand->id]).'" > Delete</a>
                        
                    </div></div>';
                                })
                ->make(true);
        }
        
        return view('brand.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }
    
    
    public function create(){
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('BrandController@index'), 'name'=>"Brand"], ['name'=>"Brand Create"]
        ];
        
        return view('brand.create', [
            'breadcrumbs' => $breadcrumbs
            ]);
        
    }
    
    
    public function add(Request $request){
        
        // validation start
        
        $duplicate_check = Brand::where('code','=',$request->code)->where('business_id','=',Auth::user()->business_id)->get()->count();
        if($duplicate_check>0){
            $request->session()->flash('flash_error',"Duplicate Brand code !");
            return redirect('/brand');
        }
        
        
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);
        // validation End
        
        
        $Brand = new Brand();
        $Brand->code = $request->code;
        $Brand->business_id = Auth::user()->business_id;
        $Brand->name = $request->name;
        
        if($Brand->save()){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/brand');
        
    }
    
    
    public function add_ajax(Request $request){
        
        // validation start
        
        $duplicate_check = Brand::where('code','=',$request->code)->where('business_id','=',Auth::user()->business_id)->get()->count();
        if($duplicate_check>0){
            $output = ['success' => 0,
                        'msg' => "Duplicate Brand code !",
                    ];
            return response()->json($output);
        }
        
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);
        // validation End

        
        $Brand = new Brand();
        $Brand->code = $request->code;
        $Brand->business_id = Auth::user()->business_id;
        $Brand->name = $request->name;
        
        
        if($Brand->save()){
            $output = ['success' => 1,
                        'msg' => "Success !",
                        'id'=>$Brand->id
                    ];
            
        }else{
            $output = ['success' => 0,
                        'msg' => "Error !",
                    ];
        }
        
        return response()->json($output);
        
    }
    
    
    public function edit($id="",Request $request){
        
        
        // validation start

        $business_id = Auth::user()->business_id;
        $brand_chk = Brand::where('business_id','=',$business_id)->where('id','=',$id)->get()->count();
        
        if($brand_chk!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/brand');
        }
        // validation end
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('BrandController@index'), 'name'=>"Brand"], ['name'=>"Brand Edit"]
        ];
        
        $Brand = Brand::find($id);
        
        return view('brand.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Brand'=>$Brand
            ]);
        
    }
    
    
    public function update(Request $request){
        
        // validation start
        $business_id = Auth::user()->business_id;
        
        $brand_chk = Brand::where('business_id','=',$business_id)->where('id','=',$request->id)->get()->count();
        
        if($brand_chk!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/brand');
        }
        
        $duplicate = Brand::where('code','=',$request->code)->where('business_id','=',$business_id)->get();
        foreach($duplicate as $duplicateVAL){
            if($request->id!=$duplicateVAL->id){
                $request->session()->flash('flash_error',"Dupliacte Brand code !");
                return redirect('/brand');
            }
        }
        
        
        $request->validate([
            'id' => 'required',
            'code' => 'required',
            'name' => 'required',
        ]);
        // validation end
        
        $Brand = Brand::find($request->id);
        $Brand->code = $request->code;
        $Brand->name = $request->name;
        
        if($Brand->save()){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/brand');
        
    }
    
    
    public function delete($id="",Request $request){
        
        $Brand = Brand::find($id);
        
        // validation start
        $business_id = Auth::user()->business_id;
        $brand_chk = Brand::where('business_id','=',$business_id)->where('id','=',$id)->get()->count();
        
        $error = "";
        
        if($brand_chk!=1){
            $error .= "Invalid request !<br/>";
        }
        
        $Sku = Sku::where('brand','=',$id)->get();
        
        foreach($Sku as $SkuVAL){
           $error .= "Brand ".$Brand->name." used in SKU ".$SkuVAL->code."<br/>";
        }
        
        if($error!=""){
            $output = ['success' => 0,
                        'msg' => $error,
                    ];
            return response()->json($output);
        }
        
        // validation end
        
        if($Brand->delete()){
            
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
        
        $error = "";
        
        foreach($ids as $id){
            
            $Brand = Brand::find($id);
            
            $Sku = Sku::where('brand','=',$id)->get();
            
            foreach($Sku as $SkuVAL){
               $error.="Brand ".$Brand->name." used in SKU ".$SkuVAL->code."<br/>";
            }
            
        }
        
        if($error!=""){
            $output = ['success' => 0,
                        'msg' => $error,
                    ];
            return response()->json($output);
        }
        
        
        foreach($ids as $id){
            
            $Brand = Brand::find($id);
            $Brand->delete();
            
        }
        
        
        $output = ['success' => 1,
                        'msg' => "success",
                    ];
        return response()->json($output);
        
    }
    
    

    
    
    
    
    
    
    
    
    



}

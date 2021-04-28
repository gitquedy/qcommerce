<?php

namespace App\Http\Controllers;

use App\Category;
use App\Sku;
use App\Shop;
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

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CategoryController@index'), 'name'=>"Category"], ['name'=>"list of Category"]
        ];
    if ( request()->ajax()) {
        
            $business_id = Auth::user()->business_id;
           $category = Category::where('business_id','=',$business_id)->orderBy('updated_at', 'desc');
           
           
            return Datatables::eloquent($category)
            ->addColumn('parent_name', function(Category $Category) {
                
                $tmp_data = Category::find($Category->parent);
                            if($tmp_data){
                               return $tmp_data->name; 
                            }else{
                                return '-';
                            }
                                })
            ->addColumn('action', function(Category $Category) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                    <a class="dropdown-item fa fa-edit" href="'.route('category.edit',['id'=>$Category->id]).'" > Edit</a>
                    <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $Category->name .' ?" data-text="This Action is irreversible." data-href="'.route('category.delete',['id'=>$Category->id]).'" > Delete</a>
                        
                        
                    </div></div>';
                                })
                ->make(true);
        }
        
        return view('category.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }
    
    
    public function create(){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CategoryController@index'), 'name'=>"Category"], ['name'=>"Category  Create"]
        ];
        
        $Category = Category::auth_category();
        
        return view('category.create', [
            'breadcrumbs' => $breadcrumbs,
            'Category' => $Category
            ]);
        
    }
    
    
    public function add(Request $request){
        
        $duplicate_check = Category::where('code','=',$request->code)->where('business_id','=',Auth::user()->business_id)->get()->count();
        
        if($duplicate_check>0){
            $request->session()->flash('flash_error',"Duplicate Category Code !");
            return redirect('/category');
        }
        
        
        $Category = new Category();
        $Category->business_id = Auth::user()->business_id;
        $Category->code = $request->code;
        $Category->name = $request->name;
        
        if($request->parent!=""){
        $Category->parent = $request->parent;    
        }else{
        $Category->parent = 0;    
        }
        
        
        if($Category->save()){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/category');
        
    }
    
    public function add_ajax(Request $request){
        
        $duplicate_check = Category::where('code','=',$request->code)->where('business_id','=',Auth::user()->business_id)->get()->count();
        
        if($duplicate_check>0){
            $output = ['success' => 0,
                        'msg' => "Duplicate Category Code !",
                    ];
            return response()->json($output);
        }
        
        
        $Category = new Category();
        $Category->business_id = Auth::user()->business_id;
        $Category->code = $request->code;
        $Category->name = $request->name;
        
        if($request->parent!=""){
        $Category->parent = $request->parent;    
        }else{
        $Category->parent = 0;    
        }
        
        
        if($Category->save()){
            $output = ['success' => 1,
                        'msg' => "Success !",
                        'id'=>$Category->id
                    ];
            
        }else{
            $output = ['success' => 0,
                        'msg' => "Error !",
                    ];
        }
        
        return response()->json($output);
        
    }
    
    
    public function edit($id="",Request $request){
        
        $business_id = Auth::user()->business_id;
        $Category_check = Category::where('business_id','=',$business_id)->where('id','=',$id)->get()->count();
        if($Category_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/category');
        }
        
        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CategoryController@index'), 'name'=>"Category"], ['name'=>"Category Edit"]
        ];
        
        $Category = Category::find($id);
        
        $CategoryALL = Category::auth_category();
        
        return view('category.edit', [
            'breadcrumbs' => $breadcrumbs,
            'Category'=>$Category,
            'CategoryALL'=>$CategoryALL
            ]);
        
    }
    
    
    public function update(Request $request){
        
        // validation start
        $business_id = Auth::user()->business_id;
        $Category_check = Category::where('business_id','=',$business_id)->where('id','=',$request->id)->get()->count();
        if($Category_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/category');
        }
        
        $duplicate_check = Category::where('code','=',$request->code)->where('business_id','=',$business_id)->where('id','=',$request->id)->get();
        
        foreach($duplicate_check as $duplicate_checkVAL){
            if($duplicate_checkVAL->id!=$request->id){
                $request->session()->flash('flash_error',"Duplicate Category Code !");
                return redirect('/category');
            }
        }
        
        // validation End
        
        $Category = Category::find($request->id);
        $Category->code = $request->code;
        $Category->name = $request->name;
        
        if($request->parent!=""){
            $Category->parent = $request->parent;
        }else{
            $Category->parent = 0;
        }
        
        if($request->id==$request->parent){
            $request->session()->flash('flash_error',"Can't become self parent !");
            return redirect('/category');
        }
        
        if($Category->save()){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/category');
        
    }
    
    
    
    public function delete($id="",Request $request){
        
        // validations start
        
        $error = "";
        
        $business_id = Auth::user()->business_id;
        $Category_check = Category::where('business_id','=',$business_id)->where('id','=',$request->id)->get()->count();
        if($Category_check!=1){
            $error.="invalid request";
        }
        
        $Sku_data = Sku::where('category','=',$id)->get()->toArray();
        
        foreach($Sku_data as $Sku_dataVAL){
            $error.= "The category used in SKU ".$Sku_dataVAL['code']."<br/>";
        }
        
        $Category_data = Category::where('parent','=',$id)->get()->toArray();
        
        foreach($Category_data as $Category_dataVAL){
            $error.= "The category parent of child category ".$Category_dataVAL['code']."<br/>";
        }
        
        if($error!=""){
            $output = ['success' => 0,
                        'msg' => $error
                    ];
            return response()->json($output);
        }
        
        // validations Ends
        
        $Brand = Category::find($id);
        
        if($Brand){
            if($Brand->delete()){
            
            $output = ['success' => 1,
                    'msg' => 'success',
                ];
            
            }
            
        }else{
            $output = ['success' => 0,
                        'msg' => "Category Not Found !",
                    ];
            
        }
        return response()->json($output);
    }
    
    
    public function bulkremove(Request $request){
        
        $ids = $request->ids;
        
        
        // validation start
        $error = "";
        
        foreach($ids as $id){
            
            $Category = Category::find($id);
            
            $sku = Sku::where('category','=',$id)->get();
            $parent = Category::where('parent','=',$id)->get();
            foreach($sku as $skuVAL){
                $error.="category ".$Category->code." used by SKU ".$skuVAL->code."<br/>";
            }
            foreach($parent as $parentVAL){
                $error.="category ".$parentVAL->code." child of ".$Category->code."<br/>";
            }
            
        }
        
        if($error!=""){
            $output = ['success' => 0,
                        'msg' => $error,
                    ];
        return response()->json($output);

        }
        
        // validation End
        
        $success="";
        
        foreach($ids as $id){
            $Category = Category::find($id);
            
            if($Category->delete()){
                $success.="category ".$Category->code." deleted <br/>";
            }
            
        }
        
        $output = ['success' => 1,
                        'msg' => $success,
                    ];
        return response()->json($output);
    }
}

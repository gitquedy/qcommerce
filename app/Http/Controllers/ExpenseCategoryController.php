<?php

namespace App\Http\Controllers;

use App\ExpenseCategory;
use App\Sku;
use App\Shop;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Utilities;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Helper;
use Auth;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CategoryController@index'), 'name'=>"Expense Category"], ['name'=>"list of Expense Category"]
        ];   
        if ( request()->ajax()) {
           $business_id = Auth::user()->business_id;
           $category = ExpenseCategory::where('business_id','=',$business_id)->orderBy('updated_at', 'desc');
           
           
            return Datatables::eloquent($category)
            ->addColumn('action', function(ExpenseCategory $ExpenseCategory) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                                        <a class="dropdown-item fa fa-edit" href="'.action('ExpenseCategoryController@edit',$ExpenseCategory->id).'" > Edit</a>
                    <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $ExpenseCategory->name .' ?" data-text="This Action is irreversible." data-href="'.action('ExpenseCategoryController@destroy',$ExpenseCategory->id).'" > Delete</a>      
                    </div></div>';
                                    })  
                ->make(true);
        }
            
        return view('expense_category.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
    
    
    public function create(){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('ExpenseCategoryController@index'), 'name'=>"Expense Category"], ['name'=>"Expense Category Create"]
        ];
        return view('expense_category.create', [
            'breadcrumbs' => $breadcrumbs,
            ]);
        
    }
    
    
    public function store(Request $request){
        
        $duplicate_check = ExpenseCategory::where('code','=',$request->code)->where('business_id','=',Auth::user()->business_id)->get()->count();
        
        if($duplicate_check>0){
            $request->session()->flash('flash_error',"Duplicate Category Code !");
            return redirect('/expense-category');
        }
        
        $data = $request->all();
        $data['business_id'] = $request->user()->business_id;
        

        if(ExpenseCategory::create($data)){
            $request->session()->flash('flash_success', 'Success !');
        }else{
            $request->session()->flash('flash_error',"something Went wrong !");
        }
        
        return redirect('/expense-category');
        
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
    
    
    public function edit(ExpenseCategory $expense_category,Request $request){        
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CategoryController@index'), 'name'=>"Category"], ['name'=>"Category Edit"]
        ];

        return view('expense_category.edit', [
            'breadcrumbs' => $breadcrumbs,
            'expense_category'=>$expense_category,
            ]);
        
    }
    
    
    public function update(ExpenseCategory $expense_category, Request $request){        
        $duplicate_check = ExpenseCategory::where('code','=',$request->code)->where('business_id','=',$request->user()->business_id)->where('id', '!=', $expense_category->id)->first();
        if($duplicate_check){
                $request->session()->flash('flash_error',"Duplicate Category Code !");
                return redirect('/expense-category');
        }
        
        if($expense_category->update($request->all())){
                    $request->session()->flash('flash_success', 'Success !');
                }else{
                    $request->session()->flash('flash_error',"something Went wrong !");
                }
        
        return redirect('/expense-category');
        
    }
    
    
    
    public function destroy(ExpenseCategory $expense_category,Request $request){
        $error = "";
        $output = ['success' => 0,
                        'msg' => $error
                    ];
        if($error!=""){
            return response()->json($output);
        }
        $expense_category->delete();
        $output = ['success' => 1,
                'msg' => 'success',
            ];
        return response()->json($output);
    }
    
    
    public function massDelete(Request $request){
        $ids = $request->ids;
        ExpenseCategory::whereIn('id', $ids)->where('business_id', $request->user()->business_id)->delete();
        $output = ['success' => 1,
                        'msg' => 'Successfully deleted expense categories.',
                    ];
        return response()->json($output);
    }
}

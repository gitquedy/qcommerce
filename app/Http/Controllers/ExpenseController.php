<?php

namespace App\Http\Controllers;


use App\Expense;
use App\Sku;
use App\Shop;
use Carbon\Carbon;
use App\Utilities;
use Validator;
use Helper;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Settings;
use App\OrderRef;
use Illuminate\Support\Facades\File; 

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CategoryController@index'), 'name'=>"Expense"], ['name'=>"list of Expenses"]
        ];   
        if ( request()->ajax()) {
           $business_id = Auth::user()->business_id;
           $expense = Expense::where('business_id','=',$business_id)->orderBy('updated_at', 'desc');
                
            return Datatables::eloquent($expense)
            ->addColumn('warehouse_name', function(Expense $Expense) {
                return $Expense->warehouse->name;
            })
            ->addColumn('category_name', function(Expense $Expense) {
                return $Expense->category ? $Expense->category->displayName() : '--';
            })
            ->addColumn('created_by_name', function(Expense $Expense) {
                return $Expense->created_by_name->formatName();
            })
            ->addColumn('updated_by_name', function(Expense $Expense) {
                if($Expense->updated_by) {
                    return $Expense->updated_by_name->formatName();
                }
                else{
                    return '--';
                }
            })
            ->addColumn('amount_formatted', function(Expense $Expense) {
                return number_format($Expense->amount, 2);
            })
            ->addColumn('date_formatted', function(Expense $Expense) {
                return Utilities::format_date($Expense->date,'M d, Y');
            })
            ->addColumn('action', function(Expense $Expense) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                                        <a class="dropdown-item fa fa-edit" href="'.action('ExpenseController@edit',$Expense->id).'" > Edit</a>
                    <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $Expense->reference_no .' ?" data-text="This Action is irreversible." data-href="'.action('ExpenseController@destroy',$Expense->id).'" > Delete</a>      
                    </div></div>';
                 })
                ->make(true);
        }
            
        return view('expense.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('ExpenseCategoryController@index'), 'name'=>"Expenses"], ['name'=>"Expense Create"]
        ];
        $warehouses = $request->user()->business->warehouse;
        $categories = $request->user()->business->expense_categories;
        return view('expense.create',compact('breadcrumbs', 'warehouses', 'categories'));
        
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
            'warehouse_id' => 'required|exists:warehouses,id',
            'amount' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'], //float or whole number
            'attachment' => ['nullable', 'mimes:jpeg,png,xls,xlsx,csv,pdf,doc,docx'],
            'note' => 'nullable|string|max:255',
            'expense_category_id' => ['required', 'exists:expense_category,id']
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }

        try {
            DB::beginTransaction();
            $data = $request->all();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $data['reference_no'] = ($request->reference_no)?$request->reference_no:$genref->getReference_exp();
            $data['business_id'] = $request->user()->business_id;
            if (! $request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['ex' => DB::raw('ex + 1')]);
            }

            if ($request->hasFile('attachment')) {
                $image = $request->file('attachment');
                $data['attachment'] = sha1(time()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/expenses') , $data['attachment']);
            }else{
                $data['attachment'] = null;
            }

            $data['created_by'] = $request->user()->id;

            $data['date'] = Carbon::parse($request->date)->format('Y-m-d');

            Expense::create($data);
            $output = ['success' => 1,
                'msg' => 'Expense added successfully!',
                'redirect' => action('ExpenseController@index')
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
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(Expense $expense, Request $request)
    {
        if($expense->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this expense');
        }

        $warehouses = $request->user()->business->warehouse;
        $categories = $request->user()->business->expense_categories;
        return view('expense.edit', compact('categories', 'warehouses', 'expense'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'amount' => ['required', 'regex:/^\d*(\.\d{1,2})?$/'], //float or whole number
            'attachment' => ['nullable', 'mimes:jpeg,png,xls,xlsx,csv,pdf,doc,docx'],
            'note' => 'nullable|string|max:255',
            'expense_category_id' => ['required', 'exists:expense_category,id']
        ]);
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }

        try {
            DB::beginTransaction();
            $data = $request->all();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $data['reference_no'] = ($request->reference_no)?$request->reference_no:$genref->getReference_exp();
            if (! $request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['ex' => DB::raw('ex + 1')]);
            }

            if ($request->hasFile('attachment')) {
                $image = $request->file('attachment');
                $data['attachment'] = sha1(time()) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/expenses') , $data['attachment']);
                File::delete('images/expenses/' . $expense->attachment);
            }else{
                $data['attachment'] = $expense->attachment;
            }

            $data['updated_by'] = $request->user()->id;

            $data['date'] = Carbon::parse($request->date)->format('Y-m-d');

            $expense->update($data);
            $output = ['success' => 1,
                'msg' => 'Expense updated successfully!',
                'redirect' => action('ExpenseController@index')
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
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        if($expense->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this expense');
        }

        try {
            DB::beginTransaction();
            File::delete('images/expenses/' . $expense->attachment);
            $expense->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Expense successfully deleted!'
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

    public function massDelete(Request $request){
        $ids = $request->ids;
        $expenses = Expense::whereIn('id', $ids)->where('business_id', $request->user()->business_id)->get();
        foreach($expenses as $expense){
            File::delete('images/expenses/' . $expense->attachment);
            $expense->delete();
        }
        $output = ['success' => 1,
                        'msg' => 'Successfully deleted expenses.',
                    ];
        return response()->json($output);
    }
}

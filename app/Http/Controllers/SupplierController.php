<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Suppliers/Billers"], ['name'=>"List of Suppliers/Billers"]
        ];
        
        if ( request()->ajax()) {
            
            $business_id = Auth::user()->business_id;
            
               
           $Supplier = Supplier::where('business_id','=',$business_id)->orderBy('updated_at', 'desc');
           
            return Datatables::eloquent($Supplier)
            ->addColumn('balance', function(Supplier $supplier) {
                $balance = 0;
                foreach ($supplier->purchases as $purchase) {
                    if (in_array($purchase->payment_status, ['pending', 'partial']) && $purchase->status == 'received') {
                      $balance += $purchase->grand_total - $purchase->paid;
                    }
                }
                foreach ($supplier->expenses as $expense) {
                    if (in_array($expense->payment_status, ['pending', 'partial'])) {
                      $balance += $expense->amount - $expense->paid;
                    }
                }
                return number_format($balance, 2);
            })
            ->addColumn('action', function(Supplier $supplier) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                            Action<span class="sr-only">Toggle Dropdown</span></button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item" href="'. action('SupplierController@show', $supplier->id) .'"><i class="fa fa-eye aria-hidden="true""></i> View</a>
                            <a class="dropdown-item fa fa-edit" href="'.route('supplier.edit',['supplier'=>$supplier->id]).'" > Edit</a>
                            <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $supplier->name .' ?" data-text="This Action is irreversible." data-href="'.route('supplier.delete', ['id'=>$supplier->id]).'" > Delete</a>
                            </div>
                            </div>';
                        })
            ->rawColumns(['action'])
            ->make(true);
        }
        
        return view('supplier.index', [
            'breadcrumbs' => $breadcrumbs,
            'all_shops' => array(),
            'statuses' => array(),
        ]);
    }

    public function addSupplierModal(Request $request) {
        $business_id = Auth::user()->business_id;
        $select_id = ($request->id)?$request->id:'select_supplier';
        return view('supplier.modal.create', compact('price_group', 'select_id'));
    }

    public function addSupplierAjax(Request $request) {
        $validator = Validator::make($request->all(),[
            'company' => 'required',
            'contact_person' => 'required',
            'phone' => 'present',
            'email' => 'present'
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            
            DB::beginTransaction();
            $data = $request->all();
            $data['business_id'] = $request->user()->business_id;
            $supplier = Supplier::create($data);
            if ($supplier->id) {
                $output = ['success' => 1,
                    'select_id' => $request->select_id,
                    'option_id' => $supplier->id,
                    'option_name' => $supplier->company,
                    'msg' => 'Supplier added successfully!',
                    'redirect' => action('SupplierController@index')
                ];
                DB::commit();
            }

          
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $output = ['success' => 0,
                        'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
                    ];
             DB::rollBack();
        }
        return response()->json($output);
    }


    public function add_ajax(Request $request){
        $request->validate([
            'company' => 'required',
            'contact_person' => 'required',
            'phone' => 'present',
            'email' => 'present'
        ]);
        $Supplier = new Supplier();
        $Supplier->business_id = Auth::user()->business_id;
        $Supplier->company = $request->company;
        $Supplier->contact_person = $request->contact_person;
        $Supplier->phone = $request->phone;
        $Supplier->email = $request->email;
        
        if($Supplier->save()){
            $output = ['success' => 1,
                        'msg' => "Success !",
                        'id'=>$Supplier->id
                    ];
            
        }else{
            $output = ['success' => 0,
                        'msg' => "Error !",
                    ];
        }
        
        return response()->json($output);
        
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Suppliers/Billers"], ['name'=>"Supplier/Billers Create"]
        ];
        
        
        return view('supplier.create', [
            'breadcrumbs' => $breadcrumbs
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $supplier = new Supplier();
        $supplier->business_id = Auth::user()->business_id;
        $supplier->company = $request->company;
        $supplier->contact_person = $request->contact_person;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        
        if($supplier->save()){
            $request->session()->flash('flash_success', 'Success !');
        }else{
            $request->session()->flash('flash_error',"something Went wrong !");
        }
        
        return redirect('/supplier');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Supplier List"], ['name'=>"View Supplier"]
        ];
        return view('supplier.show', compact('breadcrumbs', 'supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier, Request $request)
    {
        $business_id = Auth::user()->business_id;
        if($supplier->business_id != $business_id) {
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/supplier');
        }
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Supplier/Biller"], ['name'=>"Supplier/Biller Edit"]
        ];
        
        return view('supplier.edit', [
        'breadcrumbs' => $breadcrumbs,
        'Supplier'=>$supplier
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), ['company' => 'required','contact_person' => 'required','email' => ['unique:suppliers,email,' . $supplier->email]]);

        if ($validator->fails()) {
            $request->session()->flash('flash_error', $validator->errors());
            }
        try {
            DB::beginTransaction();
            $data['company'] = $request->company;
            $data['contact_person'] = $request->contact_person;
            $data['phone'] = $request->phone;
            $data['email'] = $request->email;
            $supplier = $supplier->update($data);
            DB::commit();
            $request->session()->flash('flash_success', 'Supplier updated successfully!');
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
            $request->session()->flash('flash_error',"Sorry something went wrong, please try again later.");
             DB::rollBack();
        }
        return redirect('/supplier');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }

    public function delete($id){
        $business_id = Auth::user()->business_id;
        $Supplier_check = Supplier::where('business_id','=',$business_id)->where('id',$id)->get()->count();
        if($Supplier_check!=1){
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/supplier');
        }
        $Supplier = Supplier::find($id);
        
        if($Supplier->delete()){
            
            $output = ['success' => 1,
                    'msg' => 'Success',
                ];
            
        }else{
            $output = ['success' => 0,
                        'msg' => "Error!",
                    ];
            
        }
        return response()->json($output);
    }

    public function bulkremove(Request $request){
        
        $ids = $request->ids;
        
        foreach($ids as $id){
            $Supplier = Supplier::find($id);
            $Supplier->delete();
            
        }
        
        $output = ['success' => 1,
                        'msg' => "success",
                    ];
        echo json_encode($output);
        
    }
}

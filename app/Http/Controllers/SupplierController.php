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
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Suppliers"], ['name'=>"List of Suppliers"]
        ];
        
        if ( request()->ajax()) {
            
            $user_id = Auth::user()->id;
            
               
           $Supplier = Supplier::where('user_id','=',$user_id)->orderBy('updated_at', 'desc');
           
            return Datatables::eloquent($Supplier)
            ->addColumn('action', function(Supplier $SUP) {
                            return '<div class="btn-group dropup mr-1 mb-1">
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                            Action<span class="sr-only">Toggle Dropdown</span></button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item fa fa-edit" href="'.route('supplier.edit',['supplier'=>$SUP->id]).'" > Edit</a>
                            <a class="dropdown-item fa fa-trash confirm" href="#"  data-text="Are you sure to delete '. $SUP->name .' ?" data-text="This Action is irreversible." data-href="'.route('supplier.delete', ['id'=>$SUP->id]).'" > Delete</a>
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


    public function add_ajax(Request $request){
        $Supplier = new Supplier();
        $Supplier->user_id = Auth::user()->id;
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Suppliers"], ['name'=>"Supplier  Create"]
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
        $supplier->user_id = Auth::user()->id;
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
        //
       return redirect('/supplier');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier, Request $request)
    {
        $user_id = Auth::user()->id;
        if($supplier->user_id != $user_id) {
            $request->session()->flash('flash_error',"Invalid Request !");
            return redirect('/supplier');
        }
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SupplierController@index'), 'name'=>"Supplier"], ['name'=>"Supplier Edit"]
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
        $validator = Validator::make($request->all(), ['email' => 'unique']);

        $validator = Validator::make($request->all(), ['email' => ['unique:suppliers,email,' . $supplier->email]]);

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
        $user_id = Auth::user()->id;
        $Supplier_check = Supplier::where('user_id','=',$user_id)->where('id',$id)->get()->count();
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

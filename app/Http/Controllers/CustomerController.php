<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CustomerController@index'), 'name'=>"Customer"], ['name'=>"Customer List"]
        ];
        if ( request()->ajax()) {
            $user = Auth::user();
           $customer = Customer::where('business_id', $user->business_id)->orderBy('updated_at', 'desc');
           // return $customer->get();
            return Datatables($customer)
            ->addColumn('customer_name', function(Customer $customer) {
                return $customer->formatName();
            })
            ->addColumn('action', function(Customer $customer) {
                    $actions = '<div class="btn-group dropup mr-1 mb-1">
                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">
                    Action<span class="sr-only">Toggle Dropdown</span></button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'. action('CustomerController@edit', $customer->id) .'"><i class="fa fa-edit aria-hidden="true""></i> Edit</a>
                        <a class="dropdown-item modal_button " href="#" data-href="'. action('CustomerController@delete', $customer->id).'" ><i class="fa fa-trash aria-hidden="true""></i> Delete</a>
                    </div></div>';
                    return $actions;
             })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('customer.index', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('CustomerController@index'), 'name'=>"Customers List"], ['name'=>"Add Customer"]
        ];
        return view('customer.create', compact('breadcrumbs'));
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:customer',
            'phone' => 'nullable',
            'price_group' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['business_id'] = $user->business_id;

            $customer = Customer::create($data);

            $output = ['success' => 1,
                'msg' => 'Customer added successfully!',
                'redirect' => action('CustomerController@index')
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
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer, Request $request)
    {
        if($customer->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this customer');
        }

        return view('customer.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
      if($customer->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this customer');
      }

       $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customer',
            'phone' => 'nullable',
            'price_group' => 'nullable',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors','error' => $validator->errors()]);
        }
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['business_id'] = Auth::user()->business_id;
            $customer = $customer->update($data);

            $output = ['success' => 1,
                'msg' => 'Customer updated successfully!',
                'redirect' => action('CustomerController@index')
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
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer, Request $request)
    {
      if($customer->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this customer');
      }
        try {
            DB::beginTransaction();
            $customer->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Customer successfully deleted!'
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

    public function delete(Customer $customer, Request $request){
      if($customer->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this customer');
      }
        $action = action('CustomerController@destroy', $customer->id);
        $title = 'customer ' . $customer->fullName();
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function addCustomerModal() {
        $business_id = Auth::user()->business_id;
        $title = "this SKU";
        return view('customer.modal.addCustomer');
    }

    public function addCustomerAjax(Request $request) {
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customer',
            'phone' => 'nullable',
            'price_group' => 'nullable',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            $data = new Customer;
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->price_group = $request->price_group;
            $data->address = $request->address;
            $data->business_id = $user->business_id;
            DB::beginTransaction();
            
            if ($data->save()) {
                $output = ['success' => 1,
                    'customer' => $data,
                    'msg' => 'Customer added successfully!',
                    'redirect' => action('CustomerController@index')
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
}
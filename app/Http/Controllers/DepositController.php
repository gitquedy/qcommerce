<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Customer;
use App\PriceGroup;
use App\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('DepositController@index'), 'name'=>"Deposit"], ['name'=>"Deposit List"]
        ];
        if ( request()->ajax()) {
         $deposit = Deposit::orderBy('updated_at', 'desc');
            return Datatables($deposit)   
            ->addColumn('customer_name', function(Deposit $deposit) {
                return $deposit->customer->formatName();
            })
            ->editColumn('created_by_name', function(Deposit $deposit) {
                return $deposit->created_by_name->formatName();
            })
            ->editColumn('updated_by_name', function(Deposit $deposit) {
                if($deposit->updated_by) {
                    return $deposit->updated_by_name->formatName();
                }
                else{
                    return '--';
                }
            })
            ->addColumn('action', function(Deposit $deposit) {
                    // $view = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('DepositController@viewDepositModal', $deposit->id).'"><i class="fa fa-eye" aria-hidden="true"></i> View Sale</a>'; 
                    $edit = '<a class="dropdown-item" href="'. action('DepositController@edit', $deposit->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';
                    $delete = '<a class="dropdown-item modal_button " href="#" data-href="'. action('DepositController@delete', $deposit->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action'])
            ->make(true);  
        }
        return view('deposit.index', [
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('DepositController@index'), 'name'=>"Deposit List"], ['name'=>"Add Deposit"]
        ];
        $customers = Customer::where('business_id', Auth::user()->business_id)->get();
        return view('deposit.create', compact('breadcrumbs','customers'));
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
            'customer_id' => 'required',
            'date' => 'required|date|max:255',
            'reference_no' => 'nullable',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            $data = new Deposit;
            $data->business_id = $user->business_id;
            $data->customer_id = $request->customer_id;
            $data->date =  date("Y-m-d H:i:s", strtotime($request->date));
            $data->reference_no = $request->reference_no;
            $data->amount = $request->amount;
            $data->note = $request->note;
            $data->created_by = $user->id;
            DB::beginTransaction();
            
            if ($data->save()) {
                $output = ['success' => 1,
                    'deposit' => $data,
                    'msg' => 'Deposit added successfully!',
                    'redirect' => action('DepositController@index')
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function show(Deposit $deposit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function edit(Deposit $deposit)
    {
        if($deposit->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this deposit');
        }
        $customers = Customer::where('business_id', Auth::user()->business_id)->get();
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('DepositController@index'), 'name'=>"Deposits List"], ['name'=>"Edit Deposit"]
        ];
        return view('deposit.edit', compact('deposit', 'breadcrumbs', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deposit $deposit)
    {
        if($deposit->business_id != Auth::user()->business_id){
              abort(401, 'You don\'t have access to edit this deposit');
          }

       $validator = Validator::make($request->all(),[
            'customer_id' => 'required',
            'date' => 'required|date|max:255',
            'reference_no' => 'required',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors','error' => $validator->errors()]);
        }
        try {
            $data = $request->all();
            DB::beginTransaction();
            $data['date'] = date("Y-m-d H:i:s", strtotime($request->date));;
            $data['updated_by'] = Auth::user()->id;
            $deposit = $deposit->update($data);

            $output = ['success' => 1,
                'msg' => 'Deposit updated successfully!',
                'redirect' => action('DepositController@index')
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
     * @param  \App\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        if($deposit->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this deposit');
        }
        try {
            DB::beginTransaction();
            if($deposit->amount <= $deposit->customer->available_deposit()) {
                $deposit->delete();
                DB::commit();
                $output = ['success' => 1,
                        'msg' => 'Deposit successfully deleted!'
                    ];
            }
            else {
                $output = ['sucess' => 0,
                        'msg' => 'Customer available deposit is lower than this deposit amount!'
                    ];
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

    public function delete(Deposit $deposit){
      if($deposit->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this deposit');
      }
        $action = action('DepositController@destroy', $deposit->id);
        $title = 'deposit ' . $deposit->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function viewDepositModal(Customer $customer) {
        return view('deposit.modal.viewDeposit', compact('customer'));
    }

    public function addDepositModal(Customer $customer) {
        return view('deposit.modal.addDeposit', compact('customer'));
    }

    public function addDepositAjax(Request $request) {
        $validator = Validator::make($request->all(),[
            'customer_id' => 'required',
            'date' => 'required|date|max:255',
            'reference_no' => 'nullable',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            $data = new Deposit;
            $data->business_id = $user->business_id;
            $data->customer_id = $request->customer_id;
            $data->date =  date("Y-m-d H:i:s", strtotime($request->date));
            $data->reference_no = $request->reference_no;
            $data->amount = $request->amount;
            $data->note = $request->note;
            $data->created_by = $user->id;
            DB::beginTransaction();
            
            if ($data->save()) {
                $output = ['success' => 1,
                    'deposit' => $data,
                    'msg' => 'Deposit added successfully!',
                    'redirect' => action('DepositController@index')
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

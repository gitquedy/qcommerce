<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Sku;
use App\Shop;
use App\Sales;
use App\SaleItems;
use App\Products;
use App\Payment;
use App\Customer;
use App\Warehouse;
use App\OrderRef;
use App\Settings;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"Sales"], ['name'=>"Sales List"]
        ];
        if ( request()->ajax()) {
           $sales = Sales::orderBy('updated_at', 'desc');
            return Datatables($sales)   
            ->addColumn('customer_name', function(Sales $sales) {
                if($sales->customer_id) {
                    return $sales->customer->formatName();
                }
                else {
                    return $sales->customer_last_name.', '.$sales->customer_first_name;
                }
            })   
            ->addColumn('balance', function(Sales $sales) {
                return number_format($sales->grand_total - $sales->paid, 2);
            })
            ->editColumn('grand_total', function(Sales $sales) {
                return number_format($sales->grand_total, 2);
            })
            ->editColumn('paid', function(Sales $sales) {
                return number_format($sales->paid, 2);
            })
            ->editColumn('status', function(Sales $sales) {
                switch ($sales->status) {
                    case 'completed':
                            return '<span class="badge badge-success">Complete</span>';
                        break;
                    case 'pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        break;
                    case 'canceled':
                            return '<span class="badge badge-danger">Canceled</span>';
                        break;
                    
                    default:
                            return '<span class="badge badge-secondary">Unknown</span>';
                        break;
                }
            })
            ->editColumn('payment_status', function(Sales $sales) {
                switch ($sales->payment_status) {
                    case 'paid':
                            return '<span class="badge badge-pill badge-success">Paid</span>';
                        break;
                    case 'pending':
                            return '<span class="badge badge-pill badge-warning">Pending</span>';
                        break;
                    case 'partial':
                            return '<span class="badge badge-pill badge-info">Partial</span>';
                        break;
                    case 'due':
                            return '<span class="badge badge-pill badge-danger">Due</span>';
                        break;
                    
                    default:
                            return '<span class="badge badge-pill badge-secondary">Unknown</span>';
                        break;
                }
            })
            ->addColumn('action', function(Sales $sales) {
                    $view = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('SalesController@viewSalesModal', $sales->id).'"><i class="fa fa-eye" aria-hidden="true"></i> View Sale</a>';
                    if($sales->payment_status != 'paid') {
                        $add_payment = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('PaymentController@addPaymentModal', $sales->id).'"><i class="fa fa-dollar" aria-hidden="true"></i> Add Payment</a>';
                    }
                    else {
                        $add_payment = '';
                    }

                    if ($sales->status == 'pending') {
                        $edit = '<a class="dropdown-item" href="'. action('SalesController@edit', $sales->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';
                    }
                    else {
                        $edit = '';
                    }
                    $view_payments = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('PaymentController@viewPaymentModal', $sales->id).'"><i class="fa fa-money" aria-hidden="true"></i> View Payments</a>';

                    $delete = '<a class="dropdown-item modal_button " href="#" data-href="'. action('SalesController@delete', $sales->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$view.$add_payment.$view_payments.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action','status','payment_status'])
            ->make(true);
        }
        return view('sales.index', [
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('SalesController@index'), 'name'=>"Sales List"], ['name'=>"Add Sales"]
        ];
        $warehouses = Warehouse::where('business_id', Auth::user()->business_id)->get();
        $customers = Customer::where('business_id', Auth::user()->business_id)->get();
        return view('sales.create', compact('breadcrumbs','customers','warehouses'));
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
            'customer_id' => 'required',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'paid' => 'nullable',
            'sales_item_array' => 'required|array',
            'payment_reference_no' => 'nullable',
            'payment_type' => Rule::requiredIf($request->paid > 0),
            'gift_card_no' => Rule::requiredIf($request->payment_type == 'gift_certificate' && $request->paid > 0),
            'cc_no' => Rule::requiredIf($request->payment_type == 'credit_card' && $request->paid > 0),
            'cc_holder' => Rule::requiredIf($request->payment_type == 'credit_card' && $request->paid > 0),
            'cc_type' => Rule::requiredIf($request->payment_type == 'credit_card' && $request->paid > 0),
            'cc_month' => Rule::requiredIf($request->payment_type == 'credit_card' && $request->paid > 0),
            'cc_year' => Rule::requiredIf($request->payment_type == 'credit_card' && $request->paid > 0),
            'cheque_no' => Rule::requiredIf($request->payment_type == 'cheque' && $request->paid > 0),
            'payment_note' => 'nullable',
        ],
        [
            'sales_item_array.required' => 'Please add Items.',
        ]);
        if ($request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
        }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        elseif ($request->payment_type == 'deposit' && $request->paid > $customer->available_deposit()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => ['paid' => ['Insufficient Deposit balance']]]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $customer = Customer::where('business_id', $user->business_id)->where('id', $request->customer_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $total = 0;
            $sales = new Sales;
            $sales->business_id = $user->business_id;
            $sales->customer_id = $request->customer_id;
            $sales->warehouse_id = $request->warehouse_id;
            $sales->customer_first_name = $customer->first_name;
            $sales->customer_last_name = $customer->last_name;
            $sales->date = date("Y-m-d H:i:s", strtotime($request->date));
            $sales->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_so();
            $sales->note = $request->note;
            $sales->status = $request->status;
            $sales->discount = ($request->discount)?$request->discount:0;
            $sales->paid = $request->paid;
            $sales->created_by = $user->id;
            foreach ($request->sales_item_array as $item) {
                $sub_total = $item['price'] * $item['quantity'];
                $total+= $sub_total;
            }

            if($total == $request->paid) {
                $payment_status = "paid";
            }
            else if($request->paid == 0) {
                $payment_status = "pending";
            }
            else if($request->paid > 0 && $request->paid < $total) {
                $payment_status = "partial";
            }

            $sales->payment_status = $payment_status;
            $sales->total = $total;
            $sales->grand_total = $total - $request->discount;
            $sales->save();
            $sales_items = [];
            foreach ($request->sales_item_array as $id => $item) {
                $sales_item = [];
                $sales_item['sales_id'] = $sales->id;
                $sales_item['sku_id'] = $id;
                $sales_item['sku_code'] = $item['code'];
                $sales_item['sku_name'] = $item['name'];
                $sales_item['image'] = $item['image'];
                $sales_item['unit_price'] = $item['price'];
                $sales_item['quantity'] = $item['quantity'];
                $sales_item['discount'] = 0;
                $sales_item['subtotal'] = $item['price'] * $item['quantity'];
                $sales_item['real_unit_price'] = $item['real_unit_price'];
                $sales_items[] = $sales_item;

            }
            if($request->status == 'completed') {
                Sku::syncStocks($request->warehouse_id ,$request->sales_item_array);   
            }
            $sales_items_query = SaleItems::insert($sales_items);
            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['so' => DB::raw('so + 1')]);
            }
            if($request->paid) {
                $payment = new Payment;
                $payment->sales_id = $sales->id;
                $payment->customer_id = $sales->customer_id;
                $payment->date =  date("Y-m-d H:i:s", strtotime($request->date));
                $payment->reference_no = ($request->payment_reference_no)?$request->payment_reference_no:$genref->getReference_pay();
                $payment->amount = $request->paid;
                $payment->payment_type = $request->payment_type;
                $payment->gift_card_no = $request->gift_card_no;
                $payment->cc_no = $request->cc_no;
                $payment->cc_holder = $request->cc_holder;
                $payment->cc_type = $request->cc_type;
                $payment->cc_month = $request->cc_month;
                $payment->cc_year = $request->cc_year;
                $payment->cheque_no = $request->cheque_no;
                $payment->status = 'received';
                $payment->note = $request->note;
                $payment->created_by = $user->id;
                $payment->save();
                if (!$request->payment_reference_no) {
                    $increment = OrderRef::where('settings_id', $genref->id)->update(['pay' => DB::raw('pay + 1')]);
                }
            }
            $output = ['success' => 1,
                'msg' => 'Sale added successfully!',
                'redirect' => action('SalesController@index')
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $sales = Sales::findOrFail($id);
        $warehouses = Warehouse::where('business_id', Auth::user()->business_id)->get();
        $customers = Customer::where('business_id', Auth::user()->business_id)->get();
        if($sales->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this sales');
        }
        // print json_encode($sales->items);die();
        return view('sales.edit', compact('sales', 'customers', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $sales = Sales::findOrFail($id);
        $old_status = $sales->status;
        $old_warehouse = $sales->warehouse_id;
        if($sales->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this customer');
        }

        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            'customer_id' => 'required',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'sales_item_array' => 'required|array',
        ],
        [
            'sales_item_array.required' => 'Please add Items.',
        ]);

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            DB::beginTransaction();
            $user = Auth::user();
            $customer = Customer::where('business_id', $user->business_id)->where('id', $request->customer_id)->first();
            $total = 0;
            $sales->business_id = $user->business_id;
            $sales->customer_id = $request->customer_id;
            $sales->warehouse_id = $request->warehouse_id;
            $sales->customer_first_name = $customer->first_name;
            $sales->customer_last_name = $customer->last_name;
            $sales->date = date("Y-m-d H:i:s", strtotime($request->date));
            if ($request->reference_no) {
                $sales->reference_no = $request->reference_no;
            }
            $sales->note = $request->note;
            $sales->status = $request->status;
            $sales->discount = ($request->discount)?$request->discount:0;
            $sales->updated_by = $user->id;

            //future :: return all stocks then delete
                //return stocks code here
            SaleItems::where('sales_id', $sales->id)->delete();
            foreach ($request->sales_item_array as $item) {
                $sub_total = $item['price'] * $item['quantity'];
                $total+= $sub_total;
            }

            if($total == $request->paid) {
                $payment_status = "paid";
            }
            else if($request->paid == 0) {
                $payment_status = "pending";
            }
            else if($request->paid > 0 && $request->paid < $total) {
                $payment_status = "partial";
            }

            $sales->payment_status = $payment_status;
            $sales->total = $total;
            $sales->grand_total = $total - $request->discount;
            $sales->save();
            $sales_items = [];
            foreach ($request->sales_item_array as $id => $item) {
                $sales_item = [];
                $sales_item['sales_id'] = $sales->id;
                $sales_item['sku_id'] = $id;
                $sales_item['sku_code'] = $item['code'];
                $sales_item['sku_name'] = $item['name'];
                $sales_item['image'] = $item['image'];
                $sales_item['unit_price'] = $item['price'];
                $sales_item['quantity'] = $item['quantity'];
                $sales_item['discount'] = 0;
                $sales_item['subtotal'] = $item['price'] * $item['quantity'];
                $sales_item['real_unit_price'] = $item['real_unit_price'];
                $sales_items[] = $sales_item;

            }
            if($old_status != 'completed' && $request->status == 'completed') {
                Sku::syncStocks($request->warehouse_id, $request->sales_item_array);
            }
            else if($old_status == 'completed' && $request->status == 'completed') {
                Sku::returnStocks($old_warehouse, $sales->items);
                Sku::syncStocks($request->warehouse_id, $request->sales_item_array);
            }
            else if($old_status == 'completed' && $request->status != 'completed') {
                 Sku::returnStocks($old_warehouse, $sales->items);
            }
            SaleItems::insert($sales_items);
            $output = ['success' => 1,
                'msg' => 'Sale updated successfully!',
                'redirect' => action('SalesController@index')
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
    public function destroy($id)
    {
        $sales = Sales::findOrFail($id);
        if ($sales->status == 'completed') {
            Sku::returnStocks($sales->warehouse_id, $sales->items);
        }
        if($sales->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this sale');
        }
        try {
            DB::beginTransaction();
            $sales->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Sale successfully deleted!'
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

    public function delete(Sales $sales, Request $request){
      if($sales->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this sales');
      }
        $action = action('SalesController@destroy', $sales->id);
        $title = 'Sale ' . $sales->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function viewSalesModal(Sales $sales, Request $request) {
        $business_id = Auth::user()->business_id;
        $payments = Payment::where('sales_id', $sales->id)->get();
        return view('sales.modal.viewSales', compact('sales','payments'));
    }
}

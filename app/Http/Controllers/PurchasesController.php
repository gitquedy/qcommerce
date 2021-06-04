<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Sku;
use App\Shop;
use App\Purchases;
use App\PurchaseItems;
use App\Products;
use App\Payment;
use App\Supplier;
use App\OrderRef;
use App\Settings;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PurchasesController@index'), 'name'=>"Purchases"], ['name'=>"Purchases List"]
        ];
        if ( request()->ajax()) {
           $purchases = Purchases::where('business_id', Auth::user()->business_id)->orderBy('updated_at', 'desc');
            return Datatables($purchases)   
            ->addColumn('balance', function(Purchases $purchase) {
                return number_format($purchase->grand_total - $purchase->paid, 2);
            })
            ->editColumn('grand_total', function(Purchases $purchase) {
                return number_format($purchase->grand_total, 2);
            })
            ->editColumn('paid', function(Purchases $purchase) {
                return number_format($purchase->paid, 2);
            })
            ->editColumn('status', function(Purchases $purchase) {
                switch ($purchase->status) {
                    case 'received':
                            return '<span class="badge badge-success">Received</span>';
                        break;
                    case 'pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        break;
                    case 'ordered':
                            return '<span class="badge badge-danger">Ordered</span>';
                        break;
                    
                    default:
                            return '<span class="badge badge-secondary">Unknown</span>';
                        break;
                }
            })
            ->editColumn('payment_status', function(Purchases $purchase) {
                switch ($purchase->payment_status) {
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
            ->addColumn('action', function(Purchases $purchase) {
                    $view = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('PurchasesController@viewPurchasesModal', $purchase->id).'"><i class="fa fa-eye" aria-hidden="true"></i> View Purchase</a>';
                    if($purchase->payment_status != 'paid') {
                        $add_payment = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('PaymentController@addPaymentModal', ['type' => 'Purchases', 'id' =>  $purchase->id]).'"><i class="fa fa-dollar" aria-hidden="true"></i> Add Payment</a>';
                    }
                    else {
                        $add_payment = '';
                    }

                    if ($purchase->status == 'pending' || $purchase->status == 'ordered') {
                        $edit = '<a class="dropdown-item" href="'. action('PurchasesController@edit', $purchase->id) .'"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a>';
                    }
                    else {
                        $edit = '';
                    }
                    $view_payments = '<a class="dropdown-item toggle_view_modal" href="" data-action="'.action('PaymentController@viewPaymentModal',['type' => 'Purchases', 'id' =>  $purchase->id]).'"><i class="fa fa-money" aria-hidden="true"></i> View Payments</a>';

                    $delete = '<a class="dropdown-item modal_button " href="#" data-href="'. action('PurchasesController@delete', $purchase->id).'" ><i class="fa fa-trash" aria-hidden="true"></i> Delete</a>';

                    $actions = '<div class="btn-group dropup mr-1 mb-1"><button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown"aria-haspopup="true" aria-expanded="false">Action<span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu">'.$view.$add_payment.$view_payments.$edit.$delete.'</div></div>';
                    return $actions;
             })
            ->rawColumns(['action','status','payment_status'])
            ->make(true);
        }
        return view('purchases.index', [
            'breadcrumbs' => $breadcrumbs,
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
            ['link'=>"/",'name'=>"Home"],['link'=> action('PurchasesController@index'), 'name'=>"Purchases List"], ['name'=>"Add Purchase"]
        ];
        $warehouses = $request->user()->business->warehouse->where('status', 1);
        $suppliers = $request->user()->business->suppliers;
        return view('purchases.create', compact('breadcrumbs','suppliers','warehouses'));
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
            'supplier_id' => 'required',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'paid' => 'nullable',
            'purchases_items_array' => 'required|array',
            'payment_reference_no' => 'nullable',
            'grand_total' => 'required|integer|min:0',
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
            'purchases_items_array.required' => 'Please add Items.',
        ]);
        if ($request->supplier_id) {
            $supplier = Supplier::findOrFail($request->supplier_id);
        }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        // try {
            DB::beginTransaction();
            $user = Auth::user();
            $supplier = Supplier::where('business_id', $user->business_id)->where('id', $request->supplier_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $total = 0;
            $purchase = new Purchases;
            $purchase->business_id = $user->business_id;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->warehouse_id = $request->warehouse_id;
            $purchase->supplier_name = $supplier->company;
            $purchase->date = date("Y-m-d H:i:s", strtotime($request->date));
            $purchase->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_po();
            $purchase->note = $request->note;
            $purchase->status = $request->status;
            $purchase->discount = ($request->discount)?$request->discount:0;
            $purchase->paid = $request->paid;
            $purchase->shipping_fee = $request->shipping_fee;
            $purchase->other_fees = $request->other_fees;
            $purchase->discount = $request->discount;
            $purchase->created_by = $user->id;

            foreach ($request->purchases_items_array as $item) {
                $sub_total = $item['price'] * $item['quantity'];
                $total+= $sub_total;
            }

            $purchase->total = $total;
            $grand_total = ($total + $request->shipping_fee + $request->other_fees) - $request->discount;
            $purchase->grand_total = $grand_total;
            if($grand_total == $request->paid) {
                $payment_status = "paid";
            }
            else if($request->paid == 0) {
                $payment_status = "pending";
            }
            else if($request->paid > 0 && $request->paid < $grand_total) {
                $payment_status = "partial";
            }

            $purchase->payment_status = $payment_status;
            $purchase->save();
            $purchase_items = [];
            foreach ($request->purchases_items_array as $item) {
                $purchase_item = [];
                $purchase_item['purchases_id'] = $purchase->id;
                $purchase_item['sku_id'] = $item['sku_id'];
                $purchase_item['sku_code'] = $item['code'];
                $purchase_item['sku_name'] = $item['name'];
                $purchase_item['image'] = $item['image'];
                $purchase_item['unit_price'] = $item['price'];
                $purchase_item['quantity'] = $item['quantity'];
                $purchase_item['discount'] = 0;
                $purchase_item['subtotal'] = $item['price'] * $item['quantity'];
                $purchase_item['real_unit_price'] = $item['real_unit_price'];
                $purchase_items[] = $purchase_item;
            }
            $sales_items_query = PurchaseItems::insert($purchase_items);
            if($request->status == 'received') {
                Sku::returnStocks($request->warehouse_id , $purchase->items);   
            }
            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['po' => DB::raw('po + 1')]);
            }
            if($request->paid) {
                $payment = new Payment;
                $payment->people_id = $purchase->supplier_id;
                $payment->people_type = "Supplier";
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
                $purchase->payments()->save($payment);
                if (!$request->payment_reference_no) {
                    $increment = OrderRef::where('settings_id', $genref->id)->update(['pay' => DB::raw('pay + 1')]);
                }
            }
            $output = ['success' => 1,
                'msg' => 'Purchase added successfully!',
                'redirect' => action('PurchasesController@index')
            ];
            DB::commit();
        // } catch (\Exception $e) {
        //     \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
        //     $output = ['success' => 0,
        //                 'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
        //             ];
        //      DB::rollBack();
        // }
        return response()->json($output);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Purchases  $purchases
     * @return \Illuminate\Http\Response
     */
    public function show(Purchases $purchases)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Purchases  $purchases
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchases $purchase, Request $request)
    {
        if($purchase->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to edit this purchase');
        }
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PurchasesController@index'), 'name'=>"Purchases List"], ['name'=>"Edit Purchase"]
        ];
        $warehouses = $request->user()->business->warehouse->where('status', 1);
        $suppliers = $request->user()->business->suppliers;
        return view('purchases.edit', compact('breadcrumbs','suppliers','warehouses', 'purchase'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Purchases  $purchases
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchases $purchase)
    {
        $validator = Validator::make($request->all(),[
            'date' => 'required|date',
            'reference_no' => 'nullable|string|max:255',
            // 'supplier_id' => 'required',
            'status' => 'required',
            'note' => 'nullable|string|max:255',
            'paid' => 'nullable',
            'purchases_items_array' => 'required|array',
            'payment_reference_no' => 'nullable',
            'grand_total' => 'required|integer|min:0',
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
            'purchases_items_array.required' => 'Please add Items.',
        ]);
        // if ($request->supplier_id) {
        //     $supplier = Supplier::findOrFail($request->supplier_id);
        // }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        try {
            $old_status = $purchase->status;
            DB::beginTransaction();
            $user = Auth::user();
            // $supplier = Supplier::where('business_id', $user->business_id)->where('id', $request->supplier_id)->first();
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $total = 0;
            // $data['business_id'] = $user->business_id;
            $data['supplier_id'] = $request->supplier_id;
            $data['warehouse_id'] = $request->warehouse_id;
            // $data['supplier_name'] = $supplier->company;
            $data['date'] = date("Y-m-d H:i:s", strtotime($request->date));
            $data['reference_no'] = ($request->reference_no)?$request->reference_no:$genref->getReference_po();
            $data['note'] = $request->note;
            $data['status'] = $request->status;
            $data['discount'] = ($request->discount)?$request->discount:0;
            $data['paid'] = $request->paid;
            $data['shipping_fee'] = $request->shipping_fee;
            $data['other_fees'] = $request->other_fees;
            $data['discount'] = $request->discount;
            $data['updated_by'] = $user->id;
            $data['paid'] = $purchase->paid;

            foreach ($request->purchases_items_array as $item) {
                $sub_total = $item['price'] * $item['quantity'];
                $total+= $sub_total;
            }

            $data['total'] = $total;
            $grand_total = ($total + $request->shipping_fee + $request->other_fees) - $request->discount;
            $data['grand_total'] = $grand_total;
            if($grand_total == $request->paid) {
                $payment_status = "paid";
            }
            else if($request->paid == 0) {
                $payment_status = "pending";
            }
            else if($request->paid > 0 && $request->paid < $grand_total) {
                $payment_status = "partial";
            }

            $purchase->payment_status = $payment_status;
            $purchase->update($data);
            $purchase->items()->delete();
            $purchase_items = [];
            foreach ($request->purchases_items_array as $item) {
                $purchase_item = [];
                $purchase_item['purchases_id'] = $purchase->id;
                $purchase_item['sku_id'] = $item['sku_id'];
                $purchase_item['sku_code'] = $item['code'];
                $purchase_item['sku_name'] = $item['name'];
                $purchase_item['image'] = $item['image'];
                $purchase_item['unit_price'] = $item['price'];
                $purchase_item['quantity'] = $item['quantity'];
                $purchase_item['discount'] = 0;
                $purchase_item['subtotal'] = $item['price'] * $item['quantity'];
                $purchase_item['real_unit_price'] = $item['real_unit_price'];
                $purchase_items[] = $purchase_item;
            }
            $sales_items_query = PurchaseItems::insert($purchase_items);
            if($request->status == 'received' && $old_status != 'received') {
                Sku::returnStocks($request->warehouse_id , $purchase->items);   
            }
            if (!$request->reference_no) {
                $increment = OrderRef::where('settings_id', $genref->id)->update(['po' => DB::raw('po + 1')]);
            }
            $output = ['success' => 1,
                'msg' => 'Purchase updated successfully!',
                'redirect' => action('PurchasesController@index')
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
     * @param  \App\Purchases  $purchases
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchases $purchases)
    {
        
        if($purchases->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this purchase');
        }
        try {
            DB::beginTransaction();
            if ($purchases->status == 'received') {
                Sku::syncStocks($purchases->warehouse_id, $purchases->items->toArray());
            }
            $purchases->payments()->delete();
            $purchases->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Purchases successfully deleted!'
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

    public function viewPurchasesModal(Purchases $purchases){
        $business_id = Auth::user()->business_id;
        $payments = $purchases->payments;
        return view('purchases.modal.viewPurchase', compact('purchases','payments'));
    }

    public function delete(Purchases $purchases, Request $request){
      if($purchases->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to delete this purchase');
      }
        $action = action('PurchasesController@destroy', $purchases->id);
        $title = 'Purchase ' . $purchases->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }
}

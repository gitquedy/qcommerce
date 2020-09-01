<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Sales;
use App\Payment;
use App\Customer;
use App\Settings;
use App\OrderRef;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        $sales = $payment->sale;
        if($sales->business_id != Auth::user()->business_id){
            abort(401, 'You don\'t have access to edit this sale');
        }
        try {
            DB::beginTransaction();
            $sales->paid -= $payment->amount;
            if($sales->grand_total != $sales->paid && $sales->paid == 0) {
                $sales->payment_status = "pending";
            }
            else if($sales->grand_total != $sales->paid && $sales->paid != 0) {
                $sales->payment_status = "partial";
            }
            $sales->save();
            $payment->delete();
            DB::commit();
            $output = ['success' => 1,
                        'msg' => 'Payment successfully deleted!'
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

    public function delete(Request $request, Payment $payment){
      if($payment->sale->business_id != Auth::user()->business_id){
          abort(401, 'You don\'t have access to delete this payment');
      }
        $action = action('PaymentController@destroy', $payment);
        $title = 'Payment ' . $payment->reference_no;
        return view('layouts.delete', compact('action' , 'title'));
    }

    public function viewPaymentModal(Sales $sales, Request $request) {
        $business_id = Auth::user()->business_id;
        $payments = Payment::where('sales_id', $sales->id)->get();
        return view('payment.modal.viewPayment', compact('sales','payments'));
    }

    public function addPaymentModal(Sales $sales, Request $request) {
        $business_id = Auth::user()->business_id;
        return view('payment.modal.addPayment', compact('sales'));
    }

    public function addPaymentAjax(Request $request) {
        $validator = Validator::make($request->all(),[
            'sales_id' => 'required',
            'customer_id' => 'required',
            'date' => 'required|date|max:255',
            'reference_no' => 'nullable',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required',
            'gift_card_no' => Rule::requiredIf($request->payment_type == 'gift_certificate'),
            'cc_no' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_holder' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_type' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_month' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_year' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cheque_no' => Rule::requiredIf($request->payment_type == 'cheque'),
            'note' => 'nullable',
        ]);
        if ($request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
        }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        elseif ($request->payment_type == 'deposit' && $request->amount > $customer->available_deposit()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => ['amount' => ['Insufficient Deposit balance']]]);
        }
        $user = Auth::user();
        try {
            $data = $request->all();
            $data = new Payment;
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $data->sales_id = $request->sales_id;
            $data->customer_id = $request->customer_id;
            $data->date =  date("Y-m-d H:i:s", strtotime($request->date));
            $data->reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_pay();
            $data->amount = $request->amount;
            $data->payment_type = $request->payment_type;
            $data->gift_card_no = $request->gift_card_no;
            $data->cc_no = $request->cc_no;
            $data->cc_holder = $request->cc_holder;
            $data->cc_type = $request->cc_type;
            $data->cc_month = $request->cc_month;
            $data->cc_year = $request->cc_year;
            $data->cheque_no = $request->cheque_no;
            $data->status = 'received';
            $data->note = $request->note;
            $data->created_by = $user->id;
            DB::beginTransaction();
            
            if ($data->save()) {
                if (!$request->reference_no) {
                    $increment = OrderRef::where('settings_id', $genref->id)->update(['pay' => DB::raw('pay + 1')]);
                }
                $sale = Sales::whereId($request->sales_id)->first();
                $sale->paid += $request->amount;
                if($sale->grand_total == $sale->paid) {
                    $sale->payment_status = "paid";
                }
                else if($sale->paid == 0) {
                    $sale->payment_status = "pending";
                }
                else if($sale->paid > 0 && $sale->paid < $sale->grand_total) {
                    $sale->payment_status = "partial";
                }
                $sale->save();
                $output = ['success' => 1,
                    'customer' => $data,
                    'msg' => 'Payment added successfully!',
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

    public function addMultiPaymentModal(Customer $customer, Request $request) {
        $business_id = Auth::user()->business_id;
        $sales = $customer->sales()->whereIn('id', $request->ids)->get();
        return view('payment.modal.addMultiPayment', compact('sales', 'customer'));
    }

    public function addMultiPaymentAjax(Request $request) {
        $validator = Validator::make($request->all(),[
            'sales_id' => 'required',
            'customer_id' => 'required',
            'date' => 'required|date|max:255',
            'reference_no' => 'nullable',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required',
            'gift_card_no' => Rule::requiredIf($request->payment_type == 'gift_certificate'),
            'cc_no' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_holder' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_type' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_month' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cc_year' => Rule::requiredIf($request->payment_type == 'credit_card'),
            'cheque_no' => Rule::requiredIf($request->payment_type == 'cheque'),
            'note' => 'nullable',
        ]);
        if ($request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
        }
        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        }
        elseif ($request->payment_type == 'deposit' && $request->amount > $customer->available_deposit()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => ['amount' => ['Insufficient Deposit balance']]]);
        }
        $user = Auth::user();
        try {
            $continue = true;
            $total_paid = $request->amount;
            $genref = Settings::where('business_id', Auth::user()->business_id)->first();
            $reference_no = ($request->reference_no)?$request->reference_no:$genref->getReference_pay();

            DB::beginTransaction();
            $sales = $customer->sales()->whereIn('id', $request->sales_id)->get();
            foreach ($sales as $sale) {
              if($total_paid != 0) {
                $amount_paid = 0;
                if($total_paid < ($sale->grand_total - $sale->paid)) {
                  $amount_paid = $total_paid;
                  $total_paid = 0;
                }
                else {
                  $amount_paid = ($sale->grand_total - $sale->paid);
                  $total_paid -= $amount_paid;
                }
                $sale->paid += $amount_paid;
                if($sale->grand_total == $sale->paid) {
                    $sale->payment_status = "paid";
                }
                else if($sale->paid == 0) {
                    $sale->payment_status = "pending";
                }
                else if($sale->paid > 0 && $sale->paid < $sale->grand_total) {
                    $sale->payment_status = "partial";
                }
                $sale->save();


                $result = false;

                $data = new Payment;
                $data->sales_id = $sale->id;
                $data->customer_id = $request->customer_id;
                $data->date =  date("Y-m-d H:i:s", strtotime($request->date));
                $data->reference_no = $reference_no;
                $data->amount = $amount_paid;
                $data->payment_type = $request->payment_type;
                $data->gift_card_no = $request->gift_card_no;
                $data->cc_no = $request->cc_no;
                $data->cc_holder = $request->cc_holder;
                $data->cc_type = $request->cc_type;
                $data->cc_month = $request->cc_month;
                $data->cc_year = $request->cc_year;
                $data->cheque_no = $request->cheque_no;
                $data->status = 'received';
                $data->note = $request->note;
                $data->created_by = $user->id;
                $result = $data->save();
                if($continue && !$result) {
                  $continue = false;
                }
              }
            }
            
            if ($continue) {
                if (!$request->reference_no) {
                    $increment = OrderRef::where('settings_id', $genref->id)->update(['pay' => DB::raw('pay + 1')]);
                }
                $output = ['success' => 1,
                    'customer' => $data,
                    'msg' => 'Payment added successfully!',
                    'reload' => true,
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

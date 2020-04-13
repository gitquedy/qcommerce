<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Sales;
use App\Payment;
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
        //
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

        if ($validator->fails()) {
            return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
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
}

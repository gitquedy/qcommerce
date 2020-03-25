<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use App\Sales;
use App\Payment;
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

    public function addPaymentModal(Sales $sales, Request $request) {
        $business_id = Auth::user()->business_id;
        return view('payment.modal.addPayment', compact('sales'));
    }

    public function addPaymentAjax(Request $request) {
         $output = ['success' => 1,
                    'msg' => 'Payment data recieved, but not stored This is a Work in Progess!',
                    'redirect' => action('PaymentController@index')
                ];
        return response()->json($output);
        // $validator = Validator::make($request->all(),[
        //     'first_name' => 'required|string|max:255',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['msg' => 'Please check for errors' ,'error' => $validator->errors()]);
        // }
        // $user = Auth::user();
        // try {
        //     $data = $request->all();
        //     $data = new Payment;
        //     $data->first_name = $request->first_name;
        //     $data->last_name = $request->last_name;
        //     $data->email = $request->email;
        //     $data->phone = $request->phone;
        //     $data->price_group = $request->price_group;
        //     $data->address = $request->address;
        //     $data->business_id = $user->business_id;
        //     DB::beginTransaction();
            
        //     if ($data->save()) {
        //         $output = ['success' => 1,
        //             'customer' => $data,
        //             'msg' => 'Payment added successfully!',
        //             'redirect' => action('PaymentController@index')
        //         ];
        //         DB::commit();
        //     }

          
        // } catch (\Exception $e) {
        //     \Log::emergency("File:" . $e->getFile(). " Line:" . $e->getLine(). " Message:" . $e->getMessage());
        //     $output = ['success' => 0,
        //                 'msg' => env('APP_DEBUG') ? $e->getMessage() : 'Sorry something went wrong, please try again later.'
        //             ];
        //      DB::rollBack();
        // }
        // return response()->json($output);
    }
}

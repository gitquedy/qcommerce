<?php

namespace App\Http\Controllers;
  
use Auth;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;
use App\Package;
use App\Plan;
use App\Promocode;
use Carbon\Carbon;
use DB;
   
class PayPalController extends Controller
{
    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Plan $plan, Request $request)
    {
        try {
            $invoice_no = Billing::getNextInvoiceNumber();
	        $data = [];
            $billing_period = $request->billing;
            $promocode_id = isset($request->promocode)?$request->promocode:null;
            $total_discount = 0;
            if($billing_period == 'Month') {
                if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost) {
                    $price = $plan->promo_monthly_cost;
                }
                else {
                    $price = $plan->monthly_cost;
                }
            }
            elseif($billing_period == 'Year') {
                if ($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost) {
                    $price = $plan->promo_yearly_cost;
                }
                else {
                    $price = $plan->yearly_cost;
                }
            }
            $desc = '1 '.$billing_period.' '.$plan->name.' Plan subscription on Qcommerce.';

            $billing_price = $price;
            if($promocode_id) {
                $promocode = Promocode::whereId($promocode_id)->where('starts_at', '<=', Carbon::now())->where('expires_at', '>=', Carbon::now())->whereRaw('uses < max_uses')->first();
                if($promocode->discount_type == "percentage") {
                    $total_discount = ($promocode->discount_amount / 100) * $price;
                }
                else if($promocode->discount_type == "fixed") {
                    if($promocode->discount_amount > $price) {
                        $total_discount = $price;
                    }
                    else {
                        $total_discount = $promocode->discount_amount;
                    }
                }

                if($promocode->discount_range == "all") {
                    $billing_price = $price - $total_discount;
                }
            } 
            $paypal_price = $price - $total_discount;
	        $data['items'] = [
	            [
	                'name' => $plan->name,
	                'price' => $paypal_price,
	                'desc'  => $desc,
	                'qty' => 1
	            ]
	        ];

	        $billing = Billing::create([
	        	'business_id' => $request->user()->business_id,
	        	'plan_id' => $plan->id,
                'billing_period' => $billing_period,
                'promocode' => $promocode_id,
                'amount' => $billing_price,
	        	'invoice_no' => $invoice_no
	        ]);

	        $data['invoice_id'] = $invoice_no;
	        $data['invoice_description'] = $desc;
	        $data['return_url'] = action('PlanController@confirm', $billing->id);
	        $data['cancel_url'] = action('PayPalController@cancel', $billing->id);
	        $data['total'] = $paypal_price;
	  
	        $provider = new ExpressCheckout;
	  
	        $response = $provider->setExpressCheckout($data, true);
	        if($response['paypal_link'] != null){
	        	$output = ['success' => 1,
                            'msg' => 'You will now redirected to paypal. Thank you',
                            'redirect' => $response['paypal_link']
                        ];
            }else{
                // $billing->delete();
            	\Log::emergency("Paypal Response:". implode(', ', $response));
            	$output = ['success' => 0,
                    'msg' => 'Sorry something went wrong, please try again later.',
                    'redirect' => action('ApplicationController@show', $plan->id)
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

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, Billing $billing)
    {
    	$billing->delete();
    	$request->session()->flash('flash_success', 'Successfully cancelled');
        return redirect(action('PlanController@index'));
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request, Billing $billing)
    {
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);
        return redirect()->route('plan.confirm', ['response' => $response]);
    }

    public function confirm(Request $request, Billing $billing) {
        $data['items'] = [
            [ 
                'name' => $billing->plan->name,
                'price' => $billing->amount,
                'desc'  =>   $request->desc,
                'qty' => 1
            ]
        ];
        $data['total'] = $billing->amount;
        $data['invoice_id'] = $billing->invoice_id;
        $data['invoice_description'] = $request->desc;
        $data['return_url'] = '';
        $data['cancel_url'] = '';

        $provider = new ExpressCheckout;
        $response = $provider->doExpressCheckoutPayment($data, $request->token, $request->payer_id);
        if(in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            $old_billing = Billing::where('business_id', Auth::user()->business_id)->where('paid_status', 1)->where('id', '!=', $billing->id)->first();
            if($old_billing) {
                $response_old = $provider->cancelRecurringPaymentsProfile($old_billing->profile_id);
                $old_billing->paid_status = 3;
                $old_billing->save();
            }

            $billing->paid_status = 1;
            $billing->payment_date = date("Y-m-d H:i:s");
            $billing->next_payment_date = date("Y-m-d H:i:s", strtotime('+ 1'.$billing->billing_period,strtotime('+1day')));
            $billing->payment_transaction_id = $response['PAYMENTINFO_0_TRANSACTIONID'];
            $billing->promocode_details->uses += 1;
            $billing->promocode_details->save();
            $startdate = Carbon::now()->toAtomString();
            $data = [
                'PROFILESTARTDATE' => $startdate,
                'DESC' => $request->desc,
                'BILLINGPERIOD' => $billing->billing_period, // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
                'BILLINGFREQUENCY' => 1, // 
                'AMT' => $billing->amount, // Billing amount for each billing cycle
                'CURRENCYCODE' => 'PHP', // Currency code 
                'TRIALBILLINGPERIOD' => $billing->billing_period,  // (Optional) Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
                'TRIALBILLINGFREQUENCY' => 1, // (Optional) set 12 for monthly, 52 for yearly 
                'TRIALTOTALBILLINGCYCLES' => 1, // (Optional) Change it accordingly
                'TRIALAMT' => 0, // (Optional) Change it accordingly
            ];
            $response = $provider->createRecurringPaymentsProfile($data, $request->token);
            if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
                 $billing->profile_id = $response['PROFILEID'];
                 $billing->save();
                 $output = ['success' => 1,
                        'msg' => 'Your payment was successfully. '.$request->desc,
                       'redirect' => action('PlanController@index')
                    ];
            }
            else {
                $output = ['success' => 0,
                    'msg' => $response['L_LONGMESSAGE0'],
                    'redirect' => action('PlanController@confirm', $billing->id)
                ];
            }

        }
        else {
            $output = ['success' => 0,
                'msg' => 'Sorry something went wrong, please try again later.',
                'redirect' => action('PlanController@confirm', $billing->id)
            ];
        }
        return response()->json($output);
    }
}
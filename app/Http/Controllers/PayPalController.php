<?php

namespace App\Http\Controllers;
  
use Auth;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;
use App\Package;
use App\Plan;
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
            if($billing_period == 'Monthly') {
                $billing_period = "Month";
                if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost) {
                    $price = $plan->promo_monthly_cost;
                }
                else {
                    $price = $plan->monthly_cost;
                }
            }
            elseif($billing_period == 'Annually') {
                $billing_period = "Year";
                if ($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost) {
                    $price = $plan->promo_yearly_cost;
                }
                else {
                    $price = $plan->yearly_cost;
                }
            }
            $desc = '1 '.$billing_period.' '.$plan->name.' Plan subscription on Qcommerce.';
	        $data['items'] = [
	            [
	                'name' => $plan->name,
	                'price' => $price,
	                'desc'  => $desc,
	                'qty' => 1
	            ]
	        ];

	        $billing = Billing::create([
	        	'business_id' => $request->user()->business_id,
	        	'plan_id' => $plan->id,
                'billing_period' => $billing_period,
                'amount' => $price,
	        	'invoice_no' => $invoice_no
	        ]);

	        $data['invoice_id'] = $invoice_no;
	        $data['invoice_description'] = $desc;
	        $data['return_url'] = action('PlanController@confirm', $billing->id);
	        $data['cancel_url'] = action('PayPalController@cancel', $billing->id);
	        $data['total'] = $price;
	  
	        $provider = new ExpressCheckout;
	  
	        // $response = $provider->setExpressCheckout($data);
	  
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

            $billing->billing_period = "Day";; //DEBUG ONLY
            $billing->paid_status = 1;
            $billing->payment_transaction_id = $response['PAYMENTINFO_0_TRANSACTIONID'];
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
                       'redirect' => action('PlanController@success')
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
<?php

namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;
use App\Package;
use App\Plan;
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
                $duration = "month";
                if($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->monthly_cost != $plan->promo_monthly_cost) {
                $price = $plan->promo_monthly_cost;
                }
                else {
                $price = $plan->monthly_cost;
                }
            }
            elseif($billing_period == 'Annually') {
                $duration = "year";
                if ($plan->promo_start <= date("Y-m-d") && $plan->promo_end >= date("Y-m-d") && $plan->yearly_cost != $plan->promo_yearly_cost) {
                $price = $plan->promo_yearly_cost;
                }
                else {
                $price = $plan->yearly_cost;
                }
            }
	        $data['items'] = [
	            [
	                'name' => $plan->name,
	                'price' => $price,
	                'desc'  =>   $plan->name . ' 1 '.$duration.' subscription for qcommerce.com',
	                'qty' => 1
	            ]
	        ];

	        $billing = Billing::create([
	        	'business_id' => $request->user()->business_id,
	        	'plan_id' => $plan->id,
                'billing' => $billing_period,
	        	'invoice_no' => $invoice_no
	        ]);

	        $data['invoice_id'] = $invoice_no;
	        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
	        $data['return_url'] = action('PayPalController@success', $billing->id);
	        $data['cancel_url'] = action('PayPalController@cancel', $billing->id);
	        $data['total'] = $price;
	  
	        $provider = new ExpressCheckout;
	  
	        $response = $provider->setExpressCheckout($data);
	  
	        $response = $provider->setExpressCheckout($data, true);
	        if($response['paypal_link'] != null){
	        	$output = ['success' => 1,
                            'msg' => 'You will now redirected to paypal. Thank you',
                            'redirect' => $response['paypal_link']
                        ];
            }else{
            	\Log::emergency("Paypal Response:". implode(', ', $response));
            	$output = ['success' => 0,
                    'msg' => 'Sorry something went wrong, please try again later.',
                    'redirect' => action('ApplicationController@show', $package->id)
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
        print json_encode($response);die();

        $startdate = Carbon::now()->toAtomString();
        $profile_desc = !empty($data['subscription_desc']) ?
                    $data['subscription_desc'] : $data['invoice_description'];
        $data = [
            'PROFILESTARTDATE' => $startdate,
            'DESC' => $profile_desc,
            'BILLINGPERIOD' => 'Month', // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
            'BILLINGFREQUENCY' => 1, // 
            'AMT' => 10, // Billing amount for each billing cycle
            'CURRENCYCODE' => 'USD', // Currency code 
            'TRIALBILLINGPERIOD' => 'Day',  // (Optional) Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
            'TRIALBILLINGFREQUENCY' => 10, // (Optional) set 12 for monthly, 52 for yearly 
            'TRIALTOTALBILLINGCYCLES' => 1, // (Optional) Change it accordingly
            'TRIALAMT' => 0, // (Optional) Change it accordingly
        ];
        $response = $provider->createRecurringPaymentsProfile($data, $token);
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            dd('Your payment was successfully. You can create success page here.');
        }
  
        dd('Something is wrong.');
    }
}
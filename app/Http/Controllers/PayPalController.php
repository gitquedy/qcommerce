<?php

namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;
use App\Package;
use DB;
   
class PayPalController extends Controller
{
    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function payment(Package $package, Request $request)
    {
        try {
            $invoice_no = Billing::getNextInvoiceNumber();
	        $data = [];
	        $price = $package->price;
	        $data['items'] = [
	            [
	                'name' => $package->name,
	                'price' => $price,
	                'desc'  =>   $package->name . ' 1  month subscription for qcommerce.com',
	                'qty' => 1
	            ]
	        ];

	        $billing = Billing::create([
	        	'business_id' => $request->user()->business_id,
	        	'package_id' => $package->id,
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
        return redirect(action('ApplicationController@index'));
    }

    /**
     * Responds with a welcome message with instructions
     *
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request, Billing $billing)
    {
        $response = $provider->getExpressCheckoutDetails($request->token);
  
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            dd('Your payment was successfully. You can create success page here.');
        }
  
        dd('Something is wrong.');
    }
}
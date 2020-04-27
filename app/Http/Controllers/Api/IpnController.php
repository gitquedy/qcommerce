<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;
use Log;

class IpnController extends Controller
{
    public function postNotify(Request $request)
    {
        $provider = new ExpressCheckout;
        
        $request->merge(['cmd' => '_notify-validate']);
        $post = $request->all();        
        
        // $response = (string) $provider->verifyIPN($post);
        $response = 'VERIFIED'; //Debug Only
        if ($response === 'VERIFIED') {
            Log::channel('ipnlog')->info(json_encode($post));
            return $this->switch($post);
        }                            
    }  

    public function switch($post) {
        switch ($post['txn_type']) {
            case 'cart':
                $billing = Billing::where('invoice_no', $post['invoice'])->first();
                // print json_encode($billing);die();
                if (isset($billing) && $billing->paid_status == 0 && $post['payment_status'] == "Completed") {
                    $billing->payer_id = $post['payer_id'];
                    $billing->payer_firstname = $post['first_name'];
                    $billing->payer_lastname = $post['last_name'];
                    $billing->payer_email = $post['payer_email'];
                    $billing->country_code = $post['residence_country'];
                    $billing->paid_status = 1;
                    $billing->save();
                }
                break;
            case 'recurring_payment_profile_created':
                $billing = Billing::where('payer_id', $post['payer_id'])->first();
                if (isset($billing)) {
                    $billing->payer_firstname = $post['first_name'];
                    $billing->payer_lastname = $post['last_name'];
                    $billing->payer_email = $post['payer_email'];
                    $billing->profile_id = $post['recurring_payment_id'];
                    $billing->save();
                }
                break;
            case 'recurring_payment_profile_cancel':
                $billing = Billing::where('payer_id', $post['payer_id'])->first();
                if (isset($billing)) {
                    $billing->payment_date = null;
                    $billing->next_payment_date = null;
                    $billing->paid_status == 3;
                    $billing->save();
                }
                break;
            case 'recurring_payment':
                $billing = Billing::where('payer_id', $post['payer_id'])->first();
                if (isset($billing)) {
                    $billing->payer_firstname = $post['first_name'];
                    $billing->payer_lastname = $post['last_name'];
                    $billing->payer_email = $post['payer_email'];
                    $billing->payment_date = date("Y-m-d H:i:s", strtotime($post['payment_date']));
                    $billing->next_payment_date = date("Y-m-d H:i:s", strtotime($post['next_payment_date']));
                    $billing->save();
                }
                break;
            // case 'recurring_payment_skipped':
            // case 'recurring_payment_suspended':
            // case 'recurring_payment_suspended_due_to_max_failed_payment':
            // case 'recurring_payment_failed':
            // case 'recurring_payment_expired':
            default:
                break;
        }
    }


    public function test() {
        return view('admin.test');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;

class IpnController extends Controller
{
    public function postNotify(Request $request)
    {
        $provider = new ExpressCheckout;
        
        $request->merge(['cmd' => '_notify-validate']);
        $post = $request->all();        
        
        $response = (string) $provider->verifyIPN($post);
        // $response = 'VERIFIED';
        if ($response === 'VERIFIED') {
            \Log::useDailyFiles('storage/paypal_ipn/ipn_logs.txt');
            \Log::info("PAYPAL IPN :: Date:" . date("Y-m-d H:i:s"). " Message:" . json_encode($post));
        }                            
    }  

    public function test() {
        return view('admin.test');
    }
}

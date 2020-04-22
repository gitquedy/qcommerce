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
            print json_encode($post);die();
        }                            
    }  

    public function test() {
        return view('admin.test');
    }
}

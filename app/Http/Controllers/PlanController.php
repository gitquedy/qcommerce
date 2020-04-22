<?php

namespace App\Http\Controllers;

use Auth;
use App\Plan;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan"], ['name'=>"Plan List"]
        ];
        $user = Auth::user();
        $plans = Plan::where('status', 1)->get();
        return view('plan.index', [
            'breadcrumbs' => $breadcrumbs,
            'plans' => $plans,
        ]);
    }

    public function show(Plan $plan){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan List"], ['name'=>"Payment"]
        ];
        return view('plan.show', compact('plan', 'breadcrumbs'));
    }

    public function subscribe(Plan $plan, $billing){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan List"], ['name'=>"Payment"]
        ];
        return view('plan.show', compact('plan', 'breadcrumbs', 'billing'));
    }

    public function confirm(Request $request, Billing $billing){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan List"], ['name'=>"Payment"]
        ];
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);
        // print json_encode($response);die();
        $billing->payer_id = $response['PAYERID'];
        $billing->payer_firstname = $response['FIRSTNAME'];
        $billing->payer_lastname = $response['LASTNAME'];
        $billing->payer_email = $response['EMAIL'];
        $billing->country_code = $response['COUNTRYCODE'];
        $billing->save();
        // print json_encode($response);die();
        return view('plan.confirm', compact('breadcrumbs', 'response', 'billing'));
    }

    public function success() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan List"], ['name'=>"Success Payment"]
        ];
        return view('plan.success', compact('breadcrumbs'));
    }
}

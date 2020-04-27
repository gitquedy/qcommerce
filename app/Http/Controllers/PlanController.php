<?php

namespace App\Http\Controllers;

use Auth;
use App\Plan;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Billing;
use App\Business;

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
        $business = Business::where('id', Auth::user()->business_id)->first();
        return view('plan.index', [
            'breadcrumbs' => $breadcrumbs,
            'plans' => $plans,
            'billing' => $business->subscription(),
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
        $business = Business::where('id', Auth::user()->business_id)->first();
        return view('plan.show', compact('plan', 'breadcrumbs', 'billing', 'business'));
    }

    public function confirm(Request $request, Billing $billing){
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('PlanController@index'), 'name'=>"Plan List"], ['name'=>"Payment"]
        ];
        $provider = new ExpressCheckout;
        $response = $provider->getExpressCheckoutDetails($request->token);
        $billing->payer_id = $response['PAYERID'];
        $billing->payer_firstname = $response['FIRSTNAME'];
        $billing->payer_lastname = $response['LASTNAME'];
        $billing->payer_email = $response['EMAIL'];
        $billing->country_code = $response['COUNTRYCODE'];
        $billing->save();
        return view('plan.confirm', compact('breadcrumbs', 'response', 'billing'));
    }

    public function cancel(Request $request) {

        $billing = Billing::whereId($request->id)->first();
        $billing->paid_status = 3;

        $provider = new ExpressCheckout;
        $response = $provider->cancelRecurringPaymentsProfile($billing->profile_id);
        if($response) {
            $billing->save();
        }
        return true;
    }
}

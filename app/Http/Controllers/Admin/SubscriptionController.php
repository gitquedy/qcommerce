<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Plan;
use App\Billing;
use App\Business;

class SubscriptionController extends Controller {

    public function index() {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\SubscriptionController@index'), 'name'=>"Subscription"], ['name'=>"Plan List"]
        ];
        $user = Auth::user();
        $plans = Plan::where('status', 1)->get();
        $business = Business::where('id', Auth::user()->business_id)->first();
        return view('admin.subscription.index', [
            'breadcrumbs' => $breadcrumbs,
            'plans' => $plans,
            'billing' => $business->subscription(),
        ]);
    }

    public function edit($id="",Request $request) {
        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"],['link'=> action('Admin\SubscriptionController@index'), 'name'=>"Subscription"], ['name'=>"Plan"]
        ];
        $subs = Plan::find($id);
        return view('admin.subscription.edit', [
            'breadcrumbs' => $breadcrumbs,
            'subs' => $subs,
            ]
        );
    }

    public function update(Request $request) {
        $request->validate([
            'order_processing' => 'required|numeric',
            'sales_channels' => 'required|array|min:1',
            'users' => 'required|numeric',
            'accounts_marketplace' => 'required|numeric',
            'return_recon' => 'required|in:1,0',
            'payment_recon' => 'required|in:1,0',
            'shipping_overcharge_recon' => 'required|in:1,0',
            'inventory_management' => 'required|in:1,0',
            'sync_inventory' => 'required|in:1,0',
            'no_of_warehouse' => 'required|numeric',
            'stock_transfer' => 'required|in:1,0',
            'purchase_orders' => 'required|in:1,0',
            'add_sales' => 'required|in:1,0',
            'customers_management' => 'required|in:1,0',
            'stock_alert_monitoring' => 'required|in:1,0',
            'out_of_stock' => 'required|in:1,0',
            'items_not_moving' => 'required|in:1,0',
            'daily_sales' => 'required|in:1,0',
            'monthly_sales' => 'required|in:1,0',
            'top_selling_products' => 'required|in:1,0',
        ]);

        $plan = Plan::find($request->id);
        $plan->order_processing = $request->order_processing;
        $plan->sales_channels = implode("/", $request->sales_channels);
        $plan->users = $request->users;
        $plan->accounts_marketplace = $request->accounts_marketplace;
        $plan->return_recon = $request->return_recon;
        $plan->payment_recon = $request->payment_recon;
        $plan->shipping_overcharge_recon = $request->shipping_overcharge_recon;
        $plan->inventory_management = $request->inventory_management;
        $plan->sync_inventory = $request->sync_inventory;
        $plan->no_of_warehouse = $request->no_of_warehouse;
        $plan->stock_transfer = $request->stock_transfer;
        $plan->purchase_orders = $request->purchase_orders;
        $plan->add_sales = $request->add_sales;
        $plan->customers_management = $request->customers_management;
        $plan->stock_alert_monitoring = $request->stock_alert_monitoring;
        $plan->out_of_stock = $request->out_of_stock;
        $plan->items_not_moving = $request->items_not_moving;
        $plan->daily_sales = $request->daily_sales;
        $plan->monthly_sales = $request->monthly_sales;
        $plan->top_selling_products = $request->top_selling_products;
        
        if($plan->save()) {
            $request->session()->flash('flash_success', 'Success !');
        }
        else {
            $request->session()->flash('flash_error',"something Went wrong !");
        }

        return redirect('/admin/subscription');
    }
}
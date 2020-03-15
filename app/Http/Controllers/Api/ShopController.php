<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shop;
use Illuminate\Http\Request;
use App\Lazop;
use App\Shopee;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shops = $request->user()->business->shops()->paginate(10);

        return response()->json(['shops' => $shops]);
    }

    public function links(Request $request){
        return ['lazada' => Lazop::getAuthLink(), 'shopee' => Shopee::getAuthLink()];
    }

    public function getDashboardDetails(Request $request){
        $user = $request->user();
        // $shops = $user->shops;
        $shops = Shop::where('business_id', $user->business_id);
        if($request->get('shop_id')){
            $shops->where('id', $request->get('shop_id'));
        }
        $shops = $shops->get();

        $shops = $shops->map(function ($shop) use ($request){
            if($request->get('sales')){
                $shop->totalSales = $shop->totalSales();
            }
            if($request->get('orders')){
                $shop->totalOrders = $shop->totalOrders();
            }
            return $shop;
        });

        if($request->get('total_sales_today')){
            $user->totalSalesToday = $user->totalSalesToday($shops);
        }

        if($request->get('total_orders_today')){
            $user->totalOrdersToday = $user->totalOrdersToday($shops);
        }

        if($request->get('current_pending_orders')){
            $user->currentPendingOrders = $user->currentPendingOrders($shops);
        }

        if($request->get('total_monthly_sales')){
            $user->totalMonthlySales = $user->totalMonthlySales($shops);
        }

        $response = ['success' => 1, 'user' => $request->user(), 'shops' => $shops];
        return response()->json($response);
    }


    public function getShopTotalOrders(Request $request){

        $shops = $request->user()->business->shops;
        if($request->get('shop_id')){
            $shops = $shops->where('id', $request->get('shop_id'));
        }

        foreach($shops as $shop){
            $shop = $shop->totalOrders();
        }

        return response()->json(['shops' => $shops, 'success' => 1]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function show(Shop $shop)
    {
        $this->authorizeForUser(request()->user(), 'show', [$shop]);
        return response()->json($shop);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Shop $shop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shop $shop)
    {
        //
    }
}

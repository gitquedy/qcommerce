<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shop;
use Illuminate\Http\Request;
use App\Lazop;
use App\Shopee;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;


class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $shops = $request->user()->business->shops;
        return ResponseBuilder::asSuccess(200)
                  ->withData(['shops' => $shops])
                  ->withMessage('OK')
                  ->build();
    }

    public function links(Request $request){
        return ResponseBuilder::asSuccess(200)
                  ->withData(['lazada' => Lazop::getAuthLink(), 'shopee' => Shopee::getAuthLink()])
                  ->withMessage('OK')
                  ->build();
    }

    public function getDashboardDetails(Request $request){
        $user = $request->user();
        $user->business = $user->business;
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

        $shop_ids = $shops->pluck('id')->toArray();

        if($request->get('total_sales_today')){
            $user->hourlySalesToday = $user->totalSalesToday($shop_ids);
            $user->totalSalesToday = number_format(array_sum($user->hourlySalesToday), 2);
        }

        if($request->get('total_orders_today')){
            $user->hourlyOrdersToday = $user->totalOrdersToday($shop_ids);
            $user->totalOrdersToday = number_format(array_sum($user->hourlyOrdersToday), 2);
        }

        if($request->get('current_pending_orders')){
            $user->currentPendingOrders = $user->currentPendingOrders($shop_ids);
        }

        if($request->get('total_monthly_sales')){
            $user->monthlySales = $user->totalMonthlySales($shop_ids);
            $user->totalMonthlySales = number_format(array_sum($user->monthlySales), 2);
        }
        

        $data = ['user' => $request->user(), 'shops' => $shops];
        return ResponseBuilder::success($data);
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
        return ResponseBuilder::success(['shop' => $shop]);
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

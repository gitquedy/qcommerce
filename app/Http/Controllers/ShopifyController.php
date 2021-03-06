<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Oseintow\Shopify\Facades\Shopify;
use App\Shop;
use Carbon\Carbon;
use App\Order;
use App\Products;   
use App\OrderItem;
use App\Utilities;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ShopifyController extends Controller
{
    public function install(Request $request){
    	$shopUrl = $request->channel;
	    $scope = ["read_orders", "write_products", "write_inventory", "read_inventory", "read_products", "write_products", "read_locations"];

	    $redirectUrl = "https://app.qcommerce.asia/shop/form";

	    $shopify = Shopify::setShopUrl($shopUrl);
	    return redirect()->to($shopify->getAuthorizeUrl($scope,$redirectUrl));
    }

    public function getAccessToken(Request $request){
    	$shopUrl = $request->shop;
	    $accessToken = Shopify::setShopUrl($shopUrl)->getAccessToken($request->code);

	    dd($accessToken);
    }

    public function customersRedact(Request $request){
        $payload = $request->all();

        $shop = Shop::where('domain', $payload['shop_domain'])->first();
        if($shop){
            $shop->orders->whereIn('ordersn', $payload['orders_to_redact'])->delete();
        }

        $data = ['orders_redacted' => $payload['orders_to_redact'], 'shop_domain' => $payload['shop_domain'], 'shop_id' => $payload['shop_id']];

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function shopRedact(Request $request){
        $payload = $Request->all();
        $shop = Shop::where('domain', $payload['shop_domain'])->first();

        if($shop){
            $shop->delete();
        }

        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function dataRequest(Request $request){
        $payload = $request->all();
        $shop = Shop::where('domain', $payload['shop_domain'])->first();

        $data = ['customer' => $payload['customer']];
        return ResponseBuilder::asSuccess(200)
                  ->withData($data)
                  ->withMessage('OK')
                  ->build();
    }

    public function test(Request $request){
      $shop = Shop::where('site', 'shopify')->first();
       
       $order_items = Shopify::setShopUrl($shop->domain)
                ->setAccessToken($shop->access_token)
                ->get("admin/api/2020-07/orders/3055414706362.json");
        
        dd($order_items);
    }
}



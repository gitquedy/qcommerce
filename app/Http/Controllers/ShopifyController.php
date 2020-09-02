<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Oseintow\Shopify\Facades\Shopify;

class ShopifyController extends Controller
{
    public function install(Request $request){
    	$shopUrl = $request->channel;
	    $scope = ["read_orders", "write_products"];

	    $redirectUrl = "https://app.qcommerce.asia/shop/form";

	    $shopify = Shopify::setShopUrl($shopUrl);
	    return redirect()->to($shopify->getAuthorizeUrl($scope,$redirectUrl));
    }

    public function getAccessToken(Request $request){
    	// $shopUrl = "pepper-juan.myshopify.com";
    	$shopUrl = $request->shop;
	    $accessToken = Shopify::setShopUrl($shopUrl)->getAccessToken($request->code);

	    dd($accessToken);
    }

    public function test(){
    	Shopify::get("resource uri", ["query string params"]);
    }
}

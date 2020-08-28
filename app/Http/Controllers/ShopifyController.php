<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Oseintow\Shopify\Facades\Shopify;

class ShopifyController extends Controller
{
    public function install(Request $request){
    	$shopUrl = $request->channel;
	    $scope = ["write_products","read_orders"];
	    $redirectUrl = "https://app.qcommerce.asia/shopify/getAccessToken";

	    $shopify = Shopify::setShopUrl($shopUrl);
	    return redirect()->to($shopify->getAuthorizeUrl($scope,$redirectUrl));
    }

    public function getAccessToken(){
    	$shopUrl = "testdevqcommerce1.myshopify.com";
	    $accessToken = Shopify::setShopUrl($shopUrl)->getAccessToken($request->code);

	    dd($accessToken);
    }
}

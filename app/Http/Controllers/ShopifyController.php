<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Oseintow\Shopify\Facades\Shopify;
use App\Shop;
use Carbon\Carbon;
use App\Order;
use App\Products;
use App\OrderItem;

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

    public function test(Request $request){ 
        $shop = Shop::first();
        $inventory_levels = Shopify::setShopUrl($shop->domain)
            ->setAccessToken($shop->access_token)
            ->get('/admin/api/2020-07/locations/53632041115/inventory_levels.json');

        dd($inventory_levels);

        $products->each(function($product){
             \Log::info($product->title);
        });

        // get products see if theres a field inventory_item_id location_id

        
    }
}

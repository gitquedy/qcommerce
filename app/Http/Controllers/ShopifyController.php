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
       try {
            $date = '2020-11-03';
            $shop = Shop::first();
            $since_id = 0;
            do{
                $params = [
                    'status' => 'any',
                    'limit' => 250,
                    'since_id' => $since_id,
                    'updated_at_min' => Carbon::parse($date)->format('c'),
                    'updated_at_max' => Carbon::now()->addDays(2)->format('c'),
                ];
                $orders = Shopify::setShopUrl($shop->domain)
                                ->setAccessToken($shop->access_token)
                                ->get("admin/api/2020-07/orders.json", $params);
                dd($orders);
                if(count($orders) != 0){
                    $since_id = $orders->last()->id;
                }

                $orders->each(function($order) use($shop){
                    // dd($order);
                    $printed = count($order->fulfillments) == 0 ? false : true;
                    $orders_details = [
                            'ordersn' => $order->id,
                            'payment_method' => isset($order->payment_gateway_names[0]) ?  $order->payment_gateway_names[0] : 'None',
                            'price' => $order->total_line_items_price,
                            'shop_id' => $shop->id,
                            'site' => 'shopify',
                            'items_count' => count($order->line_items),
                            'status' => $order->fulfillment_status == 'fulfilled' ? 'closed' : 'open',
                            'tracking_no' => count($order->fulfillments) ? $order->fulfillments[0]->tracking_number : '',
                            'shipping_fee' => $order->total_shipping_price_set->shop_money->amount,
                            'customer_first_name' => 'No Customer',
                            'printed' => $printed,
                            'created_at' => Carbon::parse($order->created_at)->toDateTimeString(),
                            'updated_at' => Carbon::parse($order->updated_at)->toDateTimeString(),
                    ];
                    // return $order;
                    $record = Order::updateOrCreate(
                        ['ordersn' => $orders_details['ordersn']], $orders_details);
                    foreach($order->line_items as $item){
                            $product = Products::where('shop_id', $shop->id)->where('item_id', $item->variant_id)->first();
                            if($product != null){
                                $item_detail = [
                                    'order_id' => $record->id,
                                    'product_id' => $product->id,
                                    'quantity' => $item->quantity,
                                    'price' => $item->price,
                                    'created_at' => $record->created_at,
                                    'updated_at' => $record->updated_at
                                ];
                                OrderItem::updateOrCreate(
                                    ['order_id' => $item_detail['order_id'], 'product_id' => $item_detail['product_id']], $item_detail
                                );
                            }
                        } //items
                }); // orders
            }while(count($orders) != 0);
        } catch (Exception $e) {
            //
        }
    }
}



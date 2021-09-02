<?php

namespace App\Jobs;

use App\Products;
use Carbon\Carbon;
use Oseintow\Shopify\Facades\Shopify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePlatform implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Products $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo json_encode($this->product->site.' product id'.$this->product->id);
        if(env('lazada_sku_sync', true) == false){
            return false;
        }
        if($this->product->site == 'lazada'){ // price qty ok
            $xml = '<?xml version="1.0" encoding="UTF-8" ?>
            <Request>
                <Product>
                    <Skus>
                        <Sku>
                            <SkuId>'. $this->product->SkuId .'</SkuId>
                            <SellerSku>'.$this->product->SellerSku.'</SellerSku>
                            <Quantity>'.$this->product->quantity.'</Quantity>
                            <Price>'.$this->product->price.'</Price>
                        </Sku>
                    </Skus>
                </Product>
            </Request>';
            $response = $this->product->product_price_quantity_update($xml);
        }
        else if($this->product->site == 'shopee') { // price stock sku ok tested
            $stock = [
                "item_id" =>(int) $this->product->item_id,
                "stock" => $this->product->quantity,
                "shopid" => $this->product->shop->shop_id,
                "timestamp" => Carbon::now()->timestamp,
            ];
            $price = [
                "item_id" =>(int) $this->product->item_id,
                "price" => $this->product->price,
                "shopid" => $this->product->shop->shop_id,
                "timestamp" => Carbon::now()->timestamp,
            ];
            $sku = [
                "item_id" =>(int) $this->product->item_id,
                "item_sku" => $this->product->SellerSku,
                "shopid" => $this->product->shop->shop_id,
                "timestamp" => Carbon::now()->timestamp,
            ];
            $client = $this->product->shop->shopeeGetClient();
            $stock = $client->item->updateStock($stock)->getData();
            $price = $client->item->updatePrice($price)->getData();
            $sku = $client->item->updateItem($sku)->getData();
        }else if($this->product->site == 'shopify'){ // qty price sku ok tested
            $params = [
                'inventory_item_ids' => $this->product->inventory_item_id
            ];
            $inventory_level = Shopify::setShopUrl($this->product->shop->domain)
                ->setAccessToken($this->product->shop->access_token)
                ->get('/admin/api/2020-07/inventory_levels.json', $params)->first();

            if($inventory_level){ //qty price sku ok tested
                $system_stock  = $this->product->quantity;
                $shopify_stock = $inventory_level->available;
                $adjusting_stock = $system_stock - $shopify_stock;

                $stockParams = [
                    'available_adjustment' => $adjusting_stock,
                    'location_id' => $inventory_level->location_id,
                    'inventory_item_id' => $this->product->inventory_item_id,
                ];

                $stock = Shopify::setShopUrl($this->product->shop->domain) //qty
                ->setAccessToken($this->product->shop->access_token)
                ->post('/admin/api/2020-07/inventory_levels/adjust.json', $stockParams);

                $productParams = [
                    'product' => [
                        'id' => $this->product->SkuId,
                        'variants' => [
                            0 => [
                                'id' =>  $this->product->item_id,
                                'price' => $this->product->price,
                                'sku' => $this->product->SellerSku,
                            ]
                        ]
                    ]
                ];
                $priceSku = Shopify::setShopUrl($this->product->shop->domain) //price sku
                ->setAccessToken($this->product->shop->access_token)
                ->put('/admin/api/2020-07/products/'. $this->product->SkuId .'.json', $productParams);
            }
        }
        else if ($this->product->site == 'woocommerce') {
            $data = [
                'manage_stock' => true,
                'stock_quantity' => $this->product->quantity,
                'regular_price' => (string)$this->product->price,
            ];
            $client = $this->product->shop->woocommerceGetClient();
            $client->put('products/' . $this->product->item_id, $data);
        }
    }
}

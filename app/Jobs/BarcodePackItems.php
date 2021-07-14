<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Order;
use App\Products;
use App\WarehouseItems;
use App\Sku;
use App\OrderItem;
use Auth;

class BarcodePackItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // print json_encode($request->all());die();
        $result = false;
        $order = Order::where('id',$this->request['order_id'])->first();
        if($order->packed == 0){
            $order->packed = 1;
            $order->save();
            foreach ($this->request['items'] as $sku => $qty) {
                
                $shop_id = $this->request['shop_id'];
                $shop = DB::table('shop')->select('warehouse_id')->find($shop_id);
                $warehouse_id = $shop->warehouse_id;
                $prod = Products::with('sku')->where('shop_id', $shop_id)
                                ->where(function ($query) use ($sku){
                                    $query->where('SellerSku', $sku)->orWhere('item_id', $sku);
                                })->first();
                if(isset($prod->seller_sku_id)) {
                    $sku = $prod->sku;

                    //single products
                    if ($sku->type == 'single') {
                        $sku->quantity -= $qty;
                        $witem = DB::table('warehouse_items')->where('warehouse_id', $warehouse_id)->where('sku_id', $prod->seller_sku_id)->first();
                        $warehouse_qty = isset($witem->quantity)?$witem->quantity:0;
                        $new_quantity = $warehouse_qty - $qty;
                        $warehouse_item = WarehouseItems::updateOrCreate(
                            ['warehouse_id' => $warehouse_id,
                            'sku_id' => $sku->id],
                            ['quantity' => $new_quantity]
                        );
                        $result = $sku->save();
                        $Sku_prod = $sku->products;
                        foreach ($Sku_prod as $product) {
                            $product->quantity = $warehouse_item->quantity;
                            $product->save();
                            if(env('lazada_sku_sync', true)){
                                $product->updatePlatform();
                            }
                        }
                    }
                    //set products
                    else if ($sku->type == 'set') {

                        //sku parent
                        $sku->quantity -= $qty;
                        $witem = DB::table('warehouse_items')->where('warehouse_id', $warehouse_id)->where('sku_id', $prod->seller_sku_id)->first();
                        $warehouse_qty = isset($witem->quantity)?$witem->quantity:0;
                        $new_quantity = $warehouse_qty - $qty;
                        $warehouse_item = WarehouseItems::updateOrCreate(
                            ['warehouse_id' => $warehouse_id,
                            'sku_id' => $sku->id],
                            ['quantity' => $new_quantity]
                        );
                        $result = $sku->save();
                        $Sku_prod = $sku->products;
                        foreach ($Sku_prod as $product_parent) {
                            $product_parent->quantity = $warehouse_item->quantity;
                            $product_parent->save();
                            if(env('lazada_sku_sync', true)){
                                $product_parent->updatePlatform();
                            }
                        }

                        //sku child
                        foreach ($sku->set_items as $set_item) {
                            $sku = Sku::where('id', $set_item->sku_single_id)->where('business_id', Auth::user()->business_id)->first();
                            $set_quantity = $set_item->set_quantity;
                            $sku->quantity -= ($qty*$set_quantity);

                            $witem = DB::table('warehouse_items')->where('warehouse_id', $warehouse_id)->where('sku_id', $set_item->sku_single_id)->first();
                            $warehouse_qty = isset($witem->quantity)?$witem->quantity:0;
                            $new_quantity = $warehouse_qty - $qty*$set_quantity;
                            $warehouse_item = WarehouseItems::updateOrCreate(
                                ['warehouse_id' => $warehouse_id,
                                 'sku_id' => $sku->id],
                                ['quantity' => $new_quantity]
                            );
                            $result = $sku->save();

                            $Sku_prod = $sku->products;
                            foreach ($Sku_prod as $product_child) {
                                $product_child->quantity = $warehouse_item->quantity;
                                $product_child->save();
                                if(env('lazada_sku_sync', true)){
                                    $product_child->updatePlatform();
                                }
                            }
                        }
                    }
                    $orderitem = OrderItem::where('order_id', $order->id)->where('product_id', $prod->id)->first();
                    $orderitem->new_quantity = DB::table('warehouse_items')->where('warehouse_id', $warehouse_id)->where('sku_id', $prod->seller_sku_id)->first()->quantity;
                    $orderitem->save();
                }
            }
        }
        echo json_encode($result);
    }
}

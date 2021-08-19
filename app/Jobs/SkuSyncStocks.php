<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\SetItem;
use App\Sku;
use App\WarehouseItems;

class SkuSyncStocks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $item;
    protected $sku;
    protected $warehouse_id;
    protected $business_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item, $sku, $warehouse_id, $business_id)
    {
        $this->item = $item;
        $this->sku = $sku;
        $this->warehouse_id = $warehouse_id;
        $this->business_id = $business_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->sku->type == 'set') {
            foreach ($this->sku->set_items as $set_item) {
                $sku = Sku::where('business_id','=', $this->business_id)->where('id','=', $set_item->sku_single_id)->first();
                $warehouse_item = WarehouseItems::where('warehouse_id', $this->warehouse_id)->where('sku_id', $set_item->sku_single_id)->first();

                $sku->quantity -= $this->item['quantity']*$set_item->set_quantity;
                if($warehouse_item == null){
                    $warehouse_item = WarehouseItems::create(
                        ['warehouse_id' => $this->warehouse_id,
                        'sku_id' => $set_item->sku_single_id,
                        'quantity' => 0]
                    );
                }
                $warehouse_item->quantity -= $this->item['quantity']*$set_item->set_quantity;
                $warehouse_item->save();
                $sku->save();
                $sku->updateProductsandPlatforms();
            }
        }
        else if ($this->sku->type == 'single') {
            $set_of_item = SetItem::where('sku_single_id', $this->item['sku_id'])->get();
            if ($set_of_item) {
                foreach ($set_of_item as $set) {
                    $sku = Sku::find($set->sku_set_id);
                    $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($this->warehouse_id);
                    $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                        ['warehouse_id' => $this->warehouse_id,
                        'sku_id' => $sku->id],
                        ['quantity' => $warehouse_set_quantity]
                    );
                    $sku->update(['quantity' => $sku->computeSetSkuQuantity()]);
                    $sku->updateProductsandPlatforms();
                }
            }
        }
    }
}

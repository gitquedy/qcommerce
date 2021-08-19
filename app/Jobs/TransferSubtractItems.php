<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\SetItem;
use App\Sku;

class TransferSubtractItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $item;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = json_decode($item);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $set_of_item = SetItem::where('sku_single_id', $this->item->sku_id)->get();
        if ($set_of_item) {
            foreach ($set_of_item as $set) {
                $sku = Sku::find($set->sku_set_id);
                $warehouse_set_quantity = $sku->computeSetWarehouseQuantity($this->item->from_warehouse_id);
                $warehouse_item = $sku->warehouse_items()->updateOrCreate(
                    ['warehouse_id' => $this->item->from_warehouse_id,
                    'sku_id' => $sku->id],
                    ['quantity' => $warehouse_set_quantity]
                );
                $sku->update(['quantity' => $sku->computeSetSkuQuantity()]);
                $sku->updateProductsandPlatforms();
            }
        }
    }
}

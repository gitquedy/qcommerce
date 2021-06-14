<?php

namespace App\Console\Commands\shop;

use App\Products;
use App\Shop;
use App\Sku;
use Carbon\Carbon;
use Illuminate\Console\Command;

class syncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:syncProducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will sync products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // $shops = Shop::all();
        $shops = Shop::where('active', '!=', 0)->get();
        // $shops = Shop::where('business_id', 5)->get();
        $sku_ids = [];
        foreach($shops as $shop){
            $shop->syncShopeeProducts(); //Carbon::now()->subDays(15)->format('Y-m-d'), '+2 day'
            $shop->syncLazadaProducts();
            $shop->syncShopifyProducts(Carbon::now()->subDays(30)->format('Y-m-d'));
            $shop->syncWoocommerceProducts();
            $shop->touch();
            $skus = $shop->products()->where('seller_sku_id', '!=', null)->groupBy('seller_sku_id')->pluck('seller_sku_id');
            foreach ($skus as $sku) {
                if (!in_array($sku, $sku_ids)) {
                    $sku_ids[] = $sku;
                }
            }
        }
        Sku::reSyncStocks($sku_ids, true);
        
        echo 'Synced products successfully ' . date('d-m-Y H:i:s') . PHP_EOL;
    }
}

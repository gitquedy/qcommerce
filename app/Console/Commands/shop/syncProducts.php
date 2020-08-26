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
        $shops = Shop::all();
            foreach($shops as $shop){
                $shop->syncShopeeProducts(Carbon::now()->subDays(15)->format('Y-m-d'), '+2 day');
                $test = $shop->syncLazadaProducts();
                echo "TEST LOGS :: ".json_encode($test);
                $shop->touch();
                $sku_id = $shop->products()->where('seller_sku_id', '!=', null)->groupBy('seller_sku_id')->pluck('seller_sku_id');
                Sku::reSyncStocks($sku_id, true);
            }
        // $sku_id = Products::where('seller_sku_id', '!=', null)->groupBy('seller_sku_id')->pluck('seller_sku_id');
        
        echo 'Synced products successfully ' . date('d-m-Y H:i:s') . PHP_EOL;
    }
}

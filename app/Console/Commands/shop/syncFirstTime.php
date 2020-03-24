<?php

namespace App\Console\Commands\shop;

use Illuminate\Console\Command;
use App\Shop;
use Carbon\Carbon;

class syncFirstTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:syncfirst';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Shop for the first time';

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
        $shops = Shop::where('is_first_time', true)->get();
        foreach($shops as $shop){
            $shop->syncShopeeProducts();
            $shop->syncLazadaProducts();
            $shop->syncOrders();
            $shop->syncLazadaPayout();
            $shop->syncShopeePayout();
            $shop->syncShippingDetails(Carbon::now()->subDays(30)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
            $shop->touch();
            $shop->update(['is_first_time', false]);
        }
         echo 'Synced orders and products for first time successfully ' . date('d-m-Y H:i:s') . PHP_EOL;
    }
}

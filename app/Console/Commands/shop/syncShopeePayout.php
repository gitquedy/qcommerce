<?php

namespace App\Console\Commands\shop;

use Illuminate\Console\Command;
use App\Shop;
use Carbon\Carbon;

class syncShopeePayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:syncShopeePayout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'syncShopeePayout';

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
        // $shops = Shop::where('site', 'shopee')->get();
        $shops = Shop::where('site', 'shopee')->where('active', '!=', 0)->get();
        foreach($shops as $shop){
            $shop->syncShopeePayout(Carbon::now()->subDays(30)->format('Y-m-d'));
            $shop->touch();
            echo 'Synced Shopee Payout Successfully ' . date('d-m-Y H:i:s') . PHP_EOL;
        }
    }
}

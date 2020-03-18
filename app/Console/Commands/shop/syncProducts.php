<?php

namespace App\Console\Commands\shop;

use Illuminate\Console\Command;
use App\Shop;
use Carbon\Carbon;

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
                $shop->syncLazadaProducts();
                $shop->touch();
            }
        echo 'Synced products successfully ' . date('d-m-Y H:i:s') . PHP_EOL;
    }
}
<?php

namespace App\Console\Commands\shop;

use Illuminate\Console\Command;
use App\Shop;
use Carbon\Carbon;

class syncShippingDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:syncShippingDetails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Shipping Details';

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
        $shops = Shop::get();
        foreach($shops as $shop){
            $shop->syncShippingDetails(Carbon::now()->subDays(7)->format('Y-m-d'), Carbon::now()->format('Y-m-d'));
            echo 'Sync Shipping Details Successfully ' .  date('d-m-Y H:i:s') . PHP_EOL;
        }
    }
}

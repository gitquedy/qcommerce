<?php

namespace App\Console\Commands\shop;

use Illuminate\Console\Command;
use App\Shop;
use Carbon\Carbon;

class syncLazadaPayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:syncLazadaPayout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'syncLazadaPayout';

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
        $shops = Shop::where('site', 'lazada')->get();
        foreach($shops as $shop){
            $shop->syncLazadaPayout(Carbon::now()->subDays(30)->format('Y-m-d'));
            $shop->touch();
            echo 'Synced Lazada Payout Successfully ' . date('d-m-Y H:i:s') . PHP_EOL;
        }
    }
}

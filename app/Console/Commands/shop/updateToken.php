<?php

namespace App\Console\Commands\shop;

use Illuminate\Console\Command;
use App\Shop;

class updateToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:updateToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update All Shops refresh token';

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
            $shop->refreshToken();
            $shop->touch();
            echo 'Token Updated Successfully ' .  date('d-m-Y H:i:s') . PHP_EOL;
        }
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Shop;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('shop:syncfirst')
                 ->everyMinute()
                 ->appendOutputTo('storage/logs/cron_syncfirst.log');
        $schedule->command('shop:syncOrders')
                 ->everyFifteenMinutes()
                 ->appendOutputTo('storage/logs/cron_syncorders.log');
        $schedule->command('shop:syncProducts')
                 ->hourly()
                 ->appendOutputTo('storage/logs/cron_syncProducts.log');
        $schedule->command('shop:updateToken')
                 ->cron('0 0 */5 * *')
                 ->appendOutputTo('storage/logs/cron_updateToken.log');
        $schedule->command('shop:syncShippingDetails')
                 ->weeklyOn(1, '2:00')
                 ->appendOutputTo('storage/logs/cron_syncShippingDetails.log');
        $schedule->command('shop:syncLazadaPayout')
                 ->weeklyOn(1, '2:00')
                 ->appendOutputTo('storage/logs/cron_syncLazadaPayout.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

<?php

namespace App\Console\Commands;

use App\Business;
use Carbon\Carbon;
use App\Mail\SubscriptionInvoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class SendInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:sendInvoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $businesses = Business::all();
        foreach ($businesses as $business) {
            if ($business->subscription()) {
                $notice_date = Carbon::parse($business->subscription()->next_payment_date)->subDays(1)->toDateString();
                $date_now = Carbon::now()->toDateString();
                if ($date_now == $notice_date) {
                    Mail::to($business->users()->where('role', 'Owner')->first()->email)->send(new SubscriptionInvoice($business));
                    echo json_encode('Invoice sent to '.$business->users()->where('role', 'Owner')->first()->email);
                }
            }
        }
    }
}

<?php

namespace App\Mail;

use App\Business;
use App\Billing;
use PDF;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The business instance.
     *
     * @var \App\Business
     */
    public $business;

    /**
     * Create a new message instance.
     *
     * @param  \App\Business  $business
     * @return void
     */

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Business $business)
    {
        $this->business = $business;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->business->name;
        $date = $this->business->subscription()->next_payment_date;
        $subject = 'Invoice#'.Billing::getNextInvoiceNumber().' for '.$this->business->subscription()->plan->name.' Plan of Qcommerce, due '.$this->business->subscription()->next_payment_date;
        $pdf = PDF::loadview('email.subscriptionInvoice', ['business' => $this->business])->output();
        return $this->view('email.subscriptionInvoice')
                    ->subject($subject)
                    ->attachData($pdf, $subject.'.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}

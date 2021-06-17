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
        
        $invoice = new Billing;
        $invoice->invoice_no = Billing::getNextInvoiceNumber();
        $invoice->business_id = $this->business->id;
        $invoice->plan_id = $this->business->subscription()->plan_id;
        $invoice->billing_period = $this->business->subscription()->billing_period;
        $invoice->amount = $this->business->subscription()->amount;
        $invoice->paid_status = 0;
        $invoice->save();

        $subject = 'Invoice#'.$invoice->invoice_no.' for '.$this->business->subscription()->plan->name.' Plan of Qcommerce, due '.$date;
        $pdf = PDF::loadview('email.subscriptionInvoice', ['business' => $this->business, 'invoice' => $invoice])->output();
        return $this->view('email.subscriptionInvoice', ['invoice' => $invoice])
                    ->subject($subject)
                    ->attachData($pdf, $subject.'.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}

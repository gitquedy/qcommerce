<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProofOfPayment extends Model
{
    protected $table = 'proof_of_payment';

    protected $fillable = ['billing_id', 'bank_id', 'date', 'transaction_reference_no', 'bank_name', 'account_name', 'account_no', 'receipt'];

    public function billing() {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }
}

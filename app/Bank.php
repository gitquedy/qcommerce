<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank_details';

    protected $fillable = ['bank', 'account_name', 'account_number'];
}

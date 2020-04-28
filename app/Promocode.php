<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
	protected $fillable = [
        'code', 'name', 'max_uses', 'max_uses_business', 'discount_amount', 'discount_type', 'starts_at', 'expires_at', 'description',
    ];

}

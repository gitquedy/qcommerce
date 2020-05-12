<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
	protected $fillable = [
        'code', 'name', 'max_uses', 'discount_range', 'discount_amount', 'discount_type', 'starts_at', 'expires_at', 'description',
    ];

}

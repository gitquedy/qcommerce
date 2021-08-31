<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRef extends Model
{
     protected $table = 'order_refs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'settings_id', 'so', 'qu', 'po', 'tr', 'do', 'pay', 're', 'adj', 'ir', 'ex'
    ];
}

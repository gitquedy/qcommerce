<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopPermission extends Model
{
    protected $table = 'shop_permission';

    protected $fillable = [
    	'user_id',
    	'shop_id'
    ];


}

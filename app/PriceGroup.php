<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceGroup extends Model
{
    protected $fillable = [
        'business_id', 'name'
    ];


    public function items(){
		return $this->hasMany(PriceGroupItemPrice::class, 'price_group_id', 'id');
	}
}

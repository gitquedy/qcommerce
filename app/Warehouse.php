<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'business_id', 'code', 'name', 'phone', 'address', 'email'
    ];


    public function items(){
        return $this->hasMany(WarehouseItems::class, 'warehouse_id', 'id');
    }

    public function Name() {
    	return isset($this->name)?ucwords($this->name):'[Deleted Warehouse]';
    }
}

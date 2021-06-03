<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Plan;
use Auth;

class Warehouse extends Model
{
    protected $fillable = [
        'business_id', 'code', 'name', 'phone', 'address', 'email', 'created_at', 'updated_at'
    ];


    public function items(){
        return $this->hasMany(WarehouseItems::class, 'warehouse_id', 'id');
    }

    public function Name() {
    	return isset($this->name)?ucwords($this->name):'[Deleted Warehouse]';
    }

    public static function getAvailableWarehouses() {
        $user = Auth::user();
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5) {
                return $user->business->warehouse()->orderBy('created_at', 'asc');
            }
            else {
                return $user->business->warehouse()->orderBy('created_at', 'asc')->take($user->business->subscription()->plan->no_of_warehouse)->get();
            }
        }
        else {
            return $user->business->warehouse()->orderBy('created_at', 'asc')->take(Plan::whereId(1)->value('no_of_warehouse'))->get();
        }
    }
}

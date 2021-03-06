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


    public function business() {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function items(){
        return $this->hasMany(WarehouseItems::class, 'warehouse_id', 'id');
    }

    public function Name() {
    	return isset($this->name)?ucwords($this->name):'[Deleted Warehouse]';
    }

    public function getStatusDisplay(){
        $status = '';
        if($this->status == 1){
            $status ='<div class="chip chip-success"><div class="chip-body"><div class="chip-text">Active</div></div></div>';
        }
        else if ($this->status == 0) {
            $status ='<div class="chip chip-danger"><div class="chip-body"><div class="chip-text">Suspended</div></div></div>';
        }
        return $status;
    }

    public function updateWarehouseStatus() {
        $user = $this->business->users()->where('role', 'Owner')->first();
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5 || $user->business->warehouse()->count() <= $user->business->subscription()->plan->no_of_warehouse) {
                $user->business->warehouse()->update(['status' => 1]);
            }
            else {
                $active_count = $user->business->subscription()->plan->no_of_warehouse;
                $suspended_count = $user->business->warehouse()->count() - $active_count;

                $active_warehouses = $user->business->warehouse()->orderBy('created_at', 'asc')->take($active_count)->get();
                foreach ($active_warehouses as $active) {
                    $active->status = 1;
                    $active->save();
                }

                $suspended_warehouses = $user->business->warehouse()->orderBy('created_at', 'desc')->take($suspended_count)->get();
                foreach ($suspended_warehouses as $suspend) {
                    $suspend->status = 0;
                    $suspend->save();
                }
            }
        }
        else {
            if ($user->business->warehouse()->count() > Plan::whereId(1)->value('no_of_warehouse')) {
                $active_count = Plan::whereId(1)->value('no_of_warehouse');
                $suspended_count = $user->business->warehouse()->count() - $active_count;

                $active_warehouse = $user->business->warehouse()->orderBy('created_at', 'asc')->take($active_count)->get();
                foreach ($active_warehouse as $active) {
                    $active->status = 1;
                    $active->save();
                }

                $suspended_warehouse = $user->business->warehouse()->orderBy('created_at', 'desc')->take($suspended_count)->get();
                foreach ($suspended_warehouse as $suspend) {
                    $suspend->status = 0;
                    $suspend->save();
                }
            }
            else {
                $user->business->warehouse()->update(['status' => 1]);
            }
        }
    }
}

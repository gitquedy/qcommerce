<?php

namespace App\Policies;

use App\User;
use App\Warehouse;
use App\Plan;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WarehousePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(User $user) {
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5) {
                return true;
            }
            else {
                return $user->business->warehouse()->count() <= $user->business->subscription()->plan->no_of_warehouse
                ? Response::allow()
                : abort(403, 'You\'ve reached the number of available warehouses for your subscription plan');
            }
        }
        else {
            return $user->business->warehouse()->count() <= Plan::whereId(1)->value('no_of_warehouse')
                ? Response::allow()
                : abort(403, 'You\'ve reached the number of available warehouses for your subscription plan');
        }
    }

    public function show(User $user, Warehouse $warehouse) {
        if ($user->business->subscription() !== null) {
            $warehouse_avail = $user->business->warehouse()->orderBy('created_at', 'asc')->take($user->business->subscription()->plan->no_of_warehouse)->pluck('id')->toArray();
        }
        else {
            $warehouse_avail = $user->business->warehouse()->orderBy('created_at', 'asc')->take(Plan::whereId(1)->value('no_of_warehouse'))->pluck('id')->toArray();
        }
        return in_array($warehouse->id, $warehouse_avail)
            ? Response::allow()
            : abort(403, 'Please upgrade your subscription plan to view this warehouse again');
    }

    public function edit(User $user, Warehouse $warehouse) {
        if ($user->business->subscription() !== null) {
            $warehouse_avail = $user->business->warehouse()->orderBy('created_at', 'asc')->take($user->business->subscription()->plan->no_of_warehouse)->pluck('id')->toArray();
        }
        else {
            $warehouse_avail = $user->business->warehouse()->orderBy('created_at', 'asc')->take(Plan::whereId(1)->value('no_of_warehouse'))->pluck('id')->toArray();
        }
        return in_array($warehouse->id, $warehouse_avail)
            ? Response::allow()
            : abort(403, 'Please upgrade your subscription plan to edit this warehouse');
    }
}

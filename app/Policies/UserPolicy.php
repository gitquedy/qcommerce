<?php

namespace App\Policies;

use App\User;
use App\Plan;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
                return $user->business->users()->count() <= $user->business->subscription()->plan->users
                ? Response::allow()
                : abort(403, 'You\'ve reached the number of available users for your subscription plan');
            }
        }
        else {
            return $user->business->users()->count() <= Plan::whereId(1)->value('users')
                ? Response::allow()
                : abort(403, 'You\'ve reached the number of available users for your subscription plan');
        }
    }

    public function edit(User $user, User $model) {
        return ($user->business->users()->whereId($model->id)->value('status') == 1)
            ? Response::allow()
            : abort(403, 'Please upgrade your subscription plan to edit this user');
    }
}

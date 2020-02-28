<?php

namespace App\Policies;

use App\User;
use App\Shop;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShopPolicy
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

    public function show(User $user, Shop $shop){
        return $user->id == $shop->user_id;
    }
}

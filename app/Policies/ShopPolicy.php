<?php

namespace App\Policies;

use App\User;
use App\Shop;
use App\Plan;
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
        return $user->business_id == $shop->business_id;
    }

    public function create(User $user) {
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5) {
                return true;
            }
            else {
                return $user->business->shops()->count() <= $user->business->subscription()->plan->accounts_marketplace
                ? Response::allow()
                : abort(403, 'You\'ve reached the number of available shops for your subscription plan');
            }
        }
        else {
            return $user->business->shops()->count() <= Plan::whereId(1)->value('accounts_marketplace')
                ? Response::allow()
                : abort(403, 'You\'ve reached the number of available shops for your subscription plan');
        }
    }

    public function createShopify(User $user) {
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5) {
                return true;
            }
            else {
                $allowed_sites = explode('/', $user->business->subscription()->plan->sales_channels);
                return (in_array('Shopify', $allowed_sites) ||  in_array('All', $allowed_sites))
                    ? Response::allow()
                    : abort(403, 'This site is not included in your subscription plan');
            }
        }
        else {
            $allowed_sites = explode('/', Plan::whereId(1)->value('sales_channels'));
            return (in_array('Shopify', $allowed_sites) ||  in_array('All', $allowed_sites))
                ? Response::allow()
                : abort(403, 'This site is not included in your subscription plan');
        }
    }

    public function createWoocommerce(User $user) {
        if ($user->business->subscription() !== null) {
            if ($user->business->subscription()->plan_id == 5) {
                return true;
            }
            else {
                $allowed_sites = explode('/', $user->business->subscription()->plan->sales_channels);
                return (in_array('Woocommerce', $allowed_sites) ||  in_array('All', $allowed_sites))
                    ? Response::allow()
                    : abort(403, 'This site is not included in your subscription plan');
            }
        }
        else {
            $allowed_sites = explode('/', Plan::whereId(1)->value('sales_channels'));
            return (in_array('Woocommerce', $allowed_sites) ||  in_array('All', $allowed_sites))
                ? Response::allow()
                : abort(403, 'This site is not included in your subscription plan');
        }
    }

    public function edit(User $user, Shop $shop) {
        if ($user->business->subscription() !== null) {
            $shop_avail = $user->business->shops()->where('active', '!=', 0)->pluck('id')->toArray();
        }
        else {
            $shop_avail = $user->business->shops()->where('active', '!=', 0)->pluck('id')->toArray();
        }
        return in_array($shop->id, $shop_avail)
            ? Response::allow()
            : abort(403, 'Please upgrade your subscription plan to edit this shop');
    }
}

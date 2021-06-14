<?php

namespace App\Providers;

use App\Warehouse;
use App\User;
use App\Shop;
use App\Plan;
use App\Policies\WarehousePolicy;
use App\Policies\UserPolicy;
use App\Policies\ShopPolicy;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Warehouse::class => WarehousePolicy::class,
        User::class => UserPolicy::class,
        Shop::class => ShopPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        // Passport::routes();
        
        Gate::define('is_included_in_plan', function (User $user, $feature) {
            if ($user->business->subscription() !== null) {
                if ($user->business->subscription()->plan_id == 5) {
                    return true;
                }
                else {
                    return $user->business->subscription()->plan->$feature
                        ? Response::allow()
                        : abort(403, 'This is not included in your subscription plan');
                }
            }
            else {
                return Plan::whereId(1)->value($feature)
                    ? Response::allow()
                    : abort(403, 'This is not included in your subscription plan');
            }
        });
    }
}

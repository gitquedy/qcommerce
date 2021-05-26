<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // print json_encode($request->user()->isAdmin());die();
        if($request->user()->isAdmin()){
            return $next($request);
        }
        else {
            abort(403, 'Unauthorized action.');
        }
    }
}

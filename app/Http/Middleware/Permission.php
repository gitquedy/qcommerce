<?php

namespace App\Http\Middleware;

use Closure;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if(! $request->user()->can($permission)){
            if ($request->wantsJson() || $request->expectsJson() || $request->isJson()) {
                return ResponseBuilder::asError(401)
                  ->withHttpCode(401)
                  ->withDebugData(['message' => 'Auth user does not have access to ' . $permission])
                  ->withMessage('UNAUTHORIZED')
                  ->build();
            }else{
                abort(403, 'Unauthorized Access');
            }
        }
        return $next($request);
    }
}

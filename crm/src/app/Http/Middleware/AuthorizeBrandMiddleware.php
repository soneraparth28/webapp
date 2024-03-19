<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Helper\CheckForBrand;
use Closure;

class AuthorizeBrandMiddleware
{
    use CheckForBrand;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->isAppAdmin()) {
            $this->byPassBrandDashboard($request);
            return $next($request);
        }

        $this->checkForAllowedBrand($request);

        $this->byPassBrandDashboard($request);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfNotInstalled
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
        if (app()->environment('production')) {
            if (!config('gain.installed'))
                return $next($request);
            else
                return redirect()->route('users.login.index');
        }
        return $next($request);

    }
}

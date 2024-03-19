<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfInstalledMiddleware
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
        if (app()->environment('production')){
            if (!config('gain.installed') && !$request->expectsJson())
                return redirect(request()->root().'/install');
        }
        return $next($request);
    }
}

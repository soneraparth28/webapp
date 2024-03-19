<?php

namespace App\Http\Middleware;

use App\Exceptions\GeneralException;
use App\Helpers\Traits\HasAppRole;
use Closure;

class CheckAppRoleMiddleWare
{

    public function handle($request, Closure $next)
    {
        if (!request()->brand_id && !resolve(HasAppRole::class)->hasAppRole()){
            throw new GeneralException(trans('default.action_not_allowed'));
        }

        return $next($request);
    }
}

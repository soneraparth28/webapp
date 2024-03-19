<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;


class XssProtectionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $input = array_filter($request->all());

        array_walk_recursive($input, function (&$input) {
            if (is_string($input)) {
                $input = clean($input);
            }
//            $input = strip_tags(
//                str_replace(array("&lt;", "&gt;"), '', $input),
//                ['div','b','strong','small','mark','i','em','u','a','ul','ol','li','p','br','hr','span','style','img',
//                    'table','thead','tfoot','tbody','tr','th','td','colgroup','footer','header','address','col','strong',
//                    'pre','del','main','section','code','caption','cite','sup','sub','embed','iframe','time','mark','article']);
        });

        $request->merge($input);

        return $next($request);
    }
}
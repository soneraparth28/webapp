<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetAPIToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set("authtoken" , "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjgwZmM1NWNkODNkZWYxNTZlYjI5OWI3MzJkMmU0MjFhMWUwMDdkY2M1Mzc1ZDI0ZTNkNTIyZTVlYzA2MjY5MDIwYzBmNmE4MjAzODk5OWEiLCJpYXQiOjE2NzQ3MzE4NTEuMzkwMDMzMDA2NjY4MDkwODIwMzEyNSwibmJmIjoxNjc0NzMxODUxLjM5MDA0MTExMjg5OTc4MDI3MzQzNzUsImV4cCI6MTcwNjI2Nzg1MS4zMzIwNzIwMTk1NzcwMjYzNjcxODc1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.vSMqk9rJwLW4I1SV7hqBqey410hEgh-V8wh4ikvu_9foraB46uFge1XwcrjDqLrgvS8rEbrlhApngW7YgLmHQfK1QLrcQ_H9W0PL2H9uHEbrQ3MYE1cuo7j853XTUdesFp_wvtjvNz8oYbTpRvjamuJ8xyZcQBooe5ux78K7iRPrC7qzzU-BTgbkoOUzt_36szynhDz5-BKGItCDtJBEtbXsyUlL872RjZQiABXlucviyZ8S_g9aEvr5G4LC321arie44UwcbpoRohESBjJMXC1wqraNlEY5yFtMpGcqa5dlwg0N8rQ5MSRbjLWykVb3jaMwo_U4dfPWBtoURAHs83YiMIcUhouOXsFmRlz7OAxx5t6B3A0zOjmCqyVa2KAjdxhGLFheX_7WRsO0APw9fzkjO-0QDclasEJRoeCxqIBjESvrDBiOtiEZXmbmmEQ7e1UC8s1g6MsNmIwcY97XQ5cwG0kisqGVEx02E7mtLEPK98CCrHc5CvphcnFw8WO4o2gpi1KarO-0i9fWev0JTwE3naloX8vlEx6_l35JH5aeNel4PIvOPNSDx1OV3k34b_k2MwZqk68PzYm4FaV-a_mKt1-2v-zG5d46CWn0YnlOB7g7ygE-Xc1ldoMuKygpEUriB5mrVW3-Lrwa_8eGXflBf33LKw8MlDYF_9MiWNY");
        if ($request->headers->has('authtoken') && !$request->headers->has('Authorization')) {
            $request->headers->set('Authorization',  $request->header('authtoken'));
            // dd($request->header());
        }
        return $next($request);
    }
}

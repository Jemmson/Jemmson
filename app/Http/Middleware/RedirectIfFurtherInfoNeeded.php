<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RedirectIfFurtherInfoNeeded
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        session(['prevDestination' => $request->getRequestUri()]);
        $details = Auth::user()->getDetails();
        if (Auth::guard($guard)->check() &&
            (
            Auth::user()->password_updated == 0 ||
                $details == null ||
                $details->location_id == null
            )) {
            return redirect('/#/furtherInfo');
        }

        return $next($request);
    }
}

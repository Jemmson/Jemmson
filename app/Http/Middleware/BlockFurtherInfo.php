<?php
namespace App\Http\Middleware;

use Closure;
use Auth;

class BlockFurtherInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * 
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $details = Auth::user()->getDetails();
        if (Auth::guard($guard)->check() && $details->location_id !== null) {
            return back();
        }

        return $next($request);
    }
}

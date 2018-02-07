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
     * @param  \Closure                 $next
     * @param  string|null              $guard
     * 
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check() && Auth::user()->getDetails()->address_line_1 === null) {
            // TODO: if they are coming from a passwordless link and further info was
            //       needed redirect them to the bid afterwords not just home, better way to do/not do this?
            return redirect('furtherInfo');
        }

        return $next($request);
    }
}

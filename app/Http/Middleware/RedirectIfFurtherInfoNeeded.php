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
        session(['prevDestination' => $request->getRequestUri()]);
        $details = Auth::user()->getDetails();
        if (Auth::guard($guard)->check() && ($details === null || $details->address_line_1 === null)) {
            // TODO: if they are coming from a passwordless link and further info was
            //       needed redirect them to the bid afterwords not just home, better way to do/not do this?
            return redirect('furtherInfo');
        }

        return $next($request);
    }
}

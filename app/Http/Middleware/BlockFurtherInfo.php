<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Location;

class BlockFurtherInfo
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
// //        dd(Auth::user()->location_id);
// //        dd(Auth::user());
// //        dd(Location);
//         if(Auth::user() && !empty(Location::select()->where("user_id","=",Auth::user()->id)->get()->first()->id)) {
//             return back();
// //        if (!empty(Auth::user()->location_id)) {
// //            $details = Auth::user()->getDetails();
// //            if (Auth::guard($guard)->check() && $details->location_id !== null) {
// //                return back();
// //            }

//         }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Quickbook;
use Closure;
use Illuminate\Support\Facades\Auth;

class QuickBookTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $qb = new Quickbook();
        if ($qb->isContractorThatUsesQuickbooks()) {
            if ($qb->checkIfSessionAccessTokenExists()) {
                $accessToken = session('sessionAccessToken');
                $accessTokenTimestamp = $qb->getAccessTokenTimeStamp($accessToken->getAccessTokenExpiresAt());
                if ($qb->checkIfAccessTokenHasNotExpired($accessTokenTimestamp)) {
                    return $next($request);
                } else {
                    if ($qb->checkIfRefreshTokenIsValid(Auth::user()->getAuthIdentifier())) {
                        $qb->refreshAccessToken();
                        return $next($request);
                    } else {
//                 TODO:  need to take them through the authorization flow again
//                        if they are still using quickbooks
                        return response()->json([
                            'tokenState' => 'refreshTokenHasExpired'
                        ], 200);
                    }
                }
            } else {
                if ($qb->checkIfRefreshTokenIsValid(Auth::user()->getAuthIdentifier())) {
                    $qb->refreshAccessToken();
                    return $next($request);
                } else {

//                TODO:      need to take them through the authorization flow again
//                        if they are still using quickbooks
                    return response()->json([
                        'tokenState' => 'refreshTokenHasExpired'
                    ], 200);
                }
            }
        } else {
            return $next($request);
        }
    }
}

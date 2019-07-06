<?php

namespace App\Http\Controllers;

use App\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoadController extends Controller
{
    //

    public function load(Request $request)
    {

        $state = [];

        if (Auth::user() !== null) {
            if (Auth::user()->email == 'pike.shawn@gmail.com' ||
                Auth::user()->email == 'jemmsoninc@gmail.com') {
                array_push($state, Feature::select(['name', 'on'])->get());
            } else {
                array_push($state, false);
            }

            if (Auth::user()->usertype === 'contractor') {

                $user = collect(Auth::user())->toArray();
                array_push($user, ['contractor' => Auth::user()->contractor()->get()]);

                array_push($state, $user);
            } else {
                array_push($state, Auth::user()->customer());
            }

            return response()->json([
                'state' => $state
            ], 200);

        } else {

            // hello world

            if (
                $request->location["pathname"] != '/' &&
                $request->location["pathname"] != '/demo' &&
                $request->location["pathname"] != '/check_accounting' &&
                $request->location["pathname"] != '/howto' &&
                $request->location["pathname"] != '/benefits' &&
                $request->location["pathname"] != '/register' &&
                $request->location["pathname"] != '/registerQuickBooks'
            ) {
                return response()->json([
                    'redirect' => '/'
                ], 200);
            } else {
                return response()->json([
                    'redirect' => $request->location["pathname"]
                ], 200);
            }


        }


//    if (Auth::user() != null &&
//        Auth::user()->email == 'pike.shawn@gmail.com' || 'jemmsoninc@gmail.com') {
//        return Feature::select(['name', 'on'])->get();
//    } else {
//        return redirect('/home');
//    }
    }
}

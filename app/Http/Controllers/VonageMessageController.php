<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VonageMessageController extends Controller
{
    //

    public function inbound(Request $request)
    {

        if ($request->type === 'KPS Pools') {
            if ($request->event === 'STOP') {
                dd('stop');
            } else if ($request->event === 'JOIN') {
                dd('join');
            }
        }

        if ($request->type === 'JEMMSON') {
            if ($request->event === 'STOP') {
                dd('stop');
            } else if ($request->event === 'JOIN') {
                dd('join');
            }
        }



        return response(['Inbound: Hello World from Jemmson'], 200);

    }


    public function status(Request $request)
    {

        return response(['Status: Hello World from Jemmson'], 200);
    }
}

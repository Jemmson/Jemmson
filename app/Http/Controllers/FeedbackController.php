<?php

namespace App\Http\Controllers;

use App\Feedback;
use Auth;

class FeedbackController extends Controller
{
    /**
     * Construct
     */
    public function __construct()
    {
        
    }
    
    public function show () {
        if (Auth::user()->email == 'jemmsoninc@gmail.com') {
            $feedback = Feedback::all();
            return response()->json([
                'feedback' => $feedback
            ], 200);
        } else {
            return response()->json([
                'error' => 'You are not Authorized to see this page'
            ], 403);
        }
    }

}

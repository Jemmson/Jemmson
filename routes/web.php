<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@show');

// login routes
Route::get('login', 'Auth\LoginController@show');
Route::post('login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);

Route::get('/contractorFeatures/', function(){
    return view('/public.contractorFeatures');
});

Route::get('/customerFeatures/', function(){
    return view('/public.customerFeatures');
});

Route::group(['middleware' => 'auth'], function () {

    //  Route::get('/furtherInfo', 'Auth\RegisterController@furtherInfo');
    Route::get('/furtherInfo', function () {


        // TODO: The code below needs to be deleted when I can figure out how to save the usertype in the datebase
//      ===============================
        $user = Auth::user();
        $user->usertype = env('USER_TYPE');
        $user->save();
//        dd($user);
//      ==============================

        return view('auth.furtherInfo');
    });

    Route::get('/home', 'HomeController@show');
    Route::post('/home', 'HomeController@create');

    // contractor routes
    Route::get('/contractor/', 'ContractorController@index');
    Route::get('/contractor/initiate-bid', 'InitiateBidController@index');
    Route::post('/contractor/initiate-bid', 'InitiateBidController@send');
    Route::get('/contractor/bid-list', 'BidListController@contractorIndex');


    // customer routes
    Route::get('/customer/bid-list', 'BidListController@customerIndex');
    Route::get('/customer/check', 'CustomerController@checkCustomerData');
    Route::resource('/customer', 'CustomerController');
    Route::resource('/job', 'JobController');
}
);


// passwordless login
Route::get('/login/{token}/{job_id}', function ($token, $job_id) {
    // find token in the db
    $token = App\PasswordlessToken::where('token', $token)->first();
    // invalid token
    if (!$token) {
        return redirect('login')->withErrors(__('passwordless.invalid_token'));
    }

    // find user connected to token
    $user = App\User::find($token->user_id);
    // user not found or login user if they where found
    if (!$user) {
        return redirect('login')->withErrors(__('passwordless.no_user'));
    } else {
        if ($user->isValidToken($token->token)) {
            Auth::login($user);
            return redirect('/customer/check')->with('data', ['user_id' => $user->id, 'job_id' => $job_id]);
        }
    }
});



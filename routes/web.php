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

use App\User;

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

Route::get('/public/customerJobTracking', function(){
    return view('/public.customerJobTracking');
});

Route::get('/public/customerSecurity', function(){
    return view('/public.customerSecurity');
});

Route::get('/public/customerInvoiceManagement', function(){
    return view('/public.customerInvoiceManagement');
});

Route::get('/public/customerCommunication', function(){
    return view('/public.customerCommunication');
});

Route::get('/public/contractorJobTracking', function(){
    return view('/public.contractorJobTracking');
});

Route::get('/public/contractorSecurity', function(){
    return view('/public.contractorSecurity');
});

Route::get('/public/contractorInvoiceManagement', function(){
    return view('/public.contractorInvoiceManagement');
});

Route::get('/public/contractorCommunication', function(){
    return view('/public.contractorCommunication');
});

Route::group(['middleware' => 'auth'], function () {

    //  Route::get('/furtherInfo', 'Auth\RegisterController@furtherInfo');
    Route::get(
        '/furtherInfo', function () {
            return view('auth.furtherInfo', ['password_updated' => Auth::user()->password_updated]);
        }
    );

    Route::get('/home', 'HomeController@show')->middleware('further.info');
    Route::post('/home', 'HomeController@create');

    // common routes
    Route::get('/initiate-bid', 'InitiateBidController@index');
    Route::post('/initiate-bid', 'InitiateBidController@send');
    Route::get('/bid-list', 'BidListController@contractorIndex');
    Route::get('/settings', 'Controller@create');
    Route::get('/invoices', 'Controller@create');
    Route::get('/current-job', 'Controller@create');
    Route::get('/payments-and-review', 'Controller@create');
    Route::get('/my-contractors', 'Controller@create');
    Route::resource('/job', 'JobController');

    // contractor routes
//    Route::get('/contractor/', 'ContractorController@index');
//    Route::get('/contractor/initiate-bid', 'InitiateBidController@index');
//    Route::post('/contractor/initiate-bid', 'InitiateBidController@send');
//    Route::get('/contractor/bid-list', 'BidListController@contractorIndex');


    // customer routes
//    Route::get('/customer/bid-list', 'BidListController@customerIndex');
//    Route::get('/customer/check', 'CustomerController@checkCustomerData');
//    Route::resource('/customer', 'CustomerController');
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
            session(['job_id' => $job_id]);
            return redirect('/job/' . $job_id . '/edit');
        }
    }
});



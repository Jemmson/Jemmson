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
    Route::get('/bid-list', 'BidListController@index');
    Route::get('/invoices', 'Controller@create');
    Route::get('/current-job', 'Controller@create');
    Route::get('/payments-and-review', 'Controller@create');
    Route::get('/my-contractors', 'Controller@create');
    Route::get('/bid/tasks', 'TaskController@bidContractorJobTasks');
    Route::post('/bid/tasks', 'TaskController@bidTasks');
    Route::post('/bid/tasks/reopen', 'TaskController@reopenTask');
    Route::post('/task/deny', 'TaskController@denyTask');
    Route::resource('/job', 'JobController');
    Route::post('/jobs', 'JobController@jobs');

    // Stripe routes
    Route::get('/stripe/express/connect', 'StripeController@connectExpress');
    Route::get('/stripe/express/auth', 'StripeController@expressAuth');
    Route::post('/stripe/express/dashboard', 'StripeController@createExpressDashboardLink');
    Route::post('/stripe/express/task/payment', 'StripeController@sendExpressTaskPayment');    
    
    Route::post('/stripe/customer', 'StripeController@saveCustomer');
    Route::post('/stripe/customer/charge', 'StripeController@chargeCustomer'); 
}
);


// passwordless login
Route::get('/login/{type}/{job_id}/{token}', 'PasswordlessController@JobBid');
// passwordless login
Route::get('/login/{type}/task/{task_id}/{token}', 'PasswordlessController@taskBid');



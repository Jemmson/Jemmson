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

Route::group(['middleware' => ['auth', 'further.info']], function () {

    Route::get('/home', 'HomeController@show');
    // common routes
    Route::get('/initiate-bid', 'InitiateBidController@index');
    Route::post('/initiate-bid', 'InitiateBidController@send');
    Route::get('/bid-list', 'BidListController@index');
    Route::get('/invoices', 'Controller@create');
    Route::get('/current-job', 'Controller@create');
    Route::get('/payments-and-review', 'Controller@create');
    Route::get('/my-contractors', 'Controller@create');

    // TaskController
    Route::get('/bid/tasks', 'TaskController@bidContractorJobTasks');
    Route::post('/bid/tasks', 'TaskController@bidTasks');
    Route::post('/bid/tasks/reopen', 'TaskController@reopenTask');
    Route::post('/task/deny', 'TaskController@denyTask');

    // JobController
    Route::resource('/job', 'JobController');
    Route::post('/jobs', 'JobController@jobs');
    Route::post('/bid/job/decline', 'JobController@declineJobBid');
    Route::post('job/approve/{job}', 'JobController@approveJob');
    
    // Stripe routes
    Route::get('/stripe/express/connect', 'StripeController@connectExpress');
    Route::get('/stripe/express/auth', 'StripeController@expressAuth');
    Route::post('/stripe/express/dashboard', 'StripeController@createExpressDashboardLink');
    Route::post('/stripe/express/task/payment', 'StripeController@sendExpressTaskPayment');    
    
    Route::post('/stripe/customer', 'StripeController@saveCustomer');
    Route::post('/stripe/customer/charge', 'StripeController@chargeCustomer'); 

    // home controller
    Route::post('/settings/logo', 'HomeController@uploadCompanyLogo');
}
);
Route::group(['middleware' => ['auth']], function () {
    Route::post('/home', 'HomeController@create');
    Route::get(
        '/furtherInfo', function () {
            return view('auth.furtherInfo', ['password_updated' => Auth::user()->password_updated]);
        }
    );
}
);



// passwordless login
Route::get('/login/{type}/{job_id}/{token}', 'PasswordlessController@JobBid');
// passwordless login
Route::get('/login/{type}/task/{task_id}/{token}', 'PasswordlessController@taskBid');



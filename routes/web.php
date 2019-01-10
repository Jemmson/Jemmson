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

use App\Feature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

Route::get('/', 'WebController@index');
Route::get('/home', 'WebController@index');
Route::get('/loadFeatures', function () {

    if (Auth::user() != null &&
        Auth::user()->email == 'pike.shawn@gmail.com' || 'jemmsoninc@gmail.com') {
        return Feature::select(['name', 'on'])->get();
    } else {
        return redirect('/home');
    }
});


// login routes
Route::get('login', 'Auth\LoginController@show');
Route::post('login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);

Route::group(['middleware' => ['auth', 'further.info']], function () {

    Route::get('/feedback', 'FeedbackController@show');

    Route::post('/initiate-bid', 'InitiateBidController@send');

    // TaskController
    Route::post('/bid/tasks', 'TaskController@bidTasks');
    Route::post('/bid/tasks/reopen', 'TaskController@reopenTask');
    Route::post('/task/deny', 'TaskController@denyTask');
    Route::post('/task/image', 'TaskController@uploadTaskImage');
    Route::delete('/task/image/{taskImage}', 'TaskController@deleteImage');

    // JobController
    Route::resource('/job', 'JobController');
    Route::get('/jobtask/{jobTaskId}', 'TaskController@getJobTask');
    Route::post('/jobs', 'JobController@jobs');
    Route::post('/bid/job/decline', 'JobController@declineJobBid');
    Route::post('job/approve/{job}', 'JobController@approveJob');
    Route::get('invoices', 'JobController@getInvoices');
    Route::get('invoice/{job}', 'JobController@getInvoice');
    Route::get('/sub/invoice/{jobTask}', 'JobController@getSubInvoice');
    Route::post('job/cancel', 'JobController@cancelJobBid');

    // Stripe routes
    Route::get('/stripe/express/connect', 'StripeController@connectExpress');
    Route::get('/stripe/express/auth', 'StripeController@expressAuth');
    Route::post('/stripe/express/dashboard', 'StripeController@createExpressDashboardLink');
    Route::post('/stripe/express/task/payment', 'StripeController@sendExpressTaskPayment');


    Route::post('/stripe/customer', 'StripeController@saveCustomer');
    Route::post('/stripe/customer/charge', 'StripeController@chargeCustomer');
    Route::post('/stripe/customer/pay/tasks', 'StripeController@payAllPayableTasks');
    Route::post('/stripe/customer/pay/tasks/cash', 'StripeController@payAllPayableTasksWithCash');
    Route::delete('/stripe/customer/card', 'StripeController@deleteCard');
}
);
Route::group(['middleware' => ['auth']], function () {
    Route::post('/home', 'HomeController@create');
    Route::post('/', 'HomeController@create');
    Route::get(
        '/furtherInfo', function () {
        return view('auth.furtherInfo', ['password_updated' => Auth::user()->password_updated]);
    }
    )->middleware('block.further.info');

    // home controller
    Route::post('/settings/logo', 'HomeController@uploadCompanyLogo');
}
);


// passwordless login
Route::get('/login/{type}/{job_id}/{token}', 'PasswordlessController@JobBid');
// passwordless login
Route::get('/login/{type}/task/{task_id}/{token}', 'PasswordlessController@taskBid');

Route::post('/bid/customer/getName', 'CustomerController@getName');
Route::post('/customer/updateCustomerNotes', 'CustomerController@updateCustomerNotes');

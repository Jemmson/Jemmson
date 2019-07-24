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
use Illuminate\Support\Facades\Request;

Route::get('/', 'WebController@index');
Route::get('/home', 'WebController@index');
Route::post('/loadFeatures/', 'LoadController@load');

Route::get('/loggedIn', function () {
    if (Auth::check()) {
        return response()->json([
            'user' => Auth::user()
        ], 200);

    }
});

Route::get('checkAuth', function() {
   if (Auth::check()) {
       return response()->json([
           'auth' => true
       ], 200);
   } else {
       return response()->json([
           'auth' => false
       ], 200);
   }
});

Route::get('/welcome', 'WelcomeController@show');

Route::get('/search/{company_name}', 'ContractorController@getContractors');

Route::get('/quickbooks/getAuthUrl/{state}', 'QuickbooksController@getAuthUrl');
Route::get('/quickbooks/processToken/', 'QuickbooksController@processToken');
Route::get('/quickbooks/getCachedCompanyInfo', 'QuickbooksController@getCachedCompanyInfo');
Route::post('/register/contractor', 'RegisterController@registerContractor');
Route::post('/task/addTask', 'TaskController@addTask');

// login routes
Route::get('login', 'Auth\LoginController@show');
Route::post('login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);

Route::group(['middleware' => ['auth', 'further.info']], function () {

    Route::get('/feedback', 'FeedbackController@show');

    Route::get('/customer/search', 'CustomerController@getCustomerAssociatedToContractor');

    Route::post('/search/task', 'TaskController@getTasks');

//    Route::get('/quickbooks/getAuthUrl', 'QuickbooksController@getAuthUrl');
//    Route::get('/quickbooks/processToken/', 'QuickbooksController@processToken');
    Route::get('/quickbooks/getCompanyInfo', 'QuickbooksController@getCompanyInfo');


    Route::post('/initiate-bid', 'InitiateBidController@send')->middleware('quickbook.token');

    // TaskController
    Route::post('/bid/tasks', 'TaskController@bidTasks');
    Route::post('/bid/tasks/reopen', 'TaskController@reopenTask');
    Route::post('/task/deny', 'TaskController@denyTask');
    Route::post('/task/image', 'TaskController@uploadTaskImage');
    Route::delete('/task/image/{taskImage}', 'TaskController@deleteImage');

    // JobController
    Route::resource('/job', 'JobController');
    Route::get('/jobtask/{jobTaskId}', 'TaskController@getJobTask');
    Route::get('/jobsPage', 'JobController@jobsPage');
    Route::get('/jobs', 'JobController@jobs');
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

    // Tasks
    Route::post('/task/notify', 'TaskController@notify')->middleware('quickbook.token');


    Route::post('/paidWithCashMessage', 'JobController@paidWithCashMessage');

}
);


Route::group(['middleware' => ['auth']], function () {
    Route::post('/home', 'HomeController@create');
    Route::post('/', 'HomeController@create');
    Route::get(
        '/furtherInfo', function () {

//        dd('further Info');

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

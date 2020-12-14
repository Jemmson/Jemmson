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
use Illuminate\Support\Facades\Artisan;

Route::get('/refreshDatabase', function () {

    if (env('APP_ENV') == 'testing') {
        Artisan::call('migrate:fresh');
    }

});



Route::get('/', 'WebController@index');
Route::get('/home', 'WebController@index');
Route::post('/loadFeatures', 'LoadController@load');
Route::post('/hooks', 'StripeHooksController@hooks');

Route::get('/loggedIn', 'UserController@loggedIn');

Route::get('checkAuth', 'UserController@checkAuth');

Route::get('/welcome', 'WelcomeController@show');

Route::get('/search/{company_name}', 'ContractorController@getContractors');

Route::get('/quickbooks/getAuthUrl/{state}', 'QuickbooksController@getAuthUrl');
Route::get('/quickbooks/processToken', 'QuickbooksController@processToken');
Route::get('/quickbooks/getCachedCompanyInfo', 'QuickbooksController@getCachedCompanyInfo');
Route::post('/register/contractor', 'RegisterController@registerContractor');
Route::post('/task/addTask', 'TaskController@addTask');

Route::get('/pwr', function () {
    return view('reset')
        ->with(['token' => 1234, 'email' => 'sdf@asdsa.com']);
});

// login routes
Route::get('login', 'Auth\LoginController@show');
Route::post('login', ['as' => 'login', 'uses' => 'Auth\LoginController@login']);

//Route::post('/verification/supportingDocs', 'StripeExpressController@supportingDocs');
//Route::get('/verification/supporting', 'StripeExpressController@supporting');

Route::group(['middleware' => ['auth', 'further.info']], function () {

    Route::get('/assessor/{location}', 'AssessorController@getLocation');
    Route::post('/location/update', 'JobController@updateLocation');

    Route::get('/feedback', 'FeedbackController@show');

    Route::get('/customer/search', 'CustomerController@getCustomerAssociatedToContractor');

    Route::post('/search/task', 'TaskController@getTasks');
    Route::post('/verification/supportingDocs', 'StripeExpressController@supportingDocs');

    Route::get('/quickbooks/getCompanyInfo', 'QuickbooksController@getCompanyInfo');

    Route::post('/initiate-bid', 'InitiateBidController@send')->middleware('quickbook.token');

    // TaskController
    Route::post('/bid/tasks', 'TaskController@bidTasks');
    Route::get('/getJobs', 'JobController@getJobs');
    Route::get('/getTasks', 'JobController@getTasks');
    Route::post('/bid/tasks/reopen', 'TaskController@reopenTask');
    Route::post('/task/deny', 'TaskController@denyTask');
    Route::post('/task/accept', 'TaskController@accept');
    Route::post('/jobTask/delete', 'TaskController@deleteJobTask');
    Route::post('/task/image', 'TaskController@uploadTaskImage');
    Route::delete('/task/image/{taskImage}', 'TaskController@deleteImage');
    Route::get('/getAllTaskIdsForJob/{jobId}', 'TaskController@getAllTaskIdsForJob');
    Route::get('/getJobTaskForGeneral/{task}/{userId}', 'JobTaskController@getJobTaskForGeneral');
    Route::get('/getImagesNotAssociatedToATask/{jobId}', 'TaskImagesController@getImagesNotAssociatedToATask');
    Route::post('/associateImagesToTasks', 'TaskImagesController@associateImagesToTasks');
    Route::get('/jobImages/{id}', 'JobController@jobImages');
    Route::get('/job/getLatestJobNumber', 'JobController@getLatestJobNumber');
    Route::post('/task/updateMessage', 'TaskController@updateMessage');

    Route::post('user/profileImage', 'UserController@uploadProfileImage');

    Route::post('subscription/plan', 'SubscriptionController@plan');
    Route::post('subscription/changePlan', 'SubscriptionController@changePlan');
    Route::post('subscription/cancelPlan', 'SubscriptionController@cancelPlan');
    Route::post('subscription/updatePaymentMethod', 'SubscriptionController@updatePaymentMethod');
    Route::get('subscription/getPaymentMethods', 'SubscriptionController@getPaymentMethods');
    Route::post('subscriptions/setPaymentMethod', 'SubscriptionController@setPaymentMethod');
    Route::get('subscription/getPaymentIntent', 'SubscriptionController@getPaymentIntent');
    Route::get('subscription/getInvoices', 'SubscriptionController@getInvoices');

    // JobController
    Route::resource('/job', 'JobController');
    Route::get('/jobtask/{jobTaskId}', 'TaskController@getJobTask');
    Route::get('/jobs', 'JobController@jobs');
    Route::get('/getJobsForCustomer', 'JobController@getJobsForCustomer');
    Route::get('/jobsPage', 'JobController@jobsPage');
    Route::post('/bid/job/decline', 'JobController@declineJobBid');
    Route::post('job/approve/{job}', 'JobController@approveJob');
    Route::post('job/delete', 'JobController@destroy');
    Route::post('job/onyourway', 'JobController@onyourway');
    Route::get('invoices', 'JobController@getInvoices');
    Route::get('invoice/{job}', 'JobController@getInvoice');
    Route::get('/sub/invoice/{jobTask}', 'JobController@getSubInvoice');
    Route::post('job/cancel', 'JobController@cancelJobBid');
    Route::get('/getContractor/{id}', 'ContractorController@getContractor');
    Route::get('/getCustomer/{id}', 'CustomerController@getCustomer');
    Route::post('/bid/task', 'TaskController@updateBidContractorJobTask');
    Route::post('/bidTask', 'TaskController@updateBidContractorJobTask');
    Route::post('/task/finished', 'TaskController@taskHasBeenFinished');
    Route::post('/task/finished/sub', 'TaskController@taskFinishedBySubContractor');
    Route::post('/task/finished/general', 'TaskController@taskFinishedBGeneralContractor');
    Route::post('/task/general/finished', 'TaskController@taskHasBeenFinished');
    Route::post('/task/approve', 'TaskController@approveTaskHasBeenFinished');

    // Stripe routes
    Route::get('/stripe/express/connect', 'StripeController@connectExpress');
    Route::get('/stripe/express/auth', 'StripeController@expressAuth');
    Route::post('/stripe/express/dashboard', 'StripeController@createExpressDashboardLink');
    Route::post('/stripe/express/task/payment', 'StripeController@sendExpressTaskPayment');
    Route::get('/stripe/hideModal', 'ContractorController@hideStripeModal');
    Route::get('/getStripeOauthUrl/{path}', 'StripeGatewayController@getStripeOauthUrl');

    Route::post('/stripe/charge', 'StripeGatewayController@charge');


    Route::post('/stripe/customer', 'StripeController@saveCustomer');
    Route::post('/stripe/getClientSecret', 'StripeGatewayController@getClientSecret');
    Route::post('/stripe/customer/charge', 'StripeController@chargeCustomer');
    Route::post('/stripe/customer/pay/tasks', 'StripeController@payAllPayableTasks');
    Route::post('/stripe/customer/pay/tasks/cash', 'StripeController@payAllPayableTasksWithCash');
    Route::delete('/stripe/customer/card', 'StripeController@deleteCard');
    Route::post('/stripe/customer/newcard', 'StripeController@newCard');
    Route::get('/stripe/customer/removeCard/{paymentMethodId}/{customerId}', 'StripeController@removeCard');
    Route::get('/stripe/customer/getPaymentMethods/{customerStripeId}', 'StripeController@getAllPaymentMethods');
    Route::get('/stripe/contractor/getPaymentMethods/{customerStripeId}', 'StripeController@getAllContractorPaymentMethods');

    // Tasks
    Route::post('/task/notify', 'TaskController@notify')->middleware('quickbook.token');
    Route::get('/task/getAssociatedSubs/{jobTaskId}', 'TaskController@getAssociatedSubs');
    Route::post('/task/inviteSubs', 'TaskController@inviteSubs');
    Route::post('/paidWithCashMessage', 'JobController@paidWithCashMessage');
    Route::get('/email/duplicate/{email}', 'ContractorController@checkDuplicateEmail');
    Route::post('/jobTask/message', 'TaskController@setChangeMessage');
    Route::get('/contractors/getSubs', 'ContractorContractorController@getSubs');

}
);



Route::group(['middleware' => ['auth']], function () {
    Route::post('/home', 'HomeController@create');
    Route::post('/', 'HomeController@create');
    Route::get('/furtherInfo', 'UserController@furtherInfo')->middleware('block.further.info');
    Route::post('/user/savePhoto', 'UserController@savePhoto');
    Route::post('/user/changePassword', 'UserController@changePassword');
    // home controller
    Route::post('/settings/logo', 'HomeController@uploadCompanyLogo');
}
);


// passwordless login
Route::get('/login/{type}/{job_id}/{token}', 'PasswordlessController@JobBid');
// passwordless login
Route::get('/login/{type}/task/{task_id}/{token}', 'PasswordlessController@taskBid');
// passwordless login
Route::get('/login/{type}/receipt/{job_id}/{token}', 'PasswordlessController@receipt');


Route::post('/bid/customer/getName', 'CustomerController@getName');
Route::post('/customer/updateCustomerNotes', 'CustomerController@updateCustomerNotes');
Route::post('/location', 'JobTaskController@updateJTLocation');
Route::post('/jobTask/updateDetails', 'JobTaskController@updateDetails');

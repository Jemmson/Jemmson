<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\JobTask;
use App\Job;
use App\Task;

Route::get('/test', 'UserController@test')->middleware('auth:api');
Route::get('/search', 'UserController@search')->middleware('auth:api');

// TODO: need to lock these routes down
// based on user and the resources they
// try to edit



//Route::post('/search/task', function (Request $request) {
//    $jobId = $request->jobId;
//    $job = \App\Job::find($jobId);
//    $tasks = Task::select()->
//        where('contractor_id', '=', $job->contractor_id)->
//        where('name', 'like', $request->taskname.'%')->get();
//    return $tasks;
//});


// Jobs
Route::post('/job/action', 'JobController@action');
Route::get('job', 'JobController@index');
Route::get('job/{job}', 'JobController@show');
Route::post('job', 'JobController@store');
Route::put('job/{job}', 'JobController@update');
Route::delete('job/{job}', 'JobController@delete');
Route::post('job/update', 'JobController@updateJobDate');
Route::post('job/cancel', 'JobController@cancelJobBid');
Route::post('job/completed', 'JobController@jobCompleted');
Route::get('/invoices', 'JobController@getInvoices');
Route::post('/task/acceptJob', 'JobController@acceptJob');
Route::post('/task/declineJob', 'JobController@declineJob');
Route::post('/task/finishedBidNotification', 'JobController@apiFinishedBidNotification');
Route::post('/task/addTask', 'TaskController@apiAddTask');


// Tasks
Route::resource('task', 'TaskController');
Route::post('/task/notifyAcceptedBid', 'TaskController@notifyAcceptedBid');
Route::post('/task/updateTaskName', 'TaskController@updateTaskName');
Route::post('/task/updateTaskQuantity', 'TaskController@updateTaskQuantity');
Route::post('/task/updateCustomerPrice', 'TaskController@updateCustomerPrice');
Route::post('/task/acceptTask', 'TaskController@acceptTask');
Route::post('/task/updateTaskStartDate', 'TaskController@updateTaskStartDate');
Route::post('/apiLogin', 'TokenController@store');
Route::post('/initiate-bid', 'InitiateBidController@apiSend');

//Route::post('/task/updateJobStartDate', function (Request $request) {
//
//    $job = Job::find($jt->jobId);
//    $job->updateJobAgreedStartDate(date);
//
//});

Route::post('/task/delete', 'TaskController@destroy');
Route::post('/task/togglestripe', 'TaskController@toggleStripe');
Route::post('/task/checkStripeForJob', 'TaskController@checkStripeForJob');

Route::post('/user/validatePhoneNumber', 'UserController@validatePhoneNumber');

Route::post('/job/updateArea', 'JobController@updateArea');
Route::post('/job/getArea', 'JobController@getArea');

Route::post('/customer/getAddress', 'CustomerController@getAddress');

// stripe controller
Route::post('/stripe/task/cash', 'StripeController@taskPaidWithCash');

Route::post('feedback', 'HomeController@feedback');
Route::post('location', 'TaskController@updateTaskLocation');

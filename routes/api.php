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


Route::group([
    'middleware' => 'auth:api'
], function () {
    //

    Route::get('/test', function () {
        return ['name' => 'Shawn'];
    });

    Route::get('/search', function () {
        $query = Input::get('query');
        $users = \App\User::where([
            ['name', 'like', '%' . $query . '%'],
            ['usertype', '=', 'customer'],
        ])->get();
        return $users;
    });
});

// TODO: need to lock these routes down
// based on user and the resources they 
// try to edit

Route::get('/search', function (Request $request) {
    $query = $request->query('query');
    $users = \App\User::whereHas('contractor', function ($q) use ($query) {
        $q->where('company_name', 'like', '%' . $query . '%');
    })->orWhere('name', 'like', '%' . $query . '%')->where('usertype', '!=', 'customer')->with('contractor')->get();
    return $users;
});

Route::get('/customer/search', function (Request $request) {
    $query = $request->query('query');
    $users = \App\User::where('name', 'like', '%' . $query . '%')->where('usertype', '!=', 'contractor')->get();
    return $users;
});

Route::post('/search/task', function (Request $request) {
    $taskName = $request->taskname;
    $jobId = $request->jobId;
    $job = \App\Job::find($jobId);
    $tasks = DB::select("select * from tasks where id not in 
                (SELECT jt.task_id from tasks t join job_task jt on jt.task_id = t.id and deleted_at = null) 
                and contractor_id = " . $job->contractor_id . " and name like '%" . $request->taskname . "%'");
    return $tasks;
});


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
Route::post('/task/finishedBidNotification', 'JobController@finishedBidNotification');


// Tasks
Route::resource('task', 'TaskController');
Route::put('bid/task/{id}', 'TaskController@updateBidContractorJobTask');
Route::post('/task/notify', 'TaskController@notify');
Route::post('/task/notifyAcceptedBid', 'TaskController@notifyAcceptedBid');
Route::post('/task/updateTaskName', 'TaskController@updateTaskName');
Route::post('/task/updateTaskQuantity', 'TaskController@updateTaskQuantity');
Route::post('/task/updateMessage', 'TaskController@updateMessage');
Route::post('/task/updateCustomerPrice', 'TaskController@updateCustomerPrice');
Route::post('/task/accept', 'TaskController@accept');
Route::post('/task/acceptTask', 'TaskController@acceptTask');
Route::post('/task/addTask', 'TaskController@addTask');
Route::post('/task/updateTaskStartDate', function (Request $request) {

    $jt = JobTask::find($request->jobTaskId);
    $jt->updateTaskStartDate($request->date);

});
Route::post('/task/delete', 'TaskController@destroy');
Route::post('/task/approve', 'TaskController@approveTaskHasBeenFinished');
Route::post('/task/finished', 'TaskController@taskHasBeenFinished');
Route::post('/task/togglestripe', 'TaskController@toggleStripe');
Route::post('/task/checkStripeForJob', 'TaskController@checkStripeForJob');


Route::post('/user/validatePhoneNumber', function (Request $request) {
//    dd($request->num);
    $user = User::select()->where("phone", "=", $request->num)->get()->first();
    if (empty($user)) {
        return User::validatePhoneNumber($request->num);
    } else {
        return ['success', 'mobile', 'mobile', 'alreadyExists'];
    }
});

Route::post('/job/updateArea', 'JobController@updateArea');
Route::post('/job/getArea', 'JobController@getArea');

Route::post('/customer/getAddress', 'CustomerController@getAddress');

// stripe controller 
Route::post('/stripe/task/cash', 'StripeController@taskPaidWithCash');

Route::post('feedback', 'HomeController@feedback');
Route::post('location', 'TaskController@updateTaskLocation');




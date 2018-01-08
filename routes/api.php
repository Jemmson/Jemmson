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
    $users = \App\User::where('name', 'like', '%' . $query . '%')->get();
    return $users;
});

Route::post('/search/task', function (Request $request) {
    $taskName = $request->taskname;
//    $contId = $request->contractorId;
    $jobId = $request->jobId;

    // TODO: change this select statement so that I only pull tasks associated to the contractor but are not already associated to the job possibly use the validate function
    $cont = DB::select("select contractor_id 
                           from jobs 
                           where id = ?", [$jobId]);
    $contId = $cont[0]->contractor_id;

    $tasks = \App\Task::where('name', 'like', '%' . $taskName . '%')
        ->where('contractor_id', '=', $contId)
        ->get();
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

// Tasks
Route::post('/task/notify', 'TaskController@notify');
Route::post('/task/notifyAcceptedBid', 'TaskController@notifyAcceptedBid');
Route::post('/task/updateTaskName', 'TaskController@updateTaskName');
Route::post('/task/updateCustomerPrice', 'TaskController@updateCustomerPrice');
Route::post('/task/finishedBidNotification', 'TaskController@finishedBidNotification');
Route::post('/task/accept', 'TaskController@accept');
Route::post('/task/acceptTask', 'TaskController@acceptTask');
Route::post('/task/acceptJob', 'TaskController@acceptJob');
Route::post('/task/declineJob', 'TaskController@declineJob');
Route::post('/task/addTask', 'TaskController@addTask');
Route::post('/task/delete', 'TaskController@destroy');

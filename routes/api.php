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

    Route::get('/search',function(){
        $query = Input::get('query');
        $users = \App\User::where([
            ['name','like','%'.$query.'%'],
            ['usertype', '=','customer'],
        ])->get();
        return $users;
    });
});

// TODO: need to lock these routes down
// based on user and the resources they 
// try to edit

Route::get('/search',function(Request $request){
    $query = $request->query('query');
    $users = \App\User::where('name','like','%'.$query.'%')->get();
    return $users;
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

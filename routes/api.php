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
        $users = \App\User::where('name','like','%'.$query.'%')->get();
        return $users;
    });
});

Route::get('/search',function(Request $request){
    $query = $request->query('query');
    $users = \App\User::where('name','like','%'.$query.'%')->get();
    return $users;
});

Route::post('/job/action', 'JobController@action');

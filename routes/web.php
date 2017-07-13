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

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');

// contractor routes
Route::get('/contractor/initiate-bid', 'InitiateBidController@index');
Route::post('/contractor/initiate-bid', 'InitiateBidController@send');

Route::get('/contractor/bid-list', 'BidListController@contractorIndex');


// customer routes
Route::get('/customer/bid-list', 'BidListController@customerIndex');

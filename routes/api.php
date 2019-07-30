<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'Api\ClientController@login');
//Route::post('register', 'Api\ClientController@register');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('details', 'Api\ClientController@details');

    Route::resource('pointOnMap', 'Api\PointOnMapApiController');
    Route::resource('bid', 'Api\BidApiController');
    Route::post('bid/changeStatus', 'Api\BidApiController@changeStatus');
});

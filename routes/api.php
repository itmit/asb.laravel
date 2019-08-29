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
    Route::post('bid/updateCoordinates', 'Api\BidApiController@updateCoordinates');
    Route::post('client/changePhoto', 'Api\ClientController@changePhoto');
    Route::post('client/note', 'Api\ClientController@note');
    Route::post('client/updateCurrentLocation', 'Api\ClientController@updateCurrentLocation');
});

Route::post('bid/testFunc', 'Api\BidApiController@testFunc');

Route::fallback(function () {
    $code = 404;
    $response = [
        'success' => false,
        'message' => 'Page not found',
    ];

    return response()->json($response, $code);
});

Route::any('{url?}/{sub_url?}', function(){
    $code = 404;
    $response = [
        'success' => false,
        'message' => 'Page not found',
    ];

    return response()->json($response, $code);
});
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
Route::post('register', 'Api\ClientController@register');
Route::post('forgotPassword', 'Api\ResetPasswordApiController@forgotPassword');
Route::post('checkCode', 'Api\ResetPasswordApiController@checkCode');
Route::post('resetPassword', 'Api\ResetPasswordApiController@resetPassword');

Route::group(['middleware' => 'auth:api'], function () {
    // Route::post('details', 'Api\ClientController@details');

    Route::resource('pointOnMap', 'Api\PointOnMapApiController');
    // Route::resource('bid', 'Api\BidApiController');
    // Route::post('bid/changeStatus', 'Api\BidApiController@changeStatus');
    // Route::post('bid/updateCoordinates', 'Api\BidApiController@updateCoordinates');
    // Route::post('client/changePhoto', 'Api\ClientController@changePhoto');
    // Route::post('client/note', 'Api\ClientController@note');
    // Route::post('client/edit', 'Api\ClientController@edit');
    // Route::post('client/updateCurrentLocation', 'Api\ClientController@updateCurrentLocation');
    // Route::post('client/capturePayment', 'Api\ClientController@capturePayment');
    // Route::post('client/chechClientActiveStatus', 'Api\ClientController@chechClientActiveStatus');
    // Route::post('client/setActivityFrom', 'Api\ClientController@setActivityFrom');
    // Route::post('client/getPaymentStatus', 'Api\ClientController@getPaymentStatus');

    // Route::post('client/activate', 'Api\ClientController@activate');

    // Route::get('payment', 'Api\PaymentController@index');
});

Route::get('paymentSuccess', 'Api\PaymentController@showSuccess');

Route::post('client/sendSMS', 'Api\ClientController@sendSMS');

Route::post('client/checkDates', 'Api\ClientController@checkDates');

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
<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'auth.', 'middleware' => 'auth'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::resource('dispatcher', 'Web\DispatcherWebController');
    Route::resource('representative', 'Web\RepresentativeWebController');
    Route::resource('bid', 'Web\BidWebController');
    Route::resource('client', 'Web\ClientWebController');

    Route::post('bid/updateList', 'Web\BidWebController@updateList');
    Route::post('bid/updateCoordinates', 'Web\BidWebController@updateCoordinates');
    Route::post('bid/alarmSound', 'Web\BidWebController@alarmSound');
    Route::post('bid/closeByUser', 'Web\BidWebController@closeByUser');
    Route::post('clients/lastLocation', 'Web\ClientWebController@lastLocation');
    Route::post('clients/changeActivity', 'Web\ClientWebController@changeActivity');
    Route::post('clients/selectClientsByType', 'Web\ClientWebController@selectClientsByType');
    Route::post('clients/clientType', 'Web\ClientWebController@clientType');

    Route::delete('clients/delete', 'Web\ClientWebController@destroy');
    Route::delete('dispatcher/delete', 'Web\DispatcherWebController@destroy');

    Route::delete('representative/delete', 'Web\RepresentativeWebController@destroy');
});

Auth::routes();

// Route::post('messages', function(Illuminate\Http\Request $request) {
// 	App\Events\PrivateChat::dispatch($request->all());
// });

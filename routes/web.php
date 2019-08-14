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
    Route::resource('guard', 'Web\GuardWebController');
    Route::post('bid/updateList', 'Web\BidWebController@updateList');
    Route::post('clients/lastLocation', 'Web\ClientWebController@lastLocation');
    Route::post('clients/changeActivity', 'Web\ClientWebController@changeActivity');

    Route::delete('clients/delete', 'Web\ClientWebController@destroy');
    Route::delete('dispatcher/delete', 'Web\DispatcherWebController@destroy');
    Route::delete('guard/delete', 'Web\GuardWebController@destroy');
    Route::delete('representative/delete', 'Web\RepresentativeWebController@destroy');
});

Auth::routes();


Route::post('messages', function(Illuminate\Http\Request $request) {
	App\Events\PrivateChat::dispatch($request->all());
});

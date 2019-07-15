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
});

Auth::routes();


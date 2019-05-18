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

    Route::get('/dispatcher-list/', ['as' => 'dispatcherList', 'uses' => 'DispatcherListController@index']);
    Route::get('/representative-list/', ['as' => 'representativeList', 'uses' => 'RepresentativeListController@index']);

    Route::group(['as' => 'representative.', 'middleware' => ['role:super-admin|representative']], function () {
        Route::get('/create-dispatcher/', ['as' => 'createDispatcher', 'uses' => 'CreateDispatcherController@index']);

        Route::post('/create-dispatcher/', ['as' => 'createDispatcherHandler', 'uses' => 'CreateDispatcherController@createDispatcher']);
    });

    Route::group(['as' => 'admin.', 'middleware' => ['role:super-admin']], function () {
        Route::get('/create-representative/', ['as' => 'createRepresentative', 'uses' => 'CreateRepresentativeController@index']);


        Route::post('/create-representative/', ['as' => 'createRepresentativeHandler', 'uses' => 'CreateRepresentativeController@createRepresentative']);
    });

    Route::group(['as' => 'dispatcher.', 'middleware' => ['role:super-admin|representative|dispatcher']], function () {
        Route::get('/create-client/', ['as' => 'createClient', 'uses' => 'CreateClientController@index']);

        Route::post('/create-client/', ['as' => 'createClientHandler', 'uses' => 'CreateClientController@createClient']);
    });
});

Auth::routes();


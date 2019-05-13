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

// Руты для представителей.
Route::get('/representative', ['as' => 'representativeHome', 'uses' => 'HomeRepresentativeController@index']);
Route::group(['as' => 'representative.', 'middleware' => ['role:super-admin|representative']], function () {
    Route::get('/representative/create-dispatcher/', ['as' => 'createDispatcher', 'uses' => 'CreateDispatcherController@index']);

    Route::post('/representative/create-dispatcher/', ['as' => 'createDispatcherHandler', 'uses' => 'CreateDispatcherController@createDispatcher']);
});


Route::get('/', function () {

    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

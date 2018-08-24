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

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('challenge', 'Base\\ChallengeController@index');
    Route::get('challenge/list', 'Base\\ChallengeController@list');
    Route::get('challenge/info', 'Base\\ChallengeController@info');
    Route::post('challenge/add', 'Base\\ChallengeController@add');
    Route::put('challenge/edit', 'Base\\ChallengeController@edit');
    Route::delete('challenge/remove', 'Base\\ChallengeController@remove');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('user', 'Base\\UserController@index');
    Route::get('user/info', 'Base\\UserController@info');
    Route::post('user/edit', 'Base\\UserController@edit');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('bank', 'Base\\BankController@index');
    Route::post('bank/add', 'Base\\BankController@add');
});
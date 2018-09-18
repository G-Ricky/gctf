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
    Route::get('', 'Base\\ChallengeController@index');
    Route::get('challenge', 'Base\\ChallengeController@index');
    Route::get('challenge/list', 'Base\\ChallengeController@list');
    Route::get('challenge/info', 'Admin\\ChallengeController@info');
    Route::post('challenge/add', 'Admin\\ChallengeController@add');
    Route::put('challenge/edit', 'Admin\\ChallengeController@edit');
    Route::post('challenge/remove', 'Admin\\ChallengeController@remove');
    Route::get('challenge/detail', 'Base\\ChallengeController@detail');
    Route::post('flag', 'Base\\ChallengeController@submitFlag');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('user', 'Base\\UserController@index');
    Route::get('user/info', 'Base\\UserController@info');
    Route::post('user/edit', 'Base\\UserController@edit');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('bank', 'Base\\BankController@index');
    Route::post('bank/add', 'Admin\\BankController@add');
    Route::get('bank/list', 'Admin\\BankController@list');
});

Route::group([], function() {
    Route::get('submission', 'Admin\\SubmissionController@index');
    Route::get('submissions', 'Admin\\SubmissionController@list');
    Route::post('submission', 'Admin\\SubmissionContoller@add');
    Route::delete('submission', 'Admin\\SubmissionController@delete');
});

Route::group([], function() {
    Route::get('users', 'Admin\\UserController@index');
    Route::get('adm1n/users', 'Admin\\UserController@list');
});

Route::group([], function() {
    Route::get('ranking', 'Base\\RankingController@index');
});
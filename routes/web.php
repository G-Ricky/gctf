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

Route::group(['middleware' => 'auth'], function() {
    Route::get('submissions', 'Admin\\SubmissionController@index');
    Route::get('submissions/correct', 'Admin\\SubmissionController@index');
    Route::get('submissions/incorrect', 'Admin\\SubmissionController@index');
    Route::post('submission', 'Admin\\SubmissionContoller@add');
    Route::delete('submission', 'Admin\\SubmissionController@delete');
});

Route::group([], function() {
    Route::get('users', 'Admin\\UserController@index');
    Route::get('adm1n/users', 'Admin\\UserController@list');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('ranking', 'Base\\RankingController@index');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('roles', 'Admin\\RoleController@index');
    Route::get('role/{id}', 'Admin\\RoleController@role');
    Route::get('adm1n/roles', 'Admin\\RoleController@list');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('privileges', 'Admin\\AbilityController@index');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('permissions/{roleId}', 'Admin\\PermissionController@index');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('password/change', 'Auth\\ChangePasswordController@index');
});

Route::group(
    [
        'middleware' => 'auth',
        'prefix'     => 'api'
    ], function() {
        Route::get('submissions/{type}', 'Admin\\SubmissionController@list');
        Route::get('submissions', 'Admin\\SubmissionController@listAll');

        Route::get('permissions/{roleId}', 'Admin\\PermissionController@list');
        Route::put('permissions/{roleId}', 'Admin\\PermissionController@modify');

        Route::post('privilege', 'Admin\\AbilityController@add');
        Route::put('privilege', 'Admin\\AbilityController@edit');
        Route::delete('privilege', 'Admin\\AbilityController@delete');
        Route::get('privileges', 'Admin\\AbilityController@list');
        Route::get('privileges/all', 'Admin\\AbilityController@listAll');
});
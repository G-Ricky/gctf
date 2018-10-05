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
    Route::get('challenge/info', 'Admin\\ChallengeController@info');
    Route::post('challenge/add', 'Admin\\ChallengeController@add');
    Route::put('challenge/edit', 'Admin\\ChallengeController@edit');
    Route::get('challenge/detail', 'Base\\ChallengeController@detail');
    Route::post('flag', 'Base\\ChallengeController@submitFlag');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('user', 'Base\\UserController@index');
    Route::get('user/info', 'Base\\UserController@info');
    Route::post('user/edit', 'Base\\UserController@edit');
});

Route::group(['middleware' => 'auth'], function() {
    Route::post('bank/add', 'Admin\\BankController@add');
    Route::get('bank/list', 'Admin\\BankController@list');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('submissions', 'Admin\\SubmissionController@index');
    Route::get('submissions/correct', 'Admin\\SubmissionController@index');
    Route::get('submissions/incorrect', 'Admin\\SubmissionController@index');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('roles', 'Admin\\RoleController@index');
    Route::get('role/{id}', 'Admin\\RoleController@role');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('privileges', 'Admin\\AbilityController@index');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('permissions/{roleId}', 'Admin\\PermissionController@index');
});

Route::group([], function() {
    Route::get('', 'Base\\HomeController@index');
});

Route::group(
    ['middleware' => 'auth'],
    function() {

        Route::get('banks', 'Base\\BankController@index');

        Route::get('contents', 'Admin\\ContentController@index');

        Route::get('password/change', 'Auth\\ChangePasswordController@index');

        Route::get('ranking', 'Base\\RankingController@index');

        Route::get('settings', 'Admin\\SettingController@index');

        Route::get('users', 'Admin\\UserController@index');

    }
);

Route::group(
    [
        'middleware' => 'auth',
        'prefix'     => 'api'
    ],
    function() {

        Route::get('challenges', 'Base\\ChallengeController@list');
        Route::delete('challenge', 'Admin\\ChallengeController@delete');

        Route::get('contents', 'Admin\\ContentController@list');
        Route::post('content', 'Admin\\ContentController@add');
        Route::put('content', 'Admin\\ContentController@edit');
        Route::delete('content', 'Admin\\ContentController@delete');

        Route::put('password', 'Auth\\ChangePasswordController@change');

        Route::get('permissions/{roleId}', 'Admin\\PermissionController@list');
        Route::put('permissions/{roleId}', 'Admin\\PermissionController@modify');

        Route::get('privileges', 'Admin\\AbilityController@list');
        Route::get('privileges/all', 'Admin\\AbilityController@listAll');
        Route::post('privilege', 'Admin\\AbilityController@add');
        Route::put('privilege', 'Admin\\AbilityController@edit');
        Route::delete('privilege', 'Admin\\AbilityController@delete');

        Route::get('rankings', 'Base\\RankingController@list');

        Route::put('relation', 'Admin\\RoleController@change');

        Route::get('roles', 'Admin\\RoleController@list');
        Route::get('roles/all', 'Admin\\RoleController@listAll');
        Route::post('role', 'Admin\\RoleController@add');
        Route::put('role', 'Admin\\RoleController@edit');
        Route::delete('role', 'Admin\\RoleController@delete');

        Route::get('settings', 'Admin\\SettingController@list');
        Route::post('setting', 'Admin\\SettingController@add');
        Route::put('setting', 'Admin\\SettingController@edit');
        Route::delete('setting', 'Admin\\SettingController@delete');

        Route::get('submissions/{type}', 'Admin\\SubmissionController@list');
        Route::get('submissions', 'Admin\\SubmissionController@listAll');
        Route::delete('submission', 'Admin\\SubmissionController@delete');

        Route::get('users', 'Admin\\UserController@list');
        Route::post('user', 'Admin\\UserController@add');
        Route::put('user', 'Admin\\UserController@edit');
        Route::put('user/hide', 'Admin\\UserController@hide');
        Route::put('user/unhide', 'Admin\\UserController@unhide');
        Route::put('user/ban', 'Admin\\UserController@ban');
        Route::put('user/unban', 'Admin\\UserController@unban');
        Route::delete('user', 'Admin\\UserController@delete');
    }
);

Route::get('install', 'Admin\\InstallController@install');
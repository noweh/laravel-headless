<?php

use Illuminate\Http\Request;

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

$pattern = '[0-9]+';
Route::model('adminUser', 'AdminUser');
Route::pattern('adminUser', $pattern);
Route::model('client', 'Client');
Route::pattern('client', $pattern);
Route::model('show', 'Show');
Route::pattern('show', '[a-zA-Z0-9-]+');

Route::group(
    [
        'domain' => Config::get('app.domain_api'),
        'middleware' => ['cors', 'throttle:' . Config::get('app.rate_limit', 60) .',1', 'api.language', 'profile.jsonresponse', 'parseMultipartFormData']
    ],
    function () {
        Route::group(['prefix' => 'v1'], function () {
            Route::options('{any}', 'SettingController@options')->where('any', '(.*)');

            Route::post('admin/auth/login', 'Auth\Admin\AuthController@login');
            Route::get('admin/auth/logout', 'Auth\Admin\AuthController@logout');
            Route::get('admin/auth/refresh', 'Auth\Admin\AuthController@refresh');
            Route::get('admin/auth/user', 'Auth\Admin\AuthController@show');
            Route::get('admin/auth/test', 'Auth\Admin\AuthController@test');
			Route::get('admin/auth/testAndRefresh', 'Auth\Admin\AuthController@testAndRefresh');

            Route::group(
                ['middleware' => ['headerCacheControl:' . Config::get('cache.cache_control_maxage.huge')]],
                function () {
                    Route::get('settings', 'SettingController@index');
                }
            );

            Route::group(['middleware' => ['jwt.admin.verify']], function () {
                Route::apiResource('adminUsers', 'CRUD\AdminUserController');

                Route::group(
                    ['middleware' => ['headerCacheControl:' . Config::get('cache.cache_control_maxage.medium')]],
                    function () {
                        Route::apiResource('clients', 'CRUD\ClientController');
                        Route::apiResource('shows', 'CRUD\ShowController');
                    }
                );
            });
        });
    }
);

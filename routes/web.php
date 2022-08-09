<?php

use Illuminate\Support\Facades\Route;

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

// front
Route::get('donate/{type?}', 'DonateController@index')->name('index');
Route::get('code', 'DonateController@refreshCaptcha')->name('code');
Route::post('donate/{type?}', 'DonateController@checkForm');
Route::post('receive', 'DonateController@blueCallBack')->name('receive');

// admin
Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin'
], function () {
    // 登入
    Route::get('/', 'LoginController@index')->name('admin.login');
    Route::get('login', 'LoginController@index')->name('admin.login');
    Route::post('login', 'LoginController@loginAuth');

    Route::group(['middleware' => 'role.auth:admin'],function () {
        // 首頁
        Route::prefix('main')->group(function () {
            Route::get('/', 'MainController@index')->name('admin.main');
        });

        // 登出
        Route::get('logout', 'MainController@logout')->name('admin.logout');

        // 伺服器
        Route::prefix('server')->group(function () {
            Route::get('/', 'ServerController@index')->name('admin.server');
            Route::post('/', 'ServerController@create');

            Route::prefix('all')->group(function () {
                Route::get('/', 'ServerController@all');
                Route::patch('sort', 'ServerController@sort');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'ServerController@find');
                Route::patch('/', 'ServerController@update');
                Route::delete('/', 'ServerController@delete');
                Route::get('db', 'ServerController@dbConnectTest');
            });
        });

        // 商品
        Route::prefix('product')->group(function () {
            Route::get('/', 'ProductController@index')->name('admin.product');
            Route::post('/', 'ProductController@create');

            Route::prefix('all')->group(function () {
                Route::get('/', 'ProductController@all');
                Route::patch('sort', 'ProductController@sort');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'ProductController@find');
                Route::patch('/', 'ProductController@update');
                Route::delete('/', 'ProductController@delete');
            });
        });

        // 贊助紀錄
        Route::prefix('record')->group(function () {
            Route::get('/', 'RecordController@index')->name('admin.record');

            Route::prefix('all')->group(function () {
                Route::get('/', 'RecordController@all');
            });

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'RecordController@find');
            });
        });
    });
});

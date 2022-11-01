<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('products', 'API\ProductController@index');
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('checkExpired', 'AuthController@check');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
        Route::resource('products', 'API\ProductController')->except(['update']);
        Route::post('products/{product}', 'API\ProductController@updatenew');
        Route::resource('categories', 'API\CategoryController')->except(['update']);
        Route::post('categories/{category}', 'API\CategoryController@updatenew');
        Route::resource('uploads', 'API\FileController')->only("index","store","destroy","show");
        Route::post('uploads/{upload}', 'API\FileController@updatenew')->name("uploads.update");
    });
});

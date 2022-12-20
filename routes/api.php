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

Route::group(['middleware' => ['cors', 'json.response']], function () {
    // Route Login
    Route::namespace('API')
        ->name('api.user.')
        ->prefix('auth')
        ->group(function () {
            Route::post('login', 'AuthController@login')->name('login');
            Route::post('register', 'AuthController@register')->name('register');

            Route::middleware(['auth:api'])
                ->group(function () {
                    Route::get('logout', 'AuthController@logout')->name('logout');
                    Route::get('user', 'AuthController@user')->name('index');
                });
        });

    // Route Posts
    Route::namespace('API')
        ->name('posts.')
        ->prefix('posts')
        ->middleware(['auth:api','role:admin|writer|manager'])
        ->group(function () {
            Route::get('/', 'PostController@index')->name('index');
            Route::get('/{post}', 'PostController@show')->name('show');
            Route::post('/', 'PostController@store')->name('store');
            Route::put('/{post}', 'PostController@update')->name('update');
            Route::delete('/{post}', 'PostController@destroy')->name('destroy');
        });

    // Route Users
    Route::namespace('API')
        ->name('users.')
        ->prefix('users')
        ->middleware(['auth:api','role:admin'])
        ->group(function () {
            Route::get('/', 'UserController@index')->name('index');
            Route::get('/{user}', 'UserController@show')->name('show');
            Route::post('/', 'UserController@store')->name('store');
            Route::put('/{user}', 'UserController@update')->name('update');
            Route::delete('/{user}', 'UserController@destroy')->name('destroy');
        });

});

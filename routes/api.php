<?php

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

Route::prefix('v1')->group(function () {

    Route::namespace('Auth')->group(function(){
        Route::post('login', 'AuthController@login');
        Route::middleware('auth:api')->post('/logout', 'AuthController@logout');
    });

    Route::namespace('User')->group(function (){
        Route::apiResource('user', 'UserController')->only('store');
    });

    Route::middleware('auth:api')->namespace('GitHub')->group(function() {
        Route::apiResource('profiles', "ProfileGitHubController")->only('index');
        Route::get('profile/search/{username}', 'ProfileGitHubController@searchProfile');
    });

});



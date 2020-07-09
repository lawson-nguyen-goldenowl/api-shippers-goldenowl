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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('api')->group(function () {
    Route::post('login', 'userController@login');
    Route::post('register', 'userController@register');
    Route::get('places', 'placeController@all');
});

Route::group(['middleware' => 'auth:api', 'namespace' => 'api'], function () {
    Route::get('details', 'userController@details');
    
    Route::get('orders', 'orderController@all');
    Route::get('orders/{order}', 'orderController@show');
    Route::post('orders', 'orderController@create');
    Route::put('orders/{order}', 'orderController@update');
    Route::delete('orders/{order}', 'orderController@destroy');
});

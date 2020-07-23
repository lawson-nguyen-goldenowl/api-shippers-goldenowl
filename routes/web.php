<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('account', function () {
    $shipper = App\shipper::has('orders', '>', 3)->first();
    return $shipper->account;
});

Route::get('/distribute', 'api\orderController@distribute');

Route::get('/zxc', function () {
    $allOrder = App\orders::where('status', 2)->get();
    $allOrder = $allOrder->pluck('id')->all();
    App\orders::whereIn('id',$allOrder)->update([
        'status' => 1,
        'idShipper' => null
    ]);
});

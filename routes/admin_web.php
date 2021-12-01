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
Route::middleware('auth:admin_user')->prefix('admin')->name('admin.')->namespace('Backend')->group(function(){
    Route::get('/', 'PageController@home')->name('home');

    Route::resource('admin-user','AdminUserController');
    Route::get('admin-user/datatables/ssd', 'AdminUserController@ssd' );

    Route::resource('user','UserController');
    Route::get('user/datatable/ssd','UserController@ssd');

    Route::get('wallet','WalletController@index')->name('wallet.index');
    Route::get('wallet/datatable/ssd','WalletController@ssd');
    Route::get('wallet/add/amount','WalletController@addAmount');
    Route::get('wallet/reduce/amount','WalletController@reduceAmount');
});

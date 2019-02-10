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

Route::get('/checktx', 'TokenController@getTransactions');
Route::get('/sendtokens', 'TokenController@sendTokens');
Route::get('/refund/{va}', 'TokenController@refund');
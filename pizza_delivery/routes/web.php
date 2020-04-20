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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('orders')->group(function () {
    Route::get('{order}', 'OrderController@read');
    Route::get('', 'OrderController@all');
    Route::post('', 'OrderController@create');
});

Route::prefix('/products')->group(function () {
    Route::get('', 'ProductController@menu');
});
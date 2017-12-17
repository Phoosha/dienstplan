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

Route::get('/', 'PostController@index');
Route::get('plan/{year?}/{month?}', 'DutyController@index');
Route::get('duties/create', 'DutyController@create');
Route::post('duties', 'DutyController@store');
Route::get('duties/{id}', 'DutyController@edit');
Route::put('duties/{id}', 'DutyController@update');
Route::delete('duties/{id}', 'DutyController@destroy');

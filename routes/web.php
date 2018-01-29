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

Route::get('/', 'PostController@index')->name('home');

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('plan/{year?}/{month?}', 'DutyController@index');
Route::get('duties/create', 'DutyController@create');
Route::post('duties', 'DutyController@store');
Route::get('duties/{duty}', 'DutyController@edit');
Route::put('duties/{id}', 'DutyController@update');
Route::delete('duties/{duty}', 'DutyController@destroy');

Route::get('phones', 'PhoneController@index');
Route::post('phones', 'PhoneController@store');
Route::get('phones/edit', 'PhoneController@edit');
Route::delete('phones/{phone}', 'PhoneController@destroy');

Route::get('users/{user}', 'UserController@edit');
Route::post('users/{user}', 'UserController@update');
Route::delete('users/{user}', 'UserController@destroy');
Route::put('users/{user}/password', 'UserController@reset');

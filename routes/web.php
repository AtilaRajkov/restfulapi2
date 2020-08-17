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

//Auth::routes();

// Authentication Routes...
Route::get('/login', '\App\Http\Controllers\Auth\LoginController@showLoginForm')
  ->name('login');
Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login');
Route::post('/logout', '\App\Http\Controllers\Auth\LoginController@logout')
  ->name('logout');

// Password Reset Routes...
Route::get('/password/confirm', '\App\Http\Controllers\Auth\ConfirmPasswordController@showConfirmForm')
  ->name('password.confirm');
Route::post('/password/confirm', '\App\Http\Controllers\Auth\ConfirmPasswordController@confirm');
Route::post('/password/email', '\App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')
  ->name('password.email');
Route::get('/password/reset', '\App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')
  ->name('password.request');
Route::post('/password/reset', '\App\Http\Controllers\Auth\ResetPasswordController@reset')
  ->name('password.update');
Route::get('/password/reset/{token}', '\App\Http\Controllers\Auth\ResetPasswordController@showResetForm')
  ->name('password.reset');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/', function() {
  return view('welcome');
})->middleware('guest');



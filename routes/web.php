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

Route::get('/', function () {
    return redirect()->route('start');
});

Route::get('redirect', 'Auth\LoginController@redirect')
	->name('oauth.redirect');

Route::get('login', 'Auth\LoginController@login')
	->name('oauth.login');

Route::get('start', 'Auth\LoginController@start')
	->name('oauth.start');

Route::get('users/{user}/profile', 'UserController@show')
	->name('profile');

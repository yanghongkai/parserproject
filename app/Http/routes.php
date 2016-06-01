<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('help', function() { return view('user.help');});
Route::get('tree', function() { return view('user.tree');});


Route::get('auth/namelogin', 'Auth\AuthController@namegetLogin');
Route::post('auth/namelogin', 'Auth\AuthController@namepostLogin');

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

Route::controllers([
	'password' => 'Auth\PasswordController',
]);


Route::get('/', 'fenxiController@index');

Route::get('fenxi','fenxiController@index');
Route::post('fenxi','fenxiController@index');



Route::get('guize/','guizeController@index');
Route::post('guize','guizeController@index');

Route::get('newguize','guizeController@newguize');
Route::post('newguize','guizeController@create');
Route::get('deleteguize/{id}/{name}','guizeController@delete');
Route::post('editguize','guizeController@update');


Route::get('cidian','cidianController@index');
Route::post('cidian','cidianController@index');

Route::get('newcidian','cidianController@newcidian');
Route::post('newcidian','cidianController@create');
Route::get('deletecidian/{id}/{name}','cidianController@delete');
Route::post('editcidian','cidianController@update');


 Route::get('usermanagement', 'userController@usermanagement');
 Route::get('authedit/{id}', 'userController@authedit'); 
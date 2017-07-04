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
use App\ReceiveData;
use Illuminate\Http\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk as LaravelFacebookSdk;

Route::get('/', function () {
    return view('welcome');

});

Route::get('/facebook/login', 'LoginController@login');

// Endpoint that is redirected to after an authentication attempt
Route::get('/facebook/callback', 'CallbackController@callback');

Route::get('/regis', 'RegisterController@Register');

Route::any('/take', 'TakeController@Take');

Route::get('/getdata','GetDataController@getdata');

Route::any('/getdetail','GetDataController@getdetail');

Route::any('/chat','TakeController@onChat');

Route::any('/picture','TakeController@onPicture');
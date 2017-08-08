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

Route::any('/chat','TakeController@onChat');

Route::any('/picture','TakeController@onPicture');

Route::any('/like','TakeController@onLike');

Route::any('/delete','TakeController@onDelete');

Route::any('/hide','TakeController@onHide');

Route::any('/inbox','TakeController@onInbox');

Route::any('/save','SaveReceiptController@Save');

Route::any('/search','GetDataController@search');

Route::get('/getdata','GetDataController@getdata');

Route::get('/getmoredata','GetDataController@getmoredata');

Route::get('/getmoredata2','GetDataController@getmoredata2');

Route::any('/getdetail','GetDataController@getdetail');

Route::any('/searchReceiptList','GetDataController@searchReceiptList');

Route::any('/DeleteReceiptList','GetDataController@DeleteReceiptList');

Route::any('/searchProductList','GetDataController@searchProductList');

Route::any('/UpdateProductList','GetDataController@UpdateProductList');

Route::any('/DeleteProductList','GetDataController@DeleteProductList');

Route::any('/InsertProductList','GetDataController@InsertProductList');

Route::any('/searchCustomerList','GetDataController@searchCustomerList');

Route::any('/UpdateCustomerList','GetDataController@UpdateCustomerList');

Route::any('/DeleteCustomerList','GetDataController@DeleteCustomerList');

Route::any('/InsertCustomerList','GetDataController@InsertCustomerList');

Route::any('/read','GetDataController@read');

Route::any('/getProvince','GetDataController@getProvince');

Route::any('/getDistrict','GetDataController@getDistrict');

Route::any('/receiptlist','ReceiptListController@get');

Route::any('/productlist','ProductListController@get');

Route::any('/customerlist','CustomerListController@get');

Route::any('/receiptdetail','ReceiptDetailListController@get');

Route::any('/more','MoreController@get');

Route::any('/logout','LogOutController@logout');

Route::any('/option','OptionController@get');

Route::any('/SearchOption','OptionController@SearchOption');

Route::any('/UpdateOption','OptionController@UpdateOption');

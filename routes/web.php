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
Route::get('/checkout', 'HomeController@checkout')->name('checkout');
Route::get('/demo', function()
{
	return view('demo');})->name('demo');

Route::get('/chat', function()
{
	return view('chat');})->name('chat');

	Route::post('payment/process', 'HomeController@paymentProcess')->name('payment.process');

		Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
		Route::get('author/status_active', 'AuthorController@status_active')->name('status_active');
		Route::get('author/status_deactive', 'AuthorController@status_deactive')->name('status_deactive');
		Route::get('author/delete_all', 'AuthorController@delete_all')->name('delete_all');
    	Route::put('author/{id}/status', 'AuthorController@status');
		Route::resource('author', 'AuthorController');
    	Route::put('book/{id}/status', 'BookController@status');
    	Route::resource('book', 'BookController');
    	Route::put('category/{id}/status', 'CategoryController@status');
    	Route::resource('category', 'CategoryController');
    	Route::put('media/{id}/status', 'MediaController@status');
    	Route::resource('media', 'MediaController');
    	route::put('team/{id}/status', 'TeamController@status');
    	Route::resource('team', 'TeamController');
	  	Route::post('/updatepassword', 'HomeController@update_password')->name('update.password');
		Route::get('profile', 'HomeController@profile')->name('profile');
		Route::put('/profile/update', 'HomeController@profile_update')->name('profile.update');
	});




Route::get('/contact', 'MainController@contact')->name('contact');
Route::get('/author_detail/{slug}', 'MainController@author_detail')->name('author_detail');
Route::get('/author', 'MainController@author')->name('author');
Route::get('/gallery', 'MainController@gallery')->name('gallery');
Route::get('/about', 'MainController@about')->name('about');
Route::get('/book_detail/{slug}', 'BookController@detail')->name('book.detail');
Route::get('/category_detail/{slug}', 'CategoryController@detail')->name('category.detail');
Route::get('/', 'MainController@index');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');


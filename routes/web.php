<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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

// telegram bot
Route::post('telegram-bot-4wJDmjWysZdp2j9c', "TelegramBotController@index")->name('telegram-bot');
Route::get('telegram-bot/sethook-4wJDmjWysZdp2j9c', "TelegramBotController@sethook")->name('telegram-bot.sethook');
Route::get('telegram-bot/deletehook-4wJDmjWysZdp2j9c', "TelegramBotController@deletehook")->name('telegram-bot.deletehook');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', "HomeController@index")->name('home');
    // Route::resource('categories', 'CategoryController');
    Route::resource('products', 'ProductController');
    Route::resource('orders', 'OrderController');
    // Route::resource('reviews', 'ReviewController');
    Route::resource('users', 'UserController');
    Route::resource('galleries', 'GalleryController');
});
Auth::routes(['register' => false, 'reset' => false]);

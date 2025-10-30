<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\YearMiddleware;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\AuthController@me');
});

Route::group([
    'middleware' => ['api', YearMiddleware::class],
    'prefix' => 'books'
], function ($router) {
    Route::get('get/{year}', 'App\Http\Controllers\BooksController@getBooksByYear');
    Route::get('unreaded/get/{year}', 'App\Http\Controllers\BooksController@getUnreadedBooksByYear');
    Route::post('insert', 'App\Http\Controllers\BooksController@insertBook');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'books'
], function ($router) {
    Route::get("years", 'App\Http\Controllers\BooksController@getReadingYears');
    Route::get('current', 'App\Http\Controllers\BooksController@getCurrentReadingBookOfUser');
    Route::get('get/{id}', 'App\Http\Controllers\BooksController@getBook');
    Route::put('get/{id}/update', 'App\Http\Controllers\BooksController@updateBook');
    Route::delete('get/{id}/delete', 'App\Http\Controllers\BooksController@deleteBook');
});



<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AccountController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'account'],function(){
    Route::group(['middleware'=>'guest'],function(){
    Route::get('register',[AccountController::class,'register'])->name('account.register');        Route::post('process/register',[AccountController::class,'processREgister'])->name('account.processRegister');
    Route::get('login',[AccountController::class,'login'])->name('account.login');
    Route::post('login',[AccountController::class,'authenticate'])->name('account.authenticate');

     });
    Route::group(['middleware'=>'auth'],function(){
    Route::get('profile',[AccountController::class,'profile'])->name('account.profile');
    Route::get('logout',[AccountController::class,'logout'])->name('account.logout');
    Route::post('update-profile',[AccountController::class,'updateProfile'])->name('account.updateProfile');
    Route::get('books',[BookController::class,'index'])->name('books.index');
    Route::get('books/create',[BookController::class,'create'])->name('books.create');
    Route::post('books-create',[BookController::class,'store'])->name('books.store');

     });
});

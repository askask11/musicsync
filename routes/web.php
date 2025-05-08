<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SheetController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\GoogleController;

//User Authentication
Route::get('/', [HomeController::class,'index'])->name('index');
Route::get('/login',[LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',[LoginController::class, 'login'])->name('login.authenticate');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');
Route::get('/register',[RegisterController::class, 'index'])->name('register');
Route::post('/register',[RegisterController::class, 'register'])->name('register.store');//Google Login
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


Route::middleware(['auth'])->group(function () {
    Route::post('/upload',[FileUploadController::class,'upload'])->name('upload');
    Route::get('/sheets',[SheetController::class,'index'])->name('sheets.index');
    Route::get('/sheets/create',[SheetController::class,'createPage'])->name('sheets.create');


    Route::post('/sheets/create',[SheetController::class,'create'])->name('sheets.create.submit');
    Route::put('/sheets/update',[SheetController::class,'update'])->name('sheets.update');
    Route::delete('/sheets/delete',[SheetController::class,'delete'])->name('sheets.delete');
    Route::get('/sheets/toggleFavorite', [FavouriteController::class, 'toggleFavourite'])->name('sheets.favourite');
    Route::post('/sheets/{sheet}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/favorites', [FavouriteController::class, 'index'])->name('favorites.index');

});
Route::get('/sheets/{sheet}',[SheetController::class,'show'])->name('sheets.show');
Route::get('/sheets/{sheet}/edit',[SheetController::class,'edit'])->name('sheets.edit');
//delete
Route::delete('/sheets/{sheet}/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::put('/sheets/{sheet}/comments/{comment}', [CommentController::class, 'edit'])->name('comments.update');

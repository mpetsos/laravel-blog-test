<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

// Public post routes
Route::resource('posts', PostController::class);

// Categories (CRUD for authenticated users)
Route::resource('categories', CategoryController::class)->middleware('auth');

// Comments (auth required to store/update/delete)
Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('auth');
Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update')->middleware('auth');
Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy')->middleware('auth');

// Manual Auth (register, login, logout)
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

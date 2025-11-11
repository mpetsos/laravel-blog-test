<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}/{slug}', [PostController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/users/{id}/posts', [UserController::class, 'posts']);
Route::get('/users/{id}/comments', [UserController::class, 'comments']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout']);
	Route::get('/users/{userId}/comments', [AuthController::class, 'comments']);
});

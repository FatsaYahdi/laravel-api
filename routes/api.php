<?php

use App\Http\Controllers\API\CommentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;

// Authenticate
Route::post('/register', [RegisterController::class , 'register']); // register
Route::post('/login', [LoginController::class, 'login']); // login

// view & update profile
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']); // view
    Route::put('/profile', [ProfileController::class, 'update']); // update
    Route::post('/logout', [LoginController::class, 'logout']); // logout
});

// CRUD user
Route::post('/users', [UserController::class, 'create']); // create
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class , 'index']); // show all
    Route::get('/users/{id}', [UserController::class , 'show']); // show single
    Route::put('/users/{id}', [UserController::class, 'update']); // update
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // delete
});

// CRUD Post
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
});

// comment
Route::middleware('auth:sanctum')->controller(CommentController::class)->group(function () {
    Route::get('/post/{postId}/comments', 'index');
    Route::post('/post/{postId}/comments', 'store');
    Route::put('/comment/{id}', 'update');
    Route::delete('/comment/{id}', 'destroy');
});

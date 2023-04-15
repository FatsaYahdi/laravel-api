<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\BookmarkController;
use App\Models\Post;

Route::prefix('v1')->group(function () {
// Authenticate
Route::post('/register', [RegisterController::class , 'register']); // register
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login'); // login

// view & update profile
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']); // view
    Route::put('/profile', [ProfileController::class, 'update']); // update
    Route::post('/logout', [LoginController::class, 'logout']); // logout
});

Route::post('/password/forgot',[ResetPasswordController::class, 'token'])->middleware('throttle:reset');
Route::post('/password/reset',[ResetPasswordController::class, 'reset']);

// CRUD user
Route::post('/users', [UserController::class, 'create']); // create
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class , 'index']); // show all
    Route::get('/users/{id}', [UserController::class , 'show']); // show single
    Route::put('/users/{id}', [UserController::class, 'update']); // update
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // delete
});

// CRUD Post
Route::prefix('posts')->controller(PostController::class)->group(function() {
    Route::get('/','index');
    Route::get('/list','list')->middleware('auth:sanctum');
    Route::post('/','store')->middleware('auth:sanctum');
    Route::get('/{post}','show');
    Route::post('/{post}/edit','update')->middleware('auth:sanctum');
    Route::delete('/{post}/delete','destroy')->middleware('auth:sanctum');

    Route::get('/post/{postId}/views','views');
});

// comment
Route::controller(CommentController::class)->group(function () {
    Route::get('/post/{postId}/comments', 'index');
    Route::post('/post/{postId}/comments', 'store')->middleware('auth:sanctum');
    Route::put('/comment/{id}', 'update')->middleware('auth:sanctum');
    Route::delete('/comment/{id}', 'destroy')->middleware('auth:sanctum');
});
});
Route::post('/login', [LoginController::class, 'login']);
Route::prefix('v2')->group(function () {
    // tag
   Route::controller(TagController::class)->prefix('tag')->group(function () {
    Route::get('/','index')->middleware('auth:sanctum');
    Route::post('/create','store')->middleware('auth:sanctum');
    Route::put('/update/{tag}','update')->middleware('auth:sanctum');
    Route::delete('/delete/{tag}','destroy')->middleware('auth:sanctum');
   });
    // like
   Route::controller(LikeController::class)->group(function () {
    Route::post('/like/{post}','like')->middleware('auth:sanctum');
   });
    // saved
    Route::controller(BookmarkController::class)->prefix('saved')->group(function () {
        Route::get('/','index')->middleware('auth:sanctum');
        Route::post('/{id}/save','store')->middleware('auth:sanctum');
        Route::delete('/{id}/delete','destroy')->middleware('auth:sanctum');
    });
    // post tag
    Route::controller(PostController::class)->prefix('posts')->middleware('auth:sanctum')->group(function () {
        Route::get('/','index');
        Route::post('/','store');
        Route::get('/{post}','show');
        Route::put('/{post}','update');
        Route::delete('/{post}','destroy');
    });
    // category
    Route::controller(CategoryController::class)->prefix('category')->group(function () {
        Route::get('/','index');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/create','store');
            Route::put('/{category}/edit','update');
            Route::delete('/{category}','destroy');
            Route::get('/list','list');
        });
    });
    Route::get('/posts/tag/{id}',[PostController::class, 'taglist']);
    Route::get('/tags',[TagController::class, 'list'])->middleware('auth:sanctum');
    Route::get('/post/{id}/comments',[CommentController::class, 'show']);
});
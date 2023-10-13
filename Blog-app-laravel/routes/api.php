<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function (){
//    User
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

//    Post
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/posts/{id}', [PostController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->where('id', '[0-9]+');

//    Comment
    Route::get('/posts/{id}/comments', [CommentController::class, 'index'])->where('id', '[0-9]+');
    Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->where('id', '[0-9]+');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->where('id', '[0-9]+');

//    Like
    Route::post('/posts/{id}/likes', [LikeController::class, 'likeOrUnlike'])->where('id', '[0-9]+');
});

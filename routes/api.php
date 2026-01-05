<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;


Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logout', [LoginController::class, 'logout']);
        Route::get('/me', [LoginController::class, 'me']);
        Route::put('/me', [LoginController::class, 'updateProfile']);
    });
});


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'indexposts']);
        Route::get('/draft', [PostController::class, 'draft']);
        Route::get('/published', [PostController::class, 'published']);
        Route::get('/popular', [PostController::class, 'popular']);
        Route::get('/search', [PostController::class, 'search']);
        Route::get('/my-posts', [PostController::class, 'myPosts']);
        Route::get('/count', [PostController::class, 'count']);
        Route::post('/', [PostController::class, 'store']);
        Route::get('/{id}', [PostController::class, 'show']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::patch('/{id}/publish', [PostController::class, 'publish']);
        Route::patch('/{id}/unpublish', [PostController::class, 'unpublish']);
        Route::get('/{id}/likes', [PostController::class, 'likes']);
        Route::post('/{id}/like', [PostController::class, 'like']);
        Route::delete('/{id}/unlike', [PostController::class, 'unlike']);
        Route::post('/{id}/bookmark', [PostController::class, 'bookmark']);
        Route::delete('/{id}/unbookmark', [PostController::class, 'unbookmark']); //18
    });
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'indexcategory']);
        Route::get('/popular', [CategoryController::class, 'popular']);
        Route::get('/empty', [CategoryController::class, 'empty']);
        Route::get('/count', [CategoryController::class, 'count']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
        Route::get('/{id}/posts', [CategoryController::class, 'posts']);
        Route::get('/{id}/posts-count', [CategoryController::class, 'postsCount']);
        Route::post('/{id}/attach-post', [CategoryController::class, 'attachPost']);
    });
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/{id}/posts', [UserController::class, 'posts']);
        Route::get('/{id}/likes', [UserController::class, 'likes']);
        Route::get('/{id}/bookmarks', [UserController::class, 'bookmarks']); //7
    });
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/posts-count', [DashboardController::class, 'postsCount']);
        Route::get('/users-count', [DashboardController::class, 'usersCount']);
        Route::get('/categories-count', [DashboardController::class, 'categoriesCount']);
        Route::get('/activity', [DashboardController::class, 'activity']);
        Route::get('/recent-posts', [DashboardController::class, 'recentPosts']);
        Route::get('/top-posts', [DashboardController::class, 'topPosts']);
        Route::get('/top-categories', [DashboardController::class, 'topCategories']); //8
    });
    Route::prefix('bookmarks')->group(function () {
        Route::get('/', [PostController::class, 'myBookmarks']); //1
    });
});

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']); //1
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::resource('/posts', \App\Http\Controllers\Api\PostController::class)->except(['create', 'edit']);
    Route::resource('/categories', \App\Http\Controllers\Api\CategoryController::class);
    Route::resource('/products', \App\Http\Controllers\Api\ProductController::class);
    Route::resource('/orders', \App\Http\Controllers\Api\OrderController::class);
    Route::get('/orders/{code}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

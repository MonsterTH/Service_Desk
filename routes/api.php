<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

});

/*
|--------------------------------------------------------------------------
| CRUD resource routes (Tickets, Categories, Comments)
|--------------------------------------------------------------------------
*/
Route::apiResource('tickets',    TicketController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('comments',   CommentController::class);

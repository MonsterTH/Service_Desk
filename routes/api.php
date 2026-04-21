<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    Route::apiResource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->withTrashed();

    Route::patch('tickets/{ticket}/assign', [TicketController::class, 'assign'])
        ->middleware('role:admin|agent');
    Route::patch('tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
    Route::patch('tickets/{ticket}/priority', [TicketController::class, 'updatePriority']);

    Route::get('tickets/{ticket}/comments', [CommentController::class, 'index']);
    Route::post('tickets/{ticket}/comments', [CommentController::class, 'store']);

    Route::get('tickets/{ticket}/comments/{comment}', [CommentController::class, 'show']);
    Route::put('tickets/{ticket}/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('tickets/{ticket}/comments/{comment}', [CommentController::class, 'destroy']);

    Route::patch(
        'tickets/{ticket}/internal-comments',
        [CommentController::class, 'internal_comments']
    )
    ->middleware('role:admin|agent');

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::apiResource('categories', CategoryController::class)
        ->except(['index', 'show']);
});

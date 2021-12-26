<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionariesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WordsController;
use Illuminate\Support\Facades\Route;

// Auth
Route::prefix('/v1')->group(function () {
   Route::post('/register', [AuthController::class, 'register']);
   Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/v1')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Dictionaries
        Route::prefix('/dictionaries')->group(function () {
            Route::get('/', [DictionariesController::class, 'index']);
            Route::get('/words', [DictionariesController::class, 'allWords']);

            Route::get('/{language}', [DictionariesController::class, 'show']);
            Route::get('/{language}/words', [DictionariesController::class, 'showWords']);

            Route::post('/', [DictionariesController::class, 'store']);
            Route::delete('/{language}', [DictionariesController::class, 'destroy']);
        });

        // Words
        Route::prefix('/words')->group(function () {
            Route::get('/', [WordsController::class, 'index']);
            Route::post('/', [WordsController::class, 'store']);

            Route::get('/{id}', [WordsController::class, 'show']);
            Route::put('/{id}', [WordsController::class, 'update']);
            Route::delete('/{id}', [WordsController::class, 'destroy']);
        });

        // User
        Route::prefix('/user')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::put('/', [UserController::class, 'update']);

            Route::get('/dictionary', [UserController::class, 'currentDictionary']);
        });

        // Admin
        Route::prefix('/admin')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/users', [UserController::class, 'all']);

            Route::get('/words', [WordsController::class, 'all']);
            Route::get('/words/{id}', [WordsController::class, 'show']);
            Route::post('/words', [WordsController::class, 'store']);
            Route::put('/words/{id}', [WordsController::class, 'update']);
            Route::delete('/words/{id}', [WordsController::class, 'destroy']);
        });
    });
});

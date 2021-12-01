<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WordsController;
use App\Models\Dictionary;
use App\Models\Word;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/v1')->group(function () {
        // Words
        Route::get('/dictionary', function () {
            return Dictionary::first()->words()->get();
//            return Word::whereId(10)->first()->dictionary()->first();
        });

        Route::prefix('/words')->group(function () {
            Route::get('/', [WordsController::class, 'index']);
            Route::post('/', [WordsController::class, 'store']);
            Route::delete('/', [WordsController::class, 'destroyWordBank']);
            Route::put('/', [WordsController::class, 'updateBulk']);

            Route::get('/{id}', [WordsController::class, 'show']);
            Route::put('/{id}', [WordsController::class, 'update']);
            Route::delete('/{id}', [WordsController::class, 'destroy']);
        });

        // User
        Route::prefix('/user')->group(function () {
            Route::get('/', [UserController::class, 'index']);
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

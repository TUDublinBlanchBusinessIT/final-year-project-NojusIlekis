<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);

    // Parent-only routes (placeholder for Part 2)
    Route::middleware('api.role:parent')->prefix('parent')->name('api.parent.')->group(function () {
        // endpoints added in Part 2
    });

    // Carer-only routes (placeholder for Part 2)
    Route::middleware('api.role:carer')->prefix('carer')->name('api.carer.')->group(function () {
        // endpoints added in Part 2
    });

    // Manager-only routes (placeholder for Part 2)
    Route::middleware('api.role:manager')->prefix('manager')->name('api.manager.')->group(function () {
        // endpoints added in Part 2
    });
});

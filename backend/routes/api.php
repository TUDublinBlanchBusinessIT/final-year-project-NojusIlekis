<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ParentDataController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);

    // Parent-only routes
    Route::middleware('api.role:parent')->prefix('parent')->name('api.parent.')->group(function () {
        Route::get('/children', [ParentDataController::class, 'children'])->name('children.index');
        Route::get('/children/{child}', [ParentDataController::class, 'showChild'])->name('children.show');
        Route::get('/children/{child}/attendance', [ParentDataController::class, 'childAttendance'])->name('children.attendance');
        Route::get('/children/{child}/daily-updates', [ParentDataController::class, 'childDailyUpdates'])->name('children.daily-updates');
        Route::get('/invoices', [ParentDataController::class, 'invoices'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [ParentDataController::class, 'showInvoice'])->name('invoices.show');
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

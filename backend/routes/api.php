<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\CarerDataController;
use App\Http\Controllers\Api\ManagerDataController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MilestoneController;
use App\Http\Controllers\Api\ParentDataController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiAuthController::class, 'logout']);
    Route::get('/user', [ApiAuthController::class, 'user']);

    // Messaging (shared by parent and carer)
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
    Route::get('/messages/conversations', [MessageController::class, 'conversations']);
    Route::get('/messages/conversations/{user}', [MessageController::class, 'show']);
    Route::post('/messages', [MessageController::class, 'store']);

    // Milestone endpoints — accessible by all authenticated roles
    Route::get('/children/{child}/milestones', [MilestoneController::class, 'index']);
    Route::post('/children/{child}/milestones/{milestone}/toggle', [MilestoneController::class, 'toggle']);

    // Parent-only routes
    Route::middleware('api.role:parent')->prefix('parent')->name('api.parent.')->group(function () {
        Route::get('/children', [ParentDataController::class, 'children'])->name('children.index');
        Route::get('/children/{child}', [ParentDataController::class, 'showChild'])->name('children.show');
        Route::get('/children/{child}/attendance', [ParentDataController::class, 'childAttendance'])->name('children.attendance');
        Route::get('/children/{child}/daily-updates', [ParentDataController::class, 'childDailyUpdates'])->name('children.daily-updates');
        Route::get('/children/{child}/timeline', [ParentDataController::class, 'timeline'])->name('children.timeline');
        Route::get('/invoices', [ParentDataController::class, 'invoices'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [ParentDataController::class, 'showInvoice'])->name('invoices.show');
    });

    // Carer-only routes
    Route::middleware('api.role:carer')->prefix('carer')->name('api.carer.')->group(function () {
        Route::get('/rooms', [CarerDataController::class, 'rooms'])->name('rooms.index');
        Route::get('/rooms/{room}/children', [CarerDataController::class, 'roomChildren'])->name('rooms.children');
        Route::post('/attendance', [CarerDataController::class, 'storeAttendance'])->name('attendance.store');
        Route::post('/daily-updates', [CarerDataController::class, 'storeDailyUpdate'])->name('daily-updates.store');
        Route::post('/medication-logs', [CarerDataController::class, 'storeMedicationLog'])->name('medication-logs.store');
    });

    // Manager-only routes
    Route::middleware('api.role:manager')->prefix('manager')->name('api.manager.')->group(function () {
        Route::get('/children', [ManagerDataController::class, 'children'])->name('children.index');
        Route::get('/parents', [ManagerDataController::class, 'parents'])->name('parents.index');
        Route::get('/carers', [ManagerDataController::class, 'carers'])->name('carers.index');
        Route::get('/rooms', [ManagerDataController::class, 'rooms'])->name('rooms.index');
        Route::get('/dashboard', [ManagerDataController::class, 'dashboard'])->name('dashboard');
    });
});

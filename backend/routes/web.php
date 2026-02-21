<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Carer\AttendanceController;
use App\Http\Controllers\Manager\ReportsController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'parent'  => redirect()->route('parent.dashboard'),
        'carer'   => redirect()->route('carer.dashboard'),
        'manager' => redirect()->route('manager.dashboard'),
        default   => redirect('/'),
    };
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::view('/dashboard', 'dashboards.parent')->name('dashboard');
    });

Route::middleware(['auth', 'role:carer'])
    ->prefix('carer')
    ->name('carer.')
    ->group(function () {
        Route::view('/dashboard', 'dashboards.carer')->name('dashboard');

        
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    });

Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::view('/dashboard', 'dashboards.manager')->name('dashboard');

       
        Route::get('/reports/attendance', [ReportsController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/tasks', [ReportsController::class, 'tasks'])->name('reports.tasks');
    });

Route::get('/carer/daily_updates', function () {
    return view('carer.daily_updates.index');
});

require __DIR__.'/auth.php';
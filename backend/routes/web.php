<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:parent'])->group(function () {
    Route::view('/parent', 'dashboards.parent')->name('parent.dashboard');
});

Route::middleware(['auth', 'role:carer'])->group(function () {
    Route::view('/carer', 'dashboards.carer')->name('carer.dashboard');
});

Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::view('/manager', 'dashboards.manager')->name('manager.dashboard');
});


require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
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
})->middleware(['auth'])->name('dashboard'); // removed 'verified' for simplicity


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'role:parent'])->prefix('parent')->group(function () {
    Route::view('/dashboard', 'dashboards.parent')->name('parent.dashboard');
});

Route::middleware(['auth', 'role:carer'])->prefix('carer')->group(function () {
    Route::view('/dashboard', 'dashboards.carer')->name('carer.dashboard');
});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->group(function () {
    Route::view('/dashboard', 'dashboards.manager')->name('manager.dashboard');
});

require __DIR__ . '/auth.php';

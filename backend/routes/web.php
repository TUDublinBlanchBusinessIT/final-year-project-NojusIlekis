<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Carer\AttendanceController;
use App\Http\Controllers\Carer\DailyReportController;
use App\Http\Controllers\Carer\DailyUpdateController;
use App\Http\Controllers\Carer\MedicationController;
use App\Http\Controllers\Carer\IncidentReportController;
use App\Http\Controllers\Manager\ReportsController;
use App\Http\Controllers\Manager\MedicationLogsController;
use App\Http\Controllers\Manager\InvoiceController;
use App\Http\Controllers\Manager\ChildController;
use App\Http\Controllers\Manager\ParentController;
use App\Http\Controllers\Manager\CarerController;
use App\Http\Controllers\Manager\DailyReportsController as ManagerDailyReportsController;
use App\Http\Controllers\Parent\AcknowledgementController;
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
        Route::get('/dashboard', [AcknowledgementController::class, 'dashboard'])->name('dashboard');

        // Parent acknowledgements
        Route::get('/acknowledgements', [AcknowledgementController::class, 'index'])
            ->name('acknowledgements.index');

        Route::post('/acknowledgements/{acknowledgement}/sign', [AcknowledgementController::class, 'sign'])
            ->name('acknowledgements.sign');
    });

Route::middleware(['auth', 'role:carer'])
    ->prefix('carer')
    ->name('carer.')
    ->group(function () {

        Route::view('/dashboard', 'dashboards.carer')->name('dashboard');

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

        // Daily Reports (written + media)
        Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
        Route::post('/daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');

        // Daily Updates (structured)
        Route::get('/daily-updates', [DailyUpdateController::class, 'index'])->name('daily-updates.index');
        Route::post('/daily-updates', [DailyUpdateController::class, 'store'])->name('daily-updates.store');

        // Medication Logs
        Route::get('/medication', [MedicationController::class, 'index'])->name('medication.index');
        Route::post('/medication', [MedicationController::class, 'store'])->name('medication.store');

        // Incident Reports
        Route::get('/incident-reports', [IncidentReportController::class, 'index'])->name('incident-reports.index');
        Route::post('/incident-reports', [IncidentReportController::class, 'store'])->name('incident-reports.store');
    });

Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/reports/attendance', [ReportsController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/tasks', [ReportsController::class, 'tasks'])->name('reports.tasks');

        // Manager Daily Reports
        Route::get('/reports/daily-reports', [ManagerDailyReportsController::class, 'index'])
            ->name('reports.daily-reports.index');

        Route::get('/reports/daily-reports/{dailyReport}', [ManagerDailyReportsController::class, 'show'])
            ->name('reports.daily-reports.show');

        // Manager requests acknowledgement (signature) from parent
        Route::post('/reports/daily-reports/{dailyReport}/request-ack', [ManagerDailyReportsController::class, 'requestAck'])
            ->name('reports.daily-reports.request-ack');

        Route::get('/reports/medication', [MedicationLogsController::class, 'index'])
            ->name('reports.medication.index');

        // Manager Invoices
        Route::get('/invoices/create', [InvoiceController::class, 'create'])
            ->name('invoices.create');

        Route::post('/invoices', [InvoiceController::class, 'store'])
            ->name('invoices.store');

        Route::get('/invoices/{invoice}/items/create', [InvoiceController::class, 'createItem'])
            ->name('invoices.items.create');

        Route::post('/invoices/{invoice}/items', [InvoiceController::class, 'storeItem'])
            ->name('invoices.items.store');
            
        Route::patch('/invoices/{invoice}/discount', [InvoiceController::class, 'updateDiscount'])
            ->name('invoices.discount.update');
        
        Route::get('/invoices', [InvoiceController::class, 'index'])
            ->name('invoices.index');

        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
            ->name('invoices.show');

        Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])
            ->name('invoices.status.update');

        // Parent linking & room assignment (must be before the resource route)
        Route::get('children/{child}/link-parent', [ChildController::class, 'linkParentForm'])->name('children.link-parent');
        Route::post('children/{child}/link-parent', [ChildController::class, 'linkParent'])->name('children.link-parent.store');
        Route::delete('children/{child}/unlink-parent/{user}', [ChildController::class, 'unlinkParent'])->name('children.unlink-parent');
        Route::patch('children/{child}/assign-room', [ChildController::class, 'assignRoom'])->name('children.assign-room');

        // Children CRUD
        Route::resource('children', ChildController::class);

        // Parents CRUD
        Route::resource('parents', ParentController::class);

        // Carers CRUD
        Route::resource('carers', CarerController::class);
    });

require __DIR__.'/auth.php';
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Carer\AttendanceController;
use App\Http\Controllers\Carer\DashboardController as CarerDashboardController;
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
use App\Http\Controllers\Manager\IncidentReportsController;
use App\Http\Controllers\Manager\DailyReportsController as ManagerDailyReportsController;
use App\Http\Controllers\Parent\AcknowledgementController;
use App\Http\Controllers\Parent\ParentChildrenController;
use App\Http\Controllers\Parent\ParentIncidentController;
use App\Http\Controllers\Parent\InvoiceController as ParentInvoiceController;
use App\Http\Controllers\Parent\MessagingController;
use App\Http\Controllers\Parent\MilestoneController as ParentMilestoneController;
use App\Http\Controllers\Carer\MessagingController as CarerMessagingController;
use App\Http\Controllers\Carer\MilestoneController as CarerMilestoneController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\MessagingController as ManagerMessagingController;
use App\Http\Controllers\Parent\TimelineController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});


Route::post('/locale', function (Request $request) {
    $request->validate([
        'locale' => 'required|in:en,pt,pl,ro',
    ]);

    session(['locale' => $request->locale]);

    return back();
})->name('locale.switch');

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
        Route::post('/dashboard/enquiry', [MessagingController::class, 'storeDashboardEnquiry'])->name('dashboard.enquiry.store');

        // Parent messaging
        Route::get('/messages', [MessagingController::class, 'index'])->name('messages.index');
        Route::get('/messages/new', [MessagingController::class, 'create'])->name('messages.create');
        Route::post('/messages', [MessagingController::class, 'store'])->name('messages.store');
        Route::get('/messages/{user}', [MessagingController::class, 'show'])->name('messages.show');

        // Parent acknowledgements
        Route::get('/acknowledgements', [AcknowledgementController::class, 'index'])
            ->name('acknowledgements.index');

        Route::post('/acknowledgements/{acknowledgement}/sign', [AcknowledgementController::class, 'sign'])
            ->name('acknowledgements.sign');

        // Parent invoices
        Route::get('/invoices', [ParentInvoiceController::class, 'index'])
            ->name('invoices.index');

        Route::get('/invoices/{invoice}', [ParentInvoiceController::class, 'show'])
            ->name('invoices.show');

        Route::get('/invoices/{invoice}/print', [ParentInvoiceController::class, 'print'])
            ->name('invoices.print');


        // Parent children
        Route::get('/children', [ParentChildrenController::class, 'index'])
            ->name('children.index');

        Route::get('/children/{child}', [ParentChildrenController::class, 'show'])
            ->name('children.show');


        Route::get('/children/{child}/timeline', [TimelineController::class, 'show'])
        ->name('children.timeline');

        // Parent milestones
        Route::get('/milestones/{child}', [ParentMilestoneController::class, 'show'])
        ->name('milestones.show');

        // Parent incidents
        Route::get('/incidents', [ParentIncidentController::class, 'index'])
            ->name('incidents.index');

        Route::get('/incidents/{incident}', [ParentIncidentController::class, 'show'])
            ->name('incidents.show');

        Route::post('/incidents/{incident}/sign', [ParentIncidentController::class, 'sign'])
            ->name('incidents.sign');

    });

Route::middleware(['auth', 'role:carer'])
    ->prefix('carer')
    ->name('carer.')
    ->group(function () {

        Route::get('/dashboard', [CarerDashboardController::class, 'index'])->name('dashboard');

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

        // Milestones
        Route::get('/milestones', [CarerMilestoneController::class, 'index'])->name('milestones.index');
        Route::get('/milestones/{child}', [CarerMilestoneController::class, 'show'])->name('milestones.show');
        Route::post('/milestones/{child}/{milestone}/toggle', [CarerMilestoneController::class, 'toggle'])->name('milestones.toggle');

        // Messaging
        Route::get('/messages', [CarerMessagingController::class, 'index'])->name('messages.index');
        Route::get('/messages/new', [CarerMessagingController::class, 'create'])->name('messages.create');
        Route::post('/messages', [CarerMessagingController::class, 'store'])->name('messages.store');
        Route::get('/messages/{user}', [CarerMessagingController::class, 'show'])->name('messages.show');
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

        // Manager Messaging / Parent Enquiries
        Route::get('/messages', [ManagerMessagingController::class, 'index'])->name('messages.index');

        Route::get('/messages/{user}', [ManagerMessagingController::class, 'show'])->name('messages.show');

        Route::post('/messages', [ManagerMessagingController::class, 'store'])->name('messages.store');

        Route::get('/reports/medication', [MedicationLogsController::class, 'index'])
            ->name('reports.medication.index');

        Route::get('/reports/incidents', [IncidentReportsController::class, 'index'])
            ->name('reports.incidents');

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

        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])
            ->name('invoices.print');

        // Parent linking & room assignment
        Route::get('children/{child}/link-parent', [ChildController::class, 'linkParentForm'])
            ->name('children.link-parent');

        Route::post('children/{child}/link-parent', [ChildController::class, 'linkParent'])
            ->name('children.link-parent.store');

        Route::delete('children/{child}/unlink-parent/{user}', [ChildController::class, 'unlinkParent'])
            ->name('children.unlink-parent');

        Route::patch('children/{child}/assign-room', [ChildController::class, 'assignRoom'])
            ->name('children.assign-room');

        // Children CRUD
        Route::resource('children', ChildController::class);

        // Parents CRUD
        Route::resource('parents', ParentController::class);

        // Carers CRUD
        Route::resource('carers', CarerController::class);

        Route::patch('/reports/incidents/{incident}/status',
            [IncidentReportsController::class, 'updateStatus']
        )->name('reports.incidents.status');
    });

require __DIR__.'/auth.php';
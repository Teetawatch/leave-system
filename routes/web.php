<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\GuardChangeRequestController;
use App\Http\Controllers\DutyRosterController;
use App\Http\Controllers\EmployeeRegistrationController;
use App\Http\Controllers\ExecutiveDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

/*
|-------------------------------------------------------------------                                          -------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Storage file route for shared hosting (no symbolic links)
Route::get('/storage/{path}', function ($path) {
    $path = str_replace(['../', '..\\'], '', $path); // Prevent directory traversal
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mimeType]);
})->where('path', '.*')->name('storage.file');

// Face Attendance Storage file route (for reading student photos from face-attendance app)
Route::get('/fa-storage/{path}', function ($path) {
    $path = str_replace(['../', '..\\'], '', $path); // Prevent directory traversal

    // Path to face-attendance storage (adjust based on your server setup)
    $faceAttendancePath = base_path('../face-attendance/storage/app/public/' . $path);

    // Fallback for local development
    if (!file_exists($faceAttendancePath)) {
        $faceAttendancePath = 'c:/face-attendance/storage/app/public/' . $path;
    }

    if (!file_exists($faceAttendancePath)) {
        abort(404);
    }

    $mimeType = mime_content_type($faceAttendancePath);
    return response()->file($faceAttendancePath, ['Content-Type' => $mimeType]);
})->where('path', '.*')->name('fa-storage.file');

// Debug: Test PDF generation
Route::get('/test-pdf', function () {
    try {
        // Get first leave request
        $leaveRequest = \App\Models\LeaveRequest::with(['user', 'leaveType', 'approvals.approver'])->first();

        if (!$leaveRequest) {
            return 'No leave requests found';
        }

        if (!$leaveRequest->user) {
            return 'Leave request has no user (user_id: ' . $leaveRequest->user_id . ')';
        }

        $leaveBalance = new \App\Models\LeaveBalance([
            'user_id' => $leaveRequest->user_id,
            'leave_type_id' => $leaveRequest->leave_type_id,
            'year' => now()->year,
            'total_days' => 10,
            'used_days' => 0,
            'remaining_days' => 10,
        ]);

        $lastYearBalance = null;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('leave_request.pdf', compact('leaveRequest', 'leaveBalance', 'lastYearBalance'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('test.pdf');

    } catch (\Exception $e) {
        return '<pre>Error: ' . $e->getMessage() . "\n\n" . $e->getTraceAsString() . '</pre>';
    }
})->middleware(['auth', 'ensure.avatar']);

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Employee Self-Registration Routes (Public - no auth required)
Route::get('/employee-register', [EmployeeRegistrationController::class, 'showForm'])->name('employee.register');
Route::post('/employee-register', [EmployeeRegistrationController::class, 'register'])->name('employee.register.store');
Route::get('/api/employees/search', [EmployeeRegistrationController::class, 'searchEmployee'])->name('api.employees.search');

Route::get('/dashboard', function () {
    $user = Illuminate\Support\Facades\Auth::user();

    // Redirect executives to the Executive Dashboard
    if (in_array($user->role, ['admin', 'deputy_director', 'director'])) {
        return redirect()->route('executive.dashboard');
    }

    // 1. Vacation Balance
    // Assumes 'vacation' slug exists. For Phase 1 we seeded it.
    // If no balance record, we might show default or 0.
    // In store() we auto-create. Here let's just fetch if exists.
    $vacationType = \App\Models\LeaveType::where('slug', 'vacation')->first();
    $vacationBalance = \App\Models\LeaveBalance::where('user_id', $user->id)
        ->where('leave_type_id', $vacationType->id)
        ->where('year', now()->year)
        ->first();

    // 2. Sick Leave Usage (Count requests or Sum days)
    // "Used X times" or "Used X days"
    $sickType = \App\Models\LeaveType::where('slug', 'sick')->first();
    $sickUsageDays = \App\Models\LeaveRequest::where('user_id', $user->id)
        ->where('leave_type_id', $sickType->id)
        ->where('status', 'approved')
        ->whereYear('start_date', now()->year)
        ->sum('total_days');

    $sickUsageCount = \App\Models\LeaveRequest::where('user_id', $user->id)
        ->where('leave_type_id', $sickType->id)
        ->where('status', 'approved')
        ->whereYear('start_date', now()->year)
        ->count();

    // Personal Leave Usage
    $personalType = \App\Models\LeaveType::where('slug', 'personal')->first();
    $personalUsageDays = 0;
    $personalUsageCount = 0;

    if ($personalType) {
        $personalUsageDays = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->where('leave_type_id', $personalType->id)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        $personalUsageCount = \App\Models\LeaveRequest::where('user_id', $user->id)
            ->where('leave_type_id', $personalType->id)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->count();
    }

    // 3. Pending Requests
    $pendingCount = \App\Models\LeaveRequest::where('user_id', $user->id)
        ->whereIn('status', ['pending_supervisor', 'pending_head', 'pending_manager', 'pending_deputy_director', 'pending_director'])
        ->count();

    // 4. Colleagues on leave today (for wider visibility or just dept)
    // For simplicity: All users approved for today
    $todayLeaves = \App\Models\LeaveRequest::where('status', 'approved')
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->with('user', 'leaveType')
        ->take(5)
        ->get();

    // 5. Recent Requests (for Dashboard list)
    $recentRequests = \App\Models\LeaveRequest::where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact('vacationBalance', 'sickUsageDays', 'sickUsageCount', 'personalUsageDays', 'personalUsageCount', 'pendingCount', 'todayLeaves', 'recentRequests'));
})->middleware(['auth', 'verified', 'ensure.avatar'])->name('dashboard');

Route::middleware(['auth', 'ensure.avatar'])->group(function () {
    // Calendar Routes (Shared Leave Calendar)
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events');

    // Duty Roster Routes (ตารางเวร - ทุกคนเห็น)
    Route::get('/duty-roster', [DutyRosterController::class, 'index'])->name('duty-roster.index');
    Route::get('/duty-roster/data', [DutyRosterController::class, 'getMonthData'])->name('duty-roster.data');
    Route::get('/duty-roster/export-pdf', [DutyRosterController::class, 'exportPdf'])->name('duty-roster.export-pdf');
    Route::get('/calendar/summary', [App\Http\Controllers\CalendarController::class, 'summary'])->name('calendar.summary');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Leave Routes
    Route::put('/leave-request/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-request.cancel');
    Route::get('/leave-request/{leaveRequest}/pdf', [LeaveRequestController::class, 'exportPdf'])->name('leave-request.pdf');
    Route::resource('leave-request', LeaveRequestController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Guard Change Routes
    Route::get('/guard-change/{guardChange}/pdf', [GuardChangeRequestController::class, 'exportPdf'])->name('guard-change.pdf');
    Route::put('/guard-change/{guardChange}/cancel', [GuardChangeRequestController::class, 'cancel'])->name('guard-change.cancel');
    Route::resource('guard-change', GuardChangeRequestController::class)->only(['index', 'create', 'store', 'show']);

    // Guard Change Approval Routes (for supervisors/managers)
    Route::get('/guard-change-approvals', [GuardChangeRequestController::class, 'approvalIndex'])->name('guard-change.approvals');
    Route::post('/guard-change/{guardChange}/approve', [GuardChangeRequestController::class, 'approve'])->name('guard-change.approve');
    Route::post('/guard-change/{guardChange}/reject', [GuardChangeRequestController::class, 'reject'])->name('guard-change.reject');

    // Guard Change Director Approval Routes (for deputy_director)
    Route::get('/guard-change-director-approvals', [GuardChangeRequestController::class, 'directorApprovalIndex'])->name('guard-change.director-approvals');
    Route::post('/guard-change/{guardChange}/director-approve', [GuardChangeRequestController::class, 'directorApprove'])->name('guard-change.director-approve');

    // Guard Change Final Approval Routes (for director/ผอ.)
    Route::get('/guard-change-final-approvals', [GuardChangeRequestController::class, 'finalApprovalIndex'])->name('guard-change.final-approvals');
    Route::post('/guard-change/{guardChange}/final-approve', [GuardChangeRequestController::class, 'finalApprove'])->name('guard-change.final-approve');

    // Notifications
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.markRead');

    // Approval Routes
    Route::middleware(['role:supervisor,department_head,deputy_director,director,admin'])->group(function () {
        Route::get('/approvals', [App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{leaveRequest}/approve', [App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{leaveRequest}/reject', [App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

        // Reports Routes
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/temporary-leave', [App\Http\Controllers\TemporaryLeaveReportController::class, 'index'])->name('reports.temporary-leave');
        Route::get('/reports/temporary-leave/export', [App\Http\Controllers\TemporaryLeaveReportController::class, 'export'])->name('reports.temporary-leave.export');

        // Attendance Reports (from Face Attendance API)
        Route::get('/attendance-reports', [App\Http\Controllers\AttendanceReportController::class, 'index'])->name('attendance-reports.index');
        Route::get('/attendance-reports/pdf', [App\Http\Controllers\AttendanceReportController::class, 'exportPdf'])->name('attendance-reports.pdf');
        Route::get('/attendance-reports/summary', [App\Http\Controllers\AttendanceReportController::class, 'dashboardSummary'])->name('attendance-reports.summary');

        // Ranking Route
        Route::get('/ranking', [App\Http\Controllers\RankingController::class, 'index'])->name('ranking.index');

        // Employee Import Routes (MUST be before resource route)
        Route::get('/employees/import', [App\Http\Controllers\EmployeeController::class, 'importForm'])->name('employees.import');
        Route::post('/employees/import', [App\Http\Controllers\EmployeeController::class, 'import'])->name('employees.import.store');
        Route::post('/employees/import/preview', [App\Http\Controllers\EmployeeController::class, 'previewImport'])->name('employees.import.preview');
        Route::get('/employees/template', [App\Http\Controllers\EmployeeController::class, 'downloadTemplate'])->name('employees.template');
        Route::get('/employees/export', [App\Http\Controllers\EmployeeController::class, 'exportData'])->name('employees.export');

        // Employee Registration Approval (MUST be before resource route)
        Route::get('/employees/pending-registrations', [App\Http\Controllers\EmployeeController::class, 'pendingRegistrations'])->name('employees.pending-registrations');
        Route::post('/employees/{id}/approve-registration', [App\Http\Controllers\EmployeeController::class, 'approveRegistration'])->name('employees.approve-registration');
        Route::post('/employees/{id}/reject-registration', [App\Http\Controllers\EmployeeController::class, 'rejectRegistration'])->name('employees.reject-registration');

        // Admin Routes - Employees (resource route AFTER specific routes)
        Route::post('/employees/bulk-destroy', [App\Http\Controllers\EmployeeController::class, 'bulkDestroy'])->name('employees.bulk-destroy');
        Route::post('/employees/{id}/official-duty', [App\Http\Controllers\EmployeeController::class, 'storeOfficialDuty'])->name('employees.official-duty.store');
        Route::resource('employees', App\Http\Controllers\EmployeeController::class);

        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // Duty Roster Management Routes (Admin only)
        Route::get('/duty-roster/manage', [DutyRosterController::class, 'manage'])->name('duty-roster.manage');
        Route::post('/duty-roster/store', [DutyRosterController::class, 'store'])->name('duty-roster.store');
        Route::post('/duty-roster/bulk-store', [DutyRosterController::class, 'bulkStore'])->name('duty-roster.bulk-store');
        Route::delete('/duty-roster/destroy', [DutyRosterController::class, 'destroy'])->name('duty-roster.destroy');
        Route::get('/duty-roster/template', [DutyRosterController::class, 'downloadTemplate'])->name('duty-roster.template');
        Route::post('/duty-roster/import', [DutyRosterController::class, 'import'])->name('duty-roster.import');
        Route::post('/duty-roster/auto-schedule', [DutyRosterController::class, 'autoSchedule'])->name('duty-roster.auto-schedule');
        Route::delete('/duty-roster/clear-month', [DutyRosterController::class, 'clearMonth'])->name('duty-roster.clear-month');
        Route::post('/duty-roster/set-monthly-reserve', [DutyRosterController::class, 'setMonthlyReserve'])->name('duty-roster.set-monthly-reserve');
        Route::post('/duty-roster/exemptions', [DutyRosterController::class, 'updateExemptions'])->name('duty-roster.exemptions');
        Route::post('/duty-roster/senior/store', [DutyRosterController::class, 'storeSenior'])->name('duty-roster.senior.store');
        Route::delete('/duty-roster/senior/{id}', [DutyRosterController::class, 'destroySenior'])->name('duty-roster.senior.destroy');

        Route::resource('departments', App\Http\Controllers\DepartmentController::class)->only(['index', 'store', 'update', 'destroy']);

        // Leave Entitlements Bulk Edit (Admin only)
        Route::get('/leave-entitlements', [App\Http\Controllers\LeaveEntitlementController::class, 'index'])->name('leave-entitlements.index');
        Route::post('/leave-entitlements/bulk-update', [App\Http\Controllers\LeaveEntitlementController::class, 'bulkUpdate'])->name('leave-entitlements.bulk-update');
    });

    // Guard Change Reports (Admin, Director, Deputy Director only)
    Route::middleware(['role:admin,director,deputy_director'])->group(function () {
        Route::get('/reports/guard-change', [App\Http\Controllers\GuardChangeReportController::class, 'index'])->name('reports.guard-change');
    });

    // Executive Dashboard (Admin, Director, Deputy Director only)
    Route::middleware(['role:admin,director,deputy_director'])->group(function () {
        Route::get('/executive-dashboard', [ExecutiveDashboardController::class, 'index'])->name('executive.dashboard');
        Route::get('/executive-dashboard/department-stats', [ExecutiveDashboardController::class, 'departmentStats'])->name('executive.department-stats');
    });

});

// =============================================================================
// External Cron Routes (Shared Hosting Workaround)
// =============================================================================

// Trigger daily leave summary to LINE group
Route::get('/cron/daily-leave-summary/{secret}', function ($secret) {
    if ($secret !== env('QUEUE_WORKER_SECRET', 'my-secret-key')) {
        abort(403, 'Unauthorized');
    }

    $exitCode = Artisan::call('line:daily-leave-summary');

    return response()->json([
        'message' => 'Daily leave summary executed',
        'exit_code' => $exitCode,
        'output' => Artisan::output()
    ]);
});

// Trigger daily duty roster notification to LINE group
Route::get('/cron/daily-duty-roster/{secret}', function ($secret) {
    if ($secret !== env('QUEUE_WORKER_SECRET', 'my-secret-key')) {
        abort(403, 'Unauthorized');
    }

    $exitCode = Artisan::call('line:daily-duty-roster');

    $logs = [];
    try {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $lines = array_slice(file($logFile), -30);
            $logs = array_values(array_filter($lines, fn($l) => str_contains($l, 'duty') || str_contains($l, 'LINE') || str_contains($l, 'Error')));
        }
    } catch (\Exception $e) {}

    return response()->json([
        'message' => 'Daily duty roster notification executed',
        'exit_code' => $exitCode,
        'output' => Artisan::output(),
        'line_group_id_set' => !empty(env('LINE_GROUP_ID')),
        'line_token_set' => !empty(env('LINE_CHANNEL_ACCESS_TOKEN')),
        'recent_logs' => $logs,
    ]);
});


// New LINE Bot 2 Routes (for daily reports)
Route::get('/line/daily-leave-summary', function () {
    $exitCode = Artisan::call('line:daily-leave-summary');
    return response()->json([
        'message' => 'Daily leave summary (Bot 2) executed',
        'exit_code' => $exitCode,
        'output' => Artisan::output(),
        'debug' => [
            'bot2_token_set' => !empty(env('LINE_CHANNEL_ACCESS_TOKEN_2')),
            'bot2_group_id_set' => !empty(env('LINE_GROUP_ID_2')),
        ],
    ]);
});

Route::get('/line/daily-duty-roster', function () {
    $exitCode = Artisan::call('line:daily-duty-roster');
    return response()->json([
        'message' => 'Daily duty roster (Bot 2) executed',
        'exit_code' => $exitCode,
        'output' => Artisan::output(),
        'debug' => [
            'bot2_token_set' => !empty(env('LINE_CHANNEL_ACCESS_TOKEN_2')),
            'bot2_group_id_set' => !empty(env('LINE_GROUP_ID_2')),
            'queue_connection' => env('QUEUE_CONNECTION', 'sync'),
        ],
    ]);
});


Route::get('/queue-work/{secret}', function ($secret) {
    // Check if the secret matches the one in .env
    if ($secret !== env('QUEUE_WORKER_SECRET', 'my-secret-key')) {
        abort(403, 'Unauthorized');
    }

    // Run the queue worker for one job or until empty
    $exitCode = Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--tries' => 3
    ]);

    return response()->json([
        'message' => 'Queue worker executed',
        'exit_code' => $exitCode,
        'output' => Artisan::output()
    ]);
});

require __DIR__ . '/auth.php';


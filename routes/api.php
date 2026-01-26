<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\LeaveTypeController;
use App\Http\Controllers\Api\LeaveBalanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

Route::post('/login', [AuthController::class, 'login']);

// =============================================================================
// PROTECTED ROUTES (Require Sanctum Token)
// =============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // -------------------------------------------------------------------------
    // Authentication
    // -------------------------------------------------------------------------
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/fcm-token', [AuthController::class, 'updateFcmToken']);

    // -------------------------------------------------------------------------
    // Leave Types
    // -------------------------------------------------------------------------
    Route::get('/leave-types', [LeaveTypeController::class, 'index']);

    // -------------------------------------------------------------------------
    // Leave Balance
    // -------------------------------------------------------------------------
    Route::get('/leave-balance', [LeaveBalanceController::class, 'index']);

    // -------------------------------------------------------------------------
    // Leave Requests (CRUD)
    // -------------------------------------------------------------------------
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::get('/leave-requests/{id}', [LeaveRequestController::class, 'show']);
    Route::post('/leave-requests/{id}/cancel', [LeaveRequestController::class, 'cancel']);

    // -------------------------------------------------------------------------
    // Approvals
    // -------------------------------------------------------------------------
    Route::get('/approvals', [ApprovalController::class, 'index']);
    Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve']);
    Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject']);

    // -------------------------------------------------------------------------
    // Reports (For Admins/Commanders)
    // -------------------------------------------------------------------------
    Route::get('/reports/leave-summary', [\App\Http\Controllers\Api\ReportController::class, 'leaveSummary']);
    Route::get('/reports/guard-change', [\App\Http\Controllers\Api\ReportController::class, 'guardChangeSummary']);
});

<?php

namespace App\Http\Controllers;

use App\Models\GuardChangeRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class GuardChangeReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Only allow admin, director, deputy_director
        $allowedRoles = [
            \App\Enums\UserRole::ADMIN->value,
            \App\Enums\UserRole::DIRECTOR->value,
            \App\Enums\UserRole::DEPUTY_DIRECTOR->value,
        ];
        
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $query = GuardChangeRequest::with(['user', 'replacementUser', 'approver', 'directorApprover', 'finalApprover']);

        // Filter by date range (duty_date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('duty_date', '>=', $request->start_date)
                  ->whereDate('duty_date', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('duty_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('duty_date', '<=', $request->end_date);
        }

        // Filter by department (requester's department)
        if ($request->filled('department')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $departments = Department::all();

        return view('reports.guard-change', compact('requests', 'departments'));
    }
}

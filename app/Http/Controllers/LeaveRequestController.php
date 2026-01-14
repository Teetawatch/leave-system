<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Notifications\NewLeaveRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::where('user_id', Auth::id())
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('leave_request.index', compact('requests')); 
    }



    public function cancel(LeaveRequest $leaveRequest)
    {
        // Authorize
        if ($leaveRequest->user_id !== Auth::id()) {
            abort(403);
        }

        // Logic Check: Can only cancel if not approved/rejected yet
        if (!in_array($leaveRequest->status, ['pending_supervisor', 'pending_head'])) {
            return back()->withErrors(['msg' => 'ไม่สามารถยกเลิกคำขอที่ดำเนินการเสร็จสิ้นแล้วได้']);
        }

        $leaveRequest->status = 'cancelled';
        $leaveRequest->cancelled_at = now();
        $leaveRequest->save();

        return redirect()->route('leave-request.index')->with('status', 'ยกเลิกคำขอเรียบร้อยแล้ว');
    }

    public function create()
    {
        $leaveTypes = LeaveType::all();
        return view('leave_request.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $diffDays = $startDate->diffInDays($endDate) + 1; // Inclusive

        // --- Business Rules Validation ---

        // 1. Check specific Leave Type rules
        // Advance Notice
        if ($leaveType->requires_advance_notice) {
            $daysInAdvance = now()->diffInDays($startDate, false);
            // $daysInAdvance is negative if startDate is in past
            if ($daysInAdvance < $leaveType->advance_notice_days) {
                return back()->withErrors(['start_date' => "ประเภทการลานี้ ({$leaveType->name}) ต้องยื่นล่วงหน้าอย่างน้อย {$leaveType->advance_notice_days} วัน"]);
            }
        }

        // Retroactive Check (if NOT required advance notice, e.g. Sick Leave)
        if (!$leaveType->allows_retroactive) {
            if ($startDate->isPast() && !$startDate->isToday()) {
                 return back()->withErrors(['start_date' => "ประเภทการลานี้ ({$leaveType->name}) ไม่สามารถยื่นย้อนหลังได้"]);
            }
        } else {
            // Allowed retroactive but maybe limit to 7 days?
            // "ป่วย — ยื่นย้อนหลังได้ (เช่น ยื่นภายใน N วัน...)"
            // Let's implement strict Check: created_at vs start_date
            // If I am submitting TODAY (now), and start_date was 7 days ago.
            // limit to configurable N=7
            $daysPast = $startDate->diffInDays(now(), false);
            if ($daysPast > 7) {
                 return back()->withErrors(['start_date' => "ไม่สามารถยื่นย้อนหลังเกิน 7 วันได้"]);
            }
        }

        // 3. Check Temporary Leave Time Constraints
        if ($leaveType->slug === 'temporary') {
            $request->validate([
                'temporary_leave_period' => 'required|in:morning,afternoon',
            ], [
                'temporary_leave_period.required' => 'กรุณาเลือกช่วงเวลาลาชั่วกาล',
                'temporary_leave_period.in' => 'ช่วงเวลาไม่ถูกต้อง',
            ]);

            // Must be today only
            $today = Carbon::today();
            if (!$startDate->isSameDay($today) || !$endDate->isSameDay($today)) {
                return back()->withErrors(['start_date' => 'ลาชั่วกาลสามารถลาได้เฉพาะวันนี้เท่านั้น']);
            }

            // Must be single day
            if (!$startDate->isSameDay($endDate)) {
                return back()->withErrors(['end_date' => 'ลาชั่วกาลสามารถลาได้แค่ 1 วันเท่านั้น']);
            }

            $currentHour = (int) now()->setTimezone('Asia/Bangkok')->format('H');
            $period = $request->temporary_leave_period;

            if ($period === 'morning' && $currentHour >= 6) {
                return back()->withErrors(['temporary_leave_period' => 'ลาชั่วกาลช่วงเช้าต้องยื่นก่อน 06:00 น. (ขณะนี้เลยเวลาแล้ว)']);
            }
            if ($period === 'afternoon' && $currentHour >= 11) {
                return back()->withErrors(['temporary_leave_period' => 'ลาชั่วกาลช่วงบ่ายต้องยื่นก่อน 11:00 น. (ขณะนี้เลยเวลาแล้ว)']);
            }
        }
        // 4. Check Balance (Skip for temporary leave - no balance deduction)
        if ($leaveType->slug !== 'temporary') {
            // Ensure Balance record exists
            $currentYear = now()->year;
            $balance = LeaveBalance::firstOrCreate(
                ['user_id' => $user->id, 'leave_type_id' => $leaveType->id, 'year' => $currentYear],
                [
                    'total_days' => $leaveType->max_days_per_year,
                    'used_days' => 0,
                    'remaining_days' => $leaveType->max_days_per_year
                ]
            );

            if ($balance->remaining_days < $diffDays) {
                return back()->withErrors(['leave_type_id' => "วันลาคงเหลือไม่เพียงพอ (เหลือ {$balance->remaining_days} วัน, ต้องการ {$diffDays} วัน)"]);
            }
        }

        // --- Create Request ---
        
        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        if ($request->leave_type_id) {
            $type = LeaveType::find($request->leave_type_id);
            if ($type && $type->slug == 'sick') {
                $request->validate([
                    'addr_house' => 'required|string',
                    'addr_province' => 'required|string',
                ]);
            } elseif ($type && $type->slug == 'personal') {
                $request->validate([
                    'personal_location' => 'required|string',
                    'personal_province' => 'required|string',
                ]);
            }
        }

        // Store Address as Array/JSON
        $contactAddress = null;
        // Handle contact address for sick leave
        if ($leaveType->slug === 'sick') {
            $contactAddress = [
                'house' => $request->addr_house,
                'road' => $request->addr_road,
                'tambon' => $request->addr_tambon,
                'amphoe' => $request->addr_amphoe,
                'province' => $request->addr_province,
            ];
        } elseif ($leaveType->slug === 'personal') {
            $contactAddress = [
                'house' => $request->personal_location,
                'road' => '-', // Personal leave might not have a road, tambon, amphoe
                'tambon' => '-',
                'amphoe' => '-',
                'province' => $request->personal_province,
            ];
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $diffDays,
            'reason' => $request->reason,
            'contact_address' => $contactAddress,
            'temporary_leave_period' => $leaveType->slug === 'temporary' ? $request->temporary_leave_period : null,
            'status' => 'pending_supervisor', // Initial status
            'attachment_path' => $filePath,
        ]);

        // Load the leaveType relation for notification
        $leaveRequest->load('leaveType');

        // Notify the supervisor about the new leave request
        if ($user->supervisor_id) {
            $supervisor = User::find($user->supervisor_id);
            if ($supervisor) {
                $supervisor->notify(new NewLeaveRequestNotification($leaveRequest, $user));
            }
        }
        
        return redirect()->route('dashboard')->with('status', 'ส่งคำขอเรียบร้อยแล้ว รอการอนุมัติ');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        // Simple show for now
        return view('leave_request.show', compact('leaveRequest'));
    }

    public function exportPdf(LeaveRequest $leaveRequest)
    {
        // Authorize (User can view their own, Approvers can view others)
        $user = Auth::user();
        if ($leaveRequest->user_id !== $user->id && 
            !in_array($user->role, ['supervisor', 'manager', 'department_head', 'deputy_director', 'director', 'admin'])) {
            abort(403);
        }

        $leaveRequest->load(['user', 'leaveType', 'approvals.approver']);

        $leaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year)
            ->first();

        // Create default balance if not exists
        if (!$leaveBalance) {
            $leaveBalance = new LeaveBalance([
                'user_id' => $leaveRequest->user_id,
                'leave_type_id' => $leaveRequest->leave_type_id,
                'year' => now()->year,
                'total_days' => 10,
                'used_days' => 0,
                'remaining_days' => 10,
            ]);
        }

        // Previous year balance (optional, for complete stats)
        $lastYearBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year - 1)
            ->first();

        // Determine View based on Leave Type
        $viewName = 'leave_request.pdf'; // Default (Vacation/Other)
        
        if ($leaveRequest->leaveType) {
            $slug = $leaveRequest->leaveType->slug;
            if ($slug == 'sick') {
                $viewName = 'leave_request.pdf_sick';
            } elseif ($slug == 'personal') {
                $viewName = 'leave_request.pdf_personal';
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact('leaveRequest', 'leaveBalance', 'lastYearBalance'));
        
        // Optional: Custom paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('leave-request-'.$leaveRequest->id.'.pdf');
    }
}

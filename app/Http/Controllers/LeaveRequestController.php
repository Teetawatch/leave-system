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
        // Exclude 'official-duty' from standard leave request form
        $leaveTypes = LeaveType::where('slug', '!=', 'official-duty')->get();
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
        // 1. Check specific Leave Type rules
        // Advance Notice
        // 1. Check specific Leave Type rules
        // Advance Notice
        $nowBkk = Carbon::now('Asia/Bangkok');
        $today = $nowBkk->copy()->startOfDay();
        // Parse input strictly
        $start = Carbon::parse($request->start_date)->setTimezone('Asia/Bangkok')->startOfDay();

        if ($leaveType->slug === 'personal') {
            // ลากิจ: ต้องลาล่วงหน้า 1 วัน (อย่างน้อยเริ่มวันพรุ่งนี้)
            // ตัวอย่าง: ยื่นวันที่ 1, ลาได้เร็วสุดวันที่ 2
            $minDate = $today->copy()->addDays(1);
            if ($start->lessThan($minDate)) {
                return back()->withErrors(['start_date' => "ลากิจต้องยื่นล่วงหน้าอย่างน้อย 1 วัน (ยื่นวันนี้ ลาได้ตั้งแต่วันที่ {$minDate->format('d/m/Y')} เป็นต้นไป)"]);
            }
        } elseif ($leaveType->slug === 'vacation') {
            // ลาพักผ่อน: ต้องลาล่วงหน้า 3 วัน
            // ตัวอย่าง: ยื่นวันที่ 1, ลาได้เร็วสุดวันที่ 4
            $minDate = $today->copy()->addDays(3);
            if ($start->lessThan($minDate)) {
                return back()->withErrors(['start_date' => "ลาพักผ่อนต้องยื่นล่วงหน้าอย่างน้อย 3 วัน (ยื่นวันนี้ ลาได้ตั้งแต่วันที่ {$minDate->format('d/m/Y')} เป็นต้นไป)"]);
            }
        } elseif ($leaveType->requires_advance_notice) {
            $minDate = $today->copy()->addDays($leaveType->advance_notice_days);
            if ($start->lessThan($minDate)) {
                return back()->withErrors(['start_date' => "ประเภทการลานี้ ({$leaveType->name}) ต้องยื่นล่วงหน้าอย่างน้อย {$leaveType->advance_notice_days} วัน (ยื่นวันนี้ ลาได้ตั้งแต่วันที่ {$minDate->format('d/m/Y')} เป็นต้นไป)"]);
            }
        }

        // Retroactive Check
        if (!in_array($leaveType->slug, ['personal', 'vacation']) && !$leaveType->allows_retroactive) {
            if ($start->isPast() && !$start->isSameDay($today)) {
                return back()->withErrors(['start_date' => "ประเภทการลานี้ ({$leaveType->name}) ไม่สามารถยื่นย้อนหลังได้"]);
            }
        } elseif (!in_array($leaveType->slug, ['personal', 'vacation'])) {
            // Retroactive limit
            $daysPast = $start->diffInDays($today, false);
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

            // Must be today only (Thai Time)
            $today = Carbon::now('Asia/Bangkok')->startOfDay();
            if (!$startDate->isSameDay($today) || !$endDate->isSameDay($today)) {
                return back()->withErrors(['start_date' => 'ลาชั่วกาลสามารถลาได้เฉพาะวันที่ทำรายการเท่านั้น (อ้างอิงเวลาประเทศไทยปัจจุบัน)']);
            }

            // Must be single day
            if (!$startDate->isSameDay($endDate)) {
                return back()->withErrors(['end_date' => 'ลาชั่วกาลสามารถลาได้แค่ 1 วันเท่านั้น']);
            }

            $currentTime = now()->setTimezone('Asia/Bangkok');
            $currentHour = (int) $currentTime->format('H');
            $currentMinute = (int) $currentTime->format('i');
            $period = $request->temporary_leave_period;

            // ลาชั่วกาลช่วงเช้าต้องยื่นก่อน 07:30 น.
            if ($period === 'morning' && ($currentHour > 7 || ($currentHour === 7 && $currentMinute >= 30))) {
                return back()->withErrors(['temporary_leave_period' => 'ลาชั่วกาลช่วงเช้าต้องยื่นก่อน 07:30 น. (ขณะนี้เลยเวลาแล้ว)']);
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
                // Send Database Notification
                $supervisor->notify(new NewLeaveRequestNotification($leaveRequest, $user));

                // Send Push Notification via FCM
                if ($supervisor->fcm_token) {
                    $fcmService = new \App\Services\FCMService();
                    $fcmService->sendNotification(
                        $supervisor->fcm_token,
                        'มีใบลาเข้ามาใหม่ 🔔',
                        "{$user->rank} {$user->name} ขอ{$leaveType->name} จำนวน {$diffDays} วัน รอการอนุมัติจากคุณ",
                        [
                            'type' => 'new_leave_request',
                            'request_id' => $leaveRequest->id,
                        ]
                    );
                }
            }
        }

        return redirect()->route('leave-request.index')->with('status', 'ส่งคำขอเรียบร้อยแล้ว รอการอนุมัติ');
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
        if (
            $leaveRequest->user_id !== $user->id &&
            !in_array($user->role, ['supervisor', 'manager', 'department_head', 'deputy_director', 'director', 'admin'])
        ) {
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

        return $pdf->stream('leave-request-' . $leaveRequest->id . '.pdf');
    }
}

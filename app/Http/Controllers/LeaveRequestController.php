<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Notifications\NewLeaveRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $requests = LeaveRequest::where('user_id', Auth::id())
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('LeaveRequest/Index', [
            'requests' => $requests->through(fn ($r) => [
                'id' => $r->id,
                'leave_type' => $r->leaveType ? ['name' => $r->leaveType->name, 'slug' => $r->leaveType->slug] : null,
                'start_date' => $r->start_date->format('Y-m-d'),
                'end_date' => $r->end_date->format('Y-m-d'),
                'start_date_thai' => $r->start_date->locale('th')->isoFormat('D MMMM YYYY'),
                'end_date_thai' => $r->end_date->locale('th')->isoFormat('D MMMM YYYY'),
                'total_days' => $r->total_days + 0,
                'reason' => $r->reason,
                'status' => $r->status,
                'attachment_path' => $r->attachment_path,
                'created_at_human' => $r->created_at->diffForHumans(),
            ]),
        ]);
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

        // Get leave balances for the current user this year, keyed by leave_type_id
        $currentYear = now()->year;
        $leaveBalances = LeaveBalance::where('user_id', Auth::id())
            ->where('year', $currentYear)
            ->get()
            ->keyBy('leave_type_id');

        return Inertia::render('LeaveRequest/Create', [
            'leaveTypes' => $leaveTypes->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'max_days_per_year' => $t->max_days_per_year,
                'requires_advance_notice' => $t->requires_advance_notice,
                'advance_notice_days' => $t->advance_notice_days,
            ]),
            'leaveBalances' => $leaveBalances->map(fn ($b) => [
                'leave_type_id' => $b->leave_type_id,
                'total_days' => $b->total_days + 0,
                'used_days' => $b->used_days + 0,
                'remaining_days' => $b->remaining_days + 0,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'leave_type_id.required' => 'กรุณาเลือกประเภทการลา',
            'leave_type_id.exists' => 'ประเภทการลาไม่ถูกต้อง',
            'start_date.required' => 'กรุณาระบุวันเริ่มต้น',
            'start_date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
            'end_date.required' => 'กรุณาระบุวันสิ้นสุด',
            'end_date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
            'end_date.after_or_equal' => 'วันสิ้นสุดต้องไม่ก่อนวันเริ่มต้น',
            'reason.required' => 'กรุณาระบุเหตุผลการลา',
            'reason.max' => 'เหตุผลต้องไม่เกิน 500 ตัวอักษร',
            'attachment.mimes' => 'รองรับเฉพาะไฟล์ jpg, jpeg, png, และ pdf เท่านั้น',
            'attachment.max' => 'ขนาดไฟล์แนบต้องไม่เกิน 5MB',
        ]);

        $user = Auth::user();
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $diffDays = $startDate->diffInDays($endDate) + 1; // Inclusive

        // --- Business Rules Validation ---

        $nowBkk = Carbon::now('Asia/Bangkok');
        $today = $nowBkk->copy()->startOfDay();
        $start = Carbon::parse($request->start_date)->setTimezone('Asia/Bangkok')->startOfDay();

        // 1. Advance Notice Check (using database settings)
        if ($leaveType->requires_advance_notice && $leaveType->enforce_advance_notice && $leaveType->advance_notice_days > 0) {
            $minDate = $today->copy()->addDays($leaveType->advance_notice_days);
            if ($start->lessThan($minDate)) {
                return back()->withErrors(['start_date' => "{$leaveType->name} ต้องยื่นล่วงหน้าอย่างน้อย {$leaveType->advance_notice_days} วัน (ยื่นวันนี้ ลาได้ตั้งแต่วันที่ {$minDate->format('d/m/Y')} เป็นต้นไป)"]);
            }
        }

        // 2. Retroactive Check (using database settings)
        if ($leaveType->enforce_retroactive_check) {
            if (!$leaveType->allows_retroactive) {
                if ($start->isPast() && !$start->isSameDay($today)) {
                    return back()->withErrors(['start_date' => "{$leaveType->name} ไม่สามารถยื่นย้อนหลังได้"]);
                }
            } else {
                // Retroactive limit from database
                $maxRetro = $leaveType->max_retroactive_days ?? 7;
                $daysPast = $start->diffInDays($today, false);
                if ($daysPast > $maxRetro) {
                    return back()->withErrors(['start_date' => "ไม่สามารถยื่นย้อนหลังเกิน {$maxRetro} วันได้"]);
                }
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

            // Allow today and future dates (Thai Time)
            if ($start->lessThan($today)) {
                return back()->withErrors(['start_date' => 'ลาชั่วกาลไม่สามารถลาล่วงลับ (ย้อนหลัง) ได้']);
            }

            // Must be single day
            if (!$start->isSameDay($endDate->setTimezone('Asia/Bangkok')->startOfDay())) {
                return back()->withErrors(['end_date' => 'ลาชั่วกาลสามารถลาได้แค่ 1 วันเท่านั้น']);
            }

            // Time constraints only apply if the leave is for TODAY
            if ($start->isSameDay($today)) {
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
        }
        // 4. Check Balance (Skip for temporary leave - no balance deduction)
        if ($leaveType->slug !== 'temporary' && $leaveType->enforce_balance_check) {
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
                    'addr_road' => 'required|string',
                    'addr_tambon' => 'required|string',
                    'addr_amphoe' => 'required|string',
                    'addr_province' => 'required|string',
                ], [
                    'addr_house.required' => 'กรุณาระบุบ้านเลขที่',
                    'addr_road.required' => 'กรุณาระบุถนน',
                    'addr_tambon.required' => 'กรุณาระบุตำบล/แขวง',
                    'addr_amphoe.required' => 'กรุณาระบุอำเภอ/เขต',
                    'addr_province.required' => 'กรุณาระบุจังหวัด',
                ]);
            } elseif ($type && $type->slug == 'personal') {
                $request->validate([
                    'personal_location' => 'required|string',
                    'personal_province' => 'required|string',
                ], [
                    'personal_location.required' => 'กรุณาระบุสถานที่ติดต่อ',
                    'personal_province.required' => 'กรุณาระบุจังหวัด',
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

        // Dispatch Event for Notifications (LINE, FCM, etc.)
        // Note: app()->terminating() does not fire reliably on shared hosting,
        // so we dispatch directly here. Guzzle timeouts (5s/15s) prevent hanging.
        try {
            Log::info('[LeaveNotify] dispatching LeaveRequestSubmitted event', [
                'id'          => $leaveRequest->id,
                'status'      => $leaveRequest->status,
                'user_id'     => $leaveRequest->user_id,
                'supervisor'  => optional($leaveRequest->user)->supervisor_id,
            ]);
            event(new \App\Events\LeaveRequestSubmitted($leaveRequest));
            Log::info('[LeaveNotify] event dispatched OK');
        } catch (\Throwable $e) {
            Log::error('[LeaveNotify] event dispatch exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return redirect()->route('leave-request.index')->with('status', 'ส่งคำขอเรียบร้อยแล้ว รอการอนุมัติ');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        // Simple show for now
        $leaveRequest->load(['user', 'leaveType', 'approvals.approver']);
        return Inertia::render('LeaveRequest/Show', [
            'leaveRequest' => [
                'id' => $leaveRequest->id,
                'user' => $leaveRequest->user ? [
                    'name' => $leaveRequest->user->name,
                    'rank' => $leaveRequest->user->rank,
                    'department' => $leaveRequest->user->department,
                    'avatar' => $leaveRequest->user->avatar,
                ] : null,
                'leave_type' => $leaveRequest->leaveType ? ['name' => $leaveRequest->leaveType->name, 'slug' => $leaveRequest->leaveType->slug] : null,
                'start_date' => $leaveRequest->start_date->format('Y-m-d'),
                'end_date' => $leaveRequest->end_date->format('Y-m-d'),
                'start_date_thai' => $leaveRequest->start_date->locale('th')->isoFormat('D MMMM YYYY'),
                'end_date_thai' => $leaveRequest->end_date->locale('th')->isoFormat('D MMMM YYYY'),
                'total_days' => $leaveRequest->total_days + 0,
                'reason' => $leaveRequest->reason,
                'status' => $leaveRequest->status,
                'contact_address' => $leaveRequest->contact_address,
                'temporary_leave_period' => $leaveRequest->temporary_leave_period,
                'attachment_path' => $leaveRequest->attachment_path,
                'created_at' => $leaveRequest->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $leaveRequest->created_at->diffForHumans(),
                'approvals' => $leaveRequest->approvals->map(fn ($a) => [
                    'step' => $a->step,
                    'status' => $a->status,
                    'comment' => $a->comment,
                    'approved_at' => $a->approved_at,
                    'approver' => $a->approver ? ['name' => $a->approver->name, 'rank' => $a->approver->rank] : null,
                ]),
            ],
        ]);
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

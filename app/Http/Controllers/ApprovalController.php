<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\LeaveStatusUpdated;
use App\Notifications\NewLeaveRequestNotification;
use App\Services\FCMService;

class ApprovalController extends Controller
{
    /**
     * หลักสูตรที่ต้องใช้ 2 ขั้นตอนอนุมัติ (หัวหน้าแผนก + ผู้บังคับบัญชา)
     */
    protected const STUDENT_COURSES = [
        'หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69',
        'หลักสูตรอาชีพเพื่อเลื่อนฐานะชั้น จ.อ.',
    ];

    /**
     * ตรวจสอบว่าผู้ใช้เป็นนักเรียนในหลักสูตรที่กำหนดหรือไม่
     */
    protected function isStudentCourse($user): bool
    {
        return in_array($user->department, self::STUDENT_COURSES);
    }

    public function index()
    {
        $user = auth()->user();

        // Fetch requests needing THIS user's action
        $requests = LeaveRequest::where(function ($query) use ($user) {
            // Case 1: Pending Supervisor (Step 1) AND User is Supervisor
            $query->where('status', 'pending_supervisor')
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('supervisor_id', $user->id);
                });
        })->orWhere(function ($query) use ($user) {
            // Case 2: Pending Manager (Step 2 for students) AND User is Manager
            $query->where('status', 'pending_manager')
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('manager_id', $user->id);
                });
        })->orWhere(function ($query) use ($user) {
            // Case 3: Pending Deputy Director (Step 2) AND User is Deputy Director
            if ($user->role === 'deputy_director') {
                $query->where('status', 'pending_deputy_director');
            }
        })->orWhere(function ($query) use ($user) {
            // Case 4: Pending Director (Step 3) AND User is Director
            if ($user->role === 'director') {
                $query->whereIn('status', ['pending_director', 'pending_deputy_director']);
            }
        })->orWhere(function ($query) use ($user) {
            // Admin sees all pending requests
            if ($user->role === 'admin') {
                $query->whereIn('status', ['pending_supervisor', 'pending_manager', 'pending_deputy_director', 'pending_director']);
            }
        })
            ->orderBy('created_at', 'desc')
            ->with('user', 'leaveType', 'approvals')
            ->paginate(10);

        return view('approvals.index', compact('requests'));
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();

        $requester = $leaveRequest->user;

        $leaveRequest->load('leaveType'); // Ensure relation is loaded

        // Get leave type info
        $leaveSlug = strtolower($leaveRequest->leaveType->slug ?? '');
        $isVacation = $leaveSlug === 'vacation' || str_contains($leaveRequest->leaveType->name ?? '', 'พักผ่อน');
        $isSickOrPersonal = in_array($leaveSlug, ['sick', 'personal']);

        // Handle Signature Upload (for steps that require it)
        $signaturePath = null;
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            // Remove data:image/png;base64, prefix
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);

            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $signaturePath = $fileName;
        } elseif ($request->input('use_saved_signature') == '1' && $user->signature) {
            // Copy existing signature to new file to maintain history integrity
            $extension = pathinfo($user->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.' . $extension;

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->copy($user->signature, $fileName);
                $signaturePath = $fileName;
            }
        }

        // ========== STEP 1: Supervisor อนุญาต (ต้องลงลายเซ็น) ==========
        if ($leaveRequest->status == 'pending_supervisor') {
            if ($requester->supervisor_id !== $user->id && !in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                abort(403, 'Not authorized for this approval stage.');
            }

            // Check if this is temporary leave - single approval only
            $isTemporary = $leaveSlug === 'temporary';

            if ($isTemporary) {
                // Temporary leave: Final approval by supervisor only
                $leaveRequest->status = 'approved';
                $leaveRequest->save();

                // Log final approval
                $leaveRequest->approvals()->create([
                    'approver_id' => $user->id,
                    'step' => 'supervisor',
                    'action' => 'approved',
                    'comment' => $request->input('comment'),
                    'signature' => $signaturePath,
                    'ip_address' => $request->ip()
                ]);

                // NO balance deduction for temporary leave

                // Notify User
                $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));

                // Send Push Notification
                if ($requester->fcm_token) {
                    (new FCMService())->sendNotification(
                        $requester->fcm_token,
                        'การลาชั่วกาลได้รับอนุมัติ ✅',
                        "ใบลาชั่วกาลของคุณได้รับการอนุมัติแล้ว",
                        ['type' => 'leave_status', 'request_id' => $leaveRequest->id]
                    );
                }

                return redirect()->back()->with('success', 'อนุมัติลาชั่วกาลเรียบร้อยแล้ว');
            }

            // ========== สำหรับนักเรียนหลักสูตร: ลากิจ/ลาป่วย ต้องผ่าน 2 ขั้นตอน ==========
            $isStudentCourse = $this->isStudentCourse($requester);
            $hasManager = !empty($requester->manager_id);

            if ($isStudentCourse && $isSickOrPersonal && $hasManager) {
                // นักเรียนหลักสูตร: ส่งไปให้ผู้บังคับบัญชาอนุมัติขั้นที่ 2
                $leaveRequest->status = 'pending_manager';
                $leaveRequest->save();

                // Log approval for step 1
                $leaveRequest->approvals()->create([
                    'approver_id' => $user->id,
                    'step' => 'supervisor',
                    'action' => 'approved',
                    'comment' => $request->input('comment'),
                    'signature' => $signaturePath,
                    'ip_address' => $request->ip()
                ]);

                // Notify Manager about the new pending approval
                $manager = User::find($requester->manager_id);
                if ($manager) {
                    $manager->notify(new NewLeaveRequestNotification($leaveRequest, $requester));

                    // Push to Manager
                    if ($manager->fcm_token) {
                        (new FCMService())->sendNotification(
                            $manager->fcm_token,
                            'มีใบลาใหม่ (รออนุมัติ) 🔔',
                            "ใบลาของ {$requester->rank} {$requester->name} รอการอนุมัติจากคุณ",
                            ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                        );
                    }
                }

                return redirect()->back()->with('success', 'อนุญาตขั้นที่ 1 เรียบร้อยแล้ว รอผู้บังคับบัญชาอนุมัติขั้นสุดท้าย');
            }

            // Move to Step 2: pending_deputy_director (for regular employees)
            $leaveRequest->status = 'pending_deputy_director';
            $leaveRequest->save();

            // Log approval for step 1
            $leaveRequest->approvals()->create([
                'approver_id' => $user->id,
                'step' => 'supervisor',
                'action' => 'approved',
                'comment' => $request->input('comment'),
                'signature' => $signaturePath,
                'ip_address' => $request->ip()
            ]);

            // Notify Deputy Director about the new pending approval
            $deputyDirectors = User::where('role', 'deputy_director')->get();
            foreach ($deputyDirectors as $deputy) {
                $deputy->notify(new NewLeaveRequestNotification($leaveRequest, $requester));

                // Push to Deputy
                if ($deputy->fcm_token) {
                    (new FCMService())->sendNotification(
                        $deputy->fcm_token,
                        'มีใบลาใหม่ (รอรับทราบ) 🔔',
                        "ใบลาของ {$requester->rank} {$requester->name} รอการรับทราบจากคุณ",
                        ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                    );
                }
            }

            return redirect()->back()->with('success', 'อนุญาตและลงลายมือชื่อเรียบร้อยแล้ว รอ รอง ผอ. รับทราบ');
        }

        // ========== STEP 2 (นักเรียน): ผู้บังคับบัญชาอนุมัติ (ต้องลงลายเซ็น) ==========
        if ($leaveRequest->status == 'pending_manager') {
            if ($requester->manager_id !== $user->id && !in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                abort(403, 'Not authorized for this approval stage.');
            }

            // Final Approval by Manager
            $leaveRequest->status = 'approved';
            $leaveRequest->save();

            // Log final approval for manager
            LeaveApproval::create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $user->id,
                'step' => 'manager',
                'action' => 'approved',
                'comment' => $request->input('comment'),
                'signature' => $signaturePath,
                'ip_address' => $request->ip()
            ]);

            // Deduct Balance
            $this->deductBalance($leaveRequest);

            // Notify User
            $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));

            // Push to Requester
            if ($requester->fcm_token) {
                (new FCMService())->sendNotification(
                    $requester->fcm_token,
                    'ใบลาของคุณได้รับการอนุมัติ 🎉',
                    "ใบลา{$leaveRequest->leaveType->name} ของคุณได้รับการอนุมัติเรียบร้อยแล้ว",
                    ['type' => 'leave_status', 'request_id' => $leaveRequest->id]
                );
            }

            return redirect()->back()->with('success', 'อนุมัติการลาขั้นสุดท้ายเรียบร้อยแล้ว');
        }

        // ========== STEP 2 (ข้าราชการ): รอง ผอ. รับทราบ (ไม่ต้องลงลายเซ็น) ==========
        if ($leaveRequest->status == 'pending_deputy_director') {
            if ($user->role !== 'deputy_director' && $user->role !== 'admin' && $user->role !== 'director') {
                abort(403, 'Not authorized for this approval stage.');
            }

            // Special Case: Director Approval at Deputy Stage -> Final Approval (Skip Deputy Step)
            if ($user->role === 'director') {
                $leaveRequest->status = 'approved';
                $leaveRequest->save();

                // Log final approval
                LeaveApproval::create([
                    'leave_request_id' => $leaveRequest->id,
                    'approver_id' => $user->id,
                    'step' => 'director',
                    'action' => 'approved',
                    'comment' => $request->input('comment'),
                    'signature' => $signaturePath,
                    'ip_address' => $request->ip()
                ]);

                // Deduct Balance
                $this->deductBalance($leaveRequest);

                // Notify User
                $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));

                // Push to Requester
                if ($requester->fcm_token) {
                    (new FCMService())->sendNotification(
                        $requester->fcm_token,
                        'ใบลาของคุณได้รับการอนุมัติ 🎉',
                        "ใบลา{$leaveRequest->leaveType->name} ของคุณได้รับการอนุมัติเรียบร้อยแล้ว",
                        ['type' => 'leave_status', 'request_id' => $leaveRequest->id]
                    );
                }

                return redirect()->back()->with('success', 'อนุมัติการลา (ขั้นตอนสุดท้าย) เรียบร้อยแล้ว');
            }

            // Move to Step 3: pending_director
            $leaveRequest->status = 'pending_director';
            $leaveRequest->save();

            // Log acknowledgment for step 2 (NO signature required)
            LeaveApproval::create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $user->id,
                'step' => 'deputy_director',
                'action' => 'acknowledged',
                'comment' => $request->input('comment'),
                'signature' => null, // No signature for deputy director
                'ip_address' => $request->ip()
            ]);

            // Notify Director about the new pending approval
            $directors = User::where('role', 'director')->get();
            foreach ($directors as $director) {
                $director->notify(new NewLeaveRequestNotification($leaveRequest, $requester));

                // Push to Director
                if ($director->fcm_token) {
                    (new FCMService())->sendNotification(
                        $director->fcm_token,
                        'มีใบลาใหม่ (รออนุมัติสุดท้าย) 🔔',
                        "ใบลาของ {$requester->rank} {$requester->name} รอการอนุมัติสุดท้ายจากคุณ",
                        ['type' => 'new_leave_request', 'request_id' => $leaveRequest->id]
                    );
                }
            }

            return redirect()->back()->with('success', 'รับทราบคำขอเรียบร้อยแล้ว รอ ผอ. ดำเนินการขั้นสุดท้าย');
        }

        // ========== STEP 3 (ข้าราชการ): ผอ. รับทราบ/อนุญาต (ต้องลงลายเซ็น) ==========
        if ($leaveRequest->status == 'pending_director') {
            if ($user->role !== 'director' && $user->role !== 'admin') {
                abort(403, 'Not authorized for this approval stage.');
            }

            // Final Approval by Director
            $leaveRequest->status = 'approved';
            $leaveRequest->save();

            // Determine action type based on leave type
            // ลาพักผ่อน = อนุญาต (approved), ลาป่วย/ลากิจ = รับทราบ (acknowledged)
            $actionType = $isVacation ? 'approved' : 'acknowledged';

            // Log final approval for step 3 (with signature)
            LeaveApproval::create([
                'leave_request_id' => $leaveRequest->id,
                'approver_id' => $user->id,
                'step' => 'director',
                'action' => $actionType,
                'comment' => $request->input('comment'),
                'signature' => $signaturePath,
                'ip_address' => $request->ip()
            ]);

            // Deduct Balance
            $this->deductBalance($leaveRequest);

            // Notify User
            $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));

            // Push to Requester
            if ($requester->fcm_token) {
                (new FCMService())->sendNotification(
                    $requester->fcm_token,
                    'ใบลาของคุณได้รับการอนุมัติ 🎉',
                    "ใบลา{$leaveRequest->leaveType->name} ของคุณได้รับการอนุมัติเรียบร้อยแล้ว",
                    ['type' => 'leave_status', 'request_id' => $leaveRequest->id]
                );
            }

            $actionLabel = $isVacation ? 'อนุญาต' : 'รับทราบ';
            return redirect()->back()->with('success', "{$actionLabel}การลาขั้นสุดท้ายเรียบร้อยแล้ว");
        }

        return redirect()->back()->with('error', 'สถานะใบลาไม่ถูกต้อง');
    }

    protected function deductBalance(LeaveRequest $leaveRequest)
    {
        $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year)
            ->first();

        // Auto create if missing? For now assume seeded or handled elsewhere.
        if ($balance) {
            $balance->used_days += $leaveRequest->total_days;
            $balance->remaining_days -= $leaveRequest->total_days;
            $balance->save();
        }
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Log Rejection
        LeaveApproval::create([
            'leave_request_id' => $leaveRequest->id,
            'approver_id' => $user->id,
            'step' => $leaveRequest->status, // Current status as step
            'action' => 'rejected',
            'comment' => $request->input('comment'),
            'ip_address' => $request->ip()
        ]);

        $leaveRequest->status = 'rejected';
        $leaveRequest->save();

        // Notify User
        $leaveRequest->user->notify(new LeaveStatusUpdated($leaveRequest, 'rejected', $user));

        // Push to Requester
        if ($leaveRequest->user->fcm_token) {
            (new FCMService())->sendNotification(
                $leaveRequest->user->fcm_token,
                'ใบลาของคุณถูกปฏิเสธ ❌',
                "ใบลา{$leaveRequest->leaveType->name} ของคุณถูกปฏิเสธ",
                ['type' => 'leave_status', 'request_id' => $leaveRequest->id]
            );
        }

        return redirect()->route('approvals.index')->with('success', 'ปฏิเสธคำขอเรียบร้อยแล้ว');
    }
}

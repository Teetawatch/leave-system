<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveRequestResource;
use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\User;
use App\Notifications\LeaveStatusUpdated;
use App\Notifications\NewLeaveRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Get pending approvals for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = LeaveRequest::with(['user', 'leaveType', 'approvals.approver']);

        // Explicit Role Checks for Query Building
        if ($user->role === 'admin') {
            // Admin sees ALL pending requests
            $query->whereIn('status', [
                'pending',
                'pending_supervisor',
                'pending_head',
                'pending_manager',
                'pending_deputy_director',
                'pending_director'
            ]);
        } elseif ($user->role === 'director') {
            // Director sees pending_director (for approval) and pending_deputy_director (for monitoring/override)
            $query->whereIn('status', ['pending_director', 'pending_deputy_director']);
        } elseif ($user->role === 'deputy_director') {
            // Deputy Director see pending_deputy_director
            $query->where('status', 'pending_deputy_director');
        } elseif ($user->role === 'department_head') {
            // Department Head sees all in their department that are at early stages
            $query->whereIn('status', ['pending_supervisor', 'pending_head', 'pending_manager'])
                ->whereHas('user', function ($q) use ($user) {
                    $q->where('department', $user->department);
                });
        } else {
            // Normal Approvers (Supervisors/Managers)
            $query->where(function ($q) use ($user) {
                // Case 1: Pending Supervisor (Step 1)
                $q->where(function ($subQ) use ($user) {
                    $subQ->whereIn('status', ['pending_supervisor', 'pending_head'])
                        ->whereHas('user', function ($userQ) use ($user) {
                            $userQ->where('supervisor_id', $user->id);
                        });
                });

                // Case 2: Pending Manager (Step 2 for students)
                $q->orWhere(function ($subQ) use ($user) {
                    $subQ->where('status', 'pending_manager')
                        ->whereHas('user', function ($userQ) use ($user) {
                            $userQ->where('manager_id', $user->id);
                        });
                });
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => LeaveRequestResource::collection($requests->items()),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    /**
     * Approve a leave request
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
            'signature' => 'nullable|string', // Base64 signature
            'use_saved_signature' => 'nullable|boolean',
        ]);

        $user = $request->user();
        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);
        $requester = $leaveRequest->user;

        $leaveSlug = strtolower($leaveRequest->leaveType->slug ?? '');
        $isVacation = $leaveSlug === 'vacation' || str_contains($leaveRequest->leaveType->name ?? '', 'พักผ่อน');
        $isSickOrPersonal = in_array($leaveSlug, ['sick', 'personal']);

        // Handle Signature
        $signaturePath = $this->handleSignature($request, $leaveRequest, $user);

        // Process based on current status (Matching web logic)

        // --- STEP 1: Supervisor ---
        if ($leaveRequest->status === 'pending_supervisor') {
            if ($requester->supervisor_id !== $user->id && !in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($leaveSlug === 'temporary') {
                $leaveRequest->status = 'approved';
                $leaveRequest->save();
                $this->logApproval($leaveRequest, $user, 'supervisor', 'approved', $request->comment, $signaturePath, $request->ip());
                $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));
                return $this->successResponse('อนุมัติลาชั่วกาลเรียบร้อยแล้ว', $leaveRequest);
            }

            $isStudentCourse = $this->isStudentCourse($requester);
            $hasManager = !empty($requester->manager_id);

            if ($isStudentCourse && $isSickOrPersonal && $hasManager) {
                $leaveRequest->status = 'pending_manager';
                $leaveRequest->save();
                $this->logApproval($leaveRequest, $user, 'supervisor', 'approved', $request->comment, $signaturePath, $request->ip());
                $manager = User::find($requester->manager_id);
                if ($manager)
                    $manager->notify(new NewLeaveRequestNotification($leaveRequest, $requester));
                return $this->successResponse('อนุญาตขั้นที่ 1 เรียบร้อยแล้ว รอผู้บังคับบัญชาอนุมัติขั้นสุดท้าย', $leaveRequest);
            }

            $leaveRequest->status = 'pending_deputy_director';
            $leaveRequest->save();
            $this->logApproval($leaveRequest, $user, 'supervisor', 'approved', $request->comment, $signaturePath, $request->ip());
            $this->notifyRole('deputy_director', $leaveRequest, $requester);
            return $this->successResponse('อนุญาตและลงลายมือชื่อเรียบร้อยแล้ว รอ รอง ผอ. รับทราบ', $leaveRequest);
        }

        // --- STEP 2 (Student): Manager ---
        if ($leaveRequest->status === 'pending_manager') {
            if ($requester->manager_id !== $user->id && !in_array($user->role, ['admin', 'director', 'deputy_director'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $leaveRequest->status = 'approved';
            $leaveRequest->save();
            $this->logApproval($leaveRequest, $user, 'manager', 'approved', $request->comment, $signaturePath, $request->ip());
            $this->deductBalance($leaveRequest);
            $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));
            return $this->successResponse('อนุมัติการลาขั้นสุดท้ายเรียบร้อยแล้ว', $leaveRequest);
        }

        // --- STEP 2 (Regular): Deputy Director ---
        if ($leaveRequest->status === 'pending_deputy_director') {
            if (!in_array($user->role, ['deputy_director', 'admin', 'director'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            if ($user->role === 'director') {
                $leaveRequest->status = 'approved';
                $leaveRequest->save();
                $this->logApproval($leaveRequest, $user, 'director', 'approved', $request->comment, $signaturePath, $request->ip());
                $this->deductBalance($leaveRequest);
                $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));
                return $this->successResponse('อนุมัติการลา (ขั้นตอนสุดท้าย) เรียบร้อยแล้ว', $leaveRequest);
            }

            $leaveRequest->status = 'pending_director';
            $leaveRequest->save();
            $this->logApproval($leaveRequest, $user, 'deputy_director', 'acknowledged', $request->comment, null, $request->ip());
            $this->notifyRole('director', $leaveRequest, $requester);
            return $this->successResponse('รับทราบคำขอเรียบร้อยแล้ว รอ ผอ. ดำเนินการขั้นสุดท้าย', $leaveRequest);
        }

        // --- STEP 3 (Regular): Director ---
        if ($leaveRequest->status === 'pending_director') {
            if (!in_array($user->role, ['director', 'admin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $leaveRequest->status = 'approved';
            $leaveRequest->save();
            $actionType = $isVacation ? 'approved' : 'acknowledged';
            $this->logApproval($leaveRequest, $user, 'director', $actionType, $request->comment, $signaturePath, $request->ip());
            $this->deductBalance($leaveRequest);
            $requester->notify(new LeaveStatusUpdated($leaveRequest, 'approved', $user));
            return $this->successResponse('ดำเนินการขั้นสุดท้ายเรียบร้อยแล้ว', $leaveRequest);
        }

        return response()->json([
            'success' => false,
            'message' => 'สถานะใบลาไม่ถูกต้องสำหรับการอนุมัติ',
        ], 422);
    }

    /**
     * Reject a leave request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])->findOrFail($id);

        // Log Rejection
        $leaveRequest->approvals()->create([
            'approver_id' => $user->id,
            'step' => $leaveRequest->status,
            'action' => 'rejected',
            'comment' => $request->comment,
            'ip_address' => $request->ip(),
        ]);

        $leaveRequest->status = 'rejected';
        $leaveRequest->save();

        // Notify User
        $leaveRequest->user->notify(new LeaveStatusUpdated($leaveRequest, 'rejected', $user));

        return response()->json([
            'success' => true,
            'message' => 'ปฏิเสธคำขอเรียบร้อยแล้ว',
            'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
        ]);
    }

    private function handleSignature(Request $request, LeaveRequest $leaveRequest, $user)
    {
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);

            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.png';
            Storage::disk('public')->put($fileName, $imageData);
            return $fileName;
        }

        if ($request->input('use_saved_signature') && $user->signature) {
            $extension = pathinfo($user->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.' . $extension;

            if (Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->copy($user->signature, $fileName);
                return $fileName;
            }
        }

        return null;
    }

    private function logApproval($leaveRequest, $user, $step, $action, $comment, $signature, $ip)
    {
        return $leaveRequest->approvals()->create([
            'approver_id' => $user->id,
            'step' => $step,
            'action' => $action,
            'comment' => $comment,
            'signature' => $signature,
            'ip_address' => $ip,
        ]);
    }

    private function notifyRole($role, $leaveRequest, $requester)
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $u) {
            $u->notify(new NewLeaveRequestNotification($leaveRequest, $requester));
        }
    }

    private function successResponse($message, $leaveRequest)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => new LeaveRequestResource($leaveRequest->load(['leaveType', 'approvals.approver'])),
        ]);
    }

    private function deductBalance(LeaveRequest $leaveRequest)
    {
        $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', now()->year)
            ->first();

        if ($balance) {
            $balance->used_days += $leaveRequest->total_days;
            $balance->remaining_days -= $leaveRequest->total_days;
            $balance->save();
        }
    }
}

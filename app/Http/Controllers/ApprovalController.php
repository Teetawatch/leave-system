<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LeaveApprovalService;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(LeaveApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
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
            ->with(['user.supervisor', 'user.manager', 'user.deputy', 'leaveType', 'approvals.approver'])
            ->paginate(10);

        return Inertia::render('Approvals/Index', [
            'requests' => $requests->through(fn ($req) => [
                'id' => $req->id,
                'user' => $req->user ? [
                    'id' => $req->user->id,
                    'name' => $req->user->name,
                    'rank' => $req->user->rank,
                    'department' => $req->user->department,
                    'avatar' => $req->user->avatar,
                    'supervisor' => $req->user->supervisor ? ['name' => $req->user->supervisor->name, 'rank' => $req->user->supervisor->rank, 'avatar' => $req->user->supervisor->avatar] : null,
                    'manager' => $req->user->manager ? ['name' => $req->user->manager->name, 'rank' => $req->user->manager->rank, 'avatar' => $req->user->manager->avatar] : null,
                ] : null,
                'leave_type' => $req->leaveType ? ['name' => $req->leaveType->name, 'slug' => $req->leaveType->slug] : null,
                'start_date' => $req->start_date->format('Y-m-d'),
                'end_date' => $req->end_date->format('Y-m-d'),
                'start_date_thai' => $req->start_date->locale('th')->isoFormat('D MMMM YYYY'),
                'end_date_thai' => $req->end_date->locale('th')->isoFormat('D MMMM YYYY'),
                'total_days' => $req->total_days + 0,
                'reason' => $req->reason,
                'contact_address' => $req->contact_address,
                'status' => $req->status,
                'attachment_path' => $req->attachment_path,
                'created_at_human' => $req->created_at->diffForHumans(),
                'approvals' => $req->approvals->map(fn ($a) => [
                    'step' => $a->step,
                    'status' => $a->action,
                    'comment' => $a->comment,
                    'approved_at' => $a->created_at,
                    'approver' => $a->approver ? ['name' => $a->approver->name, 'rank' => $a->approver->rank, 'avatar' => $a->approver->avatar] : null,
                ]),
            ]),
        ]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();

        // Handle Signature Upload (if provided)
        $signaturePath = null;
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);
            $fileName = 'signatures/sig_' . time() . '_' . $leaveRequest->id . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $signaturePath = $fileName;
        }

        try {
            $this->approvalService->approve($leaveRequest, $user, $request->input('comment'), $signaturePath);
            return redirect()->back()->with('success', 'ดำเนินการอนุมัติเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $user = auth()->user();
        try {
            $this->approvalService->reject($leaveRequest, $user, $request->input('comment'));
            return redirect()->route('approvals.index')->with('success', 'ปฏิเสธคำขอเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

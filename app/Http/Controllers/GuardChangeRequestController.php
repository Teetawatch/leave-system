<?php

namespace App\Http\Controllers;

use App\Models\GuardChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class GuardChangeRequestController extends Controller
{
    /**
     * Display a listing of guard change requests.
     */
    public function index()
    {
        $requests = GuardChangeRequest::where('user_id', Auth::id())
            ->with(['replacementUser'])
            ->latest()
            ->paginate(10);

        return view('guard_change.index', compact('requests'));
    }

    /**
     * Show the form for creating a new guard change request.
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())
            ->whereNotNull('registration_status')
            ->where('registration_status', 'approved')
            ->orderBy('name')
            ->get();

        $dutyPositions = [
            'senior_duty_officer' => 'นายทหารเวรอาวุโส',
            'duty_officer' => 'นายทหารเวร',
            'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
        ];

        return view('guard_change.create', compact('users', 'dutyPositions'));
    }

    /**
     * Store a newly created guard change request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'replacement_user_id' => 'required|exists:users,id',
            'duty_position' => 'required|in:senior_duty_officer,duty_officer,assistant_duty_officer',
            'duty_date' => 'required|date|after_or_equal:today',
            'remarks' => 'nullable|string|max:1000',
        ], [
            'replacement_user_id.required' => 'กรุณาเลือกผู้ที่จะมาเปลี่ยนแทน',
            'duty_position.required' => 'กรุณาเลือกตำแหน่งเวรยาม',
            'duty_date.required' => 'กรุณาระบุวันที่เข้าเวร',
            'duty_date.after_or_equal' => 'วันที่เข้าเวรต้องไม่เป็นวันที่ผ่านมาแล้ว',
        ]);

        $guardChangeRequest = GuardChangeRequest::create([
            'user_id' => Auth::id(),
            'replacement_user_id' => $validated['replacement_user_id'],
            'duty_position' => $validated['duty_position'],
            'duty_date' => $validated['duty_date'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('guard-change.show', $guardChangeRequest)
            ->with('status', 'ส่งคำขอเปลี่ยนยามเรียบร้อยแล้ว');
    }

    /**
     * Display the specified guard change request.
     */
    public function show(GuardChangeRequest $guardChange)
    {
        $guardChange->load(['user', 'replacementUser']);
        
        return view('guard_change.show', compact('guardChange'));
    }

    /**
     * Export guard change request to PDF.
     */
    public function exportPdf(GuardChangeRequest $guardChange)
    {
        $guardChange->load(['user', 'replacementUser']);

        $dutyPositions = [
            'senior_duty_officer' => 'นายทหารเวรอาวุโส',
            'duty_officer' => 'นายทหารเวร',
            'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
        ];

        // Get deputy director for approval section
        $deputyDirector = \App\Models\User::where('role', 'deputy_director')->first();
        
        // Get director for final approval section
        $director = \App\Models\User::where('role', 'director')->first();

        $pdf = Pdf::loadView('guard_change.pdf', compact('guardChange', 'dutyPositions', 'deputyDirector', 'director'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('guard-change-' . $guardChange->id . '.pdf');
    }

    /**
     * Cancel a guard change request.
     */
    public function cancel(GuardChangeRequest $guardChange)
    {
        // Authorize - only owner can cancel
        if ($guardChange->user_id !== Auth::id()) {
            abort(403);
        }

        // Can only cancel if still pending
        if ($guardChange->status !== 'pending') {
            return back()->withErrors(['msg' => 'ไม่สามารถยกเลิกคำขอที่ดำเนินการเสร็จสิ้นแล้วได้']);
        }

        $guardChange->status = 'cancelled';
        $guardChange->save();

        return redirect()->route('guard-change.index')->with('status', 'ยกเลิกคำขอเปลี่ยนยามเรียบร้อยแล้ว');
    }

    /**
     * Display guard change requests pending approval.
     * Shows only requests where current user is the replacement user (ผู้ถูกขอให้มาเปลี่ยนแทน)
     */
    public function approvalIndex()
    {
        $requests = GuardChangeRequest::where('status', 'pending')
            ->where('replacement_user_id', Auth::id())
            ->with(['user', 'replacementUser'])
            ->latest()
            ->get();

        return view('guard_change.approvals', compact('requests'));
    }

    /**
     * Approve a guard change request with signature.
     */
    public function approve(Request $request, GuardChangeRequest $guardChange)
    {
        $user = Auth::user();

        // Only the replacement user (ผู้ถูกขอให้มาเปลี่ยน) can approve
        if ($guardChange->replacement_user_id !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
        }

        // Handle Signature Upload
        $signaturePath = null;
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);
            
            $fileName = 'signatures/guard_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $signaturePath = $fileName;
        } elseif ($request->input('use_saved_signature') == '1' && $user->signature) {
            $extension = pathinfo($user->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/guard_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.' . $extension;
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->copy($user->signature, $fileName);
                $signaturePath = $fileName;
            }
        }

        $guardChange->update([
            'status' => 'approved',
            'approver_id' => $user->id,
            'approval_signature' => $signaturePath,
            'approval_comment' => $request->input('comment'),
            'approved_at' => now(),
        ]);

        return redirect()->route('guard-change.approvals')
            ->with('success', 'อนุมัติคำขอเปลี่ยนยามเรียบร้อยแล้ว');
    }

    /**
     * Reject a guard change request.
     */
    public function reject(Request $request, GuardChangeRequest $guardChange)
    {
        $user = Auth::user();

        // Only the replacement user (ผู้ถูกขอให้มาเปลี่ยน) can reject
        if ($guardChange->replacement_user_id !== $user->id) {
            abort(403, 'คุณไม่มีสิทธิ์ปฏิเสธคำขอนี้');
        }

        $request->validate([
            'comment' => 'required|string|max:500',
        ], [
            'comment.required' => 'กรุณาระบุเหตุผลในการปฏิเสธ',
        ]);

        $guardChange->update([
            'status' => 'rejected',
            'approver_id' => $user->id,
            'approval_comment' => $request->input('comment'),
            'approved_at' => now(),
        ]);

        return redirect()->route('guard-change.approvals')
            ->with('success', 'ปฏิเสธคำขอเปลี่ยนยามเรียบร้อยแล้ว');
    }

    /**
     * Display guard change requests pending director approval.
     * For deputy_director role only - shows requests already approved by replacement user
     */
    public function directorApprovalIndex()
    {
        $requests = GuardChangeRequest::where('status', 'approved')
            ->whereNull('director_approved_at')
            ->with(['user', 'replacementUser'])
            ->latest()
            ->get();

        return view('guard_change.director_approvals', compact('requests'));
    }

    /**
     * Deputy Director approve a guard change request with signature.
     * Changes status to 'director_approved' (intermediate)
     */
    public function directorApprove(Request $request, GuardChangeRequest $guardChange)
    {
        $user = Auth::user();

        // Only deputy_director can approve at this level
        if (!in_array($user->role, ['deputy_director', 'admin'])) {
            abort(403, 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
        }

        // Handle Signature Upload
        $signaturePath = null;
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);
            
            $fileName = 'signatures/guard_director_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $signaturePath = $fileName;
        } elseif ($request->input('use_saved_signature') == '1' && $user->signature) {
            $extension = pathinfo($user->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/guard_director_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.' . $extension;
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->copy($user->signature, $fileName);
                $signaturePath = $fileName;
            }
        }

        $guardChange->update([
            'status' => 'director_approved',
            'director_approver_id' => $user->id,
            'director_signature' => $signaturePath,
            'director_comment' => $request->input('comment'),
            'director_approved_at' => now(),
        ]);

        return redirect()->route('guard-change.director-approvals')
            ->with('success', 'อนุมัติคำขอเปลี่ยนยามเรียบร้อยแล้ว (รอ ผอ. อนุมัติ)');
    }

    /**
     * Display guard change requests pending final approval by Director (ผอ.)
     */
    public function finalApprovalIndex()
    {
        $requests = GuardChangeRequest::where('status', 'director_approved')
            ->whereNull('final_approved_at')
            ->with(['user', 'replacementUser', 'directorApprover'])
            ->latest()
            ->get();

        return view('guard_change.final_approvals', compact('requests'));
    }

    /**
     * Director (ผอ.) final approve a guard change request with signature.
     */
    public function finalApprove(Request $request, GuardChangeRequest $guardChange)
    {
        $user = Auth::user();

        // Only director can do final approval
        if (!in_array($user->role, ['director', 'admin'])) {
            abort(403, 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
        }

        // Handle Signature Upload
        $signaturePath = null;
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);
            
            $fileName = 'signatures/guard_final_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.png';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $signaturePath = $fileName;
        } elseif ($request->input('use_saved_signature') == '1' && $user->signature) {
            $extension = pathinfo($user->signature, PATHINFO_EXTENSION);
            $fileName = 'signatures/guard_final_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.' . $extension;
            
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->copy($user->signature, $fileName);
                $signaturePath = $fileName;
            }
        }

        $guardChange->update([
            'status' => 'fully_approved',
            'final_approver_id' => $user->id,
            'final_signature' => $signaturePath,
            'final_comment' => $request->input('comment'),
            'final_approved_at' => now(),
        ]);

        return redirect()->route('guard-change.final-approvals')
            ->with('success', 'อนุมัติคำขอเปลี่ยนยามเรียบร้อยแล้ว (เสร็จสมบูรณ์)');
    }
}

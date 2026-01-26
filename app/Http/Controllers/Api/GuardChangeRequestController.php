<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GuardChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GuardChangeRequestController extends Controller
{
    /**
     * Display a listing of guard change requests for the authenticated user.
     */
    public function index()
    {
        $requests = GuardChangeRequest::where('user_id', Auth::id())
            ->with(['replacementUser', 'approver', 'directorApprover', 'finalApprover'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
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
        ]);

        $guardChangeRequest = GuardChangeRequest::create([
            'user_id' => Auth::id(),
            'replacement_user_id' => $validated['replacement_user_id'],
            'duty_position' => $validated['duty_position'],
            'duty_date' => $validated['duty_date'],
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ส่งคำขอเปลี่ยนยามเรียบร้อยแล้ว',
            'data' => $guardChangeRequest->load('replacementUser')
        ], 201);
    }

    /**
     * Display the specified guard change request.
     */
    public function show($id)
    {
        $guardChange = GuardChangeRequest::with(['user', 'replacementUser', 'approver', 'directorApprover', 'finalApprover'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $guardChange
        ]);
    }

    /**
     * Export guard change request to PDF.
     */
    public function exportPdf($id)
    {
        $guardChange = GuardChangeRequest::with(['user', 'replacementUser'])->findOrFail($id);

        $dutyPositions = [
            'senior_duty_officer' => 'นายทหารเวรอาวุโส',
            'duty_officer' => 'นายทหารเวร',
            'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
        ];

        $deputyDirector = User::where('role', 'deputy_director')->first();
        $director = User::where('role', 'director')->first();

        $pdf = Pdf::loadView('guard_change.pdf', compact('guardChange', 'dutyPositions', 'deputyDirector', 'director'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('guard-change-' . $guardChange->id . '.pdf');
    }

    /**
     * List requests for approval (where user is replacement user).
     */
    public function approvalIndex()
    {
        $requests = GuardChangeRequest::where('replacement_user_id', Auth::id())
            ->whereIn('status', ['pending']) // Only pending for the replacement user
            ->with(['user', 'replacementUser'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Approve a guard change request.
     */
    public function approve(Request $request, $id)
    {
        $guardChange = GuardChangeRequest::findOrFail($id);
        $user = Auth::user();

        if ($guardChange->replacement_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้'], 403);
        }

        // Handle Signature if provided as base64
        $signaturePath = null;
        if ($request->filled('signature')) {
            $imageData = $request->input('signature');
            $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
            $imageData = base64_decode($imageData);

            $fileName = 'signatures/guard_sig_' . time() . '_' . $guardChange->id . '_' . $user->id . '.png';
            Storage::disk('public')->put($fileName, $imageData);
            $signaturePath = $fileName;
        } elseif ($request->input('use_saved_signature') == '1' && $user->signature) {
            // Logic to use saved signature
            $signaturePath = $user->signature;
        }

        $guardChange->update([
            'status' => 'approved',
            'approver_id' => $user->id,
            'approval_signature' => $signaturePath,
            'approval_comment' => $request->input('comment'),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'อนุมัติคำขอเปลี่ยนยามเรียบร้อยแล้ว'
        ]);
    }

    /**
     * Reject a guard change request.
     */
    public function reject(Request $request, $id)
    {
        $guardChange = GuardChangeRequest::findOrFail($id);
        $user = Auth::user();

        if ($guardChange->replacement_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ปฏิเสธคำขอนี้'], 403);
        }

        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $guardChange->update([
            'status' => 'rejected',
            'approver_id' => $user->id,
            'approval_comment' => $request->input('comment'),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ปฏิเสธคำขอเปลี่ยนยามเรียบร้อยแล้ว'
        ]);
    }

    /**
     * Get list of users for selection (replacement).
     */
    public function getUsers()
    {
        $users = User::where('id', '!=', Auth::id())
            ->where('registration_status', 'approved')
            ->select('id', 'name', 'rank', 'position')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}

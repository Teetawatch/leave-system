<?php

namespace App\Services;

use App\Models\GuardChangeRequest;
use App\Models\User;
use App\Notifications\GuardChangeStatusUpdated;
use App\Notifications\NewGuardChangeNotification;
use App\Services\FCMService;
use App\Services\LineService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class GuardChangeApprovalService
{
    protected $lineService;

    public function __construct(LineService $lineService)
    {
        $this->lineService = $lineService;
    }

    /**
     * ตำแหน่งเวรยาม mapping
     */
    protected const DUTY_POSITIONS = [
        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
        'duty_officer' => 'นายทหารเวร',
        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
    ];

    /**
     * Status labels mapping
     */
    protected const STATUS_LABELS = [
        'pending' => 'รอผู้เปลี่ยนแทนตอบรับ',
        'approved' => 'รอ รอง ผอ. อนุมัติ',
        'director_approved' => 'รอ ผอ. อนุมัติ',
        'fully_approved' => 'อนุมัติเสร็จสมบูรณ์',
        'rejected' => 'ถูกปฏิเสธ',
        'cancelled' => 'ยกเลิกแล้ว',
    ];

    /**
     * Get status label in Thai
     */
    public function getStatusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
    }

    /**
     * Check if actor can approve/reject at the current step
     */
    public function canActorApprove(GuardChangeRequest $guardChange, User $actor): bool
    {
        if ($actor->role === 'admin') return true;

        $status = $guardChange->status;

        switch ($status) {
            case 'pending':
                // Only the replacement user can approve at step 1
                return $guardChange->replacement_user_id === $actor->id;

            case 'approved':
                // Deputy Director or Director can approve at step 2
                return in_array($actor->role, ['deputy_director', 'director']);

            case 'director_approved':
                // Only Director can do final approval
                return $actor->role === 'director';

            default:
                return false;
        }
    }

    /**
     * Step 1: Replacement user approves
     */
    public function approveByReplacement(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if (!$this->canActorApprove($guardChange, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        if ($guardChange->status !== 'pending') {
            throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติขั้นตอนนี้");
        }

        // Handle auto-signature
        $signaturePath = $this->handleAutoSignature($guardChange, $actor, 'guard_sig');

        $guardChange->update([
            'status' => 'approved',
            'approver_id' => $actor->id,
            'approval_signature' => $signaturePath,
            'approval_comment' => $comment ?: ($actor->line_user_id ? 'อนุมัติผ่าน LINE' : null),
            'approved_at' => now(),
        ]);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'approved', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรได้รับการตอบรับ ✅',
                "{$actor->rank} {$actor->name} ตอบรับคำขอเปลี่ยนเวรของคุณแล้ว",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        // Send LINE notification to requester
        $this->sendStatusUpdateToLine($guardChange, $requester, 'approved', $actor);

        // Notify Deputy Directors (next step) via LINE
        $this->notifyNextApproverViaLine($guardChange, 'approved');

        // Also notify via FCM/DB
        $deputyDirectors = User::where('role', 'deputy_director')->get();
        foreach ($deputyDirectors as $deputy) {
            $deputy->notify(new NewGuardChangeNotification($guardChange, $requester));
            if ($deputy->fcm_token) {
                (new FCMService())->sendNotification(
                    $deputy->fcm_token,
                    'มีคำขอเปลี่ยนเวรใหม่ (รออนุมัติ) 🔔',
                    "มีคำขอเปลี่ยนเวรของ {$requester->rank} {$requester->name} รอการอนุมัติจากคุณ",
                    ['type' => 'new_guard_change_approval', 'request_id' => $guardChange->id]
                );
            }
        }

        return true;
    }

    /**
     * Step 2: Deputy Director approves
     */
    public function approveByDeputyDirector(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if (!$this->canActorApprove($guardChange, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        if ($guardChange->status !== 'approved') {
            throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติขั้นตอนนี้");
        }

        // If Director approves directly at deputy stage, skip to fully_approved
        if ($actor->role === 'director') {
            return $this->approveByDirector($guardChange, $actor, $comment);
        }

        $signaturePath = $this->handleAutoSignature($guardChange, $actor, 'guard_director_sig');

        $guardChange->update([
            'status' => 'director_approved',
            'director_approver_id' => $actor->id,
            'director_signature' => $signaturePath,
            'director_comment' => $comment ?: ($actor->line_user_id ? 'อนุมัติผ่าน LINE' : null),
            'director_approved_at' => now(),
        ]);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'director_approved', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรผ่านการอนุมัติเบื้องต้น ℹ️',
                "รอง ผอ. {$actor->name} ได้อนุมัติคำขอเปลี่ยนเวรของคุณแล้ว (รอ ผอ. อนุมัติ)",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        // Send LINE status update to requester
        $this->sendStatusUpdateToLine($guardChange, $requester, 'director_approved', $actor);

        // Notify Directors (final step) via LINE
        $this->notifyNextApproverViaLine($guardChange, 'director_approved');

        // Also notify via FCM/DB
        $directors = User::where('role', 'director')->get();
        foreach ($directors as $director) {
            $director->notify(new NewGuardChangeNotification($guardChange, $requester));
            if ($director->fcm_token) {
                (new FCMService())->sendNotification(
                    $director->fcm_token,
                    'มีคำขอเปลี่ยนเวรใหม่ (รออนุมัติสุดท้าย) 🔔',
                    "มีคำขอเปลี่ยนเวรของ {$requester->rank} {$requester->name} รอการอนุมัติจากคุณ",
                    ['type' => 'new_guard_change_approval', 'request_id' => $guardChange->id]
                );
            }
        }

        return true;
    }

    /**
     * Step 3: Director final approval
     */
    public function approveByDirector(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if ($actor->role !== 'director' && $actor->role !== 'admin') {
            throw new Exception("คุณไม่มีสิทธิ์อนุมัติคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        if (!in_array($guardChange->status, ['director_approved', 'approved'])) {
            throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติขั้นตอนนี้");
        }

        $signaturePath = $this->handleAutoSignature($guardChange, $actor, 'guard_final_sig');

        $guardChange->update([
            'status' => 'fully_approved',
            'final_approver_id' => $actor->id,
            'final_signature' => $signaturePath,
            'final_comment' => $comment ?: ($actor->line_user_id ? 'อนุมัติผ่าน LINE' : null),
            'final_approved_at' => now(),
        ]);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'fully_approved', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรอนุมัติเสร็จสมบูรณ์ 🎉',
                "ผอ. {$actor->name} ได้อนุมัติคำขอเปลี่ยนเวรของคุณเรียบร้อยแล้ว",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        // Send LINE status update to requester
        $this->sendStatusUpdateToLine($guardChange, $requester, 'fully_approved', $actor);

        return true;
    }

    /**
     * Unified approve method - determines which step based on current status
     */
    public function approve(GuardChangeRequest $guardChange, User $actor, $comment = null, $isFromLine = false)
    {
        // Check if actor has a saved signature when approving via LINE
        if ($isFromLine && !$actor->signature) {
            $profileUrl = url('/profile');
            throw new Exception("⚠️ คุณยังไม่มีลายเซ็นในระบบ\n\nกรุณาอัปโหลดรูปภาพลายเซ็นที่หน้าโปรไฟล์ก่อนอนุมัติผ่าน LINE\n\n👉 {$profileUrl}");
        }

        $status = $guardChange->status;

        switch ($status) {
            case 'pending':
                return $this->approveByReplacement($guardChange, $actor, $comment);
            case 'approved':
                return $this->approveByDeputyDirector($guardChange, $actor, $comment);
            case 'director_approved':
                return $this->approveByDirector($guardChange, $actor, $comment);
            default:
                throw new Exception("สถานะคำขอไม่ถูกต้องสำหรับการอนุมัติ (สถานะปัจจุบัน: {$this->getStatusLabel($status)})");
        }
    }

    /**
     * Unified reject method
     */
    public function reject(GuardChangeRequest $guardChange, User $actor, $comment = null)
    {
        if (!$this->canActorApprove($guardChange, $actor)) {
            throw new Exception("คุณไม่มีสิทธิ์ปฏิเสธคำขอเปลี่ยนเวรนี้ในขั้นตอนนี้");
        }

        $previousStatus = $guardChange->status;
        $rejectComment = $comment ?: ($actor->line_user_id ? 'ปฏิเสธผ่าน LINE' : 'ไม่ได้ระบุเหตุผล');

        // Determine which field to update based on step
        $updateData = [
            'status' => 'rejected',
        ];

        if ($previousStatus === 'pending') {
            $updateData['approver_id'] = $actor->id;
            $updateData['approval_comment'] = $rejectComment;
            $updateData['approved_at'] = now();
        } elseif ($previousStatus === 'approved') {
            $updateData['director_approver_id'] = $actor->id;
            $updateData['director_comment'] = $rejectComment;
            $updateData['director_approved_at'] = now();
        } elseif ($previousStatus === 'director_approved') {
            $updateData['final_approver_id'] = $actor->id;
            $updateData['final_comment'] = $rejectComment;
            $updateData['final_approved_at'] = now();
        }

        $guardChange->update($updateData);

        // Notify requester
        $requester = $guardChange->user;
        $requester->notify(new GuardChangeStatusUpdated($guardChange, 'rejected', $actor));

        if ($requester->fcm_token) {
            (new FCMService())->sendNotification(
                $requester->fcm_token,
                'การขอเปลี่ยนเวรถูกปฏิเสธ ❌',
                "{$actor->rank} {$actor->name} ปฏิเสธคำขอเปลี่ยนเวรของคุณ",
                ['type' => 'guard_change_status', 'request_id' => $guardChange->id]
            );
        }

        // Send LINE status update to requester
        $this->sendStatusUpdateToLine($guardChange, $requester, 'rejected', $actor);

        return true;
    }

    /**
     * Handle auto-signature for LINE approvals
     */
    protected function handleAutoSignature(GuardChangeRequest $guardChange, User $actor, $prefix = 'guard_sig')
    {
        if ($actor->signature && Storage::disk('public')->exists($actor->signature)) {
            $extension = pathinfo($actor->signature, PATHINFO_EXTENSION);
            $fileName = "signatures/{$prefix}_" . time() . '_' . $guardChange->id . '_' . $actor->id . '.' . $extension;
            Storage::disk('public')->copy($actor->signature, $fileName);
            return $fileName;
        }

        return null;
    }

    /**
     * Send LINE Flex Message to notify the next approver with approve/reject buttons
     */
    public function notifyNextApproverViaLine(GuardChangeRequest $guardChange, string $currentStatus)
    {
        $guardChange->load(['user', 'replacementUser']);
        $requester = $guardChange->user;
        $replacement = $guardChange->replacementUser;
        $dutyPosition = self::DUTY_POSITIONS[$guardChange->duty_position] ?? $guardChange->duty_position;

        $recipients = [];

        if ($currentStatus === 'pending') {
            // Notify replacement user
            if ($replacement && $replacement->line_user_id) {
                $recipients[] = $replacement;
            }
        } elseif ($currentStatus === 'approved') {
            // Notify deputy directors
            $recipients = User::where('role', 'deputy_director')
                ->whereNotNull('line_user_id')
                ->get()
                ->all();
        } elseif ($currentStatus === 'director_approved') {
            // Notify directors
            $recipients = User::where('role', 'director')
                ->whereNotNull('line_user_id')
                ->get()
                ->all();
        }

        foreach ($recipients as $recipient) {
            $this->sendGuardChangeApprovalFlex($recipient, $guardChange, $requester, $replacement, $dutyPosition, $currentStatus);
        }
    }

    /**
     * Send a Flex Message for guard change approval request
     */
    protected function sendGuardChangeApprovalFlex($recipient, $guardChange, $requester, $replacement, $dutyPosition, $currentStatus)
    {
        $stepLabel = '';
        if ($currentStatus === 'pending') {
            $stepLabel = 'ขั้นตอนที่ 1: ผู้เปลี่ยนแทนตอบรับ';
        } elseif ($currentStatus === 'approved') {
            $stepLabel = 'ขั้นตอนที่ 2: รอง ผอ. อนุมัติ';
        } elseif ($currentStatus === 'director_approved') {
            $stepLabel = 'ขั้นตอนที่ 3: ผอ. อนุมัติ (สุดท้าย)';
        }

        $detailsContents = [
            [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'ผู้ขอ:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => trim("{$requester->rank} {$requester->name}"), 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ],
            [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'ผู้แทน:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => trim("{$replacement->rank} {$replacement->name}"), 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ],
            [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'ตำแหน่ง:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => $dutyPosition, 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ],
            [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'วันที่เวร:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => Carbon::parse($guardChange->duty_date)->format('d/m/Y'), 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ],
        ];

        if ($guardChange->remarks) {
            $detailsContents[] = [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'หมายเหตุ:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => $guardChange->remarks, 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ];
        }

        // Add step indicator
        $detailsContents[] = [
            'type' => 'box',
            'layout' => 'horizontal',
            'margin' => 'md',
            'contents' => [
                ['type' => 'text', 'text' => '📍 ' . $stepLabel, 'size' => 'xs', 'color' => '#4f46e5', 'flex' => 0, 'wrap' => true, 'weight' => 'bold'],
            ]
        ];

        $flexContents = [
            'type' => 'bubble',
            'size' => 'kilo',
            'header' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '🔄 คำขอเปลี่ยนเวรยาม',
                        'weight' => 'bold',
                        'color' => '#ffffff',
                        'size' => 'md',
                    ]
                ],
                'backgroundColor' => '#f59e0b',
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => 'คำขอเปลี่ยนเวรยาม',
                        'weight' => 'bold',
                        'size' => 'lg',
                        'wrap' => true,
                    ],
                    [
                        'type' => 'text',
                        'text' => "สังกัด: " . ($requester->department ?: '-'),
                        'size' => 'sm',
                        'color' => '#666666',
                        'wrap' => true,
                        'margin' => 'sm',
                    ],
                    [
                        'type' => 'separator',
                        'margin' => 'md',
                    ],
                    [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'margin' => 'md',
                        'spacing' => 'sm',
                        'contents' => $detailsContents
                    ]
                ]
            ],
            'footer' => [
                'type' => 'box',
                'layout' => 'vertical',
                'spacing' => 'sm',
                'contents' => [
                    [
                        'type' => 'button',
                        'action' => [
                            'type' => 'postback',
                            'label' => '✅ อนุมัติ',
                            'data' => "action=gc_approve&id={$guardChange->id}",
                            'displayText' => 'ขออนุมัติคำขอเปลี่ยนเวร'
                        ],
                        'style' => 'primary',
                        'color' => '#10b981',
                    ],
                    [
                        'type' => 'button',
                        'action' => [
                            'type' => 'postback',
                            'label' => '❌ ปฏิเสธ',
                            'data' => "action=gc_reject&id={$guardChange->id}",
                            'displayText' => 'ขอปฏิเสธคำขอเปลี่ยนเวร'
                        ],
                        'style' => 'secondary',
                        'color' => '#ef4444',
                    ],
                ]
            ]
        ];

        $this->lineService->sendFlexMessage(
            $recipient->line_user_id,
            "คำขอเปลี่ยนเวรยามจาก {$requester->name}",
            $flexContents
        );
    }

    /**
     * Send status update to requester via LINE
     */
    protected function sendStatusUpdateToLine($guardChange, $requester, $status, $actor)
    {
        if (!$requester->line_user_id) return;

        $title = '';
        $color = '#4f46e5';
        $statusText = '';

        switch ($status) {
            case 'approved':
                $title = '✅ ผู้เปลี่ยนแทนตอบรับแล้ว';
                $color = '#10b981';
                $statusText = "ผ่านขั้นตอนที่ 1 (รอ รอง ผอ. อนุมัติ)";
                break;
            case 'director_approved':
                $title = 'ℹ️ รอง ผอ. อนุมัติแล้ว';
                $color = '#3b82f6';
                $statusText = "ผ่านขั้นตอนที่ 2 (รอ ผอ. อนุมัติ)";
                break;
            case 'fully_approved':
                $title = '🎉 อนุมัติเสร็จสมบูรณ์';
                $color = '#10b981';
                $statusText = "ผ่านครบทุกขั้นตอนแล้ว";
                break;
            case 'rejected':
                $title = '❌ ถูกปฏิเสธ';
                $color = '#ef4444';
                $statusText = "ถูกปฏิเสธโดย {$actor->rank} {$actor->name}";
                break;
        }

        $dutyPosition = self::DUTY_POSITIONS[$guardChange->duty_position] ?? $guardChange->duty_position;

        // Build progress indicator
        $step1 = '✅';
        $step2 = in_array($status, ['director_approved', 'fully_approved']) ? '✅' : '⬜';
        $step3 = $status === 'fully_approved' ? '✅' : '⬜';

        if ($status === 'rejected') {
            $step1 = $guardChange->approver_id ? '✅' : '❌';
            $step2 = $guardChange->director_approver_id ? '✅' : ($guardChange->approver_id ? '❌' : '⬜');
            $step3 = '⬜';
        }

        $progressText = "{$step1} ผู้แทน → {$step2} รอง ผอ. → {$step3} ผอ.";

        $flexContents = [
            'type' => 'bubble',
            'header' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => $title,
                        'weight' => 'bold',
                        'color' => '#ffffff',
                    ]
                ],
                'backgroundColor' => $color,
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '🔄 คำขอเปลี่ยนเวรยาม',
                        'weight' => 'bold',
                        'size' => 'lg',
                    ],
                    [
                        'type' => 'text',
                        'text' => "ตำแหน่ง: {$dutyPosition}",
                        'size' => 'sm',
                        'color' => '#666666',
                        'margin' => 'sm',
                    ],
                    [
                        'type' => 'text',
                        'text' => "วันที่: " . Carbon::parse($guardChange->duty_date)->format('d/m/Y'),
                        'size' => 'sm',
                        'color' => '#666666',
                    ],
                    [
                        'type' => 'separator',
                        'margin' => 'md',
                    ],
                    [
                        'type' => 'text',
                        'text' => "สถานะ: {$statusText}",
                        'size' => 'md',
                        'margin' => 'md',
                        'color' => '#333333',
                        'wrap' => true,
                    ],
                    [
                        'type' => 'text',
                        'text' => "โดย: {$actor->rank} {$actor->name}",
                        'size' => 'sm',
                        'color' => '#666666',
                    ],
                    [
                        'type' => 'separator',
                        'margin' => 'md',
                    ],
                    [
                        'type' => 'text',
                        'text' => '📊 ความคืบหน้า',
                        'size' => 'sm',
                        'color' => '#4f46e5',
                        'weight' => 'bold',
                        'margin' => 'md',
                    ],
                    [
                        'type' => 'text',
                        'text' => $progressText,
                        'size' => 'sm',
                        'margin' => 'sm',
                        'wrap' => true,
                    ],
                ]
            ],
        ];

        $this->lineService->sendFlexMessage(
            $requester->line_user_id,
            $title,
            $flexContents
        );
    }

    /**
     * Send initial LINE notification when a guard change request is created
     */
    public function sendNewRequestNotification(GuardChangeRequest $guardChange)
    {
        $this->notifyNextApproverViaLine($guardChange, 'pending');
    }
}

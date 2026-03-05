<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\GuardChangeRequest;
use App\Services\LeaveApprovalService;
use App\Services\GuardChangeApprovalService;
use GuzzleHttp\Client;

class LineController extends Controller
{
    private $channelSecret;
    private $channelAccessToken;
    protected $approvalService;
    protected $guardChangeService;
    
    public function __construct(LeaveApprovalService $approvalService, GuardChangeApprovalService $guardChangeService)
    {
        $this->channelSecret = env('LINE_CHANNEL_SECRET');
        $this->channelAccessToken = env('LINE_CHANNEL_ACCESS_TOKEN');
        $this->approvalService = $approvalService;
        $this->guardChangeService = $guardChangeService;
    }
    
    public function webhook(Request $request)
    {
        Log::info("LINE Webhook Received", [
            'headers' => $request->headers->all(),
            'body' => $request->getContent()
        ]);

        // 1. Verify Signature
        $signature = $request->header('x-line-signature');
        if (empty($signature)) {
            Log::warning("LINE Signature Missing");
            return response('Bad Request', 400);
        }

        $body = $request->getContent();
        $hash = base64_encode(hash_hmac('sha256', $body, $this->channelSecret, true));
        
        if (!hash_equals($hash, $signature)) {
            Log::error("LINE Signature Mismatch", ['expected' => $hash, 'received' => $signature]);
            return response('Invalid Signature', 400);
        }

        // 3. Parse Events
        $data = json_decode($body, true);
        $events = $data['events'] ?? [];
        Log::info("Processing " . count($events) . " LINE events");
        
        foreach ($events as $event) {
            $replyToken = $event['replyToken'] ?? null;
            $userId = $event['source']['userId'] ?? null;
            $sourceType = $event['source']['type'] ?? 'user';
            
            // ❌ ไม่ตอบโต้ใดๆ ในกลุ่ม (กลุ่มใช้แค่ส่งรายงานอย่างเดียว)
            if ($sourceType === 'group' || $sourceType === 'room') {
                continue;
            }
            
            // --- ด้านล่างนี้เป็นแชทส่วนตัวเท่านั้น ---
            
            // A. Handle Postback (Approval/Rejection)
            if ($event['type'] === 'postback') {
                $this->handlePostback($userId, $event['postback']['data'], $replyToken);
            }
            // B. Handle Text Messages
            elseif ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $text = trim($event['message']['text']);
                
                if (strtolower($text) === 'get id') {
                    $this->replyText($replyToken, "User ID: {$userId}");
                } elseif (str_starts_with(strtolower($text), 'register ')) {
                    $email = trim(substr($text, 9));
                    $this->handleRegistration($userId, $email, $replyToken);
                } else {
                    $this->replyText($replyToken, "พิมพ์ 'Register [Email]' เพื่อเชื่อมต่อบัญชีครับ");
                }
            }
        }
        
        return response('OK', 200);
    }
    
    private function handlePostback($lineUserId, $dataString, $replyToken)
    {
        // Parse data: action=approve&id=123 OR action=gc_approve&id=123
        parse_str($dataString, $params);
        $action = $params['action'] ?? null;
        $requestId = $params['id'] ?? null;

        if (!$action || !$requestId) return;

        // 1. Find User
        $user = User::where('line_user_id', $lineUserId)->first();
        if (!$user) {
            $this->replyText($replyToken, "กรุณาลงทะเบียนก่อนใช้งาน พิมพ์ 'Register [Email]' ครับ");
            return;
        }

        // 2. Route to correct handler based on action prefix
        if (in_array($action, ['gc_approve', 'gc_reject'])) {
            $this->handleGuardChangePostback($user, $action, $requestId, $replyToken);
        } else {
            $this->handleLeavePostback($user, $action, $requestId, $replyToken);
        }
    }

    /**
     * Handle leave request postback (existing logic)
     */
    private function handleLeavePostback(User $user, $action, $requestId, $replyToken)
    {
        $leaveRequest = LeaveRequest::with('user')->find($requestId);
        if (!$leaveRequest) {
            $this->replyText($replyToken, "ไม่พบข้อมูลใบลา หรือข้อมูลอาจถูกลบไปแล้ว");
            return;
        }

        // Check if already processed
        if ($leaveRequest->status === 'approved' || $leaveRequest->status === 'rejected') {
            $this->replyText($replyToken, "ใบลานี้ได้รับการดำเนินการไปแล้ว (สถานะ: " . $leaveRequest->status . ")");
            return;
        }

        try {
            if ($action === 'approve') {
                $this->approvalService->approve($leaveRequest, $user);
                $this->replyText($replyToken, "✅ ดำเนินการอนุมัติใบลาของ " . $leaveRequest->user->name . " สำเร็จครับ");
            } elseif ($action === 'reject') {
                $this->approvalService->reject($leaveRequest, $user, 'ปฏิเสธผ่าน LINE');
                $this->replyText($replyToken, "❌ ปฏิเสธใบลาของ " . $leaveRequest->user->name . " แล้วครับ");
            }
        } catch (\Exception $e) {
            $this->replyText($replyToken, "เกิดข้อผิดพลาด: " . $e->getMessage());
        }
    }

    /**
     * Handle guard change request postback (new)
     */
    private function handleGuardChangePostback(User $user, $action, $requestId, $replyToken)
    {
        $guardChange = GuardChangeRequest::with(['user', 'replacementUser'])->find($requestId);
        if (!$guardChange) {
            $this->replyText($replyToken, "ไม่พบข้อมูลคำขอเปลี่ยนเวร หรือข้อมูลอาจถูกลบไปแล้ว");
            return;
        }

        // Check if already fully processed or rejected
        if (in_array($guardChange->status, ['fully_approved', 'rejected', 'cancelled'])) {
            $statusLabel = $this->guardChangeService->getStatusLabel($guardChange->status);
            $this->replyText($replyToken, "คำขอเปลี่ยนเวรนี้ดำเนินการไปแล้ว (สถานะ: {$statusLabel})");
            return;
        }

        $statusLabels = [
            'pending' => 'ขั้นตอนที่ 1: ผู้เปลี่ยนแทนตอบรับ',
            'approved' => 'ขั้นตอนที่ 2: รอง ผอ. อนุมัติ',
            'director_approved' => 'ขั้นตอนที่ 3: ผอ. อนุมัติ (สุดท้าย)',
        ];

        $currentStep = $statusLabels[$guardChange->status] ?? $guardChange->status;

        try {
            if ($action === 'gc_approve') {
                $this->guardChangeService->approve($guardChange, $user, null, true);

                // Refresh to get updated status
                $guardChange->refresh();
                $newStatusLabel = $this->guardChangeService->getStatusLabel($guardChange->status);

                $this->replyText($replyToken, 
                    "✅ อนุมัติคำขอเปลี่ยนเวรของ " . $guardChange->user->name . " สำเร็จครับ\n" .
                    "📍 สถานะ: {$newStatusLabel}"
                );
            } elseif ($action === 'gc_reject') {
                $this->guardChangeService->reject($guardChange, $user, 'ปฏิเสธผ่าน LINE');
                $this->replyText($replyToken, "❌ ปฏิเสธคำขอเปลี่ยนเวรของ " . $guardChange->user->name . " แล้วครับ");
            }
        } catch (\Exception $e) {
            $this->replyText($replyToken, "เกิดข้อผิดพลาด: " . $e->getMessage());
        }
    }
    
    private function handleRegistration($lineUserId, $email, $replyToken)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->replyText($replyToken, "ไม่พบอีเมล $email ในระบบครับ");
            return;
        }
        
        if ($user->line_user_id) {
            $this->replyText($replyToken, "บัญชีนี้ได้เชื่อมต่อไปแล้วครับ");
            return;
        }

        $user->line_user_id = $lineUserId;
        $user->save();
        
        $this->replyText($replyToken, "ผูกบัญชีสำเร็จ! สวัสดีคุณ " . $user->name . " ครับ");
    }
    
    private function replyText($replyToken, $text)
    {
        if (!$replyToken) return;
        
        $client = new Client();
        try {
            $client->post('https://api.line.me/v2/bot/message/reply', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->channelAccessToken,
                ],
                'json' => [
                    'replyToken' => $replyToken,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $text,
                        ]
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("LINE Reply Error: " . $e->getMessage());
        }
    }
}

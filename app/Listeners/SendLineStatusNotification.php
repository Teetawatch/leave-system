<?php

namespace App\Listeners;

use App\Events\LeaveRequestStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\LineService;
use App\Models\User;

class SendLineStatusNotification implements ShouldQueue
{
    protected $lineService;

    /**
     * Create the event listener.
     */
    public function __construct(LineService $lineService)
    {
        $this->lineService = $lineService;
    }

    /**
     * Handle the event.
     */
    public function handle(LeaveRequestStatusChanged $event): void
    {
        $leaveRequest = $event->leaveRequest;
        $status = $event->status;
        $actor = $event->actor;
        $user = $leaveRequest->user; // The requester

        // 1. Notify the Requester about the change (Approved/Rejected/Acknowledged at some step)
        $this->notifyRequester($leaveRequest, $status, $actor);

        // 2. If it's a "pending" status, notify the NEXT person
        if (str_starts_with($status, 'pending_')) {
            $this->notifyNextApprover($leaveRequest, $status);
        }
    }

    private function notifyRequester($leaveRequest, $status, $actor)
    {
        $user = $leaveRequest->user;
        if (!$user->line_user_id) return;

        $title = "ใบลาได้รับการอัปเดต";
        $color = "#4f46e5"; // Default Blue
        $statusText = $status;

        if ($status === 'approved') {
            $title = "✅ การลาได้รับอนุมัติแล้ว";
            $color = "#10b981"; // Green
            $statusText = "อนุมัติเรียบร้อย";
        } elseif ($status === 'rejected') {
            $title = "❌ การลาถูกปฏิเสธ";
            $color = "#ef4444"; // Red
            $statusText = "ถูกปฏิเสธ";
        } elseif (str_starts_with($status, 'pending_')) {
            $title = "ℹ️ อยู่ระหว่างการดำเนินการ";
            $statusText = "ผ่านการตรวจสอบจาก {$actor->rank} {$actor->name} แล้ว";
        }

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
                        'text' => "ใบลา{$leaveRequest->leaveType->name}",
                        'weight' => 'bold',
                        'size' => 'lg',
                    ],
                    [
                        'type' => 'text',
                        'text' => "สถานะ: {$statusText}",
                        'size' => 'md',
                        'margin' => 'sm',
                        'color' => '#333333',
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
                        'text' => "วันที่: " . $leaveRequest->start_date->format('d/m/Y') . " - " . $leaveRequest->end_date->format('d/m/Y'),
                        'size' => 'sm',
                        'margin' => 'md',
                        'color' => '#666666',
                    ]
                ]
            ],
            'footer' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'button',
                        'action' => [
                            'type' => 'uri',
                            'label' => 'ดูรายละเอียด',
                            'uri' => route('leave-request.index'), // Link to my leaves
                        ],
                        'style' => 'secondary',
                    ]
                ]
            ]
        ];

        $this->lineService->sendFlexMessage(
            $user->line_user_id,
            $title,
            $flexContents
        );
    }

    private function notifyNextApprover($leaveRequest, $status)
    {
        $user = $leaveRequest->user;

        if ($status === 'pending_manager') {
            $recipient = $user->manager_id ? User::find($user->manager_id) : null;
            if ($recipient && $recipient->line_user_id) {
                $this->sendApprovalRequest($recipient, $leaveRequest);
            }
            return;
        }

        if ($status === 'pending_deputy_director') {
            $recipients = User::where('role', 'deputy_director')->whereNotNull('line_user_id')->get();
        } elseif ($status === 'pending_director') {
            $recipients = User::where('role', 'director')->whereNotNull('line_user_id')->get();
        } else {
            return;
        }

        if ($recipients->isEmpty()) return;

        $lineUserIds = $recipients->pluck('line_user_id')->values()->all();

        if (count($lineUserIds) === 1) {
            $this->sendApprovalRequest($recipients->first(), $leaveRequest);
            return;
        }

        $user = $leaveRequest->user;
        $flexContents = $this->buildApprovalFlexContents($leaveRequest, $user);

        $this->lineService->multicastFlexMessage(
            $lineUserIds,
            "มีใบลาใหม่รอนุมัติจาก {$user->name}",
            $flexContents
        );
    }

    private function sendApprovalRequest($recipient, $leaveRequest)
    {
        $user = $leaveRequest->user;
        $flexContents = $this->buildApprovalFlexContents($leaveRequest, $user);

        $this->lineService->sendFlexMessage(
            $recipient->line_user_id,
            "มีใบลาใหม่รอนุมัติจาก {$user->name}",
            $flexContents
        );
    }

    private function buildApprovalFlexContents($leaveRequest, $user): array
    {
        $detailsContents = [
            [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'ประเภท:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => $leaveRequest->leaveType->name, 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ]
        ];

        if ($leaveRequest->temporary_leave_period) {
            $periodText = $leaveRequest->temporary_leave_period;
            if ($periodText === 'morning') $periodText = 'ช่วงเช้า';
            elseif ($periodText === 'afternoon') $periodText = 'ช่วงบ่าย';

            $detailsContents[] = [
                'type' => 'box',
                'layout' => 'horizontal',
                'contents' => [
                    ['type' => 'text', 'text' => 'ช่วงเวลา:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => $periodText, 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ];
        }

        $detailsContents[] = [
            'type' => 'box',
            'layout' => 'horizontal',
            'contents' => [
                ['type' => 'text', 'text' => 'วันที่:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                ['type' => 'text', 'text' => $leaveRequest->start_date->format('d/m/Y') . ' - ' . $leaveRequest->end_date->format('d/m/Y'), 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
            ]
        ];

        $detailsContents[] = [
            'type' => 'box',
            'layout' => 'horizontal',
            'contents' => [
                ['type' => 'text', 'text' => 'จำนวน:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                ['type' => 'text', 'text' => $leaveRequest->total_days . ' วัน', 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
            ]
        ];

        $detailsContents[] = [
            'type' => 'box',
            'layout' => 'horizontal',
            'contents' => [
                ['type' => 'text', 'text' => 'เหตุผล:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                ['type' => 'text', 'text' => $leaveRequest->reason ?: '-', 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
            ]
        ];

        if (!empty($leaveRequest->contact_address)) {
            $contactText = is_array($leaveRequest->contact_address)
                ? implode(', ', $leaveRequest->contact_address)
                : (string) $leaveRequest->contact_address;

            $detailsContents[] = [
                'type' => 'box',
                'layout' => 'horizontal',
                'margin' => 'md',
                'contents' => [
                    ['type' => 'text', 'text' => 'เบอร์ติดต่อ:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => $contactText, 'size' => 'sm', 'color' => '#333333', 'flex' => 5, 'wrap' => true],
                ]
            ];
        }

        if ($leaveRequest->attachment_path) {
            $detailsContents[] = [
                'type' => 'box',
                'layout' => 'horizontal',
                'margin' => 'md',
                'contents' => [
                    ['type' => 'text', 'text' => 'ไฟล์แนบ:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                    ['type' => 'text', 'text' => '📎 มีเอกสารแนบ (ดูในระบบ)', 'size' => 'sm', 'color' => '#10b981', 'flex' => 5, 'weight' => 'bold', 'wrap' => true],
                ]
            ];
        }

        return [
            'type' => 'bubble',
            'size' => 'kilo',
            'header' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '🔔 มีใบลาใหม่รอนุมัติ',
                        'weight' => 'bold',
                        'color' => '#ffffff',
                        'size' => 'md',
                    ]
                ],
                'backgroundColor' => '#4f46e5',
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => trim("{$user->rank} {$user->name}"),
                        'weight' => 'bold',
                        'size' => 'lg',
                        'wrap' => true,
                    ],
                    [
                        'type' => 'text',
                        'text' => "สังกัด: " . ($user->department ?: '-'),
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
                            'data' => "action=approve&id={$leaveRequest->id}",
                            'displayText' => 'ขออนุมัติใบลา'
                        ],
                        'style' => 'primary',
                        'color' => '#10b981',
                    ],
                    [
                        'type' => 'button',
                        'action' => [
                            'type' => 'postback',
                            'label' => '❌ ปฏิเสธ',
                            'data' => "action=reject&id={$leaveRequest->id}",
                            'displayText' => 'ขอปฏิเสธใบลา'
                        ],
                        'style' => 'secondary',
                        'color' => '#ef4444',
                    ],
                    [
                        'type' => 'button',
                        'action' => [
                            'type' => 'uri',
                            'label' => '📎 รายละเอียดเพิ่มเติม',
                            'uri' => route('approvals.index'),
                        ],
                        'style' => 'link',
                    ]
                ]
            ]
        ];
    }
}

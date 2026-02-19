<?php

namespace App\Listeners;

use App\Events\LeaveRequestSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\LineService;
use App\Models\User;

class SendLineLeaveNotification implements ShouldQueue
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
    public function handle(LeaveRequestSubmitted $event): void
    {
        $leaveRequest = $event->leaveRequest;
        $user = $leaveRequest->user;
        
        // Find the next person to notify based on status
        $recipientId = null;
        
        if ($leaveRequest->status === 'pending_supervisor') {
            $recipientId = $user->supervisor_id;
        } elseif ($leaveRequest->status === 'pending_head') {
            $recipientId = $user->deputy_id; // Mapping to deputy as "head" if that's the flow
        } elseif ($leaveRequest->status === 'pending_manager') {
            $recipientId = $user->manager_id;
        }

        if (!$recipientId) return;

        $recipient = User::find($recipientId);
        if (!$recipient || !$recipient->line_user_id) return;

        // Prepare Flex Message
        $flexContents = [
            'type' => 'bubble',
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
                        'text' => "{$user->rank} {$user->name}",
                        'weight' => 'bold',
                        'size' => 'lg',
                    ],
                    [
                        'type' => 'text',
                        'text' => "สังกัด: " . ($user->department ?: '-'),
                        'size' => 'sm',
                        'color' => '#666666',
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
                        'contents' => [
                            [
                                'type' => 'box',
                                'layout' => 'horizontal',
                                'contents' => [
                                    ['type' => 'text', 'text' => 'ประเภท:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                                    ['type' => 'text', 'text' => $leaveRequest->leaveType->name, 'size' => 'sm', 'color' => '#333333', 'flex' => 4],
                                ]
                            ],
                            [
                                'type' => 'box',
                                'layout' => 'horizontal',
                                'contents' => [
                                    ['type' => 'text', 'text' => 'วันที่:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                                    ['type' => 'text', 'text' => $leaveRequest->start_date->format('d/m/Y') . ' - ' . $leaveRequest->end_date->format('d/m/Y'), 'size' => 'sm', 'color' => '#333333', 'flex' => 4],
                                ]
                            ],
                            [
                                'type' => 'box',
                                'layout' => 'horizontal',
                                'contents' => [
                                    ['type' => 'text', 'text' => 'รวม:', 'size' => 'sm', 'color' => '#aaaaaa', 'flex' => 2],
                                    ['type' => 'text', 'text' => $leaveRequest->total_days . ' วัน', 'size' => 'sm', 'color' => '#333333', 'flex' => 4],
                                ]
                            ],
                        ]
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

        $this->lineService->sendFlexMessage(
            $recipient->line_user_id,
            "มีใบลาใหม่จาก {$user->name}",
            $flexContents
        );
    }
}

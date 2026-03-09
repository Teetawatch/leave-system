<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\LeaveType;
use App\Services\LineService;
use App\Jobs\SendLineMessageJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyLeaveSummary extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'line:daily-leave-summary 
                            {--date= : วันที่ต้องการสรุป (YYYY-MM-DD), ค่าเริ่มต้นคือวันนี้}
                            {--test : โหมดทดสอบ แสดงข้อความแต่ไม่ส่ง LINE}';

    /**
     * The console command description.
     */
    protected $description = 'ส่งสรุปการลาประจำวันเข้ากลุ่ม LINE แยกตามข้าราชการและหลักสูตร';

    /**
     * รายชื่อหลักสูตร (นักเรียน)
     */
    protected $studentCourses = [
        'หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69',
    ];

    protected $lineService;

    public function __construct(LineService $lineService)
    {
        parent::__construct();
        $this->lineService = $lineService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dateStr = $this->option('date') ?: now()->format('Y-m-d');
        $date = Carbon::parse($dateStr);
        $isTest = $this->option('test');

        $this->info("📋 กำลังสรุปการลาประจำวันที่ {$this->toThaiDate($date)}...");

        // ดึงข้อมูลการลาที่ approved ในวันนี้
        $leaveRequests = LeaveRequest::with(['user', 'leaveType'])
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();

        if ($leaveRequests->isEmpty()) {
            $this->info('✅ ไม่มีใครลาในวันนี้');

            if (!$isTest) {
                $noLeaveMessage = $this->buildNoLeaveMessage($date);
                $this->lineService->sendGroupTextMessage2($noLeaveMessage);
            }

            return Command::SUCCESS;
        }

        // แยกข้อมูลตามประเภท: ข้าราชการ vs หลักสูตร
        $officerLeaves = [];   // ข้าราชการ
        $studentLeaves = [];    // หลักสูตร

        foreach ($leaveRequests as $leave) {
            $user = $leave->user;
            if (!$user) continue;

            $isStudent = in_array($user->department, $this->studentCourses);

            $leaveInfo = [
                'name' => ($user->rank ? $user->rank . ' ' : '') . $user->name,
                'department' => $user->department ?? 'ไม่ระบุ',
                'leave_type' => $leave->leaveType->name ?? 'ไม่ระบุ',
                'start_date' => $this->toThaiDate($leave->start_date),
                'end_date' => $this->toThaiDate($leave->end_date),
                'total_days' => $leave->total_days,
            ];

            if ($isStudent) {
                $studentLeaves[] = $leaveInfo;
            } else {
                $officerLeaves[] = $leaveInfo;
            }
        }

        // แสดงข้อมูลใน Console
        $this->displaySummary($date, $officerLeaves, $studentLeaves);

        // ส่ง Flex Message เข้ากลุ่ม LINE
        if (!$isTest) {
            // Send flex message directly (no queue for shared hosting)
            $flexMessage = $this->buildFlexMessage($date, $officerLeaves, $studentLeaves);
            $altText = sprintf(
                '📋 สรุปการลาประจำวัน %s',
                $this->toThaiDate($date)
            );
            
            try {
                $result = $this->lineService->sendGroupFlexMessage2($altText, $flexMessage);
                
                if ($result) {
                    $this->info('✅ ส่งสรุปเข้ากลุ่ม LINE เรียบร้อยแล้ว!');
                    Log::info('Daily leave summary sent to LINE group', [
                        'date' => $dateStr,
                        'officers' => count($officerLeaves),
                        'students' => count($studentLeaves),
                    ]);
                } else {
                    $this->error('❌ ส่งสรุปเข้ากลุ่ม LINE ไม่สำเร็จ');
                    Log::error('Failed to send daily leave summary to LINE group');
                }
            } catch (\Exception $e) {
                $this->error("Failed to send flex message: " . $e->getMessage());
                $result = false;
            }
        } else {
            $this->warn('⚠️  โหมดทดสอบ: ไม่ได้ส่งข้อความจริง');
        }

        return Command::SUCCESS;
    }

    /**
     * แสดงสรุปใน Console
     */
    protected function displaySummary(Carbon $date, array $officerLeaves, array $studentLeaves)
    {
        $this->newLine();
        $this->info("══════════════════════════════════════════");
        $this->info("   📋 สรุปการลาประจำวัน {$this->toThaiDate($date)}");
        $this->info("══════════════════════════════════════════");

        // ข้าราชการ
        $this->newLine();
        $this->info("🏛️  ข้าราชการ ({$this->countText(count($officerLeaves))})");
        $this->info("──────────────────────────────────────────");

        if (empty($officerLeaves)) {
            $this->line("   ✅ ไม่มีข้าราชการลา");
        } else {
            foreach ($officerLeaves as $i => $leave) {
                $num = $i + 1;
                $this->line("   {$num}. {$leave['name']}");
                $this->line("      📁 {$leave['department']}");
                $this->line("      📝 {$leave['leave_type']} ({$leave['total_days']} วัน)");
                if ($leave['start_date'] !== $leave['end_date']) {
                    $this->line("      📅 {$leave['start_date']} - {$leave['end_date']}");
                }
            }
        }

        // หลักสูตร
        $this->newLine();
        $this->info("🎓 หลักสูตร ({$this->countText(count($studentLeaves))})");
        $this->info("──────────────────────────────────────────");

        if (empty($studentLeaves)) {
            $this->line("   ✅ ไม่มีนักเรียนหลักสูตรลา");
        } else {
            // Group by course
            $grouped = collect($studentLeaves)->groupBy('department');
            foreach ($grouped as $course => $leaves) {
                $this->line("   📚 {$course}");
                foreach ($leaves as $i => $leave) {
                    $num = $i + 1;
                    $this->line("      {$num}. {$leave['name']}");
                    $this->line("         📝 {$leave['leave_type']} ({$leave['total_days']} วัน)");
                    if ($leave['start_date'] !== $leave['end_date']) {
                        $this->line("         📅 {$leave['start_date']} - {$leave['end_date']}");
                    }
                }
            }
        }

        $this->newLine();
        $total = count($officerLeaves) + count($studentLeaves);
        $this->info("📊 รวมทั้งหมด: {$total} คน");
        $this->info("══════════════════════════════════════════");
    }

    /**
     * สร้าง Flex Message สำหรับสรุปการลา
     */
    protected function buildFlexMessage(Carbon $date, array $officerLeaves, array $studentLeaves): array
    {
        $thaiDate = $this->toThaiDate($date);
        $totalCount = count($officerLeaves) + count($studentLeaves);

        $bodyContents = [];

        // ── Header Section ──
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => '📋 สรุปการลาประจำวัน',
                    'weight' => 'bold',
                    'size' => 'lg',
                    'color' => '#1a237e',
                ],
                [
                    'type' => 'text',
                    'text' => $thaiDate,
                    'size' => 'sm',
                    'color' => '#666666',
                    'margin' => 'xs',
                ],
                [
                    'type' => 'text',
                    'text' => "ลาทั้งหมด {$totalCount} คน",
                    'size' => 'sm',
                    'color' => '#e65100',
                    'weight' => 'bold',
                    'margin' => 'xs',
                ],
            ],
            'paddingBottom' => 'md',
        ];

        // ── Separator ──
        $bodyContents[] = [
            'type' => 'separator',
            'color' => '#e0e0e0',
        ];

        // ══════ ข้าราชการ Section ══════
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => '🏛️ ข้าราชการ',
                            'weight' => 'bold',
                            'size' => 'md',
                            'color' => '#1565c0',
                            'flex' => 1,
                        ],
                        [
                            'type' => 'text',
                            'text' => count($officerLeaves) . ' คน',
                            'size' => 'sm',
                            'color' => '#1565c0',
                            'weight' => 'bold',
                            'align' => 'end',
                            'flex' => 0,
                        ],
                    ],
                ],
            ],
            'paddingTop' => 'md',
            'paddingBottom' => 'sm',
        ];

        if (empty($officerLeaves)) {
            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '✅ ไม่มีข้าราชการลา',
                        'size' => 'sm',
                        'color' => '#4caf50',
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        } else {
            foreach ($officerLeaves as $leave) {
                $dateRange = $leave['start_date'] === $leave['end_date']
                    ? ''
                    : " ({$leave['start_date']}-{$leave['end_date']})";
                
                $bodyContents[] = [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => "• {$leave['name']}",
                            'size' => 'sm',
                            'color' => '#333333',
                            'wrap' => true,
                        ],
                        [
                            'type' => 'text',
                            'text' => "  {$leave['leave_type']} {$leave['total_days']} วัน{$dateRange}",
                            'size' => 'xs',
                            'color' => '#888888',
                            'wrap' => true,
                        ],
                    ],
                    'paddingStart' => 'md',
                    'paddingBottom' => 'xs',
                ];
            }
        }

        // ── Separator ──
        $bodyContents[] = [
            'type' => 'separator',
            'color' => '#e0e0e0',
            'margin' => 'sm',
        ];

        // ══════ หลักสูตร Section ══════
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => '🎓 หลักสูตร',
                            'weight' => 'bold',
                            'size' => 'md',
                            'color' => '#6a1b9a',
                            'flex' => 1,
                        ],
                        [
                            'type' => 'text',
                            'text' => count($studentLeaves) . ' คน',
                            'size' => 'sm',
                            'color' => '#6a1b9a',
                            'weight' => 'bold',
                            'align' => 'end',
                            'flex' => 0,
                        ],
                    ],
                ],
            ],
            'paddingTop' => 'md',
            'paddingBottom' => 'sm',
        ];

        if (empty($studentLeaves)) {
            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '✅ ไม่มีนักเรียนหลักสูตรลา',
                        'size' => 'sm',
                        'color' => '#4caf50',
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        } else {
            // Group by course
            $grouped = collect($studentLeaves)->groupBy('department');
            foreach ($grouped as $course => $leaves) {
                // Course header
                $shortCourse = mb_strlen($course) > 35 ? mb_substr($course, 0, 35) . '...' : $course;
                $bodyContents[] = [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => "📚 {$shortCourse}",
                            'size' => 'xs',
                            'color' => '#6a1b9a',
                            'weight' => 'bold',
                            'wrap' => true,
                        ],
                    ],
                    'paddingStart' => 'md',
                    'paddingTop' => 'xs',
                ];

                foreach ($leaves as $leave) {
                    $dateRange = $leave['start_date'] === $leave['end_date']
                        ? ''
                        : " ({$leave['start_date']}-{$leave['end_date']})";

                    $bodyContents[] = [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => "• {$leave['name']}",
                                'size' => 'sm',
                                'color' => '#333333',
                                'wrap' => true,
                            ],
                            [
                                'type' => 'text',
                                'text' => "  {$leave['leave_type']} {$leave['total_days']} วัน{$dateRange}",
                                'size' => 'xs',
                                'color' => '#888888',
                                'wrap' => true,
                            ],
                        ],
                        'paddingStart' => 'lg',
                        'paddingBottom' => 'xs',
                    ];
                }
            }
        }

        // ── Footer ──
        $bodyContents[] = [
            'type' => 'separator',
            'color' => '#e0e0e0',
            'margin' => 'md',
        ];

        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => 'ระบบบริหารจัดการการลา รพธ.พธ.ทร.',
                    'size' => 'xxs',
                    'color' => '#aaaaaa',
                    'align' => 'center',
                ],
                [
                    'type' => 'text',
                    'text' => 'ส่งอัตโนมัติ ' . now()->format('H:i') . ' น.',
                    'size' => 'xxs',
                    'color' => '#aaaaaa',
                    'align' => 'center',
                    'margin' => 'xs',
                ],
            ],
            'paddingTop' => 'sm',
        ];

        return [
            'type' => 'bubble',
            'size' => 'mega',
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => $bodyContents,
                'paddingAll' => 'lg',
                'backgroundColor' => '#ffffff',
            ],
        ];
    }

    /**
     * สร้างข้อความเมื่อไม่มีใครลา
     */
    protected function buildNoLeaveMessage(Carbon $date): string
    {
        $thaiDate = $this->toThaiDate($date);
        
        return "📋 สรุปการลาประจำวัน {$thaiDate}\n\n✅ วันนี้ไม่มีข้าราชการและนักเรียนหลักสูตรลา\n\n🏛️ ระบบบริหารจัดการการลา รพธ.พธ.ทร.";
    }

    /**
     * แปลงวันที่เป็นรูปแบบไทย เช่น 5 มี.ค. 69
     */
    protected function toThaiDate(Carbon $date): string
    {
        $thaiMonths = [
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.',
        ];

        $thaiYear = ($date->year + 543) % 100; // เอาแค่ 2 หลักท้าย เช่น 69
        return $date->day . ' ' . $thaiMonths[$date->month] . ' ' . $thaiYear;
    }

    /**
     * แปลงจำนวนเป็นข้อความ
     */
    protected function countText(int $count): string
    {
        return $count > 0 ? "{$count} คน" : 'ไม่มี';
    }
}

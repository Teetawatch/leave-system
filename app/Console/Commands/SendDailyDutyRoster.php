<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DutyRoster;
use App\Models\SeniorDutyRoster;
use App\Services\LineService;
use App\Jobs\SendLineMessageJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyDutyRoster extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'line:daily-duty-roster
                            {--date= : วันที่ต้องการแจ้งเตือน (YYYY-MM-DD), ค่าเริ่มต้นคือวันนี้}
                            {--test : โหมดทดสอบ แสดงข้อความแต่ไม่ส่ง LINE}';

    /**
     * The console command description.
     */
    protected $description = 'ส่งแจ้งเตือนเวรยามประจำวันเข้ากลุ่ม LINE';

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

        $this->info("🛡️ กำลังดึงข้อมูลเวรยามประจำวันที่ {$this->toThaiDate($date)}...");

        // ดึงข้อมูลเวรรายวัน
        $dutyRoster = DutyRoster::with(['dutyOfficer', 'assistantDutyOfficer'])
            ->whereDate('duty_date', $date)
            ->first();

        // ดึงข้อมูลนายทหารเวรอาวุโสที่ครอบคลุมวันนี้
        $seniorDutyRoster = SeniorDutyRoster::with(['seniorOfficer'])
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        // แสดงใน Console
        $this->displaySummary($date, $dutyRoster, $seniorDutyRoster);

        if (!$isTest) {
            if (!$dutyRoster && !$seniorDutyRoster) {
                // Use queue for text message
                SendLineMessageJob::dispatch('text', [
                    'text' => $this->buildNoRosterMessage($date)
                ]);
                $result = true;
            } else {
                // Use queue for flex message
                $flexMessage = $this->buildFlexMessage($date, $dutyRoster, $seniorDutyRoster);
                $altText = sprintf(
                    '🛡️ เวรยามประจำวัน %s',
                    $this->toThaiDate($date)
                );
                SendLineMessageJob::dispatch('flex', [
                    'altText' => $altText,
                    'flexContents' => $flexMessage
                ]);
                $result = true;
            }

            if ($result) {
                $this->info('✅ ส่งแจ้งเตือนเวรยามเข้ากลุ่ม LINE เรียบร้อยแล้ว!');
                Log::info('Daily duty roster notification sent to LINE group', ['date' => $dateStr]);
            } else {
                $this->error('❌ ส่งแจ้งเตือนเวรยามเข้ากลุ่ม LINE ไม่สำเร็จ');
                Log::error('Failed to send daily duty roster notification to LINE group', ['date' => $dateStr]);
            }
        } else {
            $this->warn('⚠️  โหมดทดสอบ: ไม่ได้ส่งข้อความจริง');
        }

        return Command::SUCCESS;
    }

    /**
     * แสดงสรุปใน Console
     */
    protected function displaySummary(Carbon $date, $dutyRoster, $seniorDutyRoster)
    {
        $this->newLine();
        $this->info("══════════════════════════════════════════");
        $this->info("   🛡️ เวรยามประจำวัน {$this->toThaiDate($date)}");
        $this->info("══════════════════════════════════════════");

        // นายทหารเวรอาวุโส
        $this->newLine();
        $this->info("⭐ นายทหารเวรอาวุโส");
        $this->info("──────────────────────────────────────────");
        if ($seniorDutyRoster && $seniorDutyRoster->seniorOfficer) {
            $officer = $seniorDutyRoster->seniorOfficer;
            $name = ($officer->rank ? $officer->rank . ' ' : '') . $officer->name;
            $period = $this->toThaiDate($seniorDutyRoster->start_date) . ' - ' . $this->toThaiDate($seniorDutyRoster->end_date);
            $this->line("   {$name}");
            $this->line("   📅 ห้วงเวร: {$period}");
            if ($seniorDutyRoster->notes) {
                $this->line("   📝 {$seniorDutyRoster->notes}");
            }
        } else {
            $this->line("   ⚠️  ไม่มีข้อมูลนายทหารเวรอาวุโส");
        }

        // นายทหารเวร
        $this->newLine();
        $this->info("🎖️  นายทหารเวร");
        $this->info("──────────────────────────────────────────");
        if ($dutyRoster && $dutyRoster->dutyOfficer) {
            $officer = $dutyRoster->dutyOfficer;
            $name = ($officer->rank ? $officer->rank . ' ' : '') . $officer->name;
            $this->line("   {$name}");
        } else {
            $this->line("   ⚠️  ไม่มีข้อมูลนายทหารเวร");
        }

        // ผู้ช่วยนายทหารเวร
        $this->newLine();
        $this->info("🎗️  ผู้ช่วยนายทหารเวร");
        $this->info("──────────────────────────────────────────");
        if ($dutyRoster && $dutyRoster->assistantDutyOfficer) {
            $officer = $dutyRoster->assistantDutyOfficer;
            $name = ($officer->rank ? $officer->rank . ' ' : '') . $officer->name;
            $this->line("   {$name}");
        } else {
            $this->line("   ⚠️  ไม่มีข้อมูลผู้ช่วยนายทหารเวร");
        }

        if ($dutyRoster && $dutyRoster->notes) {
            $this->newLine();
            $this->info("📝 หมายเหตุ: {$dutyRoster->notes}");
        }

        $this->newLine();
        $this->info("══════════════════════════════════════════");
    }

    /**
     * สร้าง Flex Message สำหรับเวรยาม
     */
    protected function buildFlexMessage(Carbon $date, $dutyRoster, $seniorDutyRoster): array
    {
        $thaiDate = $this->toThaiDate($date);
        $thaiDay = $this->toThaiDayOfWeek($date);

        $bodyContents = [];

        // ── Header Section ──
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => '🛡️ เวรยามประจำวัน',
                    'weight' => 'bold',
                    'size' => 'lg',
                    'color' => '#1a237e',
                ],
                [
                    'type' => 'text',
                    'text' => "{$thaiDay} {$thaiDate}",
                    'size' => 'sm',
                    'color' => '#666666',
                    'margin' => 'xs',
                ],
            ],
            'paddingBottom' => 'md',
        ];

        // ── Separator ──
        $bodyContents[] = ['type' => 'separator', 'color' => '#e0e0e0'];

        // ══════ นายทหารเวรอาวุโส ══════
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => '⭐ นายทหารเวรอาวุโส',
                    'weight' => 'bold',
                    'size' => 'md',
                    'color' => '#b8860b',
                ],
            ],
            'paddingTop' => 'md',
            'paddingBottom' => 'sm',
        ];

        if ($seniorDutyRoster && $seniorDutyRoster->seniorOfficer) {
            $officer = $seniorDutyRoster->seniorOfficer;
            $name = ($officer->rank ? $officer->rank . ' ' : '') . $officer->name;
            $period = $this->toThaiDate($seniorDutyRoster->start_date) . ' - ' . $this->toThaiDate($seniorDutyRoster->end_date);

            $nameContents = [
                [
                    'type' => 'text',
                    'text' => $name,
                    'size' => 'sm',
                    'color' => '#333333',
                    'weight' => 'bold',
                    'wrap' => true,
                ],
                [
                    'type' => 'text',
                    'text' => "ห้วงเวร: {$period}",
                    'size' => 'xs',
                    'color' => '#888888',
                    'wrap' => true,
                ],
            ];

            if ($seniorDutyRoster->notes) {
                $nameContents[] = [
                    'type' => 'text',
                    'text' => "หมายเหตุ: {$seniorDutyRoster->notes}",
                    'size' => 'xs',
                    'color' => '#888888',
                    'wrap' => true,
                ];
            }

            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => $nameContents,
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        } else {
            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '⚠️ ไม่มีข้อมูล',
                        'size' => 'sm',
                        'color' => '#e65100',
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        }

        // ── Separator ──
        $bodyContents[] = ['type' => 'separator', 'color' => '#e0e0e0', 'margin' => 'sm'];

        // ══════ นายทหารเวร ══════
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => '🎖️ นายทหารเวร',
                    'weight' => 'bold',
                    'size' => 'md',
                    'color' => '#1565c0',
                ],
            ],
            'paddingTop' => 'md',
            'paddingBottom' => 'sm',
        ];

        if ($dutyRoster && $dutyRoster->dutyOfficer) {
            $officer = $dutyRoster->dutyOfficer;
            $name = ($officer->rank ? $officer->rank . ' ' : '') . $officer->name;

            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => $name,
                        'size' => 'sm',
                        'color' => '#333333',
                        'weight' => 'bold',
                        'wrap' => true,
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        } else {
            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '⚠️ ไม่มีข้อมูล',
                        'size' => 'sm',
                        'color' => '#e65100',
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        }

        // ── Separator ──
        $bodyContents[] = ['type' => 'separator', 'color' => '#e0e0e0', 'margin' => 'sm'];

        // ══════ ผู้ช่วยนายทหารเวร ══════
        $bodyContents[] = [
            'type' => 'box',
            'layout' => 'vertical',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => '🎗️ ผู้ช่วยนายทหารเวร',
                    'weight' => 'bold',
                    'size' => 'md',
                    'color' => '#2e7d32',
                ],
            ],
            'paddingTop' => 'md',
            'paddingBottom' => 'sm',
        ];

        if ($dutyRoster && $dutyRoster->assistantDutyOfficer) {
            $officer = $dutyRoster->assistantDutyOfficer;
            $name = ($officer->rank ? $officer->rank . ' ' : '') . $officer->name;

            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => $name,
                        'size' => 'sm',
                        'color' => '#333333',
                        'weight' => 'bold',
                        'wrap' => true,
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        } else {
            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '⚠️ ไม่มีข้อมูล',
                        'size' => 'sm',
                        'color' => '#e65100',
                    ],
                ],
                'paddingStart' => 'md',
                'paddingBottom' => 'sm',
            ];
        }

        // ── Notes (if any) ──
        if ($dutyRoster && $dutyRoster->notes) {
            $bodyContents[] = ['type' => 'separator', 'color' => '#e0e0e0', 'margin' => 'sm'];
            $bodyContents[] = [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => '📝 หมายเหตุ',
                        'weight' => 'bold',
                        'size' => 'sm',
                        'color' => '#555555',
                    ],
                    [
                        'type' => 'text',
                        'text' => $dutyRoster->notes,
                        'size' => 'xs',
                        'color' => '#888888',
                        'wrap' => true,
                    ],
                ],
                'paddingTop' => 'md',
                'paddingBottom' => 'sm',
            ];
        }

        // ── Footer ──
        $bodyContents[] = ['type' => 'separator', 'color' => '#e0e0e0', 'margin' => 'md'];
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
     * สร้างข้อความเมื่อไม่มีข้อมูลเวร
     */
    protected function buildNoRosterMessage(Carbon $date): string
    {
        $thaiDate = $this->toThaiDate($date);
        $thaiDay = $this->toThaiDayOfWeek($date);

        return "🛡️ เวรยามประจำวัน\n{$thaiDay} {$thaiDate}\n\n⚠️ ยังไม่มีการบันทึกข้อมูลเวรยามประจำวันนี้\n\n🏛️ ระบบบริหารจัดการการลา รพธ.พธ.ทร.";
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

        $thaiYear = ($date->year + 543) % 100;
        return $date->day . ' ' . $thaiMonths[$date->month] . ' ' . $thaiYear;
    }

    /**
     * แปลงวันในสัปดาห์เป็นภาษาไทย
     */
    protected function toThaiDayOfWeek(Carbon $date): string
    {
        $thaiDays = [
            0 => 'วันอาทิตย์',
            1 => 'วันจันทร์',
            2 => 'วันอังคาร',
            3 => 'วันพุธ',
            4 => 'วันพฤหัสบดี',
            5 => 'วันศุกร์',
            6 => 'วันเสาร์',
        ];

        return $thaiDays[$date->dayOfWeek];
    }
}

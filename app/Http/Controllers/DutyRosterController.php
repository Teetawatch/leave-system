<?php

namespace App\Http\Controllers;

use App\Models\DutyRoster;
use App\Models\SeniorDutyRoster;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DutyRosterTemplateExport;
use App\Imports\DutyRosterImport;

class DutyRosterController extends Controller
{
    /**
     * แสดงตารางเวรรายเดือน (ทุกคนเห็นได้)
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $rosters = DutyRoster::forMonth($year, $month)
            ->with(['dutyOfficer', 'assistantDutyOfficer', 'reserveDutyOfficer', 'reserveAssistantDutyOfficer'])
            ->orderBy('duty_date')
            ->get()
            ->keyBy(function ($item) {
                return $item->duty_date->format('Y-m-d');
            });

        // ดึงข้อมูลนายทหารเวรอาวุโส (ห้วงเวลา)
        $seniorRosters = SeniorDutyRoster::forMonth($year, $month)
            ->with('seniorOfficer')
            ->orderBy('start_date')
            ->get();

        // สร้าง array ของวันทั้งหมดในเดือน
        $days = [];
        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $dateKey = $current->format('Y-m-d');
            $days[] = [
                'date' => $current->copy(),
                'roster' => $rosters->get($dateKey),
            ];
            $current->addDay();
        }

        $monthName = $this->getThaiMonth($month);
        $thaiYear = $year + 543;

        return view('duty_roster.index', compact('days', 'year', 'month', 'monthName', 'thaiYear', 'seniorRosters'));
    }

    /**
     * หน้าจัดการตารางเวร (Admin เท่านั้น)
     */
    public function manage(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $rosters = DutyRoster::forMonth($year, $month)
            ->with(['dutyOfficer', 'assistantDutyOfficer', 'reserveDutyOfficer', 'reserveAssistantDutyOfficer'])
            ->orderBy('duty_date')
            ->get()
            ->keyBy(function ($item) {
                return $item->duty_date->format('Y-m-d');
            });

        // ดึงข้อมูลนายทหารเวรอาวุโส
        $seniorRosters = SeniorDutyRoster::forMonth($year, $month)
            ->with('seniorOfficer')
            ->orderBy('start_date')
            ->get();

        // สร้าง array ของวันทั้งหมดในเดือน
        $days = [];
        $current = $startOfMonth->copy();
        while ($current->lte($endOfMonth)) {
            $dateKey = $current->format('Y-m-d');
            $days[] = [
                'date' => $current->copy(),
                'roster' => $rosters->get($dateKey),
            ];
            $current->addDay();
        }

        // ดึงรายชื่อข้าราชการ
        $users = User::whereNotNull('registration_status')
            ->where('registration_status', 'approved')
            ->orderBy('name')
            ->get();

        $monthName = $this->getThaiMonth($month);
        $thaiYear = $year + 543;

        $exemptUserIds = User::where('is_duty_exempt', true)->pluck('id')->toArray();

        return view('duty_roster.manage', compact('days', 'year', 'month', 'monthName', 'thaiYear', 'users', 'seniorRosters', 'exemptUserIds'));
    }

    /**
     * บันทึก/อัปเดตตารางเวร (Admin เท่านั้น)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'duty_date' => 'required|date',
            'duty_officer_id' => 'nullable|exists:users,id',
            'reserve_duty_officer_id' => 'nullable|exists:users,id',
            'assistant_duty_officer_id' => 'nullable|exists:users,id',
            'reserve_assistant_duty_officer_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ], [
            'duty_date.required' => 'กรุณาระบุวันที่เข้าเวร',
        ]);

        $roster = DutyRoster::updateOrCreate(
            ['duty_date' => $validated['duty_date']],
            [
                'duty_officer_id' => $validated['duty_officer_id'],
                'reserve_duty_officer_id' => $validated['reserve_duty_officer_id'] ?? null,
                'assistant_duty_officer_id' => $validated['assistant_duty_officer_id'],
                'reserve_assistant_duty_officer_id' => $validated['reserve_assistant_duty_officer_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลเวรเรียบร้อยแล้ว',
            'roster' => $roster->load(['dutyOfficer', 'assistantDutyOfficer', 'reserveDutyOfficer', 'reserveAssistantDutyOfficer']),
        ]);
    }

    /**
     * บันทึก/อัปเดตหลายวันพร้อมกัน (Bulk)
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.duty_date' => 'required|date',
            'entries.*.duty_officer_id' => 'nullable|exists:users,id',
            'entries.*.reserve_duty_officer_id' => 'nullable|exists:users,id',
            'entries.*.assistant_duty_officer_id' => 'nullable|exists:users,id',
            'entries.*.reserve_assistant_duty_officer_id' => 'nullable|exists:users,id',
            'entries.*.notes' => 'nullable|string|max:500',
        ]);

        $count = 0;
        foreach ($validated['entries'] as $entry) {
            DutyRoster::updateOrCreate(
                ['duty_date' => $entry['duty_date']],
                [
                    'duty_officer_id' => $entry['duty_officer_id'] ?? null,
                    'reserve_duty_officer_id' => $entry['reserve_duty_officer_id'] ?? null,
                    'assistant_duty_officer_id' => $entry['assistant_duty_officer_id'] ?? null,
                    'reserve_assistant_duty_officer_id' => $entry['reserve_assistant_duty_officer_id'] ?? null,
                    'notes' => $entry['notes'] ?? null,
                    'created_by' => Auth::id(),
                ]
            );
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "บันทึกข้อมูลเวร {$count} รายการเรียบร้อยแล้ว",
        ]);
    }

    /**
     * ลบข้อมูลเวร
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'duty_date' => 'required|date',
        ]);

        DutyRoster::where('duty_date', $request->duty_date)->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลเวรเรียบร้อยแล้ว',
        ]);
    }

    /**
     * ดาวน์โหลดแม่แบบ Excel (Template)
     */
    public function downloadTemplate(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        $monthName = $this->getThaiMonth($month);
        $fileName = "แม่แบบตารางเวร_{$monthName}_{$year}.xlsx";

        return Excel::download(new DutyRosterTemplateExport($year, $month), $fileName);
    }

    /**
     * นำเข้าข้อมูลตารางเวรจาก Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'กรุณาเลือกไฟล์ Excel',
            'file.mimes' => 'รองรับเฉพาะไฟล์ .xlsx, .xls, .csv เท่านั้น',
            'file.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
        ]);

        try {
            $import = new DutyRosterImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrorMessages();
            $success = $import->getSuccessCount();
            
            if (count($errors) > 0) {
                // Return success with error warning messages separated by space or comma
                $errorMsg = "นำเข้าสำเร็จ $success รายการ พบข้อผิดพลาดต่อไปนี้: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $errorMsg .= " ...และอีก " . (count($errors) - 5) . " รายการ";
                }
                
                return back()->with('success', $errorMsg);
            }

            return back()->with('success', "นำเข้าข้อมูลตารางเวรเรียบร้อยแล้ว $success รายการ");

        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดในการนำเข้าไฟล์: ' . $e->getMessage());
        }
    }

    // =====================================================
    // Senior Duty Officer (นายทหารเวรอาวุโส) - ห้วงเวลา
    // =====================================================

    /**
     * บันทึก/อัปเดตข้อมูลนายทหารเวรอาวุโส
     */
    public function storeSenior(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:senior_duty_rosters,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'senior_officer_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ], [
            'start_date.required' => 'กรุณาระบุวันเริ่มต้น',
            'end_date.required' => 'กรุณาระบุวันสิ้นสุด',
            'end_date.after_or_equal' => 'วันสิ้นสุดต้องไม่ก่อนวันเริ่มต้น',
            'senior_officer_id.required' => 'กรุณาเลือกนายทหารเวรอาวุโส',
        ]);

        if (!empty($validated['id'])) {
            $senior = SeniorDutyRoster::findOrFail($validated['id']);
            $senior->update([
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'senior_officer_id' => $validated['senior_officer_id'],
                'notes' => $validated['notes'] ?? null,
            ]);
            $message = 'แก้ไขข้อมูลนายทหารเวรอาวุโสเรียบร้อยแล้ว';
        } else {
            $senior = SeniorDutyRoster::create([
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'senior_officer_id' => $validated['senior_officer_id'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);
            $message = 'เพิ่มข้อมูลนายทหารเวรอาวุโสเรียบร้อยแล้ว';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'senior' => $senior->load('seniorOfficer'),
        ]);
    }

    /**
     * ลบข้อมูลนายทหารเวรอาวุโส
     */
    public function destroySenior($id)
    {
        SeniorDutyRoster::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลนายทหารเวรอาวุโสเรียบร้อยแล้ว',
        ]);
    }

    /**
     * API: ดึงข้อมูลเวรสำหรับเดือนที่ระบุ
     */
    public function getMonthData(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $rosters = DutyRoster::forMonth($year, $month)
            ->with(['dutyOfficer', 'assistantDutyOfficer', 'reserveDutyOfficer', 'reserveAssistantDutyOfficer'])
            ->orderBy('duty_date')
            ->get()
            ->map(function ($roster) {
                return [
                    'id' => $roster->id,
                    'duty_date' => $roster->duty_date->format('Y-m-d'),
                    'duty_officer' => $roster->dutyOfficer ? [
                        'id' => $roster->dutyOfficer->id,
                        'name' => $roster->dutyOfficer->rank . ' ' . $roster->dutyOfficer->name,
                    ] : null,
                    'reserve_duty_officer' => $roster->reserveDutyOfficer ? [
                        'id' => $roster->reserveDutyOfficer->id,
                        'name' => $roster->reserveDutyOfficer->rank . ' ' . $roster->reserveDutyOfficer->name,
                    ] : null,
                    'assistant_duty_officer' => $roster->assistantDutyOfficer ? [
                        'id' => $roster->assistantDutyOfficer->id,
                        'name' => $roster->assistantDutyOfficer->rank . ' ' . $roster->assistantDutyOfficer->name,
                    ] : null,
                    'reserve_assistant_duty_officer' => $roster->reserveAssistantDutyOfficer ? [
                        'id' => $roster->reserveAssistantDutyOfficer->id,
                        'name' => $roster->reserveAssistantDutyOfficer->rank . ' ' . $roster->reserveAssistantDutyOfficer->name,
                    ] : null,
                    'notes' => $roster->notes,
                ];
            });

        return response()->json($rosters);
    }

    /**
     * แปลงเลขเดือนเป็นชื่อเดือนภาษาไทย
     */
    private function getThaiMonth($month)
    {
        $months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม',
            4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
            7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน',
            10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
        ];

        return $months[$month] ?? '';
    }

    /**
     * จัดเวรอัตโนมัติ (Automated Scheduling)
     */
    public function autoSchedule(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = $request->year;
        $month = $request->month;

        // Get allowed ranks
        $dutyOfficerRanks = \App\Models\Rank::whereBetween('sort_order', [17, 30])->pluck('name')->toArray();
        $assistantRanks = \App\Models\Rank::whereBetween('sort_order', [31, 40])->pluck('name')->toArray();

        // Query สำหรับคัดกรองบุคคล
        $queryBase = function($query) {
            $query->where('registration_status', 'approved')
                  ->where('is_duty_exempt', false) // ตัดคนถูกยกเว้นถาวรออก
                  ->whereNotIn('rank', ['นาย', 'นาง', 'นางสาว']) // ยกเว้นคนที่มี นายกับนาง นางสาว
                  ->whereNotIn('department', ['หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69']);
        };

        $doList = User::whereIn('rank', $dutyOfficerRanks)->where($queryBase)->get();
        $adoList = User::whereIn('rank', $assistantRanks)->where($queryBase)->get();

        if ($doList->count() < 2 || $adoList->count() < 2) {
            return back()->with('error', 'มีจำนวนผู้ปฏิบัติหน้าที่ไม่เพียงพอสำหรับการจัดเวร (ต้องมีอย่างน้อย 2 คนต่อช่วงชั้นยศ)');
        }

        $doStats = [];
        foreach($doList as $user) {
            $doStats[$user->id] = ['id' => $user->id, 'count' => 0, 'holiday_count' => 0, 'last_duty_date' => null];
        }

        $adoStats = [];
        foreach($adoList as $user) {
            $adoStats[$user->id] = ['id' => $user->id, 'count' => 0, 'holiday_count' => 0, 'last_duty_date' => null];
        }

        $pickUsers = function(&$stats, $isHoliday, $dateStr, $needed = 1) {
            $keys = array_keys($stats);
            shuffle($keys);

            usort($keys, function($a, $b) use ($stats, $isHoliday) {
                $sa = $stats[$a];
                $sb = $stats[$b];

                if ($isHoliday) {
                    if ($sa['holiday_count'] !== $sb['holiday_count']) {
                        return $sa['holiday_count'] <=> $sb['holiday_count'];
                    }
                }
                if ($sa['count'] !== $sb['count']) {
                    return $sa['count'] <=> $sb['count'];
                }

                $la = $sa['last_duty_date'];
                $lb = $sb['last_duty_date'];
                if ($la !== $lb) {
                    if ($la === null) return -1;
                    if ($lb === null) return 1;
                    return $la <=> $lb;
                }
                return 0;
            });

            $picked = [];
            for ($i=0; $i<$needed; $i++) {
                $selectedId = $keys[$i];
                $picked[] = $selectedId;

                $stats[$selectedId]['count']++;
                if ($isHoliday) {
                    $stats[$selectedId]['holiday_count']++;
                }
                $stats[$selectedId]['last_duty_date'] = $dateStr;
            }

            return $picked;
        };

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            for ($d = clone $startOfMonth; $d->lte($endOfMonth); $d->addDay()) {
                $isHoliday = $d->isWeekend(); // For now, treat weekends as holidays. Can be expanded if a holidays table exists.
                $dateStr = $d->format('Y-m-d');

                $chosenDO = $pickUsers($doStats, $isHoliday, $dateStr, 1);
                $chosenADO = $pickUsers($adoStats, $isHoliday, $dateStr, 1);

                $record = DutyRoster::firstOrNew(['duty_date' => $dateStr]);
                $record->duty_officer_id = $chosenDO[0];
                $record->assistant_duty_officer_id = $chosenADO[0];
                $record->created_by = Auth::id() ?? 1;
                $record->save();
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการจัดเวร: ' . $e->getMessage());
        }

        return back()->with('success', 'จัดเวรอัตโนมัติสำหรับเดือนนี้เรียบร้อยแล้ว (ไม่รวมเวรสำรอง)');
    }

    /**
     * กำหนดเวรสำรองแบบรายเดือน
     */
    public function setMonthlyReserve(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
            'reserve_duty_officer_id' => 'nullable|exists:users,id',
            'reserve_assistant_duty_officer_id' => 'nullable|exists:users,id',
        ]);

        $year = $request->year;
        $month = $request->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            for ($d = clone $startOfMonth; $d->lte($endOfMonth); $d->addDay()) {
                $dateStr = $d->format('Y-m-d');
                $record = DutyRoster::firstOrNew(['duty_date' => $dateStr]);
                $record->reserve_duty_officer_id = $request->reserve_duty_officer_id;
                $record->reserve_assistant_duty_officer_id = $request->reserve_assistant_duty_officer_id;
                if (!$record->exists) {
                    $record->created_by = Auth::id() ?? 1;
                }
                $record->save();
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการกำหนดเวรสำรอง: ' . $e->getMessage());
        }

        return back()->with('success', 'กำหนดเวรสำรองสำหรับเดือนนี้เรียบร้อยแล้ว');
    }

    /**
     * ล้างข้อมูลเวรประจำวันทั้งหมดในเดือนนั้น (ยกเว้นนายทหารเวรอาวุโส)
     */
    public function clearMonth(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
        ]);

        DutyRoster::forMonth($request->year, $request->month)->delete();

        return back()->with('success', 'ล้างข้อมูลเวรสำหรับเดือนนี้เรียบร้อยแล้ว');
    }

    /**
     * อัปเดตรายชื่อผู้ที่ได้รับการยกเว้นตลอดไป
     */
    public function updateExemptions(Request $request)
    {
        $request->validate([
            'exempt_users' => 'nullable|array',
            'exempt_users.*' => 'exists:users,id',
        ]);

        $exemptUserIds = $request->exempt_users ?? [];

        \Illuminate\Support\Facades\DB::transaction(function () use ($exemptUserIds) {
            // Set all to false first
            User::where('is_duty_exempt', true)->update(['is_duty_exempt' => false]);
            
            // Set selected to true
            if (!empty($exemptUserIds)) {
                User::whereIn('id', $exemptUserIds)->update(['is_duty_exempt' => true]);
            }
        });

        return back()->with('success', 'บันทึกรายชื่อผู้ได้รับการยกเว้นเรียบร้อยแล้ว');
    }
}

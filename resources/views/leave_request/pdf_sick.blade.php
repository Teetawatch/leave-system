<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ใบลาป่วย แบบ ๓</title>
    <style>
        @php
            // Use storage/fonts folder (inside Laravel app folder - works on all hosting)
            $fontPath = storage_path('fonts/THSarabunNew.ttf');
            $fontPathBold = storage_path('fonts/THSarabunNew Bold.ttf');
            
            // Format paths for DomPDF (use file:// protocol and forward slashes)
            $fontPath = 'file:///' . ltrim(str_replace('\\', '/', $fontPath), '/');
            $fontPathBold = 'file:///' . ltrim(str_replace('\\', '/', $fontPathBold), '/');
        @endphp
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{!! $fontPath !!}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{!! $fontPathBold !!}") format('truetype');
        }
        @page {
            size: A4;
            margin-top: 2cm;
            margin-left: 3cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 16pt;
            line-height: 1.0; 
            margin: 0;
        }
        .header-top {
            text-align: right;
            margin-bottom: 5px;
            margin-top: -20px; /* Slight adjustment if header feels too low, but keeping 0 for now based on margin */
        }
        .form-number {
             /* ... */
        }
        /* ... */
        .indent-1 { text-indent: 2.5cm; }
        .form-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin-top: 0px;
            margin-bottom: 10px;
        }
        
        .date-section {
            width: 60%;
            margin-left: auto; 
            text-align: center; 
            margin-bottom: 15px;
        }
        .date-section div {
            margin-bottom: 3px;
        }
        .dotted {
            border-bottom: 1px dotted #000;
            display: inline-block;
            text-align: left;
            padding-left: 5px;
            color: #000;
            line-height: 0.7;
            height: 18px;
        }
        .sig-img {
            height: 30px; 
            display: inline-block;
            vertical-align: middle;
        }
        .indent-1 { text-indent: 50px; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    @php
        if (!function_exists('toThaiNum')) {
            function toThaiNum($number) {
                $arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
                $thai = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
                return str_replace($arabic, $thai, $number);
            }
        }

        $approverHead = $leaveRequest->approvals->whereIn('step', ['supervisor', 'pending_supervisor'])->first();
        $approverManager = $leaveRequest->approvals->whereIn('step', ['deputy_director', 'department_head', 'pending_deputy_director'])->first();

        // Calculate Fiscal Year Start for this request
        $date = $leaveRequest->start_date ? \Carbon\Carbon::parse($leaveRequest->start_date) : $leaveRequest->created_at;
        $year = $date->year;
        if ($date->month >= 10) {
            $fiscalYearStart = \Carbon\Carbon::create($year, 10, 1);
        } else {
            $fiscalYearStart = \Carbon\Carbon::create($year - 1, 10, 1);
        }

        // Calculate Previous Sick Leave in Fiscal Year
        $previousSickLeaves = \App\Models\LeaveRequest::where('user_id', $leaveRequest->user_id)
            ->whereHas('leaveType', function($q) {
                $q->where('slug', 'sick');
            })
            ->where('status', 'approved') // Only count approved requests
            ->where('start_date', '>=', $fiscalYearStart)
            ->where('start_date', '<', $leaveRequest->start_date) // Strictly before this one
            ->get();

        $sickLeaveCount = $previousSickLeaves->count();
        $sickLeaveDays = $previousSickLeaves->sum('total_days');

        // Calculate Previous Personal Leave in Fiscal Year
        $previousPersonalLeaves = \App\Models\LeaveRequest::where('user_id', $leaveRequest->user_id)
            ->whereHas('leaveType', function($q) {
                $q->where('slug', 'personal');
            })
            ->where('status', 'approved') // Only count approved requests
            ->where('start_date', '>=', $fiscalYearStart)
            ->where('start_date', '<', $leaveRequest->start_date) // Strictly before this one
            ->get();

        $personalLeaveCount = $previousPersonalLeaves->count();
        $personalLeaveDays = $previousPersonalLeaves->sum('total_days');

        // Calculate Latest Sick Leave (for "Same Instance" field)
        $lastSickLeave = \App\Models\LeaveRequest::where('user_id', $leaveRequest->user_id)
            ->whereHas('leaveType', function($q) {
                $q->where('slug', 'sick');
            })
            ->where('status', 'approved')
            ->where('start_date', '<', $leaveRequest->start_date)
            ->latest('start_date')
            ->first();
            
        $lastSickLeaveDays = $lastSickLeave ? $lastSickLeave->total_days : 0;
        $lastSickLeaveTimes = $lastSickLeave ? 1 : 0;
    @endphp

    <div class="header-top">
        <div class="form-number">แบบ ๓</div>
    </div>

    <div class="form-title">ใบลาป่วย</div>

    <div class="date-section">
        <div style="text-align: right;">
            เขียนที่ <span class="dotted" style="width: 150px;">{{ $leaveRequest->user->department ?? 'สำนักงาน.......' }}</span>
        </div>
        <div style="text-align: right;">
            วันที่ <span class="dotted" style="width: 40px;">{{ toThaiNum($leaveRequest->created_at->day) }}</span>
            เดือน <span class="dotted" style="width: 80px;">{{ $leaveRequest->created_at->locale('th')->monthName }}</span>
            พ.ศ. <span class="dotted" style="width: 50px;">{{ toThaiNum($leaveRequest->created_at->year + 543) }}</span>
        </div>
    </div>

    <div class="content-line">
        เรื่อง <span style="margin-right: 10px;">  ขอลาป่วย</span>
    </div>
    <div class="content-line">
        เรียน <span class="dotted" style="width: 150px;">{{ $approverHead ? ($approverHead->approver->position ?? $leaveRequest->user->supervisor->position ?? 'หัวหน้าแผนก') : ($leaveRequest->user->supervisor->position ?? 'หัวหน้าแผนก') }}</span>
    </div>

    <div style="margin-left: 1.5cm;" class="content-line">
        กระผม/ดิฉัน <span class="dotted" style="width: 170px;">{{ $leaveRequest->user->name }}</span>
        ตำแหน่ง <span class="dotted" style="width: 220px;font-size:13pt;">{{ $leaveRequest->user->position ?? '...............' }}</span>
    </div>

    <div class="content-line">
         ป่วยเป็น <span class="dotted" style="width: 180px;">{{ $leaveRequest->reason }}</span>
        จึงขออนุญาตลาป่วยเพื่อรักษาตัวมีกำหนด <span class="dotted" style="width: 80px;">{{ toThaiNum((int)$leaveRequest->total_days) }}</span> วัน ตั้งแต่วันที่ <span class="dotted" style="width: 35px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->start_date)->day) }}</span> 
       เดือน <span class="dotted" style="width: 65px;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->locale('th')->monthName }}</span> พ.ศ. <span class="dotted" style="width: 40px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->start_date)->year + 543) }}</span> ถึงวันที่ <span class="dotted" style="width: 25px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->end_date)->day) }}</span> เดือน <span class="dotted" style="width: 60px;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->locale('th')->monthName }}</span> พ.ศ. <span class="dotted" style="width: 80px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->end_date)->year + 543) }}</span>
    </div>

    <div class="content-line">
        ในระหว่างลาป่วยนี้ได้รักษาตัวอยู่ที่ บ้านเลขที่ <span class="dotted" style="width: 50px;">{{ is_array($leaveRequest->contact_address) ? ($leaveRequest->contact_address['house'] ?? '-') : '-' }}</span>
        ถนน <span class="dotted" style="width: 100px;">{{ is_array($leaveRequest->contact_address) ? ($leaveRequest->contact_address['road'] ?? '-') : '-' }}</span>
        ตำบล <span class="dotted" style="width: 100px;">{{ is_array($leaveRequest->contact_address) ? ($leaveRequest->contact_address['tambon'] ?? '-') : '-' }}</span>
        อำเภอ <span class="dotted" style="width: 100px;">{{ is_array($leaveRequest->contact_address) ? ($leaveRequest->contact_address['amphoe'] ?? '-') : '-' }}</span>
        จังหวัด <span class="dotted" style="width: 120px;">{{ is_array($leaveRequest->contact_address) ? ($leaveRequest->contact_address['province'] ?? '-') : '-' }}</span>
    </div>

    <div class="content-line" style="margin-left: 1.5cm;">
        กระผม ดิฉัน ได้ลาป่วยอยู่เดิมแล้วในคราวเดียวกันนี้ <span class="dotted" style="width: 35px;">{{ toThaiNum($lastSickLeaveTimes) }} </span> ครั้ง
        รวม <span class="dotted" style="width: 35px;">{{ toThaiNum((int)$lastSickLeaveDays) }} </span> วัน
    </div>

    <div class="signature-section">
        <div style="text-align: center; margin-left: 6.7cm; width: 8cm;">
            <div style="margin-bottom: 5px;margin-top: 5px;">ควรมิควรแล้วแต่จะกรุณา</div>
            <div style="margin-bottom: 5px;">
                (ลงชื่อ) <span class="dotted" style="width: 150px;">{{ $leaveRequest->user->name }} </span>
            </div>
        </div>
    </div>

    <!-- Divider Line -->
    <div style="border-top: 1px solid #000; margin-top: 5px; margin-bottom: 5px;"></div>

    <div class="content-line indent-1">
        ในปีงบประมาณนี้<span class="dotted" style="width: 260px;">{{ $leaveRequest->user->name }}</span>
       ได้ลาป่วย <span class="dotted" style="width: 70px;">{{ toThaiNum($sickLeaveCount) }}</span> ครั้ง
    </div>

    @php
        // Calculate logic to prevent double counting
        // DB reflects status based on 'approved'.
        $isApproved = in_array($leaveRequest->status, ['approved']);
        
        $dbUsed = $leaveBalance->used_days ?? 0;
        $dbRemaining = $leaveBalance->remaining_days ?? 0;
        $reqDays = (int)$leaveRequest->total_days;

        if ($isApproved) {
            // Database is already updated (Post-Deduction)
            // Restore 'Before' state
            $usedBefore = max(0, $dbUsed - $reqDays);
            $remainingBefore = $dbRemaining + $reqDays;
            
            // 'After' state matches DB
            $usedTotal = $dbUsed;
            $remainingTotal = $dbRemaining;
        } else {
            // Database is NOT updated (Pre-Deduction)
            // 'Before' state matches DB
            $usedBefore = $dbUsed;
            $remainingBefore = $dbRemaining;
            
            // Calculate 'After' state
            $usedTotal = $dbUsed + $reqDays;
            $remainingTotal = max(0, $dbRemaining - $reqDays);
        }
    @endphp

    <div class="content-line">
        รวม <span class="dotted" style="width: 70px;">{{ toThaiNum($sickLeaveDays) }}</span> วัน ทั้งครั้งนี้รวมเป็น <span class="dotted" style="width: 100px;">{{ toThaiNum((int)$sickLeaveDays + (int)$reqDays) }}</span>วันทำการ 
    </div>

    <div class="content-line indent-1">
        ในปีงบประมาณนี้ ผู้นี้เคยลาป่วยมาแล้ว<span class="dotted" style="width: 80px;">{{ toThaiNum($sickLeaveDays) }}</span>วัน
        รวม <span class="dotted" style="width: 80px;">{{ toThaiNum($sickLeaveCount) }}</span>ครั้ง
    </div>


        <div class="content-line indent-1">
        การลาป่วยครั้งนี้ อยู่ในอำนาจของ<span class="dotted" style="width: 200px;">{{ $approverHead ? ($approverHead->approver->position ?? $leaveRequest->user->supervisor->position ?? 'หัวหน้าแผนก') : ($leaveRequest->user->supervisor->position ?? 'หัวหน้าแผนก') }}</span>อนุญาตได้ตามข้อบังคับ
    </div>

 
    
    <br>
    <!-- Bottom Table: Stats (Left) vs Approvals (Right) -->
    <!-- Bottom Section: Stats (Left) vs Approvals (Right) using Floats -->
    <div style="width: 100%; margin-top: 10px;">
        <!-- LEFT: Stats & Checker -->
        <div style="width: 48%; float: left; padding-right: 10px;">
            <div class="stats-line indent-1">
            </div>
            <div class="checker-section" style="margin-top: 20px;">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;">
                        <div style="">
                            <div class="text-bold">- อนุญาตให้ลาป่วยได้</div>
                            <div style="margin-bottom: 5px;">
                                 @if($approverHead && $approverHead->action == 'approved')
                                    &nbsp;<span style="font-family: DejaVu Sans;">&#9745;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9744;</span> ไม่อนุญาต
                                 @elseif($approverHead && $approverHead->action == 'rejected')
                                    &nbsp;<span style="font-family: DejaVu Sans;">&#9744;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9745;</span> ไม่อนุญาต
                                 @else
                                    &nbsp;<span>&#9744;</span> อนุญาต &nbsp; <span>&#9744;</span> ไม่อนุญาต
                                 @endif
                                 @if($approverHead && $approverHead->comment)
                                    <br><span style="font-size: 13pt;">({{ $approverHead->comment }})</span>
                                 @endif
                            </div>
                            
                            <div style="text-align: center; white-space: nowrap;">
                                <span style="margin-right: 5px;">{{ $approverHead ? $approverHead->approver->rank : '' }}</span>
                                @if($approverHead && $approverHead->signature)
                                
                                    <img src="{{ 'file://' . str_replace('\\', '/', storage_path('app/public/' . $approverHead->signature)) }}" class="sig-img">
                                @else
                                    <span style="display: inline-block; margin-top: 15px;">(ลงชื่อ) ........................................</span>
                                @endif
                            </div>
                                
                                <div style="margin-top: 3px;">( {{ $approverHead ? $approverHead->approver->name : '.......................................' }} )</div>
                                <div style="margin-top: 3px;">{{ $approverHead ? ($approverHead->approver->position ?? 'หัวหน้างาน') : '.......................................' }}</div>
                                <div style="margin-top: 3px;">{{ $approverHead ? toThaiNum(\Carbon\Carbon::parse($approverHead->created_at)->addYears(543)->locale('th')->translatedFormat('d M Y')) : '....../....../......' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Approvals -->
        <div style="width: 48%; float: right; padding-left: 10px;">
            <!-- Step 1: Supervisor -->
            <!-- Step 2: Commander / Director -->
            <div style="">
                <div class="text-bold" style="margin-top:15px;">- ทราบ</div>
                <div style="margin-bottom: 5px;">
                     @if($approverManager && $approverManager->action == 'approved')
                        &nbsp;<span style="font-family: DejaVu Sans;">&#9745;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9744;</span> ไม่อนุญาต
                     @elseif($approverManager && $approverManager->action == 'rejected')
                         &nbsp;<span style="font-family: DejaVu Sans;">&#9744;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9745;</span> ไม่อนุญาต
                     @elseif($leaveRequest->status == 'approved')
                         &nbsp;<span style="font-family: DejaVu Sans;">&#9745;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9744;</span> ไม่อนุญาต
                     @else
                        &nbsp;<span>&#9744;</span> อนุญาต &nbsp; <span>&#9744;</span> ไม่อนุญาต
                     @endif
                     @if($approverManager && $approverManager->comment)
                        <br><span style="font-size: 13pt;">({{ $approverManager->comment }})</span>
                     @endif
                </div>
                
                <div style="text-align: center; white-space: nowrap;">
                     <span style="margin-right: 5px;">{{ $approverManager ? $approverManager->approver->rank : '' }}</span>
                     @if($approverManager && $approverManager->signature)
                        <img src="{{ 'file://' . str_replace('\\', '/', storage_path('app/public/' . $approverManager->signature)) }}" class="sig-img">
                     @elseif($leaveRequest->status == 'approved' && (!$approverManager)) 
                         <!-- Approved but no specific manager step record (auto or 1-step) -->
                         <!-- Leaving blank for manual sig or show empty -->
                         <span style="display: inline-block; margin-top: 15px;">(ลงชื่อ) ........................................</span>
                     @else
                        <span style="display: inline-block; margin-top: 15px;">(ลงชื่อ) ........................................</span>
                    @endif

                    <div style="margin-top: 3px;">( {{ $approverManager ? $approverManager->approver->name : '.......................................' }} )</div>
                    <div style="margin-top: 3px;">{{ $approverManager ? ($approverManager->approver->position ?? 'ผู้อำนวยการ') : '.......................................' }}</div>
                    <div style="margin-top: 3px;">{{ $approverManager ? toThaiNum(\Carbon\Carbon::parse($approverManager->created_at)->addYears(543)->locale('th')->translatedFormat('d M Y')) : '....../....../......' }}</div>
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>

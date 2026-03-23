<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ใบลาพักผ่อน (แบบ ๗)</title>
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
        $approverDirector = $leaveRequest->approvals->whereIn('step', ['director', 'pending_director'])->first();
    @endphp

    <div class="header-top">
        <div class="form-number">แบบ ๗</div>
    </div>

    <div class="form-title">ใบลาพักผ่อนประจำปี</div>

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
        เรื่อง <span style="margin-right: 10px;">ขอลาพักผ่อนประจำปี</span>
    </div>
    <div class="content-line">
        เรียน <span class="dotted" style="width: 130px;">ผอ.รร.พธ.พธ.ทร.</span>
    </div>

    <div style="margin-left: 1.5cm;" class="content-line">
        กระผม/ดิฉัน <span class="dotted" style="width: 170px;">{{ $leaveRequest->user->rank }}{{ $leaveRequest->user->name }}</span>
        ตำแหน่ง <span class="dotted" style="width: 220px;font-size:13pt;">{{ $leaveRequest->user->position ?? '...............' }}</span>
    </div>

    <div class="content-line">
        ขออนุญาตลาหยุดราชการเพื่อพักผ่อนประจำปี มีกำหนด <span class="dotted" style="width: 50px;">{{ toThaiNum((int)$leaveRequest->total_days) }}</span> วัน ตั้งแต่วันที่ <span class="dotted" style="width: 130px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->start_date)->day) }}</span> 
       เดือน <span class="dotted" style="width: 90px;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->locale('th')->monthName }}</span> พ.ศ. <span class="dotted" style="width: 60px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->start_date)->year + 543) }}</span> ถึงวันที่ <span class="dotted" style="width: 60px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->end_date)->day) }}</span> เดือน <span class="dotted" style="width: 90px;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->locale('th')->monthName }}</span> พ.ศ. <span class="dotted" style="width: 80px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->end_date)->year + 543) }}</span>
    </div>

    <div class="content-line">
        ในระหว่างลานี้ กระผม / ดิฉัน จะไปที่จังหวัด <span class="dotted" style="width: 330px;">{{ $leaveRequest->reason }}</span>
    </div>

    <div class="content-line">
        ในวันที่ <span class="dotted" style="width: 25px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->start_date)->day) }}</span>
        เดือน <span class="dotted" style="width: 70px;">{{ \Carbon\Carbon::parse($leaveRequest->start_date)->locale('th')->monthName }}</span>
        พ.ศ. <span class="dotted" style="width: 50px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->start_date)->year + 543) }}</span>
        และจะกลับในวันที่ <span class="dotted" style="width: 20px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->end_date)->day) }}</span>
        เดือน <span class="dotted" style="width: 70px;">{{ \Carbon\Carbon::parse($leaveRequest->end_date)->addDay()->locale('th')->monthName }}</span>
        พ.ศ. <span class="dotted" style="width: 30px;">{{ toThaiNum(\Carbon\Carbon::parse($leaveRequest->end_date)->addDay()->year + 543) }}</span>
    </div>

    <div class="signature-section">
        <div style="text-align: center; margin-left: 6.3cm; width: 8cm;">
            <div style="margin-bottom: 5px;">ควรมิควรแล้วแต่จะกรุณา</div>
            <div style="margin-bottom: 5px;">
                (ลงชื่อ) <span class="dotted" style="width: 180px;">  {{ $leaveRequest->user->rank }} {{ $leaveRequest->user->name }} </span>
            </div>
        </div>
    </div>

    <!-- Divider Line -->
    <div style="border-top: 1px solid #000; margin-top: 5px; margin-bottom: 5px;"></div>

    <div class="content-line indent-1">
        ในปีงบประมาณที่แล้วตั้งแต่ ๑ ต.ค.<span class="dotted" style="width: 120px;">{{ toThaiNum(now()->subYear()->year + 543) }}</span>
        ถึง ๓๐ ก.ย.<span class="dotted" style="width: 120px;">{{ toThaiNum(now()->year + 543) }}</span>
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
        ได้ลาพักผ่อนประจำปีรวม <span class="dotted" style="width: 70px;">{{ toThaiNum((int)$usedBefore) }}</span> วันทำการ เหลือวันลาพักผ่อนสะสม <span class="dotted" style="width: 100px;">{{ toThaiNum((int)$remainingBefore) }}</span>วันทำการ 
    </div>

    <div class="content-line indent-1">
        ในปีงบประมาณนี้<span class="dotted" style="width: 260px;">{{ $leaveRequest->user->rank }}{{ $leaveRequest->user->name }}</span>ได้ลาพักผ่อนประจำปีมาแล้ว
    </div>

    <div class="content-line">
         <span class="dotted" style="width: 80px;">{{ toThaiNum((int)$usedBefore) }}</span> วันทำการ
         ทั้งครั้งนี้รวมเป็น <span class="dotted" style="width: 50px;">{{ toThaiNum((int)$usedTotal) }}</span> วันทำการ 
          เหลือวันลาพักผ่อน <span class="dotted" style="width: 50px;">{{ toThaiNum((int)$remainingTotal) }}</span> วันทำการ
    </div>

 
    
    <br>
    <!-- Bottom Section: 3 Approval Boxes - Only show boxes with data -->
    @php
        $approvalCount = ($approverHead ? 1 : 0) + ($approverManager ? 1 : 0) + ($approverDirector ? 1 : 0);
        $boxWidth = $approvalCount > 0 ? (100 / $approvalCount) : 33;
    @endphp
    
    <div style="width: 100%; margin-top: 10px;">
        @if($approverHead)
        <!-- Box 1: หัวหน้าแผนก (Supervisor) -->
        <div style="width: {{ $boxWidth - 2 }}%; float: left; padding-right: 5px;">
            <div class="checker-section" style="margin-top: 20px;">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;">
                        <div style="">
                            <div class="text-bold">ความคิดเห็นหัวหน้าแผนก</div>
                            <div style="margin-bottom: 5px;">
                                 @if($approverHead->action == 'approved')
                                    &nbsp;<span style="font-family: DejaVu Sans;">&#9745;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9744;</span> ไม่อนุญาต
                                 @elseif($approverHead->action == 'rejected')
                                    &nbsp;<span style="font-family: DejaVu Sans;">&#9744;</span> อนุญาต &nbsp; <span style="font-family: DejaVu Sans;">&#9745;</span> ไม่อนุญาต
                                 @else
                                    &nbsp;<span>&#9744;</span> อนุญาต &nbsp; <span>&#9744;</span> ไม่อนุญาต
                                 @endif
                                 @if($approverHead->comment)
                                    <br><span style="font-size: 13pt;">({{ $approverHead->comment }})</span>
                                 @endif
                            </div>
                            
                            <div style="text-align: center; white-space: nowrap;">
                                <span style="margin-right: 5px;">{{ $approverHead->approver->rank ?? '' }}</span>
                                @if($approverHead->signature)
                                    <img src="{{ 'file://' . str_replace('\\', '/', storage_path('app/public/' . $approverHead->signature)) }}" class="sig-img">
                                @else
                                    <span style="display: inline-block; margin-top: 15px;">(ลงชื่อ) ........................</span>
                                @endif
                            </div>
                                
                                <div style="line-height: 1; margin: 0; padding: 0;">( {{ $approverHead->approver->name ?? '.....................' }} )</div>
                                <div style="line-height: 1; margin: 0; padding: 0; font-size: 14pt;">{{ $approverHead->approver->position ?? 'หัวหน้างาน' }}</div>
                                <div style="line-height: 1; margin: 0; padding: 0;">{{ toThaiNum(\Carbon\Carbon::parse($approverHead->created_at)->addYears(543)->locale('th')->translatedFormat('d M Y')) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($approverManager)
        <!-- Box 2: ผู้บังคับบัญชา (Manager) - รับทราบ -->
        <div style="width: {{ $boxWidth - 2 }}%; float: left; padding: 0 5px;">
            <div class="checker-section" style="margin-top: 20px;">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;">
                        <div style="">
                            <div style="text-align: center; white-space: nowrap;">
                                <span style="margin-right: 5px;">{{ $approverManager->approver->rank ?? '' }}</span>
                                @if($approverManager->signature)
                                    <img src="{{ 'file://' . str_replace('\\', '/', storage_path('app/public/' . $approverManager->signature)) }}" class="sig-img">
                                @else
                                    <span style="display: inline-block; margin-top: 15px;">(ลงชื่อ) ........................</span>
                                @endif
                            </div>
                                
                                <div style="line-height: 1; margin: 0; padding: 0;">( {{ $approverManager->approver->name ?? '.....................' }} )</div>
                                <div style="line-height: 1; margin: 0; padding: 0; font-size: 14pt;">{{ $approverManager->approver->position ?? 'ผู้บังคับบัญชา' }}</div>
                                <div style="line-height: 1; margin: 0; padding: 0;">{{ toThaiNum(\Carbon\Carbon::parse($approverManager->created_at)->addYears(543)->locale('th')->translatedFormat('d M Y')) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($approverDirector)
        <!-- Box 3: ผู้อำนวยการ (Director) - อนุมัติขั้นสุดท้าย -->
        <div style="width: {{ $boxWidth - 2 }}%; float: left; padding-left: 5px;">
            <div class="checker-section" style="margin-top: 20px;">
                <div style="text-align: center;">
                    <div style="margin-bottom: 5px;">
                        <div style="">
                            <div class="text-bold">ความเห็นผู้บังคับบัญชา</div>
                            <div style="margin-bottom: 5px;">
                                 @if($approverDirector->action == 'approved')
                                    &nbsp;<span style="font-family: DejaVu Sans;">&#9745;</span> อนุมัติ &nbsp; <span style="font-family: DejaVu Sans;">&#9744;</span> ไม่อนุมัติ
                                 @elseif($approverDirector->action == 'rejected')
                                     &nbsp;<span style="font-family: DejaVu Sans;">&#9744;</span> อนุมัติ &nbsp; <span style="font-family: DejaVu Sans;">&#9745;</span> ไม่อนุมัติ
                                 @else
                                    &nbsp;<span>&#9744;</span> อนุมัติ &nbsp; <span>&#9744;</span> ไม่อนุมัติ
                                 @endif
                                 @if($approverDirector->comment)
                                    <br><span style="font-size: 13pt;">({{ $approverDirector->comment }})</span>
                                 @endif
                            </div>
                            
                            <div style="text-align: center; white-space: nowrap;">
                                <span style="margin-right: 5px;">{{ $approverDirector->approver->rank ?? '' }}</span>
                                @if($approverDirector->signature)
                                    <img src="{{ 'file://' . str_replace('\\', '/', storage_path('app/public/' . $approverDirector->signature)) }}" class="sig-img">
                                @else
                                    <span style="display: inline-block; margin-top: 15px;">(ลงชื่อ) ........................</span>
                                @endif
                            </div>
                                
                                <div style="line-height: 1; margin: 0; padding: 0;">( {{ $approverDirector->approver->name ?? '.....................' }} )</div>
                                <div style="line-height: 1; margin: 0; padding: 0; font-size: 14pt;">{{ $approverDirector->approver->position ?? 'ผู้อำนวยการ' }}</div>
                                <div style="line-height: 1; margin: 0; padding: 0;">{{ toThaiNum(\Carbon\Carbon::parse($approverDirector->created_at)->addYears(543)->locale('th')->translatedFormat('d M Y')) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div style="clear: both;"></div>
    </div>

</body>
</html>

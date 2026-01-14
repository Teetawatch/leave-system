<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ใบขออนุญาตเปลี่ยนเวรยาม</title>
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
            line-height: 0.8; 
            margin: 0;
        }
        .header-top {
            text-align: right;
            margin-bottom: 5px;
            margin-top: -20px;
        }
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
        .content-line {
            margin-bottom: 8px;
        }
        .indent-1 { text-indent: 50px; }
        .text-bold { font-weight: bold; }
        .signature-section {
            margin-top: 6px;
        }
        .sig-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .sig-line1 {
            border-bottom: 1px dotted #000;
            display: inline-block;
            width: 330px;
            text-align: center;
        }
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
    @endphp

    <div class="form-title">ใบขออนุญาตเปลี่ยนเวรยาม</div>

    <div class="date-section">
        <div style="text-align: right;">
            เขียนที่ <span class="dotted" style="width: 150px;">{{ $guardChange->user->department ?? 'สำนักงาน.......' }}</span>
        </div>
        <div style="text-align: right;">
            วันที่ <span class="dotted" style="width: 40px;">{{ toThaiNum($guardChange->created_at->day) }}</span>
            เดือน <span class="dotted" style="width: 80px;">{{ $guardChange->created_at->locale('th')->monthName }}</span>
            พ.ศ. <span class="dotted" style="width: 50px;">{{ toThaiNum($guardChange->created_at->year + 543) }}</span>
        </div>
    </div>

    <div class="content-line">
        เรื่อง  <span style="margin-right: 10px;">ขออนุญาตเปลี่ยนเวรยาม</span>
    </div>
    <div class="content-line">
        เรียน <span class="dotted" style="width: 130px;">ผอ.รร.พธ.พธ.ทร.</span>
    </div>

    <div style="margin-left: 1.5cm;" class="content-line">
        เนื่องด้วย  กระผม<span class="dotted" style="width: 180px;">{{ $guardChange->user->rank }}{{ $guardChange->user->name }}</span>
        ตำแหน่ง<span class="dotted" style="width: 200px; font-size:14pt;">{{ $guardChange->user->position ?? '...............' }}</span>
    </div>

    <div class="content-line">
        ได้รับมอบหน้าที่<span class="dotted" style="width: 180px;">{{ $dutyPositions[$guardChange->duty_position] ?? $guardChange->duty_position }}</span>
         วันที่ <span class="dotted" style="width: 40px;">{{ toThaiNum(\Carbon\Carbon::parse($guardChange->duty_date)->day) }}</span>
        เดือน <span class="dotted" style="width: 80px;">{{ \Carbon\Carbon::parse($guardChange->duty_date)->locale('th')->monthName }}</span>
        พ.ศ. <span class="dotted" style="width: 80px;">{{ toThaiNum(\Carbon\Carbon::parse($guardChange->duty_date)->year + 543) }}</span>
    </div>

    @if($guardChange->remarks)
    <div class="content-line" style="margin-top: 10px;">
        แต่กระผมมีความจำเป็นไม่สามารถจะเข้าเวรยามในวันดังกล่าวได้ เนื่องจาก<span class="dotted" style="width: 180px;">{{ $guardChange->remarks }}</span>
    </div>
    @endif

        <div class="content-line">
        จึงขออนุญาตเปลี่ยนเวรยามกับ<span class="dotted" style="width: 240px;">{{ $guardChange->replacementUser->rank }}{{ $guardChange->replacementUser->name }}</span>
        โดยกระผมจะเข้าเวรยามแทนใน
    </div>

        <div class="content-line">
         วันที่ <span class="dotted" style="width: 40px;">{{ toThaiNum(\Carbon\Carbon::parse($guardChange->duty_date)->day) }}</span>
        เดือน <span class="dotted" style="width: 80px;">{{ \Carbon\Carbon::parse($guardChange->duty_date)->locale('th')->monthName }}</span>
        พ.ศ. <span class="dotted" style="width: 80px;">{{ toThaiNum(\Carbon\Carbon::parse($guardChange->duty_date)->year + 543) }}</span>
    </div>

    <div class="content-line" style="margin-top: 6px; margin-left:2.5cm;">
        จึงเรียนมาเพื่อโปรดพิจารณา
    </div>

    <!-- Signature: Requester -->
    <div class="signature-section">
        <div style="text-align: center; margin-left: 6.3cm; width: 8cm;">
                        <div style="margin-bottom: 30px;">
                ควรมิควรแล้วแต่จะกรุณา
            </div>
            <div style="margin-bottom: 6px;">
                {{ $guardChange->user->rank }} <span class="sig-line">{{ $guardChange->user->name }} </span>
            </div>
            <div>
                (<span class="sig-line">  {{ $guardChange->user->name }}</span> )
            </div>
            <div style="margin-top: 6px;">
                ตำแหน่ง <span style="width: auto;border-bottom: 1px dotted #000;display: inline-block;">{{ $guardChange->user->position ?? '...............' }}</span>
            </div>
        </div>
    </div>

    <!-- Divider Line -->
    <div style="border-top: 1px solid #000; margin-top: 10px; margin-bottom: 10px;"></div>

    <!-- Approval Section -->
    <div style="width: 100%;">
        <!-- LEFT: Replacement User Confirmation -->
        <div style="width: 48%; float: left; padding-right: 10px;">
            <div style="text-align: center;">
                <div class="">กระผม ไม่ขัดข้องในการเปลี่ยนเวรยามในครั้งนี้</div>
                <div style="margin-top: 15px; height: 40px;">
                    @if($guardChange->approval_signature)
                        <img src="{{ storage_path('app/public/' . $guardChange->approval_signature) }}" style="max-height: 50px; max-width: 80px; margin: 0 auto;">
                    @endif
                </div>
                <div style="margin-top: -30px;">
                    {{ $guardChange->replacementUser->rank }}<span class="sig-line"></span>
                </div>
                <div style="margin-top: 5px;">
                    (  {{ $guardChange->replacementUser->name }} )
                </div>
                <div style="margin-top: 5px;">
                    ตำแหน่ง {{ $guardChange->replacementUser->position ?? '...............' }}
                </div>
                                <div style="">
                    @if($guardChange->director_approved_at)
                        @php
                            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                            $thaiNums = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
                            $day = str_replace(range(0,9), $thaiNums, $guardChange->director_approved_at->day);
                            $month = $thaiMonths[$guardChange->director_approved_at->month];
                            $year = str_replace(range(0,9), $thaiNums, $guardChange->director_approved_at->year + 543);
                        @endphp
                        {{ $day }} {{ $month }}{{ $year }}
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT: Commander Approval (รอง ผอ.) -->
        <div style="width: 48%; float: right; padding-left: 10px;">
            <div style="text-align: center;">
                <div class="">เรียน  ผอ.รร.พธ.พธ.ทร.</div>
                <div class="">เพื่อโปรดพิจารณาอนุญาต</div>
                <div style="margin-top: 15px; height: 40px;">
                    @if($guardChange->director_signature)
                        <img src="{{ storage_path('app/public/' . $guardChange->director_signature) }}" style="max-height: 40px; max-width: 120px; margin: 0 auto;">
                    @endif
                </div>
                <div style="margin-top: -25px;">
                    @if($deputyDirector)
                        {{ $deputyDirector->rank }}<span class="sig-line"></span>
                    @else
                        (ลงชื่อ) <span class="sig-line"></span>
                    @endif
                </div>
                <div style="margin-top: 5px;">
                    @if($deputyDirector)
                        ( {{ $deputyDirector->name }} )
                    @else
                        ( ......................................... )
                    @endif
                </div>
                <div style="margin-top: 5px;">
                    ตำแหน่ง รอง ผอ.รร.พธ.พธ.ทร.
                </div>
                <div style="">
                    @if($guardChange->director_approved_at)
                        @php
                            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                            $thaiNums = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
                            $day = str_replace(range(0,9), $thaiNums, $guardChange->director_approved_at->day);
                            $month = $thaiMonths[$guardChange->director_approved_at->month];
                            $year = str_replace(range(0,9), $thaiNums, $guardChange->director_approved_at->year + 543);
                        @endphp
                        {{ $day }} {{ $month }}{{ $year }}
                    @endif
                </div>
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>


  <br>
        <!-- Approval Section -->
    <div style="width: 100%;">
        <!-- Director Final Approval (ผอ.รร.พธ.พธ.ทร.) -->
        <div style="width: 48%; float: left; padding-right: 10px;">
            <div style="text-align: center;">
                <div style="text-align: center; margin-bottom: 10px;">
                    <span style="margin-right: 15px;">
                        @if($guardChange->status === 'fully_approved')
                            <span style="border: 1px solid #000; display: inline-block; width: 14px; height: 14px; text-align: center; line-height: 12px; font-weight: bold;">/</span>
                        @else
                            <span style="border: 1px solid #000; display: inline-block; width: 14px; height: 14px;"></span>
                        @endif
                        อนุญาต
                    </span>
                    <span>
                        @if($guardChange->status === 'rejected')
                            <span style="border: 1px solid #000; display: inline-block; width: 14px; height: 14px; text-align: center; line-height: 12px; font-weight: bold;">/</span>
                        @else
                            <span style="border: 1px solid #000; display: inline-block; width: 14px; height: 14px;"></span>
                        @endif
                        ไม่อนุญาต
                    </span>
                </div>
                <div style="margin-top: 15px; height: 40px;">
                    @if($guardChange->final_signature)
                        <img src="{{ storage_path('app/public/' . $guardChange->final_signature) }}" style="max-height: 50px; max-width: 80px; margin: 0 auto;">
                    @endif
                </div>
                <div style="margin-top: -20px;">
                    @if($director)
                        {{ $director->rank }}<span class="sig-line"></span>
                    @else
                        (ลงชื่อ) <span class="sig-line"></span>
                    @endif
                </div>
                <div style="margin-top: 5px;">
                    @if($director)
                        ( {{ $director->name }} )
                    @else
                        ( ......................................... )
                    @endif
                </div>
                <div style="margin-top: 5px;">
                    ตำแหน่ง ผอ.รร.พธ.พธ.ทร.
                </div>
                <div style="">
                    @if($guardChange->director_approved_at)
                        @php
                            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                            $thaiNums = ['๐', '๑', '๒', '๓', '๔', '๕', '๖', '๗', '๘', '๙'];
                            $day = str_replace(range(0,9), $thaiNums, $guardChange->director_approved_at->day);
                            $month = $thaiMonths[$guardChange->director_approved_at->month];
                            $year = str_replace(range(0,9), $thaiNums, $guardChange->director_approved_at->year + 543);
                        @endphp
                        {{ $day }} {{ $month }}{{ $year }}
                    @endif
                </div>

            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>บัญชีรายชื่อผู้ปฏิบัติหน้าที่เวร</title>
    <style>
        @php
            $fontPath = storage_path('fonts/THSarabunNew.ttf');
            $fontPathBold = storage_path('fonts/THSarabunNew Bold.ttf');
            
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
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 16pt;
            line-height: 1.1; 
            margin: 0;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .title {
            font-size: 20pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 18pt;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        th {
            font-weight: bold;
            text-align: center;
            background-color: #f3f4f6;
        }
        .reserve-section {
            margin-top: 30px;
        }
        .reserve-line {
            margin-bottom: 10px;
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

    <div class="title">บัญชีรายชื่อผู้ปฏิบัติหน้าที่เวร</div>
    <div class="subtitle">ประจำเดือน {{ $monthName }} พ.ศ. {{ toThaiNum($thaiYear) }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">วัน/เดือน/ปี</th>
                <th style="width: 37.5%;">นายทหารเวร</th>
                <th style="width: 37.5%;">ผู้ช่วยนายทหารเวร</th>
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                @php
                    $roster = $day['roster'];
                    
                    // Thai day names
                    $dayNames = [
                        'Sunday' => 'อาทิตย์',
                        'Monday' => 'จันทร์',
                        'Tuesday' => 'อังคาร',
                        'Wednesday' => 'พุธ',
                        'Thursday' => 'พฤหัสบดี',
                        'Friday' => 'ศุกร์',
                        'Saturday' => 'เสาร์',
                    ];
                    $thaiDayName = $dayNames[$day['date']->format('l')];
                    $dateStr = "วัน" . $thaiDayName . "ที่ " . $day['date']->format('j') . " " . $monthName . " " . $thaiYear;
                    $thaiDateStr = toThaiNum($dateStr);
                @endphp
                <tr>
                    <td>{{ $thaiDateStr }}</td>
                    <td>
                        @if($roster && $roster->dutyOfficer)
                            {{ $roster->dutyOfficer->rank }}{{ $roster->dutyOfficer->name }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($roster && $roster->assistantDutyOfficer)
                            {{ $roster->assistantDutyOfficer->rank }}{{ $roster->assistantDutyOfficer->name }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="reserve-section">
        <div class="text-bold" style="margin-bottom: 10px; text-decoration: underline;">ผู้ปฏิบัติหน้าที่เวรสำรอง ประจำเดือน{{ $monthName }}</div>
        <div class="reserve-line">
            <span class="text-bold" style="display:inline-block; width: 120px;">นายทหารเวรสำรอง :</span> 
            @if($reserveDO)
                {{ $reserveDO->rank }}{{ $reserveDO->name }}
            @else
                - 
            @endif
        </div>
        <div class="reserve-line">
            <span class="text-bold" style="display:inline-block; width: 120px;">ผู้ช่วยนายทหารเวรสำรอง :</span> 
            @if($reserveADO)
                {{ $reserveADO->rank }}{{ $reserveADO->name }}
            @else
                - 
            @endif
        </div>
    </div>
</body>
</html>

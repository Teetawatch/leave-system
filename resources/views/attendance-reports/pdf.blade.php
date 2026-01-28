<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการลงเวลา</title>

    <!-- Google Fonts: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Table layout fix */
        table {
            table-layout: fixed;
        }

        table td,
        table th {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Tab styles */
        .tab-button {
            transition: all 0.2s ease;
        }

        .tab-button.active {
            background: #1e40af;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(30, 64, 175, 0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media print {
            .no-print {
                display: none;
            }

            @page {
                margin: 0.5cm;
                size: A4 portrait;
            }

            body {
                background: white;
            }

            .page-break {
                page-break-inside: avoid;
            }

            table {
                table-layout: fixed;
            }

            .tab-content {
                display: block !important;
            }

            .print-page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen p-8 text-slate-800">

    <!-- Print Controls -->
    <div class="no-print fixed top-6 right-6 flex gap-3 z-50">
        <button onclick="window.print()"
            class="bg-blue-600 text-white px-5 py-2.5 rounded-full shadow-lg hover:bg-blue-700 font-semibold flex items-center gap-2 transition-all hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            พิมพ์รายงาน
        </button>
        <a href="{{ route('attendance-reports.index') }}"
            class="bg-white text-slate-600 px-5 py-2.5 rounded-full shadow-lg hover:bg-slate-50 font-semibold transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            กลับ
        </a>
    </div>

    <!-- Filter Controls -->
    <div class="no-print max-w-[1200px] mx-auto mb-6 bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form action="{{ route('attendance-reports.pdf') }}" method="GET"
            class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="text-xs text-slate-500 block mb-1">หลักสูตร</label>
                <select name="course_id"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ทุกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500 block mb-1">วันที่</label>
                <input type="date" name="date" value="{{ $date }}"
                    class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="px-6 py-2.5 bg-slate-700 text-white rounded-lg text-sm hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-search mr-1"></i> แสดงรายงาน
            </button>
        </form>
    </div>

    <!-- Tab Controls -->
    <div class="no-print max-w-[1200px] mx-auto mb-4 flex gap-2">
        <button onclick="switchTab('student')" id="tab-student"
            class="tab-button active px-6 py-3 bg-white rounded-lg font-semibold text-slate-600 hover:bg-blue-50 flex items-center gap-2 shadow-sm border border-slate-200">
            <i class="fa-solid fa-graduation-cap"></i>
            นักเรียน
        </button>
        <button onclick="switchTab('employee')" id="tab-employee"
            class="tab-button px-6 py-3 bg-white rounded-lg font-semibold text-slate-600 hover:bg-blue-50 flex items-center gap-2 shadow-sm border border-slate-200">
            <i class="fa-solid fa-user-tie"></i>
            ข้าราชการ
        </button>
    </div>

    <!-- ==================== STUDENT REPORT ==================== -->
    <div id="content-student" class="tab-content active">
        <div
            class="max-w-[297mm] mx-auto bg-white shadow-xl rounded-none md:rounded-lg overflow-hidden print:shadow-none print:w-full print:max-w-none">

            <!-- Header -->
            <div class="bg-slate-800 text-white p-8 print:bg-slate-800 print:text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold mb-1">รายงานการลงเวลานักเรียน</h1>
                        <p class="text-slate-300 text-sm font-light">โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-400">REPORT</div>
                        <p class="text-xs text-slate-400 mt-1">พิมพ์เมื่อ:
                            {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY HH:mm') }}</p>
                    </div>
                </div>

                <!-- Report Info -->
                <div class="mt-6 flex flex-wrap gap-6 text-sm border-t border-slate-700 pt-6">
                    <div>
                        <span class="text-slate-400 block text-xs uppercase tracking-wider mb-1">วันที่</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs uppercase tracking-wider mb-1">หลักสูตร</span>
                        <span class="font-medium">{{ $courseName }}</span>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-4 border-b border-slate-200 bg-slate-50 print:bg-slate-50">
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-slate-500 uppercase font-bold tracking-wider">นักเรียนทั้งหมด</div>
                    <div class="text-2xl font-bold text-slate-700 mt-1">{{ $totalStudents }}</div>
                </div>
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-emerald-600 uppercase font-bold tracking-wider">ปกติ</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $presentCount }}</div>
                </div>
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-amber-600 uppercase font-bold tracking-wider">มาสาย</div>
                    <div class="text-2xl font-bold text-amber-600 mt-1">{{ $lateCount }}</div>
                </div>
                <div class="p-4 text-center">
                    <div class="text-xs text-rose-600 uppercase font-bold tracking-wider">ไม่มาลงชื่อ</div>
                    <div class="text-2xl font-bold text-rose-600 mt-1">{{ $absentCount }}</div>
                </div>
            </div>

            <!-- Table -->
            <div class="p-0">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr
                            class="bg-white border-b-2 border-slate-100 text-slate-500 font-semibold uppercase text-xs tracking-wider">
                            <th class="px-2 py-4 w-8 text-center">ลำดับ</th>
                            <th class="px-2 py-4 w-40">ชื่อ - นามสกุล</th>
                            <th class="px-2 py-4 w-32 min-w-[80px]">หลักสูตร</th>
                            <th class="px-2 py-4 w-28 text-center">รูปถ่าย (เช้า)</th>
                            <th class="px-2 py-4 w-16 text-center">เวลาเช้า</th>
                            <th class="px-2 py-4 w-28 text-center">รูปถ่าย (บ่าย)</th>
                            <th class="px-2 py-4 w-16 text-center">เวลาบ่าย</th>
                            <th class="px-2 py-4 w-24 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($studentLogs as $index => $row)
                            <tr class="page-break {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}">
                                <td class="px-3 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-3 py-3">
                                    <div class="font-bold text-slate-800">{{ $row['student']->first_name }}
                                        {{ $row['student']->last_name }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $row['student']->student_code ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 text-xs max-w-[120px]">
                                    <span
                                        class="bg-slate-100 px-2 py-1 rounded text-slate-500 border border-slate-200 inline-block max-w-full break-words leading-tight">
                                        {{ $row['student']->course->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['morning'] && $row['morning']->snapshot_path)
                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($row['morning']->snapshot_path) }}"
                                            class="w-16 h-16 object-cover rounded-lg border border-slate-200 mx-auto">
                                    @else
                                        <div
                                            class="w-16 h-16 bg-slate-100 rounded-lg border border-slate-200 mx-auto flex items-center justify-center text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center font-mono font-medium">
                                    @if($row['morning'])
                                        @php
                                            // Calculate late status purely for display/PDF logic (Hardcoded > 08:30)
                                            $scanTime = \Carbon\Carbon::parse($row['morning']->scan_time);
                                            $isAfter830 = $scanTime->format('H:i') > '08:30';
                                        @endphp
                                        <span class="{{ $isAfter830 ? 'text-amber-600' : 'text-slate-700' }}">
                                            {{ $scanTime->format('H:i') }}
                                        </span>
                                        @if($isAfter830)
                                            <div class="text-xs text-amber-500 font-bold">(สาย)</div>
                                        @endif
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['afternoon'] && $row['afternoon']->snapshot_path)
                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($row['afternoon']->snapshot_path) }}"
                                            class="w-16 h-16 object-cover rounded-lg border border-slate-200 mx-auto">
                                    @else
                                        <div
                                            class="w-16 h-16 bg-slate-100 rounded-lg border border-slate-200 mx-auto flex items-center justify-center text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center font-mono font-medium">
                                    @if($row['afternoon'])
                                        <span class="{{ $row['afternoon_late'] ? 'text-amber-600' : 'text-slate-700' }}">
                                            {{ \Carbon\Carbon::parse($row['afternoon']->scan_time)->format('H:i') }}
                                        </span>
                                        @if($row['afternoon_late'])
                                            <div class="text-xs text-amber-500">(สาย)</div>
                                        @endif
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['status'] == 'ปกติ')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            ปกติ
                                        </span>
                                    @elseif($row['status'] == 'มาสาย')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                            มาสาย
                                        </span>
                                    @elseif($row['status'] == 'ไม่มาลงชื่อ')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                            ไม่มาลงชื่อ
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400 italic">
                                    ไม่พบข้อมูลในช่วงเวลานี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                <tr>
                    <!-- Verifier Column -->
                    <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 0 1rem;">
                        <p style="font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 2rem;">
                            ผู้ตรวจสอบ</p>

                        <div
                            style="border-bottom: 1px dotted #94a3b8; width: 80%; margin: 0 auto; margin-bottom: 0.5rem;">
                        </div>

                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                            (.......................................................)
                        </p>
                        <p style="font-size: 0.75rem; color: #64748b;">ฝธก.รร.พธ.พธ.ทร.</p>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">วันที่
                            {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY') }}</p>
                    </td>

                    <!-- Approver Column -->
                    <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 0 1rem;">
                        <p style="font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 2rem;">ทราบ</p>

                        <div
                            style="border-bottom: 1px dotted #94a3b8; width: 80%; margin: 0 auto; margin-bottom: 0.5rem;">
                        </div>

                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                            (.......................................................)
                        </p>
                        <p style="font-size: 0.75rem; color: #64748b;">ผอ.รร.พธ.พธ.ทร.</p>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">วันที่
                            {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY') }}</p>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <div class="bg-slate-50 border-t border-slate-200 p-6 text-center text-xs text-slate-400 print:bg-white">
                <p>&copy; {{ date('Y') }} ระบบสแกนหน้าเข้างาน - โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
            </div>
        </div>
    </div>
    <!-- END STUDENT REPORT -->

    <!-- ==================== EMPLOYEE REPORT ==================== -->
    <div id="content-employee" class="tab-content print-page-break">
        <div
            class="max-w-[297mm] mx-auto bg-white shadow-xl rounded-none md:rounded-lg overflow-hidden print:shadow-none print:w-full print:max-w-none">

            <!-- Header -->
            <div class="bg-indigo-800 text-white p-8 print:bg-indigo-800 print:text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold mb-1">รายงานการลงเวลาข้าราชการ</h1>
                        <p class="text-indigo-300 text-sm font-light">โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-indigo-300">REPORT</div>
                        <p class="text-xs text-indigo-400 mt-1">พิมพ์เมื่อ:
                            {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMMM YYYY HH:mm') }}</p>
                    </div>
                </div>

                <!-- Report Info -->
                <div class="mt-6 flex flex-wrap gap-6 text-sm border-t border-indigo-700 pt-6">
                    <div>
                        <span class="text-indigo-400 block text-xs uppercase tracking-wider mb-1">วันที่</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-indigo-400 block text-xs uppercase tracking-wider mb-1">ประเภท</span>
                        <span class="font-medium">ข้าราชการทั้งหมด</span>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-5 border-b border-slate-200 bg-indigo-50 print:bg-indigo-50">
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-indigo-600 uppercase font-bold tracking-wider">ข้าราชการทั้งหมด</div>
                    <div class="text-2xl font-bold text-indigo-700 mt-1">{{ $totalEmployees }}</div>
                </div>
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-emerald-600 uppercase font-bold tracking-wider">ปกติ</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-1">{{ $employeePresentCount }}</div>
                </div>
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-amber-600 uppercase font-bold tracking-wider">มาสาย</div>
                    <div class="text-2xl font-bold text-amber-600 mt-1">{{ $employeeLateCount }}</div>
                </div>
                <div class="p-4 text-center border-r border-slate-200">
                    <div class="text-xs text-rose-600 uppercase font-bold tracking-wider">ยังไม่ลงชื่อ</div>
                    <div class="text-2xl font-bold text-rose-600 mt-1">{{ $employeeAbsentCount }}</div>
                </div>
                <div class="p-4 text-center">
                    <div class="text-xs text-blue-600 uppercase font-bold tracking-wider">ไปราชการ</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">{{ $employeeOfficialDutyCount }}</div>
                </div>
            </div>

            <!-- Table -->
            <div class="p-0">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr
                            class="bg-white border-b-2 border-indigo-100 text-indigo-600 font-semibold uppercase text-xs tracking-wider">
                            <th class="px-2 py-4 w-8 text-center">ลำดับ</th>
                            <th class="px-2 py-4 w-40">ชื่อ - นามสกุล</th>
                            <th class="px-2 py-4 w-32 min-w-[80px]">แผนก</th>
                            <th class="px-2 py-4 w-28 text-center">รูปถ่าย</th>
                            <th class="px-2 py-4 w-20 text-center">เวลาเข้า</th>
                            <th class="px-2 py-4 w-24 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($employeeLogs as $index => $row)
                            <tr class="page-break {{ $index % 2 == 0 ? 'bg-white' : 'bg-indigo-50/30' }}">
                                <td class="px-3 py-3 text-center text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                                <td class="px-3 py-3">
                                    <div class="font-bold text-slate-800">{{ $row['employee']->first_name ?? '' }}
                                        {{ $row['employee']->last_name ?? '' }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">
                                        {{ $row['employee']->employee_code ?? '-' }}</div>
                                </td>
                                <td class="px-3 py-3 text-slate-600 text-xs max-w-[120px]">
                                    <span
                                        class="bg-indigo-100 px-2 py-1 rounded text-indigo-600 border border-indigo-200 inline-block max-w-full break-words leading-tight">
                                        {{ $row['employee']->department ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @php
                                        // ใช้ข้อมูลจาก morning log (ข้าราชการสแกนแค่ตอนเช้า)
                                        $log = $row['morning'] ?? null;
                                        $snapshotPath = $log ? $log->snapshot_path : null;
                                    @endphp
                                    @if($snapshotPath)
                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($snapshotPath) }}"
                                            class="w-20 h-20 object-cover rounded-lg border border-slate-200 mx-auto">
                                    @else
                                        <div
                                            class="w-20 h-20 bg-slate-100 rounded-lg border border-slate-200 mx-auto flex items-center justify-center text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center font-mono font-medium">
                                    @if($log)
                                        <span class="{{ $row['morning_late'] ? 'text-amber-600' : 'text-slate-700' }}">
                                            {{ \Carbon\Carbon::parse($log->scan_time)->format('H:i') }}
                                        </span>
                                        @if($row['morning_late'])
                                            <div class="text-xs text-amber-500">(สาย)</div>
                                        @endif
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($row['status'] == 'ปกติ')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            ปกติ
                                        </span>
                                    @elseif($row['status'] == 'มาสาย')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                            มาสาย
                                        </span>
                                    @elseif($row['status'] == 'ไม่มาลงชื่อ')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                            ไม่มาลงชื่อ
                                        </span>
                                    @elseif($row['status'] == 'ไปราชการ')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            ไปราชการ
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                                    ไม่พบข้อมูลในช่วงเวลานี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Signatures -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 2rem;">
                <tr>
                    <!-- Verifier Column -->
                    <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 0 1rem;">
                        <p style="font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 2rem;">
                            ผู้ตรวจสอบ</p>

                        <div
                            style="border-bottom: 1px dotted #94a3b8; width: 80%; margin: 0 auto; margin-bottom: 0.5rem;">
                        </div>

                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                            (.......................................................)
                        </p>
                        <p style="font-size: 0.75rem; color: #64748b;">ฝธก.รร.พธ.พธ.ทร.</p>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">วันที่
                            {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY') }}</p>
                    </td>

                    <!-- Approver Column -->
                    <td style="width: 50%; text-align: center; vertical-align: bottom; padding: 0 1rem;">
                        <p style="font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 2rem;">ทราบ</p>

                        <div
                            style="border-bottom: 1px dotted #94a3b8; width: 80%; margin: 0 auto; margin-bottom: 0.5rem;">
                        </div>

                        <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                            (.......................................................)
                        </p>
                        <p style="font-size: 0.75rem; color: #64748b;">ผอ.รร.พธ.พธ.ทร.</p>
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">วันที่
                            {{ \Carbon\Carbon::now()->locale('th')->isoFormat('D MMM YYYY') }}</p>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <div class="bg-indigo-50 border-t border-indigo-200 p-6 text-center text-xs text-indigo-400 print:bg-white">
                <p>&copy; {{ date('Y') }} ระบบสแกนหน้าเข้างาน - โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
            </div>
        </div>
    </div>
    <!-- END EMPLOYEE REPORT -->

    <!-- JavaScript for Tab Switching -->
    <script>
        function switchTab(tabName) {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to selected tab and content
            document.getElementById('tab-' + tabName).classList.add('active');
            document.getElementById('content-' + tabName).classList.add('active');
        }
    </script>

</body>

</html>
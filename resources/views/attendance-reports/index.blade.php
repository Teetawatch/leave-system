@extends('layouts.app')

@section('title', 'รายงานการลงเวลา')

@section('content')
    <div class="min-h-screen bg-[#f8fafc] pb-20" x-data="{ activeTab: 'students' }">
        <!-- Bright Attendance Header -->
        <div class="relative bg-white pt-16 pb-32 overflow-hidden border-b border-slate-100">
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-emerald-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
            </div>

            <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                    <div class="flex items-center gap-8">
                        <div class="relative">
                            <div class="absolute inset-0 bg-indigo-500 blur-3xl opacity-10 animate-pulse"></div>
                            <div class="relative w-24 h-24 bg-white rounded-[2rem] flex items-center justify-center border border-slate-100 shadow-xl group overflow-hidden">
                                <i data-lucide="fingerprint" class="w-12 h-12 text-indigo-600 group-hover:scale-110 transition-transform duration-700"></i>
                                <div class="absolute inset-x-0 bottom-0 h-1 bg-indigo-500 animate-scan"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-3">
                                <span class="px-3 py-1 bg-indigo-500 text-white text-xs font-bold uppercase tracking-widest rounded-full shadow-lg shadow-indigo-500/20">ติดตามสถานะล่าสุด</span>
                                <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">รหัสอ้างอิง: {{ now()->format('Ymd') }}</span>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight">รายงานการลงเวลา</h1>
                            <p class="text-slate-500 mt-3 font-medium text-lg leading-relaxed">
                                การตรวจสอบและควบคุมการเข้าพื้นที่ผ่านระบบยืนยันอัตลักษณ์บุคคล (Biometric Data)
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('attendance-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}"
                            target="_blank"
                            class="px-8 py-4 bg-white border border-slate-200 text-slate-900 rounded-2xl font-bold uppercase tracking-widest text-xs flex items-center gap-3 shadow-sm hover:bg-slate-50 hover:-translate-y-1 transition-all group">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-125 transition-transform text-indigo-500"></i>
                            ส่งออกรายงาน
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20 space-y-10">
            <!-- Dashboard Intelligence Controller -->
            <div
                class="bg-white/80 backdrop-blur-xl rounded-[3rem] p-10 shadow-2xl shadow-slate-200/50 border border-white relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-[100px] -mr-48 -mt-48 opacity-50">
                </div>

                <form action="{{ route('attendance-reports.index') }}" method="GET"
                    class="relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] px-2">หลักสูตร/สังกัด</label>
                        <div class="relative">
                            <select name="course_id"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                                <option value="">ทุกหลักสูตร</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="graduation-cap"
                                class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-2">วันที่เริ่มต้น</label>
                        <div class="relative">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all">
                            <i data-lucide="calendar"
                                class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-2">วันที่สิ้นสุด</label>
                        <div class="relative">
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-8 focus:ring-indigo-500/5 transition-all">
                            <i data-lucide="calendar"
                                class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold uppercase tracking-widest text-xs shadow-xl shadow-slate-900/20 hover:bg-indigo-600 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                            <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                            ประมวลผลข้อมูล
                        </button>
                    </div>
                </form>
            </div>

            <!-- Matrix Selector Tabs -->
            <div class="flex p-2 bg-slate-100/80 backdrop-blur-md rounded-[2.5rem] border-2 border-slate-200">
                <button @click="activeTab = 'students'"
                    :class="activeTab === 'students' ? 'bg-white shadow-2xl text-indigo-600 scale-[1.01]' : 'text-slate-400 hover:text-slate-600'"
                    class="flex-1 px-8 py-6 rounded-[2rem] font-bold transition-all duration-500 flex items-center justify-center gap-4 group">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                    <div class="text-left">
                        <p class="text-xs font-bold uppercase tracking-widest leading-none mb-1 opacity-50">ข้อมูลนักเรียน</p>
                        <p class="text-xl tracking-tight">รายชื่อนักเรียน</p>
                    </div>
                    <div
                        class="ml-auto px-4 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                        {{ number_format($totalStudents) }}</div>
                </button>

                <button @click="activeTab = 'employees'"
                    :class="activeTab === 'employees' ? 'bg-white shadow-2xl text-emerald-600 scale-[1.01]' : 'text-slate-400 hover:text-slate-600'"
                    class="flex-1 px-8 py-6 rounded-[2rem] font-bold transition-all duration-500 flex items-center justify-center gap-4 group">
                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                    <div class="text-left">
                        <p class="text-xs font-bold uppercase tracking-widest leading-none mb-1 opacity-50">ข้อมูลบุคลากร</p>
                        <p class="text-xl tracking-tight">รายชื่อบุคลากร</p>
                    </div>
                    <div
                        class="ml-auto px-4 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                        {{ number_format($totalEmployees) }}</div>
                </button>
            </div>

            <!-- ==================== MATRIX: STUDENTS ==================== -->
            <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-500" class="space-y-10">
                <!-- Key Metric Cells - Students -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group">
                        <div class="flex items-center gap-5 mb-6">
                            <div
                                class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                                <i data-lucide="user-check" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">มาเรียน/ปกติ
                                </p>
                                <h3 class="text-3xl font-bold text-slate-900 mt-1">
                                    {{ number_format($uniqueStudentsCount) }}</h3>
                            </div>
                        </div>
                        <div class="h-2 bg-slate-50 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-full rounded-full transition-all duration-[2s]"
                                style="width: {{ $totalStudents > 0 ? ($uniqueStudentsCount / $totalStudents) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group">
                        <div class="flex items-center gap-5 mb-6">
                            <div
                                class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-rose-600 group-hover:text-white transition-all duration-500">
                                <i data-lucide="user-minus" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">ขาด/ไม่มา</p>
                                <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($absentCount) }}</h3>
                            </div>
                        </div>
                        <div class="h-2 bg-slate-50 rounded-full overflow-hidden">
                            <div class="bg-rose-500 h-full rounded-full transition-all duration-[2s]"
                                style="width: {{ $totalStudents > 0 ? ($absentCount / $totalStudents) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group">
                        <div class="flex items-center gap-5 mb-6">
                            <div
                                class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
                                <i data-lucide="clock" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">มาสาย</p>
                                <h3 class="text-3xl font-bold text-slate-900 mt-1">{{ number_format($lateCount) }}</h3>
                            </div>
                        </div>
                        <div class="flex gap-1 mt-4">
                            @for($i = 0; $i < 10; $i++)
                                <div
                                    class="h-2 flex-1 rounded-full {{ $i < ($lateCount > 0 ? 5 : 0) ? 'bg-amber-400' : 'bg-slate-50' }}">
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div
                        class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-200/50 relative overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20">
                        </div>
                        <div class="relative z-10 flex flex-col justify-between h-full">
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center border border-white/10 backdrop-blur-md">
                                    <i data-lucide="database" class="w-7 h-7 text-indigo-300"></i>
                                </div>
                                <span class="text-[10px] font-bold opacity-50 uppercase tracking-widest">จำนวนการสแกน</span>
                            </div>
                            <h3 class="text-3xl font-bold">{{ number_format($totalScansCount) }} <span
                                    class="text-xs font-normal opacity-50">ครั้ง</span></h3>
                        </div>
                    </div>
                </div>

                <!-- Anomaly Alerts (Late Students) -->
                @if($lateStudents->count() > 0)
                    <div x-data="{ open: false }"
                        class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-10 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-8">
                                <div
                                    class="w-16 h-16 rounded-[1.5rem] bg-amber-50 text-amber-600 flex items-center justify-center shadow-inner border border-amber-100">
                                    <i data-lucide="alert-circle" class="w-8 h-8"></i>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">ตรวจพบนักเรียนมาสาย</h3>
                                    <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mt-1">ตรวจพบทั้งหมด
                                        {{ $lateStudents->unique('student_id')->count() }} รายการ</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-10 h-10 text-slate-300 transition-transform duration-500"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="p-10 pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                                @foreach($lateStudents->unique('student_id') as $log)
                                    <div
                                        class="bg-slate-50/50 p-6 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center group hover:bg-white hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500">
                                        <div
                                            class="w-20 h-20 rounded-[2rem] overflow-hidden bg-slate-200 mb-4 ring-4 ring-white shadow-xl transition-transform group-hover:scale-110">
                                            @if($log->student->photo_path)
                                                <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i data-lucide="user" class="w-10 h-10 text-slate-400 mt-5"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $log->student->first_name }}</h4>
                                        <p
                                            class="text-[9px] font-bold text-amber-500 bg-amber-50 px-3 py-1 rounded-full mt-2 border border-amber-100">
                                            สาย @ {{ $log->scan_time->format('H:i') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Anomaly Alerts (Absent Students) -->
                @if($absentStudents->count() > 0)
                    <div x-data="{ open: false }"
                        class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mt-10">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-10 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-8">
                                <div
                                    class="w-16 h-16 rounded-[1.5rem] bg-rose-50 text-rose-600 flex items-center justify-center shadow-inner border border-rose-100">
                                    <i data-lucide="user-minus" class="w-8 h-8"></i>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">ตรวจพบนักเรียนขาด/ไม่มา</h3>
                                    <p class="text-xs font-bold text-rose-500 uppercase tracking-widest mt-1">ตรวจพบทั้งหมด
                                        {{ $absentStudents->count() }} รายการ</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-10 h-10 text-slate-300 transition-transform duration-500"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="p-10 pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                                @foreach($absentStudents as $student)
                                    <div
                                        class="bg-slate-50/50 p-6 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center group hover:bg-white hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500">
                                        <div
                                            class="w-20 h-20 rounded-[2rem] overflow-hidden bg-slate-200 mb-4 ring-4 ring-white shadow-xl transition-transform group-hover:scale-110">
                                            @if($student->photo_path)
                                                <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($student->photo_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i data-lucide="user" class="w-10 h-10 text-slate-400 mt-5"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $student->first_name }}</h4>
                                        <p
                                            class="text-[9px] font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full mt-2 border border-rose-100">
                                            ขาด/ไม่มา</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif


                <!-- Data Grid Matix - Students -->
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-10 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center">
                                <i data-lucide="database" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">บันทึกการเข้า-ออก</h3>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">วิเคราะห์ข้อมูล
                                    ({{ number_format($logs->total()) }} รายการ)</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-10 py-6 text-left">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">รายชื่อนักเรียน</span>
                                    </th>
                                    <th class="px-10 py-6 text-center">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">สังกัด/หลักสูตร</span>
                                    </th>
                                    <th class="px-10 py-6 text-center">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">เวลาสแกน</span>
                                    </th>
                                    <th class="px-10 py-6 text-center">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">สถานะ</span>
                                    </th>
                                    <th class="px-10 py-6 text-center">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">รอบเวลา</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($logs as $log)
                                    <tr class="group hover:bg-slate-50/30 transition-colors">
                                        <td class="px-10 py-6 whitespace-nowrap">
                                            <div class="flex items-center gap-5">
                                                <div
                                                    class="w-14 h-14 rounded-[1.5rem] bg-slate-100 border-4 border-white shadow-lg overflow-hidden transition-transform group-hover:scale-110">
                                                    @if($log->student->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900 uppercase">
                                                        {{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                                    <p class="text-xs font-bold text-indigo-500 mt-1">รหัส:
                                                        {{ $log->student->student_code }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span
                                                class="inline-flex px-4 py-1.5 bg-slate-900 text-white rounded-xl text-[9px] font-bold uppercase tracking-widest shadow-xl shadow-slate-900/10">
                                                {{ $log->student->course->name ?? 'ไม่ระบุหลักสูตร' }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="text-base font-bold text-slate-900 tabular-nums">{{ $log->scan_time->format('H:i:s') }}</span>
                                                <span
                                                    class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">วันที่:
                                                    {{ $log->scan_time->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            @if($log->is_late)
                                                <span
                                                    class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest">มาสาย</span>
                                            @else
                                                <span
                                                    class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest">ปกติ</span>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                             @if($log->scan_type === 'in')
                                                <span
                                                    class="inline-flex items-center gap-2 px-5 py-2 bg-indigo-600 text-white rounded-xl text-[9px] font-bold shadow-lg shadow-indigo-600/20 uppercase tracking-[0.2em]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                    ขาเข้า
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2 px-5 py-2 bg-slate-800 text-white rounded-xl text-[9px] font-bold shadow-lg shadow-slate-800/20 uppercase tracking-[0.2em]">
                                                    ขาออก
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-32 text-center">
                                            <div class="flex flex-col items-center">
                                                <div
                                                    class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-6 shadow-inner ring-8 ring-slate-50/20">
                                                    <i data-lucide="database-zap" class="w-12 h-12 text-slate-200"></i>
                                                </div>
                                                <h4 class="text-2xl font-bold text-slate-900 mb-2">ไม่พบข้อมูล</h4>
                                                <p class="text-sm font-medium text-slate-500">ไม่พบข้อมูลการลงเวลาในช่วงที่กำหนด</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($logs->hasPages())
                        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100">
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- ==================== MATRIX: EMPLOYEES ==================== -->
            <div x-show="activeTab === 'employees'" x-transition:enter="transition ease-out duration-500"
                class="space-y-10">
                <!-- Key Metric Cells - Employees -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-6">
                    <!-- Metrics are similar to students but with emerald/staff theme -->
                    <div
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group transition-all hover:-translate-y-1">
                        <i data-lucide="briefcase"
                            class="w-10 h-10 text-emerald-600 mb-6 bg-emerald-50 rounded-2xl p-2 group-hover:bg-emerald-600 group-hover:text-white transition-all"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">บุคลากรทั้งหมด</p>
                        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($totalEmployees) }}</h3>
                    </div>

                    <div
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group transition-all hover:-translate-y-1">
                        <i data-lucide="user-check"
                            class="w-10 h-10 text-emerald-600 mb-6 bg-emerald-50 rounded-2xl p-2 group-hover:bg-emerald-600 group-hover:text-white transition-all"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">มาปฏิบัติหน้าที่</p>
                        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($uniqueEmployeesCount) }}</h3>
                    </div>

                    <div
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group transition-all hover:-translate-y-1">
                        <i data-lucide="user-minus"
                            class="w-10 h-10 text-rose-600 mb-6 bg-rose-50 rounded-2xl p-2 group-hover:bg-rose-600 group-hover:text-white transition-all"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">ขาดงาน</p>
                        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($absentEmployeeCount) }}</h3>
                    </div>

                    <div
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group transition-all hover:-translate-y-1">
                        <i data-lucide="clock"
                            class="w-10 h-10 text-amber-600 mb-6 bg-amber-50 rounded-2xl p-2 group-hover:bg-amber-600 group-hover:text-white transition-all"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">มาสาย</p>
                        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($lateEmployeeCount) }}</h3>
                    </div>

                    <div
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group transition-all hover:-translate-y-1">
                        <i data-lucide="globe"
                            class="w-10 h-10 text-blue-600 mb-6 bg-blue-50 rounded-2xl p-2 group-hover:bg-blue-600 group-hover:text-white transition-all"></i>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">ไปราชการ</p>
                        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($officialDutyCount) }}</h3>
                    </div>

                    <div
                        class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-slate-200/50 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-emerald-500/10 rounded-full blur-3xl opacity-50 -mr-20 -mt-20">
                        </div>
                        <i data-lucide="fingerprint"
                            class="w-10 h-10 text-emerald-400 mb-6 bg-white/5 rounded-2xl p-2 transition-all relative z-10"></i>
                        <p class="text-[10px] font-bold opacity-40 uppercase tracking-widest relative z-10">สแกนทั้งหมด</p>
                        <h3 class="text-3xl font-bold relative z-10">{{ number_format($totalEmployeeScans) }}</h3>
                    </div>
                </div>

                <!-- Anomaly Alerts (Late Employees) -->
                @if($lateEmployees->count() > 0)
                    <div x-data="{ open: false }"
                        class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-10 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-8">
                                <div
                                    class="w-16 h-16 rounded-[1.5rem] bg-amber-50 text-amber-600 flex items-center justify-center shadow-inner border border-amber-100">
                                    <i data-lucide="alert-circle" class="w-8 h-8"></i>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">ตรวจพบข้าราชการมาสาย</h3>
                                    <p class="text-xs font-bold text-amber-500 uppercase tracking-widest mt-1">ตรวจพบทั้งหมด
                                        {{ $lateEmployees->unique('employee_id')->count() }} รายการ</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-10 h-10 text-slate-300 transition-transform duration-500"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="p-10 pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                                @foreach($lateEmployees->unique('employee_id') as $log)
                                    <div
                                        class="bg-slate-50/50 p-6 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center group hover:bg-white hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500">
                                        <div
                                            class="w-20 h-20 rounded-[2rem] overflow-hidden bg-slate-200 mb-4 ring-4 ring-white shadow-xl transition-transform group-hover:scale-110">
                                            @if($log->employee->photo_path)
                                                <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->employee->photo_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i data-lucide="user" class="w-10 h-10 text-slate-400 mt-5"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $log->employee->first_name }}</h4>
                                        <p
                                            class="text-[9px] font-bold text-amber-500 bg-amber-50 px-3 py-1 rounded-full mt-2 border border-amber-100">
                                            สาย @ {{ $log->scan_time->format('H:i') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Anomaly Alerts (Absent Employees) -->
                @if($absentEmployees->count() > 0)
                    <div x-data="{ open: false }"
                        class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mt-10">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-10 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-8">
                                <div
                                    class="w-16 h-16 rounded-[1.5rem] bg-rose-50 text-rose-600 flex items-center justify-center shadow-inner border border-rose-100">
                                    <i data-lucide="user-minus" class="w-8 h-8"></i>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">ตรวจพบข้าราชการขาด/ลา</h3>
                                    <p class="text-xs font-bold text-rose-500 uppercase tracking-widest mt-1">ตรวจพบทั้งหมด
                                        {{ $absentEmployees->count() }} รายการ</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-10 h-10 text-slate-300 transition-transform duration-500"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="p-10 pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                                @foreach($absentEmployees as $employee)
                                    <div
                                        class="bg-slate-50/50 p-6 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center group hover:bg-white hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500">
                                        <div
                                            class="w-20 h-20 rounded-[2rem] overflow-hidden bg-slate-200 mb-4 ring-4 ring-white shadow-xl transition-transform group-hover:scale-110">
                                            @if($employee->photo_path)
                                                <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($employee->photo_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i data-lucide="user" class="w-10 h-10 text-slate-400 mt-5"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $employee->first_name }}</h4>
                                        <p
                                            class="text-[9px] font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full mt-2 border border-rose-100">
                                            ขาด/ลา</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Anomaly Alerts (Official Duty Employees) -->
                @if($onOfficialDutyEmployees->count() > 0)
                    <div x-data="{ open: false }"
                        class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mt-10">
                        <button @click="open = !open"
                            class="w-full flex items-center justify-between p-10 hover:bg-slate-50 transition-all">
                            <div class="flex items-center gap-8">
                                <div
                                    class="w-16 h-16 rounded-[1.5rem] bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner border border-blue-100">
                                    <i data-lucide="globe" class="w-8 h-8"></i>
                                </div>
                                <div class="text-left">
                                    <h3 class="text-2xl font-bold text-slate-900 tracking-tight">ข้าราชการไปราชการ</h3>
                                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mt-1">จำนวนทั้งหมด
                                        {{ $onOfficialDutyEmployees->count() }} รายการ</p>
                                </div>
                            </div>
                            <i data-lucide="chevron-down" class="w-10 h-10 text-slate-300 transition-transform duration-500"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-collapse>
                            <div class="p-10 pt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                                @foreach($onOfficialDutyEmployees as $employee)
                                    <div
                                        class="bg-slate-50/50 p-6 rounded-[2.5rem] border border-slate-100 flex flex-col items-center text-center group hover:bg-white hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500">
                                        <div
                                            class="w-20 h-20 rounded-[2rem] overflow-hidden bg-slate-200 mb-4 ring-4 ring-white shadow-xl transition-transform group-hover:scale-110">
                                            @if($employee->photo_path)
                                                <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($employee->photo_path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <i data-lucide="user" class="w-10 h-10 text-slate-400 mt-5"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-900 uppercase">{{ $employee->first_name }}</h4>
                                          @if($employee->official_duty_reason)
                                            <p class="text-[9px] font-bold text-blue-500 mt-1 truncate w-full px-2" title="{{ $employee->official_duty_reason }}">
                                                {{ Str::limit($employee->official_duty_reason, 20) }}
                                            </p>
                                          @endif
                                        <p
                                            class="text-[9px] font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-full mt-2 border border-blue-100">
                                            ไปราชการ</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Employee Matrix Content (Table) -->
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="p-10 border-b border-slate-100 flex items-center justify-between bg-emerald-50/20">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight">บันทึกการเวลาบุคลากร
                                </h3>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">รายการลงเวลาปัจจุบัน</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-slate-50">
                                    <th class="px-10 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                        รายชื่อบุคลากร</th>
                                    <th
                                        class="px-10 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                        สังกัด/แผนก</th>
                                    <th
                                        class="px-10 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                        เวลาบันทึก</th>
                                    <th
                                        class="px-10 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                        สถานะ</th>
                                    <th
                                        class="px-10 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                                        รูปแบบ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($employeeLogs as $log)
                                    <tr class="group hover:bg-emerald-50/10 transition-colors">
                                        <td class="px-10 py-6 whitespace-nowrap">
                                            <div class="flex items-center gap-5">
                                                <div
                                                    class="w-14 h-14 rounded-[1.5rem] bg-slate-100 border-4 border-white shadow-lg overflow-hidden transition-transform group-hover:scale-110">
                                                    @if($log->employee->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->employee->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900 uppercase">
                                                        {{ $log->employee->first_name }} {{ $log->employee->last_name }}</p>
                                                    <p class="text-xs font-bold text-emerald-600 mt-1">
                                                        {{ $log->employee->position ?? 'ไม่ระบุตำแหน่ง' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span
                                                class="text-xs font-bold bg-slate-100 text-brand-700 rounded-xl px-4 py-1.5 uppercase tracking-widest shadow-sm">
                                                {{ $log->employee->department ?? 'ส่วนกลาง' }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="text-base font-bold text-slate-900 tabular-nums">{{ $log->scan_time->format('H:i:s') }}</span>
                                                <span
                                                    class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">วันที่:
                                                    {{ $log->scan_time->format('d/m/Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            @if($log->is_late)
                                                <span
                                                    class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest">มาสาย</span>
                                            @else
                                                <span
                                                    class="px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest">ปกติ</span>
                                            @endif
                                        </td>
                                        <td class="px-10 py-6 text-center">
                                            <span
                                                class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-600 text-white rounded-xl text-[9px] font-bold shadow-lg shadow-emerald-600/20 uppercase tracking-[0.2em]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                                บันทึกเวลา
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-32 text-center">
                                            <div class="flex flex-col items-center">
                                                <div
                                                    class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-6 shadow-inner ring-8 ring-slate-50/20">
                                                    <i data-lucide="database-zap" class="w-12 h-12 text-slate-200"></i>
                                                </div>
                                                <h4 class="text-2xl font-bold text-slate-900 mb-2">ไม่พบข้อมูล</h4>
                                                <p class="text-sm font-medium text-slate-500">ไม่พบข้อมูลการลงเวลาในช่วงที่กำหนด</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($employeeLogs->hasPages())
                        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100">
                            {{ $employeeLogs->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% {
                transform: translateY(-100%);
            }

            100% {
                transform: translateY(100%);
            }
        }

        .animate-scan {
            animation: scan 2s linear infinite;
        }
    </style>
@endsection
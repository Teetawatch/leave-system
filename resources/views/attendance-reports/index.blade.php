@extends('layouts.app')

@section('title', 'รายงานนักเรียน')

@push('styles')
<style>
    /* Animated gradient background */
    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #f5576c 75%, #4facfe 100%);
        background-size: 400% 400%;
        animation: gradientShift 8s ease infinite;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    /* Card hover animation */
    .stat-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }
    
    /* Icon pulse animation */
    .icon-pulse {
        animation: pulse 2s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    /* Glass morphism */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    /* Shimmer effect */
    .shimmer {
        position: relative;
        overflow: hidden;
    }
    
    .shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    /* Table row hover */
    .table-row-hover {
        transition: all 0.3s ease;
    }
    
    .table-row-hover:hover {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.08) 0%, rgba(168, 85, 247, 0.08) 100%);
        transform: scale(1.005);
    }
    
    /* Badge glow */
    .badge-glow {
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }
    
    /* Floating animation */
    .float-animation {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    /* Button shine effect */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    
    .btn-shine::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .btn-shine:hover::before {
        left: 100%;
    }
    
    /* Gradient text */
    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Student card hover */
    .student-card {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .student-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
    }
    
    /* Section expand animation */
    .section-expand {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('content')
<div class="space-y-8">
    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-600/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-600/10 rounded-full translate-y-1/2 -translate-x-1/2 blur-2xl"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center float-animation">
                    <i data-lucide="bar-chart-3" class="w-8 h-8 text-white"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">รายงานการเข้าเรียนนักเรียน</h2>
                    <p class="text-white/80 mt-1 flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                        ช่วงวันที่ {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('attendance-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-3 bg-white text-rose-600 px-6 py-3.5 rounded-2xl transition-all shadow-lg hover:shadow-2xl text-sm font-bold btn-shine group">
                    <i data-lucide="file-text" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 px-6 py-4 rounded-2xl border border-emerald-200 flex items-center gap-4 shadow-lg animate-pulse">
            <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center">
                <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
            </div>
            <span class="font-semibold text-lg">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-gradient-to-r from-rose-50 to-pink-50 text-rose-700 px-6 py-4 rounded-2xl border border-rose-200 flex items-center gap-4 shadow-lg">
            <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center">
                <i data-lucide="x-circle" class="w-6 h-6 text-white"></i>
            </div>
            <span class="font-semibold text-lg">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="glass-card rounded-3xl shadow-xl p-6 border border-white/50">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                <i data-lucide="filter" class="w-5 h-5 text-white"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-700">กรองข้อมูล</h3>
        </div>
        <form action="{{ route('attendance-reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="text-xs font-semibold text-slate-500 block mb-2 uppercase tracking-wider">หลักสูตร</label>
                <div class="relative">
                    <select name="course_id" class="w-full px-5 py-3.5 bg-white border-2 border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                        <option value="">🎓 ทุกหลักสูตร</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500 block mb-2 uppercase tracking-wider">วันที่เริ่ม</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="px-5 py-3.5 bg-white border-2 border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500 block mb-2 uppercase tracking-wider">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="px-5 py-3.5 bg-white border-2 border-slate-200 rounded-xl text-sm focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
            <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-bold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 btn-shine flex items-center gap-2">
                <i data-lucide="search" class="w-4 h-4"></i> ค้นหา
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Total Students -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white shimmer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">นักเรียนทั้งหมด</p>
                    <p class="text-4xl font-black mt-2">{{ number_format($totalStudents) }}</p>
                    <p class="text-blue-200 text-xs mt-1">คน</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center icon-pulse">
                    <i data-lucide="users" class="w-7 h-7"></i>
                </div>
            </div>
        </div>
        
        <!-- Present -->
        <div class="stat-card bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-xl p-6 text-white shimmer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">มาเรียน</p>
                    <p class="text-4xl font-black mt-2">{{ number_format($uniqueStudentsCount) }}</p>
                    <p class="text-emerald-200 text-xs mt-1">คน (ช่วงเวลา)</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center icon-pulse">
                    <i data-lucide="user-check" class="w-7 h-7"></i>
                </div>
            </div>
        </div>
        
        <!-- Absent -->
        <div class="stat-card bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl shadow-xl p-6 text-white shimmer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-rose-100 text-sm font-medium">ยังไม่เข้าเรียน</p>
                    <p class="text-4xl font-black mt-2">{{ number_format($absentCount) }}</p>
                    <p class="text-rose-200 text-xs mt-1">คน</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center icon-pulse">
                    <i data-lucide="user-x" class="w-7 h-7"></i>
                </div>
            </div>
        </div>
        
        <!-- Late -->
        <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white shimmer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">มาสาย</p>
                    <p class="text-4xl font-black mt-2">{{ number_format($lateCount) }}</p>
                    <p class="text-amber-200 text-xs mt-1">คน</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center icon-pulse">
                    <i data-lucide="clock" class="w-7 h-7"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Scans -->
        <div class="stat-card bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl shadow-xl p-6 text-white shimmer">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">การสแกนทั้งหมด</p>
                    <p class="text-4xl font-black mt-2">{{ number_format($totalScansCount) }}</p>
                    <p class="text-purple-200 text-xs mt-1">ครั้ง</p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center icon-pulse">
                    <i data-lucide="scan-face" class="w-7 h-7"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Late Students Section -->
    @if($lateStudents->count() > 0)
    <div class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 rounded-3xl shadow-xl border border-amber-200/50 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-8 py-5 bg-gradient-to-r from-amber-100/80 to-orange-100/80 hover:from-amber-200/80 hover:to-orange-200/80 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="clock" class="w-7 h-7 text-white"></i>
                </div>
                <div class="text-left">
                    <h3 class="text-xl font-black text-amber-800">นักเรียนที่มาสาย</h3>
                    <p class="text-sm text-amber-600 flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-amber-500 text-white text-xs font-bold rounded-full">{{ $lateStudents->unique('student_id')->count() }}</span>
                        <span>คน</span>
                    </p>
                </div>
            </div>
            <div class="w-10 h-10 bg-amber-200 rounded-xl flex items-center justify-center">
                <i data-lucide="chevron-down" class="w-5 h-5 text-amber-600 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </div>
        </button>
        <div x-show="open" x-collapse class="section-expand">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($lateStudents->unique('student_id') as $log)
                <div class="student-card bg-white rounded-2xl p-5 border-2 border-amber-200/50 flex items-center gap-4 hover:border-amber-400">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 overflow-hidden flex-shrink-0 border-2 border-amber-300 shadow-md">
                        @if($log->student->photo_path)
                            <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-amber-400">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 truncate text-base">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $log->student->student_code }}</p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 text-amber-700 rounded-lg text-xs font-semibold">
                                <i data-lucide="clock" class="w-3 h-3"></i>
                                {{ $log->scan_time->format('H:i:s') }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Absent Students Section -->
    @if($absentStudents->count() > 0)
    <div class="bg-gradient-to-br from-rose-50 via-pink-50 to-red-50 rounded-3xl shadow-xl border border-rose-200/50 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-8 py-5 bg-gradient-to-r from-rose-100/80 to-pink-100/80 hover:from-rose-200/80 hover:to-pink-200/80 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <i data-lucide="user-x" class="w-7 h-7 text-white"></i>
                </div>
                <div class="text-left">
                    <h3 class="text-xl font-black text-rose-800">นักเรียนที่ยังไม่เข้าเรียน</h3>
                    <p class="text-sm text-rose-600 flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center justify-center w-6 h-6 bg-rose-500 text-white text-xs font-bold rounded-full">{{ $absentStudents->count() }}</span>
                        <span>คน</span>
                    </p>
                </div>
            </div>
            <div class="w-10 h-10 bg-rose-200 rounded-xl flex items-center justify-center">
                <i data-lucide="chevron-down" class="w-5 h-5 text-rose-600 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </div>
        </button>
        <div x-show="open" x-collapse class="section-expand">
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($absentStudents as $student)
                <div class="student-card bg-white rounded-2xl p-5 border-2 border-rose-200/50 flex items-center gap-4 hover:border-rose-400">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-100 to-pink-100 overflow-hidden flex-shrink-0 border-2 border-rose-300 shadow-md">
                        @if($student->photo_path)
                            <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($student->photo_path) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-rose-400">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 truncate text-base">{{ $student->first_name }} {{ $student->last_name }}</p>
                        <p class="text-xs text-slate-500 font-medium">{{ $student->student_code }}</p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg text-xs font-semibold">
                                <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                                {{ $student->course->name ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/50 overflow-hidden">
        <!-- Table Header -->
        <div class="px-8 py-5 bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="table" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">ประวัติการลงเวลา</h3>
                        <p class="text-xs text-slate-500">รายละเอียดการสแกนเข้าเรียนทั้งหมด</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full text-sm font-medium text-slate-600 shadow-sm border border-slate-200">
                    <i data-lucide="clipboard-list" class="w-4 h-4 text-indigo-500"></i>
                    {{ number_format($logs->total()) }} รายการ
                </span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gradient-to-r from-slate-100 to-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="graduation-cap" class="w-4 h-4 text-indigo-500"></i>
                                นักเรียน
                            </div>
                        </th>
                        <th class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="book-open" class="w-4 h-4 text-purple-500"></i>
                                หลักสูตร
                            </div>
                        </th>
                        <th class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                                วันที่
                            </div>
                        </th>
                        <th class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="clock" class="w-4 h-4 text-green-500"></i>
                                เวลา
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <i data-lucide="tag" class="w-4 h-4 text-amber-500"></i>
                                ประเภท
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="table-row-hover">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden flex-shrink-0 border-2 border-white shadow-md">
                                    @if($log->student->photo_path)
                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400">
                                            <i data-lucide="user" class="w-5 h-5"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                    <p class="text-xs text-slate-500 font-mono">{{ $log->student->student_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 border border-indigo-200/50">
                                <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                                {{ $log->student->course->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-lucide="calendar-days" class="w-4 h-4 text-blue-600"></i>
                                </span>
                                <span class="text-slate-700 font-medium">{{ $log->scan_time->format('d/m/Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-slate-800 bg-slate-100 px-3 py-1.5 rounded-lg font-semibold">
                                {{ $log->scan_time->format('H:i:s') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl text-xs font-bold shadow-md badge-glow">
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                {{ $log->scan_type === 'in' ? 'เข้าเรียน' : 'ออก' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-200 rounded-3xl flex items-center justify-center mb-5 float-animation">
                                    <i data-lucide="clipboard-list" class="w-12 h-12 text-slate-400"></i>
                                </div>
                                <p class="font-bold text-slate-600 text-lg">ไม่พบข้อมูลการลงเวลา</p>
                                <p class="text-sm mt-2 text-slate-400">ลองเปลี่ยนช่วงเวลาหรือหลักสูตรแล้วค้นหาใหม่</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="px-8 py-5 border-t border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Reinitialize Lucide icons after page load
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
@endsection

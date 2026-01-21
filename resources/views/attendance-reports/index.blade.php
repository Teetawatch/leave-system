@extends('layouts.app')

@section('title', 'รายงานการลงเวลา')

@section('content')
<div class="max-w-[95rem] mx-auto py-8 sm:px-6 lg:px-8 space-y-8" x-data="{ activeTab: 'students' }">
    
    <!-- Hero Header -->
    <div class="relative overflow-hidden rounded-[2rem] bg-brand-900 p-8 shadow-xl border border-brand-700/50">
        <!-- Background Effects -->
        <div class="absolute inset-0 bg-gradient-to-br from-brand-600 to-slate-900 opacity-90"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-500/20 rounded-full blur-2xl -ml-20 -mb-20 pointer-events-none"></div>
        
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner">
                    <i data-lucide="scan-face" class="w-10 h-10 text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">รายงานการลงเวลา</h1>
                    <div class="flex items-center gap-3 mt-2 text-brand-200 font-medium">
                        <span class="flex items-center gap-1.5 bg-brand-800/50 px-3 py-1 rounded-lg text-sm backdrop-blur-sm border border-brand-700">
                            <i data-lucide="calendar" class="w-4 h-4 text-brand-300"></i>
                            {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                        </span>
                        <i data-lucide="arrow-right" class="w-4 h-4 opacity-50"></i>
                        <span class="flex items-center gap-1.5 bg-brand-800/50 px-3 py-1 rounded-lg text-sm backdrop-blur-sm border border-brand-700">
                            <i data-lucide="calendar" class="w-4 h-4 text-brand-300"></i>
                            {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('attendance-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}" 
                   target="_blank"
                   class="group inline-flex items-center gap-2 bg-white text-brand-700 px-6 py-3 rounded-xl font-bold shadow-lg hover:bg-brand-50 hover:scale-105 hover:shadow-xl transition-all duration-300">
                    <i data-lucide="file-down" class="w-5 h-5 transition-transform group-hover:-translate-y-0.5"></i>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="check" class="w-5 h-5"></i>
            </div>
            <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex items-center gap-3 shadow-sm">
            <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
            <span class="text-rose-800 font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search & Filters -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-50">
            <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                <i data-lucide="search" class="w-4 h-4 text-brand-600"></i>
            </div>
            <h3 class="font-bold text-slate-800">ตัวกรองข้อมูล</h3>
        </div>
        
        <form action="{{ route('attendance-reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">หลักสูตร (นักเรียน)</label>
                <div class="relative">
                    <select name="course_id" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                        <option value="">ทั้งหมด</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    <i data-lucide="graduation-cap" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">วันที่เริ่มต้น</label>
                <div class="relative">
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <i data-lucide="calendar-days" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>
            
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase">วันที่สิ้นสุด</label>
                <div class="relative">
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <i data-lucide="calendar-days" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                </div>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md shadow-brand-200 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex border-b border-slate-100">
            <button @click="activeTab = 'students'" 
                    :class="activeTab === 'students' ? 'bg-brand-50 text-brand-700 border-b-2 border-brand-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                    class="flex-1 px-6 py-4 font-bold transition-all flex items-center justify-center gap-3">
                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                <span>นักเรียน</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" 
                      :class="activeTab === 'students' ? 'bg-brand-100 text-brand-700' : 'bg-slate-100 text-slate-600'">
                    {{ number_format($totalStudents) }}
                </span>
            </button>
            <button @click="activeTab = 'employees'" 
                    :class="activeTab === 'employees' ? 'bg-indigo-50 text-indigo-700 border-b-2 border-indigo-600' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                    class="flex-1 px-6 py-4 font-bold transition-all flex items-center justify-center gap-3">
                <i data-lucide="briefcase" class="w-5 h-5"></i>
                <span>ข้าราชการ</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" 
                      :class="activeTab === 'employees' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'">
                    {{ number_format($totalEmployees) }}
                </span>
            </button>
        </div>
    </div>

    <!-- ==================== STUDENTS TAB ==================== -->
    <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
        
        <!-- Statistics Cards - Students -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Total Students -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-brand-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">นักเรียนทั้งหมด</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalStudents) }}</h3>
                        <p class="text-[10px] text-slate-400 font-medium">คนในระบบ</p>
                    </div>
                    <div class="w-12 h-12 bg-brand-100 text-brand-600 rounded-xl flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition-colors">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Present -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">มาเรียน</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($uniqueStudentsCount) }}</h3>
                        <p class="text-[10px] text-emerald-500 font-bold flex items-center gap-0.5">
                            <i data-lucide="trending-up" class="w-3 h-3"></i> เข้าเรียน
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Absent -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ไม่เข้าเรียน</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($absentCount) }}</h3>
                        <p class="text-[10px] text-rose-500 font-bold flex items-center gap-0.5">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i> ขาด/ลา
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                        <i data-lucide="user-x" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Late -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">มาสาย</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($lateCount) }}</h3>
                        <p class="text-[10px] text-amber-500 font-bold flex items-center gap-0.5">
                            <i data-lucide="clock" class="w-3 h-3"></i> เกินเวลา
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <i data-lucide="history" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Scans -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-slate-100 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">สแกนรวม</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalScansCount) }}</h3>
                        <p class="text-[10px] text-slate-400 font-medium">ครั้ง (เข้า/ออก)</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center group-hover:bg-slate-800 group-hover:text-white transition-colors">
                        <i data-lucide="fingerprint" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Students Toggle Section -->
        @if($lateStudents->count() > 0)
        <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
            <button @click="open = !open" class="w-full flex items-center justify-between p-6 bg-gradient-to-r from-amber-50 to-white hover:from-amber-100/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                        {{ $lateStudents->unique('student_id')->count() }}
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-lg text-slate-800">นักเรียนที่มาสาย</h3>
                        <p class="text-xs text-slate-500">แตะเพื่อแสดง/ซ่อน รายชื่อนักเรียนที่มาสาย</p>
                    </div>
                </div>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse>
                <div class="p-6 bg-slate-50/50 border-t border-amber-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($lateStudents->unique('student_id') as $log)
                        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow group">
                            <div class="w-12 h-12 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                @if($log->student->photo_path)
                                    <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $log->student->first_name }} {{ $log->student->last_name }}</h4>
                                <p class="text-xs text-slate-500 mb-1">{{ $log->student->student_code }}</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $log->scan_time->format('H:i:s') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Absent Students Toggle Section -->
        @if($absentStudents->count() > 0)
        <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-sm border border-rose-100 overflow-hidden">
            <button @click="open = !open" class="w-full flex items-center justify-between p-6 bg-gradient-to-r from-rose-50 to-white hover:from-rose-100/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold shadow-sm">
                        {{ $absentStudents->count() }}
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-lg text-slate-800">นักเรียนที่ยังไม่เข้าเรียน</h3>
                        <p class="text-xs text-slate-500">แตะเพื่อแสดง/ซ่อน รายชื่อนักเรียนที่ขาดเรียน</p>
                    </div>
                </div>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse>
                <div class="p-6 bg-slate-50/50 border-t border-rose-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($absentStudents as $student)
                        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow group">
                            <div class="w-12 h-12 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                @if($student->photo_path)
                                    <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($student->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $student->first_name }} {{ $student->last_name }}</h4>
                                <p class="text-xs text-slate-500 mb-1">{{ $student->student_code }}</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-600 text-[10px] font-bold border border-rose-100">
                                    <i data-lucide="graduation-cap" class="w-3 h-3"></i> {{ $student->course->name ?? '-' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Data Table - Students -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-slate-400"></i>
                    ประวัติการลงเวลานักเรียน
                </h3>
                <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full font-medium">
                    {{ number_format($logs->total()) }} รายการ
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">นักเรียน</th>
                            <th class="px-6 py-4 text-center">หลักสูตร</th>
                            <th class="px-6 py-4 text-center">วัน/เวลา</th>
                            <th class="px-6 py-4 text-center">สถานะ</th>
                            <th class="px-6 py-4 text-center">ประเภท</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm">
                                        @if($log->student->photo_path)
                                            <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i data-lucide="user" class="w-5 h-5"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm group-hover:text-brand-600 transition-colors">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                        <p class="text-xs text-slate-500 font-mono">{{ $log->student->student_code }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600">
                                    {{ $log->student->course->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold text-slate-700">{{ $log->scan_time->format('H:i:s') }}</span>
                                    <span class="text-xs text-slate-400">{{ $log->scan_time->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->is_late)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> สาย
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ปกติ
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->scan_type === 'in')
                                    <span class="font-bold text-brand-600 text-xs bg-brand-50 px-3 py-1 rounded-lg border border-brand-100">
                                        เข้าเรียน
                                    </span>
                                @else
                                    <span class="font-bold text-slate-500 text-xs bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">
                                        ออก
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="mx-auto w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <i data-lucide="search-x" class="w-8 h-8 text-slate-300"></i>
                                </div>
                                <p class="font-medium text-slate-500">ไม่พบข้อมูลการลงเวลา</p>
                                <p class="text-sm text-slate-400">ลองปรับเปลี่ยนตัวกรองค้นหาใหม่</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- ==================== EMPLOYEES TAB ==================== -->
    <div x-show="activeTab === 'employees'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
        
        <!-- Statistics Cards - Employees -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <!-- Total Employees -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ข้าราชการทั้งหมด</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalEmployees) }}</h3>
                        <p class="text-[10px] text-slate-400 font-medium">คนในระบบ</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Present Employees -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">มาทำงาน</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($uniqueEmployeesCount) }}</h3>
                        <p class="text-[10px] text-emerald-500 font-bold flex items-center gap-0.5">
                            <i data-lucide="trending-up" class="w-3 h-3"></i> ลงเวลาแล้ว
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <i data-lucide="user-check" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Absent Employees -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ยังไม่ลงเวลา</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($absentEmployeeCount) }}</h3>
                        <p class="text-[10px] text-rose-500 font-bold flex items-center gap-0.5">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i> ขาด/ลา
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                        <i data-lucide="user-x" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Late Employees -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">มาสาย</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($lateEmployeeCount) }}</h3>
                        <p class="text-[10px] text-amber-500 font-bold flex items-center gap-0.5">
                            <i data-lucide="clock" class="w-3 h-3"></i> เกินเวลา
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <i data-lucide="history" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
            
            <!-- Total Employee Scans -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                <div class="absolute top-0 right-0 w-24 h-24 bg-slate-100 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">สแกนรวม</p>
                        <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalEmployeeScans) }}</h3>
                        <p class="text-[10px] text-slate-400 font-medium">ครั้ง (เข้า/ออก)</p>
                    </div>
                    <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center group-hover:bg-slate-800 group-hover:text-white transition-colors">
                        <i data-lucide="fingerprint" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Late Employees Toggle Section -->
        @if($lateEmployees->count() > 0)
        <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-sm border border-amber-100 overflow-hidden">
            <button @click="open = !open" class="w-full flex items-center justify-between p-6 bg-gradient-to-r from-amber-50 to-white hover:from-amber-100/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                        {{ $lateEmployees->unique('employee_id')->count() }}
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-lg text-slate-800">ข้าราชการที่มาสาย</h3>
                        <p class="text-xs text-slate-500">แตะเพื่อแสดง/ซ่อน รายชื่อข้าราชการที่มาสาย</p>
                    </div>
                </div>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse>
                <div class="p-6 bg-slate-50/50 border-t border-amber-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($lateEmployees->unique('employee_id') as $log)
                        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow group">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                @if($log->employee->photo_path)
                                    <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->employee->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-indigo-400">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $log->employee->first_name }} {{ $log->employee->last_name }}</h4>
                                <p class="text-xs text-slate-500 mb-1">{{ $log->employee->employee_code ?? $log->employee->position ?? '-' }}</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">
                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $log->scan_time->format('H:i:s') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Absent Employees Toggle Section -->
        @if($absentEmployees->count() > 0)
        <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-sm border border-rose-100 overflow-hidden">
            <button @click="open = !open" class="w-full flex items-center justify-between p-6 bg-gradient-to-r from-rose-50 to-white hover:from-rose-100/50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold shadow-sm">
                        {{ $absentEmployees->count() }}
                    </div>
                    <div class="text-left">
                        <h3 class="font-bold text-lg text-slate-800">ข้าราชการที่ยังไม่ลงเวลา</h3>
                        <p class="text-xs text-slate-500">แตะเพื่อแสดง/ซ่อน รายชื่อข้าราชการที่ยังไม่มา</p>
                    </div>
                </div>
                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <div x-show="open" x-collapse>
                <div class="p-6 bg-slate-50/50 border-t border-rose-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($absentEmployees as $employee)
                        <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow group">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm group-hover:scale-105 transition-transform">
                                @if($employee->photo_path)
                                    <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($employee->photo_path) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-indigo-400">
                                        <i data-lucide="user" class="w-6 h-6"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                                <p class="text-xs text-slate-500 mb-1">{{ $employee->employee_code ?? '-' }}</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-600 text-[10px] font-bold border border-indigo-100">
                                    <i data-lucide="briefcase" class="w-3 h-3"></i> {{ $employee->position ?? $employee->department ?? 'ข้าราชการ' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Data Table - Employees -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-slate-400"></i>
                    ประวัติการลงเวลาข้าราชการ
                </h3>
                <span class="text-xs text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full font-medium">
                    {{ number_format($employeeLogs->total()) }} รายการ
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">ข้าราชการ</th>
                            <th class="px-6 py-4 text-center">ตำแหน่ง/หน่วยงาน</th>
                            <th class="px-6 py-4 text-center">วัน/เวลา</th>
                            <th class="px-6 py-4 text-center">สถานะ</th>
                            <th class="px-6 py-4 text-center">ประเภท</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($employeeLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 overflow-hidden flex-shrink-0 border-2 border-white shadow-sm">
                                        @if($log->employee->photo_path)
                                            <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->employee->photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-indigo-400">
                                                <i data-lucide="user" class="w-5 h-5"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">{{ $log->employee->first_name }} {{ $log->employee->last_name }}</p>
                                        <p class="text-xs text-slate-500 font-mono">{{ $log->employee->employee_code ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-600">
                                    {{ $log->employee->position ?? $log->employee->department ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold text-slate-700">{{ $log->scan_time->format('H:i:s') }}</span>
                                    <span class="text-xs text-slate-400">{{ $log->scan_time->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->is_late)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> สาย
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ปกติ
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($log->scan_type === 'in')
                                    <span class="font-bold text-indigo-600 text-xs bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">
                                        เข้างาน
                                    </span>
                                @else
                                    <span class="font-bold text-slate-500 text-xs bg-slate-100 px-3 py-1 rounded-lg border border-slate-200">
                                        ออก
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="mx-auto w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <i data-lucide="search-x" class="w-8 h-8 text-slate-300"></i>
                                </div>
                                <p class="font-medium text-slate-500">ไม่พบข้อมูลการลงเวลา</p>
                                <p class="text-sm text-slate-400">ลองปรับเปลี่ยนตัวกรองค้นหาใหม่</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($employeeLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $employeeLogs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

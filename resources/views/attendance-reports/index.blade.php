@extends('layouts.app')

@section('title', 'รายงานการลงเวลา')

@section('content')
    <div class="max-w-[95rem] mx-auto py-8 sm:px-6 lg:px-8 space-y-8" x-data="{ activeTab: 'students' }">

        <!-- Hero Header -->
        <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 shadow-2xl border border-white/10 group">
            <!-- Dynamic Background Effects -->
            <div class="absolute inset-0 bg-gradient-to-br from-brand-600/40 via-brand-900 to-slate-950"></div>
            <div
                class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-500/10 rounded-full blur-[120px] -mr-48 -mt-48 animate-pulse">
            </div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-[100px] -ml-20 -mb-20"></div>

            <div class="relative p-8 md:p-12 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="flex items-center gap-8">
                    <div class="relative">
                        <div class="absolute inset-0 bg-brand-500 blur-2xl opacity-20 animate-pulse"></div>
                        <div
                            class="relative w-24 h-24 bg-white/5 backdrop-blur-2xl rounded-3xl flex items-center justify-center border border-white/20 shadow-2xl">
                            <i data-lucide="scan" class="w-12 h-12 text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
                            รายงานการลงเวลา
                        </h1>
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            <div
                                class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10">
                                <i data-lucide="calendar" class="w-4 h-4 text-brand-300"></i>
                                <span class="text-sm font-bold text-white uppercase tracking-wider">
                                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="w-6 h-px bg-white/20"></div>
                            <div
                                class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-2xl border border-white/10">
                                <i data-lucide="calendar" class="w-4 h-4 text-brand-300"></i>
                                <span class="text-sm font-bold text-white uppercase tracking-wider">
                                    {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('attendance-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}"
                        target="_blank"
                        class="group flex items-center gap-3 bg-white text-slate-900 px-8 py-4 rounded-2xl font-black shadow-[0_20px_40px_-15px_rgba(255,255,255,0.3)] hover:bg-brand-50 hover:scale-105 active:scale-95 transition-all duration-300">
                        <i data-lucide="file-output" class="w-5 h-5 group-hover:animate-bounce"></i>
                        <span>ส่งออกรายงาน</span>
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
        <div
            class="bg-white/70 backdrop-blur-xl rounded-[2rem] p-8 shadow-sm border border-slate-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full blur-3xl -mr-32 -mt-32 opacity-50"></div>

            <div class="relative">
                <div class="flex items-center gap-3 mb-8">
                    <div
                        class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center shadow-lg shadow-brand-200">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 uppercase tracking-wider">ตัวกรองข้อมูล</h3>
                        <p class="text-xs text-slate-400 font-bold">ระบุเงื่อนไขเพื่อดูข้อมูลที่ต้องการ</p>
                    </div>
                </div>

                <form action="{{ route('attendance-reports.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="space-y-2.5">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">หลักสูตร
                            (นักเรียน)</label>
                        <div class="relative group">
                            <select name="course_id"
                                class="w-full pl-11 pr-4 py-4 bg-slate-50/50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                                <option value="">หลักสูตรทั้งหมด</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="graduation-cap"
                                class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                            <i data-lucide="chevron-down"
                                class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none transition-transform group-focus-within:rotate-180"></i>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <label
                            class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">วันที่เริ่มต้น</label>
                        <div class="relative group">
                            <input type="date" name="start_date" value="{{ $startDate }}"
                                class="w-full pl-11 pr-4 py-4 bg-slate-50/50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                            <i data-lucide="calendar-range"
                                class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        <label
                            class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1">วันที่สิ้นสุด</label>
                        <div class="relative group">
                            <input type="date" name="end_date" value="{{ $endDate }}"
                                class="w-full pl-11 pr-4 py-4 bg-slate-50/50 border-2 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                            <i data-lucide="calendar-range"
                                class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-brand-500 transition-colors"></i>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="w-full py-4 bg-slate-900 hover:bg-brand-600 text-white font-black rounded-2xl shadow-xl shadow-slate-200 hover:shadow-brand-200 active:scale-95 transition-all flex items-center justify-center gap-3">
                            <i data-lucide="search" class="w-5 h-5"></i>
                            <span>อัปเดตข้อมูล</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div
            class="flex flex-col sm:flex-row p-1.5 bg-slate-200/50 backdrop-blur-md rounded-[2rem] border border-slate-200 gap-1.5">
            <button @click="activeTab = 'students'"
                :class="activeTab === 'students' ? 'bg-white text-brand-600 shadow-xl' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'"
                class="flex-1 px-8 py-5 rounded-[1.5rem] font-black transition-all duration-300 flex items-center justify-center gap-4 group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                    :class="activeTab === 'students' ? 'bg-brand-100' : 'bg-slate-100 group-hover:bg-brand-50'">
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold uppercase tracking-widest leading-none mb-1 opacity-60">มุมมอง</p>
                    <p class="text-lg">รายงานนักเรียน</p>
                </div>
                <span class="ml-auto px-3 py-1 rounded-xl text-xs font-black shadow-inner"
                    :class="activeTab === 'students' ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-500'">
                    {{ number_format($totalStudents) }}
                </span>
            </button>

            <button @click="activeTab = 'employees'"
                :class="activeTab === 'employees' ? 'bg-white text-indigo-600 shadow-xl' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'"
                class="flex-1 px-8 py-5 rounded-[1.5rem] font-black transition-all duration-300 flex items-center justify-center gap-4 group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                    :class="activeTab === 'employees' ? 'bg-indigo-100' : 'bg-slate-100 group-hover:bg-indigo-50'">
                    <i data-lucide="briefcase" class="w-5 h-5"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold uppercase tracking-widest leading-none mb-1 opacity-60">มุมมอง</p>
                    <p class="text-lg">รายงานข้าราชการ</p>
                </div>
                <span class="ml-auto px-3 py-1 rounded-xl text-xs font-black shadow-inner"
                    :class="activeTab === 'employees' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-500'">
                    {{ number_format($totalEmployees) }}
                </span>
            </button>
        </div>

        <!-- ==================== STUDENTS TAB ==================== -->
        <div x-show="activeTab === 'students'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-8">

            <!-- Statistics Cards - Students -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Total Students -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-brand-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-brand-50 text-brand-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="users" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">นักเรียนทั้งหมด</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalStudents) }}</h3>
                            <span class="text-xs font-bold text-slate-400">คน</span>
                        </div>
                    </div>
                </div>

                <!-- Present -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="user-check" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">มาเรียน</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($uniqueStudentsCount) }}</h3>
                            <div
                                class="flex items-center text-emerald-500 text-[10px] font-black bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
                                {{ $totalStudents > 0 ? round(($uniqueStudentsCount / $totalStudents) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Absent -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-rose-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="user-x" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">ยังไม่เข้าเรียน</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($absentCount) }}</h3>
                            <div
                                class="flex items-center text-rose-500 text-[10px] font-black bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100">
                                {{ $totalStudents > 0 ? round(($absentCount / $totalStudents) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-amber-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="clock" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">มาสาย</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($lateCount) }}</h3>
                            <span class="text-xs font-bold text-slate-400">คน</span>
                        </div>
                    </div>
                </div>

                <!-- Total Scans -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-indigo-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="fingerprint" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">สแกนรวม</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalScansCount) }}</h3>
                            <span class="text-xs font-bold text-slate-400">ครั้ง</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Students Toggle Section -->
            @if($lateStudents->count() > 0)
                <div x-data="{ open: false }"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-8 bg-slate-50/50 hover:bg-white transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-amber-500 blur-xl opacity-20 animate-pulse"></div>
                                <div
                                    class="relative w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-black text-xl shadow-inner border border-amber-200">
                                    {{ $lateStudents->unique('student_id')->count() }}
                                </div>
                            </div>
                            <div class="text-left">
                                <h3 class="font-black text-xl text-slate-800 tracking-tight">นักเรียนที่มาสายวันนี้</h3>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    รายชื่อนักเรียนมาสาย</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 transition-all duration-300 group-hover:border-amber-200 group-hover:text-amber-500 shadow-sm"
                            :class="{ 'rotate-180 bg-amber-50 border-amber-200 text-amber-600': open }">
                            <i data-lucide="chevron-down" class="w-6 h-6"></i>
                        </div>
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="p-8 pt-0 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($lateStudents->unique('student_id') as $log)
                                    <div class="relative group/item">
                                        <div
                                            class="bg-slate-50 rounded-3xl p-4 border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1 flex items-center gap-4">
                                            <div class="relative w-14 h-14 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-amber-500 rounded-2xl blur-lg opacity-0 group-hover/item:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm">
                                                    @if($log->student->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-slate-800 text-sm truncate uppercase tracking-tight">
                                                    {{ $log->student->first_name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 mb-1">
                                                    {{ $log->student->student_code }}</p>
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black border border-amber-100">
                                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $log->scan_time->format('H:i') }}
                                                </span>
                                            </div>
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
                <div x-data="{ open: false }"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-8 bg-slate-50/50 hover:bg-white transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-rose-500 blur-xl opacity-20 animate-pulse"></div>
                                <div
                                    class="relative w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-black text-xl shadow-inner border border-rose-200">
                                    {{ $absentStudents->count() }}
                                </div>
                            </div>
                            <div class="text-left">
                                <h3 class="font-black text-xl text-slate-800 tracking-tight">นักเรียนที่ยังไม่มาเรียน</h3>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    รายชื่อนักเรียนที่ยังไม่เช็คชื่อ</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 transition-all duration-300 group-hover:border-rose-200 group-hover:text-rose-500 shadow-sm"
                            :class="{ 'rotate-180 bg-rose-50 border-rose-200 text-rose-600': open }">
                            <i data-lucide="chevron-down" class="w-6 h-6"></i>
                        </div>
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="p-8 pt-0 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($absentStudents as $student)
                                    <div class="relative group/item">
                                        <div
                                            class="bg-slate-50 rounded-3xl p-4 border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-xl hover:shadow-rose-500/10 hover:-translate-y-1 flex items-center gap-4">
                                            <div class="relative w-14 h-14 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-rose-500 rounded-2xl blur-lg opacity-0 group-hover/item:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm">
                                                    @if($student->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($student->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-slate-800 text-sm truncate uppercase tracking-tight">
                                                    {{ $student->first_name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 mb-1 line-clamp-1">
                                                    {{ $student->course->name ?? '-' }}</p>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-black border border-rose-100">
                                                    ยังไม่มา
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Data Table - Students -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div
                    class="px-8 py-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-lg">
                            <i data-lucide="history" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-slate-800 tracking-tight">ประวัติการลงเวลานักเรียน</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                ประวัติการเข้าเรียนรายบุคคล</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 px-4 py-2 rounded-2xl border border-slate-200">
                        <span
                            class="text-xs font-black text-slate-600 uppercase tracking-widest">{{ number_format($logs->total()) }}
                            รายการ</span>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead
                            class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5">ข้อมูลนักเรียน</th>
                                <th class="px-8 py-5 text-center">หลักสูตร/ห้องเรียน</th>
                                <th class="px-8 py-5 text-center">เวลาสแกน</th>
                                <th class="px-8 py-5 text-center">สถานะ</th>
                                <th class="px-8 py-5 text-center">ประเภท</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/80 transition-all duration-300 group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="relative w-12 h-12 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-brand-500 rounded-2xl blur-lg opacity-0 group-hover:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100">
                                                    @if($log->student->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->student->photo_path) }}"
                                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <p
                                                    class="font-black text-slate-800 text-sm group-hover:text-brand-600 transition-colors uppercase tracking-tight">
                                                    {{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md border border-slate-200">{{ $log->student->student_code }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center relative">
                                        <span
                                            class="relative z-10 inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-wider">
                                            {{ $log->student->course->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="font-black text-slate-900 text-lg tabular-nums tracking-tight">{{ $log->scan_time->format('H:i:s') }}</span>
                                            <div
                                                class="flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 mt-1">
                                                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $log->scan_time->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @if($log->is_late)
                                            <div class="inline-flex flex-col items-center">
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-100 shadow-sm uppercase tracking-widest">
                                                    สาย
                                                </span>
                                            </div>
                                        @else
                                            <div class="inline-flex flex-col items-center">
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm uppercase tracking-widest">
                                                    ปกติ
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @if($log->scan_type === 'in')
                                            <div class="flex items-center justify-center">
                                                <div
                                                    class="px-4 py-1.5 rounded-xl bg-brand-600 text-white text-[10px] font-black shadow-lg shadow-brand-200 uppercase tracking-widest flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                                    เข้าเรียน
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center">
                                                <div
                                                    class="px-4 py-1.5 rounded-xl bg-slate-800 text-white text-[10px] font-black shadow-lg shadow-slate-200 uppercase tracking-widest flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                                    ออกเรียน
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div
                                            class="relative w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner overflow-hidden">
                                            <div class="absolute inset-0 bg-slate-200/50 animate-pulse"></div>
                                            <i data-lucide="ghost" class="relative w-10 h-10 text-slate-300"></i>
                                        </div>
                                        <h4 class="font-black text-slate-800 uppercase tracking-tight">ไม่พบข้อมูลการลงเวลา</h4>
                                        <p class="text-sm text-slate-400 mt-2 font-bold opacity-60">
                                            ลองปรับเปลี่ยนตัวกรองค้นหาเพื่อดูข้อมูลที่ต้องการ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ==================== EMPLOYEES TAB ==================== -->
        <div x-show="activeTab === 'employees'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="space-y-8">

            <!-- Statistics Cards - Employees -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
                <!-- Total Employees -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-indigo-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="briefcase" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">ข้าราชการทั้งหมด</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalEmployees) }}</h3>
                            <span class="text-xs font-bold text-slate-400">คน</span>
                        </div>
                    </div>
                </div>

                <!-- Present Employees -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-emerald-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="user-check" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">มาทำงาน</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($uniqueEmployeesCount) }}</h3>
                            <div
                                class="flex items-center text-emerald-500 text-[10px] font-black bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
                                {{ $totalEmployees > 0 ? round(($uniqueEmployeesCount / $totalEmployees) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Absent Employees -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-rose-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="user-x" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">ยังไม่ลงเวลา</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($absentEmployeeCount) }}</h3>
                            <div
                                class="flex items-center text-rose-500 text-[10px] font-black bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100">
                                {{ $totalEmployees > 0 ? round(($absentEmployeeCount / $totalEmployees) * 100) : 0 }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late Employees -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-amber-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-amber-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="history" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">มาสาย</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($lateEmployeeCount) }}</h3>
                            <span class="text-xs font-bold text-slate-400">คน</span>
                        </div>
                    </div>
                </div>

                <!-- Total Employee Scans -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-slate-100 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-slate-200 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-slate-800 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="fingerprint" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">สแกนรวม</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalEmployeeScans) }}</h3>
                            <span class="text-xs font-bold text-slate-400">ครั้ง</span>
                        </div>
                    </div>
                </div>

                <!-- Official Duty Employees -->
                <div
                    class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100 relative overflow-hidden group hover:-translate-y-2 transition-all duration-300">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-blue-100 transition-colors">
                    </div>
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-inner">
                            <i data-lucide="briefcase" class="w-7 h-7"></i>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">ไปราชการ</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h3 class="text-3xl font-black text-slate-800">{{ number_format($officialDutyCount) }}</h3>
                            <span class="text-xs font-bold text-slate-400">คน</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Employees Toggle Section -->
            @if($lateEmployees->count() > 0)
                <div x-data="{ open: false }"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-8 bg-slate-50/50 hover:bg-white transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-amber-500 blur-xl opacity-20 animate-pulse"></div>
                                <div
                                    class="relative w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-black text-xl shadow-inner border border-amber-200">
                                    {{ $lateEmployees->unique('employee_id')->count() }}
                                </div>
                            </div>
                            <div class="text-left">
                                <h3 class="font-black text-xl text-slate-800 tracking-tight">ข้าราชการที่มาสายวันนี้</h3>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    รายชื่อข้าราชการที่มาสาย</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 transition-all duration-300 group-hover:border-amber-200 group-hover:text-amber-500 shadow-sm"
                            :class="{ 'rotate-180 bg-amber-50 border-amber-200 text-amber-600': open }">
                            <i data-lucide="chevron-down" class="w-6 h-6"></i>
                        </div>
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="p-8 pt-0 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($lateEmployees->unique('employee_id') as $log)
                                    <div class="relative group/item">
                                        <div
                                            class="bg-slate-50 rounded-3xl p-4 border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1 flex items-center gap-4">
                                            <div class="relative w-14 h-14 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-amber-500 rounded-2xl blur-lg opacity-0 group-hover/item:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm">
                                                    @if($log->employee->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->employee->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-slate-800 text-sm truncate uppercase tracking-tight">
                                                    {{ $log->employee->first_name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 mb-1 truncate">
                                                    {{ $log->employee->position ?? '-' }}</p>
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-black border border-amber-100">
                                                    <i data-lucide="clock" class="w-3 h-3"></i> {{ $log->scan_time->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Official Duty Employees Toggle Section -->
            @if($onOfficialDutyEmployees->count() > 0)
                <div x-data="{ open: false }"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-8 bg-slate-50/50 hover:bg-white transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-blue-500 blur-xl opacity-20 animate-pulse"></div>
                                <div
                                    class="relative w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xl shadow-inner border border-blue-200">
                                    {{ $onOfficialDutyEmployees->count() }}
                                </div>
                            </div>
                            <div class="text-left">
                                <h3 class="font-black text-xl text-slate-800 tracking-tight">ข้าราชการที่ไปราชการ</h3>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    รายชื่อผู้ปฏิบัติราชการนอกสถานที่</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 transition-all duration-300 group-hover:border-blue-200 group-hover:text-blue-500 shadow-sm"
                            :class="{ 'rotate-180 bg-blue-50 border-blue-200 text-blue-600': open }">
                            <i data-lucide="chevron-down" class="w-6 h-6"></i>
                        </div>
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="p-8 pt-0 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($onOfficialDutyEmployees as $employee)
                                    <div class="relative group/item">
                                        <div
                                            class="bg-slate-50 rounded-3xl p-4 border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1 flex items-center gap-4">
                                            <div class="relative w-14 h-14 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-blue-500 rounded-2xl blur-lg opacity-0 group-hover/item:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm">
                                                    @if($employee->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($employee->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-slate-800 text-sm truncate uppercase tracking-tight">
                                                    {{ $employee->first_name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 mb-1 truncate" title="{{ $employee->official_duty_reason ?? '' }}">
                                                    {{ $employee->official_duty_reason ?? 'ไม่ระบุเหตุผล' }}</p>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black border border-blue-100">
                                                    ไปราชการ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            <!-- Absent Employees Toggle Section -->
            @if($absentEmployees->where('on_official_duty', false)->count() > 0)
                <div x-data="{ open: false }"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-8 bg-slate-50/50 hover:bg-white transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-rose-500 blur-xl opacity-20 animate-pulse"></div>
                                <div
                                    class="relative w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center font-black text-xl shadow-inner border border-rose-200">
                                    {{ $absentEmployees->where('on_official_duty', false)->count() }}
                                </div>
                            </div>
                            <div class="text-left">
                                <h3 class="font-black text-xl text-slate-800 tracking-tight">ข้าราชการที่ยังไม่ลงเวลา</h3>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    รายชื่อข้าราชการที่ยังไม่ลงเวลา</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 transition-all duration-300 group-hover:border-rose-200 group-hover:text-rose-500 shadow-sm"
                            :class="{ 'rotate-180 bg-rose-50 border-rose-200 text-rose-600': open }">
                            <i data-lucide="chevron-down" class="w-6 h-6"></i>
                        </div>
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="p-8 pt-0 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($absentEmployees->where('on_official_duty', false) as $employee)
                                    <div class="relative group/item">
                                        <div
                                            class="bg-slate-50 rounded-3xl p-4 border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-xl hover:shadow-rose-500/10 hover:-translate-y-1 flex items-center gap-4">
                                            <div class="relative w-14 h-14 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-rose-500 rounded-2xl blur-lg opacity-0 group-hover/item:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm">
                                                    @if($employee->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($employee->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-slate-800 text-sm truncate uppercase tracking-tight">
                                                    {{ $employee->first_name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 mb-1 truncate">
                                                    {{ $employee->position ?? 'ข้าราชการ' }}</p>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 text-[10px] font-black border border-indigo-100">
                                                    ยังไม่ลงเวลา
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Official Duty Employees Toggle Section -->
            @if($absentEmployees->where('on_official_duty', true)->count() > 0)
                <div x-data="{ open: false }"
                    class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between p-8 bg-slate-50/50 hover:bg-white transition-all duration-300">
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <div class="absolute inset-0 bg-blue-500 blur-xl opacity-20 animate-pulse"></div>
                                <div
                                    class="relative w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xl shadow-inner border border-blue-200">
                                    {{ $absentEmployees->where('on_official_duty', true)->count() }}
                                </div>
                            </div>
                            <div class="text-left">
                                <h3 class="font-black text-xl text-slate-800 tracking-tight">ข้าราชการที่ไปราชการ</h3>
                                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                    รายชื่อข้าราชการที่ติดภารกิจไปราชการ</p>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 transition-all duration-300 group-hover:border-blue-200 group-hover:text-blue-600 shadow-sm"
                            :class="{ 'rotate-180 bg-blue-50 border-blue-200 text-blue-600': open }">
                            <i data-lucide="chevron-down" class="w-6 h-6"></i>
                        </div>
                    </button>

                    <div x-show="open" x-collapse>
                        <div class="p-8 pt-0 bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($absentEmployees->where('on_official_duty', true) as $employee)
                                    <div class="relative group/item">
                                        <div
                                            class="bg-slate-50 rounded-3xl p-4 border border-slate-100 transition-all duration-300 hover:bg-white hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1 flex items-center gap-4">
                                            <div class="relative w-14 h-14 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-blue-500 rounded-2xl blur-lg opacity-0 group-hover/item:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm">
                                                    @if($employee->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($employee->photo_path) }}"
                                                            class="w-full h-full object-cover">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <h4 class="font-black text-slate-800 text-sm truncate uppercase tracking-tight">
                                                    {{ $employee->first_name }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 mb-1 truncate"
                                                    title="{{ $employee->official_duty_reason }}">
                                                    {{ $employee->official_duty_reason ?? $employee->position ?? 'ข้าราชการ' }}</p>
                                                <span
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-black border border-blue-100">
                                                    ไปราชการ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Data Table - Employees -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <div
                    class="px-8 py-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-900 text-white flex items-center justify-center shadow-lg">
                            <i data-lucide="history" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-slate-800 tracking-tight">ประวัติการลงเวลาข้าราชการ</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                ประวัติการเข้างานรายบุคคล</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 px-4 py-2 rounded-2xl border border-slate-200">
                        <span
                            class="text-xs font-black text-slate-600 uppercase tracking-widest">{{ number_format($employeeLogs->total()) }}
                            รายการ</span>
                    </div>
                </div>

                <div class="overflow-x-auto overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead
                            class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] border-b border-slate-100">
                            <tr>
                                <th class="px-8 py-5">ข้อมูลข้าราชการ</th>
                                <th class="px-8 py-5 text-center">ตำแหน่ง/หน่วยงาน</th>
                                <th class="px-8 py-5 text-center">เวลาสแกน</th>
                                <th class="px-8 py-5 text-center">สถานะ</th>
                                <th class="px-8 py-5 text-center">ประเภท</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($employeeLogs as $log)
                                <tr class="hover:bg-slate-50/80 transition-all duration-300 group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="relative w-12 h-12 flex-shrink-0">
                                                <div
                                                    class="absolute inset-0 bg-indigo-500 rounded-2xl blur-lg opacity-0 group-hover:opacity-20 transition-opacity">
                                                </div>
                                                <div
                                                    class="relative w-full h-full rounded-2xl overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100">
                                                    @if($log->employee->photo_path)
                                                        <img src="https://nass.ac.th/faceattendance/storage-file?path={{ urlencode($log->employee->photo_path) }}"
                                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                    @else
                                                        <div
                                                            class="w-full h-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                                                            <i data-lucide="user" class="w-6 h-6"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <p
                                                    class="font-black text-slate-800 text-sm group-hover:text-indigo-600 transition-colors uppercase tracking-tight">
                                                    {{ $log->employee->first_name }} {{ $log->employee->last_name }}</p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded-md border border-slate-200">{{ $log->employee->employee_code ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center relative">
                                        <span
                                            class="relative z-10 inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200 uppercase tracking-wider">
                                            {{ $log->employee->position ?? $log->employee->department ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="font-black text-slate-900 text-lg tabular-nums tracking-tight">{{ $log->scan_time->format('H:i:s') }}</span>
                                            <div
                                                class="flex items-center gap-1 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200 mt-1">
                                                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $log->scan_time->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @if($log->is_late)
                                            <div class="inline-flex flex-col items-center">
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-100 shadow-sm uppercase tracking-widest">
                                                    สาย
                                                </span>
                                            </div>
                                        @else
                                            <div class="inline-flex flex-col items-center">
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm uppercase tracking-widest">
                                                    ปกติ
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @if($log->scan_type === 'in')
                                            <div class="flex items-center justify-center">
                                                <div
                                                    class="px-4 py-1.5 rounded-xl bg-indigo-600 text-white text-[10px] font-black shadow-lg shadow-indigo-200 uppercase tracking-widest flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                                                    เข้างาน
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center">
                                                <div
                                                    class="px-4 py-1.5 rounded-xl bg-slate-800 text-white text-[10px] font-black shadow-lg shadow-slate-200 uppercase tracking-widest flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                                    ออกงาน
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div
                                            class="relative w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner overflow-hidden">
                                            <div class="absolute inset-0 bg-slate-200/50 animate-pulse"></div>
                                            <i data-lucide="ghost" class="relative w-10 h-10 text-slate-300"></i>
                                        </div>
                                        <h4 class="font-black text-slate-800 uppercase tracking-tight">ไม่พบข้อมูลการลงเวลา</h4>
                                        <p class="text-sm text-slate-400 mt-2 font-bold opacity-60">
                                            ลองปรับเปลี่ยนตัวกรองค้นหาเพื่อดูข้อมูลที่ต้องการ</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($employeeLogs->hasPages())
                    <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                        {{ $employeeLogs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Force re-init icons if needed
            if(window.lucide) lucide.createIcons();
        });
    </script>
@endpush
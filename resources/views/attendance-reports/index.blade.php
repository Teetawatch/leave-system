@extends('layouts.app')

@section('title', 'รายงานนักเรียน')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">📊 รายงานการเข้าเรียนนักเรียน</h2>
            <p class="text-slate-500 text-sm">ดูประวัติและสถิติการลงเวลาของนักเรียน</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('attendance-reports.pdf', array_merge(request()->query(), ['date' => $startDate])) }}" 
               target="_blank"
               class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-md text-sm font-medium">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Alert Success -->
     
    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-rose-50 text-rose-700 px-4 py-3 rounded-xl border border-rose-100 flex items-center gap-3 shadow-sm">
            <i class="fa-solid fa-circle-xmark text-lg"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
        <form action="{{ route('attendance-reports.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="text-xs text-slate-500 block mb-1">หลักสูตร</label>
                <select name="course_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">-- ทุกหลักสูตร --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500 block mb-1">วันที่เริ่ม</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="text-xs text-slate-500 block mb-1">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-slate-700 text-white rounded-lg text-sm hover:bg-slate-800 transition-colors">
                <i class="fa-solid fa-search mr-1"></i> ค้นหา
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">นักเรียนทั้งหมด</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-user-check text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">มาเรียน (ช่วงเวลา)</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($uniqueStudentsCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-rose-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-user-xmark text-rose-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">ยังไม่เข้าเรียน</p>
                    <p class="text-2xl font-bold text-rose-600">{{ number_format($absentCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">มาสาย</p>
                    <p class="text-2xl font-bold text-amber-600">{{ number_format($lateCount) }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-barcode text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">การสแกนทั้งหมด</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalScansCount) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Late Students Section -->
    @if($lateStudents->count() > 0)
    <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-100 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 bg-amber-100/50 hover:bg-amber-100 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-clock text-white"></i>
                </div>
                <div class="text-left">
                    <h3 class="text-lg font-bold text-amber-800">⏰ นักเรียนที่มาสาย</h3>
                    <p class="text-sm text-amber-600">{{ $lateStudents->unique('student_id')->count() }} คน</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-down text-amber-600 transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($lateStudents->unique('student_id') as $log)
                <div class="bg-white rounded-xl p-4 border border-amber-200 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-amber-100 overflow-hidden flex-shrink-0 border-2 border-amber-300">
                        @if($log->student->photo_path)
                            <img src="{{ route('fa-storage.file', ['path' => $log->student->photo_path]) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-amber-400">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-700 truncate">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                        <p class="text-xs text-slate-500">{{ $log->student->student_code }}</p>
                        <p class="text-xs text-amber-600 mt-1">
                            <i class="fa-solid fa-clock mr-1"></i> เข้าเรียนเวลา {{ $log->scan_time->format('H:i:s') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Absent Students Section -->
    @if($absentStudents->count() > 0)
    <div class="bg-rose-50 rounded-2xl shadow-sm border border-rose-100 overflow-hidden" x-data="{ open: true }">
        <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-4 bg-rose-100/50 hover:bg-rose-100 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-user-xmark text-white"></i>
                </div>
                <div class="text-left">
                    <h3 class="text-lg font-bold text-rose-800">❌ นักเรียนที่ยังไม่เข้าเรียน</h3>
                    <p class="text-sm text-rose-600">{{ $absentStudents->count() }} คน</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-down text-rose-600 transition-transform" :class="{ 'rotate-180': open }"></i>
        </button>
        <div x-show="open" x-collapse>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($absentStudents as $student)
                <div class="bg-white rounded-xl p-4 border border-rose-200 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-rose-100 overflow-hidden flex-shrink-0 border-2 border-rose-300">
                        @if($student->photo_path)
                            <img src="{{ route('fa-storage.file', ['path' => $student->photo_path]) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-rose-400">
                                <i class="fa-solid fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-700 truncate">{{ $student->first_name }} {{ $student->last_name }}</p>
                        <p class="text-xs text-slate-500">{{ $student->student_code }}</p>
                        <p class="text-xs text-rose-600 mt-1">
                            <i class="fa-solid fa-graduation-cap mr-1"></i> {{ $student->course->name ?? '-' }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-slate-500 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">นักเรียน</th>
                        <th class="px-6 py-4">หลักสูตร</th>
                        <th class="px-6 py-4">วันที่</th>
                        <th class="px-6 py-4">เวลา</th>
                        <th class="px-6 py-4 text-center">ประเภท</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200">
                                    @if($log->student->photo_path)
                                        <img src="{{ route('fa-storage.file', ['path' => $log->student->photo_path]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-slate-700">{{ $log->student->first_name }} {{ $log->student->last_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $log->student->student_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                🎓 {{ $log->student->course->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $log->scan_time->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-slate-700">{{ $log->scan_time->format('H:i:s') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold border border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $log->scan_type === 'in' ? 'เข้าเรียน' : 'ออก' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-clipboard-list text-2xl text-slate-300"></i>
                            </div>
                            <p class="font-medium">ไม่พบข้อมูลการลงเวลา</p>
                            <p class="text-sm mt-1 text-slate-400">ลองเปลี่ยนช่วงเวลาหรือหลักสูตร</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

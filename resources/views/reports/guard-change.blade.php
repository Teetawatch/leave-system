<x-app-layout>
    @section('title', 'รายงานการเปลี่ยนเวรยาม')

    <div class="min-h-screen bg-[#f8fafc] pb-20">
        <!-- Bright Cinematic Analytics Header -->
        <div class="relative bg-white pt-16 pb-32 overflow-hidden border-b border-slate-100">
            <div class="absolute inset-0">
                <div
                    class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-[120px] -mr-48 -mt-48">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-emerald-500/5 rounded-full blur-[100px] -ml-24 -mb-24">
                </div>
            </div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                            </span>
                            <span
                                class="text-indigo-600 font-bold tracking-widest uppercase text-sm">สถิติวิเคราะห์การเปลี่ยนเวร</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight leading-tight">
                            รายงานวิเคราะห์ <br><span class="text-indigo-600">การเปลี่ยนเวรยาม</span>
                        </h1>
                        <p class="text-slate-500 mt-6 max-w-2xl text-lg font-medium leading-relaxed">
                            ระบบรวบรวมข้อมูลและตรวจสอบความโปร่งใสในการสับเปลี่ยนหน้าที่เวรยาม
                            เพื่อความปลอดภัยและประสิทธิภาพสูงสุดขององค์กร
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <button type="button" onclick="window.print()"
                            class="px-8 py-4 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-2xl shadow-sm transition-all font-bold uppercase tracking-widest text-xs flex items-center gap-3 group">
                            <i data-lucide="printer"
                                class="w-5 h-5 group-hover:scale-110 transition-transform text-indigo-500"></i>
                            พิมพ์รายงานสรุป
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
            <!-- Executive Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Volume -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <i data-lucide="layers" class="w-24 h-24 text-slate-900"></i>
                    </div>
                    <div class="flex justify-between items-start mb-6 relative">
                        <div
                            class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg shadow-slate-900/20">
                            <i data-lucide="activity" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">จำนวนทั้งหมด</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ number_format($stats['total']) }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">รายการทั้งหมด</p>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: 100%">
                        </div>
                    </div>
                </div>

                <!-- Confirmed Operations -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <i data-lucide="check-circle" class="w-24 h-24 text-emerald-900"></i>
                    </div>
                    <div class="flex justify-between items-start mb-6 relative">
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <i data-lucide="shield-check" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">อนุมัติแล้ว</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ number_format($stats['approved']) }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">อนุมัติสมบูรณ์</p>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000"
                            style="width: {{ $stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <!-- Pending Verification -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <i data-lucide="clock" class="w-24 h-24 text-amber-900"></i>
                    </div>
                    <div class="flex justify-between items-start mb-6 relative">
                        <div
                            class="w-14 h-14 rounded-2xl bg-amber-400 text-white flex items-center justify-center shadow-lg shadow-amber-400/20">
                            <i data-lucide="timer" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">รอตรวจสอบ</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ number_format($stats['pending']) }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">รอการตรวจสอบ</p>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-amber-400 h-full rounded-full transition-all duration-1000"
                            style="width: {{ $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <!-- Rejected / Anomalies -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500 relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <i data-lucide="alert-octagon" class="w-24 h-24 text-rose-900"></i>
                    </div>
                    <div class="flex justify-between items-start mb-6 relative">
                        <div
                            class="w-14 h-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/20">
                            <i data-lucide="shield-off" class="w-7 h-7"></i>
                        </div>
                        <span
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ปฏิเสธ/ยกเลิก</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ number_format($stats['rejected']) }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">ปฏิเสธ/ยกเลิก</p>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-1000"
                            style="width: {{ $stats['total'] > 0 ? ($stats['rejected'] / $stats['total']) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <!-- Intelligence Horizontal Filter Console -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                            <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                        </div>
                        <h3 class="font-bold text-slate-900 uppercase tracking-widest text-xs">แผงควบคุมการกรองข้อมูล
                        </h3>
                    </div>

                    <form method="GET" action="{{ route('reports.guard-change') }}"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 items-end">
                        <!-- Date Start -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 flex items-center gap-2">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                ตั้งแต่วันที่
                            </label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>

                        <!-- Date End -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 flex items-center gap-2">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                ถึงวันที่
                            </label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 flex items-center gap-2">
                                <i data-lucide="database" class="w-3 h-3"></i>
                                หน่วยงาน/แผนก
                            </label>
                            <div class="relative">
                                <select name="department"
                                    class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer outline-none">
                                    <option value="">ทุกแผนก/สังกัด</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-40">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2 flex items-center gap-2">
                                <i data-lucide="shield" class="w-3 h-3"></i>
                                สถานะการตรวจสอบ
                            </label>
                            <div class="relative">
                                <select name="status"
                                    class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer outline-none">
                                    <option value="">ทุกสถานะ</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                        รอการสับเปลี่ยน</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                        ตอบรับแล้ว</option>
                                    <option value="director_approved" {{ request('status') == 'director_approved' ? 'selected' : '' }}>ผอ. รับทราบ</option>
                                    <option value="fully_approved" {{ request('status') == 'fully_approved' ? 'selected' : '' }}>อนุมัติสมบูรณ์</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                        ปฏิเสธ</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                        ยกเลิก</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-40">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button type="submit"
                                class="flex-1 py-3.5 bg-slate-900 text-white rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-slate-900/20 hover:shadow-indigo-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 group whitespace-nowrap">
                                <i data-lucide="refresh-cw"
                                    class="w-3 h-3 group-hover:rotate-180 transition-transform duration-700"></i>
                                ค้นหาข้อมูล
                            </button>
                            <a href="{{ route('reports.guard-change') }}"
                                class="px-4 py-3.5 bg-white border-2 border-slate-100 text-slate-400 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-slate-50 text-center transition-all flex items-center justify-center">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Main Operational Area -->
                <div class="min-w-0">
                    <div
                        class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden relative">
                        <!-- Table Header Overlay -->
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/50 to-transparent">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100">
                                        <th class="px-8 py-6 text-left">
                                            <span
                                                class="text-xs font-bold text-slate-500 uppercase tracking-widest">ผู้ส่งมอบ
                                                - ผู้รับมอบ</span>
                                        </th>
                                        <th class="px-8 py-6 text-left">
                                            <span
                                                class="text-xs font-bold text-slate-500 uppercase tracking-widest">รายละเอียดเวร</span>
                                        </th>
                                        <th class="px-8 py-6 text-center">
                                            <span
                                                class="text-xs font-bold text-slate-500 uppercase tracking-widest">สถานะปฏิบัติงาน</span>
                                        </th>
                                        <th class="px-8 py-6 text-right">
                                            <span
                                                class="text-xs font-bold text-slate-500 uppercase tracking-widest">บันทึกข้อมูล</span>
                                        </th>
                                        <th class="px-8 py-6 text-center">
                                            <span
                                                class="text-xs font-bold text-slate-500 uppercase tracking-widest">เอกสาร</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($requests as $req)
                                        <tr class="group hover:bg-slate-50/70 transition-all duration-300">
                                            <td class="px-8 py-8 whitespace-nowrap">
                                                <div class="flex flex-col gap-4">
                                                    <!-- From (Original) -->
                                                    <div class="flex items-center gap-4">
                                                        <div class="relative">
                                                            <div
                                                                class="w-12 h-12 rounded-[1.25rem] bg-slate-900 flex items-center justify-center text-sm font-bold text-white shadow-xl shadow-slate-900/10 overflow-hidden ring-4 ring-white group-hover:scale-105 transition-transform duration-500">
                                                                @if($req->user->avatar)
                                                                    <img src="{{ asset('storage/' . $req->user->avatar) }}"
                                                                        class="w-full h-full object-cover">
                                                                @else
                                                                    {{ substr($req->user->name, 0, 1) }}
                                                                @endif
                                                            </div>
                                                            <div
                                                                class="absolute -bottom-1 -right-1 w-5 h-5 bg-rose-500 rounded-lg border-2 border-white flex items-center justify-center shadow-md">
                                                                <i data-lucide="share" class="w-2.5 h-2.5 text-white"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <p class="text-base font-bold text-slate-900 leading-tight">
                                                                {{ $req->user->rank }} {{ $req->user->name }}</p>
                                                            <p
                                                                class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                                                {{ $req->user->department }}</p>
                                                        </div>
                                                    </div>

                                                    <!-- Exchange Indicator Animation -->
                                                    <div class="flex items-center gap-3 pl-6">
                                                        <div
                                                            class="h-8 w-[2px] bg-gradient-to-b from-indigo-200 to-transparent">
                                                        </div>
                                                        <div
                                                            class="text-[8px] font-bold text-indigo-300 uppercase tracking-[0.3em] flex items-center gap-2">
                                                            <span class="animate-pulse">กำลังสับเปลี่ยนหน้าที่</span>
                                                            <i data-lucide="arrow-down" class="w-3 h-3 animate-bounce"></i>
                                                        </div>
                                                    </div>

                                                    <!-- To (Replacement) -->
                                                    <div
                                                        class="flex items-center gap-4 pl-4 border-l-4 border-emerald-400/30 ml-6">
                                                        <div
                                                            class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold shadow-sm overflow-hidden border border-emerald-100 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                                                            @if($req->replacementUser->avatar)
                                                                <img src="{{ asset('storage/' . $req->replacementUser->avatar) }}"
                                                                    class="w-full h-full object-cover">
                                                            @else
                                                                {{ substr($req->replacementUser->name, 0, 1) }}
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-bold text-slate-700 leading-tight">
                                                                {{ $req->replacementUser->rank }}
                                                                {{ $req->replacementUser->name }}</p>
                                                            <p
                                                                class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-0.5">
                                                                ผู้รับมอบหน้าที่</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-8 py-8 whitespace-nowrap">
                                                @php
                                                    $dutyPositions = [
                                                        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                                        'duty_officer' => 'นายทหารเวร',
                                                        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                                    ];
                                                @endphp
                                                <div class="space-y-4">
                                                    <div class="inline-flex flex-col">
                                                        <span
                                                            class="text-[8px] font-bold text-slate-300 uppercase tracking-widest mb-1">ตำแหน่งที่ได้รับมอบ</span>
                                                        <span
                                                            class="px-4 py-1.5 bg-slate-900 text-white rounded-xl text-[10px] font-bold shadow-lg shadow-slate-900/10 border border-slate-700">
                                                            {{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                                            <i data-lucide="calendar-check" class="w-4 h-4"></i>
                                                        </div>
                                                        <div class="flex flex-col">
                                                            <p class="text-sm font-bold text-slate-900">
                                                                {{ $req->duty_date->locale('th')->translatedFormat('d F Y') }}
                                                            </p>
                                                            <p
                                                                class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                                                กำหนดการ</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-8 py-8 whitespace-nowrap text-center">
                                                @php
                                                    $statusConfig = match ($req->status) {
                                                        'fully_approved', 'final_approved' => ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'shadow' => 'shadow-emerald-500/10', 'label' => 'อนุมัติสมบูรณ์', 'icon' => 'check-circle', 'detail' => 'พร้อมปฏิบัติงาน'],
                                                        'director_approved' => ['bg' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'shadow' => 'shadow-indigo-500/10', 'label' => 'ผอ. รับทราบ', 'icon' => 'verified', 'detail' => 'ผ่านการตรวจสอบจากผู้บริหาร'],
                                                        'approved' => ['bg' => 'bg-blue-50 text-blue-600 border-blue-100', 'shadow' => 'shadow-blue-500/10', 'label' => 'ตอบรับแล้ว', 'icon' => 'user-check', 'detail' => 'การตอบรับแบบคู่ขนาน'],
                                                        'rejected' => ['bg' => 'bg-rose-50 text-rose-600 border-rose-100', 'shadow' => 'shadow-rose-500/10', 'label' => 'ถูกปฏิเสธ', 'icon' => 'alert-triangle', 'detail' => 'ปฏิเสธงาน'],
                                                        'cancelled' => ['bg' => 'bg-slate-50 text-slate-400 border-slate-200', 'shadow' => 'shadow-slate-500/10', 'label' => 'ยกเลิก', 'icon' => 'x-circle', 'detail' => 'ยกเลิกรายการ'],
                                                        default => ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'shadow' => 'shadow-amber-500/10', 'label' => 'รอตอบรับ', 'icon' => 'clock', 'detail' => 'รอการสับเปลี่ยน']
                                                    };
                                                @endphp
                                                <div class="flex flex-col items-center gap-2">
                                                    <div
                                                        class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-[1.25rem] {{ $statusConfig['bg'] }} border {{ $statusConfig['shadow'] }} shadow-lg transition-transform group-hover:scale-105">
                                                        <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4"></i>
                                                        <span
                                                            class="text-xs font-bold uppercase tracking-widest">{{ $statusConfig['label'] }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-8 py-8 whitespace-nowrap text-right">
                                                <div class="space-y-1.5">
                                                    <p class="text-sm font-bold text-slate-900">
                                                        {{ $req->created_at->locale('th')->translatedFormat('d M Y') }}
                                                    </p>
                                                    <div
                                                        class="flex items-center justify-end gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                        <span
                                                            class="px-2 py-0.5 bg-slate-100 rounded text-slate-500">{{ $req->created_at->format('H:i') }}
                                                            น.</span>
                                                        <span>บันทึกระบบ</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-8 py-8 whitespace-nowrap text-center">
                                                <a href="{{ route('guard-change.pdf', $req->id) }}" target="_blank"
                                                    class="w-12 h-12 rounded-2xl bg-white text-slate-400 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all duration-500 border border-slate-100 hover:border-rose-500 shadow-sm hover:shadow-xl hover:shadow-rose-500/30 group/pdf relative">
                                                    <i data-lucide="file-text"
                                                        class="w-5 h-5 group-hover/pdf:scale-110 transition-transform"></i>
                                                    <div
                                                        class="absolute -top-1 -right-1 w-3 h-3 bg-indigo-500 rounded-full border-2 border-white scale-0 group-hover/pdf:scale-100 transition-transform">
                                                    </div>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-8 py-40 text-center bg-slate-50/30">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div
                                                        class="w-24 h-24 bg-white rounded-[2.5rem] flex items-center justify-center mb-8 shadow-xl shadow-slate-200/50 relative">
                                                        <i data-lucide="search-x" class="w-12 h-12 text-slate-200"></i>
                                                        <div
                                                            class="absolute -inset-4 bg-indigo-500/5 rounded-full animate-pulse-slow">
                                                        </div>
                                                    </div>
                                                    <h4
                                                        class="text-2xl font-bold text-slate-900 mb-3 uppercase tracking-tight">
                                                        ไม่พบข้อมูล</h4>
                                                    <p
                                                        class="text-sm font-medium text-slate-400 max-w-sm mx-auto leading-relaxed">
                                                        ระบบไม่พบข้อมูลการเปลี่ยนเวรยามภายใต้มิติตัวกรองที่กำหนด
                                                        กรุณาปรับเงื่อนไขการค้นหาใหม่
                                                    </p>
                                                    <button
                                                        onclick="window.location.href='{{ route('reports.guard-change') }}'"
                                                        class="mt-8 px-8 py-3 bg-slate-900 text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-indigo-600 transition-colors">
                                                        แสดงข้อมูลทั้งหมด
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($requests->hasPages())
                        <div class="mt-12 flex justify-center">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse-slow {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.2;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
    </style>
</x-app-layout>
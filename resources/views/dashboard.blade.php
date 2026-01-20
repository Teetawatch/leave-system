<x-app-layout>
    @section('title', 'หน้าหลัก (Dashboard)')

    <div class="min-h-screen pb-12">

        <!-- Hero Header Section -->
        <div
            class="relative bg-gradient-to-br from-brand-700 via-brand-600 to-blue-500 rounded-b-[2.5rem] shadow-xl overflow-hidden -mt-8 pt-12 pb-24 px-4 sm:px-6 lg:px-8 mb-8 text-white">
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-10 pointer-events-none">
                <div
                    class="absolute -top-1/2 -right-1/4 w-[800px] h-[800px] rounded-full bg-white blur-3xl mix-blend-overlay">
                </div>
                <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full bg-brand-300 blur-3xl mix-blend-overlay">
                </div>
            </div>

            <div class="relative max-w-7xl mx-auto flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="flex-1 animate-fade-in-up">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-medium text-brand-50 mb-4">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        ระบบพร้อมใช้งาน
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold tracking-tight text-white mb-2 shadow-sm">
                        สวัสดี, {{ Auth::user()->rank }} {{ Auth::user()->name }}
                    </h1>
                    <p class="text-brand-100 text-lg max-w-2xl font-light">
                        ขอให้วันนี้เป็นวันที่ดีในการทำงาน เริ่มต้นจัดการงานของคุณได้ที่นี่
                    </p>
                </div>

                <!-- Primary Action Buttons (Floating on Desktop) -->
                <div class="flex flex-wrap gap-3 animate-fade-in-up delay-100">
                    <a href="{{ route('leave-request.create') }}"
                        class="group inline-flex items-center gap-3 px-6 py-4 bg-white text-brand-700 hover:text-brand-600 font-bold rounded-2xl shadow-lg hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div
                            class="w-8 h-8 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:scale-110 transition-transform">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </div>
                        <span class="text-base">ยื่นใบลา</span>
                    </a>
                    <a href="{{ route('guard-change.create') }}"
                        class="group inline-flex items-center gap-3 px-6 py-4 bg-brand-800/40 hover:bg-brand-800/60 text-white backdrop-blur-md border border-white/20 font-semibold rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div
                            class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                            <i data-lucide="shield" class="w-5 h-5"></i>
                        </div>
                        <span class="text-base">ขอเปลี่ยนยาม</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content (Overlapping Hero) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10">

            <!-- Alerts -->
            @if(session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 animate-fade-in-up">
                    <div
                        class="bg-white/90 backdrop-blur-xl border border-emerald-100 rounded-2xl p-4 flex items-center gap-4 shadow-lg shadow-emerald-500/5">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center flex-shrink-0 text-white shadow-md shadow-emerald-500/30">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-bold text-emerald-900">ดำเนินการสำเร็จ</h3>
                            <p class="text-sm text-emerald-700">{{ session('status') }}</p>
                        </div>
                        <button @click="show = false"
                            class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if($pendingCount > 0)
                <div class="mb-6 animate-fade-in-up">
                    <div
                        class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-[1px] shadow-lg shadow-amber-500/20">
                        <div class="bg-white rounded-[15px] p-4 flex flex-col sm:flex-row items-center gap-4">
                            <div class="relative">
                                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                                </span>
                                <div
                                    class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-500">
                                    <i data-lucide="clock" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-base font-bold text-slate-800">รายการรออนุมัติ {{ $pendingCount }} รายการ
                                </h3>
                                <p class="text-sm text-slate-500">กรุณาติดตามผลการอนุมัติจากผู้บังคับบัญชา</p>
                            </div>
                            <a href="{{ route('leave-request.index') }}"
                                class="w-full sm:w-auto text-center px-4 py-2 bg-amber-50 text-amber-700 hover:bg-amber-100 font-semibold rounded-xl text-sm transition-colors">
                                ตรวจสอบสถานะ
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Vacation Card -->
                <div
                    class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                            <i data-lucide="plane" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider">วันลาพักร้อน</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm font-medium">คงเหลือ</p>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">
                                {{ $vacationBalance ? ($vacationBalance->remaining_days + 0) : 0 }}
                            </span>
                            <span class="text-sm font-semibold text-slate-400">/
                                {{ $vacationBalance ? ($vacationBalance->total_days + 0) : 0 }} วัน</span>
                        </div>
                        @php
                            $total = ($vacationBalance && $vacationBalance->total_days > 0) ? $vacationBalance->total_days : 1;
                            $remaining = $vacationBalance ? $vacationBalance->remaining_days : 0;
                            $percent = ($remaining / $total) * 100;
                        @endphp
                        <div class="mt-4 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full transition-all duration-1000 ease-out"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Sick Leave Card -->
                <div
                    class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                            <i data-lucide="heart-pulse" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-lg bg-orange-50 text-orange-600 text-[10px] font-bold uppercase tracking-wider">ลาป่วย</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm font-medium">ใช้ไปแล้ว</p>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span
                                class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $sickUsageCount }}</span>
                            <span class="text-sm font-semibold text-slate-400">ครั้ง</span>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <div class="w-1.5 h-1.5 rounded-full bg-orange-400"></div>
                            รวม {{ $sickUsageDays + 0 }} วันในปีนี้
                        </div>
                    </div>
                </div>

                <!-- Personal Leave Card -->
                <div
                    class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                            <i data-lucide="briefcase" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wider">ลากิจ</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm font-medium">ใช้ไปแล้ว</p>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span
                                class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $personalUsageCount }}</span>
                            <span class="text-sm font-semibold text-slate-400">ครั้ง</span>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                            รวม {{ $personalUsageDays + 0 }} วันในปีนี้
                        </div>
                    </div>
                </div>

                <!-- Today's Leave Card -->
                <div
                    class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 text-[10px] font-bold uppercase tracking-wider">ลาวันนี้</span>
                    </div>
                    <div>
                        <p class="text-slate-400 text-sm font-medium">กำลังลา</p>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span
                                class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ $todayLeaves->count() }}</span>
                            <span class="text-sm font-semibold text-slate-400">คน</span>
                        </div>
                        @if($todayLeaves->isNotEmpty())
                            <div class="mt-4 flex -space-x-2 overflow-hidden">
                                @foreach($todayLeaves->take(4) as $leave)
                                    <div class="w-6 h-6 rounded-full bg-slate-100 ring-2 ring-white flex items-center justify-center text-[10px] font-bold text-slate-500"
                                        title="{{ $leave->user->name }}">
                                        {{ mb_substr($leave->user->name, 0, 1) }}
                                    </div>
                                @endforeach
                                @if($todayLeaves->count() > 4)
                                    <div
                                        class="w-6 h-6 rounded-full bg-slate-100 ring-2 ring-white flex items-center justify-center text-[8px] font-bold text-slate-500">
                                        +{{ $todayLeaves->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-emerald-600">
                                <i data-lucide="check" class="w-3 h-3"></i>
                                มาปฏิบัติงานครบ
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content Grid: Quick Links & Recent -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Center Column: Recent Activity (Takes 2 cols on Large) -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Quick Actions Grid (Sorted by Importance) -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('leave-request.create') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                            <div
                                class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                                <i data-lucide="plus-circle" class="w-6 h-6"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 group-hover:text-brand-700">ยื่นใบลา</span>
                        </a>
                        <a href="{{ route('guard-change.create') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                <i data-lucide="shield" class="w-6 h-6"></i>
                            </div>
                            <span
                                class="text-sm font-bold text-slate-700 group-hover:text-emerald-700">ขอเปลี่ยนยาม</span>
                        </a>
                        <a href="{{ route('calendar.index') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                            <div
                                class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                                <i data-lucide="calendar" class="w-6 h-6"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 group-hover:text-purple-700">ปฏิทิน</span>
                        </a>
                        <a href="{{ route('leave-request.index') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all group">
                            <div
                                class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-500 flex items-center justify-center mb-3 group-hover:scale-110 group-hover:bg-slate-600 group-hover:text-white transition-all duration-300">
                                <i data-lucide="history" class="w-6 h-6"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700 group-hover:text-slate-900">ประวัติ</span>
                        </a>
                    </div>

                    <!-- Recent Leaves List -->
                    <div
                        class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                                    <i data-lucide="clock-3" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">รายการล่าสุด</h3>
                                    <p class="text-xs text-slate-400">ติดตามสถานะการลาของคุณ</p>
                                </div>
                            </div>
                            <a href="{{ route('leave-request.index') }}"
                                class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:bg-brand-50 px-4 py-2 rounded-xl transition-all">
                                ดูทั้งหมด
                            </a>
                        </div>

                        @if($recentRequests->isEmpty())
                            <div class="p-12 text-center flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                                </div>
                                <h4 class="text-slate-800 font-bold mb-1">ยังไม่มีรายการ</h4>
                                <p class="text-slate-400 text-sm mb-6">คุณยังไม่ได้ทำการยื่นใบลาในระบบ</p>
                                <a href="{{ route('leave-request.create') }}"
                                    class="px-6 py-2 bg-brand-600 text-white rounded-xl font-medium hover:bg-brand-700 transition-colors shadow-lg shadow-brand-500/30">
                                    เริ่มยื่นใบลา
                                </a>
                            </div>
                        @else
                            <div class="divide-y divide-slate-50">
                                @foreach($recentRequests->take(5) as $req)
                                                <div class="p-6 hover:bg-slate-50/80 transition-colors group cursor-pointer"
                                                    onclick="window.location='{{ route('leave-request.show', $req->id) }}'">
                                                    <div class="flex items-center gap-4">
                                                        @php
                                                            $style = match ($req->leaveType->slug) {
                                                                'sick' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'icon' => 'heart-pulse', 'border' => 'border-orange-100'],
                                                                'vacation' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'plane', 'border' => 'border-blue-100'],
                                                                default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'briefcase', 'border' => 'border-amber-100'],
                                                            };
                                                        @endphp
                                    <div
                                                            class="w-12 h-12 rounded-2xl {{ $style['bg'] }} {{ $style['text'] }} flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300 border {{ $style['border'] }}">
                                                            <i data-lucide="{{ $style['icon'] }}" class="w-6 h-6"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="font-bold text-slate-800">{{ $req->leaveType->name }}</span>
                                                                <span class="text-xs text-slate-400">• {{ $req->total_days + 0 }} วัน</span>
                                                            </div>
                                                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                                                <i data-lucide="calendar-range" class="w-3.5 h-3.5"></i>
                                                                @thaidate($req->start_date) - @thaidate($req->end_date)
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            @php
                                                                $statusStyle = match ($req->status) {
                                                                    'approved' => 'bg-emerald-100 text-emerald-700 ring-emerald-600/20',
                                                                    'rejected' => 'bg-red-100 text-red-700 ring-red-600/20',
                                                                    'cancelled' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                                                                    default => 'bg-amber-100 text-amber-700 ring-amber-600/20'
                                                                };
                                                                $statusLabel = match ($req->status) {
                                                                    'approved' => 'อนุมัติแล้ว',
                                                                    'rejected' => 'ถูกปฏิเสธ',
                                                                    'cancelled' => 'ยกเลิก',
                                                                    default => 'รออนุมัติ'
                                                                };
                                                            @endphp
                                        <span
                                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $statusStyle }} ring-1 ring-inset">
                                                                {{ $statusLabel }}
                                                            </span>
                                                            <p class="text-[10px] text-slate-400 mt-1.5">
                                                                {{ $req->created_at->diffForHumans() }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Calendar & Info -->
                <div class="space-y-8">

                    <!-- Calendar Widget -->
                    <div
                        class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                        <div class="p-6 bg-gradient-to-br from-indigo-500 to-indigo-700 text-white">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-lg flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-5 h-5"></i>
                                    ปฏิทินการลา
                                </h3>
                                <a href="{{ route('calendar.index') }}"
                                    class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors backdrop-blur-sm">
                                    <i data-lucide="maximize-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                            <p class="text-indigo-100 text-sm mb-2">ภาพรวมการลาของบุคลากรภายในหน่วยงาน</p>
                        </div>
                        <div class="p-2">
                            <div id="dashboardCalendar" class="dashboard-calendar"></div>
                        </div>
                    </div>

                    <!-- Leave Regulation Summary -->
                    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2rem] p-6 text-white shadow-xl">
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                            <i data-lucide="info" class="w-5 h-5 text-brand-400"></i>
                            ระเบียบการลา
                        </h3>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 border border-white/10">
                                <div class="w-2 h-2 rounded-full bg-orange-500 shadow-[0_0_10px_rgba(249,115,22,0.5)]">
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm">ลาป่วย</p>
                                    <p class="text-xs text-slate-400">ยื่นได้ทันทีเมื่อมีอาการป่วย</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 border border-white/10">
                                <div class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]">
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm">ลากิจ</p>
                                    <p class="text-xs text-slate-400">ยื่นล่วงหน้าอย่างน้อย 1 วัน</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-white/5 border border-white/10">
                                <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.5)]">
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm">ลาพักผ่อน</p>
                                    <p class="text-xs text-slate-400">ยื่นล่วงหน้าอย่างน้อย 3 วัน</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
        <style>
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translate3d(0, 20px, 0);
                }

                to {
                    opacity: 1;
                    transform: translate3d(0, 0, 0);
                }
            }

            .animate-fade-in-up {
                animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
                /* Start hidden */
            }

            .delay-100 {
                animation-delay: 0.1s;
            }

            .fc-theme-standard td,
            .fc-theme-standard th {
                border: none !important;
            }

            .dashboard-calendar .fc-toolbar-title {
                font-size: 1rem !important;
                font-weight: 700;
                color: #1e293b;
            }

            .dashboard-calendar .fc-daygrid-day-number {
                font-size: 0.75rem;
                font-weight: 600;
                color: #64748b;
            }

            .dashboard-calendar .fc-day-today {
                background: #eff6ff !important;
                border-radius: 0.5rem;
            }

            .dashboard-calendar .fc-col-header-cell-cushion {
                font-size: 0.7rem;
                font-weight: 700;
                color: #94a3b8;
                padding-bottom: 10px;
            }

            .dashboard-calendar .fc-button {
                background-color: transparent !important;
                color: #64748b !important;
                border: 1px solid #e2e8f0 !important;
                border-radius: 0.5rem !important;
                box-shadow: none !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.75rem !important;
            }

            .dashboard-calendar .fc-button:hover {
                background-color: #f8fafc !important;
                color: #0f172a !important;
            }

            .dashboard-calendar .fc-button-active {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
            }

            .dashboard-calendar .fc-icon {
                font-size: 0.75rem !important;
            }

            /* Clean events - small bars */
            #dashboardCalendar .fc-event-title,
            #dashboardCalendar .fc-event-time {
                display: none;
            }

            #dashboardCalendar .fc-event {
                border: none !important;
                cursor: pointer;
                border-radius: 3px;
                margin-bottom: 2px !important;
                box-shadow: none !important;
            }

            /* Make internal content hidden but keep the colored bar */
            #dashboardCalendar .fc-daygrid-block-event .fc-event-main {
                padding: 0;
            }

            #dashboardCalendar .fc-daygrid-block-event {
                height: 6px !important;
            }

            /* For dot events (timed) */
            #dashboardCalendar .fc-daygrid-event-dot {
                border-width: 3px;
                border-radius: 50%;
                margin: 0 auto;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('dashboardCalendar');
                if (!calendarEl) return;

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'th',
                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next' // removed today to save space
                    },
                    height: '350px',
                    contentHeight: 'auto',
                    aspectRatio: 1.5,
                    dayMaxEvents: 3,
                    fixedWeekCount: false,
                    showNonCurrentDates: false,
                    events: function (info, successCallback, failureCallback) {
                        const params = new URLSearchParams({
                            start: info.startStr,
                            end: info.endStr,
                            department: 'all'
                        });

                        fetch(`{{ route('calendar.events') }}?${params}`)
                            .then(response => response.json())
                            .then(data => {
                                successCallback(data);
                            })
                            .catch(error => {
                                console.error('Error fetching events:', error);
                                failureCallback(error);
                            });
                    },
                    eventClick: function (info) {
                        window.location.href = '{{ route("calendar.index") }}';
                    },
                    eventDisplay: 'block'
                });

                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
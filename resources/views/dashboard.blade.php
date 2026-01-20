<x-app-layout>
    @section('title', 'หน้าหลัก (Dashboard)')

    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Success Alert -->
            @if(session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms class="mb-6">
                    <div class="bg-white rounded-2xl border border-emerald-200 p-4 flex items-center gap-4 shadow-sm">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-700">{{ session('status') }}</p>
                        </div>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Compact Welcome Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">👋 สวัสดี</p>
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">
                            {{ Auth::user()->rank }} {{ Auth::user()->name }}
                        </h1>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('leave-request.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>ยื่นใบลา</span>
                        </a>
                        <a href="{{ route('guard-change.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 hover:border-slate-300 shadow-sm transition-all duration-200">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>ขอเปลี่ยนยาม</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Alert (if any) -->
            @if($pendingCount > 0)
                <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-4">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center">
                                <span class="text-[10px] font-bold text-white">{{ $pendingCount }}</span>
                            </span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-800">คุณมี {{ $pendingCount }} รายการที่รออนุมัติ</p>
                            <p class="text-xs text-amber-600">กรุณารอการตรวจสอบจากผู้บังคับบัญชา</p>
                        </div>
                        <a href="{{ route('leave-request.index') }}"
                            class="text-amber-700 hover:text-amber-800 font-medium text-sm flex items-center gap-1">
                            ดูรายละเอียด
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Vacation Balance -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                            <i data-lucide="plane" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">คงเหลือ</span>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mb-1">วันลาพักผ่อน</p>
                    <div class="flex items-baseline gap-1">
                        <span
                            class="text-3xl font-bold text-slate-800">{{ $vacationBalance ? ($vacationBalance->remaining_days + 0) : 0 }}</span>
                        <span class="text-sm text-slate-400">/
                            {{ $vacationBalance ? ($vacationBalance->total_days + 0) : 0 }} วัน</span>
                    </div>
                    @php
                        $total = ($vacationBalance && $vacationBalance->total_days > 0) ? $vacationBalance->total_days : 1;
                        $remaining = $vacationBalance ? $vacationBalance->remaining_days : 0;
                        $percent = ($remaining / $total) * 100;
                    @endphp
                    <div class="mt-3 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full transition-all duration-500"
                            style="width: {{ $percent }}%"></div>
                    </div>
                </div>

                <!-- Sick Leave Usage -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                            <i data-lucide="heart-pulse" class="w-5 h-5"></i>
                        </div>
                        <span
                            class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-1 rounded-lg">ปีนี้</span>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mb-1">ลาป่วย</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-slate-800">{{ $sickUsageCount }}</span>
                        <span class="text-sm text-slate-400">ครั้ง</span>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-1 rounded-lg">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            {{ $sickUsageDays + 0 }} วัน
                        </span>
                    </p>
                </div>

                <!-- Personal Leave Usage -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                            <i data-lucide="briefcase" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">ปีนี้</span>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mb-1">ลากิจ</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-slate-800">{{ $personalUsageCount }}</span>
                        <span class="text-sm text-slate-400">ครั้ง</span>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1 bg-slate-100 px-2 py-1 rounded-lg">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            {{ $personalUsageDays + 0 }} วัน
                        </span>
                    </p>
                </div>

                <!-- Pending / Today's Leave -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">วันนี้</span>
                    </div>
                    <p class="text-xs font-medium text-slate-500 mb-1">คนที่ลา</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-slate-800">{{ $todayLeaves->count() }}</span>
                        <span class="text-sm text-slate-400">คน</span>
                    </div>
                    @if($todayLeaves->isNotEmpty())
                        <p class="mt-3 text-xs text-slate-500 truncate">
                            {{ $todayLeaves->first()->user->name }}
                            @if($todayLeaves->count() > 1)
                                และอีก {{ $todayLeaves->count() - 1 }} คน
                            @endif
                        </p>
                    @else
                        <p class="mt-3 text-xs text-emerald-600 font-medium">
                            <i data-lucide="check-circle" class="w-3 h-3 inline"></i> มาครบ!
                        </p>
                    @endif
                </div>
            </div>

            <!-- Main Grid: Recent + Quick Links -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Activity -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-brand-100 flex items-center justify-center text-brand-600">
                                <i data-lucide="history" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-semibold text-slate-800">รายการล่าสุด</h3>
                        </div>
                        <a href="{{ route('leave-request.index') }}"
                            class="text-sm font-medium text-brand-600 hover:text-brand-700 flex items-center gap-1">
                            ดูทั้งหมด
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </a>
                    </div>

                    @if($recentRequests->isEmpty())
                        <div class="p-8 text-center">
                            <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i>
                            </div>
                            <p class="text-slate-500 font-medium mb-4">ยังไม่มีรายการ</p>
                            <a href="{{ route('leave-request.create') }}"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                ยื่นใบลาใหม่
                            </a>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50">
                            @foreach($recentRequests->take(5) as $req)
                                <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        @php
                                            $iconConfig = match ($req->leaveType->slug) {
                                                'sick' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'heart-pulse'],
                                                'vacation' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'plane'],
                                                default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => 'briefcase'],
                                            };
                                        @endphp
                                        <div
                                            class="w-10 h-10 rounded-xl {{ $iconConfig['bg'] }} flex items-center justify-center {{ $iconConfig['text'] }} flex-shrink-0">
                                            <i data-lucide="{{ $iconConfig['icon'] }}" class="w-5 h-5"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-800 text-sm">{{ $req->leaveType->name }}</p>
                                            <p class="text-xs text-slate-500">
                                                @thaidate($req->start_date) - @thaidate($req->end_date)
                                                <span class="mx-1">•</span>
                                                {{ $req->total_days + 0 }} วัน
                                            </p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            @php
                                                $statusConfig = match ($req->status) {
                                                    'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'อนุมัติแล้ว'],
                                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'ถูกปฏิเสธ'],
                                                    'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'label' => 'ยกเลิกแล้ว'],
                                                    default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'รออนุมัติ']
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                            <p class="text-xs text-slate-400 mt-1">{{ $req->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Quick Links & Today's Leave Detail -->
                <div class="space-y-6">
                    <!-- Quick Links -->
                    <div class="bg-white rounded-2xl border border-slate-200 p-5">
                        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-brand-500"></i>
                            เมนูลัด
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('leave-request.index') }}"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                <div
                                    class="w-9 h-9 rounded-lg bg-slate-100 group-hover:bg-brand-100 flex items-center justify-center text-slate-500 group-hover:text-brand-600 transition-colors">
                                    <i data-lucide="history" class="w-4 h-4"></i>
                                </div>
                                <span
                                    class="font-medium text-slate-700 group-hover:text-brand-700 text-sm">ประวัติการลา</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto"></i>
                            </a>
                            <a href="{{ route('guard-change.index') }}"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                <div
                                    class="w-9 h-9 rounded-lg bg-slate-100 group-hover:bg-emerald-100 flex items-center justify-center text-slate-500 group-hover:text-emerald-600 transition-colors">
                                    <i data-lucide="shield" class="w-4 h-4"></i>
                                </div>
                                <span
                                    class="font-medium text-slate-700 group-hover:text-emerald-700 text-sm">ประวัติเปลี่ยนยาม</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto"></i>
                            </a>
                            <a href="{{ route('calendar.index') }}"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                <div
                                    class="w-9 h-9 rounded-lg bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center text-slate-500 group-hover:text-indigo-600 transition-colors">
                                    <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                </div>
                                <span
                                    class="font-medium text-slate-700 group-hover:text-indigo-700 text-sm">ปฏิทินการลา</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto"></i>
                            </a>
                            @if(in_array(Auth::user()->role, ['supervisor', 'department_head', 'deputy_director', 'director', 'admin']))
                                <a href="{{ route('attendance-reports.index') }}"
                                    class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-slate-100 group-hover:bg-teal-100 flex items-center justify-center text-slate-500 group-hover:text-teal-600 transition-colors">
                                        <i data-lucide="scan-face" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span
                                            class="font-medium text-slate-700 group-hover:text-teal-700 text-sm block">รายงานการเข้างาน</span>
                                        <span class="text-xs text-slate-400">จากระบบสแกนใบหน้า</span>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Today's Leave Detail Card -->
                    @if($todayLeaves->isNotEmpty())
                        <div class="bg-white rounded-2xl border border-slate-200 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                                    <i data-lucide="users" class="w-4 h-4 text-rose-500"></i>
                                    ลาวันนี้
                                </h3>
                                <span
                                    class="text-xs font-medium text-slate-500">{{ now()->locale('th')->isoFormat('D MMM') }}</span>
                            </div>
                            <div class="space-y-3">
                                @foreach($todayLeaves->take(5) as $leave)
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 font-semibold text-sm flex-shrink-0">
                                            {{ mb_substr($leave->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $leave->user->rank }}
                                                {{ $leave->user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $leave->leaveType->name }}</p>
                                        </div>
                                        @php
                                            $dotColors = [
                                                'sick' => 'bg-orange-500',
                                                'vacation' => 'bg-blue-500',
                                                'personal' => 'bg-amber-500',
                                            ];
                                            $dotColor = $dotColors[$leave->leaveType->slug] ?? 'bg-slate-400';
                                        @endphp
                                        <span class="w-2 h-2 rounded-full {{ $dotColor }} flex-shrink-0"></span>
                                    </div>
                                @endforeach
                                @if($todayLeaves->count() > 5)
                                    <p class="text-xs text-slate-500 text-center pt-2">และอีก {{ $todayLeaves->count() - 5 }}
                                        คน...</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Leave Rules Summary -->
                    <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
                        <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2 text-sm">
                            <i data-lucide="info" class="w-4 h-4 text-slate-500"></i>
                            ระเบียบการลา
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                <span class="text-slate-600">ลาป่วย: ยื่นได้ทันที</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-slate-600">ลากิจ: ยื่นล่วงหน้า <strong class="text-amber-700">1
                                        วัน</strong></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <span class="text-slate-600">ลาพักผ่อน: ยื่นล่วงหน้า <strong class="text-blue-700">3
                                        วัน</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div
                    class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-800">ปฏิทินการลา</h3>
                            <p class="text-xs text-slate-500">ภาพรวมการลาของทีม</p>
                        </div>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs text-slate-600">พักร้อน</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-xs text-slate-600">ลาป่วย</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-xs text-slate-600">ลากิจ</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span class="text-xs text-slate-600">เปลี่ยนเวร</span>
                        </div>
                        <a href="{{ route('calendar.index') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1 ml-auto">
                            ดูปฏิทินเต็ม
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div class="relative p-4">
                    <div id="dashboardCalendarLoading"
                        class="hidden absolute inset-0 bg-white/80 z-10 flex items-center justify-center">
                        <div class="flex flex-col items-center gap-2">
                            <div
                                class="w-8 h-8 border-3 border-indigo-200 border-t-indigo-600 rounded-full animate-spin">
                            </div>
                            <p class="text-xs text-slate-500">กำลังโหลด...</p>
                        </div>
                    </div>
                    <div id="dashboardCalendar"></div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
        <style>
            /* FullCalendar Dashboard Styles */
            #dashboardCalendar .fc {
                font-family: inherit;
            }

            #dashboardCalendar .fc .fc-toolbar {
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-bottom: 1rem !important;
            }

            #dashboardCalendar .fc .fc-toolbar-title {
                font-size: 1rem;
                font-weight: 700;
                color: rgb(30 41 59);
            }

            #dashboardCalendar .fc .fc-button {
                background: rgb(99 102 241);
                border: none;
                border-radius: 0.5rem;
                padding: 0.5rem 0.75rem;
                font-weight: 600;
                font-size: 0.75rem;
                text-transform: none;
                transition: all 0.2s ease;
            }

            #dashboardCalendar .fc .fc-button:hover:not(:disabled) {
                background: rgb(79 70 229);
            }

            #dashboardCalendar .fc .fc-button:disabled {
                opacity: 0.5;
            }

            #dashboardCalendar .fc .fc-button-primary:not(:disabled).fc-button-active,
            #dashboardCalendar .fc .fc-button-primary:not(:disabled):active {
                background: rgb(67 56 202);
            }

            #dashboardCalendar .fc .fc-day-today {
                background: rgba(99, 102, 241, 0.05) !important;
            }

            #dashboardCalendar .fc .fc-day-today .fc-daygrid-day-number {
                background: rgb(99 102 241);
                color: white;
                border-radius: 0.375rem;
                padding: 0.2rem 0.4rem;
                font-weight: 700;
                font-size: 0.75rem;
            }

            #dashboardCalendar .fc .fc-daygrid-day-number {
                font-weight: 600;
                color: rgb(71 85 105);
                padding: 0.35rem;
                font-size: 0.75rem;
            }

            #dashboardCalendar .fc .fc-col-header-cell-cushion {
                font-weight: 700;
                color: rgb(100 116 139);
                padding: 0.5rem 0.25rem;
                font-size: 0.65rem;
                text-transform: uppercase;
            }

            #dashboardCalendar .fc .fc-event {
                border-radius: 0.25rem;
                padding: 0.125rem 0.25rem;
                font-size: 0.65rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            #dashboardCalendar .fc .fc-event:hover {
                transform: scale(1.02);
            }

            #dashboardCalendar .fc .fc-daygrid-event-dot {
                display: none;
            }

            #dashboardCalendar .fc-theme-standard td,
            #dashboardCalendar .fc-theme-standard th {
                border-color: rgb(241 245 249);
            }

            #dashboardCalendar .fc .fc-scrollgrid {
                border-radius: 0.75rem;
                overflow: hidden;
                border-color: rgb(241 245 249);
            }

            #dashboardCalendar .fc .fc-more-link {
                color: rgb(99 102 241);
                font-weight: 700;
                font-size: 0.65rem;
                background: rgb(238 242 255);
                padding: 0.1rem 0.35rem;
                border-radius: 0.25rem;
            }

            #dashboardCalendar .fc .fc-popover {
                border-radius: 0.75rem;
                box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1);
                border: 1px solid rgb(241 245 249);
                overflow: hidden;
            }

            #dashboardCalendar .fc .fc-popover-header {
                background: rgb(99 102 241);
                color: white;
                padding: 0.5rem 0.75rem;
                font-weight: 700;
                font-size: 0.75rem;
            }

            /* Responsive adjustments */
            @media (max-width: 640px) {
                #dashboardCalendar .fc .fc-toolbar {
                    flex-direction: column;
                    align-items: stretch;
                }

                #dashboardCalendar .fc .fc-toolbar-chunk {
                    display: flex;
                    justify-content: center;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('dashboardCalendar');
                const loadingEl = document.getElementById('dashboardCalendarLoading');

                if (!calendarEl) return;

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'th',
                    headerToolbar: {
                        left: 'prev,next',
                        center: 'title',
                        right: 'today'
                    },
                    buttonText: {
                        today: 'วันนี้'
                    },
                    height: 'auto',
                    dayMaxEvents: 2,
                    moreLinkClick: 'popover',
                    eventDisplay: 'block',
                    fixedWeekCount: false,
                    events: function (info, successCallback, failureCallback) {
                        if (loadingEl) loadingEl.classList.remove('hidden');

                        const params = new URLSearchParams({
                            start: info.startStr,
                            end: info.endStr,
                            department: 'all',
                            show_guard_change: '1'
                        });

                        fetch(`{{ route('calendar.events') }}?${params}`)
                            .then(response => response.json())
                            .then(data => {
                                if (loadingEl) loadingEl.classList.add('hidden');
                                // Shorten event titles for dashboard
                                const shortenedData = data.map(event => ({
                                    ...event,
                                    title: event.extendedProps?.userName?.split(' ').pop() || event.title.split(' - ')[0]
                                }));
                                successCallback(shortenedData);
                            })
                            .catch(error => {
                                if (loadingEl) loadingEl.classList.add('hidden');
                                console.error('Error fetching events:', error);
                                failureCallback(error);
                            });
                    },
                    eventClick: function (info) {
                        window.location.href = '{{ route("calendar.index") }}';
                    },
                    eventDidMount: function (info) {
                        info.el.title = info.event.extendedProps?.userName + ' - ' + (info.event.extendedProps?.leaveType || 'เปลี่ยนเวร');
                    }
                });

                calendar.render();
            });
        </script>
    @endpush
</x-app-layout>
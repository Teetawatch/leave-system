<x-app-layout>
    @section('title', 'หน้าหลัก (Dashboard)')

    <div class="min-h-screen pb-20">

        <!-- Premium Hero Header -->
        <div class="relative overflow-hidden bg-slate-900 -mt-8 pt-16 pb-32 px-4 shadow-2xl">
            <!-- Dynamic Background Effects -->
            <div class="absolute inset-0 z-0">
                <div
                    class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand-600/20 rounded-full blur-[120px] -mr-48 -mt-48 animate-pulse">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-600/20 rounded-full blur-[100px] -ml-32 -mb-32">
                </div>
                <div
                    class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03]">
                </div>
            </div>

            <div
                class="relative z-10 max-w-7xl mx-auto flex flex-col lg:flex-row lg:items-center justify-between gap-10">
                <div class="flex-1 space-y-4">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 backdrop-blur-xl border border-white/10 text-xs font-black text-brand-300 uppercase tracking-widest animate-fade-in">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        ระบบพร้อมปฏิบัติการ
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight">
                        สวัสดีครับ, <br class="sm:hidden">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-white via-white to-white/50">
                            {{ Auth::user()->rank }}{{ Auth::user()->name }}
                        </span>
                    </h1>
                    <p class="text-slate-400 text-lg md:text-xl font-medium max-w-2xl leading-relaxed">
                        ยินดีต้อนรับสู่ระบบบริหารจัดการการลาและเวรยาม <span
                            class="text-brand-400 font-black">โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</span>
                        พร้อมสนับสนุนการทำงานของคุณในวันนี้
                    </p>
                </div>

                <!-- Strategic Quick Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('leave-request.create') }}"
                        class="group relative inline-flex items-center justify-center gap-4 px-8 py-5 bg-gradient-to-br from-brand-600 to-indigo-600 text-white font-black rounded-[2rem] shadow-[0_20px_40px_-15px_rgba(37,99,235,0.4)] hover:shadow-[0_25px_50px_-12px_rgba(37,99,235,0.6)] hover:-translate-y-1.5 transition-all duration-300 active:scale-95 overflow-hidden">
                        <div
                            class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                        </div>
                        <div
                            class="relative w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-md group-hover:rotate-12 transition-transform">
                            <i data-lucide="plus" class="w-6 h-6"></i>
                        </div>
                        <span class="relative text-lg">ยื่นใบลาใหม่</span>
                    </a>

                    <a href="{{ route('guard-change.create') }}"
                        class="group inline-flex items-center justify-center gap-4 px-8 py-5 bg-white/5 hover:bg-white/10 text-white backdrop-blur-xl border border-white/10 font-black rounded-[2rem] shadow-xl hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 active:scale-95">
                        <div
                            class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                            <i data-lucide="shield-half" class="w-6 h-6"></i>
                        </div>
                        <span class="text-lg">ขอเปลี่ยนยาม</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Dashboard Content Container (Overlapping Header) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">

            <!-- Global Notifications -->
            @if(session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="mb-8">
                    <div
                        class="bg-white/90 backdrop-blur-2xl border border-emerald-100 rounded-[2.5rem] p-5 flex items-center gap-6 shadow-2xl shadow-emerald-500/10 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl opacity-50 -mr-16 -mt-16">
                        </div>
                        <div
                            class="w-14 h-14 rounded-[1.25rem] bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center flex-shrink-0 text-white shadow-lg shadow-emerald-200 rotate-3 group-hover:rotate-0 transition-transform">
                            <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1 relative z-10">
                            <h3 class="text-base font-black text-emerald-900 tracking-tight">ทำรายการสำเร็จ</h3>
                            <p class="text-sm font-bold text-emerald-600/80 mt-0.5">{{ session('status') }}</p>
                        </div>
                        <button @click="show = false"
                            class="relative z-10 p-3 bg-white hover:bg-emerald-50 rounded-2xl text-emerald-400 transition-all hover:rotate-90 shadow-sm border border-emerald-50">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if($pendingCount > 0)
                <div class="mb-8 group cursor-pointer" onclick="window.location='{{ route('leave-request.index') }}'">
                    <div
                        class="bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 rounded-[2.5rem] p-1 shadow-2xl shadow-amber-500/20 hover:shadow-amber-500/40 transition-all hover:-translate-y-1">
                        <div class="bg-white rounded-[2.3rem] p-6 flex flex-col sm:flex-row items-center gap-6">
                            <div class="relative">
                                <span class="absolute -top-1 -right-1 flex h-5 w-5 z-10">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-5 w-5 bg-rose-500 border-2 border-white shadow-sm"></span>
                                </span>
                                <div
                                    class="w-16 h-16 rounded-[1.5rem] bg-amber-50 flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform shadow-inner">
                                    <i data-lucide="bell" class="w-8 h-8"></i>
                                </div>
                            </div>
                            <div class="flex-1 text-center sm:text-left">
                                <h3 class="text-xl font-black text-slate-800 tracking-tight">ตรวจพบรายการค้างอนุมัติ <span
                                        class="text-rose-600">{{ $pendingCount }}</span> รายการ</h3>
                                <p class="text-sm font-bold text-slate-400 mt-1 uppercase tracking-widest">
                                    กรุณาติดตามผลการอนุมัติเพื่อประสิทธิภาพในการบริหารจัดการ</p>
                            </div>
                            <div
                                class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 group-hover:bg-brand-600 text-white font-black rounded-2xl text-sm transition-all shadow-xl flex items-center justify-center gap-2">
                                <span>จัดการทันที</span>
                                <i data-lucide="chevron-right"
                                    class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">

                <!-- Vacation Leave: Progress Circle Style -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 border border-slate-50 shadow-2xl shadow-slate-200/40 hover:shadow-brand-500/10 transition-all duration-500 group relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-blue-50/50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-brand-50 transition-colors">
                    </div>

                    <div class="relative flex items-center justify-between mb-8">
                        <div
                            class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                            <i data-lucide="palmtree" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                            ลาพักผ่อน</div>
                    </div>

                    <div class="relative">
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black text-slate-800 tracking-tighter">
                                {{ $vacationBalance ? ($vacationBalance->remaining_days + 0) : 0 }}
                            </span>
                            <span class="text-lg font-black text-slate-300 uppercase italic">/
                                {{ $vacationBalance ? ($vacationBalance->total_days + 0) : 0 }} วัน</span>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-2 mb-6">
                            สิทธิ์คงเหลือในปีปัจจุบัน</p>

                        @php
                            $total = ($vacationBalance && $vacationBalance->total_days > 0) ? $vacationBalance->total_days : 1;
                            $remaining = $vacationBalance ? $vacationBalance->remaining_days : 0;
                            $percent = min(100, max(0, ($remaining / $total) * 100));
                        @endphp
                        <div class="relative h-2.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                            <div class="absolute left-0 top-0 h-full bg-gradient-to-r from-brand-400 to-brand-600 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(37,99,235,0.4)]"
                                style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Sick Leave: High Frequency Style -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 border border-slate-50 shadow-2xl shadow-slate-200/40 hover:shadow-rose-500/10 transition-all duration-500 group relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-rose-50/50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-rose-100 transition-colors">
                    </div>

                    <div class="relative flex items-center justify-between mb-8">
                        <div
                            class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                            <i data-lucide="thermometer-snowflake" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                            ลาป่วย</div>
                    </div>

                    <div class="relative">
                        <div class="flex items-baseline gap-2">
                            <span
                                class="text-5xl font-black text-slate-800 tracking-tighter">{{ $sickUsageCount }}</span>
                            <span class="text-lg font-black text-slate-300 uppercase italic">ครั้ง</span>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-2 mb-6">
                            รวมการรักษาตัวในปีนี้</p>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-rose-500 w-full opacity-20"></div>
                            </div>
                            <span class="text-[11px] font-black text-rose-600 px-2 py-0.5 bg-rose-50 rounded-lg">รวม
                                {{ $sickUsageDays + 0 }} วัน</span>
                        </div>
                    </div>
                </div>

                <!-- Personal Leave: Professional Style -->
                <div
                    class="bg-white rounded-[2.5rem] p-8 border border-slate-50 shadow-2xl shadow-slate-200/40 hover:shadow-amber-500/10 transition-all duration-500 group relative overflow-hidden text-slate-800">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-amber-50/50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-amber-100 transition-colors">
                    </div>

                    <div class="relative flex items-center justify-between mb-8">
                        <div
                            class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                            <i data-lucide="briefcase" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-black uppercase tracking-[0.2em] shadow-sm">
                            ลากิจส่วนตัว</div>
                    </div>

                    <div class="relative">
                        <div class="flex items-baseline gap-2">
                            <span
                                class="text-5xl font-black text-slate-800 tracking-tighter">{{ $personalUsageCount }}</span>
                            <span class="text-lg font-black text-slate-300 uppercase italic">ครั้ง</span>
                        </div>
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest mt-2 mb-6">
                            ดำเนินการธุระจำเป็น</p>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-amber-500 w-full opacity-20"></div>
                            </div>
                            <span class="text-[11px] font-black text-amber-600 px-2 py-0.5 bg-amber-50 rounded-lg">รวม
                                {{ $personalUsageDays + 0 }} วัน</span>
                        </div>
                    </div>
                </div>

                <!-- Live Presence Card -->
                <div
                    class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl shadow-slate-900/40 hover:shadow-brand-500/30 transition-all duration-500 group relative overflow-hidden">
                    <div
                        class="absolute -bottom-8 -right-8 w-40 h-40 bg-brand-600 rounded-full blur-[80px] opacity-20 group-hover:opacity-40 transition-opacity">
                    </div>

                    <div class="relative flex items-center justify-between mb-8">
                        <div
                            class="w-14 h-14 rounded-[1.25rem] bg-white/10 text-white flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform">
                            <i data-lucide="users-2" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-white/10 text-white/70 text-[10px] font-black uppercase tracking-[0.2em]">
                            กำลังลาวันนี้</div>
                    </div>

                    <div class="relative">
                        <div class="flex items-baseline gap-2">
                            <span
                                class="text-5xl font-black text-white tracking-tighter">{{ $todayLeaves->count() }}</span>
                            <span class="text-lg font-black text-white/30 uppercase italic">ท่าน</span>
                        </div>
                        <p
                            class="text-[11px] font-black text-white/40 uppercase tracking-widest mt-2 mb-6 leading-relaxed">
                            ข้อมูลการปฏิบัติงานล่าสุดในหน่วยงาน</p>

                        @if($todayLeaves->isNotEmpty())
                            <div class="flex -space-x-3 overflow-hidden">
                                @foreach($todayLeaves->take(4) as $leave)
                                    <div class="w-9 h-9 rounded-xl bg-slate-800 ring-4 ring-slate-900 flex items-center justify-center text-[12px] font-black text-slate-300 border border-white/5"
                                        title="{{ $leave->user->name }}">
                                        {{ mb_substr($leave->user->name, 0, 1) }}
                                    </div>
                                @endforeach
                                @if($todayLeaves->count() > 4)
                                    <div
                                        class="w-9 h-9 rounded-xl bg-brand-600 ring-4 ring-slate-900 flex items-center justify-center text-[10px] font-black text-white">
                                        +{{ $todayLeaves->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div
                                class="flex items-center gap-2 text-xs font-black text-emerald-400 bg-emerald-400/10 px-4 py-2 rounded-xl w-fit">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                กำลังพลปฏิบัติงานครบ
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content Grid: Feed & Secondary Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Left Column: Activity Feed (Takes 2 cols) -->
                <div class="lg:col-span-2 space-y-10">

                    <!-- Recent Leaves Feed Card -->
                    <div
                        class="bg-white rounded-[2.5rem] border border-slate-50 shadow-2xl shadow-slate-200/40 overflow-hidden group">
                        <div
                            class="px-8 py-8 border-b border-slate-50 flex items-center justify-between bg-gradient-to-r from-slate-50/50 to-white">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg rotate-3">
                                    <i data-lucide="activity" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-800 text-xl tracking-tight">Timeline รายการล่าสุด
                                    </h3>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                                        ประวัติการยื่นใบลาและการอนุมัติของคุณ</p>
                                </div>
                            </div>
                            <a href="{{ route('leave-request.index') }}"
                                class="inline-flex items-center gap-2 text-sm font-black text-brand-600 hover:text-white hover:bg-brand-600 px-6 py-3 rounded-2xl transition-all border border-brand-100 hover:shadow-lg hover:shadow-brand-500/20">
                                ดูประวัติทั้งหมด
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>

                        <div class="p-2"> <!-- Inner container for padding -->
                            @if($recentRequests->isEmpty())
                                <div class="py-24 text-center flex flex-col items-center justify-center">
                                    <div
                                        class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-6 shadow-inner rotate-12">
                                        <i data-lucide="inbox" class="w-12 h-12 text-slate-300"></i>
                                    </div>
                                    <h4 class="text-xl font-black text-slate-800 mb-2">ยังไม่มีประวัติการใช้งาน</h4>
                                    <p
                                        class="text-slate-400 font-bold text-sm max-w-xs mx-auto mb-8 uppercase tracking-widest">
                                        คุณสมควรได้รับวันพักร้อน! เริ่มยื่นใบลาใบแรกของคุณที่นี่</p>
                                    <a href="{{ route('leave-request.create') }}"
                                        class="px-10 py-4 bg-brand-600 text-white rounded-[1.5rem] font-black hover:bg-brand-700 transition-all shadow-xl shadow-brand-500/30 hover:shadow-brand-500/40 active:scale-95 flex items-center gap-2">
                                        <i data-lucide="plus" class="w-5 h-5"></i>
                                        ยื่นใบลาทันที
                                    </a>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach($recentRequests->take(6) as $req)
                                        <div class="p-6 hover:bg-slate-50 rounded-[1.75rem] transition-all group/item cursor-pointer flex items-center gap-6"
                                            onclick="window.location='{{ route('leave-request.show', $req->id) }}'">

                                            @php
                                                $style = match ($req->leaveType->slug) {
                                                    'sick' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'icon' => 'thermometer', 'ring' => 'ring-rose-100'],
                                                    'vacation' => ['bg' => 'bg-brand-50', 'text' => 'text-brand-600', 'icon' => 'palmtree', 'ring' => 'ring-brand-100'],
                                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'briefcase', 'ring' => 'ring-amber-100'],
                                                };
                                            @endphp

                                            <div class="relative">
                                                <div
                                                    class="w-14 h-14 rounded-2xl {{ $style['bg'] }} {{ $style['text'] }} flex items-center justify-center flex-shrink-0 group-hover/item:scale-110 group-hover/item:rotate-6 transition-all duration-500 shadow-sm ring-1 {{ $style['ring'] }}">
                                                    <i data-lucide="{{ $style['icon'] }}" class="w-7 h-7"></i>
                                                </div>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div
                                                    class="flex items-center gap-3 mb-1.5 font-black text-slate-800 text-lg tracking-tight">
                                                    {{ $req->leaveType->name }}
                                                    <span
                                                        class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-500 text-[10px] tracking-[0.2em] font-black uppercase">{{ $req->total_days + 0 }}
                                                        วัน</span>
                                                </div>
                                                <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-slate-400">
                                                    <span class="flex items-center gap-1.5"><i data-lucide="calendar"
                                                            class="w-3.5 h-3.5"></i> @thaidate($req->start_date) -
                                                        @thaidate($req->end_date)</span>
                                                    <span class="flex items-center gap-1.5 opacity-60"><i data-lucide="clock"
                                                            class="w-3.5 h-3.5"></i>
                                                        {{ $req->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                @php
                                                    $statusStyle = match ($req->status) {
                                                        'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                        'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                        'cancelled' => 'bg-slate-50 text-slate-400 border-slate-100',
                                                        default => 'bg-amber-50 text-amber-600 border-amber-100 animate-pulse'
                                                    };
                                                    $statusLabel = match ($req->status) {
                                                        'approved' => 'อนุมัติแล้ว',
                                                        'rejected' => 'ปฏิเสธแล้ว',
                                                        'cancelled' => 'ยกเลิกรายการ',
                                                        default => 'รออนุมัติ'
                                                    };
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider border {{ $statusStyle }} shadow-sm">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Calendar Widget & Smart Info -->
                <div class="space-y-10">

                    <!-- Interactive Quick Stats (Circle Graph Inspired) -->
                    <div
                        class="bg-white rounded-[2.5rem] border border-slate-50 shadow-2xl shadow-slate-200/40 overflow-hidden">
                        <div
                            class="p-8 bg-gradient-to-br from-indigo-600 via-brand-600 to-brand-500 text-white relative">
                            <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                            <div class="relative z-10 flex items-center justify-between mb-2">
                                <h3 class="font-black text-xl flex items-center gap-3">
                                    <i data-lucide="calendar" class="w-7 h-7"></i>
                                    ปฏิทินการลา
                                </h3>
                                <a href="{{ route('calendar.index') }}"
                                    class="p-3 bg-white/20 hover:bg-white/40 rounded-2xl transition-all active:scale-95 shadow-lg shadow-black/10">
                                    <i data-lucide="maximize" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </div>
                        <div class="p-4">
                            <div id="dashboardCalendar" class="dashboard-calendar"></div>
                        </div>
                    </div>

                    <!-- Smart Leave Intelligence (Regulations) -->
                    <div
                        class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-tr from-brand-600/10 to-transparent"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-brand-500/20 text-brand-400 flex items-center justify-center border border-white/5 animate-bounce">
                                    <i data-lucide="sparkles" class="w-6 h-6"></i>
                                </div>
                                <h3 class="font-black text-xl tracking-tight">เกร็ดความรู้คู่ระเบียบ</h3>
                            </div>

                            <div class="space-y-6">
                                <div
                                    class="p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.06] transition-all group/rule">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div
                                            class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-[0_0_15px_rgba(244,63,94,0.8)]">
                                        </div>
                                        <p class="font-black text-sm uppercase tracking-widest text-slate-300">ลาป่วย
                                        </p>
                                    </div>
                                    <p class="text-xs text-slate-500 font-bold leading-relaxed px-6">
                                        ยื่นได้ทันทีที่มีอาการ กรณีลาเกิน 3 วัน ต้องมีใบรับรองแพทย์</p>
                                </div>

                                <div
                                    class="p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.06] transition-all group/rule">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div
                                            class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-[0_0_15px_rgba(245,158,11,0.8)]">
                                        </div>
                                        <p class="font-black text-sm uppercase tracking-widest text-slate-300">
                                            ลากิจส่วนตัว</p>
                                    </div>
                                    <p class="text-xs text-slate-500 font-bold leading-relaxed px-6">
                                        ควรยื่นล่วงหน้าอย่างน้อย 1 วันทำการ เพื่อประโยชน์ในการบริหารจัดการบุคลากร</p>
                                </div>

                                <div
                                    class="p-5 rounded-3xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.06] transition-all group/rule">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div
                                            class="w-2.5 h-2.5 rounded-full bg-brand-500 shadow-[0_0_15px_rgba(37,99,235,0.8)]">
                                        </div>
                                        <p class="font-black text-sm uppercase tracking-widest text-slate-300">ลาพักผ่อน
                                        </p>
                                    </div>
                                    <p class="text-xs text-slate-500 font-bold leading-relaxed px-6">
                                        สะสมได้สูงสุดไม่เกิน 20-30 วัน (ตามอายุราชการ) แนะนำยื่นล่วงหน้า 3 วัน</p>
                                </div>
                            </div>

                            <button onclick="window.location='{{ route('leave-request.create') }}'"
                                class="w-full mt-10 py-5 bg-white text-slate-900 font-black rounded-[1.75rem] shadow-xl hover:bg-slate-100 transition-all active:scale-95 flex items-center justify-center gap-3">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                                เริ่มดำเนินการทันที
                            </button>
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
                    transform: translate3d(0, 30px, 0);
                }

                to {
                    opacity: 1;
                    transform: translate3d(0, 0, 0);
                }
            }

            .animate-fade-in {
                animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            /* FullCalendar UI Overrides */
            .fc-theme-standard td,
            .fc-theme-standard th {
                border: none !important;
            }

            .dashboard-calendar .fc-toolbar-title {
                font-size: 1.1rem !important;
                font-weight: 900 !important;
                color: #1e293b;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .dashboard-calendar .fc-daygrid-day-number {
                font-size: 0.8rem;
                font-weight: 900;
                color: #94a3b8;
            }

            .dashboard-calendar .fc-day-today {
                background: #f1f5f9 !important;
                border-radius: 1.25rem !important;
            }

            .dashboard-calendar .fc-col-header-cell-cushion {
                font-size: 0.75rem;
                font-weight: 900;
                color: #cbd5e1;
                text-transform: uppercase;
                padding-bottom: 12px;
            }

            .dashboard-calendar .fc-button {
                background-color: #f8fafc !important;
                color: #64748b !important;
                border: 1px solid #f1f5f9 !important;
                border-radius: 0.75rem !important;
                padding: 0.5rem 0.75rem !important;
                font-size: 0.75rem !important;
                font-weight: 900 !important;
                text-transform: uppercase;
                transition: all 0.2s;
            }

            .dashboard-calendar .fc-button:hover {
                background-color: #f1f5f9 !important;
                color: #334155 !important;
            }

            .dashboard-calendar .fc-button-active {
                background-color: #334155 !important;
                color: white !important;
            }

            /* Events bars */
            #dashboardCalendar .fc-event {
                border: none !important;
                cursor: pointer;
                border-radius: 4px;
                margin-bottom: 2px !important;
                height: 5px !important;
            }

            #dashboardCalendar .fc-event-title,
            #dashboardCalendar .fc-event-time {
                display: none;
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
                        right: 'next'
                    },
                    height: 'auto',
                    aspectRatio: 1.3,
                    dayMaxEvents: 2,
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
                                successCallback(data.map(event => ({
                                    ...event,
                                    backgroundColor: event.extendedProps.type === 'vacation' ? '#3b82f6' :
                                        event.extendedProps.type === 'sick' ? '#f43f5e' : '#f59e0b'
                                })));
                            })
                            .catch(error => failureCallback(error));
                    },
                    eventClick: function () { window.location.href = '{{ route("calendar.index") }}'; },
                    eventDisplay: 'block'
                });

                calendar.render();
            });

            // Re-init Lucide icons for dynamically loaded content if needed
            window.lucide.createIcons();
        </script>
    @endpush
</x-app-layout>
<x-app-layout>
    @section('title', 'แผงควบคุมอัจฉริยะ (Dashboard)')

    @push('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=IBM+Plex+Sans+Thai:wght@100;200;300;400;500;600;700&display=swap');

            :root {
                --dashboard-bg: #f8fafc;
                --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                --emerald-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
                --rose-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
                --amber-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                --glass-bg: rgba(255, 255, 255, 0.7);
                --glass-border: rgba(255, 255, 255, 0.5);
            }

            body {
                font-family: 'Outfit', 'IBM Plex Sans Thai', sans-serif;
                background-color: var(--dashboard-bg);
            }

            .premium-card {
                background: white;
                border-radius: 2rem;
                border: 1px solid #f1f5f9;
                box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
                overflow: hidden;
            }

            .premium-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
                border-color: #e2e8f0;
            }

            .glass-card {
                background: var(--glass-bg);
                backdrop-filter: blur(12px);
                border: 1px solid var(--glass-border);
                border-radius: 2rem;
            }

            .stat-value {
                font-family: 'Outfit', sans-serif;
                letter-spacing: -0.05em;
            }

            .floating-dot {
                position: absolute;
                border-radius: 50%;
                filter: blur(60px);
                z-index: 0;
                pointer-events: none;
            }

            @keyframes slow-rotate {
                from {
                    transform: rotate(0deg) scale(1);
                }

                to {
                    transform: rotate(360deg) scale(1.1);
                }
            }

            .animate-slow-rotate {
                animation: slow-rotate 20s linear infinite alternate;
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #e2e8f0;
                border-radius: 10px;
            }

            /* FullCalendar Customization */
            .fc .fc-toolbar-title {
                font-size: 1.25rem !important;
                font-weight: 800 !important;
                color: #1e293b !important;
            }

            .fc .fc-button-primary {
                background: white !important;
                border: 1px solid #e2e8f0 !important;
                color: #64748b !important;
                border-radius: 12px !important;
                font-weight: 700 !important;
                padding: 0.5rem 0.75rem !important;
                font-size: 0.875rem !important;
            }

            .fc .fc-button-primary:hover {
                background: #f8fafc !important;
                color: #0f172a !important;
            }

            .fc .fc-day-today {
                background: #f1f5f9 !important;
            }

            .fc-theme-standard td,
            .fc-theme-standard th {
                border-color: #f1f5f9 !important;
            }

            .fc .fc-daygrid-day-number {
                font-weight: 600;
                color: #94a3b8;
                padding: 8px !important;
            }
        </style>
    @endpush

    <div class="relative min-h-screen pt-4 pb-20 px-4 md:px-8 max-w-[1600px] mx-auto overflow-hidden">

        <!-- Decorative Glows -->
        <div class="floating-dot w-[500px] h-[500px] bg-indigo-500/10 -top-48 -left-24 animate-slow-rotate"></div>
        <div class="floating-dot w-[400px] h-[400px] bg-emerald-500/10 top-1/2 -right-48 animate-slow-rotate"
            style="animation-delay: -5s"></div>

        <div class="relative z-10 space-y-8">

            <!-- Top Alert Section (Consolidated) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8">
                    @if(session('status'))
                        <div x-data="{ show: true }" x-show="show" x-transition
                            class="glass-card p-6 mb-6 flex items-center gap-4 border-l-4 border-l-emerald-500 shadow-lg shadow-emerald-500/5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-900 leading-none">ทำรายการสำเร็จ</h4>
                                <p class="text-sm text-slate-500 mt-1 font-medium">{{ session('status') }}</p>
                            </div>
                            <button @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                    @endif

                    <!-- User Welcome Section -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-2">
                        <div class="space-y-2">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                ระบบบริหารจัดการงานธุรการด้านกำลังพล
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight">
                                สวัสดีครับ, {{ Auth::user()->rank }}<br class="hidden md:block">
                                <span
                                    class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-indigo-400">{{ Auth::user()->name }}</span>
                            </h1>
                            <p class="text-slate-400 font-bold text-sm tracking-wide">
                                <i data-lucide="map-pin" class="w-4 h-4 inline-block -mt-1 mr-1"></i>
                                {{ Auth::user()->department ?? 'ไม่มีสังกัด' }} • โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:block text-right">
                                <p
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">
                                    สถานะปัจจุบัน</p>
                                <p class="text-sm font-bold text-emerald-600">พร้อมปฏิบัติหน้าที่</p>
                            </div>
                            <div
                                class="w-14 h-14 rounded-2xl bg-white p-1 shadow-sm border border-slate-100 overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                                        class="w-full h-full object-cover rounded-xl">
                                @else
                                    <div
                                        class="w-full h-full rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold text-xl uppercase">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col justify-end gap-3 pb-2">
                    <a href="{{ route('leave-request.create') }}"
                        class="flex items-center justify-between p-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-[1.5rem] transition-all transform hover:-translate-y-1 shadow-xl shadow-indigo-600/20 group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i data-lucide="plus" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg leading-none">ยื่นใบลาใหม่</h4>
                                <p class="text-xs text-white/60 mt-1 font-medium">ทำรายการในระบบอัจฉริยะ</p>
                            </div>
                        </div>
                        <i data-lucide="arrow-right"
                            class="w-5 h-5 text-white/40 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('guard-change.create') }}"
                        class="flex items-center justify-between p-5 bg-slate-900 hover:bg-black text-white rounded-[1.5rem] transition-all transform hover:-translate-y-1 shadow-xl shadow-slate-900/20 group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i data-lucide="corner-up-right" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg leading-none">ขอเปลี่ยนยาม</h4>
                                <p class="text-xs text-white/40 mt-1 font-medium">ส่งรายการเปลี่ยนเวรยาม</p>
                            </div>
                        </div>
                        <i data-lucide="arrow-right"
                            class="w-5 h-5 text-white/20 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Dashboard Body -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left Section: Stats & Timeline (8 cols) -->
                <div class="lg:col-span-8 space-y-8">

                    <!-- Statistics Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

                        <!-- Vacation Balance Card -->
                        <div class="premium-card p-6 group">
                            <div class="flex items-center justify-between mb-8 text-indigo-500">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="palmtree" class="w-6 h-6"></i>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.2em] bg-indigo-50 px-3 py-1 rounded-full">พักผ่อนคงเหลือ</span>
                            </div>
                            <div class="flex items-end gap-2 mb-4">
                                <span class="stat-value text-5xl font-black text-slate-800">
                                    {{ $vacationBalance ? ($vacationBalance->remaining_days + 0) : 0 }}
                                </span>
                                <span class="text-slate-300 font-bold mb-1">/
                                    {{ $vacationBalance ? ($vacationBalance->total_days + 0) : 0 }} วัน</span>
                            </div>
                            @php
                                $vTotal = ($vacationBalance && $vacationBalance->total_days > 0) ? $vacationBalance->total_days : 1;
                                $vRem = $vacationBalance ? $vacationBalance->remaining_days : 0;
                                $vPerc = min(100, max(0, ($vRem / $vTotal) * 100));
                            @endphp
                            <div class="h-2 bg-slate-50 rounded-full overflow-hidden mb-2">
                                <div class="h-full bg-indigo-500 transition-all duration-1000 shadow-[0_0_10px_#6366f1]"
                                    style="width: {{ $vPerc }}%"></div>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 text-center uppercase tracking-widest">
                                สิทธิ์ใช้งานในปีนี้</p>
                        </div>

                        <!-- Sick Usage Card -->
                        <div class="premium-card p-6 group">
                            <div class="flex items-center justify-between mb-8 text-rose-500">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="thermometer" class="w-6 h-6"></i>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.2em] bg-rose-50 px-3 py-1 rounded-full">สถิติลาป่วย</span>
                            </div>
                            <div class="flex items-end gap-2 mb-4">
                                <span class="stat-value text-5xl font-black text-slate-800">{{ $sickUsageCount }}</span>
                                <span class="text-slate-300 font-bold mb-1">ครั้ง</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> รวม
                                    {{ $sickUsageDays + 0 }} วัน</span>
                                <span class="text-rose-400 opacity-60">ปีปัจจุบัน</span>
                            </div>
                        </div>

                        <!-- Personal Usage Card -->
                        <div class="premium-card p-6 group">
                            <div class="flex items-center justify-between mb-8 text-amber-500">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i data-lucide="briefcase" class="w-6 h-6"></i>
                                </div>
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.2em] bg-amber-50 px-3 py-1 rounded-full">ลากิจส่วนตัว</span>
                            </div>
                            <div class="flex items-end gap-2 mb-4">
                                <span
                                    class="stat-value text-5xl font-black text-slate-800">{{ $personalUsageCount }}</span>
                                <span class="text-slate-300 font-bold mb-1">ครั้ง</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> รวม
                                    {{ $personalUsageDays + 0 }} วัน</span>
                                <span class="text-amber-500 opacity-60">ปีปัจจุบัน</span>
                            </div>
                        </div>

                    </div>

                    <!-- Timeline Card -->
                    <div class="premium-card">
                        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center rotate-3">
                                    <i data-lucide="activity" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900 tracking-tight leading-none">
                                        ความเคลื่อนไหวล่าสุด</h3>
                                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-black">
                                        รายการประวัติการลาล่าสุดของคุณ</p>
                                </div>
                            </div>
                            <a href="{{ route('leave-request.index') }}"
                                class="px-5 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-indigo-600 transition-all font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                                ประวัติทั้งหมด
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>

                        <div class="p-4 max-h-[500px] overflow-y-auto custom-scrollbar">
                            @if($recentRequests->isEmpty())
                                <div class="py-20 text-center space-y-4">
                                    <div
                                        class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto text-slate-200">
                                        <i data-lucide="inbox" class="w-10 h-10"></i>
                                    </div>
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-sm">
                                        ไม่พบข้อมูลการทำรายการล่าสุด</p>
                                </div>
                            @else
                                <div class="space-y-4">
                                    @foreach($recentRequests->take(8) as $req)
                                        <div class="group flex items-center gap-5 p-4 hover:bg-slate-50 rounded-3xl transition-all cursor-pointer border border-transparent hover:border-slate-100"
                                            onclick="window.location='{{ route('leave-request.show', $req->id) }}'">

                                            @php
                                                $cat = match ($req->leaveType->slug) {
                                                    'sick' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-500', 'icon' => 'thermometer'],
                                                    'vacation' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-500', 'icon' => 'palmtree'],
                                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-500', 'icon' => 'briefcase'],
                                                };
                                                $status = match ($req->status) {
                                                    'approved' => ['class' => 'text-emerald-500 bg-emerald-50', 'label' => 'อนุมัติแล้ว'],
                                                    'rejected' => ['class' => 'text-rose-500 bg-rose-50', 'label' => 'ปฏิเสธแล้ว'],
                                                    'cancelled' => ['class' => 'text-slate-400 bg-slate-50', 'label' => 'ยกเลิก'],
                                                    default => ['class' => 'text-amber-500 bg-amber-50 animate-pulse', 'label' => 'รออนุมัติ'],
                                                };
                                            @endphp

                                            <div
                                                class="w-14 h-14 rounded-2xl {{ $cat['bg'] }} {{ $cat['text'] }} flex items-center justify-center shrink-0 shadow-sm transition-transform group-hover:scale-105">
                                                <i data-lucide="{{ $cat['icon'] }}" class="w-6 h-6"></i>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="font-bold text-slate-800 text-lg tracking-tight truncate leading-tight">
                                                    {{ $req->leaveType->name }} <span
                                                        class="text-slate-300 ml-1 font-medium italic">({{ $req->total_days + 0 }}
                                                        วัน)</span>
                                                </h4>
                                                <p
                                                    class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2 mt-1">
                                                    <i data-lucide="calendar" class="w-3 h-3 shrink-0"></i>
                                                    @thaidate($req->start_date) - @thaidate($req->end_date)
                                                </p>
                                            </div>

                                            <div class="text-right shrink-0">
                                                <span
                                                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $status['class'] }} shadow-sm">
                                                    {{ $status['label'] }}
                                                </span>
                                                <p class="text-[9px] text-slate-300 mt-2 font-bold">
                                                    {{ $req->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Section: Sidebar Elements (4 cols) -->
                <div class="lg:col-span-4 space-y-8">

                    <!-- Calendar Widget -->
                    <div class="premium-card">
                        <div class="p-6 border-b border-slate-50 text-center">
                            <h3 class="text-lg font-black text-slate-900 tracking-tight uppercase">ปฏิทินของหน่วยงาน
                            </h3>
                        </div>
                        <div class="p-4 bg-slate-50/30">
                            <div id="dashboardCalendar"></div>
                        </div>
                        <div class="p-6 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex items-center -space-x-3">
                                @foreach($todayLeaves->take(4) as $leave)
                                    <div class="w-10 h-10 rounded-full border-4 border-white bg-slate-200 flex items-center justify-center font-bold text-xs text-slate-500 uppercase overflow-hidden ring-1 ring-slate-100"
                                        title="{{ $leave->user->name }}">
                                        @if($leave->user->avatar)
                                            <img src="{{ asset('storage/' . $leave->user->avatar) }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            {{ substr($leave->user->name, 0, 1) }}
                                        @endif
                                    </div>
                                @endforeach
                                @if($todayLeaves->count() > 4)
                                    <div
                                        class="w-10 h-10 rounded-full border-4 border-white bg-indigo-600 text-white flex items-center justify-center font-black text-[10px] ring-1 ring-slate-100">
                                        +{{ $todayLeaves->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                            <span
                                class="text-xs font-black text-slate-400 uppercase tracking-widest">กำลังลาวันนี้</span>
                        </div>
                    </div>

                    <!-- Smart Tips/Regulation Card -->
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                        <div
                            class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500 rounded-full blur-[100px] opacity-20">
                        </div>
                        <div class="relative z-10 space-y-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-indigo-400">
                                    <i data-lucide="info" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-black text-xl tracking-tight uppercase">เกร็ดน่ารู้และระเบียบการลา</h4>
                            </div>

                            <div class="space-y-4">
                                <div
                                    class="p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition-colors">
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400 mb-1">
                                        ลาสะสม</p>
                                    <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                        วันลาพักผ่อนสะสมปีปัจจุบัน สามารถสะสมได้สูงสุดไม่เกิน 20-30 วัน
                                        ตามอายุการรับราชการ</p>
                                </div>
                                <div
                                    class="p-4 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition-colors">
                                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-400 mb-1">
                                        ใบรับรองแพทย์</p>
                                    <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                        กรณีลาป่วยติดต่อกันเกิน 3 วันทำการ จำเป็นต้องแนบใบรับรองแพทย์ประกอบการพิจารณา
                                    </p>
                                </div>
                            </div>

                            <button onclick="window.location='{{ route('calendar.index') }}'"
                                class="w-full py-4 bg-indigo-600 hover:bg-indigo-500 transition-all text-white font-black rounded-2xl shadow-xl shadow-indigo-600/20 active:scale-95 flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                                <span>ดูปฏิทินแบบละเอียด</span>
                                <i data-lucide="maximize" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Contact/Support Link -->
                    <div
                        class="p-8 rounded-[2.5rem] bg-indigo-50 border border-indigo-100 flex flex-col items-center text-center space-y-4">
                        <div
                            class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg border border-indigo-200 group hover:rotate-12 transition-transform">
                            <i data-lucide="help-circle" class="w-10 h-10 text-indigo-500"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-900 uppercase">ต้องการความช่วยเหลือ?</h4>
                            <p class="text-xs text-slate-500 mt-2 font-medium">ติดต่อฝ่ายกำลังพล โรงเรียนพลาธิการ
                                หากพบปัญหาในการใช้งานระบบ</p>
                        </div>
                        <a href="tel:023456789"
                            class="text-indigo-600 font-black text-sm uppercase tracking-widest hover:underline decoration-2 underline-offset-4">
                            ติดต่อเจ้าหน้าที่
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

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
                    aspectRatio: 1.1,
                    dayMaxEvents: 1,
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
                                    backgroundColor: event.extendedProps.type === 'vacation' ? '#6366f1' :
                                        event.extendedProps.type === 'sick' ? '#f43f5e' : '#f59e0b',
                                    borderColor: 'transparent'
                                })));
                            })
                            .catch(error => failureCallback(error));
                    },
                    eventClick: function () { window.location.href = '{{ route("calendar.index") }}'; }
                });

                calendar.render();
            });

            // Re-init Lucide
            if (window.lucide) {
                window.lucide.createIcons();
            }
        </script>
    @endpush
</x-app-layout>
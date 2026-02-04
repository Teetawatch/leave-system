<x-app-layout>
    @section('title', 'รายงานวิเคราะห์และสรุปการลา')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .stats-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .stats-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.1);
            }

            .data-table-row {
                transition: all 0.2s ease;
            }

            .data-table-row:hover {
                background-color: rgba(248, 250, 252, 0.8);
            }

            /* Custom scrollbar for table */
            .custom-scrollbar::-webkit-scrollbar {
                height: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .filter-input {
                background: rgba(248, 250, 252, 0.8);
                border: 1px solid rgba(226, 232, 240, 0.8);
                transition: all 0.3s ease;
            }

            .filter-input:focus {
                background: white;
                border-color: #10b981;
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
        
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-emerald-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <!-- Bright Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Analytics & Reporting Module
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-6">
                            รายงานสรุป <span class="text-emerald-600">สถิติการลา</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">
                            ระบบวิเคราะห์ทรัพยากรบุคคลแบบครบวงจร ติดตามแนวโน้มการลาของกำลังพล<br class="hidden md:block">
                            เพื่อการวางแผนและจัดการองค์กรอย่างมีประสิทธิภาพสูงสุด
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <button type="submit" form="filter-form" formaction="{{ route('reports.export') }}"
                            class="group inline-flex items-center justify-center px-8 py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-[2rem] shadow-xl hover:shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest gap-3">
                            <i data-lucide="download" class="w-5 h-5 group-hover:-translate-y-1 transition-transform"></i>
                            ส่งออกรายงาน (Excel)
                        </button>
                        <button type="button" onclick="window.print()" class="group inline-flex items-center justify-center px-8 py-5 bg-white border border-slate-200 text-slate-900 hover:bg-slate-900 hover:text-white font-black text-sm rounded-[2rem] shadow-xl hover:shadow-slate-500/10 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest gap-3">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            พิมพ์รายงาน
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 animate-slide-up" style="animation-delay: 0.1s">
                @php
                    $stats = [
                        ['icon' => 'files', 'color' => 'indigo', 'label' => 'รายการทั้งหมด', 'value' => $requests->total(), 'pct' => 100],
                        ['icon' => 'check-circle', 'color' => 'emerald', 'label' => 'อนุมัติเรียบร้อย', 'value' => \App\Models\LeaveRequest::where('status', 'approved')->count(), 'pct' => $requests->total() > 0 ? (\App\Models\LeaveRequest::where('status', 'approved')->count() / $requests->total()) * 100 : 0],
                        ['icon' => 'clock', 'color' => 'amber', 'label' => 'กำลังรอการตรวจสอบ', 'value' => \App\Models\LeaveRequest::whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director'])->count(), 'pct' => $requests->total() > 0 ? (\App\Models\LeaveRequest::whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director'])->count() / $requests->total()) * 100 : 0],
                        ['icon' => 'x-circle', 'color' => 'rose', 'label' => 'ตรวจสอบแล้วไม่ผ่าน', 'value' => \App\Models\LeaveRequest::whereIn('status', ['rejected', 'cancelled'])->count(), 'pct' => $requests->total() > 0 ? (\App\Models\LeaveRequest::whereIn('status', ['rejected', 'cancelled'])->count() / $requests->total()) * 100 : 0],
                    ];
                @endphp

                @foreach($stats as $stat)
                    <div class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-{{ $stat['color'] }}-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 flex items-center justify-center group-hover:bg-{{ $stat['color'] }}-600 group-hover:text-white transition-all duration-500 shadow-sm border border-{{ $stat['color'] }}-100 group-hover:rotate-12">
                                <i data-lucide="{{ $stat['icon'] }}" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">{{ $stat['label'] }}</span>
                        </div>
                        <div class="relative z-10 pt-2">
                            <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter">{{ number_format($stat['value']) }}</h3>
                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                                <div class="bg-{{ $stat['color'] }}-500 h-full rounded-full transition-all duration-[1.5s] ease-out" 
                                     style="width: {{ $stat['pct'] }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Filter Console -->
            <div class="glass-panel rounded-[3.5rem] p-10 mb-12 animate-slide-up shadow-2xl shadow-slate-900/5" style="animation-delay: 0.2s">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 rounded-[1.25rem] bg-slate-900 text-white flex items-center justify-center shadow-lg">
                        <i data-lucide="sliders-horizontal" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">Intelligence Filter</h3>
                        <p class="text-[10px] font-black text-slate-400 tracking-[0.25em] mt-1">กำหนดเงื่อนไขการสืบค้นข้อมูล</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('reports.index') }}" id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-8 items-end">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">ตั้งแต่วันที่</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900">
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">ถึงวันที่</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900">
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">หน่วยงาน/แผนก</label>
                        <div class="relative group">
                            <select name="department" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900 appearance-none cursor-pointer">
                                <option value="">ทุกหน่วยงาน</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">ประเภทการลา</label>
                        <div class="relative group">
                            <select name="leave_type_id" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900 appearance-none cursor-pointer">
                                <option value="">ทุกประเภท</option>
                                @foreach($leaveTypes as $type)
                                    @if($type->slug !== 'temporary')
                                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">สถานะรายการ</label>
                        <div class="relative group">
                            <select name="status" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900 appearance-none cursor-pointer">
                                <option value="">ทุกสถานะ</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                                <option value="pending" {{ str_contains(request('status') ?? '', 'pending') ? 'selected' : '' }}>รอตรวจสอบ</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธ</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                            </select>
                            <i data-lucide="chevron-down" class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none group-hover:text-emerald-500 transition-colors"></i>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 py-4 bg-slate-900 text-white rounded-[1.75rem] font-black uppercase tracking-[0.1em] text-sm shadow-xl hover:bg-emerald-600 transition-all transform hover:-translate-y-1 active:scale-95 group">
                            <i data-lucide="search" class="w-4 h-4 inline-block mr-2 group-hover:scale-125 transition-transform"></i>
                            กรองข้อมูล
                        </button>
                        <a href="{{ route('reports.index') }}" class="w-14 h-14 bg-white border border-slate-200 text-slate-400 rounded-[1.75rem] flex items-center justify-center hover:bg-slate-50 transition-all hover:rotate-180 active:scale-90 shadow-sm">
                            <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Insights Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12 animate-slide-up" style="animation-delay: 0.3s">
                @if(isset($topLeavers) && $topLeavers->isNotEmpty())
                    <!-- Leaderboard -->
                    <div class="glass-panel rounded-[3.5rem] p-10 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-slate-900 opacity-0 group-hover:opacity-[0.02] transition-opacity"></div>
                        <div class="flex items-center gap-5 mb-10">
                            <div class="w-14 h-14 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center shadow-lg rotate-3 group-hover:rotate-0 transition-transform">
                                <i data-lucide="award" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Top Leavers Analysis</h3>
                                <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase mt-1">สถิติกำลังพลที่ใช้สิทธิ์การลาสูงสุด</p>
                            </div>
                        </div>

                        <div class="space-y-8">
                            @foreach($topLeavers->take(4) as $index => $leaver)
                                <div class="flex items-center gap-6 group/item">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center font-black text-indigo-600 text-lg border border-slate-100 group-hover/item:bg-indigo-600 group-hover/item:text-white transition-all shadow-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-baseline mb-3">
                                            <p class="font-black text-slate-800 text-lg group-hover/item:translate-x-1 transition-transform">{{ $leaver->user->rank }}{{ $leaver->user->name }}</p>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-indigo-600 font-black text-2xl leading-none">{{ $leaver->total_leave_days }}</span>
                                                <span class="text-slate-400 text-[10px] font-black uppercase">วัน</span>
                                            </div>
                                        </div>
                                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-[2s] ease-out-expo" 
                                                 style="width: {{ ($leaver->total_leave_days / max($topLeavers->max('total_leave_days'), 1)) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Distribution -->
                    <div class="glass-panel rounded-[3.5rem] p-10 relative overflow-hidden group">
                        <div class="flex items-center gap-5 mb-10">
                            <div class="w-14 h-14 rounded-[1.5rem] bg-emerald-500 text-white flex items-center justify-center shadow-lg -rotate-3 group-hover:rotate-0 transition-transform">
                                <i data-lucide="pie-chart" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Leave Type Distribution</h3>
                                <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase mt-1">สัดส่วนความถี่แยกตามประเภทการลา</p>
                            </div>
                        </div>

                        <div class="space-y-8 pt-2">
                            @foreach($popularLeaveTypes->take(4) as $type)
                                <div class="space-y-3 group/type">
                                    <div class="flex justify-between items-center px-2">
                                        <span class="text-xs font-black uppercase tracking-[0.15em] text-slate-500 flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 group-hover/type:scale-150 transition-transform"></div>
                                            {{ $type->leaveType->name }}
                                        </span>
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-emerald-500 font-black text-xl leading-none">{{ $type->usage_count }}</span>
                                            <span class="text-slate-400 text-[10px] font-black uppercase">ครั้ง</span>
                                        </div>
                                    </div>
                                    <div class="h-4 bg-slate-100 rounded-full overflow-hidden ring-6 ring-slate-50/50 shadow-inner">
                                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-[2s] ease-out group-hover:brightness-110" 
                                             style="width: {{ ($type->usage_count / max($popularLeaveTypes->max('usage_count'), 1)) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Data Table -->
            <div class="glass-panel rounded-[4rem] overflow-hidden animate-slide-up shadow-2xl shadow-slate-900/10" style="animation-delay: 0.4s">
                <div class="bg-slate-900 px-12 py-8 flex justify-between items-center border-b border-white/5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-emerald-400 border border-white/10 shadow-inner">
                            <i data-lucide="database" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-lg font-black text-white tracking-widest uppercase">Database Record</h4>
                    </div>
                    <div class="px-5 py-2 rounded-full bg-white/5 border border-white/10 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        Total: {{ number_format($requests->total()) }} Records
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-left">
                                <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">ข้อมูลกำลังพล</th>
                                <th class="px-8 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">ประเภทการลา</th>
                                <th class="px-8 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">ช่วงวันปฏิบัติราชการ</th>
                                <th class="px-8 py-8 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">ยอดรวม</th>
                                <th class="px-8 py-8 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">สถานะ</th>
                                <th class="px-10 py-8 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($requests as $req)
                                <tr class="data-table-row group">
                                    <td class="px-10 py-8 whitespace-nowrap">
                                        <div class="flex items-center gap-6">
                                            <div class="w-14 h-14 rounded-[1.75rem] bg-slate-900 flex items-center justify-center font-black text-white text-lg overflow-hidden ring-4 ring-white shadow-xl group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                                @if($req->user->avatar)
                                                    <img src="{{ asset('storage/' . $req->user->avatar) }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ substr($req->user->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xl font-black text-slate-900 leading-tight mb-2 group-hover:text-indigo-600 transition-colors">
                                                    {{ $req->user->rank ? $req->user->rank . ' ' : '' }}{{ $req->user->name }}
                                                </p>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $req->user->department }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 whitespace-nowrap">
                                        <span class="inline-flex px-5 py-2 bg-slate-100 text-slate-900 rounded-full text-[11px] font-black uppercase tracking-[0.1em] shadow-sm border border-slate-200 group-hover:bg-indigo-600 group-hover:text-white group-hover:border-indigo-600 transition-all">
                                            {{ $req->leaveType->name }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-8 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-lg font-black text-slate-800 mb-1">@thaidate($req->start_date)</span>
                                            <div class="flex items-center gap-3">
                                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-emerald-500"></i>
                                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest">ถึง @thaidate($req->end_date)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 whitespace-nowrap text-center">
                                        <div class="w-14 h-14 rounded-2xl bg-white border-2 border-slate-50 flex flex-col items-center justify-center mx-auto shadow-sm group-hover:border-indigo-100 group-hover:bg-indigo-50/50 transition-all">
                                            <span class="text-xl font-black text-slate-900 leading-none mb-1">{{ $req->total_days + 0 }}</span>
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest">วัน</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-8 whitespace-nowrap text-center">
                                        @php
                                            $statusConfig = match ($req->status) {
                                                'approved' => ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติแล้ว'],
                                                'rejected' => ['bg' => 'bg-rose-50 text-rose-600 border-rose-100', 'dot' => 'bg-rose-500', 'label' => 'ไม่อนุมัติ'],
                                                'cancelled' => ['bg' => 'bg-slate-50 text-slate-400 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิก'],
                                                default => ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'dot' => 'bg-amber-500', 'label' => 'รอตรวจสอบ']
                                            };
                                        @endphp
                                        <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full {{ $statusConfig['bg'] }} border shadow-sm group-hover:scale-105 transition-transform duration-300">
                                            <span class="relative flex h-2.5 w-2.5">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $statusConfig['dot'] }} opacity-20"></span>
                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $statusConfig['dot'] }}"></span>
                                            </span>
                                            <span class="text-[11px] font-black uppercase tracking-[0.2em]">{{ $statusConfig['label'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('leave-request.pdf', $req->id) }}" target="_blank"
                                                class="w-12 h-12 rounded-2xl bg-white text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all border border-slate-100 hover:border-slate-900 shadow-sm hover:shadow-xl hover:-translate-y-1 active:scale-95 group/tool"
                                                title="เอกสาร PDF">
                                                <i data-lucide="file-text" class="w-6 h-6 group-hover/tool:rotate-12 transition-transform"></i>
                                            </a>
                                            @if($req->attachment_path)
                                                <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank"
                                                    class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-400 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all border border-indigo-100 hover:border-indigo-600 shadow-sm hover:shadow-xl hover:-translate-y-1 active:scale-95 group/tool"
                                                    title="เอกสารแนบ">
                                                    <i data-lucide="paperclip" class="w-6 h-6 group-hover/tool:-rotate-12 transition-transform"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-10 py-40 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center mb-8 shadow-inner ring-12 ring-slate-50/50">
                                                <i data-lucide="scan-search" class="w-16 h-16 text-slate-200"></i>
                                            </div>
                                            <h4 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">ไม่พบข้อมูลผลลัพธ์</h4>
                                            <p class="text-lg font-bold text-slate-400 max-w-sm mx-auto uppercase tracking-widest leading-relaxed">กรุณาปรับเปลี่ยนเงื่อนไขการค้นหา<br>หรือลองรีเซ็ตตัวกรองใหม่อีกครั้ง</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                @if($requests->hasPages())
                    <div class="bg-white px-12 py-10 border-t border-slate-100 font-bold">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>

            <!-- Dashboard Visual Decorations -->
            <div class="mt-20 flex flex-col items-center justify-center gap-6 opacity-30">
                <div class="w-1 bg-gradient-to-b from-indigo-500 to-transparent h-20 rounded-full"></div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">End of Analytics Document</div>
            </div>
        </div>
    </div>
</x-app-layout>
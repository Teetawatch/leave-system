<x-app-layout>
    @section('title', 'Executive Intelligence Dashboard')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.05) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.05) 0%, transparent 40%),
                    #0f172a;
            }

            .glass-panel {
                background: rgba(30, 41, 59, 0.7);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            .card-emerald {
                background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);
                border-color: rgba(16, 185, 129, 0.2);
            }

            .card-amber {
                background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.05) 100%);
                border-color: rgba(245, 158, 11, 0.2);
            }

            .card-rose {
                background: linear-gradient(135deg, rgba(244, 63, 94, 0.1) 0%, rgba(244, 63, 94, 0.05) 100%);
                border-color: rgba(244, 63, 94, 0.2);
            }

            .card-indigo {
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.05) 100%);
                border-color: rgba(99, 102, 241, 0.2);
            }

            @keyframes slide-up {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-slide-up {
                animation: slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .dashboard-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .dashboard-card:hover {
                transform: translateY(-8px);
                background: rgba(30, 41, 59, 0.9);
                border-color: rgba(255, 255, 255, 0.2);
            }

            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .stat-value {
                text-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Animated Background Elements -->
        <div
            class="absolute top-0 right-0 w-[1000px] h-[1000px] bg-indigo-500/10 rounded-full blur-[150px] -mr-96 -mt-96 animate-pulse">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[800px] h-[800px] bg-emerald-500/5 rounded-full blur-[120px] -ml-48 -mb-48">
        </div>

        <!-- Cinematic Executive Header -->
        <div class="relative pt-20 pb-36 animate-slide-up">
            <div class="max-w-[95rem] mx-auto px-6 sm:px-8 lg:px-12 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-12">
                    <div class="space-y-6">
                        <div
                            class="inline-flex items-center gap-3 px-5 py-2 rounded-full bg-white/5 backdrop-blur-xl border border-white/10 text-[11px] font-black text-white uppercase tracking-[0.3em] shadow-2xl">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Executive Strategy Intelligence
                        </div>
                        <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter leading-none">
                            ระบบบริหารภาพรวม<br>
                            <span
                                class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-indigo-400 to-purple-400">
                                ประจำปีงบประมาณ {{ $currentYear + 543 }}
                            </span>
                        </h1>
                        <p class="text-slate-400 font-medium text-xl max-w-2xl leading-relaxed">
                            ศูนย์ควบคุมข้อมูลกำลังพลเชิงกลยุทธ์
                            วิเคราะห์ประสิทธิภาพและแนวโน้มการปฏิบัติงานของบุคลากรภายในหน่วยงานแบบ Real-time
                        </p>
                    </div>

                    <!-- Executive Summary Pills -->
                    <div class="flex flex-wrap gap-6 items-center">
                        <div
                            class="glass-panel px-10 py-8 rounded-[2.5rem] border-white/20 shadow-2xl group hover:scale-105 transition-transform">
                            <div class="text-5xl font-black text-white mb-2 stat-value">{{ $totalEmployees }}</div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Total Force
                            </div>
                        </div>
                        <div
                            class="glass-panel px-10 py-8 rounded-[2.5rem] bg-emerald-500/10 border-emerald-500/20 shadow-2xl group hover:scale-105 transition-transform">
                            <div class="text-5xl font-black text-emerald-400 mb-2 stat-value">
                                {{ $todayOnLeave->count() }}</div>
                            <div class="text-[10px] font-black text-emerald-400/70 uppercase tracking-[0.3em]">Active
                                Leave</div>
                        </div>
                        <div
                            class="glass-panel px-10 py-8 rounded-[2.5rem] bg-amber-500/10 border-amber-500/20 shadow-2xl group hover:scale-105 transition-transform">
                            <div class="text-5xl font-black text-amber-400 mb-2 stat-value">{{ $pendingLeaves }}</div>
                            <div class="text-[10px] font-black text-amber-400/70 uppercase tracking-[0.3em]">Action
                                Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Console -->
        <div class="max-w-[95rem] mx-auto px-6 sm:px-8 lg:px-12 -mt-24 relative z-20">

            <!-- Strategic KPI Matrix -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 animate-slide-up"
                style="animation-delay: 0.1s">

                <!-- KPI: Volume -->
                <div class="glass-panel rounded-[3rem] p-10 dashboard-card card-indigo group">
                    <div class="flex items-center justify-between mb-10">
                        <div
                            class="w-16 h-16 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-2xl group-hover:rotate-12 transition-transform">
                            <i data-lucide="bar-chart-3" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-widest">
                            Yearly Performance</div>
                    </div>
                    <div class="text-6xl font-black text-white mb-3 stat-value">{{ number_format($totalLeaveRequests) }}
                    </div>
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.25em]">คำขอลาสะสมทั้งหมด
                    </div>
                </div>

                <!-- KPI: Efficiency -->
                <div class="glass-panel rounded-[3rem] p-10 dashboard-card card-emerald group">
                    <div class="flex items-center justify-between mb-10">
                        <div
                            class="w-16 h-16 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-2xl group-hover:rotate-12 transition-transform">
                            <i data-lucide="shield-check" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                            Efficiency Rate</div>
                    </div>
                    <div class="text-6xl font-black text-emerald-400 mb-3 stat-value">
                        {{ $totalLeaveRequests > 0 ? round(($approvedLeaves / $totalLeaveRequests) * 100) : 0 }}%</div>
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.25em]">อัตราการอนุมัติเอกสาร
                    </div>
                </div>

                <!-- KPI: Velocity -->
                <div class="glass-panel rounded-[3rem] p-10 dashboard-card card-amber group">
                    <div class="flex items-center justify-between mb-10">
                        <div
                            class="w-16 h-16 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-2xl group-hover:rotate-12 transition-transform">
                            <i data-lucide="activity" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-black uppercase tracking-widest">
                            Process Velocity</div>
                    </div>
                    <div class="text-6xl font-black text-amber-400 mb-3 stat-value">{{ number_format($pendingLeaves) }}
                    </div>
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.25em]">รายการรอการพิจารณา
                    </div>
                </div>

                <!-- KPI: Growth -->
                <div class="glass-panel rounded-[3rem] p-10 dashboard-card card-rose group">
                    <div class="flex items-center justify-between mb-10">
                        <div
                            class="w-16 h-16 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-2xl group-hover:rotate-12 transition-transform">
                            <i data-lucide="trending-up" class="w-8 h-8"></i>
                        </div>
                        <div
                            class="px-4 py-1.5 rounded-full bg-rose-500/20 text-rose-400 text-[10px] font-black uppercase tracking-widest">
                            Monthly Growth</div>
                    </div>
                    <div class="text-6xl font-black text-white mb-3 stat-value">{{ number_format($thisMonthLeaves) }}
                    </div>
                    <div class="text-[11px] font-black text-slate-400 uppercase tracking-[0.25em]">
                        ปริมาณการใช้สิทธิ์เดือนนี้</div>
                </div>
            </div>

            <!-- Visual Analytics Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12 animate-slide-up" style="animation-delay: 0.2s">

                <!-- Performance Trend Console -->
                <div class="glass-panel rounded-[4rem] overflow-hidden group">
                    <div class="px-12 py-10 border-b border-white/5 bg-white/5 flex items-center gap-6">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-xl">
                            <i data-lucide="line-chart" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight">Performance Trend Analysis</h3>
                            <p class="text-[10px] font-black text-slate-500 tracking-[0.3em] uppercase mt-1">
                                วิเคราะห์แนวโน้มการลาระยะยาว</p>
                        </div>
                    </div>
                    <div class="p-12">
                        <canvas id="monthlyTrendChart" height="350"></canvas>
                    </div>
                </div>

                <!-- Structural Distribution Console -->
                <div class="glass-panel rounded-[4rem] overflow-hidden group">
                    <div class="px-12 py-10 border-b border-white/5 bg-white/5 flex items-center gap-6">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-xl">
                            <i data-lucide="pie-chart" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight">Resource Allocation</h3>
                            <p class="text-[10px] font-black text-slate-500 tracking-[0.3em] uppercase mt-1">
                                สัดส่วนการใช้สิทธิ์ตามประเภทกำลังพล</p>
                        </div>
                    </div>
                    <div class="p-12 flex flex-col md:flex-row items-center justify-around gap-12">
                        <div class="w-72 h-72 relative">
                            <canvas id="leaveTypeChart"></canvas>
                        </div>
                        <div class="grid grid-cols-1 gap-6 w-full md:w-auto">
                            @foreach($leaveTypeDistribution as $type)
                                @php
                                    $typeColors = ['vacation' => '#3b82f6', 'sick' => '#f43f5e', 'personal' => '#f59e0b', 'temporary' => '#8b5cf6', 'official-duty' => '#10b981'];
                                    $color = $typeColors[$type->slug] ?? '#64748b';
                                @endphp
                                <div
                                    class="flex items-center justify-between gap-12 p-5 rounded-3xl bg-white/5 border border-white/5 hover:border-white/20 transition-all group/item">
                                    <div class="flex items-center gap-4">
                                        <div class="w-3 h-3 rounded-full group-hover/item:scale-150 transition-transform"
                                            style="background-color: {{ $color }}"></div>
                                        <span
                                            class="text-sm font-black text-slate-300 uppercase tracking-widest">{{ $type->name }}</span>
                                    </div>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xl font-black text-white">{{ $type->total_days + 0 }}</span>
                                        <span class="text-[10px] font-black text-slate-500 uppercase">Days</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deep Intelligence Matrix -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 animate-slide-up" style="animation-delay: 0.3s">

                <!-- Department Hierarchy Performance -->
                <div class="lg:col-span-2 glass-panel rounded-[4rem] overflow-hidden group">
                    <div class="px-12 py-10 border-b border-white/5 bg-white/5 flex items-center gap-6">
                        <div
                            class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center shadow-xl">
                            <i data-lucide="layout-grid" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight">Unit Operational Metrics</h3>
                            <p class="text-[10px] font-black text-slate-500 tracking-[0.3em] uppercase mt-1">
                                วิเคราะห์ความพร้อมรบ/ปฏิบัติงานรายหน่วย</p>
                        </div>
                    </div>
                    <div class="p-12">
                        <canvas id="departmentChart" height="300"></canvas>
                    </div>
                </div>

                <!-- Strategic Risk Monitoring (Top Leave Takers) -->
                <div class="glass-panel rounded-[4rem] overflow-hidden group">
                    <div class="px-10 py-8 border-b border-white/5 bg-white/5 flex items-center gap-5">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-orange-600 text-white flex items-center justify-center shadow-xl">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight">High Usage Alert</h3>
                            <p class="text-[10px] font-black text-slate-500 tracking-[0.2em] uppercase mt-1">
                                การใช้สิทธิ์ลาสูงสุด Top 10</p>
                        </div>
                    </div>
                    <div class="divide-y divide-white/5 custom-scrollbar max-h-[480px] overflow-y-auto">
                        @forelse($topLeaveTakers as $index => $employee)
                            <div class="p-6 hover:bg-white/5 transition-all flex items-center gap-6 group/staff">
                                <div
                                    class="w-12 h-12 rounded-2xl {{ $index < 3 ? 'bg-gradient-to-br from-amber-400 to-orange-600 text-white shadow-lg' : 'bg-white/5 text-slate-400 border border-white/5' }} flex items-center justify-center font-black text-xl flex-shrink-0 group-hover/staff:rotate-6 transition-transform">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="font-black text-white text-lg truncate group-hover/staff:text-amber-400 transition-colors">
                                        {{ $employee->rank }}{{ $employee->name }}</div>
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">
                                        {{ $employee->department }}</div>
                                </div>
                                <div
                                    class="text-right flex-shrink-0 bg-white/5 px-4 py-2 rounded-2xl border border-white/5">
                                    <div class="text-2xl font-black text-rose-500 stat-value">
                                        {{ $employee->total_days + 0 }}</div>
                                    <div
                                        class="text-[8px] font-black text-slate-500 uppercase tracking-widest leading-none">
                                        Total Days</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-16 text-center text-slate-500">
                                <i data-lucide="shield-check" class="w-20 h-20 mx-auto mb-6 opacity-10"></i>
                                <p class="text-sm font-black uppercase tracking-widest">No Critical Alerts</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Operational Insight: Real-time Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mt-12 animate-slide-up" style="animation-delay: 0.4s">

                <!-- Today Collective Strength -->
                <div class="glass-panel rounded-[4rem] overflow-hidden group border-emerald-500/20">
                    <div class="px-10 py-8 border-b border-white/5 bg-emerald-500/5 flex items-center justify-between">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-xl">
                                <i data-lucide="users" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Current Operational Status</h3>
                                <p class="text-[10px] font-black text-emerald-500/70 tracking-[0.2em] uppercase mt-1">
                                    {{ now()->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div
                            class="px-6 py-2 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-xl font-black text-emerald-400">
                            {{ $todayOnLeave->count() }} <span class="text-xs uppercase font-black ml-1">On Leave</span>
                        </div>
                    </div>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 divide-x divide-y divide-white/5 max-h-[440px] overflow-y-auto custom-scrollbar">
                        @forelse($todayOnLeave as $leave)
                            @php
                                $typeStyle = match ($leave->leaveType->slug) {
                                    'sick' => ['bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'icon' => 'thermometer'],
                                    'vacation' => ['bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-400', 'icon' => 'palmtree'],
                                    default => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'icon' => 'briefcase'],
                                };
                            @endphp
                            <div class="p-8 hover:bg-white/5 transition-all flex items-center gap-6 group/today">
                                <div
                                    class="w-12 h-12 rounded-[1.25rem] {{ $typeStyle['bg'] }} {{ $typeStyle['text'] }} flex items-center justify-center flex-shrink-0 group-hover/today:scale-110 transition-transform">
                                    <i data-lucide="{{ $typeStyle['icon'] }}" class="w-6 h-6"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="font-black text-white text-lg truncate group-hover/today:text-emerald-400 transition-colors">
                                        {{ $leave->user->rank }}{{ $leave->user->name }}</div>
                                    <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-1">
                                        {{ $leave->user->department }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-black {{ $typeStyle['text'] }} uppercase">
                                        {{ $leave->leaveType->name }}</div>
                                    <div class="text-[10px] font-black text-slate-500 mt-1">{{ $leave->total_days + 0 }}
                                        Days Total</div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-24 text-center">
                                <div
                                    class="w-24 h-24 bg-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner ring-12 ring-white/5">
                                    <i data-lucide="shield-check" class="w-12 h-12 text-emerald-400"></i>
                                </div>
                                <h4 class="text-2xl font-black text-white mb-4 tracking-tight">Full Force Operational</h4>
                                <p class="text-sm font-black text-slate-500 uppercase tracking-[0.2em]">
                                    ไม่มีบุคลากรลาปฏิบัติราชการในวันนี้</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Critical Pending Queue -->
                <div class="glass-panel rounded-[4rem] overflow-hidden group border-amber-500/20">
                    <div class="px-10 py-8 border-b border-white/5 bg-amber-500/5 flex items-center justify-between">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-xl">
                                <i data-lucide="shield-alert" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-tight">Pending Approval Queue</h3>
                                <p class="text-[10px] font-black text-amber-500/70 tracking-[0.2em] uppercase mt-1">
                                    รายการที่รอการพิจารณาอนุมัติ</p>
                            </div>
                        </div>
                        <a href="{{ route('approvals.index') }}"
                            class="px-6 py-2 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-[10px] font-black text-slate-400 uppercase tracking-widest transition-all">
                            View All Queue
                        </a>
                    </div>
                    <div class="divide-y divide-white/5 max-h-[440px] overflow-y-auto custom-scrollbar">
                        @forelse($recentRequests as $request)
                            @php
                                $statusLabel = [
                                    'pending_supervisor' => 'S-1 Verify',
                                    'pending_head' => 'Head Approval',
                                    'pending_manager' => 'Exec Review',
                                    'pending_deputy_director' => 'Deputy Final',
                                    'pending_director' => 'Director Final',
                                ][$request->status] ?? 'Pending Review';
                            @endphp
                            <div class="p-8 hover:bg-white/5 transition-all flex items-center gap-8 cursor-pointer group/queue"
                                onclick="window.location='{{ route('leave-request.show', $request->id) }}'">
                                <div
                                    class="w-14 h-14 rounded-[1.5rem] bg-white/5 text-slate-300 flex items-center justify-center flex-shrink-0 font-black text-xl border border-white/10 group-hover/queue:scale-110 group-hover/queue:bg-amber-500 group-hover/queue:text-black transition-all">
                                    {{ mb_substr($request->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="font-black text-white text-lg truncate group-hover/queue:text-amber-400 transition-colors">
                                        {{ $request->user->rank }}{{ $request->user->name }}</div>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span
                                            class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">{{ $request->leaveType->name }}</span>
                                        <div class="w-1 h-1 rounded-full bg-slate-600"></div>
                                        <span
                                            class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $request->total_days + 0 }}
                                            Days Duration</span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div
                                        class="px-4 py-2 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-[10px] font-black text-amber-500 uppercase tracking-widest animate-pulse leading-none">
                                        {{ $statusLabel }}
                                    </div>
                                    <div class="text-[9px] font-black text-slate-600 mt-2 uppercase tracking-widest">
                                        {{ $request->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-24 text-center">
                                <div
                                    class="w-24 h-24 bg-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-inner ring-12 ring-white/5">
                                    <i data-lucide="check-circle-2" class="w-12 h-12 text-emerald-400"></i>
                                </div>
                                <h4 class="text-2xl font-black text-white mb-4 tracking-tight">Queue Fully Processed</h4>
                                <p class="text-sm font-black text-slate-500 uppercase tracking-[0.2em]">
                                    ไม่มีรายการค้างอนุมัติในระบบขณะนี้</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Dashboard Technical Footer -->
            <div
                class="mt-24 pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-8 opacity-30 group">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-indigo-400">
                        <i data-lucide="cpu" class="w-4 h-4"></i>
                    </div>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">System Operational
                        Terminal V2.0</div>
                </div>
                <div class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em] flex items-center gap-4">
                    <span>Precision Analytics</span>
                    <div class="w-1 h-1 rounded-full bg-slate-600"></div>
                    <span>Data Privacy Secure</span>
                    <div class="w-1 h-1 rounded-full bg-slate-600"></div>
                    <span>Real-time Sync</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Chart.js Global Reset for Dark Mode
                Chart.defaults.color = 'rgba(148, 163, 184, 0.8)';
                Chart.defaults.font.family = "'Outfit', 'IBM Plex Sans Thai', sans-serif";
                Chart.defaults.font.weight = 'bold';

                // Monthly Trend Chart
                const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
                const monthlyData = @json($monthlyTrend);

                new Chart(monthlyTrendCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(d => d.month),
                        datasets: [
                            {
                                label: 'ลาพักผ่อน',
                                data: monthlyData.map(d => d.vacation),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true, tension: 0.4, borderWidth: 4,
                                pointBackgroundColor: '#3b82f6', pointBorderColor: 'white', pointBorderWidth: 2, pointRadius: 4
                            },
                            {
                                label: 'ลาป่วย',
                                data: monthlyData.map(d => d.sick),
                                borderColor: '#f43f5e',
                                backgroundColor: 'rgba(244, 63, 94, 0.1)',
                                fill: true, tension: 0.4, borderWidth: 4,
                                pointBackgroundColor: '#f43f5e', pointBorderColor: 'white', pointBorderWidth: 2, pointRadius: 4
                            },
                            {
                                label: 'ลากิจ',
                                data: monthlyData.map(d => d.personal),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true, tension: 0.4, borderWidth: 4,
                                pointBackgroundColor: '#f59e0b', pointBorderColor: 'white', pointBorderWidth: 2, pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: { usePointStyle: true, padding: 30, font: { size: 12, weight: 'black' } }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { size: 14, weight: 'black' },
                                bodyFont: { size: 14 },
                                padding: 15,
                                borderRadius: 12,
                                displayColors: true
                            }
                        },
                        scales: {
                            y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false } }
                        }
                    }
                });

                // Leave Type Distribution Chart
                const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
                const leaveTypeData = @json($leaveTypeDistribution);
                const colorMap = { 'vacation': '#3b82f6', 'sick': '#f43f5e', 'personal': '#f59e0b', 'temporary': '#8b5cf6', 'official-duty': '#10b981' };

                new Chart(leaveTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: leaveTypeData.map(d => d.name),
                        datasets: [{
                            data: leaveTypeData.map(d => d.total_days),
                            backgroundColor: leaveTypeData.map(d => colorMap[d.slug] || '#94a3b8'),
                            borderWidth: 0,
                            hoverOffset: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '75%',
                        plugins: { legend: { display: false } }
                    }
                });

                // Department Chart
                const deptCtx = document.getElementById('departmentChart').getContext('2d');
                const deptData = @json($leaveByDepartment);

                new Chart(deptCtx, {
                    type: 'bar',
                    data: {
                        labels: deptData.map(d => d.department),
                        datasets: [{
                            label: 'Total Leave Days',
                            data: deptData.map(d => d.total_days),
                            backgroundColor: 'rgba(99, 102, 241, 0.15)',
                            borderColor: '#818cf8',
                            borderWidth: 2,
                            borderRadius: 12,
                            maxBarThickness: 45,
                            hoverBackgroundColor: '#818cf8'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });

                if (window.lucide) { window.lucide.createIcons(); }
            });
        </script>
    @endpush
</x-app-layout>
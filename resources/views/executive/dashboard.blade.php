<x-app-layout>
    @section('title', 'Executive Intelligence Dashboard')

    @push('styles')
        <style>
            .premium-bg-light {
                background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            }

            .card-hover {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-hover:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
            }

            .stat-value-light {
                background: linear-gradient(to right, #1e293b, #334155);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .animate-slide-up-fade {
                animation: slideUpFade 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            @keyframes slideUpFade {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush

    <div class="min-h-screen premium-bg-light pb-20">
        <!-- Header Section -->
        <div class="bg-white/50 backdrop-blur-sm border-b border-white/50 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider mb-3">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                            </span>
                            Executive Intelligence
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
                            ระบบบริหารภาพรวมเชิงกลยุทธ์
                        </h1>
                        <p class="text-slate-500 mt-2 font-medium">
                            ปีงบประมาณ {{ $currentYear + 543 }} • ข้อมูล Real-time
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <div
                            class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 min-w-[160px]">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-800 leading-none">{{ $totalEmployees }}</div>
                                <div class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-1">
                                    กำลังพลทั้งหมด</div>
                            </div>
                        </div>
                        <div
                            class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 min-w-[160px]">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                                <i data-lucide="user-x" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-slate-800 leading-none">{{ $todayOnLeave->count() }}
                                </div>
                                <div class="text-xs font-semibold text-slate-400 uppercase tracking-widest mt-1">
                                    กำลังลาอยู่</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 space-y-8">

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Requests -->
                <div class="glass-card rounded-3xl p-6 card-hover animate-slide-up-fade" style="animation-delay: 0.1s;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border border-slate-100">
                            สะสมทั้งปี
                        </span>
                    </div>
                    <div class="text-4xl font-extrabold text-slate-800 mb-1 tracking-tight">
                        {{ number_format($totalLeaveRequests) }}</div>
                    <div class="text-sm font-medium text-slate-500">คำขอลาทั้งหมด</div>
                </div>

                <!-- Approval Rate -->
                <div class="glass-card rounded-3xl p-6 card-hover animate-slide-up-fade" style="animation-delay: 0.2s;">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border border-slate-100">
                            อัตราอนุมัติ
                        </span>
                    </div>
                    <div class="text-4xl font-extrabold text-slate-800 mb-1 tracking-tight">
                        {{ $totalLeaveRequests > 0 ? round(($approvedLeaves / $totalLeaveRequests) * 100) : 0 }}%
                    </div>
                    <div class="text-sm font-medium text-slate-500">ประสิทธิภาพการอนุมัติ</div>
                </div>

                <!-- Pending -->
                <div class="glass-card rounded-3xl p-6 card-hover animate-slide-up-fade" style="animation-delay: 0.3s;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border border-slate-100">
                            รอดำเนินการ
                        </span>
                    </div>
                    <div class="text-4xl font-extrabold text-slate-800 mb-1 tracking-tight">
                        {{ number_format($pendingLeaves) }}</div>
                    <div class="text-sm font-medium text-slate-500">รายการรอพิจารณา</div>
                </div>

                <!-- Monthly Usage -->
                <div class="glass-card rounded-3xl p-6 card-hover animate-slide-up-fade" style="animation-delay: 0.4s;">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                        <span
                            class="px-2.5 py-1 rounded-full bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-wider border border-slate-100">
                            เดือนปัจจุบัน
                        </span>
                    </div>
                    <div class="text-4xl font-extrabold text-slate-800 mb-1 tracking-tight">
                        {{ number_format($thisMonthLeaves) }}</div>
                    <div class="text-sm font-medium text-slate-500">ปริมาณการลาเดือนนี้</div>
                </div>
            </div>

            <!-- Main Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 animate-slide-up-fade" style="animation-delay: 0.5s;">
                <!-- Trend Chart -->
                <div class="glass-card rounded-[2rem] p-8 border border-white/60 shadow-lg shadow-indigo-50/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i data-lucide="line-chart" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">แนวโน้มการลาประจำปี</h3>
                            <p class="text-xs text-slate-500 font-medium">แยกตามประเภทการลาหลัก</p>
                        </div>
                    </div>
                    <div class="h-[300px]">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                <!-- Distribution Chart -->
                <div class="glass-card rounded-[2rem] p-8 border border-white/60 shadow-lg shadow-indigo-50/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-200">
                            <i data-lucide="pie-chart" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">สัดส่วนประเภทการลา</h3>
                            <p class="text-xs text-slate-500 font-medium">ภาพรวมการใช้สิทธิ์</p>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="w-full md:w-1/2 h-[250px] relative">
                            <canvas id="leaveTypeChart"></canvas>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            @foreach($leaveTypeDistribution->take(4) as $type)
                                @php
                                    $colors = ['vacation' => 'bg-blue-500', 'sick' => 'bg-rose-500', 'personal' => 'bg-amber-500', 'temporary' => 'bg-purple-500', 'official-duty' => 'bg-emerald-500'];
                                    $bgClass = $colors[$type->slug] ?? 'bg-slate-500';
                                @endphp
                                <div
                                    class="group flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full {{ $bgClass }}"></div>
                                        <span class="text-sm font-semibold text-slate-600">{{ $type->name }}</span>
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">{{ $type->total_days }} <span
                                            class="text-[10px] text-slate-400 font-normal">วัน</span></span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-slide-up-fade" style="animation-delay: 0.6s;">
                <!-- Department Performance -->
                <div
                    class="lg:col-span-2 glass-card rounded-[2rem] p-8 border border-white/60 shadow-lg shadow-indigo-50/50">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-10 h-10 rounded-xl bg-cyan-500 text-white flex items-center justify-center shadow-lg shadow-cyan-200">
                            <i data-lucide="bar-chart-big" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">สถิติแยกตามหน่วยงาน</h3>
                            <p class="text-xs text-slate-500 font-medium">เปรียบเทียบปริมาณการลา</p>
                        </div>
                    </div>
                    <div class="h-[250px]">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>

                <!-- Top Leave Takers -->
                <div
                    class="glass-card rounded-[2rem] p-0 border border-white/60 shadow-lg shadow-indigo-50/50 overflow-hidden flex flex-col">
                    <div class="p-8 pb-4 bg-gradient-to-b from-white to-slate-50/50">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-200">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">การลาสะสมสูงสุด</h3>
                                <p class="text-xs text-slate-500 font-medium">Top 5 usage ranking</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-1">
                        @forelse($topLeaveTakers->take(5) as $index => $employee)
                            <div class="p-3 rounded-2xl flex items-center gap-4 hover:bg-slate-50 transition-colors group">
                                <div
                                    class="w-8 h-8 rounded-lg {{ $index < 3 ? 'bg-orange-100 text-orange-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-sm font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-slate-800 truncate">
                                        {{ $employee->rank }}{{ $employee->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium truncate">{{ $employee->department }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-bold text-slate-800">{{ $employee->total_days }}</div>
                                    <div class="text-[9px] text-slate-400 uppercase">วัน</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400">
                                <p class="text-sm">ไม่มีข้อมูล</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Bottom Lists: Today Leave & Pending -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-8 animate-slide-up-fade"
                style="animation-delay: 0.7s;">

                <!-- Today Leaves -->
                <div
                    class="glass-card rounded-[2rem] border border-white/60 shadow-lg shadow-indigo-50/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                                <i data-lucide="calendar-check" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-bold text-slate-800">ลาวันนี้</h3>
                        </div>
                        <span
                            class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-1 rounded-md">{{ now()->translatedFormat('d F Y') }}</span>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar p-4 space-y-2">
                        @forelse($todayOnLeave as $leave)
                            <div
                                class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">
                                <div
                                    class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs ring-2 ring-white shadow-sm">
                                    {{ mb_substr($leave->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-slate-800">
                                        {{ $leave->user->rank }}{{ $leave->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $leave->leaveType->name }} •
                                        {{ $leave->user->department }}</div>
                                </div>
                                <span class="px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-xs font-bold">
                                    {{ $leave->total_days }} วัน
                                </span>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div
                                    class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i data-lucide="check" class="w-8 h-8"></i>
                                </div>
                                <p class="text-slate-500 font-medium">วันนี้ปฏิบัติงานครบทุกคน</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Queue -->
                <div
                    class="glass-card rounded-[2rem] border border-white/60 shadow-lg shadow-indigo-50/50 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                <i data-lucide="file-clock" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-bold text-slate-800">รออนุมัติล่าสุด</h3>
                        </div>
                        <a href="{{ route('approvals.index') }}"
                            class="text-xs font-bold text-amber-600 hover:text-amber-700 hover:underline">ดูทั้งหมด</a>
                    </div>
                    <div class="max-h-[300px] overflow-y-auto custom-scrollbar p-4 space-y-2">
                        @forelse($recentRequests as $request)
                            <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-amber-50/50 transition-all border border-transparent hover:border-amber-100 cursor-pointer"
                                onclick="window.location='{{ route('leave-request.show', $request->id) }}'">
                                <div
                                    class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                                    {{ mb_substr($request->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-slate-800">
                                        {{ $request->user->rank }}{{ $request->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $request->leaveType->name }} • รอพิจารณา</div>
                                </div>
                                <div class="text-right">
                                    <span class="block text-xs font-bold text-slate-800">{{ $request->total_days }}
                                        วัน</span>
                                    <span
                                        class="text-[10px] text-slate-400">{{ $request->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div
                                    class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i data-lucide="inbox" class="w-8 h-8"></i>
                                </div>
                                <p class="text-slate-500 font-medium">ไม่มีรายการรออนุมัติ</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Config
                Chart.defaults.font.family = "'IBM Plex Sans Thai', 'Sarabun', sans-serif";
                Chart.defaults.color = '#64748b';

                // Monthly Trend
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
                                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                                fill: true, tension: 0.4, borderWidth: 3,
                                pointBackgroundColor: '#fff', pointBorderColor: '#3b82f6', pointBorderWidth: 2, pointRadius: 4
                            },
                            {
                                label: 'ลาป่วย',
                                data: monthlyData.map(d => d.sick),
                                borderColor: '#f43f5e',
                                backgroundColor: 'rgba(244, 63, 94, 0.05)',
                                fill: true, tension: 0.4, borderWidth: 3,
                                pointBackgroundColor: '#fff', pointBorderColor: '#f43f5e', pointBorderWidth: 2, pointRadius: 4
                            },
                            {
                                label: 'ลากิจ',
                                data: monthlyData.map(d => d.personal),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.05)',
                                fill: true, tension: 0.4, borderWidth: 3,
                                pointBackgroundColor: '#fff', pointBorderColor: '#f59e0b', pointBorderWidth: 2, pointRadius: 4
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
                                labels: { usePointStyle: true, boxWidth: 8, padding: 20, font: { size: 12, weight: 600 } }
                            },
                            tooltip: {
                                backgroundColor: '#fff',
                                titleColor: '#1e293b',
                                bodyColor: '#475569',
                                borderColor: '#e2e8f0',
                                borderWidth: 1,
                                padding: 12,
                                boxPadding: 6,
                                usePointStyle: true,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                            }
                        },
                        scales: {
                            y: { grid: { color: '#f1f5f9' }, border: { display: false }, ticks: { padding: 10 } },
                            x: { grid: { display: false }, border: { display: false }, ticks: { padding: 10 } }
                        }
                    }
                });

                // Leave Type
                const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
                const leaveTypeData = @json($leaveTypeDistribution);
                const colorMap = { 'vacation': '#3b82f6', 'sick': '#f43f5e', 'personal': '#f59e0b', 'temporary': '#a855f7', 'official-duty': '#10b981' };

                new Chart(leaveTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: leaveTypeData.map(d => d.name),
                        datasets: [{
                            data: leaveTypeData.map(d => d.total_days),
                            backgroundColor: leaveTypeData.map(d => colorMap[d.slug] || '#94a3b8'),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
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
                            label: 'จำนวนวันลา',
                            data: deptData.map(d => d.total_days),
                            backgroundColor: '#06b6d4',
                            borderRadius: 6,
                            barThickness: 20,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { color: '#f1f5f9' }, border: { display: false } },
                            x: { grid: { display: false }, border: { display: false } }
                        }
                    }
                });

                if (window.lucide) { window.lucide.createIcons(); }
            });
        </script>
    @endpush
</x-app-layout>
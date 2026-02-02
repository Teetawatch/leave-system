<x-app-layout>
    @section('title', 'ภาพรวมผู้บริหาร (Executive Dashboard)')

    <div class="min-h-screen pb-20">
        <!-- Executive Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 -mt-8 pt-16 pb-32 px-4">
            <!-- Animated Background -->
            <div class="absolute inset-0 z-0 overflow-hidden">
                <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-500/10 rounded-full blur-[150px] -mr-64 -mt-64 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-brand-500/10 rounded-full blur-[120px] -ml-48 -mb-48"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-purple-500/5 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-sm font-bold text-white/80 uppercase tracking-widest">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            Executive Dashboard
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight leading-tight">
                            ภาพรวมการบริหาร<br>
                            <span class="bg-clip-text text-transparent bg-gradient-to-r from-brand-400 to-indigo-400">
                                ปี {{ $currentYear + 543 }}
                            </span>
                        </h1>
                        <p class="text-slate-400 text-lg font-medium max-w-xl">
                            ข้อมูลสถิติและการวิเคราะห์การลาของบุคลากรทั้งหมดในหน่วยงาน
                        </p>
                    </div>

                    <!-- Quick Stats Pills -->
                    <div class="flex flex-wrap gap-4">
                        <div class="px-6 py-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10">
                            <div class="text-3xl font-bold text-white">{{ $totalEmployees }}</div>
                            <div class="text-sm font-medium text-slate-400 uppercase tracking-wider">กำลังพลทั้งหมด</div>
                        </div>
                        <div class="px-6 py-4 bg-emerald-500/20 backdrop-blur-md rounded-2xl border border-emerald-500/20">
                            <div class="text-3xl font-bold text-emerald-400">{{ $todayOnLeave->count() }}</div>
                            <div class="text-sm font-medium text-emerald-300/70 uppercase tracking-wider">ลาวันนี้</div>
                        </div>
                        <div class="px-6 py-4 bg-amber-500/20 backdrop-blur-md rounded-2xl border border-amber-500/20">
                            <div class="text-3xl font-bold text-amber-400">{{ $pendingLeaves }}</div>
                            <div class="text-sm font-medium text-amber-300/70 uppercase tracking-wider">รออนุมัติ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            
            <!-- KPI Cards Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                
                <!-- Total Requests Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-2xl shadow-slate-200/50 border border-slate-100 hover:shadow-brand-500/10 transition-all duration-500 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-brand-50 text-brand-600 text-xs font-bold uppercase tracking-wider">ปีนี้</span>
                    </div>
                    <div class="text-4xl font-bold text-slate-800 mb-1">{{ number_format($totalLeaveRequests) }}</div>
                    <div class="text-sm font-semibold text-slate-400 uppercase tracking-wider">คำขอลาทั้งหมด</div>
                </div>

                <!-- Approved Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-2xl shadow-slate-200/50 border border-slate-100 hover:shadow-emerald-500/10 transition-all duration-500 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold">{{ $totalLeaveRequests > 0 ? round(($approvedLeaves / $totalLeaveRequests) * 100) : 0 }}%</span>
                    </div>
                    <div class="text-4xl font-bold text-slate-800 mb-1">{{ number_format($approvedLeaves) }}</div>
                    <div class="text-sm font-semibold text-slate-400 uppercase tracking-wider">อนุมัติแล้ว</div>
                </div>

                <!-- Pending Card -->
                <div class="bg-white rounded-[2rem] p-6 shadow-2xl shadow-slate-200/50 border border-slate-100 hover:shadow-amber-500/10 transition-all duration-500 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        @if($pendingLeaves > 0)
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                            </span>
                        @endif
                    </div>
                    <div class="text-4xl font-bold text-slate-800 mb-1">{{ number_format($pendingLeaves) }}</div>
                    <div class="text-sm font-semibold text-slate-400 uppercase tracking-wider">รออนุมัติ</div>
                </div>

                <!-- Month Comparison Card -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2rem] p-6 shadow-2xl shadow-slate-900/30 group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-white/10 text-white flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full {{ $leaveChangePercent >= 0 ? 'bg-rose-500/20 text-rose-400' : 'bg-emerald-500/20 text-emerald-400' }} text-xs font-bold">
                            {{ $leaveChangePercent >= 0 ? '+' : '' }}{{ $leaveChangePercent }}%
                        </span>
                    </div>
                    <div class="text-4xl font-bold text-white mb-1">{{ number_format($thisMonthLeaves) }}</div>
                    <div class="text-sm font-semibold text-slate-400 uppercase tracking-wider">ลาเดือนนี้</div>
                    <div class="text-xs text-slate-500 mt-2">เทียบกับเดือนที่แล้ว ({{ $lastMonthLeaves }})</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                
                <!-- Monthly Trend Chart -->
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 to-white">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-indigo-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="line-chart" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-xl">แนวโน้มการลารายเดือน</h3>
                                <p class="text-sm text-slate-400">ย้อนหลัง 12 เดือน</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="monthlyTrendChart" height="280"></canvas>
                    </div>
                </div>

                <!-- Leave Type Distribution Chart -->
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 to-white">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="pie-chart" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-xl">สัดส่วนประเภทการลา</h3>
                                <p class="text-sm text-slate-400">แยกตามประเภท</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 flex items-center justify-center">
                        <div class="w-64 h-64">
                            <canvas id="leaveTypeChart"></canvas>
                        </div>
                        <div class="ml-6 space-y-3">
                            @foreach($leaveTypeDistribution as $type)
                                @php
                                    $colors = [
                                        'vacation' => 'bg-blue-500',
                                        'sick' => 'bg-rose-500',
                                        'personal' => 'bg-amber-500',
                                        'temporary' => 'bg-purple-500',
                                        'official_duty' => 'bg-emerald-500',
                                    ];
                                    $color = $colors[$type->slug] ?? 'bg-slate-400';
                                @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full {{ $color }}"></div>
                                    <span class="text-sm font-medium text-slate-600">{{ $type->name }}</span>
                                    <span class="text-sm font-bold text-slate-800">{{ $type->total_days + 0 }} วัน</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Department Stats & Top Leave Takers -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                
                <!-- Department Leave Stats -->
                <div class="lg:col-span-2 bg-white rounded-[2rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/50 to-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 text-white flex items-center justify-center shadow-lg">
                                    <i data-lucide="building-2" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-xl">อัตราการลาแยกตามแผนก</h3>
                                    <p class="text-sm text-slate-400">จำนวนวันลารวม</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <canvas id="departmentChart" height="250"></canvas>
                    </div>
                </div>

                <!-- Top Leave Takers -->
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-rose-50/50 to-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-orange-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg">ลาบ่อยที่สุด</h3>
                                <p class="text-xs text-slate-400">TOP 10</p>
                            </div>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-50 max-h-[400px] overflow-y-auto">
                        @forelse($topLeaveTakers as $index => $employee)
                            <div class="p-4 hover:bg-slate-50 transition-colors flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full {{ $index < 3 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-800 truncate">{{ $employee->rank }}{{ $employee->name }}</div>
                                    <div class="text-xs text-slate-400 truncate">{{ $employee->department }}</div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-lg font-bold text-rose-600">{{ $employee->total_days + 0 }}</div>
                                    <div class="text-xs text-slate-400">วัน ({{ $employee->leave_count }} ครั้ง)</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                <p>ไม่พบข้อมูล</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Today On Leave & Pending Requests -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Today On Leave -->
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-emerald-50/50 to-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="calendar-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg">กำลังลาวันนี้</h3>
                                <p class="text-xs text-slate-400">{{ now()->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold">
                            {{ $todayOnLeave->count() }} คน
                        </span>
                    </div>
                    <div class="divide-y divide-slate-50 max-h-[350px] overflow-y-auto">
                        @forelse($todayOnLeave as $leave)
                            @php
                                $style = match ($leave->leaveType->slug) {
                                    'sick' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'icon' => 'thermometer'],
                                    'vacation' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'palmtree'],
                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'briefcase'],
                                };
                            @endphp
                            <div class="p-4 hover:bg-slate-50 transition-colors flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl {{ $style['bg'] }} {{ $style['text'] }} flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $style['icon'] }}" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-800 truncate">{{ $leave->user->rank }}{{ $leave->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $leave->user->department }}</div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="text-sm font-semibold {{ $style['text'] }}">{{ $leave->leaveType->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $leave->total_days + 0 }} วัน</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500"></i>
                                </div>
                                <h4 class="font-bold text-slate-800 mb-1">กำลังพลปฏิบัติงานครบ</h4>
                                <p class="text-sm text-slate-400">ไม่มีใครลาในวันนี้</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-amber-50/50 to-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="hourglass" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg">รอการอนุมัติ</h3>
                                <p class="text-xs text-slate-400">คำขอล่าสุด</p>
                            </div>
                        </div>
                        <a href="{{ route('approvals.index') }}" class="px-4 py-1.5 rounded-full bg-amber-100 text-amber-700 text-sm font-bold hover:bg-amber-200 transition-colors">
                            ดูทั้งหมด →
                        </a>
                    </div>
                    <div class="divide-y divide-slate-50 max-h-[350px] overflow-y-auto">
                        @forelse($recentRequests as $request)
                            @php
                                $statusLabel = match ($request->status) {
                                    'pending_supervisor' => 'รอหัวหน้า',
                                    'pending_head' => 'รอหัวหน้าแผนก',
                                    'pending_manager' => 'รอผู้จัดการ',
                                    'pending_deputy_director' => 'รอรองผอ.',
                                    'pending_director' => 'รอผอ.',
                                    default => 'รอดำเนินการ'
                                };
                            @endphp
                            <div class="p-4 hover:bg-slate-50 transition-colors flex items-center gap-4 cursor-pointer" onclick="window.location='{{ route('leave-request.show', $request->id) }}'">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0 font-bold">
                                    {{ mb_substr($request->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-800 truncate">{{ $request->user->rank }}{{ $request->user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $request->leaveType->name }} • {{ $request->total_days + 0 }} วัน</div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 text-xs font-bold animate-pulse">
                                        {{ $statusLabel }}
                                    </span>
                                    <div class="text-xs text-slate-400 mt-1">{{ $request->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-8 h-8 text-emerald-500"></i>
                                </div>
                                <h4 class="font-bold text-slate-800 mb-1">ไม่มีคำขอค้างอนุมัติ</h4>
                                <p class="text-sm text-slate-400">รายการทั้งหมดได้รับการดำเนินการแล้ว</p>
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
            document.addEventListener('DOMContentLoaded', function() {
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
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                            },
                            {
                                label: 'ลาป่วย',
                                data: monthlyData.map(d => d.sick),
                                borderColor: '#f43f5e',
                                backgroundColor: 'rgba(244, 63, 94, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                            },
                            {
                                label: 'ลากิจ',
                                data: monthlyData.map(d => d.personal),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: { weight: 'bold' }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: { font: { weight: 'bold' } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: 'bold' } }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });

                // Leave Type Distribution Chart (Doughnut)
                const leaveTypeCtx = document.getElementById('leaveTypeChart').getContext('2d');
                const leaveTypeData = @json($leaveTypeDistribution);
                
                const colorMap = {
                    'vacation': '#3b82f6',
                    'sick': '#f43f5e',
                    'personal': '#f59e0b',
                    'temporary': '#8b5cf6',
                    'official_duty': '#10b981'
                };

                new Chart(leaveTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: leaveTypeData.map(d => d.name),
                        datasets: [{
                            data: leaveTypeData.map(d => d.total_days),
                            backgroundColor: leaveTypeData.map(d => colorMap[d.slug] || '#94a3b8'),
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '60%',
                        plugins: {
                            legend: { display: false }
                        }
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
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderRadius: 8,
                            maxBarThickness: 50
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: { font: { weight: 'bold' } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { 
                                    font: { weight: 'bold', size: 11 },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });

                // Re-init Lucide icons
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        </script>
    @endpush
</x-app-layout>

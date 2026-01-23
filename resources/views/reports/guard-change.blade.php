<x-app-layout>
    @section('title', 'รายงานการเปลี่ยนยาม (Guard Change Reports)')

    <div class="min-h-screen bg-[#f8fafc] pb-20">
        <!-- Cinematic Analytics Header -->
        <div class="relative bg-slate-900 pt-16 pb-32 overflow-hidden">
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-500/10 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-500/10 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03]"></div>
            </div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                            </span>
                            <span class="text-indigo-400 font-black tracking-[0.2em] uppercase text-sm">Guard Change Analytics</span>
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight">
                            รายงานการเปลี่ยนยาม
                        </h1>
                        <p class="text-slate-400 mt-4 max-w-2xl text-lg font-medium leading-relaxed">
                            ระบบวิเคราะห์ข้อมูลและติดตามสถานะการเปลี่ยนเวรยามแบบเรียลไทม์ 
                            ช่วยในการตรวจสอบความถูกต้องและประสิทธิภาพการปฏิบัติหน้าที่
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="button" onclick="window.print()" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl backdrop-blur-md border border-white/10 transition-all font-black uppercase tracking-widest text-xs flex items-center gap-3 group">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            Print Analysis
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                            <i data-lucide="layers" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Requests</span>
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-4">{{ number_format($stats['total']) }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                            <i data-lucide="check-circle" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Approved</span>
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-4">{{ number_format($stats['approved']) }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
                            <i data-lucide="clock" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">In Progress</span>
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-4">{{ number_format($stats['pending']) }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-amber-400 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-all duration-500">
                            <i data-lucide="x-circle" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rejected</span>
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mb-4">{{ number_format($stats['rejected']) }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['total'] > 0 ? ($stats['rejected'] / $stats['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Layout -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8 sticky top-24">
                        <div class="flex items-center gap-3 mb-8">
                            <i data-lucide="sliders-horizontal" class="w-5 h-5 text-indigo-500"></i>
                            <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">Analytics Filters</h3>
                        </div>

                        <form method="GET" action="{{ route('reports.guard-change') }}" class="space-y-6">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2">Date Frame</label>
                                <div class="grid grid-cols-1 gap-3">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2">Department Context</label>
                                <select name="department" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="">Global Overview</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] px-2">Status Tracking</label>
                                <select name="status" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="">Full Cycle</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Awaiting Replacement</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Replacement Confirmed</option>
                                    <option value="director_approved" {{ request('status') == 'director_approved' ? 'selected' : '' }}>Executive Reviewed</option>
                                    <option value="fully_approved" {{ request('status') == 'fully_approved' ? 'selected' : '' }}>Final Approval</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div class="pt-6 flex flex-col gap-3">
                                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-slate-900/20 hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all">
                                    Refresh Report
                                </button>
                                <a href="{{ route('reports.guard-change') }}" class="w-full py-4 bg-white border-2 border-slate-100 text-slate-400 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-50 text-center transition-all">
                                    Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Main Content Table -->
                <div class="flex-1 min-w-0">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50/80 border-b border-slate-100">
                                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Asset Holders</th>
                                        <th class="px-8 py-6 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Assignment Context</th>
                                        <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Current State</th>
                                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Transaction Date</th>
                                        <th class="px-8 py-6 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Doc</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($requests as $req)
                                        <tr class="group hover:bg-slate-50/50 transition-colors">
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex flex-col gap-5">
                                                    <!-- Requester -->
                                                    <div class="flex items-center gap-3">
                                                        <div class="relative">
                                                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-xs font-black shadow-sm overflow-hidden ring-2 ring-white">
                                                                @if($req->user->avatar)
                                                                    <img src="{{ asset('storage/'.$req->user->avatar) }}" class="w-full h-full object-cover">
                                                                @else
                                                                    {{ substr($req->user->name, 0, 1) }}
                                                                @endif
                                                            </div>
                                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-rose-500 rounded-full border-2 border-white flex items-center justify-center">
                                                                <i data-lucide="arrow-right" class="w-2 h-2 text-white"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-black text-slate-900">{{ $req->user->name }}</p>
                                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $req->user->department }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Replacement -->
                                                    <div class="flex items-center gap-3 pl-6 border-l-2 border-emerald-100 ml-5">
                                                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-[10px] font-black text-emerald-600 shadow-sm overflow-hidden border border-emerald-100">
                                                            @if($req->replacementUser->avatar)
                                                                <img src="{{ asset('storage/'.$req->replacementUser->avatar) }}" class="w-full h-full object-cover">
                                                            @else
                                                                {{ substr($req->replacementUser->name, 0, 1) }}
                                                            @endif
                                                        </div>
                                                        <p class="text-xs font-bold text-slate-600 truncate max-w-[120px]">{{ $req->replacementUser->name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                @php
                                                    $dutyPositions = [
                                                        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                                        'duty_officer' => 'นายทหารเวร',
                                                        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                                    ];
                                                @endphp
                                                <div class="space-y-2">
                                                    <span class="inline-flex px-3 py-1 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">
                                                        {{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}
                                                    </span>
                                                    <div class="flex items-center gap-2 text-sm font-black text-slate-700">
                                                        <i data-lucide="calendar" class="w-4 h-4 text-indigo-500"></i>
                                                        {{ $req->duty_date->locale('th')->translatedFormat('d F Y') }}
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                                @php
                                                    $statusConfig = match($req->status) {
                                                        'fully_approved', 'final_approved' => ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติเรียบร้อย', 'icon' => 'check-circle'],
                                                        'director_approved' => ['bg' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'dot' => 'bg-indigo-500', 'label' => 'รอง ผอ. อนุมัติ', 'icon' => 'shield-check'],
                                                        'approved' => ['bg' => 'bg-blue-50 text-blue-600 border-blue-100', 'dot' => 'bg-blue-500', 'label' => 'รับเรื่องแทนแล้ว', 'icon' => 'user-check'],
                                                        'rejected' => ['bg' => 'bg-rose-50 text-rose-600 border-rose-100', 'dot' => 'bg-rose-500', 'label' => 'ถูกปฏิเสธ', 'icon' => 'x-circle'],
                                                        'cancelled' => ['bg' => 'bg-slate-50 text-slate-400 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิก', 'icon' => 'slash'],
                                                        default => ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'dot' => 'bg-amber-500', 'label' => 'รอตอบรับ', 'icon' => 'clock']
                                                    };
                                                @endphp
                                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl {{ $statusConfig['bg'] }} border shadow-sm">
                                                    <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4"></i>
                                                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $statusConfig['label'] }}</span>
                                                </div>
                                            </td>

                                            <td class="px-8 py-6 whitespace-nowrap text-right">
                                                <div class="space-y-1">
                                                    <p class="text-sm font-black text-slate-900">{{ $req->created_at->locale('th')->translatedFormat('d M Y') }}</p>
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $req->created_at->format('H:i') }} น.</p>
                                                </div>
                                            </td>

                                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                                <a href="{{ route('guard-change.pdf', $req->id) }}" target="_blank" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-all border border-slate-100 hover:border-rose-100 group/pdf">
                                                    <i data-lucide="file-text" class="w-5 h-5 group-hover/pdf:scale-110 transition-transform"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-8 py-32 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mb-6 shadow-inner ring-8 ring-slate-50/50">
                                                        <i data-lucide="search" class="w-10 h-10 text-slate-200"></i>
                                                    </div>
                                                    <h4 class="text-xl font-black text-slate-900 mb-2">มิติข้อมูลว่างเปล่า</h4>
                                                    <p class="text-sm font-medium text-slate-500 max-w-xs mx-auto">ไม่พบข้อมูลที่ตรงตามเงื่อนไขที่ระบุ กรุณาตรวจสอบตัวกรองใหม่</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($requests->hasPages())
                        <div class="mt-10">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

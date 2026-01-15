<x-app-layout>
    @section('title', 'รายงานการเปลี่ยนยาม (Guard Change Reports)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8 relative">
        
        <!-- Decorative Background -->
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-br from-indigo-50/50 to-slate-50/50 -z-10 rounded-b-[3rem]"></div>
        <div class="absolute top-10 right-10 w-72 h-72 bg-purple-100 rounded-full blur-3xl opacity-30 -z-10 animate-pulse"></div>
        <div class="absolute top-20 left-20 w-64 h-64 bg-blue-100 rounded-full blur-3xl opacity-30 -z-10 animate-pulse" style="animation-delay: 1s;"></div>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6 relative z-10">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    </span>
                    <h2 class="text-sm font-bold text-indigo-600 tracking-wider uppercase">Analytics</h2>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">รายงานการเปลี่ยนยาม</h1>
                <p class="text-slate-500 mt-1 text-lg">ติดตามสถานะและวิเคราะห์ข้อมูลการเปลี่ยนเวรยาม</p>
            </div>
            
            <div class="flex gap-3">
                 <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2.5 bg-white border border-slate-200 shadow-sm rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition-all group">
                    <i data-lucide="printer" class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform"></i> พิมพ์รายงาน
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 relative z-10">
            <!-- Total -->
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-lg transition-all border border-slate-50 group hover:-translate-y-1 duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">คำขอทั้งหมด</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ number_format($stats['total']) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                </div>
                <!-- Progress bar -->
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-lg transition-all border border-slate-50 group hover:-translate-y-1 duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">อนุมัติแล้ว</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ number_format($stats['approved']) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-lg transition-all border border-slate-50 group hover:-translate-y-1 duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">กำลังดำเนินการ</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 group-hover:text-amber-500 transition-colors">{{ number_format($stats['pending']) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-amber-400 h-1.5 rounded-full" style="width: {{ $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>

            <!-- Rejected -->
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-lg transition-all border border-slate-50 group hover:-translate-y-1 duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-slate-500 text-sm font-medium mb-1">ยกเลิก/ปฏิเสธ</p>
                        <h3 class="text-3xl font-extrabold text-slate-800 group-hover:text-rose-600 transition-colors">{{ number_format($stats['rejected']) }}</h3>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-rose-400 h-1.5 rounded-full" style="width: {{ $stats['total'] > 0 ? ($stats['rejected'] / $stats['total']) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Filter & Content Layout -->
        <div class="flex flex-col lg:flex-row gap-6 relative z-10">
            
            <!-- Left: Filters Panel (Sticky on Desktop) -->
            <div class="lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-24">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-50 mb-4">
                        <i data-lucide="sliders-horizontal" class="w-5 h-5 text-slate-400"></i>
                        <h3 class="font-bold text-slate-800">ตัวกรองข้อมูล</h3>
                    </div>

                    <form method="GET" action="{{ route('reports.guard-change') }}" id="filter-form" class="space-y-5">
                        <!-- Date Range -->
                        <div class="space-y-3">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">ช่วงวันที่</label>
                            
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                </div>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                       class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder:text-slate-400 hover:bg-slate-100">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 pointer-events-none">เริ่ม</span>
                            </div>
                            
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                                </div>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                       class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all placeholder:text-slate-400 hover:bg-slate-100">
                                <span class="absolute right-3 top-2.5 text-xs text-slate-400 pointer-events-none">สิ้นสุด</span>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">แผนก</label>
                            <div class="relative">
                                <select name="department" class="block w-full pl-3 pr-10 py-2.5 bg-slate-50 border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all appearance-none cursor-pointer hover:bg-slate-100 text-slate-600">
                                    <option value="">ทุกแผนก</option>
                                    @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">สถานะ</label>
                            <div class="relative">
                                <select name="status" class="block w-full pl-3 pr-10 py-2.5 bg-slate-50 border-transparent rounded-xl text-sm font-medium focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all appearance-none cursor-pointer hover:bg-slate-100 text-slate-600">
                                    <option value="">ทั้งหมด</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอผู้เปลี่ยนแทนยืนยัน</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>ผู้เปลี่ยนแทนยืนยันแล้ว</option>
                                    <option value="director_approved" {{ request('status') == 'director_approved' ? 'selected' : '' }}>รอง ผอ. อนุมัติแล้ว</option>
                                    <option value="fully_approved" {{ request('status') == 'fully_approved' ? 'selected' : '' }}>ผอ. อนุมัติแล้ว</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธ</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="pt-4 flex flex-col gap-3">
                            <button type="submit" class="w-full flex items-center justify-center py-3 bg-slate-900 text-white rounded-xl font-bold shadow-lg shadow-slate-900/10 hover:shadow-slate-900/20 hover:-translate-y-0.5 transition-all">
                                <i data-lucide="search" class="w-4 h-4 mr-2"></i> ค้นหา
                            </button>
                            <a href="{{ route('reports.guard-change') }}" class="w-full flex items-center justify-center py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 hover:text-slate-900 transition-all">
                                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i> ล้างค่า
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right: Table Results -->
            <div class="flex-1 min-w-0">
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-sm border border-white/20 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/80">
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ผู้ขอ / ผู้รับช่วง</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ตำแหน่ง / วันที่เวร</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">สถานะ</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">วันที่ทำรายการ</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">เอกสาร</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white/50">
                                @forelse($requests as $req)
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <!-- User Pair -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col gap-4">
                                            <!-- Requester -->
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-9 w-9 relative">
                                                    @if($req->user->avatar)
                                                        <img class="h-9 w-9 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ asset('storage/'.$req->user->avatar) }}" alt="">
                                                    @else
                                                        <div class="h-9 w-9 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-bold border-2 border-white shadow-sm text-xs">
                                                            {{ substr($req->user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5">
                                                        <div class="bg-rose-100 p-0.5 rounded-full">
                                                            <i data-lucide="arrow-right" class="w-2.5 h-2.5 text-rose-500"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-bold text-slate-900">{{ $req->user->name }}</div>
                                                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wide">{{ $req->user->department }}</div>
                                                </div>
                                            </div>

                                            <!-- Replacement -->
                                            <div class="flex items-center pl-4 border-l-2 border-slate-100">
                                                <div class="flex-shrink-0 h-8 w-8 relative">
                                                    @if($req->replacementUser->avatar)
                                                        <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white shadow-sm" src="{{ asset('storage/'.$req->replacementUser->avatar) }}" alt="">
                                                    @else
                                                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center text-emerald-600 font-bold border-2 border-white shadow-sm text-xs">
                                                            {{ substr($req->replacementUser->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-xs font-bold text-slate-700">{{ $req->replacementUser->name }}</div>
                                                    <span class="text-[10px] text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded font-medium">ผู้มาแทน</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Duty Info -->
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col gap-2">
                                            @php
                                                $dutyPositions = [
                                                    'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                                    'duty_officer' => 'นายทหารเวร',
                                                    'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                                ];
                                            @endphp
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100/50">
                                                    <i data-lucide="shield" class="w-3 h-3 mr-1.5"></i>
                                                    {{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}
                                                </span>
                                            </div>
                                            <div class="flex items-center text-sm font-bold text-slate-700">
                                                <i data-lucide="calendar-days" class="w-4 h-4 mr-2 text-slate-400"></i>
                                                @thaidatefull($req->duty_date)
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        @php
                                            $statusConfig = match($req->status) {
                                                'fully_approved', 'final_approved' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติเสร็จสิ้น', 'icon' => 'check-circle-2'],
                                                'director_approved' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-100', 'dot' => 'bg-blue-500', 'label' => 'รอง ผอ. อนุมัติ', 'icon' => 'check-circle'],
                                                'approved' => ['bg' => 'bg-cyan-50 text-cyan-700 border-cyan-100', 'dot' => 'bg-cyan-500', 'label' => 'ผู้แทนรับทราบ', 'icon' => 'user-check'],
                                                'rejected' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-100', 'dot' => 'bg-rose-500', 'label' => 'ถูกปฏิเสธ', 'icon' => 'x-circle'],
                                                'cancelled' => ['bg' => 'bg-slate-50 text-slate-500 border-slate-100', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิกแล้ว', 'icon' => 'slash'],
                                                default => ['bg' => 'bg-amber-50 text-amber-700 border-amber-100', 'dot' => 'bg-amber-500', 'label' => 'รอยืนยัน', 'icon' => 'clock']
                                            };
                                        @endphp
                                        <div class="inline-flex flex-col items-center gap-1">
                                            <span class="inline-flex items-center gap-1.5 pl-2 pr-3 py-1 rounded-full text-xs font-bold border {{ $statusConfig['bg'] }}">
                                                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-3.5 h-3.5"></i>
                                                {{ $statusConfig['label'] }}
                                            </span>
                                            @if($req->status == 'pending')
                                                <span class="text-[10px] text-amber-500 font-medium">รอผู้มาแทนตอบรับ</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Created At -->
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <div class="flex flex-col items-end">
                                            <span class="text-sm font-bold text-slate-700">@thaidate($req->created_at)</span>
                                            <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                @thaitime($req->created_at)
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-5 whitespace-nowrap text-center">
                                        <a href="{{ route('guard-change.pdf', $req->id) }}" target="_blank" class="group/btn inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all border border-slate-100 hover:border-red-100" title="ดาวน์โหลด PDF">
                                            <i data-lucide="file-down" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                                <i data-lucide="search-x" class="w-8 h-8 text-slate-300"></i>
                                            </div>
                                            <h3 class="text-slate-900 font-bold text-lg mb-1">ไม่พบข้อมูล</h3>
                                            <p class="text-sm text-slate-500">ลองปรับตัวกรองวันหรือสถานะเพื่อค้นหาใหม่อีกครั้ง</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($requests->hasPages())
                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

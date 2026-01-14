<x-app-layout>
    @section('title', 'รายงานสรุปการลา (Leave Reports)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-6 relative">
            <div class="relative z-10 p-2">
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-brand-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white flex items-center justify-center shadow-lg shadow-brand-500/30">
                        <i data-lucide="pie-chart" class="w-3.5 h-3.5"></i>
                    </span>
                    รายงานสรุปการลา
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">วิเคราะห์และติดตามข้อมูลการลาของบุคลากร</p>
            </div>
            
             <button type="submit" form="filter-form" formaction="{{ route('reports.export') }}" class="group inline-flex items-center px-5 py-3 bg-white border border-slate-200 rounded-2xl text-slate-600 font-bold hover:bg-slate-50 hover:text-brand-600 hover:border-brand-200 shadow-sm transition-all duration-300 transform hover:-translate-y-1">
                <div class="mr-2 bg-slate-100 p-1.5 rounded-lg group-hover:bg-brand-50 transition-colors">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-green-600"></i>
                </div>
                Export Excel
            </button>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-brand-100/50 flex items-center justify-center text-brand-600">
                    <i data-lucide="sliders" class="w-4 h-4 text-sm"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">ตัวกรองข้อมูล (Filters)</h3>
            </div>
            
            <div class="p-8">
                <form method="GET" action="{{ route('reports.index') }}" id="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <!-- Date Range -->
                        <div class="col-span-2 grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div class="group">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">ตั้งแต่วันที่</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full pl-10 pr-3 py-2 border-slate-200 rounded-xl text-sm font-medium bg-white focus:border-brand-500 focus:ring-brand-500 shadow-sm">
                                </div>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">ถึงวันที่</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full pl-10 pr-3 py-2 border-slate-200 rounded-xl text-sm font-medium bg-white focus:border-brand-500 focus:ring-brand-500 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="group">
                             <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">พนักงาน / แผนก</label>
                             <div class="relative">
                                <select name="department" class="block w-full pl-3 pr-10 py-3 border-slate-200 rounded-xl text-sm font-medium bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 transition-colors appearance-none">
                                     <option value="">ทุกแผนก</option>
                                     @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                     @endforeach
                                 </select>
                                 <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i data-lucide="chevron-down" class="w-3 h-3 text-xs text-slate-400"></i>
                                 </div>
                             </div>
                        </div>

                        <!-- Leave Type -->
                        <div class="group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">ประเภทการลา</label>
                            <div class="relative">
                                <select name="leave_type_id" class="block w-full pl-3 pr-10 py-3 border-slate-200 rounded-xl text-sm font-medium bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 transition-colors appearance-none">
                                    <option value="">ทั้งหมด</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i data-lucide="chevron-down" class="w-3 h-3 text-xs text-slate-400"></i>
                                 </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">สถานะ</label>
                            <div class="relative">
                                <select name="status" class="block w-full pl-3 pr-10 py-3 border-slate-200 rounded-xl text-sm font-medium bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-brand-500 transition-colors appearance-none">
                                    <option value="">ทั้งหมด</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                                    <option value="pending_supervisor" {{ request('status') == 'pending_supervisor' ? 'selected' : '' }}>รอหัวหน้างาน</option>
                                    <option value="pending_head" {{ request('status') == 'pending_head' ? 'selected' : '' }}>รอหัวหน้าแผนก</option>
                                     <option value="pending_deputy_director" {{ request('status') == 'pending_deputy_director' ? 'selected' : '' }}>รอ รอง ผอ.</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธ</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i data-lucide="chevron-down" class="w-3 h-3 text-xs text-slate-400"></i>
                                 </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-50">
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center px-6 py-3 text-sm font-bold text-slate-500 hover:text-brand-600 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 transition-all duration-200">
                             <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2 text-xs"></i> ล้างค่า
                        </a>
                        <button type="submit" class="inline-flex items-center px-8 py-3 text-sm font-bold text-white bg-slate-900 hover:bg-brand-600 rounded-xl shadow-lg hover:shadow-brand-500/30 transition-all duration-300 transform hover:-translate-y-1">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i> ค้นหาข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Top Leave Takers Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative group">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full blur-3xl opacity-40 -mr-20 -mt-20 group-hover:opacity-60 transition-opacity"></div>
                
                <div class="p-6 border-b border-slate-50 bg-gradient-to-r from-amber-50 to-orange-50 flex items-center gap-3 relative">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/30">
                        <i data-lucide="trophy" class="w-4 h-4 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">ผู้ใช้สิทธิ์ลามากที่สุด</h3>
                        <p class="text-xs text-slate-500">Top 5 ลาบ่อยที่สุด (อนุมัติแล้ว)</p>
                    </div>
                </div>
                
                <div class="p-4 relative">
                    @if(!isset($topLeavers) || $topLeavers->isEmpty())
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="user-x" class="w-6 h-6 text-slate-300"></i>
                            </div>
                            <p class="text-slate-400 text-sm">ยังไม่มีข้อมูล</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($topLeavers as $index => $leaver)
                            <div class="flex items-center gap-4 p-3 rounded-2xl {{ $index === 0 ? 'bg-gradient-to-r from-amber-50 to-orange-50 ring-1 ring-amber-100' : 'hover:bg-slate-50' }} transition-colors">
                                <!-- Rank Badge -->
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-black text-sm
                                    {{ $index === 0 ? 'bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-500/30' : '' }}
                                    {{ $index === 1 ? 'bg-slate-200 text-slate-600' : '' }}
                                    {{ $index === 2 ? 'bg-amber-600/20 text-amber-700' : '' }}
                                    {{ $index > 2 ? 'bg-slate-100 text-slate-500' : '' }}">
                                    {{ $index + 1 }}
                                </div>
                                
                                <!-- User Info -->
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    @if($leaver->user->avatar)
                                        <img src="{{ asset('storage/'.$leaver->user->avatar) }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-md">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-brand-600 font-bold border-2 border-white shadow-md">
                                            {{ substr($leaver->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900 text-sm truncate">{{ $leaver->user->rank }} {{ $leaver->user->name }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ $leaver->user->department }}</p>
                                    </div>
                                </div>
                                
                                <!-- Stats -->
                                <div class="flex-shrink-0 text-right">
                                    <p class="font-black text-lg {{ $index === 0 ? 'text-amber-600' : 'text-slate-700' }}">{{ number_format($leaver->total_leave_days, 0) }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">วัน</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Popular Leave Types Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative group">
                <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full blur-3xl opacity-40 -mr-20 -mt-20 group-hover:opacity-60 transition-opacity"></div>
                
                <div class="p-6 border-b border-slate-50 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center gap-3 relative">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">ประเภทการลายอดนิยม</h3>
                        <p class="text-xs text-slate-500">สถิติการใช้สิทธิ์ลาแต่ละประเภท</p>
                    </div>
                </div>
                
                <div class="p-4 relative">
                    @if(!isset($popularLeaveTypes) || $popularLeaveTypes->isEmpty())
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="bar-chart-2" class="w-5 h-5 text-slate-300 text-xl"></i>
                            </div>
                            <p class="text-slate-400 text-sm">ยังไม่มีข้อมูล</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @php
                                $maxUsage = $popularLeaveTypes->max('usage_count');
                                $colors = [
                                    'sick' => ['bg' => 'bg-orange-500', 'light' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'stethoscope'],
                                    'vacation' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'plane'],
                                    'personal' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'briefcase'],
                                ];
                            @endphp
                            @foreach($popularLeaveTypes as $index => $type)
                            @php
                                $typeSlug = $type->leaveType->slug ?? 'default';
                                $color = $colors[$typeSlug] ?? ['bg' => 'bg-slate-500', 'light' => 'bg-slate-100', 'text' => 'text-slate-600', 'icon' => 'calendar'];
                                $percentage = $maxUsage > 0 ? ($type->usage_count / $maxUsage) * 100 : 0;
                            @endphp
                            <div class="p-3 rounded-2xl {{ $index === 0 ? 'bg-gradient-to-r from-blue-50 to-indigo-50 ring-1 ring-blue-100' : 'hover:bg-slate-50' }} transition-colors">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded-lg {{ $color['light'] }} {{ $color['text'] }} flex items-center justify-center">
                                        <i data-lucide="{{ $color['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-900 text-sm">{{ $type->leaveType->name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg {{ $color['light'] }} {{ $color['text'] }} text-xs font-bold">
                                            <span>{{ $type->usage_count }}</span>
                                            <span class="text-[10px] opacity-70">ครั้ง</span>
                                        </span>
                                    </div>
                                </div>
                                <!-- Progress Bar -->
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $color['bg'] }} rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="flex justify-between mt-1.5">
                                    <span class="text-[10px] text-slate-400 font-medium">รวม {{ number_format($type->total_days, 0) }} วัน</span>
                                    @if($index === 0)
                                        <span class="text-[10px] text-blue-600 font-bold flex items-center gap-1">
                                            <i data-lucide="crown" class="w-4 h-4 text-[8px]"></i> ยอดนิยมอันดับ 1
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative">
             <!-- Decor -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 align-middle">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-8 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">พนักงาน</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ประเภท</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ช่วงเวลาที่ลา</th>
                            <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">จำนวน</th>
                            <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">สถานะ</th>
                            <th scope="col" class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">วันที่ทำรายการ</th>
                             <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">เอกสาร</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse($requests as $req)
                        @php $isCancelled = $req->status === 'cancelled'; @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors duration-150 group {{ $isCancelled ? 'opacity-60' : '' }}">
                            <td class="px-8 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11 relative">
                                        @if($req->user->avatar)
                                            <img class="h-11 w-11 rounded-full object-cover border-2 border-white shadow-md group-hover:scale-105 transition-transform" src="{{ asset('storage/'.$req->user->avatar) }}" alt="">
                                        @else
                                            <div class="h-11 w-11 rounded-full bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-brand-600 font-bold border-2 border-white shadow-md group-hover:scale-105 transition-transform">
                                                {{ substr($req->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="absolute -bottom-1 -right-1 bg-white rounded-full p-0.5">
                                             <div class="w-3 h-3 bg-green-500 rounded-full border border-white"></div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-slate-900 {{ $isCancelled ? 'line-through decoration-red-400' : '' }}">{{ $req->user->name }}</div>
                                        <div class="text-xs font-medium text-slate-500 bg-slate-100 inline-block px-1.5 py-0.5 rounded mt-1">{{ $req->user->department }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                 <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $isCancelled ? 'line-through decoration-red-400' : '' }}
                                    {{ $req->leaveType->slug == 'sick' ? 'bg-orange-50 text-orange-600 border-orange-100' : '' }}
                                    {{ $req->leaveType->slug == 'vacation' ? 'bg-blue-50 text-blue-600 border-blue-100' : '' }}
                                    {{ $req->leaveType->slug == 'personal' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-slate-50 text-slate-600 border-slate-100' }}">
                                    @if($req->leaveType->slug == 'sick') <i data-lucide="stethoscope" class="w-4 h-4 mr-1.5"></i>
                                    @elseif($req->leaveType->slug == 'vacation') <i data-lucide="plane" class="w-5 h-5 mr-1.5"></i>
                                    @else <i data-lucide="briefcase" class="w-4 h-4 mr-1.5"></i>
                                    @endif
                                    {{ $req->leaveType->name }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm text-slate-800 font-bold {{ $isCancelled ? 'line-through decoration-red-400' : '' }}">@thaidatefull($req->start_date)</div>
                                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1 {{ $isCancelled ? 'line-through decoration-red-400' : '' }}">
                                    <i data-lucide="arrow-right" class="w-4 h-4 text-[10px] text-slate-300"></i> @thaidatefull($req->end_date)
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <span class="inline-block w-8 h-8 leading-8 rounded-full bg-slate-100 text-slate-600 text-xs font-bold {{ $isCancelled ? 'line-through decoration-red-400' : '' }}">
                                    {{ $req->total_days + 0 }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                @php
                                    $statusConfig = match($req->status) {
                                        'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติแล้ว'],
                                        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'dot' => 'bg-rose-500', 'label' => 'ถูกปฏิเสธ'],
                                        'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิกแล้ว'],
                                        default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'รออนุมัติ']
                                    };
                                    
                                    if(in_array($req->status, ['pending_supervisor', 'pending_head', 'pending_deputy_director'])) {
                                         $statusConfig['label'] = 'รออนุมัติ';
                                    }
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusConfig['dot'] }} animate-pulse"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                             <td class="px-8 py-5 whitespace-nowrap text-right">
                                <span class="text-sm font-medium text-slate-600 {{ $isCancelled ? 'line-through decoration-red-400' : '' }}">@thaidatefull($req->created_at)</span>
                                <div class="text-xs text-slate-400 mt-0.5 {{ $isCancelled ? 'line-through decoration-red-400' : '' }}">@thaitime($req->created_at) น.</div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <a href="{{ route('leave-request.pdf', $req->id) }}" target="_blank" class="text-slate-400 hover:text-brand-600 transition-colors p-2 rounded-lg hover:bg-slate-50 inline-block" title="ดาวน์โหลด PDF">
                                    <i data-lucide="file-text" class="w-4 h-4 text-xl"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center relative">
                                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent to-slate-50 rounded-full blur-2xl opacity-50"></div>
                                    <div class="h-20 w-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 relative z-10">
                                        <i data-lucide="folder-open" class="w-6 h-6 text-3xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-slate-900 font-bold text-lg mb-1 relative z-10">ไม่พบข้อมูลใบลา</h3>
                                    <p class="text-sm text-slate-400 relative z-10">ลองปรับเปลี่ยนตัวกรองวันหรือสถานะเพื่อค้นหาใหม่</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($requests->hasPages())
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

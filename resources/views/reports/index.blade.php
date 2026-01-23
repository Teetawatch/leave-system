<x-app-layout>
    @section('title', 'รายงานสรุปการลา')

    <div class="min-h-screen bg-[#f8fafc] pb-20">
        <!-- Bright Cinematic Analytics Header -->
        <div class="relative bg-white pt-16 pb-32 overflow-hidden border-b border-slate-100">
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
            </div>

            <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                <i data-lucide="line-chart" class="w-6 h-6"></i>
                            </span>
                            <span class="text-emerald-600 font-bold tracking-widest uppercase text-sm">สถิติวิเคราะห์การลา</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight">
                            รายงานสรุปการลา
                        </h1>
                        <p class="text-slate-500 mt-4 max-w-2xl text-lg font-medium leading-relaxed">
                            ระบบรวบรวมและวิเคราะห์สถิติจัดการทรัพยากรบุคคล 
                            สำหรับการติดตามสิทธิ์และประวัติการใช้ใบลาภาพรวมขององค์กร
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" form="filter-form" formaction="{{ route('reports.export') }}"
                            class="px-8 py-4 bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-500 hover:text-white rounded-2xl transition-all font-bold uppercase tracking-widest text-xs flex items-center gap-3 group shadow-sm">
                            <i data-lucide="download" class="w-5 h-5 group-hover:-translate-y-1 transition-transform"></i>
                            ส่งออกรายงาน
                        </button>
                        <button type="button" onclick="window.print()" class="px-8 py-4 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-2xl shadow-sm transition-all font-bold uppercase tracking-widest text-xs flex items-center gap-3 group">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            พิมพ์รายงาน
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <!-- Total Requests -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                            <i data-lucide="files" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">รายการทั้งหมด</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-4">{{ $requests->total() }}</h3>
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
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">อนุมัติแล้ว</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-4">{{ \App\Models\LeaveRequest::where('status', 'approved')->count() }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $requests->total() > 0 ? (\App\Models\LeaveRequest::where('status', 'approved')->count() / max($requests->total(), 1)) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
                            <i data-lucide="clock" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">รอตรวจสอบ</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-4">{{ \App\Models\LeaveRequest::whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director'])->count() }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-amber-400 h-full rounded-full transition-all duration-1000" style="width: {{ $requests->total() > 0 ? (\App\Models\LeaveRequest::whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director'])->count() / max($requests->total(), 1)) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- Rejected -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-all duration-500">
                            <i data-lucide="x-circle" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ปฏิเสธ/ยกเลิก</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-4">{{ \App\Models\LeaveRequest::whereIn('status', ['rejected', 'cancelled'])->count() }}</h3>
                    <div class="w-full bg-slate-50 rounded-full h-2 overflow-hidden ring-4 ring-slate-50/50">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-1000" style="width: {{ $requests->total() > 0 ? (\App\Models\LeaveRequest::whereIn('status', ['rejected', 'cancelled'])->count() / max($requests->total(), 1)) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Data Filter Console -->
                <div class="lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8 sticky top-24">
                        <div class="flex items-center gap-3 mb-8">
                            <i data-lucide="sliders" class="w-5 h-5 text-emerald-500"></i>
                            <h3 class="font-bold text-slate-900 uppercase tracking-widest text-xs">แผงควบคุมการกรองข้อมูล</h3>
                        </div>

                        <form method="GET" action="{{ route('reports.index') }}" id="filter-form" class="space-y-6">
                            <!-- Date Frames -->
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-2">ช่วงเวลา</label>
                                <div class="grid grid-cols-1 gap-3">
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                </div>
                            </div>

                            <!-- Department Context -->
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-2">หน่วยงาน/แผนก</label>
                                <select name="department" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="">ทุกหน่วยงาน</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Leave Metadata -->
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-2">ประเภทการลา</label>
                                <select name="leave_type_id" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="">ทุกประเภท</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Transaction Status -->
                            <div class="space-y-3">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-2">สถานะรายการ</label>
                                <select name="status" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="">ทุกสถานะ</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                                    <option value="pending" {{ str_contains(request('status') ?? '', 'pending') ? 'selected' : '' }}>รอตรวจสอบ</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธ</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                            </div>

                            <div class="pt-6 flex flex-col gap-3">
                                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold uppercase tracking-widest text-xs shadow-xl shadow-slate-900/20 hover:shadow-emerald-500/30 hover:-translate-y-1 transition-all">
                                    กรองข้อมูล
                                </button>
                                <a href="{{ route('reports.index') }}" class="w-full py-4 bg-white border-2 border-slate-100 text-slate-400 rounded-2xl font-bold uppercase tracking-widest text-xs hover:bg-slate-50 text-center transition-all">
                                    ล้างค่า
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Main Matrix & Visualizations -->
                <div class="flex-1 min-w-0 space-y-8">
                    @if(isset($topLeavers) && $topLeavers->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Leaderboard Analysis -->
                            <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-indigo-200/50 group">
                                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
                                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px] -mr-24 -mt-24 group-hover:scale-125 transition-transform duration-[2s]"></div>

                                <div class="flex items-center gap-4 mb-8 relative z-10">
                                    <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10 shadow-lg">
                                        <i data-lucide="award" class="w-8 h-8 text-emerald-400"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold tracking-tight">สถิติการลาสูงสุด</h3>
                                        <p class="text-indigo-300 text-[10px] font-bold uppercase tracking-widest">รายชื่อผู้ที่สถิติการลาสูงสุด</p>
                                    </div>
                                </div>

                                <div class="space-y-6 relative z-10">
                                    @foreach($topLeavers->take(3) as $index => $leaver)
                                        <div class="flex items-center gap-4 group/item">
                                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center font-bold text-indigo-400 border border-white/5 group-hover/item:bg-emerald-500 group-hover/item:text-white transition-all">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-baseline mb-1">
                                                    <p class="font-bold text-sm group-hover/item:translate-x-1 transition-transform">{{ $leaver->user->name }}</p>
                                                    <span class="text-emerald-400 font-bold text-sm">{{ $leaver->total_leave_days }} วัน</span>
                                                </div>
                                                <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                                                    <div class="bg-indigo-400 h-full rounded-full transition-all duration-1000 group-hover/item:bg-emerald-500" style="width: {{ ($leaver->total_leave_days / max($topLeavers->max('total_leave_days'), 1)) * 100 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Distribution Analysis -->
                            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/40 border border-slate-100 group">
                                <div class="flex items-center gap-4 mb-8">
                                    <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center group-hover:bg-indigo-600 transition-all duration-500 shadow-sm border border-slate-50">
                                        <i data-lucide="bar-chart-3" class="w-8 h-8 text-indigo-500 group-hover:text-white transition-all duration-500"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-slate-900 tracking-tight">สัดส่วนการลา</h3>
                                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">ความถี่ตามประเภทการลา</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    @foreach($popularLeaveTypes->take(3) as $type)
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center text-[11px] font-bold uppercase tracking-widest text-slate-500 px-1">
                                                <span>{{ $type->leaveType->name }}</span>
                                                <span class="text-indigo-600">{{ $type->usage_count }} รายการ</span>
                                            </div>
                                            <div class="h-3 bg-slate-50 rounded-full overflow-hidden ring-4 ring-slate-50/50">
                                                <div class="bg-indigo-500 h-full rounded-full group-hover:bg-indigo-600 transition-all duration-[1.5s]" 
                                                     style="width: {{ ($type->usage_count / max($popularLeaveTypes->max('usage_count'), 1)) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Data Grid Container -->
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-left">
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">ประเภท</th>
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">ระยะเวลา</th>
                                        <th class="px-8 py-6 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">จำนวนวัน</th>
                                        <th class="px-8 py-6 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">สถานะ</th>
                                        <th class="px-8 py-6 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">เอกสาร</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($requests as $req)
                                        <tr class="group hover:bg-slate-50/50 transition-colors">
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 rounded-[1.2rem] bg-slate-100 flex items-center justify-center font-bold text-slate-400 text-sm overflow-hidden ring-4 ring-white shadow-sm transition-transform group-hover:scale-110">
                                                        @if($req->user->avatar)
                                                            <img src="{{ asset('storage/' . $req->user->avatar) }}" class="w-full h-full object-cover">
                                                        @else
                                                            {{ substr($req->user->name, 0, 1) }}
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <p class="text-base font-bold text-slate-900 leading-tight">
                                                            {{ $req->user->rank ? $req->user->rank . ' ' : '' }}{{ $req->user->name }}
                                                        </p>
                                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $req->user->department }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <span class="inline-flex px-4 py-1.5 bg-brand-50 text-brand-700 rounded-lg text-xs font-bold uppercase tracking-widest shadow-sm">
                                                    {{ $req->leaveType->name }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="text-base font-bold text-slate-700">@thaidate($req->start_date)</span>
                                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2 mt-1">
                                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                                        ถึง @thaidate($req->end_date)
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center mx-auto transition-transform group-hover:bg-indigo-50 group-hover:border-indigo-100">
                                                    <span class="text-sm font-bold text-slate-800">{{ $req->total_days + 0 }}</span>
                                                    <span class="text-[8px] font-bold text-slate-400 uppercase leading-none">วัน</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                                @php
                                                    $statusConfig = match ($req->status) {
                                                        'approved' => ['bg' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติแล้ว'],
                                                        'rejected' => ['bg' => 'bg-rose-50 text-rose-600 border-rose-100', 'dot' => 'bg-rose-500', 'label' => 'ปฏิเสธ'],
                                                        'cancelled' => ['bg' => 'bg-slate-50 text-slate-400 border-slate-200', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิก'],
                                                        default => ['bg' => 'bg-amber-50 text-amber-600 border-amber-100', 'dot' => 'bg-amber-500', 'label' => 'รอตรวจสอบ']
                                                    };
                                                @endphp
                                                <div class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl {{ $statusConfig['bg'] }} border shadow-sm">
                                                    <span class="relative flex h-2.5 w-2.5">
                                                        <span class="absolute inline-flex rounded-full h-full w-full {{ $statusConfig['dot'] }}"></span>
                                                    </span>
                                                    <span class="text-xs font-bold uppercase tracking-widest">{{ $statusConfig['label'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('leave-request.pdf', $req->id) }}" target="_blank"
                                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all border border-slate-100 hover:border-slate-900"
                                                        title="ใบลา (PDF)">
                                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                                    </a>
                                                    @if($req->leaveType->slug === 'official-duty' && $req->attachment_path)
                                                        <a href="{{ route('storage.file', $req->attachment_path) }}" target="_blank"
                                                            class="w-10 h-10 rounded-xl bg-orange-50 text-orange-400 flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all border border-orange-100 hover:border-orange-500"
                                                            title="เอกสารแนบ">
                                                            <i data-lucide="paperclip" class="w-5 h-5"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-8 py-32 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-8 shadow-inner ring-8 ring-slate-50/50">
                                                        <i data-lucide="database-zap" class="w-12 h-12 text-slate-200"></i>
                                                    </div>
                                                    <h4 class="text-2xl font-bold text-slate-900 mb-2">มิติข้อมูลว่างเปล่า</h4>
                                                    <p class="text-sm font-medium text-slate-500 max-w-sm mx-auto">ไม่พบเอกสารหรือข้อมูลการลาภายใต้เงื่อนไขที่คุณระบุ กรุณาตรวจสอบการกรองข้อมูลอีกครั้ง</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Intelligence Pagination -->
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
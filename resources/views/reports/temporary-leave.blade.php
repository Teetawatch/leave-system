<x-app-layout>
    @section('title', 'รายงานลาชั่วคราว')

    <div class="min-h-screen bg-[#f8fafc] pb-20">
        <!-- Header Section -->
        <div class="relative bg-white pt-16 pb-32 overflow-hidden border-b border-slate-100">
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-purple-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
            </div>

            <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-10">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-12 h-12 rounded-2xl bg-purple-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">
                                <i data-lucide="clock" class="w-6 h-6"></i>
                            </span>
                            <span class="text-purple-600 font-bold tracking-widest uppercase text-sm">สถิติลาชั่วคราว</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight">
                            รายงานลาชั่วคราว
                        </h1>
                        <p class="text-slate-500 mt-4 max-w-2xl text-lg font-medium leading-relaxed">
                            รายงานการลาชั่วคราว (ครึ่งวัน) แสดงรายละเอียดผู้ขอลา ช่วงเวลา สถานที่ และเหตุผล
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
                <!-- Total Requests -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-500">
                            <i data-lucide="files" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">รายการทั้งหมด</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ $totalCount }}</h3>
                    <p class="text-xs text-slate-400 font-medium">รายการ</p>
                </div>

                <!-- Approved -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                            <i data-lucide="check-circle" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">อนุมัติแล้ว</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ $approvedCount }}</h3>
                    <p class="text-xs text-slate-400 font-medium">รายการ</p>
                </div>

                <!-- Pending -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-500">
                            <i data-lucide="hourglass" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">รอตรวจสอบ</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ $pendingCount }}</h3>
                    <p class="text-xs text-slate-400 font-medium">รายการ</p>
                </div>

                <!-- Morning -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-all duration-500">
                            <i data-lucide="sunrise" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ช่วงเช้า</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ $morningCount }}</h3>
                    <p class="text-xs text-slate-400 font-medium">รายการ</p>
                </div>

                <!-- Afternoon -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 group hover:-translate-y-1 transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500">
                            <i data-lucide="sunset" class="w-7 h-7"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ช่วงบ่าย</span>
                    </div>
                    <h3 class="text-4xl font-bold text-slate-900 mb-2">{{ $afternoonCount }}</h3>
                    <p class="text-xs text-slate-400 font-medium">รายการ</p>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="space-y-8">
                <!-- Horizontal Filter Console -->
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <i data-lucide="sliders" class="w-5 h-5 text-purple-500"></i>
                        <h3 class="font-bold text-slate-900 uppercase tracking-widest text-xs">ค้นหาและกรองข้อมูล</h3>
                    </div>

                    <form method="GET" action="{{ route('reports.temporary-leave') }}" id="filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                        <!-- Date Start -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2">ตั้งแต่วันที่</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all">
                        </div>

                        <!-- Date End -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2">ถึงวันที่</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all">
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2">หน่วยงาน/แผนก</label>
                            <select name="department" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all appearance-none cursor-pointer">
                                <option value="">ทุกหน่วยงาน</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Period -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-2">ช่วงเวลา</label>
                            <select name="period" class="block w-full px-4 py-3 bg-slate-50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all appearance-none cursor-pointer">
                                <option value="">ทุกช่วง</option>
                                <option value="morning" {{ request('period') == 'morning' ? 'selected' : '' }}>ช่วงเช้า</option>
                                <option value="afternoon" {{ request('period') == 'afternoon' ? 'selected' : '' }}>ช่วงบ่าย</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-3.5 bg-slate-900 text-white rounded-2xl font-bold uppercase tracking-widest text-[10px] shadow-lg shadow-slate-900/20 hover:shadow-purple-500/30 hover:-translate-y-0.5 transition-all">
                                กรองข้อมูล
                            </button>
                            <a href="{{ route('reports.temporary-leave') }}" class="px-4 py-3.5 bg-white border-2 border-slate-100 text-slate-400 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-slate-50 text-center transition-all">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Main Content Area -->
                <div class="min-w-0 space-y-8">
                    <!-- Data Grid Container -->
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-left">
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">ชื่อ-นามสกุล</th>
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">วันที่ลา</th>
                                        <th class="px-8 py-6 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">ช่วงเวลา</th>
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">สถานที่ไป</th>
                                        <th class="px-8 py-6 text-xs font-bold text-slate-500 uppercase tracking-widest">เหตุผล</th>
                                        <th class="px-8 py-6 text-center text-xs font-bold text-slate-500 uppercase tracking-widest">สถานะ</th>
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
                                                            {{ $req->user->rank ? $req->user->rank . '' : '' }}{{ $req->user->name }}
                                                        </p>
                                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $req->user->department }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap">
                                                <div class="flex flex-col">
                                                    <span class="text-base font-bold text-slate-700">@thaidate($req->start_date)</span>
                                                    <span class="text-xs font-medium text-slate-400 mt-1">
                                                        {{ \Carbon\Carbon::parse($req->created_at)->locale('th')->translatedFormat('H:i น.') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 whitespace-nowrap text-center">
                                                @if($req->temporary_leave_period === 'morning')
                                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-orange-50 text-orange-600 rounded-xl text-xs font-bold uppercase tracking-widest border border-orange-100">
                                                        <i data-lucide="sunrise" class="w-4 h-4"></i>
                                                        ช่วงเช้า
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold uppercase tracking-widest border border-indigo-100">
                                                        <i data-lucide="sunset" class="w-4 h-4"></i>
                                                        ช่วงบ่าย
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="max-w-xs">
                                                    @php
                                                        $address = $req->contact_address;
                                                        $locationDisplay = '-';
                                                        if (is_array($address)) {
                                                            $parts = array_filter([
                                                                $address['house'] ?? null,
                                                                $address['road'] ?? null,
                                                                $address['tambon'] ?? null,
                                                                $address['amphoe'] ?? null,
                                                                $address['province'] ?? null,
                                                            ]);
                                                            $locationDisplay = !empty($parts) ? implode(' ', $parts) : '-';
                                                        } elseif (is_string($address) && !empty($address)) {
                                                            $locationDisplay = $address;
                                                        }
                                                    @endphp
                                                    <p class="text-sm font-medium text-slate-700 truncate" title="{{ $locationDisplay }}">
                                                        {{ Str::limit($locationDisplay, 40) }}
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <div class="max-w-xs">
                                                    <p class="text-sm font-medium text-slate-700 line-clamp-2" title="{{ $req->reason }}">
                                                        {{ $req->reason ?? '-' }}
                                                    </p>
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-8 py-32 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-8 shadow-inner ring-8 ring-slate-50/50">
                                                        <i data-lucide="clock" class="w-12 h-12 text-slate-200"></i>
                                                    </div>
                                                    <h4 class="text-2xl font-bold text-slate-900 mb-2">ไม่มีข้อมูลการลาชั่วคราว</h4>
                                                    <p class="text-sm font-medium text-slate-500 max-w-sm mx-auto">ไม่พบรายการลาชั่วคราวในช่วงเวลาที่เลือก กรุณาตรวจสอบตัวกรองอีกครั้ง</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
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

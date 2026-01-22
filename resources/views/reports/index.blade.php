<x-app-layout>
    @section('title', 'รายงานสรุปการลา')

    <div class="max-w-[95rem] mx-auto py-8 sm:px-6 lg:px-8">

        <!-- Header & Toolbar -->
        <div class="mb-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">รายงานสรุปการลา</h1>
                    <p class="text-slate-500 mt-1">วิเคราะห์สถิติและเรียกดูประวัติการลาทั้งหมด</p>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" form="filter-form" formaction="{{ route('reports.export') }}"
                        class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 font-medium hover:bg-slate-50 hover:text-emerald-600 transition-colors shadow-sm">
                        <i data-lucide="sheet" class="w-4 h-4 mr-2"></i>
                        Export Excel
                    </button>

                    <button type="button" onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 font-medium hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm">
                        <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                        Print
                    </button>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Requests -->
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col justify-between h-full group hover:border-indigo-100 transition-all">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">คำขอทั้งหมด</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $requests->total() }}</h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="files" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col justify-between h-full group hover:border-emerald-100 transition-all">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">อนุมัติแล้ว</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">
                                {{ \App\Models\LeaveRequest::where('status', 'approved')->count() }}
                            </h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col justify-between h-full group hover:border-amber-100 transition-all">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">รออนุมัติ</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">
                                {{ \App\Models\LeaveRequest::whereIn('status', ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director'])->count() }}
                            </h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <!-- Rejected -->
                <div
                    class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex flex-col justify-between h-full group hover:border-rose-100 transition-all">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">ถูกปฏิเสธ/ยกเลิก</p>
                            <h3 class="text-2xl font-bold text-slate-800 mt-1">
                                {{ \App\Models\LeaveRequest::whereIn('status', ['rejected', 'cancelled'])->count() }}
                            </h3>
                        </div>
                        <div
                            class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ showFilters: false }">

            <!-- Sidebar Filters (Desktop) -->
            <div class="hidden lg:block lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-24">
                    <div class="p-4 border-b border-slate-50 flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4 text-slate-400"></i>
                        <h3 class="font-bold text-slate-700">ตัวกรองข้อมูล</h3>
                    </div>

                    <form method="GET" action="{{ route('reports.index') }}" id="filter-form" class="p-4 space-y-5">

                        <!-- Date Range -->
                        <div class="space-y-3">
                            <label class="text-xs font-bold text-slate-500 uppercase">ช่วงเวลา</label>
                            <div class="space-y-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-xs text-slate-400">จาก</span>
                                    </div>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="block w-full pl-10 pr-3 py-2 border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-xs text-slate-400">ถึง</span>
                                    </div>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="block w-full pl-10 pr-3 py-2 border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">แผนก</label>
                            <select name="department"
                                class="block w-full py-2 pl-3 pr-8 border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                <option value="">ทุกแผนก</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Leave Type -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">ประเภทการลา</label>
                            <select name="leave_type_id"
                                class="block w-full py-2 pl-3 pr-8 border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                <option value="">ทั้งหมด</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase">สถานะ</label>
                            <select name="status"
                                class="block w-full py-2 pl-3 pr-8 border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-all">
                                <option value="">ทั้งหมด</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                    อนุมัติแล้ว</option>
                                <option value="pending" {{ str_contains(request('status') ?? '', 'pending') ? 'selected' : '' }}>รออนุมัติ</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ถูกปฏิเสธ
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก
                                </option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-slate-50 space-y-3">
                            <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md shadow-indigo-200 transition-all">
                                ค้นหา
                            </button>
                            <a href="{{ route('reports.index') }}"
                                class="block w-full py-2.5 text-center text-slate-500 hover:text-slate-700 font-medium text-sm hover:bg-slate-50 rounded-xl transition-all">
                                ล้างค่า
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-span-1 lg:col-span-3 space-y-6">

                <!-- Mobile Filter Toggle -->
                <div class="lg:hidden">
                    <button @click="showFilters = !showFilters"
                        class="w-full py-3 bg-white border border-slate-200 rounded-xl text-slate-600 font-bold shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        ตัวกรองข้อมูล
                    </button>

                    <!-- Mobile Filters -->
                    <div x-show="showFilters" class="mt-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100"
                        style="display: none;">
                        <!-- (Reuse form content for mobile if needed, or keeping it simple for now) -->
                        <p class="text-center text-slate-400 text-sm italic">ใช้ตัวกรองด้านบน (Desktop Mode)
                            เพื่อผลลัพธ์ที่ดีที่สุด</p>
                    </div>
                </div>

                <!-- Analysis Cards -->
                @if(isset($topLeavers) && $topLeavers->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Top Leavers -->
                        <div
                            class="bg-indigo-900 rounded-2xl p-6 text-white relative overflow-hidden shadow-lg shadow-indigo-200">
                            <div
                                class="absolute top-0 right-0 w-64 h-64 bg-indigo-500 rounded-full blur-3xl opacity-20 -mr-16 -mt-16 pointer-events-none">
                            </div>

                            <div class="flex items-center gap-3 mb-6 relative z-10">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                                    <i data-lucide="trophy" class="w-5 h-5 text-yellow-300"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg">Top 5 ใช้สิทธิ์สูงสุด</h3>
                                    <p class="text-indigo-200 text-xs">พิจารณาจากจำนวนวันที่ได้รับการอนุมัติ</p>
                                </div>
                            </div>

                            <div class="space-y-4 relative z-10">
                                @foreach($topLeavers->take(3) as $index => $leaver)
                                    <div class="flex items-center gap-3">
                                        <div class="font-mono font-bold text-indigo-300 w-4">#{{ $index + 1 }}</div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="font-bold text-sm truncate">{{ $leaver->user->name }}</p>
                                                <span class="text-yellow-300 font-bold text-sm">{{ $leaver->total_leave_days }}
                                                    วัน</span>
                                            </div>
                                            <p class="text-xs text-indigo-300 truncate">{{ $leaver->user->department }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Usage Stats -->
                        <div
                            class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col relative overflow-hidden">
                            <div class="flex items-center gap-3 mb-6 relative z-10">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                                    <i data-lucide="pie-chart" class="w-5 h-5 text-indigo-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-slate-800">สัดส่วนการลา</h3>
                                    <p class="text-slate-500 text-xs">แยกตามประเภท</p>
                                </div>
                            </div>

                            <div class="space-y-4 flex-1 relative z-10">
                                @foreach($popularLeaveTypes->take(3) as $type)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="font-medium text-slate-700">{{ $type->leaveType->name }}</span>
                                            <span class="font-bold text-slate-900">{{ $type->usage_count }} ครั้ง</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2">
                                            <div class="bg-indigo-500 h-2 rounded-full"
                                                style="width: {{ min(($type->usage_count / max($popularLeaveTypes->max('usage_count'), 1)) * 100, 100) }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Data Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        พนักงาน</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        ประเภท</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        ช่วงเวลา</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        จำนวนวัน</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        สถานะ</th>
                                    <th scope="col"
                                        class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        เอกสาร</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 bg-white">
                                @forelse($requests as $req)
                                    <tr class="hover:bg-slate-50/80 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                @if($req->user->avatar)
                                                    <img class="h-9 w-9 rounded-full object-cover shadow-sm"
                                                        src="{{ asset('storage/' . $req->user->avatar) }}" alt="">
                                                @else
                                                    <div
                                                        class="h-9 w-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                                        {{ substr($req->user->rank ?? $req->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-bold text-slate-900">
                                                        {{ $req->user->rank ? $req->user->rank . ' ' : '' }}{{ $req->user->name }}
                                                    </div>
                                                    <div class="text-xs text-slate-500">{{ $req->user->department }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="text-sm text-slate-700 font-medium">{{ $req->leaveType->name }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-slate-700">@thaidate($req->start_date)</div>
                                            <div class="text-xs text-slate-400">ถึง @thaidate($req->end_date)</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span
                                                class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">
                                                {{ $req->total_days }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $statusConfig = match ($req->status) {
                                                    'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'ring-emerald-600/20', 'label' => 'อนุมัติ'],
                                                    'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'ring-rose-600/20', 'label' => 'ไม่อนุมัติ'],
                                                    'cancelled' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-500', 'border' => 'ring-slate-500/20', 'label' => 'ยกเลิก'],
                                                    default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'ring-amber-600/20', 'label' => 'รออนุมัติ']
                                                };
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-1">
                                                {{-- Generated PDF Form --}}
                                                <a href="{{ route('leave-request.pdf', $req->id) }}" target="_blank"
                                                    class="p-2 inline-flex text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"
                                                    title="ใบลา (PDF)">
                                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                                </a>

                                                {{-- Attached File (Official Duty Only) --}}
                                                @if($req->leaveType->slug === 'official-duty' && $req->attachment_path)
                                                    <a href="{{ route('storage.file', $req->attachment_path) }}" target="_blank"
                                                        class="p-2 inline-flex text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                        title="เอกสารแนบ">
                                                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div
                                                class="mx-auto w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                                <i data-lucide="search-x" class="w-6 h-6 text-slate-300"></i>
                                            </div>
                                            <p class="text-slate-500 font-medium">ไม่พบข้อมูลตามเงื่อนไขที่กำหนด</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                    <div class="mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
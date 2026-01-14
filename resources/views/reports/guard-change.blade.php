<x-app-layout>
    @section('title', 'รายงานการเปลี่ยนยาม (Guard Change Reports)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-6 relative">
            <div class="relative z-10 p-2">
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i data-lucide="repeat" class="w-3.5 h-3.5"></i>
                    </span>
                    รายงานการเปลี่ยนยาม
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">ติดตามและวิเคราะห์ข้อมูลการเปลี่ยนยามของบุคลากร</p>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-100/50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="sliders" class="w-4 h-4 text-sm"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900">ตัวกรองข้อมูล (Filters)</h3>
            </div>
            
            <div class="p-8">
                <form method="GET" action="{{ route('reports.guard-change') }}" id="filter-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Date Range -->
                        <div class="col-span-2 grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div class="group">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">ตั้งแต่วันที่</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-full pl-10 pr-3 py-2 border-slate-200 rounded-xl text-sm font-medium bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                </div>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">ถึงวันที่</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-full pl-10 pr-3 py-2 border-slate-200 rounded-xl text-sm font-medium bg-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Department -->
                        <div class="group">
                             <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">แผนก</label>
                             <div class="relative">
                                <select name="department" class="block w-full pl-3 pr-10 py-3 border-slate-200 rounded-xl text-sm font-medium bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors appearance-none">
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

                        <!-- Status -->
                        <div class="group">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">สถานะ</label>
                            <div class="relative">
                                <select name="status" class="block w-full pl-3 pr-10 py-3 border-slate-200 rounded-xl text-sm font-medium bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 transition-colors appearance-none">
                                    <option value="">ทั้งหมด</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอผู้เปลี่ยนแทนยืนยัน</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>ผู้เปลี่ยนแทนยืนยันแล้ว</option>
                                    <option value="director_approved" {{ request('status') == 'director_approved' ? 'selected' : '' }}>รอง ผอ. อนุมัติแล้ว</option>
                                    <option value="fully_approved" {{ request('status') == 'fully_approved' ? 'selected' : '' }}>ผอ. อนุมัติแล้ว</option>
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
                        <a href="{{ route('reports.guard-change') }}" class="inline-flex items-center px-6 py-3 text-sm font-bold text-slate-500 hover:text-indigo-600 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 transition-all duration-200">
                             <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2 text-xs"></i> ล้างค่า
                        </a>
                        <button type="submit" class="inline-flex items-center px-8 py-3 text-sm font-bold text-white bg-slate-900 hover:bg-indigo-600 rounded-xl shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1">
                            <i data-lucide="search" class="w-4 h-4 mr-2"></i> ค้นหาข้อมูล
                        </button>
                    </div>
                </form>
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
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ผู้ขอเปลี่ยนยาม</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ผู้มาเปลี่ยนแทน</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">ตำแหน่งเวร</th>
                            <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">วันที่เวร</th>
                            <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">สถานะ</th>
                            <th scope="col" class="px-6 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">วันที่ทำรายการ</th>
                            <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">เอกสาร</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-150 group">
                            <!-- ผู้ขอเปลี่ยนยาม -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 relative">
                                        @if($req->user->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-md group-hover:scale-105 transition-transform" src="{{ asset('storage/'.$req->user->avatar) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-md group-hover:scale-105 transition-transform">
                                                {{ substr($req->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-slate-900">{{ $req->user->name }}</div>
                                        <div class="text-xs font-medium text-slate-500 bg-slate-100 inline-block px-1.5 py-0.5 rounded mt-1">{{ $req->user->department }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- ผู้มาเปลี่ยนแทน -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 relative">
                                        @if($req->replacementUser->avatar)
                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-md group-hover:scale-105 transition-transform" src="{{ asset('storage/'.$req->replacementUser->avatar) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center text-emerald-600 font-bold border-2 border-white shadow-md group-hover:scale-105 transition-transform">
                                                {{ substr($req->replacementUser->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-bold text-slate-900">{{ $req->replacementUser->name }}</div>
                                        <div class="text-xs font-medium text-slate-500 bg-slate-100 inline-block px-1.5 py-0.5 rounded mt-1">{{ $req->replacementUser->department }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- ตำแหน่งเวร -->
                            <td class="px-6 py-5 whitespace-nowrap">
                                @php
                                    $dutyPositions = [
                                        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                        'duty_officer' => 'นายทหารเวร',
                                        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                    <i data-lucide="shield" class="w-4 h-4 mr-1.5"></i>
                                    {{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}
                                </span>
                            </td>
                            <!-- วันที่เวร -->
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <div class="text-sm text-slate-800 font-bold">@thaidatefull($req->duty_date)</div>
                            </td>
                            <!-- สถานะ -->
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                @php
                                    $statusConfig = match($req->status) {
                                        'fully_approved', 'final_approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติเสร็จสิ้น'],
                                        'director_approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'label' => 'รอง ผอ. อนุมัติแล้ว'],
                                        'approved' => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'dot' => 'bg-cyan-500', 'label' => 'ผู้เปลี่ยนแทนยืนยันแล้ว'],
                                        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'dot' => 'bg-rose-500', 'label' => 'ถูกปฏิเสธ'],
                                        'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิกแล้ว'],
                                        default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'รอยืนยัน']
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusConfig['dot'] }} animate-pulse"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                            </td>
                             <!-- วันที่ทำรายการ -->
                             <td class="px-6 py-5 whitespace-nowrap text-right">
                                <span class="text-sm font-medium text-slate-600">@thaidatefull($req->created_at)</span>
                                <div class="text-xs text-slate-400 mt-0.5">@thaitime($req->created_at) น.</div>
                            </td>
                            <!-- เอกสาร -->
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <a href="{{ route('guard-change.pdf', $req->id) }}" target="_blank" class="text-slate-400 hover:text-indigo-600 transition-colors p-2 rounded-lg hover:bg-slate-50 inline-block" title="ดาวน์โหลด PDF">
                                    <i data-lucide="file-text" class="w-4 h-4 text-xl"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center relative">
                                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent to-slate-50 rounded-full blur-2xl opacity-50"></div>
                                    <div class="h-20 w-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 relative z-10">
                                        <i data-lucide="folder-open" class="w-6 h-6 text-3xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-slate-900 font-bold text-lg mb-1 relative z-10">ไม่พบข้อมูลการเปลี่ยนยาม</h3>
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

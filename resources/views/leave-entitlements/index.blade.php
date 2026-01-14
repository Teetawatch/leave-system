<x-app-layout>
    @section('title', 'จัดการสิทธิ์วันลาพักผ่อน')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 relative">
            <div class="relative z-10 p-2">
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-emerald-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i data-lucide="calendar-plus" class="w-6 h-6"></i>
                    </span>
                    จัดการสิทธิ์วันลาพักผ่อน
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">แก้ไขสิทธิ์วันลาพักผ่อนของบุคลากรหลายคนพร้อมกัน (ปี {{ date('Y') }})</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                <span class="text-emerald-800 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative">
            
            <!-- Decor -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

            <!-- Header & Filters -->
            <div class="px-8 py-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 relative z-10">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-800">รายชื่อบุคลากรทั้งหมด</h3>
                    <p class="text-sm text-slate-500">แก้ไขสิทธิ์วันลาพักผ่อนแล้วกดบันทึกทั้งหมด</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Department Filter -->
                    <form action="{{ route('leave-entitlements.index') }}" method="GET" class="flex gap-3">
                        <select name="department" onchange="this.form.submit()" 
                            class="px-4 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-medium">
                            <option value="">ทุกแผนก</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->name }}" {{ request('department') === $dept->name ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Search -->
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="block w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-medium placeholder:text-slate-400" 
                                placeholder="ค้นหาชื่อ...">
                        </div>
                        
                        <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-medium transition-colors">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form for Bulk Update -->
            <form action="{{ route('leave-entitlements.bulk-update') }}" method="POST" id="bulkUpdateForm">
                @csrf
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 align-middle">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">พนักงาน</th>
                                <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">แผนก</th>
                                <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    <div class="flex flex-col items-center">
                                        <span>สิทธิ์วันลา</span>
                                        <span class="text-emerald-600 font-normal">(แก้ไขได้)</span>
                                    </div>
                                </th>
                                <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">ใช้ไปแล้ว</th>
                                <th scope="col" class="px-6 py-5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">คงเหลือ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($employees as $index => $emp)
                            <tr class="hover:bg-emerald-50/10 transition-colors duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="hidden" name="entitlements[{{ $index }}][user_id]" value="{{ $emp->id }}">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 h-10 w-10 relative">
                                            @if(isset($emp->avatar) && $emp->avatar)
                                                <img class="h-10 w-10 rounded-xl object-cover border-2 border-white shadow-md" src="{{ asset('storage/'.$emp->avatar) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-200 flex items-center justify-center text-emerald-700 font-bold border-2 border-white shadow-md text-sm">
                                                    {{ mb_substr($emp->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">{{ $emp->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $emp->position ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-slate-600 bg-slate-100/50 px-3 py-1 rounded-lg">
                                        {{ $emp->department ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="number" 
                                           name="entitlements[{{ $index }}][total_days]" 
                                           value="{{ $emp->vacation_total }}"
                                           min="0" 
                                           max="30" 
                                           step="1"
                                           class="w-20 text-center px-3 py-2 rounded-xl border-2 border-emerald-200 bg-emerald-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-bold text-emerald-700">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-700 font-bold text-sm">
                                        {{ $emp->vacation_used }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-700 font-bold text-sm">
                                        {{ $emp->vacation_remaining }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                            <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium">ไม่พบข้อมูลพนักงาน</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Footer with Save Button -->
                @if($employees->count() > 0)
                <div class="px-8 py-6 border-t border-slate-100 bg-gradient-to-r from-slate-50 to-emerald-50/30 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-slate-500">
                        แสดง {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} จากทั้งหมด {{ $employees->total() }} คน
                    </div>
                    
                    <div class="flex items-center gap-4">
                        {{ $employees->links() }}
                        
                        <button type="submit" 
                                class="group inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg hover:shadow-xl hover:shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            บันทึกทั้งหมด
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>

        <!-- Quick Actions Card -->
        <div class="mt-6 p-6 bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl shadow-xl">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                        <i data-lucide="info" class="w-6 h-6 text-emerald-400"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-white">คำแนะนำ</h4>
                        <p class="text-sm text-slate-400">การแก้ไขสิทธิ์วันลาจะมีผลทันทีเมื่อกดบันทึก วันลาคงเหลือจะถูกคำนวณใหม่อัตโนมัติ</p>
                    </div>
                </div>
                <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-lg text-sm font-medium transition-colors">
                    <i data-lucide="users" class="w-4 h-4 mr-2"></i>
                    จัดการพนักงาน
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

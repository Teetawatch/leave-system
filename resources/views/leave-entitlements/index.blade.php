<x-app-layout>
    @section('title', 'จัดการสิทธิ์วันลาพักผ่อน')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .entitlement-row {
                transition: all 0.2s ease;
            }

            .entitlement-row:hover {
                background-color: rgba(16, 185, 129, 0.03);
            }

            .custom-scrollbar::-webkit-scrollbar {
                height: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-emerald-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <!-- Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            ระบบจัดการสิทธิ์
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-6">
                            จัดการสิทธิ์ <span class="text-emerald-600">วันลาพักผ่อน</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">
                            แก้ไขจำนวนวันลาพักผ่อนของบุคลากรหลายคนพร้อมกัน<br class="hidden md:block">
                            ประจำปี {{ date('Y') + 543 }} ระบบจะคำนวณยอดคงเหลืออัตโนมัติ
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('employees.index') }}" class="group inline-flex items-center justify-center px-8 py-5 bg-white border border-slate-200 text-slate-900 font-black text-sm rounded-[2rem] shadow-xl hover:shadow-slate-500/10 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest gap-3">
                            <i data-lucide="users" class="w-5 h-5 group-hover:scale-110 transition-transform text-emerald-500"></i>
                            จัดการพนักงาน
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-10">

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="glass-panel rounded-[2rem] p-6 flex items-center gap-5 animate-slide-up border-l-4 border-emerald-500">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <span class="text-emerald-800 font-black text-lg">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="glass-panel rounded-[2rem] p-6 flex items-center gap-5 animate-slide-up border-l-4 border-rose-500">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm border border-rose-100">
                        <i data-lucide="alert-circle" class="w-7 h-7"></i>
                    </div>
                    <span class="text-rose-800 font-black text-lg">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Filter Console -->
            <div class="glass-panel rounded-[3rem] p-8 animate-slide-up" style="animation-delay: 0.1s">
                <form action="{{ route('leave-entitlements.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">หน่วยงาน/แผนก</label>
                            <div class="relative group">
                                <select name="department" onchange="this.form.submit()"
                                        class="w-full px-6 py-4 bg-slate-50 border-slate-100 rounded-[1.75rem] font-bold text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all appearance-none cursor-pointer">
                                    <option value="">ทุกแผนก</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ request('department') === $dept->name ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none group-hover:text-emerald-500 transition-colors"></i>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">ค้นหาชื่อ</label>
                            <div class="relative group">
                                <i data-lucide="search" class="absolute left-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="w-full pl-14 pr-6 py-4 bg-slate-50 border-slate-100 rounded-[1.75rem] font-bold text-slate-700 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder:text-slate-400"
                                       placeholder="ค้นหาชื่อ...">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-[1.75rem] font-black uppercase tracking-widest text-xs shadow-xl hover:bg-emerald-600 transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-3">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        กรองข้อมูล
                    </button>
                </form>
            </div>

            <!-- Main Data Table -->
            <div class="glass-panel rounded-[3.5rem] overflow-hidden animate-slide-up shadow-2xl shadow-slate-900/5" style="animation-delay: 0.2s">
                <div class="bg-slate-900 px-10 py-8 flex justify-between items-center">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 text-emerald-400 flex items-center justify-center border border-white/10 shadow-inner">
                            <i data-lucide="calendar-plus" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase">รายชื่อบุคลากร</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">แก้ไขสิทธิ์วันลาแล้วกดบันทึกทั้งหมด</p>
                        </div>
                    </div>
                    <div class="px-5 py-2 rounded-full bg-white/5 border border-white/10 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        ทั้งหมด: {{ number_format($employees->total()) }} คน
                    </div>
                </div>

                <form action="{{ route('leave-entitlements.bulk-update') }}" method="POST" id="bulkUpdateForm">
                    @csrf

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th class="px-10 py-7 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ข้อมูลพนักงาน</th>
                                    <th class="px-6 py-7 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">แผนก</th>
                                    <th class="px-6 py-7 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                        <div class="flex flex-col items-center">
                                            <span>สิทธิ์วันลา</span>
                                            <span class="text-emerald-500 font-bold normal-case tracking-normal">(แก้ไขได้)</span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-7 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ใช้ไปแล้ว</th>
                                    <th class="px-10 py-7 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">คงเหลือ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($employees as $index => $emp)
                                    <tr class="entitlement-row group">
                                        <td class="px-10 py-6 whitespace-nowrap">
                                            <input type="hidden" name="entitlements[{{ $index }}][user_id]" value="{{ $emp->id }}">
                                            <div class="flex items-center gap-5">
                                                <div class="w-14 h-14 rounded-2xl overflow-hidden border-2 border-white shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                                                    @if(isset($emp->avatar) && $emp->avatar)
                                                        <img class="w-full h-full object-cover" src="{{ asset('storage/'.$emp->avatar) }}" alt="">
                                                    @else
                                                        <div class="w-full h-full bg-gradient-to-br from-emerald-100 to-teal-200 flex items-center justify-center text-emerald-700 font-black text-lg">
                                                            {{ mb_substr($emp->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-black text-slate-900 text-lg group-hover:text-emerald-600 transition-colors leading-tight">{{ $emp->name }}</p>
                                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $emp->position ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap">
                                            <span class="inline-flex px-4 py-2 bg-slate-100 text-slate-700 rounded-xl text-xs font-black border border-slate-200 shadow-sm">
                                                {{ $emp->department ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap text-center">
                                            <input type="number"
                                                   name="entitlements[{{ $index }}][total_days]"
                                                   value="{{ $emp->vacation_total }}"
                                                   min="0"
                                                   max="30"
                                                   step="1"
                                                   class="w-24 text-center px-4 py-3 rounded-2xl border-2 border-emerald-200 bg-emerald-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all font-black text-2xl text-emerald-700 shadow-sm">
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap text-center">
                                            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center font-black text-xl mx-auto border border-amber-100 shadow-sm">
                                                {{ $emp->vacation_used }}
                                            </div>
                                        </td>
                                        <td class="px-10 py-6 whitespace-nowrap text-center">
                                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black text-xl mx-auto border border-indigo-100 shadow-sm">
                                                {{ $emp->vacation_remaining }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-10 py-32 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mb-6 shadow-inner ring-8 ring-slate-50/50">
                                                    <i data-lucide="users" class="w-12 h-12 text-slate-200"></i>
                                                </div>
                                                <h4 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">ไม่พบข้อมูลพนักงาน</h4>
                                                <p class="text-slate-400 font-medium">ลองปรับเปลี่ยนตัวกรองหรือค้นหาใหม่</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer with Save Button -->
                    @if($employees->count() > 0)
                        <div class="px-10 py-8 border-t border-slate-100 bg-gradient-to-r from-slate-50/80 to-emerald-50/30 flex flex-col sm:flex-row items-center justify-between gap-6">
                            <div class="text-sm font-bold text-slate-500">
                                แสดง {{ $employees->firstItem() ?? 0 }} - {{ $employees->lastItem() ?? 0 }} จากทั้งหมด {{ $employees->total() }} คน
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="font-bold">
                                    {{ $employees->links() }}
                                </div>

                                <button type="submit"
                                        class="group inline-flex items-center justify-center px-10 py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-[2rem] shadow-xl shadow-emerald-500/25 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest gap-3">
                                    <i data-lucide="save" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                    บันทึกทั้งหมด
                                </button>
                            </div>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Info Card -->
            <div class="animate-slide-up" style="animation-delay: 0.3s">
                <div class="bg-slate-900 rounded-[3rem] p-10 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=%2220%22 height=%2220%22 viewBox=%220 0 20 20%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.03%22 fill-rule=%22evenodd%22%3E%3Ccircle cx=%223%22 cy=%223%22 r=%223%22/%3E%3C/g%3E%3C/svg%3E')]"></div>
                    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center border border-white/10">
                                <i data-lucide="lightbulb" class="w-7 h-7 text-emerald-400"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-white text-lg tracking-tight">คำแนะนำ</h4>
                                <p class="text-sm font-bold text-slate-400 mt-1">การแก้ไขสิทธิ์วันลาจะมีผลทันทีเมื่อกดบันทึก วันลาคงเหลือจะถูกคำนวณใหม่อัตโนมัติ</p>
                            </div>
                        </div>
                        <a href="{{ route('employees.index') }}" class="inline-flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-2xl text-sm font-black transition-all border border-white/10 gap-2 uppercase tracking-widest">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            จัดการพนักงาน
                        </a>
                    </div>
                </div>
            </div>

            <!-- Visual End -->
            <div class="mt-16 flex flex-col items-center justify-center gap-6 opacity-30">
                <div class="w-1 bg-gradient-to-b from-emerald-500 to-transparent h-20 rounded-full"></div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">สิ้นสุดหน้าการจัดการสิทธิ์</div>
            </div>
        </div>
    </div>
</x-app-layout>

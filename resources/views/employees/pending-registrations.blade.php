<x-app-layout>
    @section('title', 'รออนุมัติการลงทะเบียน')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.03) 0%, transparent 40%),
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

            .pending-row {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .pending-row:hover {
                background: rgba(245, 158, 11, 0.03);
                transform: translateX(4px);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-amber-100/30 rounded-full blur-[120px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-100/30 rounded-full blur-[100px] -ml-36 -mb-36"></div>

        <!-- Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-amber-100">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            ระบบตรวจสอบ
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                            รออนุมัติ <span class="text-amber-600">การลงทะเบียน</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                            พนักงานที่สมัครเข้าใช้ระบบรอการตรวจสอบและอนุมัติ<br class="hidden md:block">
                            จากผู้ดูแลระบบก่อนเข้าใช้งาน
                        </p>
                    </div>
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white border border-slate-200 text-slate-600 font-black rounded-2xl hover:bg-slate-50 transition-all active:scale-95 shadow-sm uppercase tracking-widest text-xs gap-3">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        กลับหน้ารายชื่อ
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-8">

            <!-- Success Message -->
            @if(session('success'))
                <div class="glass-panel rounded-[2rem] p-6 flex items-center gap-5 animate-slide-up border-l-4 border-emerald-500">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <span class="text-emerald-800 font-black text-lg">{{ session('success') }}</span>
                </div>
            @endif

            @if($pendingUsers->count() > 0)
                <!-- Stats Bar -->
                <div class="glass-panel rounded-[2.5rem] p-6 flex items-center gap-6 animate-slide-up">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shadow-sm">
                        <i data-lucide="users" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">จำนวนรอดำเนินการ</p>
                        <p class="text-3xl font-black text-slate-900">{{ $pendingUsers->total() }} <span class="text-sm font-bold text-slate-400">รายการ</span></p>
                    </div>
                </div>

                <!-- Main Table -->
                <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up" style="animation-delay: 0.1s">
                    <div class="bg-slate-900 px-10 py-8 flex items-center gap-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 text-amber-400 flex items-center justify-center border border-white/10 shadow-inner">
                            <i data-lucide="user-check" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white tracking-tight uppercase">รายชื่อรอตรวจสอบ</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Pending Registration Queue</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-10 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ข้อมูลพนักงาน</th>
                                    <th class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">อีเมล</th>
                                    <th class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">แผนก / ตำแหน่ง</th>
                                    <th class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ลงทะเบียนเมื่อ</th>
                                    <th class="px-10 py-6 text-center text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($pendingUsers as $user)
                                    <tr class="pending-row group">
                                        <td class="px-10 py-6 whitespace-nowrap">
                                            <div class="flex items-center gap-5">
                                                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 font-black text-lg border border-amber-100 shadow-sm group-hover:bg-amber-600 group-hover:text-white transition-all duration-500">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-black text-slate-900 text-lg group-hover:text-amber-600 transition-colors">{{ $user->rank }} {{ $user->name }}</p>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                        <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">รอการอนุมัติ</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap">
                                            <span class="text-sm font-bold text-slate-600 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">{{ $user->email }}</span>
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap">
                                            <div>
                                                <p class="text-sm font-black text-slate-700">{{ $user->department ?? '-' }}</p>
                                                <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $user->position ?? '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 whitespace-nowrap">
                                            <span class="text-sm font-bold text-slate-500">{{ $user->updated_at->diffForHumans() }}</span>
                                        </td>
                                        <td class="px-10 py-6 whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-3">
                                                <form action="{{ route('employees.approve-registration', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-1 active:scale-95 gap-2 uppercase tracking-widest">
                                                        <i data-lucide="check" class="w-4 h-4"></i>
                                                        อนุมัติ
                                                    </button>
                                                </form>
                                                <form action="{{ route('employees.reject-registration', $user->id) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('ปฏิเสธการลงทะเบียน? พนักงานจะสามารถลงทะเบียนใหม่ได้')">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-white border border-rose-200 text-rose-600 font-black text-xs rounded-2xl hover:bg-rose-50 transition-all hover:-translate-y-1 active:scale-95 gap-2 uppercase tracking-widest">
                                                        <i data-lucide="x" class="w-4 h-4"></i>
                                                        ปฏิเสธ
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($pendingUsers->hasPages())
                        <div class="px-10 py-6 border-t border-slate-100 bg-slate-50/30 font-bold">
                            {{ $pendingUsers->links() }}
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty State -->
                <div class="glass-panel rounded-[3rem] p-20 text-center animate-slide-up">
                    <div class="w-32 h-32 bg-slate-50 rounded-[3rem] flex items-center justify-center mx-auto mb-8 shadow-inner ring-12 ring-slate-50/50">
                        <i data-lucide="inbox" class="w-16 h-16 text-slate-200"></i>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">ไม่มีรายการรออนุมัติ</h3>
                    <p class="text-lg font-medium text-slate-400 max-w-md mx-auto mb-10 leading-relaxed">
                        เมื่อพนักงานลงทะเบียนใหม่ รายการจะแสดงที่นี่เพื่อรอการตรวจสอบ
                    </p>
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl shadow-xl transition-all hover:-translate-y-1 active:scale-95 gap-3 uppercase tracking-widest text-xs">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        กลับหน้าจัดการพนักงาน
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

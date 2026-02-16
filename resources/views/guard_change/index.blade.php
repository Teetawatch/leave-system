<x-app-layout>
    @section('title', 'รายการขอเปลี่ยนยาม')

    <div class="min-h-screen bg-[#f8fafc]">
        <!-- Cinematic Executive Header -->
        <div class="relative bg-white pt-16 pb-28 overflow-hidden border-b border-slate-100">
            <!-- Background Decoration -->
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-violet-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
            </div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div>
                        <nav class="flex items-center gap-2 text-indigo-600/60 transition-all mb-4 text-sm font-bold tracking-widest uppercase">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>ระบบเวรยาม</span>
                            <span class="w-1 h-1 rounded-full bg-indigo-500/20"></span>
                            <span class="text-indigo-600">การเปลี่ยนยาม</span>
                        </nav>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">
                            รายการขอเปลี่ยนยาม
                        </h1>
                        <p class="text-slate-500 max-w-xl text-lg font-medium leading-relaxed">
                            ระบบบันทึกและติดตามสถานะการขออนุมัติเปลี่ยนเวรยาม 
                            เพื่อความถูกต้องและโปร่งใสในการปฏิบัติหน้าที่
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4">
                        <a href="{{ route('guard-change.create') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-1 active:scale-95">
                            <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i>
                            ขอเปลี่ยนยามใหม่
                        </a>
                    </div>
                </div>

                <!-- Stats Dashboard Overlay -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 pb-4">
                    <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 shadow-sm">
                                <i data-lucide="clock" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">รออนุมัติ</p>
                                <p class="text-3xl font-black text-slate-900 mt-1">{{ $requests->whereIn('status', ['pending', 'approved', 'director_approved'])->count() }} <span class="text-sm font-bold text-slate-400">รายการ</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 shadow-sm">
                                <i data-lucide="check-circle" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">อนุมัติเรียบร้อย</p>
                                <p class="text-3xl font-black text-slate-900 mt-1">{{ $requests->where('status', 'fully_approved')->count() }} <span class="text-sm font-bold text-slate-400">รายการ</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center border border-indigo-100 shadow-sm">
                                <i data-lucide="shield-check" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">เวรปฏิบัติหน้าที่ทั้งหมด</p>
                                <p class="text-3xl font-black text-slate-900 mt-1">{{ $requests->count() }} <span class="text-sm font-bold text-slate-400">ครั้ง</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20">
            @if($requests->isEmpty())
                <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-20 text-center">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-inner">
                        <i data-lucide="shield-alert" class="w-16 h-16 text-slate-200"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-3">ไม่พบรายการขอเปลี่ยนยาม</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg mb-10 font-medium">คุณยังไม่มีประวัติการขอเปลี่ยนเวรยามในขณะนี้</p>
                    <a href="{{ route('guard-change.create') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 hover:bg-slate-900 text-white font-black rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-1">
                        เริ่มทำรายการครั้งแรก
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6">
                    @foreach($requests as $request)
                        @php
                            $dutyPositions = [
                                'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                'duty_officer' => 'นายทหารเวร',
                                'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                            ];

                            $statusConfig = match($request->status) {
                                'approved' => [
                                    'label' => 'รอ รอง ผอ. อนุมัติ',
                                    'color' => 'indigo',
                                    'icon' => 'clock',
                                    'bg' => 'bg-indigo-50/50',
                                    'border' => 'border-indigo-100',
                                    'text' => 'text-indigo-600',
                                    'pulse' => true
                                ],
                                'director_approved' => [
                                    'label' => 'รอ ผอ. อนุมัติ',
                                    'color' => 'purple',
                                    'icon' => 'clock',
                                    'bg' => 'bg-purple-50/50',
                                    'border' => 'border-purple-100',
                                    'text' => 'text-purple-600',
                                    'pulse' => true
                                ],
                                'fully_approved' => [
                                    'label' => 'อนุมัติเรียบร้อย',
                                    'color' => 'emerald',
                                    'icon' => 'check-circle',
                                    'bg' => 'bg-emerald-50/50',
                                    'border' => 'border-emerald-100',
                                    'text' => 'text-emerald-600',
                                    'pulse' => false
                                ],
                                'rejected' => [
                                    'label' => 'ถูกปฏิเสธ',
                                    'color' => 'rose',
                                    'icon' => 'x-circle',
                                    'bg' => 'bg-rose-50/50',
                                    'border' => 'border-rose-100',
                                    'text' => 'text-rose-600',
                                    'pulse' => false
                                ],
                                'cancelled' => [
                                    'label' => 'ยกเลิกรายการ',
                                    'color' => 'slate',
                                    'icon' => 'ban',
                                    'bg' => 'bg-slate-50/50',
                                    'border' => 'border-slate-100',
                                    'text' => 'text-slate-500',
                                    'pulse' => false
                                ],
                                default => [
                                    'label' => 'รอผู้รับมอบหมายยินยอม',
                                    'color' => 'amber',
                                    'icon' => 'user-check',
                                    'bg' => 'bg-amber-50/50',
                                    'border' => 'border-amber-100',
                                    'text' => 'text-amber-600',
                                    'pulse' => true
                                ]
                            };
                        @endphp

                        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-300 overflow-hidden relative">
                            <!-- Background Decor -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-bl-[4rem] group-hover:scale-110 transition-transform duration-500 pointer-events-none opacity-50"></div>

                            <div class="p-6 md:p-8 flex flex-col lg:flex-row lg:items-center gap-8 relative z-10">
                                <!-- Date Pillar -->
                                <div class="flex-shrink-0 flex lg:flex-col items-center gap-4 lg:w-32 border-b lg:border-b-0 lg:border-r border-slate-50 pb-6 lg:pb-0 lg:pr-8">
                                    <div class="flex flex-col items-center justify-center w-20 h-24 bg-indigo-50 rounded-2xl p-2 border border-indigo-100 group-hover:bg-indigo-600 group-hover:border-indigo-600 transition-all shadow-sm">
                                        <span class="text-[10px] text-indigo-600 font-black uppercase tracking-widest group-hover:text-white/80">{{ $request->duty_date->locale('th')->translatedFormat('M') }}</span>
                                        <span class="text-3xl font-black text-slate-800 group-hover:text-white leading-none">{{ $request->duty_date->format('d') }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold mt-1 group-hover:text-white/60">{{ $request->duty_date->year + 543 }}</span>
                                    </div>
                                    <div class="lg:text-center">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">เลขที่คำขอ</p>
                                        <p class="text-xs font-bold text-indigo-600">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>

                                <!-- Information Body -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-3 mb-4">
                                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-black tracking-wide">
                                            <i data-lucide="shield" class="w-3 h-3 text-indigo-400"></i>
                                            {{ $dutyPositions[$request->duty_position] ?? $request->duty_position }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} ring-1 ring-inset {{ $statusConfig['border'] }}">
                                            @if($statusConfig['pulse'])
                                                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                            @else
                                                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-3 h-3"></i>
                                            @endif
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:border-indigo-100 transition-colors">
                                                <i data-lucide="user-minus" class="w-6 h-6 text-slate-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">ผู้ขอเปลี่ยน</p>
                                                <p class="font-bold text-slate-800 text-sm truncate">{{ $request->user->rank }}{{ $request->user->name }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                                <i data-lucide="user-plus" class="w-6 h-6 text-emerald-500"></i>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">เปลี่ยนกับ</p>
                                                <p class="font-bold text-slate-800 text-sm truncate">{{ $request->replacementUser->rank }}{{ $request->replacementUser->name }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($request->remarks)
                                        <div class="mt-5 p-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 group-hover:border-indigo-100 transition-colors">
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                                <i data-lucide="message-square" class="w-3 h-3"></i>
                                                หมายเหตุ / เหตุผล
                                            </p>
                                            <p class="text-sm text-slate-600 font-medium leading-relaxed italic">"{{ $request->remarks }}"</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Side -->
                                <div class="flex-shrink-0 flex flex-row lg:flex-col items-center justify-end gap-3 lg:border-l border-slate-50 lg:pl-8">
                                    <a href="{{ route('guard-change.show', $request) }}" class="flex items-center justify-center w-12 h-12 lg:w-full lg:h-auto lg:px-6 lg:py-4 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-2xl font-black text-xs transition-all hover:border-slate-300">
                                        <i data-lucide="eye" class="w-5 h-5 lg:mr-2"></i>
                                        <span class="hidden lg:inline">ดูรายละเอียด</span>
                                    </a>
                                    
                                    @if($request->status === 'fully_approved')
                                        <a href="{{ route('guard-change.pdf', $request) }}" class="flex items-center justify-center w-12 h-12 lg:w-full lg:h-auto lg:px-6 lg:py-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-2xl font-black text-xs transition-all">
                                            <i data-lucide="file-text" class="w-5 h-5 lg:mr-2"></i>
                                            <span class="hidden lg:inline">ดาวน์โหลด PDF</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Premium Pagination -->
                @if($requests->hasPages())
                    <div class="mt-12 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                        {{ $requests->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>


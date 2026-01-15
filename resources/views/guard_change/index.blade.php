<x-app-layout>
    @section('title', 'รายการขอเปลี่ยนยาม')

    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 relative">
             <div class="relative z-10">
                <div class="absolute -left-8 -top-8 w-32 h-32 bg-indigo-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i data-lucide="shield" class="w-6 h-6"></i>
                    </span>
                    รายการขอเปลี่ยนยาม
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">จัดการและติดตามสถานะคำขอเปลี่ยนเวรยาม</p>
            </div>
            
            <a href="{{ route('guard-change.create') }}" class="group inline-flex items-center px-6 py-3 bg-slate-900 hover:bg-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1">
                <i data-lucide="plus" class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform"></i>
                ขอเปลี่ยนยามใหม่
            </a>
        </div>

        <!-- Filter / Stats Cards (Optional - can be added later) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                   <p class="text-xs font-bold text-slate-400 uppercase">รออนุมัติ</p>
                   <p class="text-xl font-black text-slate-800">{{ $requests->whereIn('status', ['pending', 'approved', 'director_approved'])->count() }} รายการ</p>
                </div>
            </div>
             <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                 <div>
                   <p class="text-xs font-bold text-slate-400 uppercase">อนุมัติแล้ว</p>
                   <p class="text-xl font-black text-slate-800">{{ $requests->where('status', 'fully_approved')->count() }} รายการ</p>
                </div>
            </div>
             <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                </div>
                 <div>
                   <p class="text-xs font-bold text-slate-400 uppercase">เวรปฏิบัติหน้าที่</p>
                   <p class="text-xl font-black text-slate-800">{{ $requests->where('status', 'fully_approved')->count() }} ครั้ง</p>
                </div>
            </div>
        </div>

        <!-- Request List -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

            @if($requests->isEmpty())
                <div class="p-16 text-center relative z-10">
                    <div class="w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i data-lucide="shield-off" class="w-10 h-10 text-slate-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">ยังไม่มีประวัติการเปลี่ยนยาม</h3>
                    <p class="text-slate-500 mb-8 max-w-sm mx-auto">คุณยังไม่เคยทำรายการขอเปลี่ยนยาม หรือรายการของคุณถูกลบไปแล้ว</p>
                    <a href="{{ route('guard-change.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-indigo-200 transition-all">
                        เริ่มทำรายการใหม่
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-50 relative z-10">
                    @foreach($requests as $request)
                    <a href="{{ route('guard-change.show', $request) }}" class="block p-6 hover:bg-slate-50/80 transition-all duration-200 group {{ $request->status === 'cancelled' ? 'opacity-60 grayscale' : '' }}">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            
                            <div class="flex items-start gap-5">
                                <!-- Status Icon Box -->
                                @php
                                    $iconConfig = match($request->status) {
                                        'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-400', 'icon' => 'ban', 'border' => 'border-slate-200'],
                                        'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-500', 'icon' => 'x-circle', 'border' => 'border-rose-100'],
                                        'fully_approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'icon' => 'check-check', 'border' => 'border-emerald-100'],
                                        default => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-500', 'icon' => 'shield', 'border' => 'border-indigo-100'],
                                    };
                                    
                                    $dutyPositions = [
                                        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                        'duty_officer' => 'นายทหารเวร',
                                        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                    ];
                                @endphp
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-bold {{ $iconConfig['bg'] }} {{ $iconConfig['text'] }} border-2 {{ $iconConfig['border'] }} shadow-sm group-hover:scale-105 transition-transform flex-shrink-0">
                                    <i data-lucide="{{ $iconConfig['icon'] }}" class="w-7 h-7"></i>
                                </div>
                                
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-slate-900 text-lg {{ $request->status === 'cancelled' ? 'line-through text-slate-500' : '' }}">
                                            {{ $dutyPositions[$request->duty_position] ?? $request->duty_position }}
                                        </h4>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">
                                            #{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-y-1 gap-x-4 text-sm text-slate-500 font-medium">
                                        <div class="flex items-center gap-1.5 ">
                                            <i data-lucide="calendar" class="w-4 h-4 text-indigo-400"></i>
                                            <span>เวรวันที่ {{ $request->duty_date->locale('th')->translatedFormat('d F Y') }}</span>
                                        </div>
                                        <div class="hidden sm:block w-1 h-1 rounded-full bg-slate-300"></div>
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="user-check" class="w-4 h-4 text-emerald-400"></i>
                                            <span>เปลี่ยนกับ <span class="text-slate-700 font-bold">{{ $request->replacementUser->rank }}{{ $request->replacementUser->name }}</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between md:flex-col md:items-end gap-2 pl-20 md:pl-0">
                                @php
                                    $statusConfig = match($request->status) {
                                        'approved' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'dot' => 'bg-indigo-500', 'label' => 'รอ รอง ผอ. อนุมัติ'],
                                        'director_approved' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500', 'label' => 'รอ ผอ. อนุมัติ'],
                                        'fully_approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'อนุมัติเรียบร้อย'],
                                        'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'dot' => 'bg-rose-500', 'label' => 'ถูกปฏิเสธ'],
                                        'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-400', 'label' => 'ยกเลิกรายการ'],
                                        default => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'รอผู้รับมอบหมายยินยอม']
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border border-transparent {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }} {{ in_array($request->status, ['fully_approved', 'rejected', 'cancelled']) ? '' : 'animate-pulse' }}"></span>
                                    {{ $statusConfig['label'] }}
                                </span>
                                
                                <div class="flex items-center gap-1 text-xs text-slate-400 font-medium">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>

                        </div>
                    </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $requests->links() }}
                </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>

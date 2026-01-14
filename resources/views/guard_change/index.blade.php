<x-app-layout>
    @section('title', 'รายการขอเปลี่ยนยาม')

    <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">รายการขอเปลี่ยนยาม</h1>
                <p class="text-slate-500 mt-1">ประวัติคำขอเปลี่ยนเวรยามทั้งหมดของคุณ</p>
            </div>
            <a href="{{ route('guard-change.create') }}" class="inline-flex items-center px-6 py-3 bg-slate-900 hover:bg-brand-600 text-white font-bold rounded-xl shadow-lg transition-all duration-300">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> ขอเปลี่ยนยามใหม่
            </a>
        </div>

        <!-- Request List -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            @if($requests->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="shield" class="w-4 h-4 text-slate-300 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">ยังไม่มีรายการ</h3>
                    <p class="text-slate-500 mb-6">คุณยังไม่เคยขอเปลี่ยนยาม</p>
                    <a href="{{ route('guard-change.create') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition-colors">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i> ขอเปลี่ยนยามใหม่
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach($requests as $request)
                    <a href="{{ route('guard-change.show', $request) }}" class="block px-8 py-6 hover:bg-slate-50/50 transition-colors group {{ $request->status === 'cancelled' ? 'opacity-60' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                @php
                                    $iconConfig = match($request->status) {
                                        'cancelled' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-400', 'icon' => 'ban'],
                                        'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-500', 'icon' => 'x-circle'],
                                        'fully_approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'icon' => 'check-check'],
                                        default => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-500', 'icon' => 'shield'],
                                    };
                                @endphp
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold {{ $iconConfig['bg'] }} {{ $iconConfig['text'] }}">
                                    <i data-lucide="{{ $iconConfig['icon'] }}" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    @php
                                        $dutyPositions = [
                                            'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                            'duty_officer' => 'นายทหารเวร',
                                            'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                        ];
                                    @endphp
                                    <h4 class="font-bold text-slate-800 text-base mb-0.5 {{ $request->status === 'cancelled' ? 'line-through text-slate-500' : '' }}">{{ $dutyPositions[$request->duty_position] ?? $request->duty_position }}</h4>
                                    <p class="text-xs text-slate-500 font-medium">
                                        วันที่ {{ $request->duty_date->locale('th')->translatedFormat('d F Y') }} • 
                                        เปลี่ยนกับ {{ $request->replacementUser->rank }}{{ $request->replacementUser->name }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                @php
                                    $statusConfig = match($request->status) {
                                        'approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'รอ รอง ผอ. อนุมัติ'],
                                        'director_approved' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => 'รอ ผอ. อนุมัติ'],
                                        'fully_approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'อนุมัติแล้ว'],
                                        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'label' => 'ถูกปฏิเสธ'],
                                        'cancelled' => ['bg' => 'bg-slate-200', 'text' => 'text-slate-600', 'label' => 'ยกเลิกแล้ว'],
                                        default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'รอผู้รับมอบหมายยินยอม']
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    {{ $statusConfig['label'] }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 font-medium">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($requests->hasPages())
                <div class="px-8 py-6 border-t border-slate-100">
                    {{ $requests->links() }}
                </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>

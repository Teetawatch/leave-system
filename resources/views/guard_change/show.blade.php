<x-app-layout>
    @section('title', 'รายละเอียดคำขอเปลี่ยนยาม')

    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <!-- Success Alert -->
        @if(session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms class="mb-6 rounded-2xl bg-emerald-50 p-4 border border-emerald-100 shadow-lg shadow-emerald-500/10 flex items-center gap-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-400 rounded-full blur-2xl opacity-10"></div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                    <i data-lucide="check-circle" class="w-5 h-5 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-emerald-800">สำเร็จ!</h4>
                    <p class="text-sm font-medium text-emerald-600">{{ session('status') }}</p>
                </div>
                <button @click="show = false" class="z-10 bg-white/50 hover:bg-white rounded-lg p-2 text-emerald-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('guard-change.index') }}" class="inline-flex items-center text-slate-500 hover:text-brand-600 font-medium mb-4 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> กลับไปรายการ
            </a>
            <h1 class="text-3xl font-extrabold text-slate-800">รายละเอียดคำขอเปลี่ยนยาม</h1>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            
            <!-- Status Header -->
            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">คำขอเปลี่ยนยาม #{{ $guardChange->id }}</h2>
                        <p class="text-sm text-slate-500">สร้างเมื่อ {{ $guardChange->created_at->locale('th')->translatedFormat('d F Y H:i น.') }}</p>
                    </div>
                </div>
                @php
                    $statusConfig = match($guardChange->status) {
                        'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'อนุมัติแล้ว'],
                        'rejected' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-700', 'label' => 'ถูกปฏิเสธ'],
                        default => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'รออนุมัติ']
                    };
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                    {{ $statusConfig['label'] }}
                </span>
            </div>

            <!-- Details -->
            <div class="p-8 space-y-6">
                
                <!-- Requester Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ผู้ขอเปลี่ยนยาม</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold">
                                {{ substr($guardChange->user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $guardChange->user->rank }}{{ $guardChange->user->name }}</p>
                                <p class="text-sm text-slate-500">{{ $guardChange->user->position ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ผู้ที่จะมาเปลี่ยนแทน</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                {{ substr($guardChange->replacementUser->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $guardChange->replacementUser->rank }}{{ $guardChange->replacementUser->name }}</p>
                                <p class="text-sm text-slate-500">{{ $guardChange->replacementUser->position ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Duty Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ตำแหน่งเวรยาม</p>
                        @php
                            $dutyPositions = [
                                'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                'duty_officer' => 'นายทหารเวร',
                                'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                            ];
                        @endphp
                        <p class="text-lg font-bold text-slate-800">{{ $dutyPositions[$guardChange->duty_position] ?? $guardChange->duty_position }}</p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">วันที่เข้าเวร</p>
                        <p class="text-lg font-bold text-slate-800">{{ $guardChange->duty_date->locale('th')->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                <!-- Remarks -->
                @if($guardChange->remarks)
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">หมายเหตุ / เหตุผล</p>
                    <p class="text-slate-700">{{ $guardChange->remarks }}</p>
                </div>
                @endif

            </div>

            <!-- Actions Footer -->
            <div class="bg-slate-50 px-8 py-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('guard-change.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> กลับ
                </a>
                <div class="flex items-center gap-3">
                    @if($guardChange->status === 'pending')
                        <form action="{{ route('guard-change.cancel', $guardChange) }}" method="POST" onsubmit="return confirm('ยืนยันการยกเลิกคำขอเปลี่ยนยามนี้?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-rose-500 text-white rounded-xl font-bold hover:bg-rose-600 transition-colors shadow-lg">
                                <i data-lucide="x" class="w-4 h-4 mr-2"></i> ยกเลิก
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('guard-change.pdf', $guardChange) }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-brand-600 transition-colors shadow-lg">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> ดาวน์โหลด PDF
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>

<x-app-layout>
    @section('title', 'รายละเอียดคำขอเปลี่ยนยาม')

    <div class="min-h-screen bg-[#f8fafc] pb-20">
        <!-- Cinematic Header -->
        <div class="relative bg-slate-900 pt-16 pb-32 overflow-hidden">
            <div class="absolute inset-0">
                <div
                    class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-500/10 rounded-full blur-[120px] -mr-48 -mt-48">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/10 rounded-full blur-[100px] -ml-24 -mb-24">
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <nav class="flex items-center gap-2 text-slate-400 mb-8">
                    <a href="{{ route('guard-change.index') }}"
                        class="hover:text-white transition-colors flex items-center gap-2 group">
                        <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                        <span>กลับไปรายการ</span>
                    </a>
                </nav>

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <div class="flex items-center gap-4 mb-4">
                            <span
                                class="w-12 h-12 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-lg shadow-brand-500/20">
                                <i data-lucide="shield" class="w-6 h-6"></i>
                            </span>
                            <span class="text-brand-400 font-black tracking-widest uppercase text-sm">Guard Change
                                Detail</span>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight">
                            รายละเอียดคำขอเปลี่ยนยาม
                        </h1>
                    </div>

                    <div class="flex items-center gap-4">
                        @php
                            $statusConfig = match ($guardChange->status) {
                                'approved' => [
                                    'bg' => 'bg-emerald-500/10',
                                    'text' => 'text-emerald-400',
                                    'border' => 'border-emerald-500/20',
                                    'dot' => 'bg-emerald-500',
                                    'label' => 'อนุมัติเรียบร้อย'
                                ],
                                'rejected' => [
                                    'bg' => 'bg-rose-500/10',
                                    'text' => 'text-rose-400',
                                    'border' => 'border-rose-500/20',
                                    'dot' => 'bg-rose-500',
                                    'label' => 'ถูกปฏิเสธ'
                                ],
                                'cancelled' => [
                                    'bg' => 'bg-slate-500/10',
                                    'text' => 'text-slate-400',
                                    'border' => 'border-slate-500/20',
                                    'dot' => 'bg-slate-400',
                                    'label' => 'ยกเลิกแล้ว'
                                ],
                                default => [
                                    'bg' => 'bg-amber-500/10',
                                    'text' => 'text-amber-400',
                                    'border' => 'border-amber-500/20',
                                    'dot' => 'bg-amber-500',
                                    'label' => 'อยู่ระหว่างดำเนินการ'
                                ]
                            };
                        @endphp
                        <div
                            class="px-6 py-3 rounded-2xl border {{ $statusConfig['border'] }} {{ $statusConfig['bg'] }} backdrop-blur-md flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full {{ $statusConfig['dot'] }} animate-pulse"></span>
                            <span class="font-black text-sm uppercase tracking-widest {{ $statusConfig['text'] }}">
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
            @if(session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition.out.opacity.duration.500ms
                    class="mb-8 rounded-[2rem] bg-emerald-500 p-1 pr-1 shadow-2xl shadow-emerald-500/20">
                    <div class="bg-white rounded-[1.8rem] p-4 flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner">
                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-slate-900 text-sm">ดำเนินการสำเร็จ</h4>
                            <p class="text-xs font-bold text-slate-500">{{ session('status') }}</p>
                        </div>
                        <button @click="show = false"
                            class="w-10 h-10 rounded-xl hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Core Information -->
                    <div
                        class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden group">
                        <div class="p-10">
                            <div class="flex flex-wrap items-center gap-6 mb-10 pb-10 border-b border-slate-50">
                                <div
                                    class="flex flex-col items-center justify-center w-24 h-28 bg-brand-50 rounded-3xl p-2 border border-brand-100 shadow-inner group-hover:scale-105 transition-transform duration-500">
                                    <span
                                        class="text-xs text-brand-600 font-black uppercase tracking-wider mb-1">{{ $guardChange->duty_date->locale('th')->translatedFormat('M') }}</span>
                                    <span
                                        class="text-4xl font-black text-slate-800 leading-none">{{ $guardChange->duty_date->day }}</span>
                                    <span
                                        class="text-[10px] text-slate-400 font-bold mt-1">{{ $guardChange->duty_date->year + 543 }}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">
                                        DUTY POSITION</p>
                                    @php
                                        $dutyPositions = [
                                            'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                            'duty_officer' => 'นายทหารเวร',
                                            'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                                        ];
                                    @endphp
                                    <h3 class="text-3xl font-black text-slate-900 leading-tight">
                                        {{ $dutyPositions[$guardChange->duty_position] ?? $guardChange->duty_position }}
                                    </h3>
                                    <p class="text-sm font-bold text-slate-400 mt-2 flex items-center gap-2">
                                        <i data-lucide="hash" class="w-4 h-4 text-brand-500"></i>
                                        Guard Change Reference #{{ str_pad($guardChange->id, 5, '0', STR_PAD_LEFT) }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-6">
                                    <div class="flex items-center gap-4 group/item">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover/item:bg-brand-50 group-hover/item:text-brand-500 transition-colors">
                                            <i data-lucide="user" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                ORIGINAL PERSON</p>
                                            <p class="text-base font-black text-slate-800">
                                                {{ $guardChange->user->rank }}{{ $guardChange->user->name }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 group/item">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover/item:bg-emerald-50 group-hover/item:text-emerald-500 transition-colors">
                                            <i data-lucide="user-check" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                REPLACEMENT PERSON</p>
                                            <p class="text-base font-black text-slate-800">
                                                {{ $guardChange->replacementUser->rank }}{{ $guardChange->replacementUser->name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <div class="flex items-center gap-4 group/item">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover/item:bg-indigo-50 group-hover/item:text-indigo-500 transition-colors">
                                            <i data-lucide="layers" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                DEPARTMENT</p>
                                            <p class="text-base font-black text-slate-800">
                                                {{ $guardChange->user->department }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 group/item">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover/item:bg-amber-50 group-hover/item:text-amber-500 transition-colors">
                                            <i data-lucide="clock" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                                MODIFIED DATE</p>
                                            <p class="text-base font-black text-slate-800">
                                                {{ $guardChange->updated_at->locale('th')->translatedFormat('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($guardChange->remarks)
                                <div
                                    class="mt-10 p-8 bg-slate-50/50 rounded-[2rem] border border-slate-100 relative overflow-hidden group/remarks">
                                    <div
                                        class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                                        <i data-lucide="quote" class="w-24 h-24 text-slate-900"></i>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">REASON
                                        / REMARKS</p>
                                    <p class="text-lg text-slate-600 font-medium italic relative z-10 leading-relaxed">
                                        {{ $guardChange->remarks }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval Progression -->
                    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-10">
                        <div class="flex items-center justify-between mb-8 pb-8 border-b border-slate-50">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">ขั้นตอนการอนุมัติ</h3>
                                <p class="text-sm font-bold text-slate-400 mt-1">ตรวจสอบสถานะการเข้าถึงและการอนุมัติ</p>
                            </div>
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                <i data-lucide="git-pull-request" class="w-6 h-6"></i>
                            </div>
                        </div>

                        <div class="space-y-10">
                            <!-- Step 1: Replacement User -->
                            <div class="relative flex gap-8">
                                <div class="absolute top-10 left-6 -bottom-10 w-0.5 bg-slate-100"></div>
                                <div
                                    class="relative z-10 w-12 h-12 rounded-full flex items-center justify-center {{ $guardChange->is_replacement_accepted ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/30' : 'bg-slate-100 text-slate-400' }}">
                                    @if($guardChange->is_replacement_accepted)
                                        <i data-lucide="check" class="w-6 h-6"></i>
                                    @else
                                        <i data-lucide="circle" class="w-6 h-6"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-slate-900 uppercase tracking-widest text-xs">
                                        ผู้รับหน้าที่แทนยินยอม</h4>
                                    <p class="text-sm font-medium text-slate-500 mt-1">
                                        {{ $guardChange->replacementUser->rank }}{{ $guardChange->replacementUser->name }}
                                        {{ $guardChange->is_replacement_accepted ? 'ได้ยืนยันการรับช่วงต่อแล้วเมื่อ ' . $guardChange->replacement_accepted_at?->locale('th')->translatedFormat('d M Y') : 'ยังไม่ได้กดยืนยันการรับช่วงต่อ' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Step 2: Deputy Director -->
                            <div class="relative flex gap-8">
                                <div class="absolute top-10 left-6 -bottom-10 w-0.5 bg-slate-100"></div>
                                <div
                                    class="relative z-10 w-12 h-12 rounded-full flex items-center justify-center {{ $guardChange->director_approved_at ? 'bg-purple-500 text-white shadow-lg shadow-purple-500/30' : 'bg-slate-100 text-slate-400' }}">
                                    @if($guardChange->director_approved_at)
                                        <i data-lucide="check" class="w-6 h-6"></i>
                                    @else
                                        <i data-lucide="circle" class="w-6 h-6"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-slate-900 uppercase tracking-widest text-xs">รอง ผอ.
                                        พิจารณา</h4>
                                    <p class="text-sm font-medium text-slate-500 mt-1">
                                        @if($guardChange->directorApprover)
                                            {{ $guardChange->directorApprover->rank }}{{ $guardChange->directorApprover->name }}
                                            ได้พิจารณาอนุมัติเรียบร้อยเมื่อ
                                            {{ $guardChange->director_approved_at->locale('th')->translatedFormat('d M Y') }}
                                        @else
                                            กำลังรอการตรวจสอบจาก รอง ผอ.
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Step 3: Director -->
                            <div class="relative flex gap-8">
                                <div
                                    class="relative z-10 w-12 h-12 rounded-full flex items-center justify-center {{ $guardChange->status === 'approved' ? 'bg-rose-500 text-white shadow-lg shadow-rose-500/30' : 'bg-slate-100 text-slate-400' }}">
                                    @if($guardChange->status === 'approved')
                                        <i data-lucide="check" class="w-6 h-6"></i>
                                    @else
                                        <i data-lucide="circle" class="w-6 h-6"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-black text-slate-900 uppercase tracking-widest text-xs">
                                        ผู้อำนวยการอนุมัติ</h4>
                                    <p class="text-sm font-medium text-slate-500 mt-1">
                                        ขั้นตอนสุดท้ายในการยืนยันคำขอเปลี่ยนยาม</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Actions -->
                <div class="space-y-8">
                    <!-- Identity Summary -->
                    <div class="bg-indigo-900 rounded-[2.5rem] shadow-2xl p-8 relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-800 to-indigo-950"></div>
                        <div
                            class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl transition-transform group-hover:scale-150 duration-700">
                        </div>

                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.3em] mb-6">
                                REQUISITION ASSET</p>

                            <div class="flex items-center gap-4 mb-8 bg-white/5 p-4 rounded-3xl border border-white/10">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white shadow-xl overflow-hidden ring-4 ring-indigo-500/20">
                                    @if($guardChange->user->avatar)
                                        <img src="{{ asset('storage/' . $guardChange->user->avatar) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div
                                            class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl font-black capitalize">
                                            {{ substr($guardChange->user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="text-white font-black text-lg leading-tight uppercase">
                                        {{ $guardChange->user->name }}</h5>
                                    <p class="text-indigo-400 font-bold text-xs mt-1">{{ $guardChange->user->rank }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <a href="{{ route('guard-change.pdf', $guardChange) }}" target="_blank"
                                    class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white text-indigo-900 rounded-2xl shadow-xl hover:bg-slate-50 hover:-translate-y-1 transition-all duration-300 font-black uppercase tracking-widest text-xs">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                    Download Document
                                </a>

                                @if($guardChange->status === 'pending')
                                    <form action="{{ route('guard-change.cancel', $guardChange) }}" method="POST"
                                        onsubmit="return confirm('ยืนยันการยกเลิกคำขอเปลี่ยนยามนี้?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-2xl hover:bg-rose-500/20 hover:-translate-y-1 transition-all duration-300 font-black uppercase tracking-widest text-xs">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Cancel Request
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Helpful Notice -->
                    <div class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shadow-inner">
                                <i data-lucide="info" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-black text-slate-900 uppercase tracking-widest text-xs">Information Guide
                            </h4>
                        </div>
                        <p class="text-sm font-medium text-slate-500 leading-relaxed">
                            รายการเปลี่ยนยามนี้จะถือเป็นอันเสร็จสิ้นเมื่อได้รับการอนุมัติขั้นสุดท้ายจากผู้อำนวยการ
                            ท่านสามารถดาวน์โหลดเอกสาร PDF เพื่อเก็บไว้เป็นหลักฐานได้
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
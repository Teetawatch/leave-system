<x-app-layout>
    @section('title', 'รายการตรวจสอบและอนุมัติใบลา')

    <div class="min-h-screen bg-[#f8fafc]">
        {{-- ============ Light Cinematic Header ============ --}}
        <div class="relative bg-gradient-to-br from-white via-indigo-50/60 to-violet-50/40 pt-16 pb-28 overflow-hidden">
            {{-- Background Decoration --}}
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-200/20 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-violet-200/20 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                <div class="absolute top-1/3 right-1/4 w-[300px] h-[300px] bg-sky-200/15 rounded-full blur-[80px]"></div>
            </div>
            {{-- Subtle grid pattern --}}
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #6366f1 1px, transparent 1px); background-size: 24px 24px;"></div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div>
                        <nav class="flex items-center gap-2 text-indigo-400/80 transition-all mb-4 text-sm font-black tracking-widest uppercase">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span>ระบบอนุมัติ</span>
                            <span class="w-1 h-1 rounded-full bg-indigo-400/40"></span>
                            <span class="text-indigo-500">การจัดการใบลา</span>
                        </nav>
                        <h1 class="text-4xl md:text-6xl font-black text-slate-800 tracking-tight mb-4">
                            ระบบอนุมัติใบลา
                        </h1>
                        <p class="text-slate-500 max-w-2xl text-lg font-medium leading-relaxed">
                            ตรวจสอบและพิจารณาคำขอลาของบุคลากร ดำเนินการอนุมัติหรือปฏิเสธพร้อมลงลายมือชื่ออิเล็กทรอนิกส์
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Pending Counter --}}
                        <div class="bg-white/80 border border-slate-200/60 rounded-3xl px-8 py-6 backdrop-blur-md shadow-xl shadow-indigo-500/5">
                            <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.3em] mb-1">รอดำเนินการ</p>
                            <div class="flex items-baseline gap-3">
                                <span class="text-4xl font-black text-slate-800">{{ $requests->count() }}</span>
                                <span class="text-sm font-bold text-slate-400 uppercase">รายการ</span>
                            </div>
                        </div>
                        {{-- Approved Counter --}}
                        <div class="hidden lg:block bg-white/80 border border-slate-200/60 rounded-3xl px-8 py-6 backdrop-blur-md shadow-xl shadow-emerald-500/5">
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em] mb-1">อนุมัติแล้ว</p>
                            <div class="flex items-baseline gap-3">
                                <span class="text-4xl font-black text-slate-800">{{ $requests->where('status', 'approved')->count() }}</span>
                                <span class="text-sm font-bold text-slate-400 uppercase">เดือนนี้</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigation in Header --}}
                <div class="mt-10 flex items-center gap-3" x-data>
                    <button @click="$dispatch('set-tab', 'pending')"
                        class="px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-[0.15em] transition-all duration-300 border-2 cursor-pointer"
                        :class="$store.approvalTab === 'pending' 
                            ? 'bg-white text-slate-800 border-slate-200 shadow-xl shadow-slate-200/40 -translate-y-1' 
                            : 'bg-white/40 text-slate-400 border-slate-200/40 hover:bg-white/70 hover:text-slate-600 hover:border-slate-200'">
                        <span class="flex items-center gap-3">
                            <i data-lucide="inbox" class="w-5 h-5"></i>
                            รอดำเนินการ
                            @if($requests->count() > 0)
                            <span class="flex h-6 min-w-[24px] items-center justify-center rounded-full bg-amber-100 text-amber-700 text-[10px] font-black px-2">
                                {{ $requests->count() }}
                            </span>
                            @endif
                        </span>
                    </button>
                    <button @click="$dispatch('set-tab', 'history')"
                        class="px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-[0.15em] transition-all duration-300 border-2 cursor-pointer"
                        :class="$store.approvalTab === 'history' 
                            ? 'bg-white text-slate-800 border-slate-200 shadow-xl shadow-slate-200/40 -translate-y-1' 
                            : 'bg-white/40 text-slate-400 border-slate-200/40 hover:bg-white/70 hover:text-slate-600 hover:border-slate-200'">
                        <span class="flex items-center gap-3">
                            <i data-lucide="history" class="w-5 h-5"></i>
                            ประวัติย้อนหลัง
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ============ Main Content ============ --}}
        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20"
             x-data
             @set-tab.window="$store.approvalTab = $event.detail"
             x-init="$store.approvalTab = 'pending'">

            {{-- Success Alert --}}
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition.out.opacity.duration.500ms class="mb-8 rounded-[2.5rem] bg-emerald-500 p-1 shadow-2xl shadow-emerald-500/20">
                    <div class="bg-white rounded-[2.3rem] p-5 flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner">
                            <i data-lucide="shield-check" class="w-7 h-7"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-slate-900 text-base">ดำเนินการเรียบร้อย</h4>
                            <p class="text-sm font-bold text-slate-500">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="w-12 h-12 rounded-2xl hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors cursor-pointer">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- ============ Pending Tab ============ --}}
            <div x-show="$store.approvalTab === 'pending'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                @if($requests->isEmpty())
                    {{-- Empty State --}}
                    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-32 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
                        <div class="relative z-10">
                            <div class="w-40 h-40 bg-gradient-to-br from-indigo-50 to-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 border-8 border-white shadow-inner group">
                                <i data-lucide="inbox" class="w-20 h-20 text-slate-200 group-hover:scale-110 group-hover:text-indigo-400 transition-all duration-700"></i>
                            </div>
                            <h3 class="text-3xl font-black text-slate-900 mb-4">ไม่มีรายการรออนุมัติ</h3>
                            <p class="text-slate-500 max-w-sm mx-auto text-xl font-medium">ขณะนี้ไม่มีคำขอลาที่ต้องดำเนินการ ขอบคุณที่บริหารจัดการอย่างรวดเร็ว</p>
                        </div>
                    </div>
                @else
                    {{-- Request Cards --}}
                    <div class="grid grid-cols-1 gap-10">
                        @foreach($requests as $index => $req)
                            <div class="group bg-white rounded-[3rem] shadow-2xl shadow-slate-200/40 border border-slate-100 p-10 md:p-12 hover:shadow-indigo-500/10 transition-all duration-700 relative overflow-hidden"
                                 x-data="{ openApprove: false, openReject: false }">

                                {{-- Background Decor --}}
                                <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-50 rounded-bl-full -mr-20 -mt-20 opacity-30 group-hover:scale-125 transition-transform duration-1000 pointer-events-none"></div>

                                <div class="flex flex-col xl:flex-row gap-12 relative z-10">
                                    {{-- ===== Requester Profile ===== --}}
                                    <div class="flex-shrink-0 flex flex-row xl:flex-col items-center xl:items-start gap-8 xl:w-64 min-w-0 border-b xl:border-b-0 xl:border-r border-slate-100 pb-10 xl:pb-0 xl:pr-12">
                                        <div class="relative">
                                            <div class="w-28 h-28 rounded-[2.5rem] bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 flex items-center justify-center text-4xl font-black shadow-xl overflow-hidden ring-8 ring-white">
                                                @if($req->user->avatar)
                                                    <img src="{{ asset('storage/' . $req->user->avatar) }}" alt="{{ $req->user->name }}" class="w-full h-full object-cover transform scale-110 group-hover:scale-125 transition-transform duration-700">
                                                @else
                                                    {{ mb_substr($req->user->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-2xl border-4 border-white transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                                                <i data-lucide="file-text" class="w-6 h-6"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 xl:flex-none flex flex-wrap items-center gap-3 min-w-0 max-w-full">
                                            <h4 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight tracking-tight break-words w-full">{{ $req->user->rank }}{{ $req->user->name }}</h4>
                                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] bg-indigo-50 px-3 py-1 rounded-full">ผู้ขอลา</span>
                                            <span class="text-sm font-bold text-slate-400 flex items-center gap-1">
                                                <i data-lucide="building-2" class="w-4 h-4"></i>
                                                {{ $req->user->department }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- ===== Body Content ===== --}}
                                    <div class="flex-1 min-w-0">
                                        {{-- Leave Type & Date Badges --}}
                                        <div class="flex flex-wrap items-center gap-4 mb-8">
                                            <span class="inline-flex items-center gap-3 px-6 py-3 bg-indigo-50 text-indigo-700 rounded-2xl text-xs font-black tracking-[0.1em] shadow-sm border border-indigo-100 uppercase">
                                                <i data-lucide="bookmark" class="w-4 h-4 text-indigo-400"></i>
                                                {{ $req->leaveType->name }}
                                            </span>
                                            <span class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-slate-900 rounded-2xl text-xs font-black shadow-sm uppercase">
                                                <i data-lucide="calendar" class="w-4 h-4 text-indigo-500"></i>
                                                @thaidate($req->start_date) — @thaidate($req->end_date)
                                            </span>
                                            <span class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-slate-900 rounded-2xl text-xs font-black shadow-sm uppercase">
                                                <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                                                {{ $req->total_days + 0 }} วัน
                                            </span>
                                        </div>

                                        {{-- Approval Chain Track --}}
                                        @php
                                            $stepMap = [
                                                'supervisor' => 'pending_supervisor',
                                                'manager' => 'pending_manager',
                                                'deputy_director' => 'pending_deputy_director',
                                                'director' => 'pending_director',
                                            ];
                                            $steps = [
                                                'pending_supervisor' => ['label' => 'ผู้บังคับบัญชา', 'icon' => 'user-check', 'color' => 'emerald', 'step_key' => 'supervisor'],
                                                'pending_manager' => ['label' => 'ผู้บังคับบัญชา', 'icon' => 'users', 'color' => 'blue', 'step_key' => 'manager'],
                                                'pending_deputy_director' => ['label' => 'รอง ผอ.', 'icon' => 'signature', 'color' => 'purple', 'step_key' => 'deputy_director'],
                                                'pending_director' => ['label' => 'ผอ.', 'icon' => 'crown', 'color' => 'rose', 'step_key' => 'director'],
                                            ];
                                            $statusOrder = array_keys($steps);
                                            $currentIndex = array_search($req->status, $statusOrder);
                                            if ($currentIndex === false) $currentIndex = -1;

                                            // หาชื่อผู้รับผิดชอบแต่ละขั้นตอน
                                            $getApproverName = function($stepKey) use ($req) {
                                                // 1. ถ้าเป็นขั้นตอนที่อนุมัติแล้ว ดึงจาก approvals
                                                $approval = $req->approvals->where('step', $stepKey)->first();
                                                if ($approval && $approval->approver) {
                                                    return $approval->approver->rank . $approval->approver->name;
                                                }

                                                // 2. ดึงจาก relation ของ user (ผู้ขอลา)
                                                switch ($stepKey) {
                                                    case 'supervisor':
                                                        return $req->user->supervisor
                                                            ? $req->user->supervisor->rank . $req->user->supervisor->name
                                                            : null;
                                                    case 'manager':
                                                        return $req->user->manager
                                                            ? $req->user->manager->rank . $req->user->manager->name
                                                            : null;
                                                    case 'deputy_director':
                                                        $deputy = \App\Models\User::where('role', 'deputy_director')->first();
                                                        return $deputy ? $deputy->rank . $deputy->name : null;
                                                    case 'director':
                                                        $director = \App\Models\User::where('role', 'director')->first();
                                                        return $director ? $director->rank . $director->name : null;
                                                }
                                                return null;
                                            };
                                        @endphp
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                                            @foreach($steps as $key => $step)
                                                @php
                                                    $stepIdx = array_search($key, $statusOrder);
                                                    $personName = $getApproverName($step['step_key']);
                                                @endphp
                                                <div class="{{ $stepIdx < $currentIndex 
                                                    ? 'bg-emerald-50/50 border-2 border-emerald-100' 
                                                    : ($stepIdx == $currentIndex 
                                                        ? 'bg-' . $step['color'] . '-50/50 border-2 border-' . $step['color'] . '-200 ring-4 ring-' . $step['color'] . '-50' 
                                                        : 'bg-slate-50/50 border-2 border-slate-100') 
                                                }} rounded-[2.5rem] p-6 flex items-center gap-5 transition-all duration-300">
                                                    <div class="h-14 w-14 rounded-2xl bg-white shadow-md flex items-center justify-center {{ $stepIdx < $currentIndex 
                                                        ? 'text-emerald-500' 
                                                        : ($stepIdx == $currentIndex 
                                                            ? 'text-' . $step['color'] . '-500' 
                                                            : 'text-slate-300') }}">
                                                        @if($stepIdx < $currentIndex)
                                                            <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                                                        @elseif($stepIdx == $currentIndex)
                                                            <i data-lucide="{{ $step['icon'] }}" class="w-7 h-7"></i>
                                                        @else
                                                            <i data-lucide="circle-dashed" class="w-7 h-7"></i>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-[10px] font-black uppercase tracking-widest mb-1 {{ $stepIdx < $currentIndex 
                                                            ? 'text-emerald-600/60' 
                                                            : ($stepIdx == $currentIndex 
                                                                ? 'text-' . $step['color'] . '-600/60' 
                                                                : 'text-slate-400/60') }}">
                                                            @if($stepIdx < $currentIndex)
                                                                อนุมัติแล้ว
                                                            @elseif($stepIdx == $currentIndex)
                                                                ขั้นตอนปัจจุบัน
                                                            @else
                                                                รอดำเนินการ
                                                            @endif
                                                        </p>
                                                        <p class="text-base font-black {{ $stepIdx <= $currentIndex ? 'text-slate-800' : 'text-slate-400' }}">
                                                            {{ $step['label'] }}
                                                        </p>
                                                        @if($personName)
                                                            <p class="text-sm mt-1 truncate {{ $stepIdx < $currentIndex 
                                                                ? 'text-emerald-600 font-bold' 
                                                                : ($stepIdx == $currentIndex 
                                                                    ? 'text-' . $step['color'] . '-600 font-bold' 
                                                                    : 'text-slate-400 font-medium') }}">
                                                                @if($stepIdx < $currentIndex)
                                                                    <i data-lucide="check" class="w-3.5 h-3.5 inline -mt-0.5 mr-1"></i>
                                                                @elseif($stepIdx == $currentIndex)
                                                                    <i data-lucide="loader-2" class="w-3.5 h-3.5 inline -mt-0.5 mr-1 animate-spin"></i>รอ
                                                                @else
                                                                    <i data-lucide="user" class="w-3.5 h-3.5 inline -mt-0.5 mr-1"></i>
                                                                @endif
                                                                {{ $personName }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    @if($stepIdx < $currentIndex)
                                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                                            <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                                                        </div>
                                                    @elseif($stepIdx == $currentIndex)
                                                        <div class="w-3 h-3 rounded-full bg-amber-400 animate-pulse flex-shrink-0"></div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Reason --}}
                                        @if($req->reason)
                                            <div class="bg-slate-50/80 rounded-[2rem] p-8 border border-slate-100 relative group/quote">
                                                <i data-lucide="quote" class="absolute top-4 right-6 w-12 h-12 text-slate-200/50 group-hover/quote:text-indigo-200/50 transition-colors"></i>
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">เหตุผลการลา</p>
                                                <p class="text-lg text-slate-600 font-medium italic relative z-10">"{{ $req->reason }}"</p>
                                            </div>
                                        @endif

                                        {{-- Attachment --}}
                                        @if($req->attachment_path)
                                            <div class="mt-6">
                                                <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank"
                                                   class="inline-flex items-center gap-3 px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-100 transition-colors cursor-pointer group/attach">
                                                    <i data-lucide="paperclip" class="w-4 h-4 group-hover/attach:rotate-12 transition-transform"></i>
                                                    ดูเอกสารแนบ
                                                    <i data-lucide="external-link" class="w-3 h-3 opacity-50"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ===== Executive Decisions ===== --}}
                                    <div class="flex-shrink-0 xl:w-64 flex flex-col justify-center items-center gap-6 xl:pl-12 xl:border-l-4 xl:border-slate-50 border-double">
                                        @php
                                            $userApproval = $req->approvals->where('approver_id', Auth::id())->first();
                                            $hasAlreadyApproved = $userApproval && in_array($userApproval->action, ['approved', 'acknowledged']);
                                        @endphp

                                        @if($hasAlreadyApproved)
                                            {{-- Already Approved State --}}
                                            <div class="w-full bg-emerald-50/50 rounded-[2.5rem] p-10 border-2 border-dashed border-emerald-200 text-center">
                                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-4 shadow-lg border-4 border-emerald-100">
                                                    <i data-lucide="shield-check" class="w-10 h-10"></i>
                                                </div>
                                                <span class="block text-lg font-black text-emerald-800">ดำเนินการแล้ว</span>
                                                <span class="block text-xs font-bold text-emerald-600/60 mt-2 uppercase tracking-widest">
                                                    @thaidate($userApproval->updated_at)
                                                </span>
                                            </div>
                                        @else
                                            {{-- Approve Button --}}
                                            <button @click="openApprove = true" class="w-full group/btn relative px-8 py-6 bg-indigo-600 text-white rounded-[2rem] shadow-2xl shadow-indigo-600/20 hover:shadow-indigo-600/40 transition-all hover:-translate-y-2 overflow-hidden cursor-pointer">
                                                <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-indigo-700 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                                <div class="relative flex items-center justify-center gap-4">
                                                    <i data-lucide="check-circle-2" class="w-6 h-6 group-hover/btn:scale-125 transition-transform"></i>
                                                    <span class="text-sm font-black uppercase tracking-[0.2em]">
                                                        @if($req->status == 'pending_supervisor') อนุญาต
                                                        @elseif($req->status == 'pending_manager') อนุมัติ
                                                        @elseif($req->status == 'pending_deputy_director') รับทราบ
                                                        @else อนุมัติ
                                                        @endif
                                                    </span>
                                                </div>
                                            </button>

                                            {{-- Reject Button --}}
                                            <button @click="openReject = true" class="w-full group/btn relative px-8 py-5 bg-white text-slate-400 border-2 border-slate-200 rounded-[2rem] shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 hover:shadow-rose-500/10 transition-all hover:-translate-y-1 cursor-pointer">
                                                <div class="flex items-center justify-center gap-3">
                                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover/btn:scale-110 transition-transform"></i>
                                                    <span class="text-sm font-black uppercase tracking-[0.15em]">ไม่อนุมัติ</span>
                                                </div>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                @include('approvals.partials.decision_modals')
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ============ History Tab ============ --}}
            <div x-show="$store.approvalTab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-32 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
                    <div class="relative z-10">
                        <div class="w-40 h-40 bg-gradient-to-br from-indigo-50 to-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 border-8 border-white shadow-inner group">
                            <i data-lucide="archive" class="w-20 h-20 text-slate-200 group-hover:scale-110 group-hover:text-indigo-400 transition-all duration-700"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">ประวัติการอนุมัติ</h3>
                        <p class="text-slate-500 max-w-sm mx-auto text-xl font-medium">ส่วนนี้กำลังอยู่ระหว่างการพัฒนา</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
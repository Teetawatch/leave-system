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
                                            $leaveSlug    = strtolower($req->leaveType->slug ?? '');
                                            $isVacation   = $leaveSlug === 'vacation' || str_contains($req->leaveType->name ?? '', 'พักผ่อน');
                                            $isTemporary  = $leaveSlug === 'temporary';
                                            $isSickOrPersonal = in_array($leaveSlug, ['sick', 'personal']);
                                            $studentCourses   = ['หลักสูตรนายทหารพลาธิการชั้นนายเรือ ประจำปีงบประมาณ 69', 'หลักสูตรอาชีพเพื่อเลื่อนฐานะชั้น จ.อ.'];
                                            $isStudent = in_array($req->user->department, $studentCourses);

                                            // Resolve actual approver users for this request
                                            $supervisorUser = $req->user->supervisor;
                                            $managerUser    = $req->user->manager;
                                            // Use per-person deputy_id if set, otherwise fall back to role-based lookup
                                            $deputyUser  = $req->user->deputy ?? \App\Models\User::where('role', 'deputy_director')->first();
                                            $directorUser = \App\Models\User::where('role', 'director')->first();

                                            // Build dynamic steps based on leave type and employee chain
                                            $steps = [];
                                            $steps['pending_supervisor'] = [
                                                'label'    => 'ผู้อนุมัติขั้นที่ 1',
                                                'sublabel' => 'หัวหน้าแผนก',
                                                'icon'     => 'user-check',
                                                'color'    => 'emerald',
                                                'step_key' => 'supervisor',
                                                'user'     => $supervisorUser,
                                            ];

                                            if ($isTemporary) {
                                                // Temporary leave: supervisor only, no more steps
                                            } elseif ($isStudent && $isSickOrPersonal && $managerUser) {
                                                // Student course sick/personal: goes through manager
                                                $steps['pending_manager'] = [
                                                    'label'    => 'ผู้อนุมัติขั้นที่ 2',
                                                    'sublabel' => 'ผู้บังคับบัญชา',
                                                    'icon'     => 'users',
                                                    'color'    => 'blue',
                                                    'step_key' => 'manager',
                                                    'user'     => $managerUser,
                                                ];
                                            } else {
                                                // Normal flow: deputy (รับทราบ) then director
                                                $steps['pending_deputy_director'] = [
                                                    'label'    => 'ผู้รับทราบ',
                                                    'sublabel' => 'รอง ผอ.',
                                                    'icon'     => 'shield',
                                                    'color'    => 'violet',
                                                    'step_key' => 'deputy_director',
                                                    'user'     => $deputyUser,
                                                ];
                                                // Director step only for vacation leave
                                                if ($isVacation) {
                                                    $steps['pending_director'] = [
                                                        'label'    => 'ผู้อนุมัติขั้นที่ 2',
                                                        'sublabel' => 'ผอ.',
                                                        'icon'     => 'crown',
                                                        'color'    => 'rose',
                                                        'step_key' => 'director',
                                                        'user'     => $directorUser,
                                                    ];
                                                }
                                            }

                                            $statusOrder  = array_keys($steps);
                                            $currentIndex = array_search($req->status, $statusOrder);
                                            if ($currentIndex === false) $currentIndex = -1;

                                            $isFullyApproved = $req->status === 'approved';
                                            $isRejected      = $req->status === 'rejected';

                                            $getApproverName = function($stepKey) use ($req) {
                                                $approval = $req->approvals->where('step', $stepKey)->first();
                                                if ($approval && $approval->approver) {
                                                    return $approval->approver->rank . $approval->approver->name;
                                                }
                                                switch ($stepKey) {
                                                    case 'supervisor':
                                                        return $req->user->supervisor
                                                            ? $req->user->supervisor->rank . $req->user->supervisor->name : null;
                                                    case 'manager':
                                                        return $req->user->manager
                                                            ? $req->user->manager->rank . $req->user->manager->name : null;
                                                    case 'deputy_director':
                                                        $deputy = $req->user->deputy ?? \App\Models\User::where('role', 'deputy_director')->first();
                                                        return $deputy ? $deputy->rank . $deputy->name : null;
                                                    case 'director':
                                                        $director = \App\Models\User::where('role', 'director')->first();
                                                        return $director ? $director->rank . $director->name : null;
                                                }
                                                return null;
                                            };

                                            $getApproverAvatar = function($stepKey) use ($req) {
                                                $approval = $req->approvals->where('step', $stepKey)->first();
                                                if ($approval && $approval->approver) {
                                                    return $approval->approver->avatar;
                                                }
                                                switch ($stepKey) {
                                                    case 'supervisor':
                                                        return $req->user->supervisor?->avatar;
                                                    case 'manager':
                                                        return $req->user->manager?->avatar;
                                                    case 'deputy_director':
                                                        $deputy = $req->user->deputy ?? \App\Models\User::where('role', 'deputy_director')->first();
                                                        return $deputy?->avatar;
                                                    case 'director':
                                                        $director = \App\Models\User::where('role', 'director')->first();
                                                        return $director?->avatar;
                                                }
                                                return null;
                                            };

                                            $getApproverInitial = function($stepKey) use ($req) {
                                                $approval = $req->approvals->where('step', $stepKey)->first();
                                                if ($approval && $approval->approver) {
                                                    return mb_substr($approval->approver->name, 0, 1);
                                                }
                                                switch ($stepKey) {
                                                    case 'supervisor':
                                                        return $req->user->supervisor ? mb_substr($req->user->supervisor->name, 0, 1) : '?';
                                                    case 'manager':
                                                        return $req->user->manager ? mb_substr($req->user->manager->name, 0, 1) : '?';
                                                    case 'deputy_director':
                                                        $deputy = $req->user->deputy ?? \App\Models\User::where('role', 'deputy_director')->first();
                                                        return $deputy ? mb_substr($deputy->name, 0, 1) : '?';
                                                    case 'director':
                                                        $director = \App\Models\User::where('role', 'director')->first();
                                                        return $director ? mb_substr($director->name, 0, 1) : '?';
                                                }
                                                return '?';
                                            };

                                            $getApprovedAt = function($stepKey) use ($req) {
                                                $approval = $req->approvals->where('step', $stepKey)->first();
                                                return ($approval && $approval->approved_at)
                                                    ? \Carbon\Carbon::parse($approval->approved_at)->locale('th')->isoFormat('D MMM YY')
                                                    : null;
                                            };
                                        @endphp

                                        {{-- Section Header --}}
                                        <div class="flex items-center gap-3 mb-5">
                                            <div class="h-px flex-1 bg-gradient-to-r from-slate-100 to-transparent"></div>
                                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 flex items-center gap-2">
                                                <i data-lucide="git-branch" class="w-3.5 h-3.5"></i>
                                                สายการอนุมัติ
                                            </span>
                                            <div class="h-px flex-1 bg-gradient-to-l from-slate-100 to-transparent"></div>
                                        </div>

                                        {{-- Stepper --}}
                                        @php
                                            $stepCount = count($steps);
                                            $gridCols  = $stepCount === 1 ? 'grid-cols-1 max-w-[160px] mx-auto'
                                                       : ($stepCount === 2 ? 'grid-cols-2'
                                                       : ($stepCount === 3 ? 'grid-cols-3'
                                                       : 'grid-cols-2 sm:grid-cols-4'));
                                        @endphp
                                        <div class="relative mb-8">
                                            {{-- Connector line background --}}
                                            @if($stepCount > 1)
                                            <div class="hidden sm:block absolute top-7 left-[calc({{ 100 / ($stepCount * 2) }}%+1.75rem)] right-[calc({{ 100 / ($stepCount * 2) }}%+1.75rem)] h-0.5 bg-slate-100 z-0"></div>
                                            @endif

                                            <div class="grid {{ $gridCols }} gap-y-6 gap-x-2 relative z-10">
                                                @foreach($steps as $key => $step)
                                                    @php
                                                        $stepIdx       = array_search($key, $statusOrder);
                                                        $isDone        = $isFullyApproved || $stepIdx < $currentIndex;
                                                        $isActive      = !$isFullyApproved && $stepIdx == $currentIndex;
                                                        $isPending     = !$isDone && !$isActive;
                                                        $personName    = $getApproverName($step['step_key']);
                                                        $personAvatar  = $getApproverAvatar($step['step_key']);
                                                        $personInitial = $getApproverInitial($step['step_key']);
                                                        $approvedAt    = $getApprovedAt($step['step_key']);
                                                    @endphp
                                                    <div class="flex flex-col items-center text-center gap-2 px-1">
                                                        {{-- Step bubble --}}
                                                        <div class="relative">
                                                            @if($isDone)
                                                                <div class="w-14 h-14 rounded-2xl bg-emerald-500 shadow-lg shadow-emerald-200 flex items-center justify-center ring-4 ring-emerald-50 overflow-hidden relative">
                                                                    @if($personAvatar)
                                                                        <img src="{{ asset('storage/' . $personAvatar) }}" alt="{{ $personName }}" class="w-full h-full object-cover opacity-70">
                                                                        <div class="absolute inset-0 flex items-center justify-center bg-emerald-500/50">
                                                                            <i data-lucide="check" class="w-6 h-6 text-white drop-shadow"></i>
                                                                        </div>
                                                                    @else
                                                                        <i data-lucide="check" class="w-6 h-6 text-white"></i>
                                                                    @endif
                                                                </div>
                                                            @elseif($isActive)
                                                                <div class="w-14 h-14 rounded-2xl bg-white border-2 border-{{ $step['color'] }}-400 shadow-lg shadow-{{ $step['color'] }}-100 flex items-center justify-center ring-4 ring-{{ $step['color'] }}-50 overflow-hidden">
                                                                    @if($personAvatar)
                                                                        <img src="{{ asset('storage/' . $personAvatar) }}" alt="{{ $personName }}" class="w-full h-full object-cover">
                                                                    @else
                                                                        <i data-lucide="{{ $step['icon'] }}" class="w-6 h-6 text-{{ $step['color'] }}-500"></i>
                                                                    @endif
                                                                </div>
                                                                {{-- Pulse dot --}}
                                                                <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                                                    <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-amber-400 border-2 border-white"></span>
                                                                </span>
                                                            @else
                                                                <div class="w-14 h-14 rounded-2xl bg-slate-50 border-2 border-slate-100 flex items-center justify-center overflow-hidden">
                                                                    @if($personAvatar)
                                                                        <img src="{{ asset('storage/' . $personAvatar) }}" alt="{{ $personName }}" class="w-full h-full object-cover opacity-40 grayscale">
                                                                    @else
                                                                        <i data-lucide="{{ $step['icon'] }}" class="w-6 h-6 text-slate-300"></i>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            {{-- Avatar initials fallback shown as small overlay when no avatar --}}
                                                        </div>

                                                        {{-- Status badge --}}
                                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full
                                                            {{ $isDone   ? 'bg-emerald-100 text-emerald-600'
                                                            : ($isActive ? 'bg-' . $step['color'] . '-100 text-' . $step['color'] . '-600'
                                                            : 'bg-slate-100 text-slate-400') }}">
                                                            @if($isDone) อนุมัติแล้ว
                                                            @elseif($isActive) กำลังรอ
                                                            @else รอดำเนินการ
                                                            @endif
                                                        </span>

                                                        {{-- Label --}}
                                                        <div>
                                                            <p class="text-sm font-black leading-tight {{ $isPending ? 'text-slate-400' : 'text-slate-800' }}">
                                                                {{ $step['label'] }}
                                                            </p>
                                                            <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $step['sublabel'] }}</p>
                                                        </div>

                                                        {{-- Approver name card --}}
                                                        @if($personName)
                                                            <div class="w-full max-w-[140px] rounded-xl px-2.5 py-1.5
                                                                {{ $isDone   ? 'bg-emerald-50 border border-emerald-100'
                                                                : ($isActive ? 'bg-' . $step['color'] . '-50 border border-' . $step['color'] . '-100'
                                                                : 'bg-slate-50 border border-slate-100') }}">
                                                                <p class="text-[10px] font-bold truncate leading-tight
                                                                    {{ $isDone   ? 'text-emerald-700'
                                                                    : ($isActive ? 'text-' . $step['color'] . '-700'
                                                                    : 'text-slate-400') }}">
                                                                    {{ $personName }}
                                                                </p>
                                                                @if($approvedAt && $isDone)
                                                                    <p class="text-[9px] text-emerald-500 font-medium mt-0.5">{{ $approvedAt }}</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
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

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', (id) => ({
            signaturePad: null,
            useSaved: {{ Auth::user()->signature ? 'true' : 'false' }},
            isCanvasEmpty: true,
            init() {
                this.$watch('openApprove', (value) => {
                    if (value) {
                        this.$nextTick(() => {
                            if (!this.signaturePad) {
                                this.initCanvas();
                            } else {
                                this.resizeCanvas();
                            }
                        });
                    }
                });
            },
            initCanvas() {
                const canvas = document.getElementById('signature-canvas-' + id);
                if (canvas) {
                    this.signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(255, 255, 255, 0)',
                        penColor: 'rgb(15, 23, 42)',
                        onBegin: () => {
                            this.isCanvasEmpty = false;
                        }
                    });
                    this.resizeCanvas();
                }
            },
            resizeCanvas() {
                const canvas = document.getElementById('signature-canvas-' + id);
                if (canvas && this.signaturePad) {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    this.signaturePad.clear();
                    this.isCanvasEmpty = true;
                }
            },
            clearSignature() {
                if (this.signaturePad) {
                    this.signaturePad.clear();
                    this.isCanvasEmpty = true;
                }
            },
            submitForm(event) {
                if (this.signaturePad && !this.signaturePad.isEmpty()) {
                    document.getElementById('signature-input-' + id).value = this.signaturePad.toDataURL();
                }
                document.getElementById('form-approve-' + id).submit();
            }
        }));
    });
</script>
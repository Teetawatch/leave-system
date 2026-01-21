<x-app-layout>
    @section('title', 'รายการรออนุมัติ (Pending Approvals)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 relative">
            <div class="relative z-10 p-2">
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-amber-400 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 text-white flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i data-lucide="hourglass" class="w-5 h-5"></i>
                    </span>
                    รายการรออนุมัติ
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">จัดการคำขอวันลาของพนักงานในความดูแลของคุณ</p>
            </div>
            
             <div class="flex items-center gap-2">
                <span class="px-4 py-2 bg-white rounded-xl text-slate-500 text-sm font-bold border border-slate-200 shadow-sm">
                    <i data-lucide="list-checks" class="w-4 h-4 mr-2 text-brand-600"></i>
                    รออนุมัติ: <span class="text-slate-800">{{ $requests->count() }}</span> รายการ
                </span>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms class="mb-8 rounded-2xl bg-emerald-50 p-4 border border-emerald-100 shadow-lg shadow-emerald-500/10 flex items-center gap-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-400 rounded-full blur-2xl opacity-10"></div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600">
                    <i data-lucide="check-circle" class="w-5 h-5 text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-emerald-800">ดำเนินการเรียบร้อย</h4>
                    <p class="text-sm font-medium text-emerald-600">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="z-10 bg-white/50 hover:bg-white rounded-lg p-2 text-emerald-500 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if($requests->isEmpty())
             <!-- Premium Empty State -->
             <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-20 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-1/2 -ml-40 -mt-20 w-80 h-80 bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition-transform duration-500 border-4 border-white shadow-inner">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-slate-300 text-6xl group-hover:text-emerald-400 transition-colors"></i>
                        <div class="absolute bottom-1 right-1 w-10 h-10 bg-emerald-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg transform translate-y-2 translate-x-2">
                             <i data-lucide="check" class="w-4 h-4 text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3">ไม่มีรายการค้าง</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg font-medium">เยี่ยมมาก! คุณดำเนินการอนุมัติครบทุกรายการแล้ว</p>
                </div>
            </div>
        @else
            <!-- Card Grid Layout -->
            <div class="grid grid-cols-1 gap-6">
                @foreach($requests as $req)
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 hover:-translate-y-1 relative group overflow-hidden"
                     x-data="{ openReject: false, openApprove: false }">
                    
                    <!-- Top Decor -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-slate-50 to-slate-100 rounded-bl-[4rem] -mr-10 -mt-10 opacity-50 group-hover:scale-125 transition-transform duration-500"></div>

                    <div class="flex flex-col lg:flex-row gap-8 relative z-10">
                        
                        <!-- Left: Date & Avatar -->
                        <div class="flex-shrink-0 flex flex-row lg:flex-col items-center gap-6 lg:w-40 border-b lg:border-b-0 lg:border-r border-slate-50 pb-6 lg:pb-0 lg:pr-8">
                             <!-- Date Ticket -->
                            <div class="flex flex-col items-center justify-center w-24 h-28 bg-slate-50 rounded-2xl p-2 border border-slate-100 group-hover:bg-brand-50/20 group-hover:border-brand-100 transition-colors shadow-sm">
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">{{ \Carbon\Carbon::parse($req->start_date)->locale('th')->isoFormat('MMM') }}</span>
                                <span class="text-4xl font-black text-slate-800 leading-none">{{ \Carbon\Carbon::parse($req->start_date)->day }}</span>
                                <span class="text-[10px] text-slate-400 font-bold mt-1">{{ \Carbon\Carbon::parse($req->start_date)->year + 543 }}</span>
                            </div>
                            
                            <!-- User Mini Profile -->
                            <div class="flex items-center lg:flex-col gap-3 text-left lg:text-center flex-1 lg:flex-none">
                                <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-brand-100 to-brand-200 text-brand-700 flex items-center justify-center text-lg font-bold shadow-md shadow-brand-500/10 ring-2 ring-white overflow-hidden">
                                    @if($req->user->avatar)
                                        <img src="{{ asset('storage/' . $req->user->avatar) }}" alt="{{ $req->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($req->user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div>
                                    <h5 class="text-sm font-bold text-slate-900 clamp-1">{{ $req->user->rank }} {{ $req->user->name }}</h5>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide clamp-1 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-0.5">{{ $req->user->department }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Middle: Content -->
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <div class="flex flex-wrap items-center gap-3 mb-5">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold shadow-sm
                                    {{ $req->leaveType->slug == 'sick' ? 'bg-orange-50 text-orange-600 ring-1 ring-orange-100' : '' }}
                                    {{ $req->leaveType->slug == 'vacation' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : '' }}
                                    {{ $req->leaveType->slug == 'personal' ? 'bg-purple-50 text-purple-600 ring-1 ring-purple-100' : 'bg-slate-50 text-slate-600 ring-1 ring-slate-100' }}">
                                    @if($req->leaveType->slug == 'sick') <i data-lucide="pill" class="w-5 h-5 mr-2"></i>
                                    @elseif($req->leaveType->slug == 'vacation') <i data-lucide="plane" class="w-5 h-5 mr-2"></i>
                                    @else <i data-lucide="briefcase" class="w-4 h-4 mr-2"></i>
                                    @endif
                                    {{ $req->leaveType->name }}
                                </span>
                                
                                <span class="flex items-center px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                    <i data-lucide="timer" class="w-4 h-4 mr-1.5 text-slate-400"></i>{{ $req->total_days + 0 }} วัน
                                </span>

                                <span class="flex items-center px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                    <i data-lucide="calendar" class="w-4 h-4 mr-1.5 text-slate-400"></i>
                                    @thaidatefull($req->start_date) - @thaidatefull($req->end_date)
                                </span>
                            </div>

                            <div class="mb-6 bg-slate-50 p-5 rounded-2xl border border-slate-100 relative">
                                <i data-lucide="quote" class="w-6 h-6 absolute top-3 left-3 text-slate-200 text-2xl"></i>
                                <p class="text-slate-700 text-base leading-relaxed break-words font-medium italic relative z-10 pl-4">
                                    "{{ $req->reason }}"
                                </p>
                            </div>

                            <div class="flex items-center gap-4 text-xs font-bold">
                                @if($req->attachment_path)
                                    <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="group flex items-center gap-3 px-4 py-3 bg-brand-50/40 hover:bg-brand-50 border border-brand-100/50 hover:border-brand-200 rounded-2xl transition-all duration-300 hover:shadow-lg hover:shadow-brand-500/10 hover:-translate-y-0.5">
                                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm text-brand-600 ring-1 ring-brand-100 group-hover:scale-110 transition-transform duration-300">
                                            <i data-lucide="file-check" class="w-5 h-5"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-brand-400 uppercase tracking-wider leading-none mb-1">เอกสารประกอบ</span>
                                            <span class="text-sm font-bold text-slate-700 group-hover:text-brand-700 transition-colors">คลิกเพื่อตรวจสอบ</span>
                                        </div>
                                        <i data-lucide="chevron-right" class="w-4 h-4 text-brand-300 ml-1 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                @else
                                    <span class="flex items-center gap-2 text-slate-400">
                                        <i data-lucide="paperclip" class="w-4 h-4 text-lg opacity-50"></i> ไม่มีเอกสาร
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex-shrink-0 w-full lg:w-48 flex flex-col justify-center items-center gap-3 pt-6 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-50 lg:pl-8">
                            
                            @php
                                // Check if current user has already approved this request
                                $userApproval = $req->approvals->where('approver_id', Auth::id())->first();
                                $hasAlreadyApproved = $userApproval && in_array($userApproval->action, ['approved', 'acknowledged']);
                                
                                // Check if this is Step 2 (Deputy Director - no signature needed)
                                $isDeputyDirectorStep = $req->status == 'pending_deputy_director';
                                $isManagerStep = $req->status == 'pending_manager';
                                $isVacation = strtolower($req->leaveType->slug ?? '') === 'vacation';
                            @endphp
                            
                            @if($hasAlreadyApproved)
                                <!-- Already Approved State -->
                                <div class="w-full text-center">
                                    <div class="w-full flex items-center justify-center gap-3 px-4 py-4 bg-slate-100 text-slate-400 rounded-2xl cursor-not-allowed font-bold">
                                        <i data-lucide="check-check" class="w-4 h-4 text-lg"></i>
                                        <span>อนุมัติแล้ว</span>
                                    </div>
                                    <div class="mt-3 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl">
                                        <p class="text-amber-700 text-xs font-bold flex items-center gap-2">
                                            <i data-lucide="hourglass" class="w-5 h-5"></i>
                                            รอผู้บังคับบัญชาท่านต่อไป
                                        </p>
                                    </div>
                                </div>
                            @else
                                <button @click="openApprove = true" class="w-full group flex items-center justify-center gap-3 px-4 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-1 font-bold">
                                    <i data-lucide="check" class="w-4 h-4 text-lg group-hover:scale-110 transition-transform"></i>
                                    @if($req->status == 'pending_supervisor')
                                        อนุญาต
                                    @elseif($req->status == 'pending_manager')
                                        อนุมัติ
                                    @elseif($req->status == 'pending_deputy_director')
                                        รับทราบ
                                    @elseif($req->status == 'pending_director')
                                        @if($isVacation)
                                            อนุญาต
                                        @else
                                            รับทราบ
                                        @endif
                                    @else
                                        อนุมัติ
                                    @endif
                                </button>
                                
                                <button @click="openReject = true" class="w-full group flex items-center justify-center gap-3 px-4 py-4 bg-white border-2 border-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 rounded-2xl transition-all font-bold hover:-translate-y-1">
                                    <i data-lucide="x" class="w-4 h-4 text-lg group-hover:scale-110 transition-transform"></i> ปฏิเสธ
                                </button>
                            @endif
                        </div>
                    </div>

                    @php
                        // Step 2 (Deputy Director) does NOT require signature
                        $isAcknowledgeAction = $req->status == 'pending_deputy_director';
                    @endphp

                    <!-- Approve Modal -->
                    <template x-teleport="body">
                    <div x-show="openApprove" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openApprove = false">
                                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md"></div>
                            </div>

                            <div class="bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all relative inline-block align-middle"
                                 style="{{ $isAcknowledgeAction ? 'width: 90vw; max-width: 500px;' : 'width: 90vw; height: 90vh; display: flex; flex-direction: column;' }}"
                                 x-data="signaturePad({{ $req->id }})">
                                
                                <form action="{{ route('approvals.approve', $req->id) }}" method="POST" style="{{ $isAcknowledgeAction ? '' : 'height: 100%; display: flex; flex-direction: column; overflow-y: auto;' }}">
                                    @csrf
                                    <input type="hidden" name="signature" id="signature-input-{{ $req->id }}">
                                    <input type="hidden" name="is_acknowledge" value="{{ $isAcknowledgeAction ? '1' : '0' }}">
                                    
                                    <div class="bg-white p-8">
                                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full {{ $isAcknowledgeAction ? 'bg-blue-50 text-blue-500' : ($isManagerStep ? 'bg-amber-50 text-amber-500' : 'bg-emerald-50 text-emerald-500') }} mb-6 shadow-inner">
                                            @if($isAcknowledgeAction)
                                                <i data-lucide="eye" class="w-10 h-10"></i>
                                            @elseif($isManagerStep)
                                                <i data-lucide="shield-check" class="w-10 h-10"></i>
                                            @else
                                                <i data-lucide="check" class="w-10 h-10"></i>
                                            @endif
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-2xl font-black text-slate-900 tracking-tight" id="modal-title">
                                                @if($req->status == 'pending_supervisor')
                                                    อนุญาตคำขอ
                                                @elseif($req->status == 'pending_manager')
                                                    อนุมัติคำขอ (ผู้บังคับบัญชา)
                                                @elseif($req->status == 'pending_deputy_director')
                                                    รับทราบคำขอ
                                                @elseif($req->status == 'pending_director')
                                                    @if($isVacation)
                                                        อนุญาตคำขอ
                                                    @else
                                                        รับทราบคำขอ
                                                    @endif
                                                @else
                                                    อนุมัติคำขอ
                                                @endif
                                            </h3>
                                            <div class="mt-2 text-base text-slate-500">
                                                @if($req->status == 'pending_supervisor')
                                                    อนุญาต
                                                @elseif($req->status == 'pending_manager')
                                                    อนุมัติ
                                                @elseif($req->status == 'pending_deputy_director')
                                                    รับทราบ
                                                @elseif($req->status == 'pending_director')
                                                    @if($isVacation)
                                                        อนุญาต
                                                    @else
                                                        รับทราบ
                                                    @endif
                                                @else
                                                    อนุมัติ
                                                @endif
                                                <span class="font-bold text-slate-800">{{ $req->leaveType->name }}</span> ของ <span class="font-bold text-slate-800">{{ $req->user->name }}</span>?
                                            </div>
                                            
                                            @if(false)
                                                {{-- All stages now require signature --}}
                                            @else
                                                <!-- Signature Option -->
                                                <div class="mt-6">
                                                    @if(Auth::user()->signature)
                                                    <div class="flex items-center justify-center gap-4 mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                        <label class="inline-flex items-center cursor-pointer gap-2">
                                                            <input type="radio" value="0" name="use_saved_radios" @click="useSaved = false; $nextTick(() => { resizeCanvas(); })" :checked="!useSaved" class="form-radio text-emerald-500 focus:ring-emerald-500 border-slate-300">
                                                            <span class="text-sm font-bold text-slate-700">วาดลายเซ็นใหม่</span>
                                                        </label>
                                                        <div class="w-px h-6 bg-slate-300"></div>
                                                        <label class="inline-flex items-center cursor-pointer gap-2">
                                                            <input type="radio" value="1" name="use_saved_radios" @click="useSaved = true" :checked="useSaved" class="form-radio text-emerald-500 focus:ring-emerald-500 border-slate-300">
                                                            <span class="text-sm font-bold text-slate-700">ใช้ลายเซ็นที่บันทึกไว้</span>
                                                        </label>
                                                    </div>
                                                    <input type="hidden" name="use_saved_signature" :value="useSaved ? '1' : '0'">
                                                    @endif

                                                    <!-- Draw Signature -->
                                                    <div x-show="!useSaved" class="text-left">
                                                        <div class="flex justify-between items-end mb-2">
                                                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">ลงลายมือชื่อ</label>
                                                            <button type="button" @click="clearSignature()" class="text-xs font-bold text-brand-600 hover:text-red-500 transition-colors">
                                                                <i data-lucide="eraser" class="w-4 h-4 mr-1"></i> ล้าง
                                                            </button>
                                                        </div>
                                                        <div class="border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 relative overflow-hidden h-48 hover:border-brand-300 hover:bg-brand-50/20 transition-all cursor-crosshair">
                                                            <canvas id="signature-canvas-{{ $req->id }}" class="w-full h-full"></canvas>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400 mt-2 font-medium text-center">กรุณาเซ็นชื่อในกรอบด้านบน (ใช้เมาส์หรือนิ้วสัมผัส)</p>
                                                    </div>

                                                    <!-- Saved Signature Preview -->
                                                    @if(Auth::user()->signature)
                                                    <div x-show="useSaved" style="display: none;" class="text-left">
                                                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">ลายเซ็นที่บันทึกไว้</label>
                                                        <div class="border-2 border-solid border-emerald-100 rounded-2xl bg-emerald-50/30 p-4 h-48 flex items-center justify-center relative overflow-hidden">
                                                            <img src="{{ asset('storage/' . Auth::user()->signature) }}" class="max-h-full max-w-full object-contain">
                                                            <div class="absolute top-2 right-2 bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-[10px] font-bold">
                                                                <i data-lucide="check-circle" class="w-5 h-5 mr-1"></i> ใช้ลายเซ็นเดิม
                                                            </div>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400 mt-2 font-medium text-center">ระบบจะใช้ลายเซ็นจากโปรไฟล์ของคุณ</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="mt-6 text-left">
                                                <label for="comment" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">ความคิดเห็น (ถ้ามี)</label>
                                                <textarea name="comment" rows="2" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-medium" placeholder="ระบุความคิดเห็นเพิ่มเติม..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-slate-100">
                                        <button type="button" @click="openApprove = false" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-800 focus:outline-none transition-all">
                                            ยกเลิก
                                        </button>
                                        <button type="{{ $isAcknowledgeAction ? 'submit' : 'button' }}" {{ $isAcknowledgeAction ? '' : '@click=submitForm($event)' }} class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl border border-transparent shadow-lg {{ $isAcknowledgeAction ? 'shadow-blue-500/30 bg-blue-500 hover:bg-blue-600' : ($isManagerStep ? 'shadow-amber-500/30 bg-amber-500 hover:bg-amber-600' : 'shadow-emerald-500/30 bg-emerald-500 hover:bg-emerald-600') }} text-white font-bold focus:outline-none transform hover:-translate-y-0.5 transition-all">
                                            @if($isAcknowledgeAction)
                                                <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                                            @elseif($isManagerStep)
                                                <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                                            @else
                                                <i data-lucide="check" class="w-4 h-4 mr-2"></i>
                                            @endif 
                                            @if($req->status == 'pending_supervisor')
                                                ยืนยันอนุญาต
                                            @elseif($req->status == 'pending_manager')
                                                ยืนยันอนุมัติ
                                            @elseif($req->status == 'pending_deputy_director')
                                                ยืนยันรับทราบ
                                            @elseif($req->status == 'pending_director')
                                                @if($isVacation)
                                                    ยืนยันอนุญาต
                                                @else
                                                    ยืนยันรับทราบ
                                                @endif
                                            @else
                                                ยืนยันอนุมัติ
                                            @endif
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </template>

                    <!-- Reject Modal -->
                    <template x-teleport="body">
                    <div x-show="openReject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openReject = false">
                                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md"></div>
                            </div>

                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                            <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form action="{{ route('approvals.reject', $req->id) }}" method="POST">
                                    @csrf
                                    <div class="bg-white p-8">
                                        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-rose-50 text-rose-500 mb-6 shadow-inner">
                                            <i data-lucide="x" class="w-4 h-4 text-4xl"></i>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-2xl font-black text-slate-900 tracking-tight" id="modal-title">
                                                ปฏิเสธคำขอ
                                            </h3>
                                            <div class="mt-2">
                                                <p class="text-base text-slate-500">
                                                    คุณต้องการปฏิเสธคำขอลาของ <span class="font-bold text-slate-800">{{ $req->user->name }}</span>?
                                                </p>
                                            </div>
                                            <div class="mt-8 text-left">
                                                <label for="comment" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">เหตุผลการปฏิเสธ <span class="text-rose-500">*</span></label>
                                                <textarea name="comment" rows="3" required class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm font-medium" placeholder="ระบุเหตุผล..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-slate-100">
                                        <button type="button" @click="openReject = false" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-800 focus:outline-none transition-all">
                                            ยกเลิก
                                        </button>
                                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl border border-transparent shadow-lg shadow-rose-500/30 bg-rose-500 text-white font-bold hover:bg-rose-600 focus:outline-none transform hover:-translate-y-0.5 transition-all">
                                            <i data-lucide="x" class="w-4 h-4 mr-2"></i> ยืนยันปฏิเสธ
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </template>

                </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', (id) => ({
            signaturePad: null,
            useSaved: false,
            init() {
                // Wait for modal to be visible then init
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
                        backgroundColor: 'rgba(255, 255, 255, 0)'
                    });
                    this.resizeCanvas();
                }
            },
            resizeCanvas() {
                const canvas = document.getElementById('signature-canvas-' + id);
                if (canvas && this.signaturePad) {
                    const ratio =  Math.max(window.devicePixelRatio || 1, 1);
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    this.signaturePad.clear(); // Clear to make sure it's clean or re-draw if needed (but clear is safer for basic implementation)
                }
            },
            clearSignature() {
                if (this.signaturePad) {
                    this.signaturePad.clear();
                }
            },
            submitForm(event) {
                if (this.signaturePad && !this.signaturePad.isEmpty()) {
                    document.getElementById('signature-input-' + id).value = this.signaturePad.toDataURL();
                }
                event.target.closest('form').submit();
            }
        }));
    });
</script>

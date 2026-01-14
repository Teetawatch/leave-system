<x-app-layout>
    @section('title', 'อนุมัติคำขอเปลี่ยนยาม (รอง ผอ.)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 relative">
            <div class="relative z-10 p-2">
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-purple-400 rounded-full blur-3xl opacity-20 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 text-white flex items-center justify-center shadow-lg shadow-purple-500/30">
                        <i data-lucide="stamp" class="w-6 h-6"></i>
                    </span>
                    คำขอเปลี่ยนยามรอ รอง ผอ. อนุมัติ
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">อนุมัติคำขอเปลี่ยนยามที่ผ่านการยินยอมจากผู้รับมอบหมายแล้ว</p>
            </div>
            
             <div class="flex items-center gap-2">
                <span class="px-4 py-2 bg-white rounded-xl text-slate-500 text-sm font-bold border border-slate-200 shadow-sm">
                    <i data-lucide="list-checks" class="w-4 h-4 mr-2 text-purple-600"></i>
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
             <!-- Empty State -->
             <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-20 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-1/2 -ml-40 -mt-20 w-80 h-80 bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition-transform duration-500 border-4 border-white shadow-inner">
                        <i data-lucide="user-x" class="w-16 h-16 text-slate-300 group-hover:text-purple-400 transition-colors"></i>
                        <div class="absolute bottom-1 right-1 w-10 h-10 bg-purple-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg transform translate-y-2 translate-x-2">
                             <i data-lucide="check" class="w-4 h-4 text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3">ไม่มีรายการค้าง</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg font-medium">ไม่มีคำขอเปลี่ยนยามที่รอการอนุมัติ</p>
                </div>
            </div>
        @else
            <!-- Card Grid Layout -->
            <div class="grid grid-cols-1 gap-6">
                @foreach($requests as $req)
                @php
                    $dutyPositions = [
                        'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                        'duty_officer' => 'นายทหารเวร',
                        'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                    ];
                @endphp
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 hover:-translate-y-1 relative group overflow-hidden"
                     x-data="{ openApprove: false }">
                    
                    <!-- Top Decor -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-50 to-purple-100 rounded-bl-[4rem] -mr-10 -mt-10 opacity-50 group-hover:scale-125 transition-transform duration-500"></div>

                    <div class="flex flex-col lg:flex-row gap-8 relative z-10">
                        
                        <!-- Left: Date & Avatar -->
                        <div class="flex-shrink-0 flex flex-row lg:flex-col items-center gap-6 lg:w-40 border-b lg:border-b-0 lg:border-r border-slate-50 pb-6 lg:pb-0 lg:pr-8">
                             <!-- Date Ticket -->
                            <div class="flex flex-col items-center justify-center w-24 h-28 bg-purple-50 rounded-2xl p-2 border border-purple-100 group-hover:bg-purple-100/50 transition-colors shadow-sm">
                                <span class="text-xs text-purple-600 font-bold uppercase tracking-wider mb-1">{{ \Carbon\Carbon::parse($req->duty_date)->locale('th')->isoFormat('MMM') }}</span>
                                <span class="text-4xl font-black text-slate-800 leading-none">{{ \Carbon\Carbon::parse($req->duty_date)->day }}</span>
                                <span class="text-[10px] text-slate-400 font-bold mt-1">{{ \Carbon\Carbon::parse($req->duty_date)->year + 543 }}</span>
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
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold shadow-sm bg-purple-50 text-purple-600 ring-1 ring-purple-100">
                                    <i data-lucide="shield" class="w-4 h-4 mr-2"></i>
                                    {{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}
                                </span>
                                
                                <span class="flex items-center px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                                    <i data-lucide="check" class="w-4 h-4 mr-1.5"></i>
                                    ผู้รับมอบหมายยินยอมแล้ว
                                </span>
                            </div>

                            <div class="mb-4 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ผู้รับมอบหมาย (ยินยอมแล้ว)</p>
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $req->replacementUser->rank }} {{ $req->replacementUser->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $req->replacementUser->position ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($req->remarks)
                            <div class="mb-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 relative">
                                <i data-lucide="quote" class="w-6 h-6 absolute top-3 left-3 text-slate-200 text-xl"></i>
                                <p class="text-slate-700 text-sm leading-relaxed break-words font-medium italic relative z-10 pl-4">
                                    "{{ $req->remarks }}"
                                </p>
                            </div>
                            @endif
                        </div>

                        <!-- Right: Actions -->
                        <div class="flex-shrink-0 w-full lg:w-48 flex flex-col justify-center items-center gap-3 pt-6 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-50 lg:pl-8">
                            <button @click="openApprove = true" class="w-full group flex items-center justify-center gap-3 px-4 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-2xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-1 font-bold">
                                <i data-lucide="stamp" class="w-4 h-4 text-lg group-hover:scale-110 transition-transform"></i>
                                อนุมัติ
                            </button>
                        </div>
                    </div>

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

                            <div class="bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all relative"
                                 style="width: 90vw; max-width: 600px; max-height: 90vh; display: flex; flex-direction: column;"
                                 x-data="signaturePad({{ $req->id }})">
                                
                                <form action="{{ route('guard-change.director-approve', $req->id) }}" method="POST" style="height: 100%; display: flex; flex-direction: column; overflow-y: auto;">
                                    @csrf
                                    <input type="hidden" name="signature" id="signature-input-{{ $req->id }}">
                                    
                                    <div class="bg-white p-8">
                                        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 text-white mb-6 shadow-lg shadow-emerald-500/30 ring-4 ring-emerald-50">
                                            <i data-lucide="stamp" class="w-4 h-4 text-5xl transform -rotate-12"></i>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">อนุมัติคำขอเปลี่ยนยาม</h3>
                                            <div class="mt-2 text-base text-slate-500">
                                                อนุมัติคำขอของ <span class="font-bold text-slate-800">{{ $req->user->rank }} {{ $req->user->name }}</span>
                                            </div>
                                            
                                            <!-- Signature Option -->
                                            <div class="mt-6">
                                                @if(Auth::user()->signature)
                                                <div class="flex items-center justify-center gap-4 mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                                    <label class="inline-flex items-center cursor-pointer gap-2">
                                                        <input type="radio" value="0" name="use_saved_radios_{{ $req->id }}" @click="useSaved = false; $nextTick(() => { resizeCanvas(); })" :checked="!useSaved" class="form-radio text-purple-500 focus:ring-purple-500 border-slate-300">
                                                        <span class="text-sm font-bold text-slate-700">วาดลายเซ็น/Stamp</span>
                                                    </label>
                                                    <div class="w-px h-6 bg-slate-300"></div>
                                                    <label class="inline-flex items-center cursor-pointer gap-2">
                                                        <input type="radio" value="1" name="use_saved_radios_{{ $req->id }}" @click="useSaved = true" :checked="useSaved" class="form-radio text-purple-500 focus:ring-purple-500 border-slate-300">
                                                        <span class="text-sm font-bold text-slate-700">ใช้ลายเซ็นที่บันทึกไว้</span>
                                                    </label>
                                                </div>
                                                <input type="hidden" name="use_saved_signature" :value="useSaved ? '1' : '0'">
                                                @endif

                                                <!-- Draw Signature -->
                                                <div x-show="!useSaved" class="text-left">
                                                    <div class="flex justify-between items-end mb-2">
                                                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">ลงลายมือชื่อ / Stamp</label>
                                                        <button type="button" @click="clearSignature()" class="text-xs font-bold text-brand-600 hover:text-red-500 transition-colors">
                                                            <i data-lucide="eraser" class="w-4 h-4 mr-1"></i> ล้าง
                                                        </button>
                                                    </div>
                                                    <div class="border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 relative overflow-hidden h-48 hover:border-purple-300 hover:bg-purple-50/20 transition-all cursor-crosshair">
                                                        <canvas id="signature-canvas-{{ $req->id }}" class="w-full h-full"></canvas>
                                                    </div>
                                                    <p class="text-[10px] text-slate-400 mt-2 font-medium text-center">กรุณาเซ็นชื่อหรือประทับ Stamp ในกรอบด้านบน</p>
                                                </div>

                                                <!-- Saved Signature Preview -->
                                                @if(Auth::user()->signature)
                                                <div x-show="useSaved" style="display: none;" class="text-left">
                                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">ลายเซ็นที่บันทึกไว้</label>
                                                    <div class="border-2 border-solid border-purple-100 rounded-2xl bg-purple-50/30 p-4 h-48 flex items-center justify-center relative overflow-hidden">
                                                        <img src="{{ asset('storage/' . Auth::user()->signature) }}" class="max-h-full max-w-full object-contain">
                                                        <div class="absolute top-2 right-2 bg-purple-100 text-purple-600 px-2 py-0.5 rounded text-[10px] font-bold">
                                                            <i data-lucide="check-circle" class="w-5 h-5 mr-1"></i> ใช้ลายเซ็นเดิม
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>

                                            <div class="mt-6 text-left">
                                                <label for="comment" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">ความคิดเห็น (ถ้ามี)</label>
                                                <textarea name="comment" rows="2" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-medium" placeholder="ระบุความคิดเห็นเพิ่มเติม..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 px-8 py-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 border-t border-slate-100">
                                        <button type="button" @click="openApprove = false" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl border border-slate-200 bg-white text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-800 focus:outline-none transition-all">
                                            ยกเลิก
                                        </button>
                                        <button type="button" @click="submitForm($event)" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 rounded-xl border border-transparent shadow-lg shadow-purple-500/30 bg-purple-500 text-white font-bold hover:bg-purple-600 focus:outline-none transform hover:-translate-y-0.5 transition-all">
                                            <i data-lucide="stamp" class="w-4 h-4 mr-2"></i> ยืนยันอนุมัติ
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

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', (id) => ({
            signaturePad: null,
            useSaved: false,
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
                        penColor: 'rgb(0, 51, 153)'
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
                    this.signaturePad.clear();
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
</x-app-layout>

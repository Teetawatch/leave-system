<x-app-layout>
    @section('title', 'อนุมัติคำขอเปลี่ยนยาม (ผอ.)')

    <div class="min-h-screen bg-[#f8fafc]">
        <!-- Cinematic Command Header -->
        <div class="relative bg-[#0f172a] pt-16 pb-28 overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0">
                <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-rose-500/10 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/10 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
            </div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div>
                        <nav class="flex items-center gap-2 text-rose-300/60 transition-all mb-4 text-sm font-black tracking-widest uppercase">
                            <i data-lucide="crown" class="w-4 h-4"></i>
                            <span>การตัดสินใจ</span>
                            <span class="w-1 h-1 rounded-full bg-rose-500/40"></span>
                            <span class="text-rose-400">ผอ. อนุมัติขั้นสุดท้าย</span>
                        </nav>
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4">
                            คำขอเปลี่ยนยามรอ ผอ. อนุมัติ
                        </h1>
                        <p class="text-indigo-100/60 max-w-2xl text-lg font-medium leading-relaxed">
                            ขั้นตอนสุดท้ายในการพิจารณาคำขอเปลี่ยนเวรยาม ท่านกำลังดำเนินการตรวจสอบความถูกต้อง 
                            หลังจากผ่านการเห็นชอบจากระดับฝ่ายการเจ้าหน้าที่และรองผู้อำนวยการเรียบร้อยแล้ว
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="glass-card bg-white/5 border-white/10 rounded-3xl px-8 py-6 backdrop-blur-md shadow-2xl">
                            <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.3em] mb-1">รอดำเนินการ</p>
                            <div class="flex items-baseline gap-3">
                                <span class="text-4xl font-black text-white">{{ $requests->count() }}</span>
                                <span class="text-sm font-bold text-indigo-300/40 uppercase">รายการ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20">
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition.out.opacity.duration.500ms class="mb-8 rounded-[2.5rem] bg-emerald-500 p-1 pr-1 shadow-2xl shadow-emerald-500/20">
                    <div class="bg-white rounded-[2.3rem] p-5 flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner">
                            <i data-lucide="shield-check" class="w-7 h-7"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-slate-900 text-base">อนุมัติขั้นสุดท้ายสำเร็จ</h4>
                            <p class="text-sm font-bold text-slate-500">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="w-12 h-12 rounded-2xl hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if($requests->isEmpty())
                <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-32 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
                    <div class="relative z-10">
                        <div class="w-40 h-40 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 border-8 border-white shadow-inner group">
                            <i data-lucide="crown" class="w-20 h-20 text-slate-200 group-hover:scale-110 group-hover:text-rose-400 transition-all duration-700"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">ไม่มีคำขอค้างพิจารณา</h3>
                        <p class="text-slate-500 max-w-sm mx-auto text-xl font-medium">ทุกรายการได้รับการตัดสินใจขั้นสุดท้ายโดยท่านเรียบร้อยแล้ว</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-10">
                    @foreach($requests as $req)
                        @php
                            $dutyPositions = [
                                'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                'duty_officer' => 'นายทหารเวร',
                                'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                            ];
                        @endphp
                        <div class="group bg-white rounded-[3rem] shadow-2xl shadow-slate-200/40 border border-slate-100 p-10 md:p-12 hover:shadow-rose-500/10 transition-all duration-700 relative overflow-hidden"
                             x-data="{ openApprove: false }">
                            
                            <!-- Background Decor -->
                            <div class="absolute top-0 right-0 w-80 h-80 bg-rose-50 rounded-bl-full -mr-20 -mt-20 opacity-30 group-hover:scale-125 transition-transform duration-1000 pointer-events-none"></div>

                            <div class="flex flex-col xl:flex-row gap-12 relative z-10">
                                <!-- Requester Profile -->
                                <div class="flex-shrink-0 flex flex-row xl:flex-col items-center xl:items-start gap-8 xl:w-56 border-b xl:border-b-0 xl:border-r border-slate-100 pb-10 xl:pb-0 xl:pr-12">
                                    <div class="relative">
                                        <div class="w-28 h-28 rounded-[2.5rem] bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 flex items-center justify-center text-4xl font-black shadow-xl overflow-hidden ring-8 ring-white">
                                            @if($req->user->avatar)
                                                <img src="{{ asset('storage/' . $req->user->avatar) }}" alt="{{ $req->user->name }}" class="w-full h-full object-cover transform scale-110 group-hover:scale-125 transition-transform duration-700">
                                            @else
                                                {{ substr($req->user->name, 0, 1) }}
                                            @endif
                                        </div>
                                        <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-2xl border-4 border-white transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                                            <i data-lucide="star" class="w-6 h-6 fill-current"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 xl:flex-none">
                                        <h4 class="text-2xl font-black text-slate-900 leading-tight tracking-tight">{{ $req->user->rank }}{{ $req->user->name }}</h4>
                                        <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] mt-2 bg-rose-50 px-3 py-1 rounded-full inline-block">ผู้ขอเปลี่ยนเวร</p>
                                        <p class="text-sm font-bold text-slate-400 mt-2 flex items-center gap-2">
                                            <i data-lucide="building-2" class="w-4 h-4"></i>
                                            {{ $req->user->department }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Body Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-4 mb-8">
                                        <span class="inline-flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black tracking-[0.1em] shadow-xl shadow-slate-200 uppercase">
                                            <i data-lucide="shield" class="w-4 h-4 text-rose-400"></i>
                                            {{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}
                                        </span>
                                        <span class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-slate-900 rounded-2xl text-xs font-black shadow-sm uppercase">
                                            <i data-lucide="calendar" class="w-4 h-4 text-indigo-500"></i>
                                            {{ $req->duty_date->locale('th')->translatedFormat('d F Y') }}
                                        </span>
                                    </div>

                                    <!-- Approval Chain Track -->
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                                        <div class="bg-emerald-50/50 rounded-[2.5rem] p-6 border-2 border-emerald-100 flex items-center gap-5">
                                            <div class="h-14 w-14 rounded-2xl bg-white shadow-md text-emerald-500 flex items-center justify-center">
                                                <i data-lucide="user-check" class="w-7 h-7"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[10px] font-black text-emerald-600/60 uppercase tracking-widest mb-1">ผู้รับเปลี่ยนเวร</p>
                                                <p class="text-base font-black text-slate-800">{{ $req->replacementUser->rank }}{{ $req->replacementUser->name }}</p>
                                            </div>
                                        </div>

                                        <div class="bg-purple-50/50 rounded-[2.5rem] p-6 border-2 border-purple-100 flex items-center gap-5">
                                            <div class="h-14 w-14 rounded-2xl bg-white shadow-md text-purple-500 flex items-center justify-center">
                                                <i data-lucide="signature" class="w-7 h-7"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[10px] font-black text-purple-600/60 uppercase tracking-widest mb-1">รอง ผอ. เห็นชอบ</p>
                                                <p class="text-base font-black text-slate-800">
                                                    @if($req->directorApprover)
                                                        {{ $req->directorApprover->rank }}{{ $req->directorApprover->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($req->remarks)
                                        <div class="bg-slate-50/80 rounded-[2rem] p-8 border border-slate-100 relative group/quote">
                                            <i data-lucide="quote" class="absolute top-4 right-6 w-12 h-12 text-slate-200/50 group-hover/quote:text-rose-200/50 transition-colors"></i>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">เหตุผลการเปลี่ยนเวร</p>
                                            <p class="text-lg text-slate-600 font-medium italic relative z-10">"{{ $req->remarks }}"</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Executive Decisions -->
                                <div class="flex-shrink-0 xl:w-64 flex flex-col justify-center items-center gap-6 xl:pl-12 xl:border-l-4 xl:border-slate-50 border-double">
                                    <button @click="openApprove = true" class="w-full group/btn relative px-8 py-6 bg-rose-600 text-white rounded-[2rem] shadow-2xl shadow-rose-600/20 hover:shadow-rose-600/40 transition-all hover:-translate-y-2 overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r from-rose-500 to-rose-700 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                        <div class="relative flex items-center justify-center gap-4">
                                            <i data-lucide="crown" class="w-6 h-6 group-hover/btn:scale-125 transition-transform"></i>
                                            <span class="text-sm font-black uppercase tracking-[0.2em]">อนุมัติ</span>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Final Approval Modal -->
                            <template x-teleport="body">
                                <div x-show="openApprove" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;"
                                    x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0">
                                    
                                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                                        <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openApprove = false">
                                            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-xl"></div>
                                        </div>

                                        <div class="bg-white rounded-[4rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-2xl border-t-8 border-rose-600"
                                             x-data="signaturePad({{ $req->id }})">
                                            
                                            <form action="{{ route('guard-change.final-approve', $req->id) }}" method="POST" id="form-final-{{ $req->id }}">
                                                @csrf
                                                <input type="hidden" name="signature" id="signature-input-{{ $req->id }}">
                                                
                                                <div class="bg-white p-10 md:p-14">
                                                    <div class="flex items-center justify-between mb-12">
                                                        <div class="flex items-center gap-5">
                                                            <div class="w-20 h-20 rounded-[2rem] bg-rose-50 text-rose-600 flex items-center justify-center shadow-inner">
                                                                <i data-lucide="crown" class="w-10 h-10"></i>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-4xl font-black text-slate-900 tracking-tighter">อนุมัติขั้นสุดท้าย</h3>
                                                                <p class="text-rose-500 font-black text-xs uppercase tracking-widest">กรุณาลงลายมือชื่อ</p>
                                                            </div>
                                                        </div>
                                                        <button type="button" @click="openApprove = false" class="w-14 h-14 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                                            <i data-lucide="x" class="w-8 h-8"></i>
                                                        </button>
                                                    </div>

                                                    <div class="mb-10 p-6 bg-slate-50/80 rounded-[2.5rem] border border-slate-100">
                                                        <p class="text-sm font-bold text-slate-500">ยืนยันการอนุมัติขั้นสุดท้ายสำหรับ:</p>
                                                        <p class="text-2xl font-black text-slate-900 mt-1">{{ $req->user->rank }}{{ $req->user->name }}</p>
                                                        <div class="mt-4 flex items-center gap-3">
                                                             <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm border border-slate-100 uppercase">{{ $dutyPositions[$req->duty_position] ?? $req->duty_position }}</span>
                                                             <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm border border-slate-100 uppercase">{{ $req->duty_date->locale('th')->translatedFormat('d M Y') }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-8">
                                                        @if(Auth::user()->signature)
                                                            <div class="flex p-2 bg-slate-100 rounded-[2rem] border-2 border-slate-200">
                                                                <button type="button" @click="useSaved = false; $nextTick(() => { resizeCanvas(); })" 
                                                                        :class="!useSaved ? 'bg-white shadow-xl text-rose-600 scale-[1.02]' : 'text-slate-400 hover:text-slate-600'"
                                                                        class="flex-1 py-4 rounded-[1.8rem] text-xs font-black uppercase tracking-widest transition-all duration-300">
                                                                    ลงนามใหม่
                                                                </button>
                                                                <button type="button" @click="useSaved = true" 
                                                                        :class="useSaved ? 'bg-white shadow-xl text-rose-600 scale-[1.02]' : 'text-slate-400 hover:text-slate-600'"
                                                                        class="flex-1 py-4 rounded-[1.8rem] text-xs font-black uppercase tracking-widest transition-all duration-300">
                                                                    ใช้ลายเซ็นประจำตัว
                                                                </button>
                                                            </div>
                                                            <input type="hidden" name="use_saved_signature" :value="useSaved ? '1' : '0'">
                                                        @endif

                                                        <!-- Draw Area -->
                                                        <div x-show="!useSaved" class="space-y-4">
                                                            <div class="flex justify-between items-center px-4">
                                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">ลงลายมือชื่อ</label>
                                                                <button type="button" @click="clearSignature()" class="text-xs font-black text-rose-500 hover:text-rose-600 flex items-center gap-2 transition-colors">
                                                                    <i data-lucide="eraser" class="w-4 h-4"></i>
                                                                    ล้าง
                                                                </button>
                                                            </div>
                                                            <div class="bg-slate-50 border-4 border-dashed border-slate-200 rounded-[3rem] h-64 relative cursor-crosshair group/pad hover:border-rose-400 hover:bg-rose-50/5 transition-all duration-500">
                                                                <canvas id="signature-canvas-{{ $req->id }}" class="w-full h-full"></canvas>
                                                                <div x-show="isCanvasEmpty" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none transition-opacity duration-300">
                                                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                                                        <i data-lucide="edit-3" class="w-8 h-8 text-slate-300"></i>
                                                                    </div>
                                                                    <p class="text-xs font-black text-slate-300 uppercase tracking-widest">พื้นที่ลงนาม</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Registered Signature -->
                                                        @if(Auth::user()->signature)
                                                            <div x-show="useSaved" class="space-y-4">
                                                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-4">ลายเซ็นประจำตัว</label>
                                                                <div class="bg-rose-50/30 border-4 border-solid border-rose-100 rounded-[3rem] h-64 flex items-center justify-center p-12 relative overflow-hidden group/saved">
                                                                    <img src="{{ asset('storage/' . Auth::user()->signature) }}" class="max-h-full max-w-full object-contain relative z-10 filter drop-shadow-2xl">
                                                                    <div class="absolute inset-0 bg-gradient-to-br from-rose-500/5 to-transparent opacity-0 group-hover/saved:opacity-100 transition-opacity"></div>
                                                                    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-white/80 backdrop-blur-md border border-rose-100 px-6 py-2 rounded-full text-[10px] font-black text-rose-600 shadow-lg uppercase tracking-widest">
                                                                        ลายเซ็นที่บันทึกไว้
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div class="space-y-4">
                                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-4">ความเห็น / ข้อสังเกต</label>
                                                            <textarea name="comment" rows="3" class="block w-full rounded-[2rem] border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-8 focus:ring-rose-500/5 transition-all p-6 text-base font-bold text-slate-700 placeholder:text-slate-300 resize-none shadow-inner" placeholder="ระบุข้อความคำสั่งเพิ่มเติม (ถ้ามี)..."></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="bg-slate-50 px-10 py-10 md:px-14 md:py-12 flex flex-col sm:flex-row-reverse gap-6 border-t border-slate-100">
                                                    <button type="button" @click="submitForm($event)" class="relative flex-[2] inline-flex justify-center items-center px-10 py-6 bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] shadow-2xl shadow-slate-900/40 hover:shadow-rose-600/40 hover:bg-rose-600 transition-all hover:-translate-y-2 group/submit">
                                                        <i data-lucide="check" class="w-6 h-6 mr-3 group-hover/submit:scale-125 transition-transform"></i>
                                                        บันทึกการตัดสินใจ
                                                    </button>
                                                    <button type="button" @click="openApprove = false" class="flex-1 inline-flex justify-center items-center px-10 py-6 bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] hover:bg-slate-100 hover:text-slate-600 transition-all shadow-sm">
                                                        ยกเลิก
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
                        penColor: 'rgb(15, 23, 42)', // Navy/Dark Slate for command feel
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
                    const ratio =  Math.max(window.devicePixelRatio || 1, 1);
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
                document.getElementById('form-final-' + id).submit();
            }
        }));
    });
</script>

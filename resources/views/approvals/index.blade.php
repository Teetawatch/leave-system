<x-app-layout>
    @section('title', 'รายการพิสูจน์ทราบและอนุมัติใบลา')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .decision-card {
                transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .decision-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 40px 80px -20px rgba(15, 23, 42, 0.1);
            }

            .reason-box {
                background: linear-gradient(135deg, rgba(248, 250, 252, 1) 0%, rgba(241, 245, 249, 0.8) 100%);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
        
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-amber-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <!-- Premium Executive Header -->
        <div class="relative z-10 pt-16 pb-24 animate-slide-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 text-amber-600 text-[11px] font-black uppercase tracking-[0.2em] mb-2 shadow-sm border border-amber-100">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Command & Authority Panel
                        </div>
                        <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-4">
                            รายการรอ <span class="text-amber-500 text-glow">การตัดสินใจ</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">
                            ระบบตรวจสอบความถูกต้องและพิจารณาอนุมัติคำขอลาปฏิบัติราชการ<br class="hidden md:block">
                            แบบรวมศูนย์ เพื่อการจัดการทรัพยากรบุคคลอย่างไม่มีสะดุด
                        </p>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="glass-panel px-8 py-6 rounded-[2.5rem] border-white/50 shadow-2xl flex flex-col items-center min-w-[160px] group transition-all hover:scale-105">
                            <span class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Queue Total</span>
                            <span class="text-5xl font-black text-amber-500 tracking-tighter">{{ $requests->count() }}</span>
                        </div>
                        <div class="glass-panel px-8 py-6 rounded-[2.5rem] bg-rose-50/50 border-rose-100 shadow-2xl flex flex-col items-center min-w-[160px] group transition-all hover:scale-105">
                            <span class="text-rose-400 text-[10px] font-black uppercase tracking-[0.3em] mb-2">Medical Alert</span>
                            <span class="text-5xl font-black text-rose-500 tracking-tighter">{{ $requests->where('leaveType.slug', 'sick')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" class="mb-12 animate-slide-up" style="animation-delay: 0.1s">
                    <div class="glass-panel border-emerald-100 bg-emerald-50/50 rounded-[3rem] p-6 flex items-center gap-6 shadow-xl relative overflow-hidden group">
                        <div class="w-16 h-16 rounded-[2rem] bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-lg rotate-3 group-hover:rotate-0 transition-transform">
                            <i data-lucide="shield-check" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-emerald-900 tracking-tight text-xl uppercase tracking-tighter">SUCCESSFULLY PROCESSED</h4>
                            <p class="text-base font-bold text-emerald-600/80">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="w-12 h-12 hover:bg-white rounded-2xl text-emerald-400 transition-all flex items-center justify-center">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if($requests->isEmpty())
                <div class="glass-panel rounded-[4rem] p-32 text-center relative overflow-hidden group animate-slide-up" style="animation-delay: 0.2s">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                    <div class="relative z-10">
                        <div class="w-56 h-56 bg-white rounded-[4rem] flex items-center justify-center mx-auto mb-12 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700 shadow-2xl shadow-slate-200/50">
                            <i data-lucide="inbox" class="w-28 h-28 text-slate-100"></i>
                        </div>
                        <h3 class="text-4xl font-black text-slate-900 mb-6 tracking-tight leading-tight uppercase">EVERYTHING CLEARED</h3>
                        <p class="text-slate-400 font-bold max-w-sm mx-auto text-lg leading-relaxed uppercase tracking-[0.2em]">ไม่มีรายการค้างอนุมัติในขณะนี้<br>ขอบคุณสำหรับการปฏิบัติหน้าที่อย่างรวดเร็ว</p>
                    </div>
                </div>
            @else
                <div class="space-y-12">
                    @foreach($requests as $index => $req)
                        <div class="glass-panel rounded-[4.5rem] p-10 md:p-14 decision-card relative overflow-hidden group animate-slide-up"
                             style="animation-delay: {{ 0.2 + ($index * 0.1) }}s"
                             x-data="{ openReject: false, openApprove: false }">
                            
                            <!-- Premium Background Detail -->
                            <div class="absolute top-0 right-0 w-80 h-80 bg-slate-50 rounded-bl-full -mr-24 -mt-24 opacity-30 group-hover:scale-125 transition-transform duration-1000"></div>

                            <div class="flex flex-col xl:flex-row gap-16 relative z-10">
                                
                                <!-- User Information Pillar -->
                                <div class="flex-shrink-0 flex flex-row xl:flex-col items-center gap-10 xl:w-64 xl:border-r xl:border-slate-100/50 xl:pr-14">
                                    <div class="relative">
                                        <div class="w-36 h-36 rounded-[3.5rem] bg-indigo-900 border-8 border-white shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] overflow-hidden group-hover:scale-105 transition-all duration-700">
                                            @if($req->user->avatar)
                                                <img src="{{ asset('storage/' . $req->user->avatar) }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-4xl font-black text-indigo-200 uppercase italic">
                                                    {{ substr($req->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="absolute -bottom-3 -right-3 w-14 h-14 rounded-3xl bg-amber-500 text-white flex items-center justify-center border-4 border-white shadow-2xl group-hover:rotate-12 transition-transform">
                                            <i data-lucide="award" class="w-7 h-7"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 xl:text-center text-left">
                                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] mb-2 leading-none">RANK & IDENTITY</p>
                                        <h4 class="text-3xl font-black text-slate-800 tracking-tighter leading-none mb-4">{{ $req->user->rank }}{{ $req->user->name }}</h4>
                                        <span class="inline-flex px-5 py-2 rounded-full bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.25em] shadow-lg">{{ $req->user->department }}</span>
                                    </div>
                                </div>

                                <!-- Request Core Content -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <div class="flex flex-wrap items-center gap-4 mb-10">
                                        @php
                                            $typeStyle = match($req->leaveType->slug) {
                                                'sick' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                'vacation' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                default => 'bg-amber-50 text-amber-600 border-amber-100'
                                            };
                                            $typeIcon = match($req->leaveType->slug) {
                                                'sick' => 'thermometer', 'vacation' => 'palmtree', default => 'briefcase'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-8 py-3 rounded-full text-[11px] font-black uppercase tracking-[0.2em] border shadow-sm {{ $typeStyle }}">
                                            <i data-lucide="{{ $typeIcon }}" class="w-4 h-4 mr-3"></i>
                                            {{ $req->leaveType->name }}
                                        </span>

                                        <span class="px-8 py-3 rounded-full bg-slate-100 text-slate-900 border border-slate-200 text-[11px] font-black uppercase tracking-[0.25em] shadow-sm">
                                            <i data-lucide="timer" class="w-4 h-4 mr-3 inline text-indigo-500"></i>{{ $req->total_days + 0 }} วัน
                                        </span>

                                        <span class="flex items-center gap-4 px-8 py-3 rounded-full bg-white border border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] shadow-inner">
                                            <i data-lucide="calendar" class="w-4 h-4 text-emerald-500"></i>
                                            @thaidate($req->start_date) - @thaidate($req->end_date)
                                        </span>
                                    </div>

                                    <div class="mb-10 relative group/reason">
                                        <label class="block text-[10px] font-black text-slate-300 mb-4 uppercase tracking-[0.4em] px-2 leading-none">REASON FOR REQUEST</label>
                                        <div class="p-10 rounded-[3.5rem] reason-box border border-slate-100 italic font-black text-slate-700 text-2xl leading-relaxed relative overflow-hidden group-hover/reason:bg-white transition-all shadow-inner hover:shadow-2xl">
                                            <i data-lucide="quote" class="absolute -top-6 -left-6 w-32 h-32 text-slate-200 opacity-20 group-hover:rotate-12 transition-transform"></i>
                                            <span class="relative z-10">{{ $req->reason ?: 'ไม่ได้ระบุเหตุผลในการลา' }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-8">
                                        @if($req->attachment_path)
                                            <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" 
                                               class="group/link flex items-center gap-6 text-[11px] font-black text-indigo-600 uppercase tracking-[0.3em] hover:text-indigo-800 transition-colors">
                                                <div class="w-16 h-16 rounded-[2rem] bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner group-hover/link:scale-110 group-hover/link:bg-indigo-600 group-hover/link:text-white transition-all border border-indigo-100">
                                                    <i data-lucide="file-text" class="w-8 h-8"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] text-slate-400 mb-1 leading-none uppercase">Verified Integrity</span>
                                                    <span>Open Official Document</span>
                                                </div>
                                            </a>
                                        @else
                                            <div class="flex items-center gap-4 text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] px-4">
                                                <i data-lucide="file-x" class="w-6 h-6 opacity-30"></i>
                                                No Attachment Provided
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Decision Pillars -->
                                <div class="flex-shrink-0 w-full xl:w-72 flex flex-col justify-center gap-6 xl:pl-16 xl:border-l xl:border-slate-100/50">
                                    @php
                                        $userApproval = $req->approvals->where('approver_id', Auth::id())->first();
                                        $hasAlreadyApproved = $userApproval && in_array($userApproval->action, ['approved', 'acknowledged']);
                                    @endphp

                                    @if($hasAlreadyApproved)
                                        <div class="w-full space-y-6">
                                            <label class="block text-center text-[10px] font-black text-slate-300 mb-2 uppercase tracking-[0.4em]">Current Status</label>
                                            <div class="w-full py-8 bg-emerald-500 text-white rounded-[2.5rem] font-black text-xl flex items-center justify-center gap-4 shadow-2xl shadow-emerald-500/30 border border-emerald-400">
                                                <i data-lucide="shield-check" class="w-8 h-8"></i>
                                                ดำเนินการแล้ว
                                            </div>
                                            <div class="p-6 bg-slate-900 border border-slate-800 rounded-[2rem] flex items-start gap-4 shadow-xl">
                                                <i data-lucide="info" class="w-5 h-5 text-indigo-400 mt-1"></i>
                                                <p class="text-left text-[11px] font-bold text-slate-300 leading-relaxed uppercase tracking-[0.1em]">สถานะปัจจุบัน: {{ str_contains($req->status, 'pending') ? 'รอการพิจารณาในลำดับถัดไป' : ($req->status == 'approved' ? 'อนุมัติเรียบร้อยแล้ว' : 'สิ้นสุดรายการ') }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-4">
                                            <label class="block text-center text-[10px] font-black text-slate-300 mb-2 uppercase tracking-[0.4em]">Required Action</label>
                                            <button @click="openApprove = true" 
                                                class="w-full group py-8 bg-slate-900 hover:bg-emerald-600 text-white rounded-[2.5rem] shadow-2xl hover:shadow-emerald-500/40 transition-all hover:-translate-y-2 active:scale-95 flex flex-col items-center gap-1 font-black">
                                                <span class="text-2xl uppercase tracking-[0.25em]">
                                                    @if($req->status == 'pending_supervisor') อนุญาต
                                                    @elseif($req->status == 'pending_manager') อนุมัติ
                                                    @elseif($req->status == 'pending_deputy_director') รับทราบ
                                                    @else อนุมัติ
                                                    @endif
                                                </span>
                                                <span class="text-[9px] text-white/40 font-black uppercase tracking-[0.3em] leading-none mt-1">CONFIRM DECISION</span>
                                            </button>

                                            <button @click="openReject = true" 
                                                class="w-full py-6 bg-white border border-rose-100 text-rose-500 hover:bg-rose-500 hover:text-white font-black text-sm uppercase tracking-[0.3em] rounded-[2rem] transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 shadow-sm hover:shadow-rose-500/20">
                                                <i data-lucide="ban" class="w-5 h-5"></i>
                                                ปฏิเสธคำขอ
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @include('approvals.partials.decision_modals')
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
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
                    submit(event) {
                        if (this.signaturePad && !this.signaturePad.isEmpty() && !this.useSaved) {
                            const input = document.getElementById('signature-input-' + id);
                            if (input) input.value = this.signaturePad.toDataURL();
                        }
                        event.target.closest('form').submit();
                    }
                }));
            });

            document.addEventListener('DOMContentLoaded', () => {
                if(window.lucide) window.lucide.createIcons();
            });
        </script>
    @endpush
</x-app-layout>
<x-app-layout>
    @section('title', 'รายการตรวจสอบและอนุมัติใบลา')

    @push('styles')
    <style>
        .premium-bg-light {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .decision-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .decision-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .reason-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .animate-slide-up {
            animation: slideUp 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @endpush

    <div class="premium-bg-light -m-4 md:-m-8 pb-32 relative overflow-hidden">
        <!-- Header -->
        <div class="relative z-10 pt-16 pb-12 animate-slide-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-bold uppercase tracking-wider border border-amber-100">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            ส่วนพิจารณาและอนุมัติ
                        </div>
                        <h1 class="text-3xl md:text-5xl font-extrabold text-slate-800 tracking-tight leading-tight">
                            รายการรอ <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-amber-600">การอนุมัติ</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl">
                            ตรวจสอบและพิจารณาคำขอลาอย่างเป็นระบบ เพื่อความถูกต้องและรวดเร็ว
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="bg-white px-6 py-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col items-center min-w-[140px]">
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wide mb-1">จำนวนรออนุมัติ</span>
                            <span class="text-3xl font-extrabold text-amber-500">{{ $requests->count() }}</span>
                        </div>
                        <div class="bg-rose-50 px-6 py-4 rounded-2xl border border-rose-100 shadow-sm flex flex-col items-center min-w-[140px]">
                            <span class="text-rose-400 text-xs font-bold uppercase tracking-wide mb-1">ลาป่วย</span>
                            <span class="text-3xl font-extrabold text-rose-500">{{ $requests->where('leaveType.slug', 'sick')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-8 animate-slide-up" style="animation-delay: 0.1s">
                <div class="glass-panel border-emerald-100 bg-emerald-50/50 rounded-2xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-emerald-800 text-sm">ดำเนินการสำเร็จ</h4>
                        <p class="text-sm text-emerald-600">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="p-2 hover:bg-white rounded-lg text-emerald-500 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            @endif

            @if($requests->isEmpty())
            <div class="glass-panel rounded-3xl p-24 text-center animate-slide-up" style="animation-delay: 0.2s">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                    <i data-lucide="inbox" class="w-12 h-12"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2">ไม่มีรายการรออนุมัติ</h3>
                <p class="text-slate-500">ขอบคุณสำหรับการดำเนินงานที่รวดเร็ว</p>
            </div>
            @else
            <div class="space-y-8">
                @foreach($requests as $index => $req)
                <div class="glass-panel rounded-3xl p-8 decision-card relative overflow-hidden group animate-slide-up"
                    style="animation-delay: {{ 0.2 + ($index * 0.1) }}s"
                    x-data="{ openReject: false, openApprove: false }">

                    <div class="flex flex-col xl:flex-row gap-8 relative z-10">
                        <!-- User Info -->
                        <div class="flex-shrink-0 flex flex-row xl:flex-col items-center gap-6 xl:w-56 xl:border-r xl:border-slate-100/50 xl:pr-8">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-2xl bg-slate-100 overflow-hidden shadow-sm border-2 border-white">
                                    @if($req->user->avatar)
                                    <img src="{{ asset('storage/' . $req->user->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl font-bold text-slate-400 bg-slate-50 uppercase">
                                        {{ substr($req->user->name, 0, 1) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1 xl:text-center text-left">
                                <h4 class="text-xl font-bold text-slate-800 leading-tight mb-2">{{ $req->user->rank }}{{ $req->user->name }}</h4>
                                <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wide">{{ $req->user->department }}</span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <div class="flex flex-wrap items-center gap-3 mb-6">
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
                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wide border shadow-sm {{ $typeStyle }}">
                                    <i data-lucide="{{ $typeIcon }}" class="w-4 h-4 mr-2"></i>
                                    {{ $req->leaveType->name }}
                                </span>

                                <span class="px-4 py-2 rounded-xl bg-white text-slate-700 border border-slate-200 text-xs font-bold uppercase tracking-wide shadow-sm flex items-center gap-2">
                                    <i data-lucide="timer" class="w-4 h-4 text-indigo-500"></i>{{ $req->total_days + 0 }} วัน
                                </span>

                                <span class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wide shadow-sm">
                                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-500"></i>
                                    @thaidate($req->start_date) - @thaidate($req->end_date)
                                </span>
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">เหตุผลการลา</label>
                                <div class="p-6 rounded-2xl reason-box relative">
                                    <i data-lucide="quote" class="absolute top-4 left-4 w-6 h-6 text-slate-200"></i>
                                    <span class="relative z-10 text-slate-700 font-medium italic pl-8 block text-lg">"{{ $req->reason ?: 'ไม่ได้ระบุเหตุผล' }}"</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                @if($req->attachment_path)
                                <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank"
                                    class="group flex items-center gap-3 px-4 py-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition-colors border border-indigo-100">
                                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                                    <span class="text-sm font-bold">เปิดเอกสารแนบ</span>
                                </a>
                                @else
                                <div class="flex items-center gap-2 text-xs font-medium text-slate-400 px-2">
                                    <i data-lucide="file-x" class="w-4 h-4"></i>
                                    ไม่มีเอกสารแนบ
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 w-full xl:w-64 flex flex-col justify-center gap-4 xl:pl-8 xl:border-l xl:border-slate-100/50">
                            @php
                            $userApproval = $req->approvals->where('approver_id', Auth::id())->first();
                            $hasAlreadyApproved = $userApproval && in_array($userApproval->action, ['approved', 'acknowledged']);
                            @endphp

                            @if($hasAlreadyApproved)
                            <div class="w-full text-center p-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3 text-emerald-600">
                                    <i data-lucide="check" class="w-6 h-6"></i>
                                </div>
                                <h5 class="font-bold text-emerald-800 text-sm mb-1">ดำเนินการแล้ว</h5>
                                <p class="text-xs text-emerald-600/80">
                                    สถานะ: {{ str_contains($req->status, 'pending') ? 'รอขั้นตอนถัดไป' : ($req->status == 'approved' ? 'อนุมัติแล้ว' : 'สิ้นสุด') }}
                                </p>
                            </div>
                            @else
                            <button @click="openApprove = true"
                                class="w-full py-4 bg-slate-800 hover:bg-emerald-600 text-white rounded-2xl shadow-lg transition-all hover:-translate-y-1 active:scale-95 flex flex-col items-center justify-center gap-1">
                                <span class="text-lg font-bold">
                                    @if($req->status == 'pending_supervisor') อนุญาต
                                    @elseif($req->status == 'pending_manager') อนุมัติ
                                    @elseif($req->status == 'pending_deputy_director') รับทราบ
                                    @else อนุมัติ
                                    @endif
                                </span>
                                <span class="text-[10px] opacity-70 font-medium">ยืนยันการตัดสินใจ</span>
                            </button>

                            <button @click="openReject = true"
                                class="w-full py-3 bg-white border-2 border-slate-100 text-slate-500 hover:border-rose-100 hover:text-rose-600 hover:bg-rose-50 rounded-2xl transition-all active:scale-95 flex items-center justify-center gap-2 font-bold text-sm">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                ปฏิเสธ
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Include Modals (Hidden by default) - Assuming partial exists -->
                <!-- We need to define modals inline or include them if the partial is compatible -->
                @include('approvals.partials.decision_modals')
                
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
             // Re-using the same signature pad logic as before, just ensuring it works with new structure
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
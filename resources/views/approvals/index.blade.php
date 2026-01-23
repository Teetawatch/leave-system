<x-app-layout>
    @section('title', 'รายการรออนุมัติสำหรับผู้บังคับบัญชา')

    <div class="min-h-screen bg-slate-50/50 pb-32">
        
        <!-- Premium Executive Header -->
        <div class="bg-slate-900 pt-16 pb-32 px-4 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 z-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500 rounded-full blur-[100px] -mr-20 -mt-20 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-brand-500 rounded-full blur-[100px] -ml-20 -mb-20"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 backdrop-blur-xl border border-white/10 text-xs font-black text-amber-400 uppercase tracking-widest animate-fade-in">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Approver Dashboard
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight">รายการรอการตัดสินใจ</h1>
                    <p class="text-slate-400 text-lg font-bold max-w-2xl leading-relaxed">มีคำขอรับการพิจารณาจำนวน <span class="text-white">{{ $requests->count() }}</span> รายการ รอให้ท่านดำเนินการตรวจสอบและอนุมัติในระบบ</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 flex flex-col items-center min-w-[120px]">
                        <span class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">ค้างอนุมัติ</span>
                        <span class="text-3xl font-black text-amber-400">{{ $requests->count() }}</span>
                    </div>
                    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-6 flex flex-col items-center min-w-[120px]">
                        <span class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-1">ด่วนที่สุด</span>
                        <span class="text-3xl font-black text-rose-500">{{ $requests->where('leaveType.slug', 'sick')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10">
            
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" class="mb-10">
                    <div class="bg-white/90 backdrop-blur-2xl border border-emerald-100 rounded-[2.5rem] p-5 flex items-center gap-6 shadow-2xl relative overflow-hidden group">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-lg rotate-3">
                            <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-emerald-900 tracking-tight text-lg">พิจารณาเรียบร้อยแล้ว</h4>
                            <p class="text-sm font-bold text-emerald-600/80 mt-0.5">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="p-4 hover:bg-emerald-50 rounded-2xl text-emerald-400 transition-all">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if($requests->isEmpty())
                <div class="bg-white rounded-[4rem] shadow-2xl shadow-slate-200/50 border border-slate-50 p-24 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-slate-50/50 to-transparent"></div>
                    <div class="relative z-10">
                        <div class="w-40 h-40 bg-slate-50 rounded-[3rem] flex items-center justify-center mx-auto mb-10 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700 shadow-inner">
                            <i data-lucide="clipboard-check" class="w-20 h-20 text-slate-200"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-800 mb-2 tracking-tight uppercase">อนุมัติครบถ้วน</h3>
                        <p class="text-slate-400 font-bold max-w-sm mx-auto text-lg leading-relaxed uppercase tracking-widest">ขณะนี้ยังไม่มีคำค้างในระฆังความรับผิดชอบของท่าน</p>
                    </div>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($requests as $req)
                        <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/40 border border-slate-50 p-8 md:p-12 hover:shadow-brand-500/10 transition-all duration-500 hover:-translate-y-2 relative overflow-hidden group"
                             x-data="{ openReject: false, openApprove: false }">
                            
                            <!-- Premium Background Detail -->
                            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-20 -mt-20 opacity-30 group-hover:scale-125 transition-transform duration-700"></div>

                            <div class="flex flex-col xl:flex-row gap-12 relative z-10">
                                
                                <!-- User Information Pillar -->
                                <div class="flex-shrink-0 flex flex-row xl:flex-col items-center gap-8 xl:w-56 xl:border-r xl:border-slate-50 xl:pr-12">
                                    <div class="relative">
                                        <div class="w-28 h-28 rounded-[2.5rem] bg-gradient-to-br from-slate-100 to-slate-200 border-4 border-white shadow-2xl overflow-hidden group-hover:scale-105 transition-all duration-500">
                                            @if($req->user->avatar)
                                                <img src="{{ asset('storage/' . $req->user->avatar) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-3xl font-black text-slate-400 uppercase italic">
                                                    {{ substr($req->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-2xl bg-brand-600 text-white flex items-center justify-center border-4 border-white shadow-xl">
                                            <i data-lucide="user-check" class="w-5 h-5"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 xl:text-center text-left">
                                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1">{{ $req->user->rank }}</p>
                                        <h4 class="text-2xl font-black text-slate-800 tracking-tight leading-none">{{ $req->user->name }}</h4>
                                        <p class="inline-block mt-3 px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-widest">{{ $req->user->department }}</p>
                                    </div>
                                </div>

                                <!-- Request Core Content -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <div class="flex flex-wrap items-center gap-4 mb-8">
                                        @php
                                            $typeStyle = match($req->leaveType->slug) {
                                                'sick' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                'vacation' => 'bg-brand-50 text-brand-600 border-brand-100',
                                                default => 'bg-amber-50 text-amber-600 border-amber-100'
                                            };
                                            $typeIcon = match($req->leaveType->slug) {
                                                'sick' => 'thermometer', 'vacation' => 'palmtree', default => 'briefcase'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-6 py-2.5 rounded-2xl text-[11px] font-black uppercase tracking-widest border shadow-sm {{ $typeStyle }}">
                                            <i data-lucide="{{ $typeIcon }}" class="w-4 h-4 mr-2.5"></i>
                                            {{ $req->leaveType->name }}
                                        </span>

                                        <span class="px-6 py-2.5 rounded-2xl bg-slate-900 text-white text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-900/10">
                                            <i data-lucide="timer" class="w-4 h-4 mr-2.5 inline text-brand-400"></i>{{ $req->total_days + 0 }} วัน
                                        </span>

                                        <span class="flex items-center gap-3 px-6 py-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-[11px] font-black text-slate-400 uppercase tracking-widest">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                            @thaidate($req->start_date) - @thaidate($req->end_date)
                                        </span>
                                    </div>

                                    <div class="mb-10 relative group/reason">
                                        <label class="block text-[10px] font-black text-slate-300 mb-3 uppercase tracking-widest">เหตุผลความจำเป็น</label>
                                        <div class="p-8 rounded-[2.5rem] bg-slate-50 border border-slate-100 italic font-bold text-slate-700 text-2xl leading-relaxed relative overflow-hidden group-hover/reason:bg-white transition-all shadow-inner hover:shadow-xl">
                                            <i data-lucide="quote" class="absolute -top-4 -left-4 w-24 h-24 text-slate-100 opacity-50"></i>
                                            <span class="relative z-10">"{{ $req->reason }}"</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6">
                                        @if($req->attachment_path)
                                            <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" 
                                               class="group/link flex items-center gap-4 text-xs font-black text-brand-600 uppercase tracking-[0.2em] hover:text-brand-700">
                                                <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center shadow-inner group-hover/link:scale-110 group-hover/link:bg-brand-600 group-hover/link:text-white transition-all">
                                                    <i data-lucide="file-check" class="w-7 h-7"></i>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] text-brand-400 mb-0.5">Verified Document</span>
                                                    <span>คลิกเพื่อตรวจสอบเอกสาร</span>
                                                </div>
                                            </a>
                                        @else
                                            <div class="flex items-center gap-3 text-xs font-black text-slate-300 uppercase tracking-widest">
                                                <i data-lucide="file-x" class="w-5 h-5 opacity-30"></i>
                                                ไม่มีเอกสารแนบประกอบ
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Decision Pillars -->
                                <div class="flex-shrink-0 w-full xl:w-64 flex flex-col justify-center gap-4 xl:pl-12 xl:border-l xl:border-slate-50">
                                    @php
                                        $userApproval = $req->approvals->where('approver_id', Auth::id())->first();
                                        $hasAlreadyApproved = $userApproval && in_array($userApproval->action, ['approved', 'acknowledged']);
                                    @endphp

                                    @if($hasAlreadyApproved)
                                        <div class="w-full text-center space-y-4 animate-fade-in">
                                            <div class="w-full py-6 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-[2rem] font-black text-lg flex items-center justify-center gap-3 shadow-inner">
                                                <i data-lucide="shield-check" class="w-6 h-6"></i>
                                                พิจารณาแล้ว
                                            </div>
                                            <div class="p-5 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-3">
                                                <i data-lucide="info" class="w-5 h-5 text-amber-500 mt-0.5"></i>
                                                <p class="text-left text-[11px] font-bold text-amber-700 leading-relaxed uppercase tracking-widest">ดำเนินการขั้นต่อไปโดยผู้บังคับบัญชาตามลำดับสายงาน</p>
                                            </div>
                                        </div>
                                    @else
                                        <button @click="openApprove = true" 
                                            class="w-full group py-6 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-[2rem] shadow-[0_20px_40px_-10px_rgba(16,185,129,0.3)] hover:shadow-[0_25px_50px_-12px_rgba(16,185,129,0.5)] transition-all hover:-translate-y-2 active:scale-95 flex flex-col items-center gap-1 font-black">
                                            <span class="text-xl uppercase tracking-widest">
                                                @if($req->status == 'pending_supervisor') อนุญาต
                                                @elseif($req->status == 'pending_manager') อนุมัติ
                                                @elseif($req->status == 'pending_deputy_director') รับทราบ
                                                @else อนุมัติ
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-white/50 font-bold uppercase tracking-[0.2em] leading-none mt-1">Confirm Approval</span>
                                        </button>

                                        <button @click="openReject = true" 
                                            class="w-full py-6 bg-white border border-rose-100 text-rose-500 hover:bg-rose-50 hover:text-rose-600 font-bold text-lg rounded-[2rem] transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                                            ปฏิเสธคำขอ
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Modal Logic -->
                            @include('approvals.partials.decision_modals')
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<!-- Scripts remain similar but with premium initialization -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('signaturePad', (id) => ({
            signaturePad: null,
            useSaved: true,
            init() {
                this.$watch('openApprove', (value) => {
                    if (value) {
                        this.$nextTick(() => {
                            const canvas = document.getElementById('signature-canvas-' + id);
                            if (canvas) {
                                this.signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgba(255, 255, 255, 0)' });
                                this.resize(canvas);
                            }
                        });
                    }
                });
            },
            resize(canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                this.signaturePad.clear();
            },
            clear() { this.signaturePad.clear(); },
            submit(e) {
                if (this.signaturePad && !this.signaturePad.isEmpty() && !this.useSaved) {
                    document.getElementById('signature-input-' + id).value = this.signaturePad.toDataURL();
                }
                e.target.closest('form').submit();
            }
        }));
    });
    
    // Auto-init Lucide Icons
    window.addEventListener('load', () => window.lucide.createIcons());
</script>
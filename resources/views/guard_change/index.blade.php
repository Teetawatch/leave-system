<x-app-layout>
    @section('title', 'รายการขอเปลี่ยนยาม')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.5s ease-out forwards;
            }

            .status-badge {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .status-badge:hover {
                transform: translateY(-2px);
                filter: brightness(1.1);
            }

            .ticket-shadow {
                box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.3);
            }

            .swap-connector {
                position: relative;
            }

            .swap-connector::after {
                content: '';
                position: absolute;
                left: 50%;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(to bottom, transparent, #e2e8f0, transparent);
                transform: translateX(-50%);
            }

            @media (prefers-reduced-motion: reduce) {
                .animate-slide-up {
                    animation: none;
                    opacity: 1;
                }
                .status-badge:hover {
                    transform: none;
                }
            }
        </style>
    @endpush

    <div x-data="{ showPdf: false, pdfUrl: null }" class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        {{-- Background Decorations --}}
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-violet-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">

            {{-- Premium Header Area --}}
            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8 animate-slide-up">
                <div class="relative">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        ระบบบริหารเวรยาม
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-4">
                        รายการเปลี่ยน<span class="text-indigo-600">เวรยาม</span>
                    </h1>
                    <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                        ระบบบันทึกและติดตามสถานะการขออนุมัติเปลี่ยนเวรยามแบบเรียลไทม์<br class="hidden md:block">
                        เพื่อความถูกต้องและโปร่งใสในการปฏิบัติหน้าที่ราชการ
                    </p>
                </div>

                <a href="{{ route('guard-change.create') }}"
                   class="group inline-flex items-center justify-center px-10 py-6 bg-slate-900 hover:bg-indigo-600 text-white font-black text-xl rounded-[3rem] shadow-2xl hover:shadow-indigo-500/40 transition-all duration-300 transform hover:-translate-y-2 active:scale-95 cursor-pointer">
                    <div class="mr-5 w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:rotate-12 transition-all">
                        <i data-lucide="repeat-2" class="w-7 h-7"></i>
                    </div>
                    ขอเปลี่ยนเวรยามใหม่
                </a>
            </div>

            {{-- Flash Notifications --}}
            @if (session('status'))
                <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" class="mb-12 animate-slide-up" style="animation-delay: 0.1s">
                    <div class="glass-panel border-emerald-100 bg-emerald-50/50 rounded-[3rem] p-6 flex items-center gap-6 shadow-xl relative overflow-hidden group">
                        <div class="w-16 h-16 rounded-[2rem] bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-lg rotate-3 group-hover:rotate-0 transition-transform">
                            <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-emerald-900 tracking-tight text-xl uppercase tracking-[0.05em]">ดำเนินการเรียบร้อย</h4>
                            <p class="text-base font-bold text-emerald-600/80">{{ session('status') }}</p>
                        </div>
                        <button @click="show = false" class="w-12 h-12 hover:bg-white rounded-2xl text-emerald-400 transition-all flex items-center justify-center cursor-pointer" aria-label="ปิดข้อความแจ้งเตือน">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Stats Overview Cards --}}
            @if(!$requests->isEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16 animate-slide-up" style="animation-delay: 0.15s">
                    {{-- Pending --}}
                    <div class="glass-panel rounded-[3rem] p-8 group hover:shadow-2xl hover:shadow-amber-500/5 transition-all duration-500 hover:-translate-y-1">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[2rem] bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 shadow-inner group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                                <i data-lucide="clock" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">รอดำเนินการ</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-black text-slate-900">{{ $requests->whereIn('status', ['pending', 'approved', 'director_approved'])->count() }}</span>
                                    <span class="text-sm font-bold text-slate-400">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approved --}}
                    <div class="glass-panel rounded-[3rem] p-8 group hover:shadow-2xl hover:shadow-emerald-500/5 transition-all duration-500 hover:-translate-y-1">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[2rem] bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 shadow-inner group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <i data-lucide="shield-check" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">อนุมัติเรียบร้อย</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-black text-slate-900">{{ $requests->where('status', 'fully_approved')->count() }}</span>
                                    <span class="text-sm font-bold text-slate-400">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Total --}}
                    <div class="glass-panel rounded-[3rem] p-8 group hover:shadow-2xl hover:shadow-indigo-500/5 transition-all duration-500 hover:-translate-y-1">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 rounded-[2rem] bg-indigo-50 text-indigo-500 flex items-center justify-center border border-indigo-100 shadow-inner group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">
                                <i data-lucide="layers" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">ทั้งหมด</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-black text-slate-900">{{ $requests->total() }}</span>
                                    <span class="text-sm font-bold text-slate-400">ครั้ง</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($requests->isEmpty())
                {{-- Ultra-Premium Empty State --}}
                <div class="glass-panel rounded-[4rem] p-24 text-center relative overflow-hidden group animate-slide-up" style="animation-delay: 0.2s">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>

                    <div class="relative z-10">
                        <div class="w-48 h-48 bg-slate-50 rounded-[3.5rem] flex items-center justify-center mx-auto mb-12 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700 shadow-inner">
                            <i data-lucide="shield-off" class="w-24 h-24 text-slate-200"></i>
                        </div>
                        <h3 class="text-4xl font-black text-slate-900 mb-6 tracking-tight leading-tight">ยังไม่มีรายการเปลี่ยนเวรยาม</h3>
                        <p class="text-slate-400 font-bold mb-12 max-w-sm mx-auto text-lg uppercase tracking-[0.2em] leading-relaxed">คุณยังไม่มีประวัติการขอเปลี่ยนเวรยามในขณะนี้</p>
                        <a href="{{ route('guard-change.create') }}" class="inline-flex items-center gap-4 px-12 py-6 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white font-black rounded-[2.5rem] transition-all shadow-sm text-xl cursor-pointer">
                            เริ่มทำรายการครั้งแรก <i data-lucide="arrow-right" class="w-6 h-6"></i>
                        </a>
                    </div>
                </div>
            @else
                {{-- Enhanced Timeline Stream --}}
                <div class="space-y-16 relative">
                    {{-- Premium Center Line --}}
                    <div class="absolute left-10 top-0 bottom-0 w-2 bg-slate-100 rounded-full hidden md:block"></div>

                    @foreach($requests as $index => $request)
                        @php
                            $dutyPositions = [
                                'senior_duty_officer' => 'นายทหารเวรอาวุโส',
                                'duty_officer' => 'นายทหารเวร',
                                'assistant_duty_officer' => 'ผู้ช่วยนายทหารเวร',
                            ];

                            $statusConfig = match($request->status) {
                                'approved' => [
                                    'bg' => 'bg-indigo-500',
                                    'text' => 'text-white',
                                    'icon' => 'clock',
                                    'label' => 'รอ รอง ผอ. อนุมัติ',
                                    'glow' => 'shadow-indigo-500/30',
                                    'pulse' => true,
                                ],
                                'director_approved' => [
                                    'bg' => 'bg-purple-500',
                                    'text' => 'text-white',
                                    'icon' => 'clock',
                                    'label' => 'รอ ผอ. อนุมัติ',
                                    'glow' => 'shadow-purple-500/30',
                                    'pulse' => true,
                                ],
                                'fully_approved' => [
                                    'bg' => 'bg-emerald-500',
                                    'text' => 'text-white',
                                    'icon' => 'shield-check',
                                    'label' => 'อนุมัติเรียบร้อย',
                                    'glow' => 'shadow-emerald-500/30',
                                    'pulse' => false,
                                ],
                                'rejected' => [
                                    'bg' => 'bg-rose-500',
                                    'text' => 'text-white',
                                    'icon' => 'alert-octagon',
                                    'label' => 'ถูกปฏิเสธ',
                                    'glow' => 'shadow-rose-500/30',
                                    'pulse' => false,
                                ],
                                'cancelled' => [
                                    'bg' => 'bg-slate-400',
                                    'text' => 'text-white',
                                    'icon' => 'ban',
                                    'label' => 'ยกเลิกรายการ',
                                    'glow' => 'shadow-slate-400/30',
                                    'pulse' => false,
                                ],
                                default => [
                                    'bg' => 'bg-amber-400',
                                    'text' => 'text-white',
                                    'icon' => 'user-check',
                                    'label' => 'รอผู้รับมอบหมายยินยอม',
                                    'glow' => 'shadow-amber-500/30',
                                    'pulse' => true,
                                ],
                            };
                        @endphp

                        <div class="relative pl-0 md:pl-28 group animate-slide-up" style="animation-delay: {{ 0.1 + ($index * 0.05) }}s">
                            {{-- Timeline Marker --}}
                            <div class="absolute left-[30px] top-12 w-7 h-7 bg-white border-[6px] border-slate-200 rounded-full z-10 hidden md:block group-hover:border-indigo-500 group-hover:scale-150 group-hover:shadow-[0_0_25px_rgba(79,70,229,0.4)] transition-all duration-500"></div>

                            <div class="glass-panel rounded-[3.5rem] p-10 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-700 hover:-translate-y-3 relative overflow-hidden">

                                {{-- Background Pattern Decor --}}
                                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-24 -mt-24 opacity-30 group-hover:scale-125 transition-transform duration-1000"></div>

                                <div class="flex flex-col xl:flex-row gap-12 relative z-10">

                                    {{-- Premium Date Ticket --}}
                                    <div class="flex-shrink-0 flex flex-row xl:flex-col items-center justify-center w-full xl:w-44 bg-slate-900 rounded-[3rem] p-8 ticket-shadow group-hover:bg-indigo-600 transition-colors duration-700 group-hover:scale-105">
                                        <span class="text-[10px] text-white/50 font-black uppercase tracking-[0.3em] mb-0 xl:mb-3 mr-6 xl:mr-0">{{ $request->duty_date->locale('th')->translatedFormat('M') }}</span>
                                        <span class="text-6xl xl:text-7xl font-black text-white tracking-tighter my-0 xl:my-2">{{ $request->duty_date->format('d') }}</span>
                                        <span class="text-xs text-white/40 font-black ml-6 xl:ml-0 uppercase tracking-[0.2em] border-t border-white/10 pt-3 mt-2">{{ $request->duty_date->year + 543 }}</span>
                                    </div>

                                    {{-- Content Body --}}
                                    <div class="flex-1 min-w-0 flex flex-col justify-center py-2">
                                        {{-- Position Badge & Status --}}
                                        <div class="flex flex-wrap items-center gap-4 mb-8">
                                            <span class="inline-flex items-center px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-[0.1em] border shadow-sm bg-slate-900 text-white border-slate-800">
                                                <i data-lucide="shield" class="w-4 h-4 mr-3 text-indigo-400"></i>
                                                {{ $dutyPositions[$request->duty_position] ?? $request->duty_position }}
                                            </span>

                                            <div class="status-badge flex items-center gap-2 px-5 py-2.5 rounded-full font-black text-xs uppercase tracking-[0.1em] shadow-lg {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['glow'] }}">
                                                @if($statusConfig['pulse'])
                                                    <span class="w-2 h-2 rounded-full bg-white/80 animate-pulse"></span>
                                                @else
                                                    <i data-lucide="{{ $statusConfig['icon'] }}" class="w-3.5 h-3.5"></i>
                                                @endif
                                                {{ $statusConfig['label'] }}
                                            </div>

                                            <span class="text-[10px] text-slate-300 font-black ml-auto uppercase tracking-[0.3em] hidden lg:block">REFERENCE: #{{ sprintf('%05d', $request->id) }}</span>
                                        </div>

                                        {{-- Swap Visualization --}}
                                        <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-6 items-center mb-8">
                                            {{-- Requester --}}
                                            <div class="flex items-center gap-5 bg-slate-50/50 px-6 py-5 rounded-[2rem] border border-slate-100/50 shadow-inner group-hover:border-indigo-100/50 transition-colors">
                                                <div class="w-14 h-14 rounded-[1.5rem] bg-white flex items-center justify-center text-slate-400 shadow-sm border border-slate-100 flex-shrink-0">
                                                    <i data-lucide="user-minus" class="w-7 h-7"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">ผู้ขอเปลี่ยน</p>
                                                    <p class="font-black text-slate-900 text-base truncate">{{ $request->user->rank }}{{ $request->user->name }}</p>
                                                </div>
                                            </div>

                                            {{-- Swap Arrow --}}
                                            <div class="hidden md:flex flex-col items-center justify-center gap-2">
                                                <div class="w-14 h-14 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center border border-indigo-100 shadow-sm group-hover:bg-indigo-500 group-hover:text-white group-hover:scale-110 group-hover:rotate-180 transition-all duration-500">
                                                    <i data-lucide="repeat-2" class="w-6 h-6"></i>
                                                </div>
                                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em]">สลับ</span>
                                            </div>

                                            {{-- Replacement --}}
                                            <div class="flex items-center gap-5 bg-emerald-50/30 px-6 py-5 rounded-[2rem] border border-emerald-100/50 shadow-inner group-hover:border-emerald-200/80 transition-colors">
                                                <div class="w-14 h-14 rounded-[1.5rem] bg-white flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-100 flex-shrink-0">
                                                    <i data-lucide="user-plus" class="w-7 h-7"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">ผู้รับเปลี่ยน</p>
                                                    <p class="font-black text-slate-900 text-base truncate">{{ $request->replacementUser->rank }}{{ $request->replacementUser->name }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Remarks --}}
                                        @if($request->remarks)
                                            <div class="group/reason bg-slate-50/50 px-6 py-5 rounded-[2rem] border border-dashed border-slate-200 group-hover:border-indigo-200 transition-colors">
                                                <label class="block text-[10px] font-black text-slate-300 mb-3 uppercase tracking-[0.3em] leading-none flex items-center gap-2">
                                                    <i data-lucide="message-square" class="w-3 h-3"></i>
                                                    หมายเหตุ / เหตุผล
                                                </label>
                                                <p class="text-slate-700 text-lg font-bold leading-relaxed group-hover/reason:text-indigo-600 transition-colors">
                                                    "{{ $request->remarks }}"
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Actions & Status Column --}}
                                    <div class="flex-shrink-0 w-full xl:w-56 flex flex-col justify-between items-end gap-6 pt-10 xl:pt-4 xl:pl-10 xl:border-l xl:border-slate-100">
                                        {{-- Duty Date Display --}}
                                        <div class="w-full">
                                            <label class="block text-right text-[10px] font-black text-slate-300 mb-3 uppercase tracking-[0.3em] leading-none">วันปฏิบัติเวร</label>
                                            <div class="flex items-center gap-3 justify-end bg-slate-50/50 px-5 py-3.5 rounded-[1.5rem] border border-slate-100/50 shadow-inner">
                                                <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-indigo-500 shadow-sm">
                                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                                </div>
                                                <span class="font-black text-slate-800 text-sm">{{ $request->duty_date->locale('th')->isoFormat('D MMM YYYY') }}</span>
                                            </div>
                                        </div>

                                        {{-- View Details Button --}}
                                        <div class="w-full flex flex-col gap-3">
                                            <a href="{{ route('guard-change.show', $request) }}"
                                               class="group/view w-full inline-flex items-center justify-center gap-3 px-6 py-5 rounded-[2rem] bg-white text-slate-700 font-black text-sm hover:bg-slate-900 hover:text-white transition-all active:scale-95 border border-slate-100 shadow-lg shadow-slate-200/50 cursor-pointer">
                                                <i data-lucide="eye" class="w-5 h-5 group-hover/view:scale-110 transition-transform text-indigo-500 group-hover/view:text-white"></i>
                                                ดูรายละเอียด
                                            </a>

                                            @if($request->status === 'fully_approved')
                                                <button type="button"
                                                        @click="showPdf = true; pdfUrl = '{{ route('guard-change.pdf', $request) }}'"
                                                        class="group/pdf w-full inline-flex items-center justify-center gap-3 px-6 py-5 rounded-[2rem] bg-indigo-50 text-indigo-600 font-black text-sm hover:bg-indigo-600 hover:text-white transition-all active:scale-95 border border-indigo-100 cursor-pointer">
                                                    <i data-lucide="file-text" class="w-5 h-5 group-hover/pdf:scale-110 transition-transform"></i>
                                                    ดาวน์โหลด PDF
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Enhanced Pagination --}}
                @if($requests->hasPages())
                    <div class="mt-24 flex justify-center animate-slide-up">
                        <div class="glass-panel p-4 rounded-[3rem] shadow-2xl border border-white/50">
                            {{ $requests->links() }}
                        </div>
                    </div>
                @endif
            @endif

            {{-- Premium PDF Viewer Modal --}}
            <template x-teleport="body">
                <div x-show="showPdf" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" style="display: none;" x-cloak>
                    {{-- Cinematic Backdrop --}}
                    <div x-show="showPdf"
                         x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-950/95 backdrop-blur-xl"
                         @click="showPdf = false"></div>

                    {{-- Floating Modal Panel --}}
                    <div x-show="showPdf"
                         x-transition:enter="transition ease-out duration-600" x-transition:enter-start="opacity-0 scale-90 translate-y-32" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-32"
                         class="relative bg-white rounded-[4rem] shadow-[0_40px_120px_-30px_rgba(0,0,0,0.8)] w-full max-w-7xl h-[94vh] flex flex-col overflow-hidden m-4 border border-white/20">

                        {{-- Header --}}
                        <div class="bg-white px-10 py-8 flex justify-between items-center border-b border-slate-50 relative z-20">
                            <div class="flex items-center gap-8">
                                <div class="w-16 h-16 rounded-[2rem] bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner">
                                    <i data-lucide="file-text" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-2">เอกสารขอเปลี่ยนเวรยาม</h3>
                                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">Guard Change Document Preview</p>
                                </div>
                            </div>
                            <button @click="showPdf = false" class="w-14 h-14 bg-slate-100 hover:bg-rose-50 hover:text-rose-500 rounded-[1.75rem] text-slate-500 transition-all hover:rotate-90 active:scale-90 flex items-center justify-center cursor-pointer" aria-label="ปิดหน้าต่าง PDF">
                                <i data-lucide="x" class="w-7 h-7"></i>
                            </button>
                        </div>

                        {{-- Content Area --}}
                        <div class="flex-1 bg-slate-100 relative group/viewer">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 space-y-6">
                                <i data-lucide="refresh-cw" class="w-12 h-12 animate-spin text-indigo-500"></i>
                                <p class="font-black text-sm uppercase tracking-[0.4em]">กำลังประมวลผลเอกสาร...</p>
                            </div>
                            <iframe :src="pdfUrl" class="relative z-10 w-full h-full border-0" allowfullscreen></iframe>
                        </div>

                        {{-- Footer --}}
                        <div class="bg-slate-50 px-12 py-8 flex flex-col sm:flex-row justify-between items-center gap-6 border-t border-slate-200/50">
                            <div class="flex items-center gap-4 text-sm font-bold text-slate-400">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <span>ท่านสามารถดาวน์โหลดหรือพิมพ์เอกสารได้โดยตรงผ่านแถบเครื่องมือด้านบน</span>
                            </div>
                            <div class="flex gap-6 w-full sm:w-auto">
                                <a :href="pdfUrl" target="_blank" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-4 px-10 py-5 bg-slate-900 text-white font-black rounded-2xl shadow-2xl hover:bg-black transition-all active:scale-95 text-lg uppercase tracking-widest cursor-pointer">
                                    <i data-lucide="external-link" class="w-6 h-6"></i>
                                    เปิดในแท็บใหม่
                                </a>
                                <button type="button" class="flex-1 sm:flex-initial px-10 py-5 bg-white text-slate-700 font-black rounded-2xl shadow-xl border border-slate-200 hover:bg-slate-100 transition-all active:scale-95 text-lg uppercase tracking-widest cursor-pointer" @click="showPdf = false">
                                    ย้อนกลับ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if(window.lucide) window.lucide.createIcons();
            });
        </script>
    @endpush
</x-app-layout>

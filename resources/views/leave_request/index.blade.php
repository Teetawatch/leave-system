<x-app-layout>
    @section('title', 'ประวัติการลาปฏิบัติราชการ')

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
        </style>
    @endpush

    <div x-data="{ showPdf: false, pdfUrl: null }" class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
        
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
            
            <!-- Premium Header Area -->
            <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-20 gap-8 animate-slide-up">
                <div class="relative">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        ระบบงานกำลังพลอิเล็กทรอนิกส์
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-4">
                        ประวัติการลา <span class="text-indigo-600">ทั้งหมด</span>
                    </h1>
                    <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                        ติดตามสถานะคำขอลาแบบเรียลไทม์ และเรียกดูเอกสารย้อนหลัง<br class="hidden md:block">
                        ผ่านระบบบันทึกปฎิบัติราชการที่มีความโปร่งใสและตรวจสอบได้
                    </p>
                </div>
                
                <a href="{{ route('leave-request.create') }}" 
                   class="group inline-flex items-center justify-center px-10 py-6 bg-slate-900 hover:bg-indigo-600 text-white font-black text-xl rounded-[3rem] shadow-2xl hover:shadow-indigo-500/40 transition-all duration-300 transform hover:-translate-y-2 active:scale-95">
                    <div class="mr-5 w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:rotate-12 transition-all">
                         <i data-lucide="plus" class="w-7 h-7"></i>
                    </div>
                    ส่งคำขอลาใหม่
                </a>
            </div>

            <!-- Flash Notifications -->
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
                        <button @click="show = false" class="w-12 h-12 hover:bg-white rounded-2xl text-emerald-400 transition-all flex items-center justify-center">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if($requests->isEmpty())
                 <!-- Ultra-Premium Empty State -->
                 <div class="glass-panel rounded-[4rem] p-24 text-center relative overflow-hidden group animate-slide-up" style="animation-delay: 0.2s">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                    
                    <div class="relative z-10">
                        <div class="w-48 h-48 bg-slate-50 rounded-[3.5rem] flex items-center justify-center mx-auto mb-12 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700 shadow-inner">
                            <i data-lucide="inbox" class="w-24 h-24 text-slate-200"></i>
                        </div>
                        <h3 class="text-4xl font-black text-slate-900 mb-6 tracking-tight leading-tight">ยังไม่มีประวัติการลาในฐานข้อมูล</h3>
                        <p class="text-slate-400 font-bold mb-12 max-w-sm mx-auto text-lg uppercase tracking-[0.2em] leading-relaxed">คงความเป็นมืออาชีพด้วยการปฏิบัติงานอย่างต่อเนื่อง</p>
                        <a href="{{ route('leave-request.create') }}" class="inline-flex items-center gap-4 px-12 py-6 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white font-black rounded-[2.5rem] transition-all shadow-sm text-xl">
                            เริ่มส่งใบลาครั้งแรก <i data-lucide="arrow-right" class="w-6 h-6"></i>
                        </a>
                    </div>
                </div>
            @else
                <!-- Enhanced Timeline Stream -->
                <div class="space-y-16 relative">
                    <!-- Premium Center Line -->
                    <div class="absolute left-10 top-0 bottom-0 w-2 bg-slate-100 rounded-full hidden md:block"></div>

                    @foreach($requests as $index => $req)
                    <div class="relative pl-0 md:pl-28 group animate-slide-up" style="animation-delay: {{ 0.1 + ($index * 0.05) }}s">
                        <!-- Timeline Marker -->
                        <div class="absolute left-[30px] top-12 w-7 h-7 bg-white border-6 border-slate-200 rounded-full z-10 hidden md:block group-hover:border-indigo-500 group-hover:scale-150 group-hover:shadow-[0_0_25px_rgba(79,70,229,0.4)] transition-all duration-500"></div>

                        <div class="glass-panel rounded-[3.5rem] p-10 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-700 hover:-translate-y-3 relative overflow-hidden">
                            
                            <!-- Background Pattern Decor -->
                            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-24 -mt-24 opacity-30 group-hover:scale-125 transition-transform duration-1000"></div>

                            <div class="flex flex-col xl:flex-row gap-12 relative z-10">
                                
                                <!-- Premium Designer Date Ticket -->
                                <div class="flex-shrink-0 flex flex-row xl:flex-col items-center justify-center w-full xl:w-44 bg-slate-900 rounded-[3rem] p-8 ticket-shadow group-hover:bg-indigo-600 transition-colors duration-700 group-hover:scale-105">
                                    <span class="text-[10px] text-white/50 font-black uppercase tracking-[0.3em] mb-0 xl:mb-3 mr-6 xl:mr-0">{{ \Carbon\Carbon::parse($req->start_date)->locale('th')->isoFormat('MMMM') }}</span>
                                    <span class="text-6xl xl:text-7xl font-black text-white tracking-tighter my-0 xl:my-2">{{ \Carbon\Carbon::parse($req->start_date)->day }}</span>
                                    <span class="text-xs text-white/40 font-black ml-6 xl:ml-0 uppercase tracking-[0.2em] border-t border-white/10 pt-3 mt-2">{{ \Carbon\Carbon::parse($req->start_date)->year + 543 }}</span>
                                </div>

                                <!-- Content Body -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center py-2">
                                    <div class="flex flex-wrap items-center gap-4 mb-8">
                                        @php
                                            $typeStyle = match ($req->leaveType->slug) {
                                                'sick' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                'vacation' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                'personal' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                default => 'bg-slate-50 text-slate-500 border-slate-100'
                                            };
                                            $typeIcon = match ($req->leaveType->slug) {
                                                'sick' => 'thermometer', 'vacation' => 'palmtree', 'personal' => 'briefcase', default => 'file-text'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-[0.1em] border shadow-sm {{ $typeStyle }}">
                                            <i data-lucide="{{ $typeIcon }}" class="w-4 h-4 mr-3"></i>
                                            {{ $req->leaveType->name }}
                                        </span>
                                        
                                        <span class="px-6 py-2.5 rounded-full bg-slate-900 text-white text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-900/10">
                                            <i data-lucide="timer" class="w-4 h-4 mr-3 inline text-indigo-400"></i>{{ $req->total_days + 0 }} วัน
                                        </span>
                                        
                                        <span class="text-[10px] text-slate-300 font-black ml-auto uppercase tracking-[0.3em] hidden lg:block">REFERENCE: #{{ sprintf('%06d', $req->id) }}</span>
                                    </div>

                                    <div class="mb-10 group/reason">
                                        <label class="block text-[10px] font-black text-slate-300 mb-4 uppercase tracking-[0.3em] leading-none">ระบุเหตุผลการปฏิบัติราชการ</label>
                                        <p class="text-slate-900 text-3xl font-black leading-tight tracking-tight group-hover/reason:text-indigo-600 transition-colors">
                                            {{ $req->reason }}
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-8">
                                        <div class="flex items-center gap-4 text-base font-bold text-slate-500 bg-slate-50/50 px-6 py-3.5 rounded-[2rem] border border-slate-100/50 shadow-inner">
                                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                                <i data-lucide="calendar" class="w-5 h-5"></i>
                                            </div>
                                            <span>จนถึงวันที่ <b class="text-slate-900 ml-2 text-xl font-black">{{ \Carbon\Carbon::parse($req->end_date)->locale('th')->isoFormat('D MMMM YYYY') }}</b></span>
                                        </div>

                                        @if($req->attachment_path)
                                            <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="group/link flex items-center gap-4 text-sm font-black text-indigo-600 uppercase tracking-[0.15em] hover:text-indigo-800 transition-all">
                                                <div class="w-12 h-12 rounded-[1.5rem] bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover/link:scale-110 group-hover/link:bg-indigo-600 group-hover/link:text-white transition-all shadow-sm">
                                                    <i data-lucide="paperclip" class="w-5 h-5"></i>
                                                </div>
                                                <span>ไฟล์เอกสาร</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions & Advanced Status Tracking -->
                                <div class="flex-shrink-0 w-full xl:w-72 flex flex-col justify-between items-end gap-8 pt-10 xl:pt-4 xl:pl-12 xl:border-l xl:border-slate-100">
                                     @php
                                        $statusConfig = match($req->status) {
                                            'approved' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'icon' => 'shield-check', 'label' => 'อนุมัติเรียบร้อย', 'glow' => 'shadow-emerald-500/30'],
                                            'rejected' => ['bg' => 'bg-rose-500', 'text' => 'text-white', 'icon' => 'alert-octagon', 'label' => 'ไม่ได้รับการอนุมัติ', 'glow' => 'shadow-rose-500/30'],
                                            'cancelled' => ['bg' => 'bg-slate-400', 'text' => 'text-white', 'icon' => 'ban', 'label' => 'ยกเลิกรายการแล้ว', 'glow' => 'shadow-slate-400/30'],
                                            default => ['bg' => 'bg-amber-400', 'text' => 'text-white', 'icon' => 'clock', 'label' => 'กำลังรอการอนุมัติ', 'glow' => 'shadow-amber-500/30'],
                                        };
                                        
                                        $stepLabels = [
                                            'pending_supervisor' => 'รอหน. แผนก',
                                            'pending_head' => 'รอหัวหน้าแผนก',
                                            'pending_manager' => 'รอผู้บริหารแผนก',
                                            'pending_deputy_director' => 'รอรองผู้อำนวยการ',
                                            'pending_director' => 'รอผู้อำนวยการ',
                                        ];
                                        if (isset($stepLabels[$req->status])) {
                                            $statusConfig['label'] = $stepLabels[$req->status];
                                        }
                                    @endphp

                                    <div class="w-full">
                                        <label class="block text-right text-[10px] font-black text-slate-300 mb-4 uppercase tracking-[0.3em] leading-none">สถานะปัจจุบัน</label>
                                        <div class="status-badge flex items-center gap-4 px-8 py-5 rounded-[2rem] font-black w-full justify-center xl:justify-end shadow-2xl {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['glow'] }}">
                                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-6 h-6"></i>
                                            <span class="text-lg tracking-tight leading-none">{{ $statusConfig['label'] }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="w-full flex flex-col gap-4">
                                        <button type="button" @click="showPdf = true; pdfUrl = '{{ route('leave-request.pdf', $req->id) }}'" 
                                                class="group/pdf w-full inline-flex items-center justify-center gap-4 px-8 py-5 rounded-[2rem] bg-white text-slate-700 font-black text-base hover:bg-slate-900 hover:text-white transition-all active:scale-95 border border-slate-100 shadow-lg shadow-slate-200/50">
                                            <i data-lucide="file-text" class="w-6 h-6 group-hover/pdf:scale-110 transition-transform text-indigo-500 group-hover:text-white"></i> 
                                            ดูเอกสาร PDF
                                        </button>

                                        @if(str_starts_with($req->status, 'pending_'))
                                            <form action="{{ route('leave-request.cancel', $req->id) }}" method="POST" onsubmit="return confirm('ท่านต้องการยกเลิกคำขอลาใช่หรือไม่?');" class="w-full">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="w-full inline-flex items-center justify-center gap-4 px-8 py-5 rounded-[2rem] bg-rose-50 border border-rose-100 text-rose-600 font-black text-base hover:bg-rose-600 hover:text-white transition-all active:scale-95 group/cancel shadow-inner">
                                                    <i data-lucide="trash-2" class="w-5 h-5 group-hover/cancel:rotate-12 transition-transform"></i> 
                                                    ยกเลิกคำขอลา
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Enhanced Pagination -->
                @if($requests->hasPages())
                    <div class="mt-24 flex justify-center animate-slide-up">
                        <div class="glass-panel p-4 rounded-[3rem] shadow-2xl border border-white/50">
                            {{ $requests->links() }}
                        </div>
                    </div>
                @endif
            @endif

            <!-- Premium PDF Viewer Modal -->
            <template x-teleport="body">
                <div x-show="showPdf" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" style="display: none;" x-cloak>
                    <!-- Cinematic Backdrop -->
                    <div x-show="showPdf" 
                         x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-950/95 backdrop-blur-xl" 
                         @click="showPdf = false"></div>

                    <!-- Floating Modal Panel -->
                    <div x-show="showPdf"
                         x-transition:enter="transition ease-out duration-600" x-transition:enter-start="opacity-0 scale-90 translate-y-32" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-400" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-32"
                         class="relative bg-white rounded-[4rem] shadow-[0_40px_120px_-30px_rgba(0,0,0,0.8)] w-full max-w-7xl h-[94vh] flex flex-col overflow-hidden m-4 border border-white/20">
                        
                        <!-- Header -->
                        <div class="bg-white px-10 py-8 flex justify-between items-center border-b border-slate-50 relative z-20">
                            <div class="flex items-center gap-8">
                                <div class="w-16 h-16 rounded-[2rem] bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner">
                                    <i data-lucide="file-text" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight leading-none mb-2">แสดงตัวอย่างเอกสารใบลา</h3>
                                    <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">Official Document Management System</p>
                                </div>
                            </div>
                            <button @click="showPdf = false" class="w-14 h-14 bg-slate-100 hover:bg-rose-50 hover:text-rose-500 rounded-[1.75rem] text-slate-500 transition-all hover:rotate-90 active:scale-90 flex items-center justify-center cursor-pointer">
                                <i data-lucide="x" class="w-7 h-7"></i>
                            </button>
                        </div>
                        
                        <!-- Content Area -->
                        <div class="flex-1 bg-slate-100 relative group/viewer">
                            <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 space-y-6">
                                <i data-lucide="refresh-cw" class="w-12 h-12 animate-spin text-indigo-500"></i>
                                <p class="font-black text-sm uppercase tracking-[0.4em]">กำลังประมวลผลเอกสารดิจิทัล...</p>
                            </div>
                            <iframe :src="pdfUrl" class="relative z-10 w-full h-full border-0" allowfullscreen></iframe>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-12 py-8 flex flex-col sm:flex-row justify-between items-center gap-6 border-t border-slate-200/50">
                            <div class="flex items-center gap-4 text-sm font-bold text-slate-400">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                                </div>
                                <span>ท่านสามารถดาวน์โหลดหรือพิมพ์เอกสารได้โดยตรงผ่านแถบเครื่องมือด้านบน</span>
                            </div>
                            <div class="flex gap-6 w-full sm:w-auto">
                                <a :href="pdfUrl" target="_blank" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-4 px-10 py-5 bg-slate-900 text-white font-black rounded-2xl shadow-2xl hover:bg-black transition-all active:scale-95 text-lg uppercase tracking-widest">
                                    <i data-lucide="external-link" class="w-6 h-6"></i> 
                                    เปิดในแท็บใหม่
                                </a>
                                <button type="button" class="flex-1 sm:flex-initial px-10 py-5 bg-white text-slate-700 font-black rounded-2xl shadow-xl border border-slate-200 hover:bg-slate-100 transition-all active:scale-95 text-lg uppercase tracking-widest" @click="showPdf = false">
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
            // Alpine.js is already handled globally in x-app-layout or similar, 
            // but we ensure Lucide icons are refreshed if something dynamic happens
            document.addEventListener('DOMContentLoaded', () => {
                if(window.lucide) window.lucide.createIcons();
            });
        </script>
    @endpush
</x-app-layout>

<x-app-layout>
    @section('title', 'ประวัติการลาปฏิบัติราชการ')

    <div x-data="{ showPdf: false, pdfUrl: null }" class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 pb-32">
        
        <!-- Premium Header Area -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-16 gap-8 relative">
            <div class="relative">
                <div class="absolute -left-12 -top-12 w-40 h-40 bg-brand-500 rounded-full blur-[100px] opacity-10"></div>
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-4 border border-slate-200 shadow-sm">
                    <i data-lucide="archive" class="w-3.5 h-3.5"></i>
                    Transaction History
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight relative z-10 flex flex-col md:flex-row md:items-center gap-4">
                    ประวัติคำขอลา
                    <span class="text-slate-300 hidden md:inline">/</span>
                    <span class="text-brand-600">ทั้งหมด</span>
                </h2>
                <p class="text-slate-400 mt-4 text-lg font-bold max-w-xl">ติดตามสถานะแบบ Real-time และเรียกดูเอกสารใบลาที่ได้รับอนุมัติแล้วของคุณได้ที่นี่</p>
            </div>
            
            <a href="{{ route('leave-request.create') }}" 
               class="group inline-flex items-center justify-center px-10 py-5 bg-slate-900 hover:bg-brand-600 text-white font-black text-lg rounded-[2.5rem] shadow-2xl hover:shadow-brand-500/40 transition-all duration-300 transform hover:-translate-y-2 active:scale-95">
                <div class="mr-4 w-10 h-10 bg-white/10 rounded-2xl flex items-center justify-center group-hover:bg-white/20 group-hover:rotate-12 transition-all">
                     <i data-lucide="plus" class="w-6 h-6"></i>
                </div>
                ส่งคำขอลาใหม่
            </a>
        </div>

        <!-- Flash Notifications -->
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" class="mb-10">
                <div class="bg-emerald-50 border border-emerald-100 rounded-[2.5rem] p-5 flex items-center gap-6 shadow-xl shadow-emerald-500/5 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-100 rounded-full blur-3xl opacity-50 -mr-16 -mt-16"></div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-lg rotate-3 group-hover:rotate-0 transition-transform">
                        <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-black text-emerald-900 tracking-tight">ดำเนินการเรียบร้อย</h4>
                        <p class="text-sm font-bold text-emerald-600/80">{{ session('status') }}</p>
                    </div>
                    <button @click="show = false" class="p-3 hover:bg-white rounded-2xl text-emerald-400 transition-all">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        @endif

        @if($requests->isEmpty())
             <!-- Ultra-Premium Empty State -->
             <div class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-50 p-20 text-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                <div class="absolute top-0 left-1/2 -ml-32 -mt-32 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="w-40 h-40 bg-slate-50 rounded-[3rem] flex items-center justify-center mx-auto mb-10 group-hover:scale-110 group-hover:rotate-6 transition-all duration-700 shadow-inner">
                        <i data-lucide="ghost" class="w-20 h-20 text-slate-200"></i>
                    </div>
                    <h3 class="text-3xl font-black text-slate-800 mb-4 tracking-tight">ไม่มีประวัติการลาในขณะนี้</h3>
                    <p class="text-slate-400 font-bold mb-12 max-w-sm mx-auto text-lg uppercase tracking-widest leading-relaxed">คงความเป็นมืออาชีพด้วยการปฏิบัติงานอย่างต่อเนื่อง</p>
                    <a href="{{ route('leave-request.create') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-brand-50 text-brand-600 hover:bg-brand-100 font-black rounded-[2rem] transition-all shadow-sm">
                        เริ่มส่งใบลาครั้งแรกของคุณ <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
        @else
            <!-- Enhanced Timeline Stream -->
            <div class="space-y-12 relative">
                <!-- Premium Center Line -->
                <div class="absolute left-10 top-0 bottom-0 w-1.5 bg-slate-100/50 rounded-full hidden md:block"></div>

                @foreach($requests as $req)
                <div class="relative pl-0 md:pl-28 group">
                    <!-- Timeline Marker -->
                    <div class="absolute left-[33px] top-10 w-5 h-5 bg-white border-4 border-slate-200 rounded-full z-10 hidden md:block group-hover:border-brand-500 group-hover:scale-150 group-hover:shadow-[0_0_15px_rgba(37,99,235,0.4)] transition-all duration-500"></div>

                    <div class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/30 border border-slate-50 p-8 hover:shadow-brand-500/10 transition-all duration-500 hover:-translate-y-2 relative overflow-hidden">
                        
                        <!-- Background Pattern Decor -->
                        <div class="absolute top-0 right-0 w-48 h-48 bg-slate-50 rounded-bl-full -mr-16 -mt-16 opacity-30 group-hover:scale-125 transition-transform duration-700"></div>

                        <div class="flex flex-col xl:flex-row gap-10 relative z-10">
                            
                            <!-- Premium Designer Date Ticket -->
                            <div class="flex-shrink-0 flex flex-row xl:flex-col items-center justify-center w-full xl:w-40 bg-slate-900 rounded-[2.5rem] p-6 shadow-xl shadow-slate-900/20 group-hover:bg-brand-600 transition-colors duration-500 group-hover:scale-105">
                                <span class="text-[10px] text-white/50 font-black uppercase tracking-[0.2em] mb-0 xl:mb-2 mr-4 xl:mr-0">{{ \Carbon\Carbon::parse($req->start_date)->locale('th')->isoFormat('MMMM') }}</span>
                                <span class="text-5xl xl:text-6xl font-black text-white tracking-tighter my-0 xl:my-1">{{ \Carbon\Carbon::parse($req->start_date)->day }}</span>
                                <span class="text-xs text-white/40 font-black ml-4 xl:ml-0 uppercase tracking-widest border-t border-white/10 pt-2 mt-1">{{ \Carbon\Carbon::parse($req->start_date)->year + 543 }}</span>
                            </div>

                            <!-- Content Body -->
                            <div class="flex-1 min-w-0 flex flex-col justify-center py-2">
                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    @php
                                        $typeStyle = match ($req->leaveType->slug) {
                                            'sick' => 'bg-rose-50 text-rose-600 border-rose-100 icon-thermometer',
                                            'vacation' => 'bg-brand-50 text-brand-600 border-brand-100 icon-palmtree',
                                            'personal' => 'bg-amber-50 text-amber-600 border-amber-100 icon-briefcase',
                                            default => 'bg-slate-50 text-slate-500 border-slate-100 icon-file-text'
                                        };
                                        $typeIcon = match ($req->leaveType->slug) {
                                            'sick' => 'thermometer', 'vacation' => 'palmtree', 'personal' => 'briefcase', default => 'file-text'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-5 py-2 rounded-2xl text-xs font-black uppercase tracking-wider border shadow-sm {{ $typeStyle }}">
                                        <i data-lucide="{{ $typeIcon }}" class="w-4 h-4 mr-2.5"></i>
                                        {{ $req->leaveType->name }}
                                    </span>
                                    
                                    <span class="px-5 py-2 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-slate-900/20">
                                        <i data-lucide="timer" class="w-3.5 h-3.5 mr-2 inline text-brand-400"></i>{{ $req->total_days + 0 }} วัน
                                    </span>
                                    
                                    <span class="text-[10px] text-slate-300 font-black ml-auto uppercase tracking-widest hidden sm:block">REQUEST ID: #{{ $req->id }}</span>
                                </div>

                                <div class="mb-8 group/reason">
                                    <label class="block text-[10px] font-black text-slate-300 mb-3 uppercase tracking-widest leading-none">เหตุผลภารกิจและความจำเป็น</p>
                                    <p class="text-slate-800 text-2xl font-black leading-tight tracking-tight group-hover/reason:text-brand-600 transition-colors">
                                        {{ $req->reason }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-6">
                                    <div class="flex items-center gap-3 text-sm font-black text-slate-400 bg-slate-50 px-5 py-2.5 rounded-2xl border border-slate-100">
                                        <i data-lucide="calendar" class="w-4.5 h-4.5 text-slate-300"></i>
                                        <span>จนถึงวันที่ <b class="text-slate-700 ml-1">{{ \Carbon\Carbon::parse($req->end_date)->locale('th')->isoFormat('D MMMM YYYY') }}</b></span>
                                    </div>

                                    @if($req->attachment_path)
                                        <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="group/link flex items-center gap-3 text-xs font-black text-brand-600 uppercase tracking-widest hover:text-brand-700">
                                            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center group-hover/link:scale-110 group-hover/link:bg-brand-600 group-hover/link:text-white transition-all">
                                                <i data-lucide="paperclip" class="w-4 h-4"></i>
                                            </div>
                                            <span>ไฟล์ประกอบการลา</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions & Advanced Status Tracking -->
                            <div class="flex-shrink-0 w-full xl:w-64 flex flex-col justify-between items-end gap-6 pt-10 xl:pt-4 xl:pl-10 xl:border-l xl:border-slate-50">
                                 @php
                                    $statusConfig = match($req->status) {
                                        'approved' => ['bg' => 'bg-emerald-500', 'text' => 'text-white', 'icon' => 'shield-check', 'label' => 'อนุมัติแล้ว', 'glow' => 'shadow-emerald-500/30'],
                                        'rejected' => ['bg' => 'bg-rose-500', 'text' => 'text-white', 'icon' => 'alert-triangle', 'label' => 'ปฏิเสธแล้ว', 'glow' => 'shadow-rose-500/30'],
                                        'cancelled' => ['bg' => 'bg-slate-400', 'text' => 'text-white', 'icon' => 'ban', 'label' => 'ยกเลิกแล้ว', 'glow' => 'shadow-slate-400/30'],
                                        default => ['bg' => 'bg-amber-400', 'text' => 'text-white', 'icon' => 'clock-4', 'label' => 'รออนุมัติ', 'glow' => 'shadow-amber-500/30'],
                                    };
                                    
                                    // Language refinement for steps
                                    $stepLabels = [
                                        'pending_supervisor' => 'รอหน. แผนก',
                                        'pending_head' => 'รอหน. แผนก',
                                        'pending_manager' => 'รอผู้บังคับบัญชา',
                                        'pending_deputy_director' => 'รอ รอง ผอ.',
                                        'pending_director' => 'รอ ผอ.',
                                    ];
                                    if (isset($stepLabels[$req->status])) {
                                        $statusConfig['label'] = $stepLabels[$req->status];
                                    }
                                @endphp

                                <div class="w-full">
                                    <label class="block text-right text-[10px] font-black text-slate-300 mb-3 uppercase tracking-widest leading-none">สถานะปัจจุบัน</label>
                                    <div class="flex items-center gap-3 px-6 py-4 rounded-[1.5rem] font-black w-full justify-center xl:justify-end shadow-2xl transition-transform hover:scale-105 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['glow'] }}">
                                        <i data-lucide="{{ $statusConfig['icon'] }}" class="w-5 h-5"></i>
                                        <span class="text-sm tracking-tight">{{ $statusConfig['label'] }}</span>
                                    </div>
                                </div>
                                
                                <div class="w-full flex flex-col gap-3">
                                    <button type="button" @click="showPdf = true; pdfUrl = '{{ route('leave-request.pdf', $req->id) }}'" class="group/pdf w-full inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-slate-50 text-slate-700 font-black text-sm hover:bg-slate-100 transition-all active:scale-95 border border-slate-100">
                                        <i data-lucide="file-down" class="w-5 h-5 group-hover/pdf:translate-y-1 transition-transform text-slate-400"></i> 
                                        เรียกดูเอกสาร PDF
                                    </button>

                                    @if(str_starts_with($req->status, 'pending_'))
                                        <form action="{{ route('leave-request.cancel', $req->id) }}" method="POST" onsubmit="return confirm('ยืนยันการยกเลิกคำขอลา? เมื่อยกเลิกแล้วจะไม่สามารถย้อนคืนได้');" class="w-full">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-white border border-rose-100 text-rose-500 font-black text-sm hover:bg-rose-50 hover:border-rose-200 transition-all active:scale-95 group/cancel">
                                                <i data-lucide="trash-2" class="w-4 h-4 group-hover/cancel:rotate-12 transition-transform"></i> 
                                                ยกเลิกใบลา
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
                <div class="mt-20 flex justify-center">
                    <div class="bg-white p-2 rounded-[2rem] shadow-xl border border-slate-50">
                        {{ $requests->links() }}
                    </div>
                </div>
            @endif

        @endif

        <!-- Premium PDF Viewer Integration -->
        <template x-teleport="body">
            <div x-show="showPdf" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" style="display: none;" x-cloak>
                <!-- Cinematic Backdrop -->
                <div x-show="showPdf" 
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/90 backdrop-blur-md" 
                     @click="showPdf = false"></div>

                <!-- Floating Modal Panel -->
                <div x-show="showPdf"
                     x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 scale-95 translate-y-20" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-20"
                     class="relative bg-white rounded-[3rem] shadow-[0_30px_100px_-20px_rgba(0,0,0,0.5)] w-full max-w-6xl h-[92vh] flex flex-col overflow-hidden m-4 border border-white/10">
                    
                    <!-- Modern Header -->
                    <div class="bg-white px-8 py-6 flex justify-between items-center border-b border-slate-50 relative">
                        <div class="absolute -top-10 left-1/2 -ml-20 w-40 h-10 bg-brand-500 rounded-full blur-2xl opacity-20"></div>
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center shadow-inner">
                                <i data-lucide="file-text" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-800 tracking-tight">แบบฟอร์มใบลาตามระเบียบพัสดุ/ธุรการ</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Official Document Viewer</p>
                            </div>
                        </div>
                        <button @click="showPdf = false" class="w-12 h-12 bg-slate-100 hover:bg-slate-200 rounded-2xl text-slate-500 transition-all hover:rotate-90 active:scale-90 flex items-center justify-center">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                    
                    <!-- Seamless Content Area -->
                    <div class="flex-1 bg-slate-800 relative group/viewer">
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 space-y-4">
                            <i data-lucide="loader-2" class="w-10 h-10 animate-spin text-brand-500"></i>
                            <p class="font-black text-xs uppercase tracking-widest">กำลังสร้างเอกสาร PDF...</p>
                        </div>
                        <iframe :src="pdfUrl" class="relative z-10 w-full h-full border-0" allowfullscreen></iframe>
                    </div>

                    <!-- Strategic Footer -->
                    <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-4 border-t border-slate-100">
                        <div class="flex items-center gap-3 text-xs font-bold text-slate-400">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            คำแนะนำ: ท่านสามารถสั่งพิมพ์เอกสารได้โดยตรงผ่านเมนูในโปรแกรมอ่าน PDF
                        </div>
                        <div class="flex gap-4 w-full sm:w-auto">
                            <a :href="pdfUrl" target="_blank" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-3 px-8 py-3.5 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:bg-black transition-all active:scale-95 text-sm uppercase tracking-wider">
                                <i data-lucide="external-link" class="w-5 h-5"></i> 
                                เปิดแบบเต็มจอ
                            </a>
                            <button type="button" class="flex-1 sm:flex-initial px-8 py-3.5 bg-white text-slate-700 font-black rounded-2xl shadow-md border border-slate-200 hover:bg-slate-50 transition-all active:scale-95 text-sm uppercase tracking-wider" @click="showPdf = false">
                                ปิดหน้าต่าง
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

<x-app-layout>
    @section('title', 'ประวัติการลา (Leave History)')

    <div x-data="{ showPdf: false, pdfUrl: null }" class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div class="relative">
                <div class="absolute -left-4 -top-4 w-20 h-20 bg-brand-500 rounded-full blur-3xl opacity-10"></div>
                <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight relative z-10 flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white flex items-center justify-center text-xl shadow-lg shadow-brand-500/30">
                        <i data-lucide="history" class="w-3.5 h-3.5"></i>
                    </span>
                    ประวัติการลา
                </h2>
                <p class="text-slate-500 mt-3 text-lg pl-1">ติดตามสถานะและตรวจสอบรายการย้อนหลังของคุณ</p>
            </div>
            
            <a href="{{ route('leave-request.create') }}" 
               class="group inline-flex items-center justify-center px-6 py-4 bg-slate-900 hover:bg-brand-600 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-brand-500/20 transition-all duration-300 transform hover:-translate-y-1">
                <div class="mr-3 bg-white/20 p-1.5 rounded-lg group-hover:bg-white/30 transition-colors">
                     <i data-lucide="plus" class="w-4 h-4 text-sm"></i>
                </div>
                ส่งคำขอลาใหม่
            </a>
        </div>

        <!-- Flash Status -->
        @if (session('status'))
            <div class="mb-8 p-1 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-2xl shadow-lg shadow-emerald-500/20 animate-fade-in-down">
                <div class="bg-white rounded-xl p-4 flex items-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mr-4 flex-shrink-0 text-xl">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-800 text-base">สำเร็จ!</h4>
                        <p class="text-emerald-600 text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($requests->isEmpty())
             <!-- Premium Empty State -->
             <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12 text-center relative overflow-hidden group">
                <div class="absolute top-0 left-1/2 -ml-32 -mt-32 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-500">
                        <i data-lucide="folder-open" class="w-6 h-6 text-slate-300 text-6xl group-hover:text-brand-300 transition-colors"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3">ไม่พบรายการลา</h3>
                    <p class="text-slate-500 mb-10 max-w-sm mx-auto text-lg">คุณยังไม่มีประวัติการลาในระบบ เริ่มต้นโดยการสร้างคำขอใหม่ได้เลย</p>
                    <a href="{{ route('leave-request.create') }}" class="inline-flex items-center text-brand-600 hover:text-brand-700 font-bold hover:underline text-lg">
                        สร้างคำขอลาแรกของคุณ <i data-lucide="arrow-right" class="w-4 h-4 ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
        @else
            <!-- Timeline Layout -->
            <div class="space-y-8 relative">
                <!-- Vertical Line -->
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-slate-100 hidden md:block"></div>

                @foreach($requests as $req)
                <div class="relative pl-0 md:pl-24 group">
                    <!-- Timeline Dot -->
                    <div class="absolute left-6 top-8 w-4 h-4 bg-white border-4 border-brand-200 rounded-full z-10 hidden md:block group-hover:border-brand-500 transition-colors duration-300"></div>

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden">
                        
                        <!-- Top Decor -->
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-slate-50 to-slate-100 rounded-bl-full -mr-8 -mt-8 opacity-50 group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="flex flex-col lg:flex-row gap-8 relative z-10">
                            
                            <!-- Date Ticket -->
                            <div class="flex-shrink-0 flex flex-row lg:flex-col items-center justify-center w-full lg:w-32 bg-slate-50 rounded-2xl p-4 border border-slate-100 group-hover:bg-brand-50/30 group-hover:border-brand-100 transition-all">
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-0 lg:mb-1 mr-3 lg:mr-0">{{ \Carbon\Carbon::parse($req->start_date)->locale('th')->isoFormat('MMM') }}</span>
                                <span class="text-4xl lg:text-5xl font-black text-slate-800 my-0 lg:my-1">{{ \Carbon\Carbon::parse($req->start_date)->day }}</span>
                                <span class="text-xs text-slate-400 font-medium ml-3 lg:ml-0">{{ \Carbon\Carbon::parse($req->start_date)->year + 543 }}</span>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <div class="flex flex-wrap items-center gap-3 mb-4">
                                     <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-bold shadow-sm
                                        {{ $req->leaveType->slug == 'sick' ? 'bg-orange-50 text-orange-600 ring-1 ring-orange-100' : '' }}
                                        {{ $req->leaveType->slug == 'vacation' ? 'bg-blue-50 text-blue-600 ring-1 ring-blue-100' : '' }}
                                        {{ $req->leaveType->slug == 'personal' ? 'bg-purple-50 text-purple-600 ring-1 ring-purple-100' : 'bg-slate-50 text-slate-600 ring-1 ring-slate-100' }}">
                                        @if($req->leaveType->slug == 'sick') <i data-lucide="stethoscope" class="w-4 h-4 mr-2"></i>
                                        @elseif($req->leaveType->slug == 'vacation') <i data-lucide="plane" class="w-5 h-5 mr-2"></i>
                                        @else <i data-lucide="briefcase" class="w-4 h-4 mr-2"></i>
                                        @endif
                                        {{ $req->leaveType->name }}
                                    </span>
                                    
                                     @if($req->total_days > 0)
                                        <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                            <i data-lucide="timer" class="w-4 h-4 mr-1.5 text-slate-400"></i>{{ $req->total_days + 0 }} วัน
                                        </span>
                                    @endif
                                    
                                    <span class="text-xs text-slate-300 font-medium ml-auto">REF: #{{ $req->id }}</span>
                                </div>

                                <div class="mb-5">
                                    <h5 class="text-sm font-bold text-slate-400 mb-2 uppercase tracking-wide text-[10px]">เหตุผลการลา</h5>
                                    <p class="text-slate-700 text-lg leading-relaxed font-medium">
                                        {{ $req->reason }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap items-center gap-4">
                                    <div class="flex items-center gap-2 text-sm text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                        <span>ถึงวันที่ <b class="text-slate-700">{{ \Carbon\Carbon::parse($req->end_date)->locale('th')->isoFormat('D MMM YY') }}</b></span>
                                    </div>

                                    @if($req->attachment_path)
                                        <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="flex items-center gap-2 text-sm text-brand-600 font-bold hover:underline decoration-2 underline-offset-4">
                                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                                            <span>ไฟล์แนบ</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex-shrink-0 w-full lg:w-56 flex flex-col justify-between items-end gap-4 border-l border-slate-50 pl-0 lg:pl-8 pt-6 lg:pt-0 mt-6 lg:mt-0 lg:border-l lg:border-slate-100">
                                 @php
                                    $statusConfig = match($req->status) {
                                        'approved' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-50', 'icon' => 'check', 'label' => 'อนุมัติแล้ว'],
                                        'rejected' => ['bg' => 'bg-rose-500', 'text' => 'text-rose-50', 'icon' => 'x', 'label' => 'ถูกปฏิเสธ'],
                                        'cancelled' => ['bg' => 'bg-slate-400', 'text' => 'text-white', 'icon' => 'ban', 'label' => 'ยกเลิกแล้ว'],
                                        'pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director' => ['bg' => 'bg-amber-400', 'text' => 'text-white', 'icon' => 'hourglass', 'label' => 'รออนุมัติ'],
                                        default => ['bg' => 'bg-slate-200', 'text' => 'text-slate-500', 'icon' => 'help-circle', 'label' => ucfirst($req->status)]
                                    };
                                    
                                    if ($req->status == 'pending_supervisor') $statusConfig['label'] = 'รอหัวหน้าแผนก';
                                    if ($req->status == 'pending_head') $statusConfig['label'] = 'รอหัวหน้าแผนก';
                                    if ($req->status == 'pending_manager') $statusConfig['label'] = 'รอผู้บังคับบัญชา';
                                    if ($req->status == 'pending_deputy_director') $statusConfig['label'] = 'รอ รอง ผอ.';
                                    if ($req->status == 'pending_director') $statusConfig['label'] = 'รอ ผอ.';
                                @endphp

                                <div class="flex flex-col items-end w-full">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">สถานะคำขอ</span>
                                    <div class="flex items-center gap-3 px-4 py-2 rounded-xl text-sm font-bold w-full justify-center lg:justify-end shadow-lg shadow-slate-200/50 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                        <span class="">{{ $statusConfig['label'] }}</span>
                                        <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center">
                                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-2.5 h-2.5"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="w-full flex flex-col gap-2 mt-auto">
                                    <button type="button" @click="showPdf = true; pdfUrl = '{{ route('leave-request.pdf', $req->id) }}'" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 text-slate-600 font-bold hover:bg-slate-100 transition-all duration-200 group/btn">
                                        <i data-lucide="file-text" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i> PDF
                                    </button>

                                    @if(in_array($req->status, ['pending_supervisor', 'pending_head', 'pending_deputy_director', 'pending_manager', 'pending_director']))
                                        <form action="{{ route('leave-request.cancel', $req->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจว่าต้องการยกเลิกคำขอนี้?');" class="w-full">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-rose-100 text-rose-500 font-bold hover:bg-rose-50 hover:border-rose-200 transition-all duration-200">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i> ยกเลิก
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

            @if($requests->hasPages())
                <div class="mt-8">
                    {{ $requests->links() }}
                </div>
            @endif

        @endif

        <!-- PDF Modal -->
        <template x-teleport="body">
            <div x-show="showPdf" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
                <!-- Backdrop -->
                <div x-show="showPdf" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" 
                     @click="showPdf = false"></div>

                <!-- Modal Panel -->
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showPdf"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 w-full max-w-5xl h-[85vh] flex flex-col">
                        
                        <!-- Header -->
                        <div class="bg-white px-4 py-3 flex justify-between items-center border-b border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4 text-red-500"></i>
                                ใบลาพักผ่อน/ลากิจ/ลาป่วย
                            </h3>
                            <button @click="showPdf = false" class="text-slate-400 hover:text-slate-600 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100">
                                <i data-lucide="x" class="w-4 h-4 text-lg"></i>
                            </button>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 bg-slate-100 p-0 overflow-hidden relative">
                             <div x-show="!pdfUrl" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                 <i data-lucide="loader" class="w-6 h-6 animate-spin text-2xl mr-2"></i> Loading...
                             </div>
                             <iframe :src="pdfUrl" class="w-full h-full border-0" allowfullscreen></iframe>
                        </div>

                        <!-- Footer -->
                        <div class="bg-slate-50 px-4 py-3 flex justify-end gap-3 border-t border-slate-100">
                            <a :href="pdfUrl" target="_blank" class="inline-flex justify-center rounded-xl border border-transparent bg-slate-800 px-4 py-2 text-sm font-bold text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-all">
                                <i data-lucide="external-link" class="w-4 h-4 mr-2"></i> เปิดในแท็บใหม่
                            </a>
                            <button type="button" class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all" @click="showPdf = false">
                                ปิด
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>

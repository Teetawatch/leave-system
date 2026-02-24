<x-app-layout>
    @section('title', 'จัดการแผนก (Departments)')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .dept-row {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .dept-row:hover {
                background: rgba(248, 250, 252, 0.8);
                transform: translateX(6px);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-100/30 rounded-full blur-[120px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-100/30 rounded-full blur-[100px] -ml-36 -mb-36"></div>

        <!-- Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-emerald-100">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            ผังองค์กร
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                            จัดการ <span class="text-emerald-600">แผนก</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                            สร้าง จัดการ และปรับโครงสร้างหน่วยงาน<br class="hidden md:block">
                            เพื่อความเป็นระเบียบในระบบบริหารจัดการ
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

                <!-- Create Form (Sticky Sidebar) -->
                <div class="lg:col-span-1 animate-slide-up" style="animation-delay: 0.1s">
                    <div class="glass-panel rounded-[3rem] overflow-hidden sticky top-24">
                        <div class="bg-emerald-600 px-8 py-6 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center border border-white/10 backdrop-blur-md">
                                <i data-lucide="plus-circle" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-white text-lg tracking-tight">เพิ่มแผนกใหม่</h3>
                                <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Create Department</p>
                            </div>
                        </div>

                        <form action="{{ route('departments.store') }}" method="POST" class="p-8 space-y-6">
                            @csrf
                            <div class="space-y-3">
                                <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อแผนก <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" placeholder="ระบุชื่อแผนก..." required
                                       class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 font-bold text-slate-800 text-lg transition-all"
                                       value="{{ old('name') }}">
                                @error('name') <p class="text-[10px] font-bold text-rose-500 mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full py-5 bg-slate-900 hover:bg-emerald-600 text-white font-black text-sm rounded-[2rem] shadow-xl transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest flex items-center justify-center gap-3">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                บันทึกแผนก
                            </button>
                        </form>

                        <!-- Tip Card -->
                        <div class="mx-8 mb-8 p-5 bg-slate-900 rounded-2xl text-white relative overflow-hidden">
                            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=%2220%22 height=%2220%22 viewBox=%220 0 20 20%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22%23ffffff%22 fill-opacity=%220.03%22 fill-rule=%22evenodd%22%3E%3Ccircle cx=%223%22 cy=%223%22 r=%223%22/%3E%3C/g%3E%3C/svg%3E')]"></div>
                            <div class="relative z-10 flex items-start gap-3">
                                <i data-lucide="lightbulb" class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-[10px] font-black text-white/50 uppercase tracking-widest mb-1">คำแนะนำ</p>
                                    <p class="text-xs text-slate-400 leading-relaxed">แผนกที่สร้างจะถูกนำไปใช้ในการจัดกลุ่มกำลังพลและการออกรายงานอัตโนมัติ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List -->
                <div class="lg:col-span-2 animate-slide-up" style="animation-delay: 0.2s">
                    <div class="glass-panel rounded-[3rem] overflow-hidden">
                        <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg">
                                    <i data-lucide="building-2" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 tracking-tight">รายชื่อแผนกทั้งหมด</h3>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Department Directory</p>
                                </div>
                            </div>
                            <div class="px-5 py-2 rounded-full bg-slate-100 text-slate-600 text-sm font-black border border-slate-200 shadow-sm">
                                {{ $departments->total() }} <span class="text-slate-400 text-xs">แผนก</span>
                            </div>
                        </div>

                        <div class="divide-y divide-slate-50 p-4">
                            @forelse($departments as $index => $dept)
                                @php
                                    $colors = ['indigo', 'emerald', 'amber', 'rose', 'violet', 'cyan', 'orange', 'teal'];
                                    $color = $colors[$index % count($colors)];
                                @endphp
                                <div class="dept-row flex items-center justify-between p-5 rounded-2xl group">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center font-black text-lg border border-{{ $color }}-100 group-hover:bg-{{ $color }}-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                            {{ mb_substr($dept->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-black text-slate-800 text-lg group-hover:text-{{ $color }}-600 transition-colors">{{ $dept->name }}</span>
                                            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest mt-0.5">ID: {{ str_pad($dept->id, 3, '0', STR_PAD_LEFT) }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <form action="{{ route('departments.destroy', $dept->id) }}" method="POST"
                                              onsubmit="return confirm('ยืนยันการลบแผนก {{ $dept->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-12 h-12 rounded-2xl bg-white text-slate-400 flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all border border-slate-100 hover:border-rose-200 shadow-sm hover:shadow-lg active:scale-90" title="ลบแผนก">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-20 text-center">
                                    <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner">
                                        <i data-lucide="folder-open" class="w-12 h-12 text-slate-200"></i>
                                    </div>
                                    <h4 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">ยังไม่มีข้อมูลแผนก</h4>
                                    <p class="text-slate-400 font-medium">เริ่มสร้างแผนกแรกของคุณจากแบบฟอร์มด้านซ้าย</p>
                                </div>
                            @endforelse
                        </div>

                        @if($departments->hasPages())
                            <div class="px-10 py-6 border-t border-slate-100 bg-slate-50/30 font-bold">
                                {{ $departments->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Visual End -->
            <div class="mt-20 flex flex-col items-center justify-center gap-6 opacity-30">
                <div class="w-1 bg-gradient-to-b from-emerald-500 to-transparent h-20 rounded-full"></div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">สิ้นสุดหน้าจัดการแผนก</div>
            </div>
        </div>
    </div>
</x-app-layout>

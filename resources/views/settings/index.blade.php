<x-app-layout>
    @section('title', 'ตั้งค่าระบบ (System Settings)')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
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

            .setting-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .setting-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08);
            }

            .leave-type-row {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .leave-type-row:hover {
                background: rgba(248, 250, 252, 0.8);
                transform: translateX(4px);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-violet-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <!-- Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            ตั้งค่าระบบ
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                            การตั้งค่า <span class="text-indigo-600">ระบบ</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                            กำหนดค่าพารามิเตอร์ของระบบ ปรับแต่งประเภทการลา<br class="hidden md:block">
                            และจัดการตั้งค่าองค์กรจากศูนย์กลาง
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-10">

            <!-- Leave Configuration Card -->
            <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up setting-card" style="animation-delay: 0.1s">
                <div class="bg-slate-900 px-10 py-8 flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl bg-white/10 text-indigo-400 flex items-center justify-center border border-white/10 shadow-inner">
                        <i data-lucide="sliders-horizontal" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tight">ตั้งค่าประเภทการลา</h3>
                        <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase mt-1">กำหนดจำนวนวันลาสูงสุดต่อปีสำหรับแต่ละประเภท</p>
                    </div>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="p-10">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        @php
                            $colors = ['indigo', 'emerald', 'amber', 'rose', 'violet', 'cyan', 'orange', 'teal'];
                        @endphp
                        @foreach($leaveTypes as $index => $type)
                            @php $color = $colors[$index % count($colors)]; @endphp
                            <div class="leave-type-row flex items-center justify-between p-6 rounded-[2rem] border border-slate-100 group">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center font-black text-lg border border-{{ $color }}-100 group-hover:bg-{{ $color }}-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                        {{ mb_substr($type->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <label for="type_{{ $type->id }}" class="block font-black text-slate-800 text-lg tracking-tight group-hover:text-{{ $color }}-600 transition-colors">{{ $type->name }}</label>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Slug: {{ $type->slug }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <input type="number" name="leave_types[{{ $type->id }}][max_days]" value="{{ $type->max_days_per_year }}"
                                           class="block w-28 rounded-2xl border-slate-100 bg-slate-50 shadow-sm focus:border-{{ $color }}-500 focus:ring-4 focus:ring-{{ $color }}-500/10 text-center font-black text-2xl text-slate-800 py-4 transition-all">
                                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">วัน/ปี</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="group inline-flex items-center justify-center px-10 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-[2rem] shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest gap-3">
                            <i data-lucide="save" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            บันทึกการตั้งค่า
                        </button>
                    </div>
                </form>
            </div>

            <!-- Organization Settings (Coming Soon) -->
            <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up setting-card" style="animation-delay: 0.2s">
                <div class="p-10 flex flex-col items-center text-center py-20 relative overflow-hidden group">
                    <!-- Background Pattern -->
                    <div class="absolute inset-0 opacity-[0.02]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

                    <div class="relative z-10">
                        <div class="w-24 h-24 rounded-[2rem] bg-slate-100 flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-200 group-hover:scale-110 transition-transform duration-500">
                            <i data-lucide="building-2" class="w-12 h-12 text-slate-300"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 mb-3 tracking-tight">ตั้งค่าองค์กร</h3>
                        <p class="text-slate-400 font-bold text-lg max-w-sm mx-auto mb-8 leading-relaxed">
                            ข้อมูลบริษัทและวันหยุดประจำปี
                        </p>
                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-400 rounded-full text-[11px] font-black uppercase tracking-widest border border-slate-200">
                            <i data-lucide="construction" class="w-4 h-4"></i>
                            Coming Soon
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual End -->
            <div class="mt-16 flex flex-col items-center justify-center gap-6 opacity-30">
                <div class="w-1 bg-gradient-to-b from-indigo-500 to-transparent h-20 rounded-full"></div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">สิ้นสุดหน้าตั้งค่าระบบ</div>
            </div>
        </div>
    </div>
</x-app-layout>

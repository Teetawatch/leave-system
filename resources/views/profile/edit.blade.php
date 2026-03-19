<x-app-layout>
    @section('title', 'จัดการบัญชีและข้อมูลส่วนตัว')

    <div class="min-h-screen relative pb-20 bg-slate-50/30">
        <!-- Background Orbs -->
        <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-200/40 rounded-full blur-3xl mix-blend-multiply"></div>
            <div class="absolute top-[20%] right-[-10%] w-[30%] h-[50%] bg-rose-200/30 rounded-full blur-3xl mix-blend-multiply"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-[40%] h-[40%] bg-amber-200/30 rounded-full blur-3xl mix-blend-multiply"></div>
        </div>

        <!-- Header -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-8">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 flex-shrink-0 group hover:rotate-6 transition-transform">
                    <i data-lucide="user-cog" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">ตั้งค่าบัญชี</h1>
                    <p class="text-sm text-slate-500 font-medium mt-1">จัดการตัวตนดิจิทัลของคุณและรักษาความปลอดภัยของบัญชีผู้ใช้งาน</p>
                </div>
            </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            @if(session('error'))
                <div class="bg-white/80 backdrop-blur-md border-l-4 border-rose-500 p-4 rounded-xl shadow-lg border border-slate-100 flex items-start gap-4 animate-slide-in-up">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-rose-500"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-rose-800 tracking-tight">ไม่สามารถเข้าใช้งานระบบได้</h3>
                        <p class="text-xs font-semibold text-rose-600 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Quick Identity Summary Card -->
            <div class="bg-white/70 backdrop-blur-xl rounded-[2.5rem] shadow-xl shadow-indigo-900/5 border border-white p-6 sm:p-10 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/60 rounded-bl-full -mr-20 -mt-20 opacity-50 group-hover:scale-110 transition-transform duration-700"></div>

                <div class="relative flex-shrink-0">
                    <div class="w-28 h-28 rounded-[2rem] bg-slate-50 border-4 border-white shadow-lg overflow-hidden group-hover:-rotate-3 group-hover:scale-105 transition-transform duration-500">
                        @if ($user->avatar)
                            <img src="{{ route('storage.file', ['path' => $user->avatar]) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl font-black text-slate-300 uppercase bg-slate-100">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left space-y-2 relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $user->rank }}{{ $user->name }}</h2>
                        <span class="inline-block px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 text-[10px] font-bold uppercase tracking-widest shadow-sm">
                            {{ $user->role == 'admin' ? 'ผู้ดูแลระบบ' : ($user->role == 'director' ? 'ผอ.รพธ.พธ.ทร.' : 'กำลังพล') }}
                        </span>
                    </div>
                    <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">
                        {{ $user->department ?? 'สังกัดหน่วยงาน' }} / {{ $user->position ?? '-' }}
                    </p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white/80 backdrop-blur rounded-xl text-xs font-semibold text-slate-600 border border-slate-200/60 shadow-sm">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $user->email }}
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 rounded-xl text-xs font-semibold text-emerald-600 border border-emerald-100 shadow-sm">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                            สถานะยืนยันแล้ว
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0 grid grid-cols-2 gap-3 w-full md:w-auto relative z-10">
                    <div class="bg-white/60 border border-white rounded-2xl p-4 text-center min-w-[110px] shadow-sm backdrop-blur-md hover:bg-white/80 transition-colors">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">สิทธิ์วันลาพักร้อน</p>
                        <p class="text-2xl font-black text-indigo-600 tracking-tight">{{ $user->employee->vacation_leave_days ?? 10 }}</p>
                    </div>
                    <div class="bg-white/60 border border-white rounded-2xl p-4 text-center min-w-[110px] shadow-sm backdrop-blur-md hover:bg-white/80 transition-colors">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">สถานะระบบ</p>
                        <p class="text-sm font-bold text-emerald-500 mt-2 flex items-center justify-center gap-1 bg-emerald-50 py-1 px-2 rounded-lg"><i data-lucide="check-circle-2" class="w-4 h-4"></i> ปกติ</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Content: Identity Info -->
                <div class="lg:col-span-8 space-y-8">
                    <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-white overflow-hidden group/card hover:shadow-2xl hover:shadow-indigo-900/10 transition-all duration-300">
                        <div class="px-6 sm:px-8 py-5 border-b border-slate-100/50 flex items-center gap-4 bg-white/40">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm group-hover/card:scale-110 group-hover/card:rotate-3 transition-transform">
                                <i data-lucide="contact" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-800 text-lg tracking-tight">ข้อมูลส่วนตัวและระบบ</h3>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">ส่วนสำหรับปรับปรุงข้อมูลการติดต่อและภาพประจำตัว</p>
                            </div>
                        </div>
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                    
                    <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] shadow-xl shadow-rose-900/5 border border-white overflow-hidden group/card hover:shadow-2xl hover:shadow-rose-900/10 transition-all duration-300">
                        <div class="px-6 sm:px-8 py-5 border-b border-slate-100/50 flex items-center gap-4 bg-white/40">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shadow-sm group-hover/card:scale-110 group-hover/card:rotate-3 transition-transform">
                                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-800 text-lg tracking-tight">การจัดการบัญชีอันตราย</h3>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">การลบบัญชี ข้อมูลทั้งหมดจะถูกลบถาวร</p>
                            </div>
                        </div>
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Security Actions -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] shadow-xl shadow-indigo-900/5 border border-white overflow-hidden group/card hover:shadow-2xl hover:shadow-indigo-900/10 transition-all duration-300">
                        <div class="px-6 py-5 border-b border-slate-100/50 bg-white/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center shadow-sm group-hover/card:scale-110 group-hover/card:rotate-3 transition-transform">
                                <i data-lucide="key-square" class="w-4 h-4"></i>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-sm tracking-tight">ความปลอดภัยรหัสผ่าน</h3>
                        </div>
                        <div class="p-6 sm:p-8">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Telegram Bot Connection -->
                    <div class="bg-white/70 backdrop-blur-xl rounded-[2rem] shadow-xl shadow-sky-900/5 border border-white overflow-hidden group/card hover:shadow-2xl hover:shadow-sky-900/10 transition-all duration-300">
                        <div class="px-6 py-5 border-b border-slate-100/50 bg-white/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-sky-50 border border-sky-100 text-sky-500 flex items-center justify-center shadow-sm group-hover/card:scale-110 group-hover/card:rotate-3 transition-transform">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            </div>
                            <h3 class="font-extrabold text-slate-800 text-sm tracking-tight">Telegram แจ้งเตือน</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @if($user->telegram_chat_id)
                                <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-emerald-700">เชื่อมต่อแล้ว</p>
                                        <p class="text-[10px] text-emerald-600 font-medium truncate">Chat ID: {{ $user->telegram_chat_id }}</p>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                                    คุณจะได้รับแจ้งเตือนการลา/อนุมัติ, สรุปประจำวัน และตารางเวรผ่าน Telegram
                                </p>
                                <form method="POST" action="{{ route('profile.telegram-unlink') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold rounded-xl border border-rose-200 transition-colors flex items-center justify-center gap-2"
                                        onclick="return confirm('ยืนยันยกเลิกการเชื่อมต่อ Telegram?')">
                                        <i data-lucide="unlink" class="w-3.5 h-3.5"></i>
                                        ยกเลิกการเชื่อมต่อ
                                    </button>
                                </form>
                            @else
                                <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="link" class="w-4 h-4 text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-600">ยังไม่ได้เชื่อมต่อ</p>
                                        <p class="text-[10px] text-slate-400 font-medium">เชื่อมต่อเพื่อรับแจ้งเตือนผ่าน Telegram</p>
                                    </div>
                                </div>

                                @if(session('telegram_link'))
                                    <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl space-y-2">
                                        <p class="text-[11px] font-bold text-sky-700">กดลิงก์ด้านล่างเพื่อเชื่อมต่อ:</p>
                                        <a href="{{ session('telegram_link') }}" target="_blank"
                                           class="w-full px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition-colors text-center flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                            เปิด Telegram Bot
                                        </a>
                                        <p class="text-[10px] text-sky-600 font-medium">ลิงก์จะหมดอายุเมื่อใช้งานแล้ว</p>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('profile.telegram-link') }}">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-2 shadow-lg shadow-sky-500/20">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                            เชื่อมต่อ Telegram
                                        </button>
                                    </form>
                                @endif

                                <div class="p-3 bg-slate-50/80 border border-slate-100 rounded-xl">
                                    <p class="text-[10px] text-slate-500 font-medium leading-relaxed">
                                        💡 เชื่อมต่อ Telegram เพื่อรับแจ้งเตือน: ใบลาใหม่, สถานะอนุมัติ, สรุปประจำวัน, ตารางเวร และสามารถอนุมัติ/ปฏิเสธผ่าน Telegram ได้ทันที
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Tip Card -->
                    <div class="bg-slate-800 rounded-[2rem] border border-slate-700 p-6 sm:p-8 text-white relative overflow-hidden group shadow-2xl shadow-slate-900/50">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-bl-full -mr-10 -mt-10 blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative z-10 space-y-5">
                            <div class="w-12 h-12 rounded-[1rem] bg-white/10 flex items-center justify-center text-amber-400 border border-white/10 backdrop-blur-md shadow-inner">
                                <i data-lucide="lightbulb" class="w-6 h-6"></i>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-extrabold text-base tracking-tight text-white">ข้อแนะนำด้านไฟล์ภาพ</h4>
                                <p class="text-[12px] font-medium text-slate-300 leading-relaxed">
                                    ลายเซ็นควรเป็นไฟล์ PNG แบบไม่มีพื้นหลัง เพื่อนำไปประทับในใบลาอิเล็กทรอนิกส์
                                </p>
                            </div>
                            <div class="p-4 bg-black/20 border border-white/5 rounded-2xl flex flex-col gap-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">รองรับไฟล์</span>
                                    <span class="text-[11px] font-bold text-slate-200 px-2 py-0.5 bg-white/10 rounded-md">JPG, PNG</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">ขนาดสูงสุด</span>
                                    <span class="text-[11px] font-bold text-slate-200 px-2 py-0.5 bg-white/10 rounded-md">2 MB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        </script>
        <style>
            @keyframes slideInUp {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-slide-in-up {
                animation: slideInUp 0.4s ease-out forwards;
            }
        </style>
    @endpush
</x-app-layout>
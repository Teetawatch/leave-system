<x-app-layout>
    @section('title', 'จัดการบัญชีและข้อมูลส่วนตัว')

    <div class="min-h-screen bg-slate-50/50 pb-20">

        <!-- Premium Profile Header -->
        <div class="bg-slate-900 pt-16 pb-32 px-4 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 z-0 opacity-10">
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500 rounded-full blur-[100px] -mr-20 -mt-20">
                </div>
                <div class="absolute bottom-0 left-0 w-80 h-80 bg-brand-500 rounded-full blur-[100px] -ml-20 -mb-20">
                </div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto flex flex-col items-center text-center">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 backdrop-blur-xl border border-white/10 text-[10px] font-black text-brand-400 uppercase tracking-widest mb-8 animate-fade-in">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    ตั้งค่าบัญชี
                </div>

                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
                    ข้อมูลส่วนตัวและระบบความปลอดภัย</h1>
                <p class="text-slate-400 text-lg font-bold max-w-2xl leading-relaxed italic">
                    จัดการตัวตนดิจิทัลของคุณและรักษาความปลอดภัยของบัญชีผู้ใช้งาน</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10">

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-8 rounded-r-xl shadow-lg flex items-start gap-4 animate-fade-in relative z-20">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-rose-500"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-rose-800 tracking-tight">ไม่สามารถเข้าใช้งานระบบได้</h3>
                        <p class="text-sm font-bold text-rose-600 mt-1">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Quick Identity Summary Card -->
            <div
                class="bg-white rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-50 p-8 mb-12 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-20 -mt-20 opacity-30 group-hover:scale-125 transition-transform duration-700">
                </div>

                <div class="relative flex-shrink-0">
                    <div
                        class="w-32 h-32 rounded-[2.5rem] bg-slate-100 border-4 border-white shadow-xl overflow-hidden group-hover:rotate-3 transition-transform duration-500">
                        @if ($user->avatar)
                            <img src="{{ route('storage.file', ['path' => $user->avatar]) }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-4xl font-black text-slate-300">
                                {{ substr($user->name, 0, 1) }}</div>
                        @endif
                    </div>
                </div>

                <div class="flex-1 text-center md:text-left space-y-2 relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight">{{ $user->rank }}{{ $user->name }}
                        </h2>
                        <span
                            class="inline-block px-4 py-1 rounded-full bg-brand-50 text-brand-600 text-[10px] font-black uppercase tracking-widest border border-brand-100">
                            {{ $user->role == 'admin' ? 'ผู้ดูแลระบบ' : ($user->role == 'director' ? 'ผอ.รพธ.พธ.ทร.' : 'กำลังพล') }}
                        </span>
                    </div>
                    <p class="text-slate-500 font-bold text-lg uppercase tracking-widest">
                        {{ $user->department ?? 'สังกัดหน่วยงาน' }} / {{ $user->position ?? '-' }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-4">
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl text-xs font-bold text-slate-400 border border-slate-100">
                            <i data-lucide="mail" class="w-4 h-4 text-brand-500"></i>
                            {{ $user->email }}
                        </div>
                        <div
                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-xl text-xs font-bold text-slate-400 border border-slate-100">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                            ยืนยันแล้ว
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0 grid grid-cols-2 gap-4">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-center min-w-[120px]">
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">สิทธิ์วันลา</p>
                        <p class="text-2xl font-black text-indigo-600">{{ $user->employee->vacation_leave_days ?? 10 }}
                            วัน</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-center min-w-[120px]">
                        <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">พาสเวิร์ด</p>
                        <p class="text-2xl font-black text-amber-600 italic">ปลอดภัย</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <!-- Main Content: Identity Info -->
                <div class="lg:col-span-8 space-y-10">
                    <div
                        class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50 overflow-hidden">
                        <div
                            class="px-10 py-8 border-b border-slate-50 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-inner">
                                <i data-lucide="contact" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 text-xl tracking-tight">ข้อมูลบัญชีและรูปถ่าย</h3>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1 italic">
                                    ข้อมูลส่วนตัวและรูปโปรไฟล์</p>
                            </div>
                        </div>
                        <div class="p-10">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Security Actions -->
                <div class="lg:col-span-4 space-y-10">
                    <div
                        class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50 overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-50 bg-amber-50/50 flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="key-square" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-black text-slate-800 text-sm tracking-tight uppercase">การจัดการความปลอดภัย
                            </h3>
                        </div>
                        <div class="p-8">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Status Tip Card -->
                    <div class="bg-slate-900 rounded-[3rem] p-8 text-white relative overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.05]">
                        </div>
                        <div class="relative z-10 space-y-6">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-amber-400 border border-white/10">
                                <i data-lucide="lightbulb" class="w-6 h-6"></i>
                            </div>
                            <div class="space-y-2">
                                <h4 class="font-black text-lg tracking-tight">คำแนะนำในการเปลี่ยนรูป</h4>
                                <p class="text-sm font-bold text-slate-400 leading-relaxed uppercase tracking-widest">
                                    ลายเซ็นที่อัปโหลดจะถูกนำไปใช้ในเอกสารใบลาโดยอัตโนมัติ
                                    กรุณาใช้ไฟล์รูปที่มีพื้นหลังโปร่งใสหรือสีขาวสะอาด</p>
                            </div>
                            <div class="p-4 bg-white/5 border border-white/10 rounded-2xl flex items-center gap-3">
                                <i data-lucide="info" class="w-4 h-4 text-brand-400"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">รองรับไฟล์: JPG, PNG</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
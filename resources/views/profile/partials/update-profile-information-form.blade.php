<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-col xl:flex-row gap-8 items-start">

            <!-- Asset Upload Section -->
            <div class="flex flex-col sm:flex-row xl:flex-col gap-6 w-full xl:w-56 shrink-0">
                <!-- Avatar Upload -->
                <div class="space-y-3 flex-1" x-data="{ photoPreview: null }">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">รูปประจำตัว</label>
                    <div class="relative group w-full">
                        <div class="w-full aspect-square xl:aspect-auto xl:h-56 rounded-[1.5rem] bg-white border border-slate-200 shadow-sm overflow-hidden relative cursor-pointer group-hover:border-indigo-300 transition-all duration-300"
                            onclick="document.getElementById('avatar').click()">

                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-cover">
                            </template>

                            <template x-if="!photoPreview">
                                <div class="w-full h-full flex items-center justify-center bg-slate-50">
                                    @if ($user->avatar)
                                        <img src="{{ route('storage.file', ['path' => $user->avatar]) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-4xl font-black text-slate-300 uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            </template>

                            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all gap-2 duration-300">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-md">
                                    <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                                </div>
                                <span class="text-[9px] font-bold text-white uppercase tracking-widest px-2 py-0.5 bg-slate-900/50 rounded-md">เปลี่ยนรูปโปรไฟล์</span>
                            </div>
                        </div>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*"
                            @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => photoPreview = e.target.result; reader.readAsDataURL(file); }">
                    </div>
                </div>

                <!-- Signature Upload -->
                <div class="space-y-3 flex-1" x-data="{ sigPreview: null }">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">ลายเซ็น (สำหรับใบลา)</label>
                    <div class="relative group w-full">
                        <div class="w-full aspect-square xl:aspect-[4/3] rounded-[1.5rem] bg-white border border-slate-200 shadow-sm overflow-hidden relative cursor-pointer group-hover:border-indigo-300 transition-all duration-300"
                            onclick="document.getElementById('signature').click()">

                            <template x-if="sigPreview">
                                <img :src="sigPreview" class="w-full h-full object-contain p-4 bg-slate-50/50">
                            </template>

                            <template x-if="!sigPreview">
                                <div class="w-full h-full flex items-center justify-center bg-slate-50">
                                    @if ($user->signature)
                                        <img src="{{ route('storage.file', ['path' => $user->signature]) }}" class="w-full h-full object-contain p-4 mix-blend-multiply">
                                    @else
                                        <div class="flex flex-col items-center text-slate-400 gap-2 opacity-50">
                                            <i data-lucide="file-signature" class="w-8 h-8"></i>
                                            <span class="text-[9px] font-bold uppercase tracking-widest bg-slate-200/50 px-2 py-0.5 rounded-full text-slate-600">ยังไม่เพิ่มไฟล์</span>
                                        </div>
                                    @endif
                                </div>
                            </template>

                            <div class="absolute inset-0 bg-indigo-600/40 backdrop-blur-[2px] flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all gap-2 duration-300">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-md">
                                    <i data-lucide="upload" class="w-5 h-5 text-white"></i>
                                </div>
                                <span class="text-[9px] font-bold text-white uppercase tracking-widest px-2 py-0.5 bg-indigo-900/50 rounded-md">เลือกไฟล์ภาพ</span>
                            </div>
                        </div>
                        <input type="file" id="signature" name="signature" class="hidden" accept="image/*"
                            @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => sigPreview = e.target.result; reader.readAsDataURL(file); }">
                    </div>
                </div>
            </div>

            <!-- Text Fields Section -->
            <div class="flex-1 w-full space-y-6">
                <!-- Name Field -->
                <div class="space-y-2 relative group-input">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">ยศ ชื่อ - นามสกุล <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="user" class="w-[18px] h-[18px]"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-[15px] font-semibold text-slate-800 transition-all shadow-sm outline-none"
                            placeholder="กรอกยศ และชื่อ-นามสกุล">
                    </div>
                    @error('name') 
                        <p class="text-[10px] font-bold text-rose-500 mt-1.5 ml-1 flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i>{{ $message }}</p> 
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="space-y-2 relative group-input">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">อีเมลติดต่อ <span class="text-rose-500">*</span></label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                            <i data-lucide="mail" class="w-[18px] h-[18px]"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-[15px] font-semibold text-slate-800 transition-all shadow-sm outline-none"
                            placeholder="email@example.com">
                    </div>
                    @error('email') 
                        <p class="text-[10px] font-bold text-rose-500 mt-1.5 ml-1 flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i>{{ $message }}</p> 
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative overflow-hidden">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-amber-500 opacity-5 rounded-bl-[100px] pointer-events-none"></div>
                            <div class="relative z-10 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </div>
                                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-widest leading-tight">
                                    คุณยังไม่ได้ยืนยัน<br>ที่อยู่อีเมลเข้าใช้งาน
                                </p>
                            </div>
                            <button form="send-verification" class="w-full sm:w-auto px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-xl shadow-md shadow-amber-500/20 active:scale-95 transition-all focus:ring-2 focus:ring-amber-500/30 outline-none relative z-10">
                                ส่งลิงก์ใหม่
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Submit Button Area -->
                <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-4 py-2 bg-opacity-80 backdrop-blur rounded-xl border border-emerald-200/50 shadow-sm" 
                        x-data="{ show: false }" x-show="show" x-transition.opacity.duration.300ms x-init="@if(session('status') === 'profile-updated') show = true; setTimeout(() => show = false, 4000) @endif" style="display: none;">
                        <i data-lucide="check-circle-2" class="w-[18px] h-[18px]"></i>
                        <span class="text-[11px] font-bold uppercase tracking-widest">อัปเดตข้อมูลไฟล์และรายชื่อเรียบร้อย</span>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-2xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 group sm:ml-auto focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                        <i data-lucide="save" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i>
                        บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>
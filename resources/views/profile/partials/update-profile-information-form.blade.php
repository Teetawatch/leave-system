<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-12" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-col xl:flex-row gap-12 items-start">

            <!-- Asset Upload Section -->
            <div class="flex flex-col gap-10 w-full xl:w-auto">
                <!-- Avatar Upload -->
                <div class="space-y-4" x-data="{ photoPreview: null }">
                    <label
                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">รูปประจำตัว</label>
                    <div class="relative group">
                        <div class="w-44 h-44 rounded-[2.5rem] bg-slate-50 border-4 border-white shadow-2xl overflow-hidden relative cursor-pointer group-hover:scale-105 transition-all duration-500"
                            onclick="document.getElementById('avatar').click()">

                            <template x-if="photoPreview">
                                <img :src="photoPreview" class="w-full h-full object-cover">
                            </template>

                            <template x-if="!photoPreview">
                                <div class="w-full h-full flex items-center justify-center bg-white overflow-hidden">
                                    @if ($user->avatar)
                                        <img src="{{ route('storage.file', ['path' => $user->avatar]) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="text-5xl font-black text-slate-200 uppercase tracking-tighter">
                                            {{ substr($user->name, 0, 1) }}</div>
                                    @endif
                                </div>
                            </template>

                            <div
                                class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all gap-2">
                                <i data-lucide="camera" class="w-8 h-8 text-white"></i>
                                <span
                                    class="text-[10px] font-black text-white uppercase tracking-widest">เปลี่ยนรูป</span>
                            </div>
                        </div>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*"
                            @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => photoPreview = e.target.result; reader.readAsDataURL(file); }">
                    </div>
                </div>

                <!-- Signature Upload -->
                <div class="space-y-4" x-data="{ sigPreview: null }">
                    <label
                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">ลายมือชื่อดิจิทัล</label>
                    <div class="relative group">
                        <div class="w-44 h-32 rounded-3xl bg-slate-50 border-4 border-white shadow-xl overflow-hidden relative cursor-pointer group-hover:scale-105 transition-all duration-500"
                            onclick="document.getElementById('signature').click()">

                            <template x-if="sigPreview">
                                <img :src="sigPreview" class="w-full h-full object-contain p-4">
                            </template>

                            <template x-if="!sigPreview">
                                <div class="w-full h-full flex items-center justify-center bg-white">
                                    @if ($user->signature)
                                        <img src="{{ route('storage.file', ['path' => $user->signature]) }}"
                                            class="w-full h-full object-contain p-4">
                                    @else
                                        <div class="flex flex-col items-center text-slate-300 gap-1">
                                            <i data-lucide="file-pen-line" class="w-8 h-8"></i>
                                            <span class="text-[9px] font-black uppercase">ยังไม่เพิ่ม</span>
                                        </div>
                                    @endif
                                </div>
                            </template>

                            <div
                                class="absolute inset-0 bg-brand-600/60 backdrop-blur-sm flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all gap-2">
                                <i data-lucide="upload-cloud" class="w-8 h-8 text-white"></i>
                                <span
                                    class="text-[10px] font-black text-white uppercase tracking-widest">อัปโหลดลายเซ็น</span>
                            </div>
                        </div>
                        <input type="file" id="signature" name="signature" class="hidden" accept="image/*"
                            @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => sigPreview = e.target.result; reader.readAsDataURL(file); }">
                    </div>
                </div>
            </div>

            <!-- Text Fields Section -->
            <div class="flex-1 w-full space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อ -
                            นามสกุล <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-800 transition-all"
                                placeholder="Enter Full Name">
                            <div
                                class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none text-slate-300">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </div>
                        </div>
                        @error('name') <p class="text-[10px] font-black text-rose-500 mt-1 ml-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-3">
                        <label
                            class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ที่อยู่อีเมล
                            <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-800 transition-all"
                                placeholder="email@example.com">
                            <div
                                class="absolute inset-y-0 right-0 pr-6 flex items-center pointer-events-none text-slate-300">
                                <i data-lucide="at-sign" class="w-5 h-5"></i>
                            </div>
                        </div>
                        @error('email') <p class="text-[10px] font-black text-rose-500 mt-1 ml-2">{{ $message }}</p>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                            <div
                                class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-2xl flex items-center justify-between">
                                <p class="text-[11px] font-black text-amber-700 uppercase tracking-widest">
                                    กรุณายืนยันตัวตนทางอีเมล</p>
                                <button form="send-verification"
                                    class="px-4 py-2 bg-amber-500 text-white text-[10px] font-black uppercase rounded-xl hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/30">ส่งใหม่</button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4 text-emerald-500" x-data="{ showStatus: false }"
                        x-init="@if(session('status') === 'profile-updated') showStatus = true; setTimeout(() => showStatus = false, 5000) @endif"
                        x-show="showStatus" x-transition>
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center border border-emerald-100">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-black uppercase tracking-[0.2em]">บันทึกข้อมูลเรียบร้อยแล้ว</span>
                    </div>

                    <button type="submit"
                        class="w-full sm:w-auto px-10 py-4 bg-brand-600 hover:bg-brand-700 text-white font-black text-lg rounded-[2rem] shadow-xl shadow-brand-500/20 active:scale-95 transition-all flex items-center justify-center gap-3 group">
                        <i data-lucide="save" class="w-6 h-6 group-hover:rotate-12 transition-transform"></i>
                        ยืนยันการเปลี่ยนแปลง
                    </button>
                </div>
            </div>
        </div>
    </form>
</section>
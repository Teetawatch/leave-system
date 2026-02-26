<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="space-y-2 relative group-input">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">รหัสผ่านปัจจุบัน</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                    <i data-lucide="unlock" class="w-[18px] h-[18px]"></i>
                </div>
                <input id="update_password_current_password" name="current_password" type="password" required autocomplete="current-password"
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-[15px] font-semibold text-slate-800 transition-all shadow-sm outline-none"
                    placeholder="ป้อนรหัสผ่านเดิม">
            </div>
            @error('current_password', 'updatePassword') 
                <p class="text-[10px] font-bold text-rose-500 mt-1.5 ml-1 flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i>{{ $message }}</p> 
            @enderror
        </div>

        <!-- New Password -->
        <div class="space-y-2 relative group-input">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">รหัสผ่านใหม่ <span class="text-rose-500">*</span></label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i data-lucide="key-round" class="w-[18px] h-[18px]"></i>
                </div>
                <input id="update_password_password" name="password" type="password" required autocomplete="new-password"
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-[15px] font-semibold text-slate-800 transition-all shadow-sm outline-none"
                    placeholder="อย่างน้อย 8 ตัวอักษร">
            </div>
            @error('password', 'updatePassword') 
                <p class="text-[10px] font-bold text-rose-500 mt-1.5 ml-1 flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i>{{ $message }}</p> 
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2 relative group-input mb-4">
            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">ยืนยันรหัสผ่านใหม่ <span class="text-rose-500">*</span></label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                    <i data-lucide="shield-check" class="w-[18px] h-[18px]"></i>
                </div>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                    class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-[15px] font-semibold text-slate-800 transition-all shadow-sm outline-none"
                    placeholder="ป้อนรหัสใหม่ซ้ำอีกครั้ง">
            </div>
            @error('password_confirmation', 'updatePassword') 
                <p class="text-[10px] font-bold text-rose-500 mt-1.5 ml-1 flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i>{{ $message }}</p> 
            @enderror
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-between gap-5 relative">
            <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50/80 px-4 py-2 backdrop-blur rounded-xl border border-emerald-100/60 shadow-sm" 
                x-data="{ show: false }" x-show="show" x-transition.opacity.duration.300ms x-init="@if(session('status') === 'password-updated') show = true; setTimeout(() => show = false, 4000) @endif" style="display: none;">
                <i data-lucide="check-circle-2" class="w-[18px] h-[18px]"></i>
                <span class="text-[11px] font-bold uppercase tracking-widest">รหัสผ่านถูกเปลี่ยนแปลงแล้ว</span>
            </div>

            <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-2xl shadow-lg shadow-slate-800/20 hover:shadow-slate-800/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 sm:ml-auto group outline-none focus:ring-4 focus:ring-slate-800/10">
                <i data-lucide="lock" class="w-4 h-4 text-amber-500 group-hover:rotate-12 transition-transform"></i>
                อัปเดตรหัสผ่าน
            </button>
        </div>
    </form>
</section>

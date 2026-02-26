<section class="space-y-6">
    <div class="p-5 bg-rose-50/50 border border-rose-100 rounded-2xl flex items-start sm:items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0 text-rose-500">
            <i data-lucide="info" class="w-5 h-5"></i>
        </div>
        <p class="text-[12px] sm:text-[13px] text-slate-600 font-medium leading-relaxed">
            เมื่อคุณลบบัญชี ข้อมูลที่เกี่ยวข้องทั้งหมดและการตั้งค่าของคุณจะถูกลบออกจากระบบอย่างถาวรโดยไม่สามารถกู้คืนได้
            หากมีข้อมูลสำคัญ โปรดสำรองข้อมูลไว้ก่อนดำเนินการ
        </p>
    </div>

    <button type="button" x-data x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="w-full sm:w-auto px-6 py-3.5 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-rose-600 font-bold text-sm rounded-2xl shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-2 group outline-none focus:ring-4 focus:ring-rose-500/10">
        <i data-lucide="trash-2" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform text-rose-500"></i>
        ลบบัญชีผู้ใช้งานถาวร
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 bg-white/95 backdrop-blur-xl rounded-[2rem] border border-white/60 shadow-2xl relative overflow-hidden">
            @csrf
            @method('delete')

            <!-- Decorative blur orb inside modal -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

            <div class="flex items-start gap-4 mb-6 relative z-10">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-inner">
                    <i data-lucide="alert-octagon" class="w-6 h-6"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">คุณแน่ใจหรือไม่ว่าจะลบบัญชี?</h2>
                    <p class="text-[13px] font-medium text-slate-500 mt-1.5 leading-relaxed">โปรดทราบว่าข้อมูลทั้งหมดจะถูกลบถาวร กรุณายืนยันการตัดสินใจโดยการป้อนรหัสผ่านเพื่อให้ระบบดำเนินการ</p>
                </div>
            </div>

            <div class="mt-8 space-y-2 relative group-input z-10 mb-4">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1 text-left">รหัสผ่านสำหรับยืนยัน</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-rose-500 transition-colors">
                        <i data-lucide="lock" class="w-[18px] h-[18px]"></i>
                    </div>
                    <input id="password" name="password" type="password" required autofocus
                        class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-slate-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 text-[15px] font-semibold text-slate-800 transition-all shadow-sm outline-none" 
                        placeholder="ป้อนรหัสผ่านปัจจุบันของคุณ">
                </div>
                @error('password', 'userDeletion') 
                    <p class="text-[10px] font-bold text-rose-500 mt-1.5 ml-1 flex items-center gap-1"><i data-lucide="info" class="w-3.5 h-3.5"></i>{{ $message }}</p> 
                @enderror
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 relative z-10 w-full">
                <button type="button" x-on:click="$dispatch('close')" class="w-full sm:w-auto px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-slate-300">
                    ยกเลิกการลบ
                </button>

                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-rose-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2 outline-none focus:ring-4 focus:ring-rose-500/20">
                    <i data-lucide="user-x" class="w-[18px] h-[18px]"></i>
                    ยืนยันลบบัญชีถาวร
                </button>
            </div>
        </form>
    </x-modal>
</section>

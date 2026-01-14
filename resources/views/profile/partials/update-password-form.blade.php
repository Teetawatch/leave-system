<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-slate-800">
            {{ __('เปลี่ยนรหัสผ่าน') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('เพื่อความปลอดภัย ควรใช้รหัสผ่านที่มีความยาวและคาดเดายาก') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" class="!text-slate-700 !font-bold" :value="__('รหัสผ่านปัจจุบัน')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full !rounded-xl !border-slate-300 focus:!border-brand-500 focus:!ring-brand-500" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" class="!text-slate-700 !font-bold" :value="__('รหัสผ่านใหม่')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full !rounded-xl !border-slate-300 focus:!border-brand-500 focus:!ring-brand-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" class="!text-slate-700 !font-bold" :value="__('ยืนยันรหัสผ่านใหม่')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full !rounded-xl !border-slate-300 focus:!border-brand-500 focus:!ring-brand-500" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-50">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all">
                {{ __('เปลี่ยนรหัสผ่าน') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 flex items-center gap-2 font-medium"
                >
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ __('เปลี่ยนรหัสผ่านเรียบร้อยแล้ว') }}
                </p>
            @endif
        </div>
    </form>
</section>

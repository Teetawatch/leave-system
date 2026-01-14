<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-col md:flex-row gap-8 items-start">
            <!-- Avatar Upload Section -->
            <div class="flex-shrink-0 group relative" x-data="{ photoName: null, photoPreview: null }">
                <!-- Current Profile Photo -->
                <div class="w-32 h-32 rounded-full border-4 border-slate-100 shadow-sm overflow-hidden bg-slate-50 relative">
                    <!-- Preview -->
                    <div x-show="photoPreview" style="display: none;">
                        <span class="block w-full h-full bg-cover bg-no-repeat bg-center"
                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>
                    
                    <!-- Current Image or Initials -->
                    <div x-show="!photoPreview" class="w-full h-full flex items-center justify-center bg-white">
                        @if ($user->avatar)
                            <img src="{{ route('storage.file', ['path' => $user->avatar]) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-slate-300">{{ substr($user->name, 0, 1) }}</span>
                        @endif
                    </div>

                    <!-- Upload Overlay -->
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                         onclick="document.getElementById('avatar').click()">
                        <i data-lucide="camera" class="w-5 h-5 text-white text-xl"></i>
                    </div>
                </div>

                <!-- Hidden File Input -->
                <input type="file" id="avatar" name="avatar" class="hidden"
                       x-ref="photo"
                       onchange="
                            const file = this.files[0];
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                photoPreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                       ">
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                <p class="text-xs text-center text-slate-400 mt-2">คลิกเพื่อเปลี่ยนรูป</p>
            </div>

            <!-- Signature Upload Section -->
            <div class="flex-shrink-0 group relative" x-data="{ sigName: null, sigPreview: null }">
                <p class="text-sm font-bold text-slate-700 mb-2 text-center">ลายเซ็น (สำหรับอนุมัติ)</p>
                <!-- Current Signature -->
                <div class="w-32 h-32 rounded-2xl border-4 border-slate-100 shadow-sm overflow-hidden bg-slate-50 relative">
                    <!-- Preview -->
                    <div x-show="sigPreview" style="display: none;">
                        <span class="block w-full h-full bg-contain bg-no-repeat bg-center"
                              x-bind:style="'background-image: url(\'' + sigPreview + '\');'">
                        </span>
                    </div>
                    
                    <!-- Current Image or Placeholder -->
                    <div x-show="!sigPreview" class="w-full h-full flex items-center justify-center bg-white">
                        @if ($user->signature)
                            <img src="{{ route('storage.file', ['path' => $user->signature]) }}" alt="Signature" class="w-full h-full object-contain p-2">
                        @else
                            <i data-lucide="file-pen" class="w-3.5 h-3.5 text-4xl text-slate-300"></i>
                        @endif
                    </div>

                    <!-- Upload Overlay -->
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                         onclick="document.getElementById('signature').click()">
                        <i data-lucide="upload" class="w-4 h-4 text-white text-xl"></i>
                    </div>
                </div>

                <!-- Hidden File Input -->
                <input type="file" id="signature" name="signature" class="hidden"
                       x-ref="sig"
                       @change="
                            const file = $refs.sig.files[0];
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                sigPreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                       ">
                <x-input-error class="mt-2" :messages="$errors->get('signature')" />
                <p class="text-xs text-center text-slate-400 mt-2">คลิกเพื่ออัปโหลดลายเซ็น</p>
            </div>

            <!-- Fields -->
            <div class="flex-1 space-y-6 w-full">
                <div>
                    <x-input-label for="name" class="!text-slate-700 !font-bold" :value="__('ชื่อ - นามสกุล')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full !rounded-xl !border-slate-300 focus:!border-brand-500 focus:!ring-brand-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" class="!text-slate-700 !font-bold" :value="__('อีเมล')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full !rounded-xl !border-slate-300 focus:!border-brand-500 focus:!ring-brand-500" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-2">
                            <p class="text-sm text-amber-600 bg-amber-50 px-3 py-2 rounded-lg inline-block">
                                {{ __('อีเมลของคุณยังไม่ได้รับการยืนยัน') }}
                                <button form="send-verification" class="underline hover:text-amber-900 ml-1">
                                    {{ __('คลิกเพื่อส่งลิงก์ยืนยันอีกครั้ง') }}
                                </button>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-50">
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all">
                {{ __('บันทึกการเปลี่ยนแปลง') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 flex items-center gap-2 font-medium"
                >
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ __('บันทึกข้อมูลเรียบร้อยแล้ว') }}
                </p>
            @endif
        </div>
    </form>
</section>

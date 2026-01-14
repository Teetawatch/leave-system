<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden font-kanit">
        
        <!-- Animated Background Mesh -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-brand-200/30 rounded-full blur-[120px] mix-blend-multiply animate-blob"></div>
            <div class="absolute top-[20%] right-[-10%] w-[60%] h-[60%] bg-blue-200/30 rounded-full blur-[120px] mix-blend-multiply animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-[60%] h-[60%] bg-indigo-200/30 rounded-full blur-[120px] mix-blend-multiply animate-blob animation-delay-4000"></div>
        </div>

        <!-- content -->
        <div class="relative z-10 w-full max-w-[480px] p-6">
            <div class="bg-white/90 backdrop-blur-2xl rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white/80 overflow-hidden relative">
                
                <!-- Top Decor -->
                <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-brand-500 via-blue-500 to-indigo-500"></div>

                <div class="p-10">
                    <!-- Brand / Logo -->
                    <div class="text-center mb-10">
                        <div class="relative inline-block mb-6">
                             <div class="absolute inset-0 bg-brand-500 blur-xl opacity-20 rounded-full"></div>
                             <img src="{{ asset('images/logonavy.png') }}" alt="Logo" 
                                  class="relative w-28 h-28 object-contain drop-shadow-xl transform hover:scale-105 hover:rotate-3 transition-all duration-300">
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-800 mb-2 tracking-tight">ยินดีต้อนรับ</h2>
                        <p class="text-slate-500 font-medium">ระบบบริหารจัดการงานธุรการด้านกำลังพล <br>โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div class="group">
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2 ml-1">อีเมล</label>
                            <div class="relative transition-all duration-300">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="mail" class="w-5 h-5"></i>
                                </span>
                                <input id="email" 
                                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all duration-200 text-base font-medium" 
                                    type="email" 
                                    name="email" 
                                    :value="old('email')" 
                                    required 
                                    autofocus 
                                    autocomplete="username" 
                                    placeholder="กรอกอีเมลของท่าน" 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm ml-1" />
                        </div>

                        <!-- Password -->
                        <div class="group">
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2 ml-1">รหัสผ่าน</label>
                            <div class="relative transition-all duration-300">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="lock" class="w-5 h-5"></i>
                                </span>
                                <input id="password" 
                                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all duration-200 text-base font-medium" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password" 
                                    placeholder="••••••••" 
                                />
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm ml-1" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-2">
                            <label for="remember_me" class="inline-flex items-center group cursor-pointer select-none">
                                <div class="relative flex items-center">
                                    <input id="remember_me" type="checkbox" 
                                        class="peer h-5 w-5 rounded-md border-slate-300 text-brand-600 focus:ring-brand-500 transition-all cursor-pointer" 
                                        name="remember">
                                </div>
                                <span class="ml-2.5 text-sm text-slate-500 font-medium group-hover:text-slate-700 transition-colors">จดจำฉันไว้</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors hover:underline" href="{{ route('password.request') }}">
                                    ลืมรหัสผ่าน?
                                </a>
                            @endif
                        </div>

                        <button type="submit" 
                            class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-2xl shadow-xl shadow-brand-500/20 text-base font-bold text-white bg-slate-900 hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-brand-500/30">
                            เข้าสู่ระบบ
                            <i data-lucide="log-in" class="w-5 h-5 ml-2"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="px-8 py-6 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-1">
                    <span class="text-sm text-slate-500 font-medium">เป็นข้าราชการใหม่?</span>
                    <a href="{{ route('employee.register') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors hover:underline">
                        ลงทะเบียนที่นี่
                    </a>
                </div>
            </div>

            <!-- Footer Credit -->
            <div class="text-center mt-8">
                <p class="text-xs text-slate-400 font-medium tracking-wide">
                    &copy; {{ date('Y') }} ระบบบริหารจัดการงานธุรการด้านกำลังพล ออกแบบและพัฒนาระบบโดย จ.ท.ธีร์ธวัช พิพัฒน์เดชธน All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <!-- Custom Style for background animation -->
    <style>
        .font-kanit {
            font-family: 'Kanit', sans-serif;
        }
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</x-guest-layout>

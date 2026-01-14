<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden py-12">
        
       <!-- Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-brand-100 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-green-100 rounded-full blur-3xl opacity-50"></div>
        </div>

        <!-- Card -->
        <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <!-- Header -->
             <div class="bg-gradient-to-r from-slate-800 to-slate-700 p-8 text-center">
                <div class="w-16 h-16 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                    <i data-lucide="user-plus" class="w-8 h-8 text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">ลงทะเบียนพนักงาน</h2>
                <p class="text-slate-300 text-sm">สร้างบัญชีผู้ใช้งานใหม่</p>
            </div>

            <div class="p-8">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </span>
                            <input id="name" class="block w-full pl-10 pr-3 py-2.5 rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" 
                                   type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="ระบุชื่อจริง นามสกุลจริง" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                         <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </span>
                            <input id="email" class="block w-full pl-10 pr-3 py-2.5 rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" 
                                   type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="example@company.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                         <label for="password" class="block text-sm font-medium text-gray-700 mb-1">รหัสผ่าน</label>
                         <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </span>
                             <input id="password" class="block w-full pl-10 pr-3 py-2.5 rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" 
                                    type="password" name="password" required autocomplete="new-password" placeholder="อย่างน้อย 8 ตัวอักษร" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">ยืนยันรหัสผ่าน</label>
                         <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                            </span>
                            <input id="password_confirmation" class="block w-full pl-10 pr-3 py-2.5 rounded-lg border-gray-300 text-gray-900 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm" 
                                   type="password" name="password_confirmation" required autocomplete="new-password" placeholder="ระบุรหัสผ่านอีกครั้ง" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end">
                        <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('login') }}">
                            มีบัญชีอยู่แล้ว?
                        </a>

                        <button type="submit" class="inline-flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                            ลงทะเบียน
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>

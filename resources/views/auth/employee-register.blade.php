<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 relative overflow-hidden font-kanit">
        
        <!-- Animated Background Mesh -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-emerald-200/30 rounded-full blur-[120px] mix-blend-multiply animate-blob"></div>
            <div class="absolute top-[20%] right-[-10%] w-[60%] h-[60%] bg-teal-200/30 rounded-full blur-[120px] mix-blend-multiply animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-10%] left-[20%] w-[60%] h-[60%] bg-cyan-200/30 rounded-full blur-[120px] mix-blend-multiply animate-blob animation-delay-4000"></div>
        </div>

        <!-- content -->
        <div class="relative z-10 w-full max-w-[520px] p-6">
            <div class="bg-white/90 backdrop-blur-2xl rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-white/80 overflow-hidden relative">
                
                <!-- Top Decor -->
                <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500"></div>

                <div class="p-10">
                    <!-- Brand / Logo -->
                    <div class="text-center mb-8">
                        <div class="relative inline-block">
                             <div class="absolute inset-0 bg-emerald-500 blur-xl opacity-20 rounded-full"></div>
                             <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-xl shadow-emerald-500/20 mb-4 transform hover:scale-105 hover:rotate-3 transition-all duration-300 group">
                                <i data-lucide="user" class="w-5 h-5 text-4xl text-white drop-shadow-md group-hover:scale-110 transition-transform"></i>
                             </div>
                        </div>
                        <h2 class="text-2xl font-extrabold text-slate-800 mb-2 tracking-tight">ลงทะเบียนเข้าสู่ระบบ</h2>
                        <p class="text-slate-500 font-medium text-sm">ระบบบริหารจัดการงานธุรการด้านกำลังพล  <br>โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>
                    </div>

                    <!-- Session Status -->
                    @if(session('status'))
                        <div class="mb-6 bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-100 flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-100">
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employee.register.store') }}" class="space-y-5" x-data="employeeSearch()">
                        @csrf

                        <!-- Employee Search -->
                        <div class="group">
                            <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">เลือกชื่อของคุณ <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                    <i data-lucide="user" class="w-4 h-4 text-lg"></i>
                                </span>
                                <input type="text" 
                                    x-model="searchQuery"
                                    @input.debounce.300ms="searchEmployees()"
                                    @focus="showDropdown = true"
                                    @click.away="showDropdown = false"
                                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-base font-medium" 
                                    placeholder="กรุณากรอกชื่อ..."
                                    autocomplete="off"
                                />
                                
                                <!-- Dropdown Results -->
                                <div x-show="showDropdown && results.length > 0" 
                                     x-transition
                                     class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 max-h-60 overflow-y-auto">
                                    <template x-for="emp in results" :key="emp.id">
                                        <button type="button"
                                            @click="selectEmployee(emp)"
                                            class="w-full px-4 py-3 text-left hover:bg-emerald-50 transition-colors flex items-center gap-3 border-b border-slate-50 last:border-0">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold flex-shrink-0">
                                                <span x-text="emp.name.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800" x-text="emp.rank + ' ' + emp.name"></div>
                                                <div class="text-xs text-slate-400" x-text="emp.department || 'ไม่ระบุ'"></div>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <!-- No Results -->
                                <div x-show="showDropdown && searchQuery.length >= 2 && results.length === 0 && !loading" 
                                     class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 p-4 text-center">
                                    <span class="text-slate-400">ไม่พบข้อมูล หรือชื่อนี้ได้ลงทะเบียนแล้ว</span>
                                </div>

                                <!-- Loading -->
                                <div x-show="loading" class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-xl border border-slate-100 p-4 text-center">
                                    <i data-lucide="loader" class="w-4 h-4 animate-spin text-emerald-500 mr-2"></i>
                                    <span class="text-slate-400">กำลังโหลด...</span>
                                </div>
                            </div>
                            <input type="hidden" name="employee_id" x-model="selectedId" required>
                            
                            <!-- Selected Employee Info -->
                            <div x-show="selectedEmployee" x-transition class="mt-3 p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-emerald-800" x-text="selectedEmployee?.rank + ' ' + selectedEmployee?.name"></div>
                                        <div class="text-xs text-emerald-600" x-text="(selectedEmployee?.department || '-') + ' / ' + (selectedEmployee?.position || '-')"></div>
                                    </div>
                                    <button type="button" @click="clearSelection()" class="ml-auto text-emerald-400 hover:text-emerald-600">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="group">
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-2 ml-1">อีเมล (อีเมลที่ใช้ลงทะเบียน) <span class="text-red-500">*</span></label>
                            <div class="relative transition-all duration-300">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                    <i data-lucide="mail" class="w-5 h-5 text-lg"></i>
                                </span>
                                <input id="email" 
                                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-base font-medium" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required 
                                    autocomplete="email" 
                                    placeholder="your.email@example.com" 
                                />
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="group">
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2 ml-1">รหัสผ่าน <span class="text-red-500">*</span></label>
                            <div class="relative transition-all duration-300">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                    <i data-lucide="lock" class="w-5 h-5 text-lg"></i>
                                </span>
                                <input id="password" 
                                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-base font-medium" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password" 
                                    placeholder="กรอกรหัสผ่าน 8 ตัวอักษร" 
                                />
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="group">
                            <label for="password_confirmation" class="block text-sm font-bold text-slate-700 mb-2 ml-1">ยืนยันรหัสผ่าน <span class="text-red-500">*</span></label>
                            <div class="relative transition-all duration-300">
                                <span class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                                    <i data-lucide="lock" class="w-5 h-5 text-lg"></i>
                                </span>
                                <input id="password_confirmation" 
                                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-slate-200 bg-slate-50/50 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-base font-medium" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password" 
                                    placeholder="กรอกรหัสผ่านอีกครั้ง" 
                                />
                            </div>
                        </div>

                        <button type="submit" 
                            class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-2xl shadow-xl shadow-emerald-500/20 text-base font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!selectedId">
                            ลงทะเบียน
                            <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="px-8 py-6 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-1">
                    <span class="text-sm text-slate-500 font-medium">มีบัญชีแล้ว ?</span>
                    <a href="{{ route('login') }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors hover:underline">
                        เข้าสู่ระบบ
                    </a>
                </div>
            </div>

            <!-- Footer Credit -->
            <div class="text-center mt-8">
                <p class="text-xs text-slate-400 font-medium tracking-wide">
                    &copy; {{ date('Y') }} Leave Management System. ออกแบบและพัฒนาระบบโดย จ.ท.ธีร์ธวัช พิพัฒน์เดช All rights reserved.
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

    <script>
        function employeeSearch() {
            return {
                searchQuery: '',
                results: [],
                selectedId: null,
                selectedEmployee: null,
                showDropdown: false,
                loading: false,

                async searchEmployees() {
                    if (this.searchQuery.length < 2) {
                        this.results = [];
                        return;
                    }

                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('api.employees.search') }}?q=${encodeURIComponent(this.searchQuery)}`);
                        this.results = await response.json();
                    } catch (error) {
                        console.error('Search error:', error);
                        this.results = [];
                    }
                    this.loading = false;
                },

                selectEmployee(emp) {
                    this.selectedId = emp.id;
                    this.selectedEmployee = emp;
                    this.searchQuery = emp.rank + ' ' + emp.name;
                    this.showDropdown = false;
                    this.results = [];
                },

                clearSelection() {
                    this.selectedId = null;
                    this.selectedEmployee = null;
                    this.searchQuery = '';
                }
            }
        }
    </script>
</x-guest-layout>

<x-app-layout>
    @section('title', 'จัดการข้าราชการ')

    <div class="max-w-[95rem] mx-auto py-8 sm:px-6 lg:px-8" 
         x-data="{ 
            viewMode: localStorage.getItem('employeeViewMode') || 'grid',
            toggleView(mode) {
                this.viewMode = mode;
                localStorage.setItem('employeeViewMode', mode);
            },
            showOfficialDutyModal: false,
            selectedEmployee: { id: '', name: '' },
            openOfficialDutyModal(id, name) {
                this.selectedEmployee = { id: id, name: name };
                this.showOfficialDutyModal = true;
            }
         }">
        
        <!-- Alerts -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="mb-6 rounded-3xl bg-emerald-50 p-4 border border-emerald-100 shadow-xl shadow-emerald-500/10 flex items-center gap-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-400 rounded-full blur-3xl opacity-10 -mr-16 -mt-16"></div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center flex-shrink-0 text-emerald-600 shadow-inner rotate-3">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 z-10">
                    <h4 class="font-black text-emerald-800">ดำเนินการเรียบร้อย</h4>
                    <p class="text-sm font-bold text-emerald-600/80">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="z-10 bg-white/50 hover:bg-white rounded-xl p-2.5 text-emerald-500 transition-all hover:rotate-90 active:scale-95 shadow-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="mb-6 rounded-3xl bg-rose-50 p-4 border border-rose-100 shadow-xl shadow-rose-500/10 flex items-center gap-4 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-400 rounded-full blur-3xl opacity-10 -mr-16 -mt-16"></div>
                <div class="w-12 h-12 rounded-2xl bg-rose-100 flex items-center justify-center flex-shrink-0 text-rose-600 shadow-inner rotate-3">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 z-10">
                    <h4 class="font-black text-rose-800">เกิดข้อผิดพลาด</h4>
                    @if(session('error'))
                        <p class="text-sm font-bold text-rose-600/80">{{ session('error') }}</p>
                    @endif
                    @if($errors->any())
                        <ul class="list-disc list-inside text-sm font-bold text-rose-600/80 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button @click="show = false" class="z-10 bg-white/50 hover:bg-white rounded-xl p-2.5 text-rose-500 transition-all hover:rotate-90 active:scale-95 shadow-sm">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
        
        <!-- Header & Top Actions -->
        <div class="mb-10 space-y-8">
            <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                <div class="relative">
                    <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-brand-500 rounded-full shadow-[0_0_15px_rgba(var(--color-brand-500),0.5)]"></div>
                    <h1 class="text-4xl font-black text-slate-800 tracking-tight">บุคลากรทั้งหมด</h1>
                    <p class="text-slate-500 mt-1 text-lg">บริหารจัดการข้อมูลรายชื่อ ยศ ตำแหน่ง และสิทธิ์การใช้งานระบบ</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100">
                         <a href="{{ route('employees.export') }}" class="inline-flex items-center px-4 py-2 text-slate-600 font-black text-sm hover:text-brand-600 transition-colors gap-2 group">
                            <i data-lucide="download" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i>
                            Export
                        </a>
                        <div class="w-px h-4 bg-slate-200 mx-2"></div>
                        <a href="{{ route('employees.import') }}" class="inline-flex items-center px-4 py-2 text-slate-600 font-black text-sm hover:text-emerald-600 transition-colors gap-2 group">
                            <i data-lucide="upload" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i>
                            Import
                        </a>
                    </div>

                    <a href="{{ route('employees.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 text-white font-black rounded-2xl shadow-xl shadow-brand-500/20 transition-all hover:-translate-y-1 active:scale-95 gap-2">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        เพิ่มข้าราชการใหม่
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Total Employees -->
                <div class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50 group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">ยอดรวมกำลังพล</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-indigo-600 transition-colors">
                                {{ \App\Models\User::count() }}
                            </h3>
                            <p class="text-[10px] text-slate-400 mt-2 font-bold flex items-center gap-1">
                                <span class="text-emerald-500">Active</span> ใช้งานอยู่หน้าเว็บ
                            </p>
                        </div>
                        <div class="w-16 h-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner group-hover:rotate-6 transition-transform">
                            <i data-lucide="users-round" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>

                <!-- Departments -->
                <div class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50 group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">แผนกทั้งหมด</p>
                            <h3 class="text-4xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors">
                                {{ $departments->count() }}
                            </h3>
                            <p class="text-[10px] text-slate-400 mt-2 font-bold flex items-center gap-1">
                                ตามที่กำหนดใน <span class="text-brand-500">โครงสร้างองค์กร</span>
                            </p>
                        </div>
                        <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner group-hover:-rotate-6 transition-transform">
                            <i data-lucide="building-2" class="w-8 h-8"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                @php
                    $pendingCount = \App\Models\User::where('is_registered', true)->where('registration_status', 'pending')->count();
                @endphp
                <a href="{{ route('employees.pending-registrations') }}" class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50 group hover:-translate-y-1 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 {{ $pendingCount > 0 ? 'bg-rose-50' : 'bg-slate-50' }} rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-500"></div>
                    <div class="relative z-10 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">ค้างอนุมัติลงทะเบียน</p>
                            <h3 class="text-4xl font-black {{ $pendingCount > 0 ? 'text-rose-600 animate-pulse' : 'text-slate-800' }} group-hover:scale-105 transition-transform origin-left">
                                {{ $pendingCount }}
                            </h3>
                            <p class="text-[10px] text-slate-400 mt-2 font-bold flex items-center gap-1">
                                @if($pendingCount > 0)
                                    มีคำขอลาใหม่ <span class="text-rose-500">รอการตรวจสอบ</span>
                                @else
                                    ไม่มีรายการค้าง <span class="text-emerald-500">เรียบร้อยดี</span>
                                @endif
                            </p>
                        </div>
                        <div class="w-16 h-16 rounded-3xl {{ $pendingCount > 0 ? 'bg-rose-50 text-rose-600 shadow-rose-100' : 'bg-slate-50 text-slate-400' }} flex items-center justify-center shadow-inner group-hover:rotate-12 transition-transform">
                            <i data-lucide="user-plus" class="w-8 h-8"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/40 border border-white p-4 mb-8 flex flex-col lg:flex-row items-center justify-between gap-6 sticky top-24 z-30 transition-all hover:shadow-2xl">
            <!-- Search & Filter -->
            <form action="{{ route('employees.index') }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-4 flex-1">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-12 pr-4 py-3.5 bg-slate-100/50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl text-sm font-bold transition-all text-slate-700" 
                        placeholder="พิมพ์เพื่อค้นหาชื่อ, อีเมล, หรือตำแหน่ง...">
                </div>

                <div class="relative min-w-[240px] group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                    </div>
                    <select name="department" onchange="this.form.submit()" class="block w-full pl-11 pr-10 py-3.5 bg-slate-100/50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl text-sm font-bold appearance-none cursor-pointer transition-all text-slate-700">
                        <option value="">ทุกแผนก/ฝ่าย</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </form>

            <!-- View Toggle & Bulk Action -->
            <div class="flex items-center gap-4 w-full lg:w-auto justify-between lg:justify-end">
                <button id="bulk-delete-btn" class="hidden items-center px-5 py-3.5 bg-rose-50 text-rose-600 hover:bg-rose-100 font-black text-sm rounded-2xl transition-all shadow-sm border border-rose-100 active:scale-95">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    ลบที่เลือก (<span id="selected-count">0</span>)
                </button>

                <div class="flex bg-slate-100 p-1.5 rounded-[1.25rem]">
                    <button @click="toggleView('grid')" :class="viewMode === 'grid' ? 'bg-white text-brand-600 shadow-md scale-105' : 'text-slate-500 hover:text-slate-800'" class="p-2.5 rounded-xl transition-all duration-300">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    </button>
                    <button @click="toggleView('list')" :class="viewMode === 'list' ? 'bg-white text-brand-600 shadow-md scale-105' : 'text-slate-500 hover:text-slate-800'" class="p-2.5 rounded-xl transition-all duration-300">
                        <i data-lucide="list-tree" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="min-h-[500px]">
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8" x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
                @foreach($employees as $emp)
                    <div class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-50 hover:shadow-2xl hover:border-brand-200 transition-all duration-500 group relative flex flex-col h-full overflow-hidden">
                        
                        <!-- Premium Background Pattern (Subtle) -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-brand-50 transition-colors duration-500"></div>

                        <!-- Selection Checkbox (Absolute) -->
                        <div class="absolute top-5 right-5 z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                            <input type="checkbox" name="selected_users[]" value="{{ $emp->id }}" class="user-checkbox rounded-lg border-slate-300 text-brand-600 focus:ring-0 w-6 h-6 cursor-pointer shadow-sm transition-all hover:scale-110">
                        </div>

                        <div class="relative flex flex-col items-center flex-1">
                            <!-- Avatar with Ring -->
                            <div class="relative mb-4 group-hover:-translate-y-2 transition-transform duration-500">
                                <div class="absolute -inset-1 rounded-[2rem] bg-gradient-to-br from-brand-500 to-indigo-500 blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                                 @if(isset($emp->avatar) && $emp->avatar)
                                    <img class="relative h-24 w-24 rounded-[1.75rem] object-cover ring-4 ring-white shadow-xl" src="{{ asset('storage/' . $emp->avatar) }}" alt="">
                                @else
                                    <div class="relative h-24 w-24 rounded-[1.75rem] bg-gradient-to-br from-brand-50 to-indigo-50 text-brand-600 flex items-center justify-center text-3xl font-black ring-4 ring-white shadow-xl">
                                        {{ substr($emp->name, 0, 1) }}
                                    </div>
                                @endif
                                <!-- Role Badge (Mini) -->
                                <div class="absolute -bottom-2 -right-2 p-1 bg-white rounded-xl shadow-lg border border-slate-50">
                                    <div class="w-7 h-7 rounded-lg {{ $emp->role === 'admin' ? 'bg-rose-50 text-rose-500' : 'bg-brand-50 text-brand-500' }} flex items-center justify-center">
                                        <i data-lucide="{{ $emp->role === 'admin' ? 'shield-check' : 'user' }}" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Name & Position -->
                            <div class="text-center w-full px-2">
                                <h3 class="text-lg font-black text-slate-800 line-clamp-1 group-hover:text-brand-600 transition-colors duration-300">
                                    {{ $emp->rank }}{{ $emp->name }}
                                </h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 mb-3 truncate">{{ $emp->position ?? 'ข้าราชการ/เจ้าหน้าที่' }}</p>

                                <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                                     @php
                                        $roleLabel = match ($emp->role) {
                                            'admin' => 'Admin',
                                            'director' => 'ผอ.',
                                            'deputy_director' => 'รอง ผอ.',
                                            'department_head' => 'หน. แผนก',
                                            'employee' => 'User',
                                            default => ucfirst($emp->role)
                                        };
                                        $roleBg = match ($emp->role) {
                                            'admin' => 'bg-rose-50 text-rose-600',
                                            'director' => 'bg-purple-50 text-purple-600',
                                            'deputy_director' => 'bg-indigo-50 text-indigo-600',
                                            'department_head' => 'bg-amber-50 text-amber-600',
                                            default => 'bg-slate-100 text-slate-600'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $roleBg }}">
                                        {{ $roleLabel }}
                                    </span>
                                    @if($emp->department)
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-50 text-slate-500 border border-slate-100 line-clamp-1 max-w-[120px]">
                                            {{ $emp->department }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                            <a href="mailto:{{ $emp->email }}" class="flex items-center gap-2 text-slate-400 hover:text-brand-600 transition-colors group/mail">
                                <div class="p-2 rounded-lg bg-slate-50 group-hover/mail:bg-brand-50 transition-colors">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <span class="text-[11px] font-black tracking-tight truncate max-w-[100px]">{{ $emp->email }}</span>
                            </a>

                            <div class="flex items-center gap-2">
                                <button @click="openOfficialDutyModal('{{ $emp->id }}', '{{ $emp->name }}')" 
                                        class="p-2.5 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all active:scale-90" title="บันทึกการไปราชการ">
                                    <i data-lucide="plane" class="w-4 h-4"></i>
                                </button>
                                <a href="{{ route('employees.edit', $emp->id) }}" 
                                   class="p-2.5 rounded-xl text-slate-400 hover:text-brand-600 hover:bg-brand-50 transition-all active:scale-90 shadow-sm border border-slate-50 bg-white">
                                    <i data-lucide="settings-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden transition-all" x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 scale-[0.98]" x-transition:enter-end="opacity-100 scale-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th scope="col" class="px-8 py-6 text-left">
                                    <input type="checkbox" id="select-all" class="rounded-lg border-slate-300 text-brand-600 focus:ring-0 w-5 h-5 cursor-pointer shadow-sm">
                                </th>
                                <th scope="col" class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ข้อมูลข้าราชการ</th>
                                <th scope="col" class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ยศ - ตำแหน่ง</th>
                                <th scope="col" class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">บทบาท/สถานะ</th>
                                <th scope="col" class="px-6 py-6 text-left text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">ผู้อนุมัติ (Step 1)</th>
                                <th scope="col" class="px-8 py-6 text-right text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($employees as $emp)
                                <tr class="hover:bg-brand-50/30 transition-all duration-300 group">
                                    <td class="px-8 py-5">
                                        <input type="checkbox" name="selected_users[]" value="{{ $emp->id }}" class="user-checkbox rounded-lg border-slate-300 text-brand-600 focus:ring-0 w-5 h-5 cursor-pointer shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                @if(isset($emp->avatar) && $emp->avatar)
                                                    <img class="h-12 w-12 rounded-2xl object-cover ring-2 ring-white shadow-md bg-white" src="{{ asset('storage/' . $emp->avatar) }}" alt="">
                                                @else
                                                    <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-brand-50 to-indigo-50 text-brand-600 flex items-center justify-center font-black text-sm ring-2 ring-white shadow-md">
                                                        {{ substr($emp->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 ring-2 ring-white"></div>
                                            </div>
                                            <div>
                                                <div class="text-sm font-black text-slate-800 group-hover:text-brand-600 transition-colors">
                                                    {{ $emp->name }}
                                                </div>
                                                <div class="text-[11px] font-bold text-slate-400 mt-0.5 tracking-tight">{{ $emp->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-700 leading-none mb-1">{{ $emp->rank ?? '-' }}</span>
                                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $emp->position ?? 'ไม่ได้ระบุตำแหน่ง' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @php
                                            $roleBg = match ($emp->role) {
                                                'admin' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                'director' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                'deputy_director' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                'department_head' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                default => 'bg-slate-50 text-slate-500 border-slate-100'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $roleBg }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($emp->supervisor)
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 font-black shadow-inner">
                                                    {{ substr($emp->supervisor->name, 0, 1) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-[11px] font-black text-slate-700 leading-none">{{ $emp->supervisor->name }}</span>
                                                    <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase">Approver 1</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-[11px] font-bold text-slate-300 italic flex items-center gap-1">
                                                <i data-lucide="user-x" class="w-3 h-3"></i> ยังไม่มีสายงาน
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2 translate-x-2 group-hover:translate-x-0 transition-transform">
                                            <button @click="openOfficialDutyModal('{{ $emp->id }}', '{{ $emp->name }}')" 
                                                    class="p-2.5 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all active:scale-90" title="บันทึกการไปราชการ">
                                                <i data-lucide="plane" class="w-4 h-4"></i>
                                            </button>
                                            <a href="{{ route('employees.edit', $emp->id) }}" 
                                               class="p-2.5 rounded-xl text-slate-400 hover:text-brand-600 hover:bg-brand-50 transition-all active:scale-90 bg-white shadow-sm border border-slate-100 group-hover:border-brand-100">
                                                <i data-lucide="settings-2" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-10 px-4">
                {{ $employees->links() }}
            </div>
            
            <!-- Empty State -->
            @if($employees->count() === 0)
                <div class="text-center py-20 bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 mt-8 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                    <div class="relative z-10">
                        <div class="mx-auto w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mb-6 shadow-inner rotate-12">
                            <i data-lucide="search-x" class="w-12 h-12 text-slate-300"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">ไม่พบข้อมูลที่ต้องการ</h3>
                        <p class="text-slate-500 mt-2 font-bold max-w-sm mx-auto">ระบบไม่พบข้าราชการตามเงื่อนไขที่คุณระบุ ลองเปลี่ยนคำค้นหาหรือล้างตัวกรองเพื่อเริ่มค้นหาใหม่อีกครั้ง</p>
                        <div class="mt-8 flex items-center justify-center gap-4">
                            <a href="{{ route('employees.index') }}" class="px-8 py-3.5 bg-brand-50 text-brand-600 hover:bg-brand-100 font-black rounded-2xl transition-all active:scale-95 flex items-center gap-2 shadow-sm">
                                <i data-lucide="refresh-ccw" class="w-4 h-4"></i> รีเซ็ตตัวกรอง
                            </a>
                            <a href="{{ route('employees.create') }}" class="px-8 py-3.5 bg-slate-800 text-white hover:bg-slate-900 font-black rounded-2xl transition-all active:scale-95 flex items-center gap-2 shadow-lg">
                                <i data-lucide="plus" class="w-4 h-4"></i> เพิ่มใหม่ทันที
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>


    <!-- Scripts for Bulk Action -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            
            function getCheckboxes() {
                return document.querySelectorAll('input[name="selected_users[]"]');
            }

            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCountSpan = document.getElementById('selected-count');

            function updateBulkDeleteBtn() {
                const checkboxes = getCheckboxes();
                const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                selectedCountSpan.textContent = checkedCount;
                
                if (checkedCount > 0) {
                    bulkDeleteBtn.classList.remove('hidden');
                    bulkDeleteBtn.classList.add('inline-flex');
                    bulkDeleteBtn.classList.add('animate-in', 'fade-in', 'slide-in-from-bottom-2');
                } else {
                    bulkDeleteBtn.classList.add('hidden');
                    bulkDeleteBtn.classList.remove('inline-flex');
                }
            }

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    const checkboxes = getCheckboxes();
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkDeleteBtn();
                });
            }

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('user-checkbox')) {
                    const checkboxes = getCheckboxes();
                    if (!e.target.checked && selectAll) {
                        selectAll.checked = false;
                    } 
                    updateBulkDeleteBtn();
                }
            });

            bulkDeleteBtn.addEventListener('click', async function() {
                if (!confirm('ยืนยันการลบข้าราชการที่เลือกทั้งหมด? การกระทำนี้สำคัญมากและไม่สามารถย้อนกลับได้')) {
                    return;
                }

                const checkboxes = getCheckboxes();
                const selectedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
                
                if (selectedIds.length === 0) return;

                try {
                    const response = await fetch('{{ route("employees.bulk-destroy") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่ภายหลัง');
                }
            });
        });
    </script>

    <!-- Official Duty Modal (Polished) -->
    <div x-show="showOfficialDutyModal" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-md" @click="showOfficialDutyModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-white"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-[0.9] rotate-3"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100 rotate-0">
                
                <!-- Modal Header -->
                <div class="px-8 py-8 bg-gradient-to-br from-slate-50 to-white border-b border-slate-100 relative">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner rotate-6 group">
                                <i data-lucide="plane" class="w-7 h-7 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 leading-tight tracking-tight">บันทึกการไปราชการ</h3>
                                <p class="text-xs text-slate-400 font-black uppercase tracking-[0.2em] mt-1" x-text="selectedEmployee.name"></p>
                            </div>
                        </div>
                        <button @click="showOfficialDutyModal = false" class="p-2.5 bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-500 transition-all hover:rotate-90 active:scale-95">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>

                <form :action="'{{ route('employees.index') }}/' + selectedEmployee.id + '/official-duty'" method="POST" enctype="multipart/form-data" class="p-8 space-y-6 relative z-10">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">วันที่เริ่มต้น</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-500 transition-colors">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <input type="date" name="start_date" required 
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl text-sm font-bold text-slate-700 transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">วันที่สิ้นสุด</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-500 transition-colors">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                </div>
                                <input type="date" name="end_date" required 
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl text-sm font-bold text-slate-700 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">สถานที่ไปราชการ (จังหวัด)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-500 transition-colors">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </div>
                            <input type="text" name="location" required 
                                class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl text-sm font-bold text-slate-700 transition-all"
                                placeholder="เช่น กรุงเทพมหานคร, ชลบุรี">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">วัตถุประสงค์ / รายละเอียด</label>
                        <div class="relative group">
                            <div class="absolute top-4 left-4 flex items-center pointer-events-none text-slate-300 group-focus-within:text-blue-500 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <textarea name="reason" rows="3" required 
                                    class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl text-sm font-bold text-slate-700 transition-all resize-none"
                                    placeholder="ระบุสาเหตุหรือหมายเลขคำสั่ง..."></textarea>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">เอกสารแนบ (PDF)</label>
                        <div class="relative bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 p-4 hover:bg-white hover:border-blue-400 transition-all group/upload">
                            <input type="file" name="attachment" accept=".pdf" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="flex items-center gap-4 relative z-0">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center group-hover/upload:scale-110 transition-transform shadow-inner">
                                    <i data-lucide="file-up" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-slate-700 leading-none mb-1">เลือกไฟล์ PDF ที่ต้องการ</p>
                                    <p class="text-[10px] font-bold text-slate-400 tracking-tight">ลากและวางไฟล์ หรือคลิกเพื่อเปิด (ขนาดไม่เกิน 5MB)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black rounded-3xl shadow-[0_15px_30px_-10px_rgba(59,130,246,0.3)] transition-all hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                            ยืนยันการบันทึกข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @push('scripts')
        <script>
            function initLucide() {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                initLucide();
                // Periodic re-init to catch Alpine.js changes
                setInterval(initLucide, 2000);
            });

            document.addEventListener('alpine:initialized', () => {
                initLucide();
            });
        </script>
    @endpush
</x-app-layout>

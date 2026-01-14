<x-app-layout>
    @section('title', 'หน้าหลัก (Dashboard)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <!-- Success Alert -->
        @if(session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms class="mb-6 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 p-[2px] shadow-lg shadow-emerald-500/20">
                <div class="bg-white rounded-2xl p-4 flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full blur-2xl opacity-50"></div>
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center flex-shrink-0 text-white shadow-lg shadow-emerald-500/30">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1 relative z-10">
                        <h4 class="font-bold text-slate-800 text-lg">สำเร็จ!</h4>
                        <p class="text-sm font-medium text-slate-600">{{ session('status') }}</p>
                    </div>
                    <button @click="show = false" class="z-10 bg-slate-100 hover:bg-slate-200 rounded-xl p-2.5 text-slate-500 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Premium Welcome Banner with Animated Gradient -->
        <div class="relative overflow-hidden rounded-[2rem] mb-10 group">
            <!-- Animated Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-br from-brand-600 via-brand-500 to-blue-600 animate-gradient-x"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZGVmcz48cGF0dGVybiBpZD0iZ3JpZCIgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiBwYXR0ZXJuVW5pdHM9InVzZXJTcGFjZU9uVXNlIj48cGF0aCBkPSJNIDQwIDAgTCAwIDAgMCA0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2Utb3BhY2l0eT0iMC4xIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] opacity-30"></div>
            
            <!-- Floating Orbs -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-48 -mt-48 group-hover:scale-110 transition-transform duration-1000"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full blur-3xl -ml-36 -mb-36 group-hover:scale-110 transition-transform duration-1000"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
            
            <div class="relative z-10 px-8 py-14 md:py-16">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
                    <!-- Left Content -->
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-white/90 text-sm font-medium mb-6">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            <span>ยินดีต้อนรับสู่ระบบ</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-tight mb-4">
                            สวัสดีครับ, <br class="sm:hidden">
                            <span class="relative">
                                {{ Auth::user()->rank }} {{ Auth::user()->name }}
                                <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 200 12" fill="none">
                                    <path d="M2 8C50 2 100 2 198 8" stroke="rgba(255,255,255,0.4)" stroke-width="4" stroke-linecap="round"/>
                                </svg>
                            </span>
                        </h2>
                        <p class="text-white/80 text-lg md:text-xl max-w-lg font-medium">
                            จัดการงานธุรการด้านกำลังพลได้อย่างง่ายดาย
                        </p>
                    </div>
                    
                    <!-- Right: Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('leave-request.create') }}" 
                           class="group relative inline-flex items-center justify-center px-8 py-4 bg-white text-brand-600 font-bold text-lg rounded-2xl shadow-2xl hover:shadow-white/30 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-brand-50 to-blue-50 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <i data-lucide="plus" class="w-5 h-5 mr-3 relative z-10"></i>
                            <span class="relative z-10">ยื่นใบลาทันที</span>
                        </a>
                        <a href="{{ route('guard-change.create') }}" 
                           class="group inline-flex items-center justify-center px-8 py-4 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold text-lg rounded-2xl border-2 border-white/30 transition-all duration-300 transform hover:-translate-y-1">
                            <i data-lucide="shield" class="w-5 h-5 mr-3"></i>
                            ขอเปลี่ยนยาม
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid with Glassmorphism -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            
            <!-- Card 1: Vacation Balance -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                <div class="relative bg-white rounded-3xl shadow-sm border border-slate-100 p-6 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 hover:-translate-y-2">
                    <!-- Decorative gradient -->
                    <div class="absolute -right-8 -top-8 w-32 h-32">
                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-cyan-400 rounded-full opacity-10 group-hover:opacity-20 group-hover:scale-150 transition-all duration-500"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="plane" class="w-7 h-7"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full">คงเหลือ</span>
                        </div>
                        
                        <h3 class="text-slate-500 font-bold text-sm uppercase tracking-wider mb-2">วันลาพักผ่อน</h3>
                        
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">
                                {{ $vacationBalance ? ($vacationBalance->remaining_days + 0) : 0 }}
                            </span>
                            <span class="text-slate-400 text-lg font-bold">/ {{ $vacationBalance ? ($vacationBalance->total_days + 0) : 0 }}</span>
                            <span class="text-slate-400 text-sm font-medium">วัน</span>
                        </div>
                        
                        <div class="mt-6">
                            <div class="flex justify-between text-xs font-medium text-slate-500 mb-2">
                                <span>ใช้ไปแล้ว</span>
                                @php
                                    $total = ($vacationBalance && $vacationBalance->total_days > 0) ? $vacationBalance->total_days : 1;
                                    $remaining = $vacationBalance ? $vacationBalance->remaining_days : 0;
                                    $percent = ($remaining / $total) * 100;
                                @endphp
                                <span class="text-blue-600 font-bold">{{ number_format(100 - $percent, 0) }}%</span>
                            </div>
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-500 rounded-full transition-all duration-1000" style="width: {{ 100 - $percent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Sick Leave Usage -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                <div class="relative bg-white rounded-3xl shadow-sm border border-slate-100 p-6 overflow-hidden hover:shadow-2xl hover:shadow-orange-500/10 transition-all duration-500 hover:-translate-y-2">
                    <div class="absolute -right-8 -top-8 w-32 h-32">
                        <div class="w-full h-full bg-gradient-to-br from-orange-500 to-red-400 rounded-full opacity-10 group-hover:opacity-20 group-hover:scale-150 transition-all duration-500"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-red-500 text-white flex items-center justify-center shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="heart-pulse" class="w-7 h-7"></i>
                            </div>
                            <span class="text-xs font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full">ปีนี้</span>
                        </div>
                        
                        <h3 class="text-slate-500 font-bold text-sm uppercase tracking-wider mb-2">ลาป่วย</h3>
                        
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black bg-gradient-to-r from-orange-600 to-red-500 bg-clip-text text-transparent">
                                {{ $sickUsageCount }}
                            </span>
                            <span class="text-slate-400 text-sm font-medium">ครั้ง</span>
                        </div>
                        
                        <div class="mt-6 flex items-center gap-3">
                            <div class="flex-1 flex items-center gap-2 bg-gradient-to-r from-orange-50 to-red-50 px-4 py-2.5 rounded-xl border border-orange-100">
                                <i data-lucide="calendar-days" class="w-4 h-4 text-orange-500"></i>
                                <span class="text-sm font-bold text-orange-700">{{ $sickUsageDays + 0 }} วัน</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Personal Leave Usage -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-yellow-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                <div class="relative bg-white rounded-3xl shadow-sm border border-slate-100 p-6 overflow-hidden hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-2">
                    <div class="absolute -right-8 -top-8 w-32 h-32">
                        <div class="w-full h-full bg-gradient-to-br from-amber-500 to-yellow-400 rounded-full opacity-10 group-hover:opacity-20 group-hover:scale-150 transition-all duration-500"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-yellow-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="briefcase" class="w-7 h-7"></i>
                            </div>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-full">ปีนี้</span>
                        </div>
                        
                        <h3 class="text-slate-500 font-bold text-sm uppercase tracking-wider mb-2">ลากิจ</h3>
                        
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black bg-gradient-to-r from-amber-600 to-yellow-500 bg-clip-text text-transparent">
                                {{ $personalUsageCount }}
                            </span>
                            <span class="text-slate-400 text-sm font-medium">ครั้ง</span>
                        </div>
                        
                        <div class="mt-6 flex items-center gap-3">
                            <div class="flex-1 flex items-center gap-2 bg-gradient-to-r from-amber-50 to-yellow-50 px-4 py-2.5 rounded-xl border border-amber-100">
                                <i data-lucide="calendar-days" class="w-4 h-4 text-amber-500"></i>
                                <span class="text-sm font-bold text-amber-700">{{ $personalUsageDays + 0 }} วัน</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Pending -->
            <div class="group relative">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-pink-500 rounded-3xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity duration-500"></div>
                <div class="relative bg-white rounded-3xl shadow-sm border border-slate-100 p-6 overflow-hidden hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 hover:-translate-y-2">
                    <div class="absolute -right-8 -top-8 w-32 h-32">
                        <div class="w-full h-full bg-gradient-to-br from-purple-500 to-pink-400 rounded-full opacity-10 group-hover:opacity-20 group-hover:scale-150 transition-all duration-500"></div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="clock" class="w-7 h-7"></i>
                            </div>
                            @if($pendingCount > 0)
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-purple-500"></span>
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="text-slate-500 font-bold text-sm uppercase tracking-wider mb-2">รออนุมัติ</h3>
                        
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">
                                {{ $pendingCount }}
                            </span>
                            <span class="text-slate-400 text-sm font-medium">รายการ</span>
                        </div>
                        
                        <div class="mt-6">
                            @if($pendingCount > 0)
                                <div class="flex items-center gap-2 bg-gradient-to-r from-purple-50 to-pink-50 px-4 py-2.5 rounded-xl border border-purple-100">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                    <span class="text-sm font-bold text-purple-700">กำลังรอดำเนินการ</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-100">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                                    <span class="text-sm font-bold text-slate-500">ไม่มีรายการค้าง</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Info Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
            <!-- Leave Rules Card -->
            <div class="lg:col-span-2 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-8 relative overflow-hidden group">
                <!-- Decorative Elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSA2MCAwIEwgMCAwIDAgNjAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMSIgc3Ryb2tlLW9wYWNpdHk9IjAuMDMiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-blue-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
                            <i data-lucide="info" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-xl">ระเบียบการลา</h3>
                            <p class="text-slate-400 text-sm">ข้อกำหนดเบื้องต้นในการยื่นใบลา</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 hover:bg-white/10 transition-colors group/item">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center text-white mb-4 shadow-lg shadow-orange-500/20 group-hover/item:scale-110 transition-transform">
                                <i data-lucide="heart-pulse" class="w-5 h-5"></i>
                            </div>
                            <h4 class="text-white font-bold text-lg mb-1">ลาป่วย</h4>
                            <p class="text-slate-400 text-sm">ยื่นได้ทันที<br>ไม่จำกัดวัน</p>
                        </div>
                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 hover:bg-white/10 transition-colors group/item">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-yellow-500 flex items-center justify-center text-white mb-4 shadow-lg shadow-amber-500/20 group-hover/item:scale-110 transition-transform">
                                <i data-lucide="briefcase" class="w-5 h-5"></i>
                            </div>
                            <h4 class="text-white font-bold text-lg mb-1">ลากิจ</h4>
                            <p class="text-slate-400 text-sm">ยื่นล่วงหน้า<br><span class="text-amber-400 font-bold">1 วัน</span></p>
                        </div>
                        <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 hover:bg-white/10 transition-colors group/item">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white mb-4 shadow-lg shadow-blue-500/20 group-hover/item:scale-110 transition-transform">
                                <i data-lucide="plane" class="w-5 h-5"></i>
                            </div>
                            <h4 class="text-white font-bold text-lg mb-1">ลาพักผ่อน</h4>
                            <p class="text-slate-400 text-sm">ยื่นล่วงหน้า<br><span class="text-blue-400 font-bold">3 วัน</span></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-gradient-to-br from-brand-100 to-blue-100 rounded-full opacity-50"></div>
                
                <div class="relative z-10">
                    <h3 class="text-slate-800 font-bold text-lg mb-6 flex items-center gap-2">
                        <i data-lucide="zap" class="w-5 h-5 text-brand-500"></i>
                        ทางลัด
                    </h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('leave-request.index') }}" class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-brand-50 border border-slate-100 hover:border-brand-200 transition-all">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-500 group-hover:text-brand-600 group-hover:shadow-md transition-all">
                                <i data-lucide="history" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-bold text-slate-700 group-hover:text-brand-700">ประวัติการลา</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-brand-500 group-hover:translate-x-1 transition-all"></i>
                        </a>
                        <a href="{{ route('guard-change.index') }}" class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 transition-all">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-500 group-hover:text-emerald-600 group-hover:shadow-md transition-all">
                                <i data-lucide="shield" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-bold text-slate-700 group-hover:text-emerald-700">ประวัติเปลี่ยนยาม</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all"></i>
                        </a>
                        @if(in_array(Auth::user()->role, ['supervisor', 'department_head', 'deputy_director', 'director', 'admin']))
                        <a href="{{ route('attendance-reports.index') }}" class="group flex items-center gap-4 p-4 rounded-2xl bg-slate-50 hover:bg-teal-50 border border-slate-100 hover:border-teal-200 transition-all">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-500 group-hover:text-teal-600 group-hover:shadow-md transition-all">
                                <i data-lucide="scan-face" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <span class="font-bold text-slate-700 group-hover:text-teal-700">รายงานการเข้างาน</span>
                                <p class="text-xs text-slate-400">จากระบบสแกนใบหน้า</p>
                            </div>
                            <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-teal-500 group-hover:translate-x-1 transition-all"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-8 py-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-blue-500 flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">รายการล่าสุด</h3>
                        <p class="text-sm text-slate-500">ประวัติการทำรายการล่าสุดของคุณ</p>
                    </div>
                </div>
                <a href="{{ route('leave-request.index') }}" class="group inline-flex items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700 bg-brand-50 hover:bg-brand-100 px-4 py-2 rounded-xl transition-colors">
                    ดูทั้งหมด
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            
            @if($recentRequests->isEmpty())
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-slate-100 to-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-700 mb-2">ไม่มีรายการล่าสุด</h4>
                    <p class="text-slate-500 mb-6">เริ่มต้นยื่นใบลาวันนี้เลย!</p>
                    <a href="{{ route('leave-request.create') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-bold px-6 py-3 rounded-xl transition-colors">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        ยื่นใบลาใหม่
                    </a>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach($recentRequests as $req)
                    <div class="px-8 py-5 hover:bg-gradient-to-r hover:from-slate-50 hover:to-white transition-all group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                @php
                                    $typeConfig = match($req->leaveType->slug) {
                                        'sick' => ['from' => 'from-orange-500', 'to' => 'to-red-500', 'bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'shadow' => 'shadow-orange-500/20'],
                                        'vacation' => ['from' => 'from-blue-500', 'to' => 'to-cyan-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-500', 'shadow' => 'shadow-blue-500/20'],
                                        default => ['from' => 'from-purple-500', 'to' => 'to-pink-500', 'bg' => 'bg-purple-50', 'text' => 'text-purple-500', 'shadow' => 'shadow-purple-500/20'],
                                    };
                                @endphp
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $typeConfig['from'] }} {{ $typeConfig['to'] }} flex items-center justify-center text-white shadow-lg {{ $typeConfig['shadow'] }} group-hover:scale-110 transition-transform">
                                    @if($req->leaveType->slug == 'sick') <i data-lucide="heart-pulse" class="w-5 h-5"></i>
                                    @elseif($req->leaveType->slug == 'vacation') <i data-lucide="plane" class="w-5 h-5"></i>
                                    @else <i data-lucide="briefcase" class="w-5 h-5"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-base mb-0.5">{{ $req->leaveType->name }}</h4>
                                    <p class="text-sm text-slate-500 font-medium flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 {{ $typeConfig['bg'] }} {{ $typeConfig['text'] }} px-2 py-0.5 rounded-md text-xs font-bold">
                                            {{ $req->total_days + 0 }} วัน
                                        </span>
                                        <span class="text-slate-400">•</span>
                                        <span>@thaidate($req->start_date) - @thaidate($req->end_date)</span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                @php
                                    $statusConfig = match($req->status) {
                                        'approved' => ['bg' => 'bg-gradient-to-r from-emerald-500 to-teal-500', 'text' => 'text-white', 'label' => 'อนุมัติแล้ว'],
                                        'rejected' => ['bg' => 'bg-gradient-to-r from-rose-500 to-red-500', 'text' => 'text-white', 'label' => 'ถูกปฏิเสธ'],
                                        'cancelled' => ['bg' => 'bg-slate-200', 'text' => 'text-slate-600', 'label' => 'ยกเลิกแล้ว'],
                                        default => ['bg' => 'bg-gradient-to-r from-amber-400 to-orange-400', 'text' => 'text-white', 'label' => 'รออนุมัติ']
                                    };
                                    if(in_array($req->status, ['pending_supervisor', 'pending_head', 'pending_deputy_director'])) {
                                         $statusConfig['label'] = 'รออนุมัติ';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} shadow-sm">
                                    {{ $statusConfig['label'] }}
                                </span>
                                <div class="text-xs text-slate-400 mt-1.5 font-medium">
                                    {{ $req->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
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
        @keyframes gradient-x {
            0%, 100% {
                background-size: 200% 200%;
                background-position: left center;
            }
            50% {
                background-size: 200% 200%;
                background-position: right center;
            }
        }
        .animate-gradient-x {
            animation: gradient-x 15s ease infinite;
        }
    </style>
</x-app-layout>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ระบบบริหารจัดการงานธุรการด้านกำลังพล</title>

    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js cloak style -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-600"
    x-data="{ sidebarOpen: false, mobileProfileOpen: false, mobileNotifOpen: false }">

    <!-- Mobile Header -->
    <div class="md:hidden sticky top-0 z-20">
        <!-- Main Mobile Bar -->
        <div
            class="flex items-center justify-between bg-white/95 backdrop-blur-lg shadow-sm px-4 py-3 border-b border-slate-100">
            <!-- Left: Menu + Logo -->
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-brand-50 text-slate-500 hover:text-brand-600 flex items-center justify-center transition-all active:scale-95">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logonavy.png') }}" alt="Logo"
                        class="w-10 h-10 sm:w-12 sm:h-12 object-contain flex-shrink-0">
                    <span
                        class="font-bold text-xs sm:text-sm text-slate-800 hidden xs:block truncate max-w-[150px] sm:max-w-none">ระบบบริหารจัดการงานธุรการด้านกำลังพล</span>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-1.5 sm:gap-2">
                <!-- Mobile Weather Widget (Header) -->
                <div x-data="envStatus()" x-init="init()" class="hidden xs:flex items-center gap-2 mr-1">
                    <!-- Weather -->
                    <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1">
                        <div class="relative w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-md bg-gradient-to-br shadow-sm"
                            :class="weatherBg">
                            <i :data-lucide="weatherIcon" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-700" x-text="temp + '°C'">--°C</span>
                    </div>

                    <!-- PM 2.5 -->
                    <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1">
                        <div class="relative w-6 h-6 flex-shrink-0 flex items-center justify-center rounded-md bg-gradient-to-br shadow-sm"
                            :class="aqiBg">
                            <i data-lucide="wind" class="w-3.5 h-3.5 text-white"></i>
                        </div>
                        <span class="text-xs font-bold" :class="aqiTextColor" x-text="aqi">--</span>
                    </div>
                </div>
                <!-- Notifications -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-amber-50 text-slate-500 hover:text-amber-600 flex items-center justify-center transition-all active:scale-95 relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if(($navNotificationCount ?? 0) > 0)
                            <span
                                class="absolute top-1 right-1 h-5 w-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white">
                                {{ ($navNotificationCount ?? 0) > 9 ? '9+' : ($navNotificationCount ?? 0) }}
                            </span>
                        @endif
                    </button>

                    <!-- Mobile Notification Dropdown -->
                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 py-2 z-[9999] border border-slate-100"
                        style="display: none;">
                        <div
                            class="px-4 py-3 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                            <span class="text-sm font-bold text-slate-800">การแจ้งเตือน</span>
                            @if(($navNotificationCount ?? 0) > 0)
                                <form action="{{ route('notifications.markRead') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs text-brand-600 hover:text-brand-700 font-medium">อ่านทั้งหมด</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse(Auth::user()->unreadNotifications->take(5) as $notification)
                                <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $notification->data['status'] === 'approved' ? 'อนุมัติแล้ว' : ($notification->data['status'] === 'pending' ? 'รอการอนุมัติ' : 'ถูกปฏิเสธ') }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                        {{ $notification->data['message'] }}
                                    </p>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center">
                                    <i data-lucide="bell-off" class="w-6 h-6 text-slate-300 mx-auto mb-2"></i>
                                    <p class="text-sm text-slate-400">ไม่มีการแจ้งเตือน</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Avatar/Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-0.5 shadow-md shadow-brand-500/20 active:scale-95 transition-all overflow-hidden">
                        <div class="w-full h-full rounded-lg bg-white flex items-center justify-center overflow-hidden">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="font-bold text-brand-600 text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </button>

                    <!-- Mobile Profile Dropdown -->
                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 py-2 z-[9999] border border-slate-100"
                        style="display: none;">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-0.5 shadow-md">
                                    <div
                                        class="w-full h-full rounded-lg bg-white flex items-center justify-center overflow-hidden">
                                        @if(Auth::user()->avatar)
                                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                                class="w-full h-full object-cover">
                                        @else
                                            <span
                                                class="font-bold text-brand-600">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 text-sm truncate">{{ Auth::user()->rank }}
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->department ?? 'Staff' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center px-4 py-3 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                            <i data-lucide="user" class="w-4 h-4 mr-3"></i> แก้ไขโปรไฟล์
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex w-full items-center px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <i data-lucide="log-out" class="w-4 h-4 mr-3"></i> ออกจากระบบ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Title Bar (Mobile) -->
        <div class="bg-white/80 backdrop-blur-sm px-4 py-2 border-b border-slate-100">
            <h1 class="text-base font-bold text-slate-800 truncate">@yield('title', 'หน้าหลัก')</h1>
        </div>
    </div>

    <div class="flex min-h-screen">

        <!-- Sidebar Wrapper -->
        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
            class="fixed inset-0 bg-slate-900/20 z-30 md:hidden backdrop-blur-sm"></div>

        <!-- Sidebar (Light Theme) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-100 transition-transform duration-300 md:translate-x-0 flex flex-col shadow-sm">

            <!-- Logo Area -->
            <div class="h-auto py-5 flex flex-col items-center px-4 border-b border-slate-50 text-center">
                <img src="{{ asset('images/logonavy.png') }}" alt="Logo" class="w-12 h-12 object-contain mb-3">
                <div>
                    <h1 class="text-sm font-bold text-slate-800 tracking-tight leading-tight mb-1">
                        ระบบบริหารจัดการงานธุรการด้านกำลังพล</h1>
                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">โรงเรียนพลาธิการ
                        กรมพลาธิการทหารเรือ</p>
                </div>
            </div>



            <!-- Sidebar Profile Removed -->

            <!-- Menu -->
            <nav class="flex-1 px-3 space-y-1 overflow-y-auto custom-scrollbar py-4" x-data="{ 
                openMenus: {
                    leave: {{ request()->routeIs('leave-request.*') ? 'true' : 'false' }},
                    guard: {{ request()->routeIs('guard-change.*') ? 'true' : 'false' }},
                    approval: {{ request()->routeIs('approvals.*') || request()->routeIs('reports.*') ? 'true' : 'false' }},
                    admin: {{ request()->routeIs('employees.*') || request()->routeIs('settings.*') || request()->routeIs('departments.*') || request()->routeIs('leave-entitlements.*') ? 'true' : 'false' }}
                }
            }">
                <!-- Dashboard - Always visible -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                    <i data-lucide="gauge"
                        class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                    <span class="ml-3 text-sm font-medium">หน้าหลัก</span>
                </a>

                <!-- Calendar - Shared Leave Calendar -->
                <a href="{{ route('calendar.index') }}"
                    class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('calendar.*') ? 'bg-indigo-50 text-indigo-600 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                    <i data-lucide="calendar"
                        class="w-4 h-4 {{ request()->routeIs('calendar.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                    <span class="ml-3 text-sm font-medium">ปฏิทินการลา</span>
                </a>

                <!-- Leave Section - Collapsible -->
                <div class="pt-3">
                    <button @click="openMenus.leave = !openMenus.leave"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-brand-600 transition-all duration-200 group">
                        <div class="flex items-center">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center mr-2">
                                <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-blue-500"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">การลา</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform duration-200"
                            :class="openMenus.leave && 'rotate-180'"></i>
                    </button>
                    <div x-show="openMenus.leave" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-1 ml-3 pl-3 border-l-2 border-slate-100 space-y-0.5">
                        <a href="{{ route('leave-request.create') }}"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('leave-request.create') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                            <i data-lucide="send"
                                class="w-3.5 h-3.5 {{ request()->routeIs('leave-request.create') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                            <span class="ml-2.5 text-sm font-medium">ยื่นใบลา</span>
                        </a>
                        <a href="{{ route('leave-request.index') }}"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('leave-request.index') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                            <i data-lucide="history"
                                class="w-3.5 h-3.5 {{ request()->routeIs('leave-request.index') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                            <span class="ml-2.5 text-sm font-medium">ประวัติการลา</span>
                        </a>
                    </div>
                </div>

                <!-- Guard Change Section - Collapsible -->
                <div class="pt-2">
                    <button @click="openMenus.guard = !openMenus.guard"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-emerald-600 transition-all duration-200 group">
                        <div class="flex items-center">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center mr-2">
                                <i data-lucide="shield" class="w-3.5 h-3.5 text-emerald-500"></i>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">การเปลี่ยนยาม</span>
                            @if(isset($navGuardChangePendingMe) && $navGuardChangePendingMe > 0)
                                <span
                                    class="ml-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $navGuardChangePendingMe }}</span>
                            @endif
                        </div>
                        <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform duration-200"
                            :class="openMenus.guard && 'rotate-180'"></i>
                    </button>
                    <div x-show="openMenus.guard" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="mt-1 ml-3 pl-3 border-l-2 border-slate-100 space-y-0.5">
                        <a href="{{ route('guard-change.create') }}"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('guard-change.create') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600' }}">
                            <i data-lucide="plus"
                                class="w-3.5 h-3.5 {{ request()->routeIs('guard-change.create') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600' }}"></i>
                            <span class="ml-2.5 text-sm font-medium">ขอเปลี่ยนยาม</span>
                        </a>
                        <a href="{{ route('guard-change.index') }}"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('guard-change.index') || request()->routeIs('guard-change.show') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600' }}">
                            <i data-lucide="list"
                                class="w-3.5 h-3.5 {{ request()->routeIs('guard-change.index') || request()->routeIs('guard-change.show') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600' }}"></i>
                            <span class="ml-2.5 text-sm font-medium">ประวัติของฉัน</span>
                        </a>
                        <a href="{{ route('guard-change.approvals') }}"
                            class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('guard-change.approvals') ? 'bg-amber-50 text-amber-600' : 'text-slate-500 hover:bg-slate-50 hover:text-amber-600' }}">
                            <i data-lucide="user-check"
                                class="w-3.5 h-3.5 {{ request()->routeIs('guard-change.approvals') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-600' }}"></i>
                            <span class="ml-2.5 text-sm font-medium flex-1">คำขอหาฉัน</span>
                            @if(isset($navGuardChangePendingMe) && $navGuardChangePendingMe > 0)
                                <span
                                    class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $navGuardChangePendingMe }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                @if(in_array(Auth::user()->role, ['supervisor', 'department_head', 'deputy_director', 'director', 'admin']))
                    <!-- Approval Section - Collapsible -->
                    <div class="pt-2">
                        <button @click="openMenus.approval = !openMenus.approval"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-purple-600 transition-all duration-200 group">
                            <div class="flex items-center">
                                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center mr-2">
                                    <i data-lucide="clipboard-check" class="w-3.5 h-3.5 text-purple-500"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">อนุมัติ/รายงาน</span>
                                @php
                                    $totalPending = ($navPendingCount ?? 0) + ($navGuardChangeDeputyCount ?? 0) + ($navGuardChangeFinalCount ?? 0);
                                @endphp
                                @if($totalPending > 0)
                                    <span
                                        class="ml-2 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $totalPending }}</span>
                                @endif
                            </div>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform duration-200"
                                :class="openMenus.approval && 'rotate-180'"></i>
                        </button>
                        <div x-show="openMenus.approval" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-1 ml-3 pl-3 border-l-2 border-slate-100 space-y-0.5">
                            <a href="{{ route('approvals.index') }}"
                                class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('approvals.index') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                                <i data-lucide="file-pen"
                                    class="w-3.5 h-3.5 {{ request()->routeIs('approvals.index') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                                <span class="ml-2.5 text-sm font-medium flex-1">อนุมัติใบลา</span>
                                @if(isset($navPendingCount) && $navPendingCount > 0)
                                    <span
                                        class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $navPendingCount }}</span>
                                @endif
                            </a>

                            @if(in_array(Auth::user()->role, ['deputy_director', 'admin']))
                                <a href="{{ route('guard-change.director-approvals') }}"
                                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('guard-change.director-approvals') ? 'bg-purple-50 text-purple-600' : 'text-slate-500 hover:bg-slate-50 hover:text-purple-600' }}">
                                    <i data-lucide="stamp"
                                        class="w-3.5 h-3.5 {{ request()->routeIs('guard-change.director-approvals') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
                                    <span class="ml-2.5 text-sm font-medium flex-1">รอง ผอ. อนุมัติ</span>
                                    @if(isset($navGuardChangeDeputyCount) && $navGuardChangeDeputyCount > 0)
                                        <span
                                            class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $navGuardChangeDeputyCount }}</span>
                                    @endif
                                </a>
                            @endif

                            @if(in_array(Auth::user()->role, ['director', 'admin']))
                                <a href="{{ route('guard-change.final-approvals') }}"
                                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('guard-change.final-approvals') ? 'bg-rose-50 text-rose-600' : 'text-slate-500 hover:bg-slate-50 hover:text-rose-600' }}">
                                    <i data-lucide="crown"
                                        class="w-3.5 h-3.5 {{ request()->routeIs('guard-change.final-approvals') ? 'text-rose-600' : 'text-slate-400 group-hover:text-rose-600' }}"></i>
                                    <span class="ml-2.5 text-sm font-medium flex-1">ผอ. อนุมัติ</span>
                                    @if(isset($navGuardChangeFinalCount) && $navGuardChangeFinalCount > 0)
                                        <span
                                            class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $navGuardChangeFinalCount }}</span>
                                    @endif
                                </a>
                            @endif

                            <div class="pt-1 mt-1 border-t border-slate-100">
                                <a href="{{ route('reports.index') }}"
                                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.index') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                                    <i data-lucide="pie-chart"
                                        class="w-3.5 h-3.5 {{ request()->routeIs('reports.index') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                                    <span class="ml-2.5 text-sm font-medium">รายงานการลา</span>
                                </a>

                                @if(in_array(Auth::user()->role, ['admin', 'director', 'deputy_director']))
                                    <a href="{{ route('reports.guard-change') }}"
                                        class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('reports.guard-change') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                                        <i data-lucide="repeat"
                                            class="w-3.5 h-3.5 {{ request()->routeIs('reports.guard-change') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                                        <span class="ml-2.5 text-sm font-medium">รายงานเปลี่ยนยาม</span>
                                    </a>
                                @endif

                                <a href="{{ route('attendance-reports.index') }}"
                                    class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('attendance-reports.*') ? 'bg-teal-50 text-teal-600' : 'text-slate-500 hover:bg-slate-50 hover:text-teal-600' }}">
                                    <i data-lucide="scan"
                                        class="w-3.5 h-3.5 {{ request()->routeIs('attendance-reports.*') ? 'text-teal-600' : 'text-slate-400 group-hover:text-teal-600' }}"></i>
                                    <span class="ml-2.5 text-sm font-medium">รายงานการเข้างาน</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(Auth::user()->role === 'admin')
                    <!-- Admin Section - Collapsible -->
                    <div class="pt-2">
                        <button @click="openMenus.admin = !openMenus.admin"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-rose-600 transition-all duration-200 group">
                            <div class="flex items-center">
                                <div class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center mr-2">
                                    <i data-lucide="settings" class="w-3.5 h-3.5 text-rose-500"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">ผู้ดูแลระบบ</span>
                            </div>
                            <i data-lucide="chevron-down" class="w-3 h-3 text-slate-400 transition-transform duration-200"
                                :class="openMenus.admin && 'rotate-180'"></i>
                        </button>
                        <div x-show="openMenus.admin" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            class="mt-1 ml-3 pl-3 border-l-2 border-slate-100 space-y-0.5">
                            <a href="{{ route('employees.index') }}"
                                class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('employees.*') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                                <i data-lucide="users"
                                    class="w-3.5 h-3.5 {{ request()->routeIs('employees.*') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                                <span class="ml-2.5 text-sm font-medium">จัดการข้าราชการ</span>
                            </a>
                            <a href="{{ route('departments.index') }}"
                                class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('departments.*') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                                <i data-lucide="network"
                                    class="w-3.5 h-3.5 {{ request()->routeIs('departments.*') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                                <span class="ml-2.5 text-sm font-medium">จัดการแผนก</span>
                            </a>
                            <a href="{{ route('settings.index') }}"
                                class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('settings.*') ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600' }}">
                                <i data-lucide="settings-2"
                                    class="w-3.5 h-3.5 {{ request()->routeIs('settings.*') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600' }}"></i>
                                <span class="ml-2.5 text-sm font-medium">ตั้งค่าระบบ</span>
                            </a>
                            <a href="{{ route('leave-entitlements.index') }}"
                                class="flex items-center px-3 py-2 rounded-lg transition-all duration-200 group {{ request()->routeIs('leave-entitlements.*') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600' }}">
                                <i data-lucide="calendar-plus"
                                    class="w-3.5 h-3.5 {{ request()->routeIs('leave-entitlements.*') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600' }}"></i>
                                <span class="ml-2.5 text-sm font-medium">จัดการสิทธิ์วันลา</span>
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-slate-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-red-500 transition-all duration-200 group">
                        <i data-lucide="log-out" class="w-4 h-4 group-hover:text-red-500 transition-colors"></i>
                        <span class="ml-3 text-sm font-medium">ออกจากระบบ</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50/50">
            <!-- Topbar (Desktop) -->
            <header
                class="hidden md:flex items-center justify-between h-20 bg-white border-b border-slate-100 px-8 sticky top-0 z-50 relative">
                <div>
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-slate-500">
                                    <i data-lucide="home" class="w-4 h-4"></i>
                                </a>
                            </li>
                            @if(!request()->routeIs('dashboard'))
                                <li><span class="text-slate-300">/</span></li>
                                <li><span class="text-sm font-medium text-slate-600">@yield('title')</span></li>
                            @endif
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-slate-800 mt-1 tracking-tight">
                        @yield('title', 'ระบบบริหารจัดการวันลา')
                    </h1>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Weather & Air Quality -->
                    <div x-data="envStatus()" x-init="init()"
                        class="hidden md:flex items-center gap-2 xl:gap-4 px-3 xl:px-4 py-1.5 xl:py-2 bg-slate-50/50 backdrop-blur-sm rounded-2xl border border-slate-100 shadow-sm group hover:bg-white transition-all duration-300">
                        <!-- Weather -->
                        <div class="flex items-center gap-2 xl:gap-3">
                            <div class="relative w-8 h-8 xl:w-9 xl:h-9 flex items-center justify-center rounded-xl bg-gradient-to-br transition-all duration-500 group-hover:scale-110 shadow-lg shadow-opacity-20"
                                :class="weatherBg">
                                <i :data-lucide="weatherIcon" class="w-4 h-4 xl:w-5 xl:h-5 text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="hidden lg:block text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">สภาพอากาศ</span>
                                <span class="text-xs xl:text-sm font-black text-slate-800 tracking-tight"
                                    x-text="temp + '°C'">--°C</span>
                            </div>
                        </div>

                        <div class="w-px h-6 bg-slate-200/60 mx-1 xl:mx-0"></div>

                        <!-- PM 2.5 -->
                        <div class="flex items-center gap-2 xl:gap-3">
                            <div class="relative w-8 h-8 xl:w-9 xl:h-9 flex items-center justify-center rounded-xl bg-gradient-to-br transition-all duration-500 group-hover:scale-110 shadow-lg shadow-opacity-20"
                                :class="aqiBg">
                                <i data-lucide="wind" class="w-4 h-4 xl:w-5 xl:h-5 text-white"></i>
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="hidden lg:block text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">ดัชนีคุณภาพอากาศ</span>
                                <span class="text-xs xl:text-sm font-black tracking-tight flex items-center gap-1.5"
                                    :class="aqiTextColor">
                                    <span x-text="aqi">--</span>
                                    <span class="hidden lg:inline text-[10px] opacity-70"
                                        x-text="'• ' + aqiStatus"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="relative p-2 text-slate-400 hover:text-brand-600 transition-colors rounded-full hover:bg-slate-50 focus:outline-none">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if(($navNotificationCount ?? 0) > 0)
                                <span
                                    class="absolute top-2 right-2 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
                            @endif
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 py-2 z-[9999] origin-top-right overflow-hidden border border-slate-100"
                            style="display: none;">

                            <div
                                class="px-4 py-3 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                                <span class="text-sm font-bold text-slate-800">การแจ้งเตือน</span>
                                @if(($navNotificationCount ?? 0) > 0)
                                    <form action="{{ route('notifications.markRead') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs text-brand-600 hover:text-brand-700 font-medium hover:underline">
                                            อ่านทั้งหมด
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                @forelse(Auth::user()->unreadNotifications as $notification)
                                    <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-1">
                                                @if($notification->data['status'] === 'approved')
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                                        <i data-lucide="check" class="w-4 h-4"></i>
                                                    </div>
                                                @elseif($notification->data['status'] === 'pending')
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                                        <i data-lucide="x" class="w-4 h-4"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-slate-800">
                                                    @if($notification->data['status'] === 'approved')
                                                        คำขอได้รับการอนุมัติ
                                                    @elseif($notification->data['status'] === 'pending')
                                                        มีคำขอลาใหม่รอการอนุมัติ
                                                    @else
                                                        คำขอถูกปฏิเสธ
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                                                    {{ $notification->data['message'] }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-8 text-center">
                                        <div
                                            class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                            <i data-lucide="bell-off" class="w-5 h-5"></i>
                                        </div>
                                        <p class="text-sm text-slate-500">ไม่มีการแจ้งเตือนใหม่</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-slate-100 hidden lg:block"></div>

                    <!-- Date/Time Display (Mockup) -->
                    <div class="text-right hidden xl:block">
                        <p class="text-sm font-bold text-slate-700">@thaidatefull(now())</p>
                    </div>

                    <div class="h-8 w-px bg-slate-100"></div>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-3 focus:outline-none group">
                            <div class="text-right hidden md:block">
                                <p
                                    class="text-sm font-bold text-slate-700 group-hover:text-brand-600 transition-colors">
                                    {{ Auth::user()->rank }} {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-slate-400">{{ Auth::user()->department ?? 'Staff' }}</p>
                            </div>
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-500 to-brand-600 p-0.5 shadow-md shadow-brand-500/20 group-hover:shadow-brand-500/40 transition-all overflow-hidden">
                                <div
                                    class="w-full h-full rounded-full bg-white flex items-center justify-center overflow-hidden">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                            class="w-full h-full object-cover">
                                    @else
                                        <span
                                            class="font-bold text-brand-600 text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    @endif
                                </div>
                            </div>
                            <i data-lucide="chevron-down"
                                class="w-3 h-3 text-slate-300 group-hover:text-brand-600 transition-colors"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 py-2 focus:outline-none z-[9999] origin-top-right"
                            style="display: none;">

                            <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                <p class="text-xs text-slate-400 uppercase tracking-wider font-bold">บัญชีผู้ใช้</p>
                                <p class="text-sm font-bold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors">
                                <i data-lucide="user" class="w-4 h-4 mr-2"></i> โปรไฟล์
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
                <div class="max-w-[95rem] mx-auto">
                    @if (isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Lucide Icons (Local) -->
    <!-- Lucide Icons -->
    <script src="{{ asset('js/lucide.min.js') }}"></script>
    <script>
        // Wait for lucide to load and then create icons
        function initLucideIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            } else {
                setTimeout(initLucideIcons, 50);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initLucideIcons);
        } else {
            initLucideIcons();
        }
    </script>

    @stack('scripts')

    <script>
        function envStatus() {
            return {
                loading: true,
                temp: '--',
                aqi: '--',
                aqiStatus: 'Loading...',
                weatherIcon: 'sun',
                weatherBg: 'from-amber-400 to-orange-500',
                aqiBg: 'from-slate-200 to-slate-300',
                aqiTextColor: 'text-slate-400',

                async init() {
                    try {
                        // Bangkok coordinates
                        const lat = 13.7563;
                        const lon = 100.5018;

                        // Fetch Weather & AQI from Open-Meteo (Free, no key required)
                        const [weatherRes, aqiRes] = await Promise.all([
                            fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,weather_code`),
                            fetch(`https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${lat}&longitude=${lon}&current=pm2_5`)
                        ]);

                        const weatherData = await weatherRes.json();
                        const aqiData = await aqiRes.json();

                        if (weatherData.current) {
                            this.temp = Math.round(weatherData.current.temperature_2m);
                            this.setWeatherStyles(weatherData.current.weather_code);
                        }

                        if (aqiData.current) {
                            this.aqi = Math.round(aqiData.current.pm2_5);
                            this.setAqiStyles(this.aqi);
                        }

                        this.loading = false;

                        // Re-initialize Lucide icons to process dynamic attributes
                        setTimeout(() => {
                            if (window.lucide) window.lucide.createIcons();
                        }, 100);

                    } catch (error) {
                        console.error('Failed to fetch environment data:', error);
                        this.aqiStatus = 'Error';
                    }
                },

                setWeatherStyles(code) {
                    if (code <= 3) { // Clear/Cloudy
                        this.weatherIcon = 'sun';
                        this.weatherBg = 'from-amber-400 to-orange-500 shadow-orange-200';
                    } else if (code <= 48) { // Fog
                        this.weatherIcon = 'cloud';
                        this.weatherBg = 'from-slate-300 to-slate-500 shadow-slate-200';
                    } else if (code <= 67) { // Rain
                        this.weatherIcon = 'cloud-rain';
                        this.weatherBg = 'from-blue-400 to-indigo-500 shadow-blue-200';
                    } else { // Storm
                        this.weatherIcon = 'zap';
                        this.weatherBg = 'from-purple-500 to-indigo-700 shadow-purple-200';
                    }
                },

                setAqiStyles(pm25) {
                    if (pm25 <= 15) {
                        this.aqiStatus = 'ดีมาก';
                        this.aqiBg = 'from-emerald-400 to-teal-500 shadow-emerald-200';
                        this.aqiTextColor = 'text-emerald-600';
                    } else if (pm25 <= 25) {
                        this.aqiStatus = 'ดี';
                        this.aqiBg = 'from-green-400 to-emerald-500 shadow-green-200';
                        this.aqiTextColor = 'text-green-600';
                    } else if (pm25 <= 37) {
                        this.aqiStatus = 'ปานกลาง';
                        this.aqiBg = 'from-yellow-400 to-amber-500 shadow-amber-200';
                        this.aqiTextColor = 'text-amber-600';
                    } else if (pm25 <= 75) {
                        this.aqiStatus = 'เริ่มมีผล';
                        this.aqiBg = 'from-orange-400 to-red-500 shadow-orange-200';
                        this.aqiTextColor = 'text-orange-600';
                    } else {
                        this.aqiStatus = 'มีผลกระทบ';
                        this.aqiBg = 'from-red-500 to-rose-700 shadow-red-200';
                        this.aqiTextColor = 'text-rose-600';
                    }
                }
            }
        }
    </script>
</body>

</html>
<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { Toast } from '@/utils/swal';

const props = defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user);
const notifications = computed(() => page.props.notifications || []);
const notificationCount = computed(() => page.props.notificationCount || 0);
const navPendingCount = computed(() => page.props.navPendingCount || 0);
const navGuardChangePendingMe = computed(() => page.props.navGuardChangePendingMe || 0);
const navGuardChangeDeputyCount = computed(() => page.props.navGuardChangeDeputyCount || 0);
const navGuardChangeFinalCount = computed(() => page.props.navGuardChangeFinalCount || 0);
const flash = computed(() => page.props.flash || {});
const errors = computed(() => page.props.errors || {});

const totalApprovalPending = computed(() => 
    navPendingCount.value + navGuardChangeDeputyCount.value + navGuardChangeFinalCount.value
);

const sidebarOpen = ref(false);
const profileOpen = ref(false);
const notifOpen = ref(false);
const mobileProfileOpen = ref(false);
const mobileNotifOpen = ref(false);
const showAvatarModal = ref(false);

const profileDropdownRef = ref(null);
const notifDropdownRef = ref(null);

function handleClickOutside(e) {
    if (profileDropdownRef.value && !profileDropdownRef.value.contains(e.target)) {
        profileOpen.value = false;
    }
    if (notifDropdownRef.value && !notifDropdownRef.value.contains(e.target)) {
        notifOpen.value = false;
    }
}

const currentUrl = computed(() => page.url);

// Auto-expand sidebar menus based on current route
const openMenus = ref({
    leave: false,
    guard: false,
    approval: false,
    admin: false,
});

function autoExpandMenus() {
    const url = currentUrl.value;
    openMenus.value.leave = url.includes('/leave-request');
    openMenus.value.guard = url.includes('/guard-change') || url.includes('/duty-roster');
    openMenus.value.approval = url.includes('/approvals') || url.includes('/reports') || url.includes('/attendance-reports') || url.includes('/ranking');
    openMenus.value.admin = url.includes('/employees') || url.includes('/settings') || url.includes('/departments') || url.includes('/leave-entitlements') || url.includes('/duty-roster/manage');
}

function isRoute(pattern) {
    return currentUrl.value.includes(pattern);
}

function isExactRoute(path) {
    const url = currentUrl.value.split('?')[0];
    return url === path || url === path + '/';
}

const isExecutive = computed(() => 
    user.value && ['admin', 'deputy_director', 'director'].includes(user.value.role)
);

const isApprover = computed(() => 
    user.value && ['supervisor', 'department_head', 'deputy_director', 'director', 'admin'].includes(user.value.role)
);

const isAdmin = computed(() => user.value?.role === 'admin');

const isDeputyOrAdmin = computed(() => 
    user.value && ['deputy_director', 'admin'].includes(user.value.role)
);

const isDirectorOrAdmin = computed(() => 
    user.value && ['director', 'admin'].includes(user.value.role)
);

function logout() {
    router.post('/logout');
}

function markNotificationsRead() {
    router.post('/notifications/mark-read');
}

// Weather & AQI
const weather = ref({
    temp: '--',
    icon: 'sun',
    bg: 'from-amber-400 to-orange-500',
});

const aqi = ref({
    value: '--',
    status: 'Loading...',
    bg: 'from-slate-200 to-slate-300',
    textColor: 'text-slate-400',
});

async function fetchEnvStatus() {
    try {
        const [weatherRes, aqiRes] = await Promise.all([
            fetch('https://api.open-meteo.com/v1/forecast?latitude=13.667605&longitude=100.583562&current=temperature_2m,weather_code'),
            fetch('https://air-quality-api.open-meteo.com/v1/air-quality?latitude=13.667605&longitude=100.583562&current=pm2_5'),
        ]);
        const weatherData = await weatherRes.json();
        const aqiData = await aqiRes.json();

        if (weatherData.current) {
            weather.value.temp = Math.round(weatherData.current.temperature_2m);
            setWeatherStyles(weatherData.current.weather_code);
        }
        if (aqiData.current) {
            aqi.value.value = Math.round(aqiData.current.pm2_5);
            setAqiStyles(aqi.value.value);
        }
        await nextTick();
        initLucide();
    } catch (e) {
        console.error('Failed to fetch environment data:', e);
        aqi.value.status = 'Error';
    }
}

function setWeatherStyles(code) {
    if (code <= 3) {
        weather.value.icon = 'sun';
        weather.value.bg = 'from-amber-400 to-orange-500 shadow-orange-200';
    } else if (code <= 48) {
        weather.value.icon = 'cloud';
        weather.value.bg = 'from-slate-300 to-slate-500 shadow-slate-200';
    } else if (code <= 67) {
        weather.value.icon = 'cloud-rain';
        weather.value.bg = 'from-blue-400 to-indigo-500 shadow-blue-200';
    } else {
        weather.value.icon = 'zap';
        weather.value.bg = 'from-purple-500 to-indigo-700 shadow-purple-200';
    }
}

function setAqiStyles(pm25) {
    if (pm25 <= 15) {
        aqi.value.status = 'ดีมาก'; aqi.value.bg = 'from-emerald-400 to-teal-500 shadow-emerald-200'; aqi.value.textColor = 'text-emerald-600';
    } else if (pm25 <= 25) {
        aqi.value.status = 'ดี'; aqi.value.bg = 'from-green-400 to-emerald-500 shadow-green-200'; aqi.value.textColor = 'text-green-600';
    } else if (pm25 <= 37) {
        aqi.value.status = 'ปานกลาง'; aqi.value.bg = 'from-yellow-400 to-amber-500 shadow-amber-200'; aqi.value.textColor = 'text-amber-600';
    } else if (pm25 <= 75) {
        aqi.value.status = 'เริ่มมีผล'; aqi.value.bg = 'from-orange-400 to-red-500 shadow-orange-200'; aqi.value.textColor = 'text-orange-600';
    } else {
        aqi.value.status = 'มีผลกระทบ'; aqi.value.bg = 'from-red-500 to-rose-700 shadow-red-200'; aqi.value.textColor = 'text-rose-600';
    }
}

// Thai date formatting
const thaiDate = computed(() => {
    const now = new Date();
    const months = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
    const days = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
    return `วัน${days[now.getDay()]}ที่ ${now.getDate()} ${months[now.getMonth()]} พ.ศ. ${now.getFullYear() + 543}`;
});

// Live clock
const clockTime = ref('00:00:00');
let clockInterval = null;

function updateClock() {
    const now = new Date();
    clockTime.value = [
        String(now.getHours()).padStart(2, '0'),
        String(now.getMinutes()).padStart(2, '0'),
        String(now.getSeconds()).padStart(2, '0'),
    ].join(':');
}

function initLucide() {
    if (window.lucide) window.lucide.createIcons();
}

// SweetAlert2 flash messages
function showFlashMessages() {
    const f = flash.value;
    if (f.status || f.success) {
        Toast.fire({ icon: 'success', title: f.status || f.success });
    }
    if (f.warning) {
        Toast.fire({ icon: 'warning', title: f.warning });
    }
    if (f.info) {
        Toast.fire({ icon: 'info', title: f.info });
    }
    if (f.error) {
        Swal.fire({
            icon: 'error', title: 'เกิดข้อผิดพลาด', text: f.error,
            confirmButtonText: 'ตกลง', confirmButtonColor: '#ef4444',
        });
    }
    const errs = errors.value;
    if (errs && Object.keys(errs).length > 0) {
        let html = '<ul class="text-left text-sm space-y-1">';
        Object.values(errs).forEach(e => { html += `<li>• ${e}</li>`; });
        html += '</ul>';
        Swal.fire({
            icon: 'error', title: 'กรุณาตรวจสอบข้อมูล', html,
            confirmButtonText: 'ตกลง', confirmButtonColor: '#ef4444',
        });
    }
}

onMounted(() => {
    updateClock();
    clockInterval = setInterval(updateClock, 1000);
    autoExpandMenus();
    fetchEnvStatus();
    document.addEventListener('click', handleClickOutside);
    
    // Check avatar
    if (user.value && !user.value.avatar && !currentUrl.value.includes('/profile')) {
        showAvatarModal.value = true;
    }

    // Init Lucide & SweetAlert
    setTimeout(() => { initLucide(); showFlashMessages(); }, 150);
});

onUnmounted(() => {
    if (clockInterval) clearInterval(clockInterval);
    document.removeEventListener('click', handleClickOutside);
});

// Re-init lucide after Inertia navigation & show flash
router.on('navigate', () => {
    setTimeout(() => { initLucide(); showFlashMessages(); }, 150);
    nextTick(() => autoExpandMenus());
});

// Watch for flash changes
watch(flash, () => { showFlashMessages(); }, { deep: true });

function avatarUrl(avatar) {
    if (!avatar) return null;
    return `/storage/${avatar}`;
}

function notifStatusText(status) {
    if (status === 'approved') return 'คำขอได้รับการอนุมัติ';
    if (status === 'pending') return 'มีคำขอลาใหม่รอการอนุมัติ';
    return 'คำขอถูกปฏิเสธ';
}

function notifStatusIcon(status) {
    if (status === 'approved') return 'check';
    if (status === 'pending') return 'clock';
    return 'x';
}

function notifStatusColor(status) {
    if (status === 'approved') return 'bg-green-100 text-green-600';
    if (status === 'pending') return 'bg-amber-100 text-amber-600';
    return 'bg-red-100 text-red-600';
}
</script>

<template>
    <Head :title="title" />

    <div class="antialiased bg-slate-50/50 text-slate-600 selection:bg-brand-100 selection:text-brand-700">

        <!-- ==================== MOBILE HEADER ==================== -->
        <div class="md:hidden sticky top-0 z-20">
            <div class="flex items-center justify-between glass-header px-4 py-3">
                <!-- Left: Logo -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="relative">
                            <div class="absolute inset-0 bg-brand-500 blur-lg opacity-20"></div>
                            <img src="/images/logonavy.png" alt="Logo" class="relative w-10 h-10 object-contain flex-shrink-0">
                        </div>
                    </div>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-2">
                    <!-- Mobile Weather & AQI -->
                    <div class="flex items-center gap-2 mr-1">
                        <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1.5 shadow-sm">
                            <div class="relative w-5 h-5 flex-shrink-0 flex items-center justify-center rounded-md bg-gradient-to-br" :class="weather.bg">
                                <i :data-lucide="weather.icon" class="w-3 text-white"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-700">{{ weather.temp }}°C</span>
                        </div>
                        <div class="hidden sm:flex items-center gap-1.5 bg-slate-50 border border-slate-100 rounded-lg px-2 py-1.5 shadow-sm">
                            <div class="relative w-5 h-5 flex-shrink-0 flex items-center justify-center rounded-md bg-gradient-to-br" :class="aqi.bg">
                                <i data-lucide="wind" class="w-3 text-white"></i>
                            </div>
                            <span class="text-xs font-bold" :class="aqi.textColor">{{ aqi.value }}</span>
                        </div>
                    </div>

                    <!-- Mobile Notifications -->
                    <div class="relative">
                        <button @click="mobileNotifOpen = !mobileNotifOpen" aria-label="แจ้งเตือน" class="w-9 h-9 rounded-xl bg-white text-slate-500 hover:text-brand-600 flex items-center justify-center transition-all active:scale-95 relative border border-slate-100 shadow-sm cursor-pointer focus-ring">
                            <i data-lucide="bell" class="w-4 h-4"></i>
                            <span v-if="notificationCount > 0" class="absolute top-0 right-0 h-4 w-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center ring-2 ring-white">
                                {{ notificationCount > 9 ? '9+' : notificationCount }}
                            </span>
                        </button>
                        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                            <div v-show="mobileNotifOpen" @click.away="mobileNotifOpen = false" class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 py-2 z-[9999] border border-slate-100">
                                <div class="px-4 py-3 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                                    <span class="text-sm font-bold text-slate-800">การแจ้งเตือน</span>
                                    <button v-if="notificationCount > 0" @click="markNotificationsRead" class="text-xs text-brand-600 hover:text-brand-700 font-medium">อ่านทั้งหมด</button>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <template v-if="notifications.length > 0">
                                        <div v-for="notif in notifications.slice(0, 5)" :key="notif.id" class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50">
                                            <p class="text-sm font-bold text-slate-800">
                                                {{ notif.data?.status === 'approved' ? 'อนุมัติแล้ว' : (notif.data?.status === 'pending' ? 'รอการอนุมัติ' : 'ถูกปฏิเสธ') }}
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ notif.data?.message }}</p>
                                        </div>
                                    </template>
                                    <div v-else class="px-4 py-6 text-center">
                                        <i data-lucide="bell-off" class="w-6 h-6 text-slate-300 mx-auto mb-2"></i>
                                        <p class="text-sm text-slate-400">ไม่มีการแจ้งเตือน</p>
                                    </div>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Mobile User Avatar -->
                    <div class="relative">
                        <button @click="mobileProfileOpen = !mobileProfileOpen" aria-label="เมนูผู้ใช้งาน" class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-0.5 shadow-md shadow-brand-500/20 active:scale-95 transition-all overflow-hidden border border-white/20 cursor-pointer focus-ring">
                            <div class="w-full h-full rounded-lg bg-white flex items-center justify-center overflow-hidden">
                                <img v-if="user?.avatar" :src="avatarUrl(user.avatar)" alt="โปรไฟล์" class="w-full h-full object-cover">
                                <span v-else class="font-bold text-brand-600 text-xs">{{ user?.name?.charAt(0) }}</span>
                            </div>
                        </button>
                        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-2 scale-95">
                            <div v-show="mobileProfileOpen" @click.away="mobileProfileOpen = false" class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] ring-1 ring-black/5 py-2 z-[9999] border border-slate-100 origin-top-right">
                                <div class="px-4 py-3 border-b border-slate-50 mb-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-0.5 shadow-sm">
                                            <div class="w-full h-full rounded-lg bg-white flex items-center justify-center overflow-hidden">
                                                <img v-if="user?.avatar" :src="avatarUrl(user.avatar)" alt="Avatar" class="w-full h-full object-cover">
                                                <span v-else class="font-bold text-brand-600 text-xs">{{ user?.name?.charAt(0) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-slate-800 text-sm truncate">{{ user?.rank }}{{ user?.name }}</p>
                                            <p class="text-xs text-slate-500 truncate uppercase tracking-wider">{{ user?.department || 'Staff' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-1.5 space-y-0.5">
                                    <Link href="/profile" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-600 rounded-xl transition-all">
                                        <i data-lucide="user-cog" class="w-4 h-4 mr-3 opacity-60"></i> จัดการโปรไฟล์
                                    </Link>
                                    <div class="h-px bg-slate-50 mx-3 my-1"></div>
                                    <button @click="logout" class="flex w-full items-center px-3 py-2.5 text-sm font-medium text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                        <i data-lucide="log-out" class="w-4 h-4 mr-3 opacity-60"></i> ออกจากระบบ
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Hamburger -->
                    <button @click="sidebarOpen = !sidebarOpen" aria-label="ขยายเมนู" class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center transition-all active:scale-95 shadow-lg shadow-slate-900/20 cursor-pointer focus-ring">
                        <i data-lucide="menu" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Page Title Bar -->
        <div class="md:hidden bg-white/80 backdrop-blur-md px-4 py-2.5 border-b border-slate-100 sticky top-[65px] z-10">
            <div class="flex items-center gap-2">
                <div class="w-1 h-4 bg-brand-500 rounded-full"></div>
                <h1 class="text-sm font-bold text-slate-800 truncate">{{ title || 'หน้าหลัก' }}</h1>
            </div>
        </div>

        <div class="flex min-h-screen">
            <!-- Overlay -->
            <Transition enter-active-class="transition-opacity ease-linear duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition-opacity ease-linear duration-300" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/20 z-30 md:hidden backdrop-blur-sm"></div>
            </Transition>

            <!-- ==================== SIDEBAR ==================== -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed md:sticky md:top-0 inset-y-0 left-0 z-40 w-72 bg-white border-r border-slate-100 transition-transform duration-300 md:translate-x-0 flex flex-col shadow-sm h-screen">

                <!-- Logo Area -->
                <div class="py-8 flex flex-col items-center px-6 border-b border-slate-50 text-center relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative mb-4">
                        <div class="absolute inset-0 bg-brand-500 blur-2xl opacity-10 animate-pulse"></div>
                        <img src="/images/logonavy.png" alt="Logo" class="relative w-16 h-16 object-contain transform group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="relative">
                        <h1 class="text-lg font-bold text-slate-800 tracking-tight leading-tight mb-1">ระบบบริหารจัดการงานกำลังพล</h1>
                        <p class="text-[10px] text-brand-600 uppercase tracking-[0.2em] font-bold opacity-70">NAVAL SUPPLY SCHOOL</p>
                    </div>
                </div>

                <!-- Nav Menu -->
                <nav class="flex-1 px-3 space-y-1.5 overflow-y-auto custom-scrollbar py-6">
                    <!-- Dashboard -->
                    <Link href="/dashboard" :class="isExactRoute('/dashboard') || isExactRoute('/executive-dashboard') && !isExecutive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group">
                        <i data-lucide="layout-dashboard" class="w-5 h-5" :class="isExactRoute('/dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600'"></i>
                        <span class="ml-3 text-sm font-bold tracking-tight">แผงควบคุมหลัก</span>
                    </Link>

                    <!-- Calendar -->
                    <Link href="/calendar" :class="isRoute('/calendar') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group">
                        <i data-lucide="calendar-range" class="w-5 h-5" :class="isRoute('/calendar') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600'"></i>
                        <span class="ml-3 text-sm font-bold tracking-tight">ปฏิทินของส่วนรวม</span>
                    </Link>

                    <!-- Executive Dashboard -->
                    <Link v-if="isExecutive" href="/executive-dashboard" :class="isRoute('/executive-dashboard') ? 'bg-gradient-to-r from-purple-50 to-indigo-50 text-purple-700 shadow-sm shadow-purple-500/10 ring-1 ring-purple-100' : 'text-slate-500 hover:bg-gradient-to-r hover:from-purple-50/50 hover:to-indigo-50/50 hover:text-purple-600'" class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 group">
                        <div class="w-5 h-5 rounded-md flex items-center justify-center transition-all" :class="isRoute('/executive-dashboard') ? 'bg-gradient-to-br from-purple-500 to-indigo-600' : 'bg-gradient-to-br from-slate-300 to-slate-400 group-hover:from-purple-500 group-hover:to-indigo-600'">
                            <i data-lucide="bar-chart-3" class="w-3 h-3 text-white"></i>
                        </div>
                        <span class="ml-3 text-sm font-bold opacity-90">ภาพรวมผู้บริหาร</span>
                        <span class="ml-auto px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded transition-colors" :class="isRoute('/executive-dashboard') ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-400 group-hover:bg-purple-100 group-hover:text-purple-600'">Executive</span>
                    </Link>

                    <!-- Leave Section -->
                    <div class="pt-3">
                        <button @click="openMenus.leave = !openMenus.leave" aria-label="ขยายเมนูการลา" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 transition-all duration-300 group cursor-pointer focus-ring">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <i data-lucide="send-to-back" class="w-4 h-4"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 tracking-tight">งานบริหารวันลา</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 transition-transform duration-300" :class="openMenus.leave && 'rotate-90'"></i>
                        </button>
                        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-x-2" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 -translate-x-2">
                            <div v-show="openMenus.leave" class="mt-1 ml-5 pl-4 border-l-2 border-slate-100 space-y-1">
                                <Link href="/leave-request/create" :class="isRoute('/leave-request/create') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="send" class="w-4 h-4" :class="isRoute('/leave-request/create') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">ยื่นใบลา</span>
                                </Link>
                                <Link href="/leave-request" :class="isRoute('/leave-request') && !isRoute('/leave-request/create') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="history" class="w-4 h-4" :class="isRoute('/leave-request') && !isRoute('/leave-request/create') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">ประวัติการลา</span>
                                </Link>
                            </div>
                        </Transition>
                    </div>

                    <!-- Guard Section -->
                    <div class="pt-3">
                        <button @click="openMenus.guard = !openMenus.guard" aria-label="ขยายเมนูเวรยาม" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 transition-all duration-300 group cursor-pointer focus-ring">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mr-3 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 tracking-tight">งานเวรยาม</span>
                                <span v-if="navGuardChangePendingMe > 0" class="ml-2 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20 animate-pulse">{{ navGuardChangePendingMe }}</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 transition-transform duration-300" :class="openMenus.guard && 'rotate-90'"></i>
                        </button>
                        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-x-2" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 -translate-x-2">
                            <div v-show="openMenus.guard" class="mt-1 ml-5 pl-4 border-l-2 border-slate-100 space-y-1">
                                <Link href="/guard-change/create" :class="isRoute('/guard-change/create') ? 'bg-emerald-50 text-emerald-700 shadow-sm shadow-emerald-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="plus" class="w-4 h-4" :class="isRoute('/guard-change/create') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">ขอเปลี่ยนยาม</span>
                                </Link>
                                <Link href="/guard-change" :class="isRoute('/guard-change') && !isRoute('/guard-change/create') && !isRoute('/guard-change-') ? 'bg-emerald-50 text-emerald-700 shadow-sm shadow-emerald-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="list" class="w-4 h-4" :class="isRoute('/guard-change') && !isRoute('/guard-change/create') && !isRoute('/guard-change-') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">ประวัติของฉัน</span>
                                </Link>
                                <Link href="/guard-change-approvals" :class="isRoute('/guard-change-approvals') ? 'bg-amber-50 text-amber-700 shadow-sm shadow-amber-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-amber-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="user-check" class="w-4 h-4" :class="isRoute('/guard-change-approvals') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight flex-1">คำขอหาฉัน</span>
                                    <span v-if="navGuardChangePendingMe > 0" class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20 animate-pulse">{{ navGuardChangePendingMe }}</span>
                                </Link>
                                <Link href="/duty-roster" :class="isRoute('/duty-roster') && !isRoute('/duty-roster/manage') ? 'bg-teal-50 text-teal-700 shadow-sm shadow-teal-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-teal-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="calendar-days" class="w-4 h-4" :class="isRoute('/duty-roster') && !isRoute('/duty-roster/manage') ? 'text-teal-600' : 'text-slate-400 group-hover:text-teal-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">ตารางเวร</span>
                                </Link>
                            </div>
                        </Transition>
                    </div>

                    <!-- Approval Section -->
                    <div v-if="isApprover" class="pt-2">
                        <button @click="openMenus.approval = !openMenus.approval" aria-label="ขยายเมนูการอนุมัติ" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 transition-all duration-300 group cursor-pointer focus-ring">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center mr-3 group-hover:bg-purple-600 group-hover:text-white transition-all">
                                    <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 tracking-tight">การอนุมัติ/รายงาน</span>
                                <span v-if="totalApprovalPending > 0" class="ml-2 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20 animate-pulse">{{ totalApprovalPending }}</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 transition-transform duration-300" :class="openMenus.approval && 'rotate-90'"></i>
                        </button>
                        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-x-2" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 -translate-x-2">
                            <div v-show="openMenus.approval" class="mt-1 ml-5 pl-4 border-l-2 border-slate-100 space-y-1">
                                <Link href="/approvals" :class="isRoute('/approvals') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="file-pen" class="w-4 h-4" :class="isRoute('/approvals') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight flex-1">อนุมัติใบลา</span>
                                    <span v-if="navPendingCount > 0" class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20 animate-pulse">{{ navPendingCount }}</span>
                                </Link>
                                <Link v-if="isDeputyOrAdmin" href="/guard-change-director-approvals" :class="isRoute('/guard-change-director-approvals') ? 'bg-purple-50 text-purple-700 shadow-sm shadow-purple-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-purple-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="stamp" class="w-4 h-4" :class="isRoute('/guard-change-director-approvals') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight flex-1">รอง ผอ. อนุมัติ</span>
                                    <span v-if="navGuardChangeDeputyCount > 0" class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20 animate-pulse">{{ navGuardChangeDeputyCount }}</span>
                                </Link>
                                <Link v-if="isDirectorOrAdmin" href="/guard-change-final-approvals" :class="isRoute('/guard-change-final-approvals') ? 'bg-rose-50 text-rose-700 shadow-sm shadow-rose-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-rose-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="crown" class="w-4 h-4" :class="isRoute('/guard-change-final-approvals') ? 'text-rose-600' : 'text-slate-400 group-hover:text-rose-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight flex-1">ผอ. อนุมัติ</span>
                                    <span v-if="navGuardChangeFinalCount > 0" class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-lg shadow-rose-500/20 animate-pulse">{{ navGuardChangeFinalCount }}</span>
                                </Link>

                                <div class="pt-2 mt-2 border-t border-slate-50">
                                    <Link href="/reports" :class="isRoute('/reports') && !isRoute('/reports/temporary-leave') && !isRoute('/reports/guard-change') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                        <i data-lucide="pie-chart" class="w-4 h-4" :class="isRoute('/reports') && !isRoute('/reports/temporary-leave') && !isRoute('/reports/guard-change') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                        <span class="ml-3 text-sm font-bold tracking-tight">รายงานการลา</span>
                                    </Link>
                                    <Link href="/reports/temporary-leave" :class="isRoute('/reports/temporary-leave') ? 'bg-purple-50 text-purple-700 shadow-sm shadow-purple-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-purple-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                        <i data-lucide="clock" class="w-4 h-4" :class="isRoute('/reports/temporary-leave') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600'"></i>
                                        <span class="ml-3 text-sm font-bold tracking-tight">รายงานลาชั่วกาล</span>
                                    </Link>
                                    <Link v-if="isExecutive" href="/reports/guard-change" :class="isRoute('/reports/guard-change') ? 'bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                        <i data-lucide="repeat" class="w-4 h-4" :class="isRoute('/reports/guard-change') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600'"></i>
                                        <span class="ml-3 text-sm font-bold tracking-tight">รายงานเปลี่ยนยาม</span>
                                    </Link>
                                    <Link href="/attendance-reports" :class="isRoute('/attendance-reports') ? 'bg-teal-50 text-teal-700 shadow-sm shadow-teal-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-teal-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                        <i data-lucide="scan" class="w-4 h-4" :class="isRoute('/attendance-reports') ? 'text-teal-600' : 'text-slate-400 group-hover:text-teal-600'"></i>
                                        <span class="ml-3 text-sm font-bold tracking-tight">รายงานการเข้างาน</span>
                                    </Link>
                                    <Link href="/ranking" :class="isRoute('/ranking') ? 'bg-amber-50 text-amber-700 shadow-sm shadow-amber-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-amber-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                        <i data-lucide="trophy" class="w-4 h-4" :class="isRoute('/ranking') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-600'"></i>
                                        <span class="ml-3 text-sm font-bold tracking-tight">จัดอันดับยอดเยี่ยม</span>
                                    </Link>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Admin Section -->
                    <div v-if="isAdmin" class="pt-2">
                        <button @click="openMenus.admin = !openMenus.admin" aria-label="ขยายเมนูผู้ดูแลระบบ" class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 transition-all duration-300 group cursor-pointer focus-ring">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center mr-3 group-hover:bg-rose-600 group-hover:text-white transition-all">
                                    <i data-lucide="settings" class="w-4 h-4"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-800 tracking-tight">ผู้ดูแลระบบ</span>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 transition-transform duration-300" :class="openMenus.admin && 'rotate-90'"></i>
                        </button>
                        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 -translate-x-2" enter-to-class="opacity-100 translate-x-0" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100 translate-x-0" leave-to-class="opacity-0 -translate-x-2">
                            <div v-show="openMenus.admin" class="mt-1 ml-5 pl-4 border-l-2 border-slate-100 space-y-1">
                                <Link href="/employees" :class="isRoute('/employees') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="users" class="w-4 h-4" :class="isRoute('/employees') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">จัดการข้าราชการ</span>
                                </Link>
                                <Link href="/departments" :class="isRoute('/departments') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="network" class="w-4 h-4" :class="isRoute('/departments') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">จัดการแผนก</span>
                                </Link>
                                <Link href="/settings" :class="isRoute('/settings') ? 'bg-brand-50 text-brand-700 shadow-sm shadow-brand-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-brand-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="sliders-horizontal" class="w-4 h-4" :class="isRoute('/settings') ? 'text-brand-600' : 'text-slate-400 group-hover:text-brand-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">ตั้งค่าเงื่อนไขการลา</span>
                                </Link>
                                <Link href="/leave-entitlements" :class="isRoute('/leave-entitlements') ? 'bg-emerald-50 text-emerald-700 shadow-sm shadow-emerald-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="calendar-plus" class="w-4 h-4" :class="isRoute('/leave-entitlements') ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">จัดการสิทธิ์วันลา</span>
                                </Link>
                                <Link href="/duty-roster/manage" :class="isRoute('/duty-roster/manage') ? 'bg-teal-50 text-teal-700 shadow-sm shadow-teal-500/10' : 'text-slate-500 hover:bg-slate-50 hover:text-teal-600'" class="flex items-center px-4 py-2.5 rounded-lg transition-all duration-200 group">
                                    <i data-lucide="shield-plus" class="w-4 h-4" :class="isRoute('/duty-roster/manage') ? 'text-teal-600' : 'text-slate-400 group-hover:text-teal-600'"></i>
                                    <span class="ml-3 text-sm font-bold tracking-tight">จัดการตารางเวร</span>
                                </Link>
                            </div>
                        </Transition>
                    </div>
                </nav>

                <!-- Logout -->
                <div class="p-4 border-t border-slate-50">
                    <button @click="logout" class="flex w-full items-center px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-50 hover:text-red-500 transition-all duration-200 group">
                        <i data-lucide="log-out" class="w-4 h-4 group-hover:text-red-500 transition-colors"></i>
                        <span class="ml-3 text-sm font-medium">ออกจากระบบ</span>
                    </button>
                </div>
            </aside>

            <!-- ==================== MAIN CONTENT ==================== -->
            <main class="flex-1 flex flex-col min-w-0 bg-slate-50/50">
                <!-- Desktop Topbar -->
                <header class="hidden md:flex items-center justify-between h-20 glass-header px-8 sticky top-0 z-50 premium-shadow">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <Link href="/dashboard" class="text-slate-400 hover:text-brand-600 transition-colors">
                                        <i data-lucide="home" class="w-4 h-4"></i>
                                    </Link>
                                </li>
                                <template v-if="title">
                                    <li><i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i></li>
                                    <li><span class="text-xs font-bold text-slate-800 uppercase tracking-widest">{{ title }}</span></li>
                                </template>
                            </ol>
                        </nav>
                    </div>

                    <div class="flex items-center gap-6">
                        <!-- Weather & Air Quality Widget -->
                        <div class="hidden md:flex items-center gap-2 xl:gap-4 px-3 xl:px-4 py-1.5 xl:py-2 bg-slate-50/50 backdrop-blur-sm rounded-2xl border border-slate-100 shadow-sm group hover:bg-white transition-all duration-300">
                            <!-- Weather -->
                            <div class="flex items-center gap-2 xl:gap-3">
                                <div class="relative w-8 h-8 xl:w-9 xl:h-9 flex items-center justify-center rounded-xl bg-gradient-to-br transition-all duration-500 group-hover:scale-110 shadow-lg shadow-opacity-20" :class="weather.bg">
                                    <i :data-lucide="weather.icon" class="w-4 h-4 xl:w-5 xl:h-5 text-white"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="hidden lg:block text-xs font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">สภาพอากาศ</span>
                                    <span class="text-sm xl:text-base font-bold text-slate-800 tracking-tight">{{ weather.temp }}°C</span>
                                </div>
                            </div>
                            <div class="w-px h-6 bg-slate-200/60 mx-1 xl:mx-0"></div>
                            <!-- PM 2.5 -->
                            <div class="flex items-center gap-2 xl:gap-3">
                                <div class="relative w-8 h-8 xl:w-9 xl:h-9 flex items-center justify-center rounded-xl bg-gradient-to-br transition-all duration-500 group-hover:scale-110 shadow-lg shadow-opacity-20" :class="aqi.bg">
                                    <i data-lucide="wind" class="w-4 h-4 xl:w-5 xl:h-5 text-white"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="hidden lg:block text-xs font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">ดัชนีคุณภาพอากาศ</span>
                                    <span class="text-sm xl:text-base font-bold tracking-tight flex items-center gap-1.5" :class="aqi.textColor">
                                        <span>{{ aqi.value }}</span>
                                        <span class="hidden lg:inline text-xs opacity-70">• {{ aqi.status }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Notifications -->
                        <div class="relative" ref="notifDropdownRef">
                            <button @click="notifOpen = !notifOpen" aria-label="แจ้งเตือน" class="relative p-2.5 text-slate-400 hover:text-brand-600 transition-all rounded-xl hover:bg-slate-50 focus:outline-none cursor-pointer focus-ring">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                <span v-if="notificationCount > 0" class="absolute top-2 right-2 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white animate-pulse"></span>
                            </button>
                            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-2">
                                <div v-show="notifOpen" class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 py-2 z-[9999] origin-top-right overflow-hidden border border-slate-100">
                                    <div class="px-4 py-3 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
                                        <span class="text-sm font-bold text-slate-800">การแจ้งเตือน</span>
                                        <button v-if="notificationCount > 0" @click="markNotificationsRead" class="text-xs text-brand-600 hover:text-brand-700 font-medium hover:underline">อ่านทั้งหมด</button>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto custom-scrollbar">
                                        <template v-if="notifications.length > 0">
                                            <div v-for="notif in notifications" :key="notif.id" class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="notifStatusColor(notif.data?.status)">
                                                            <i :data-lucide="notifStatusIcon(notif.data?.status)" class="w-4 h-4"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-bold text-slate-800">{{ notifStatusText(notif.data?.status) }}</p>
                                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ notif.data?.message }}</p>
                                                        <p v-if="notif.created_at_human" class="text-xs text-slate-400 mt-1">{{ notif.created_at_human }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <div v-else class="px-4 py-8 text-center">
                                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                                <i data-lucide="bell-off" class="w-5 h-5"></i>
                                            </div>
                                            <p class="text-sm text-slate-500">ไม่มีการแจ้งเตือนใหม่</p>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <div class="h-8 w-px bg-slate-100 hidden lg:block"></div>

                        <!-- Thai Date Display -->
                        <div class="text-right hidden xl:block">
                            <p class="text-sm font-bold text-slate-700">{{ thaiDate }}</p>
                        </div>

                        <div class="h-8 w-px bg-slate-100"></div>

                        <!-- Desktop User Profile Dropdown -->
                        <div class="relative" ref="profileDropdownRef">
                            <button @click="profileOpen = !profileOpen" aria-label="เมนูผู้บัญชาการ" class="flex items-center gap-3 focus:outline-none group cursor-pointer focus-ring p-1.5 rounded-2xl hover:bg-slate-50 transition-all">
                                <div class="text-right hidden md:block">
                                    <p class="text-sm font-bold text-slate-900 group-hover:text-brand-600 transition-colors tracking-tight">{{ user?.rank }} {{ user?.name }}</p>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none mt-1">{{ user?.department || 'กองบังคับการ' }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 p-0.5 shadow-md shadow-brand-500/20 group-hover:shadow-brand-500/40 transition-all overflow-hidden">
                                    <div class="w-full h-full rounded-lg bg-white flex items-center justify-center overflow-hidden">
                                        <img v-if="user?.avatar" :src="avatarUrl(user.avatar)" alt="โปรไฟล์" class="w-full h-full object-cover">
                                        <span v-else class="font-bold text-brand-600 text-sm">{{ user?.name?.charAt(0) }}</span>
                                    </div>
                                </div>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-300 group-hover:text-brand-600 transition-colors"></i>
                            </button>
                            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-4 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0 scale-100" leave-to-class="opacity-0 translate-y-4 scale-95">
                                <div v-show="profileOpen" class="absolute right-0 mt-4 w-64 bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 py-3 focus:outline-none z-[9999] origin-top-right overflow-hidden">
                                    <div class="px-6 py-4 border-b border-slate-50 mb-2 bg-slate-50/50">
                                        <p class="text-[10px] text-slate-400 uppercase tracking-[0.2em] font-bold mb-1">Authenticated As</p>
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ user?.email }}</p>
                                    </div>
                                    <div class="px-2 space-y-1">
                                        <Link href="/profile" class="flex items-center px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-brand-600 rounded-xl transition-all">
                                            <i data-lucide="user-cog" class="w-5 h-5 mr-3 opacity-50"></i> จัดการโปรไฟล์
                                        </Link>
                                        <button @click="logout" class="flex w-full items-center px-4 py-3 text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                            <i data-lucide="power" class="w-5 h-5 mr-3 opacity-50"></i> ออกจากระบบ
                                        </button>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </header>

                <!-- Content Area -->
                <div class="flex-1 p-4 md:p-8 scroll-smooth bg-[#fbfcfd]">
                    <div class="max-w-[95rem] mx-auto">
                        <slot />
                    </div>
                </div>
            </main>
        </div>

        <!-- ==================== AVATAR PROMPT MODAL ==================== -->
        <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showAvatarModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4 sm:px-6">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showAvatarModal = false"></div>
                <!-- Modal Card -->
                <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0 scale-95 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0">
                    <div class="relative bg-white rounded-[2rem] shadow-2xl max-w-md w-full overflow-hidden border border-slate-100 p-8 text-center">
                        <!-- Decoration -->
                        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-brand-500 to-indigo-600"></div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-brand-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
                        <!-- Icon -->
                        <div class="relative w-24 h-24 mx-auto mb-6">
                            <div class="absolute inset-0 bg-brand-100 rounded-full animate-ping opacity-20"></div>
                            <div class="relative w-full h-full bg-gradient-to-br from-brand-50 to-indigo-50 rounded-full flex items-center justify-center border border-brand-100 shadow-inner">
                                <i data-lucide="camera" class="w-10 h-10 text-brand-600"></i>
                            </div>
                            <div class="absolute bottom-1 right-1 w-8 h-8 bg-rose-500 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-white"></i>
                            </div>
                        </div>
                        <!-- Content -->
                        <h3 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">ยังไม่มีรูปโปรไฟล์</h3>
                        <p class="text-slate-500 mb-8 leading-relaxed">
                            เพื่อให้ระบบมีความสมบูรณ์และสวยงาม<br>
                            กรุณาอัปโหลดรูปถ่ายประจำตัวของคุณ
                        </p>
                        <!-- Actions -->
                        <div class="space-y-3">
                            <Link href="/profile" class="flex items-center justify-center w-full px-6 py-3.5 text-base font-bold text-white bg-gradient-to-r from-brand-600 to-indigo-600 rounded-xl hover:from-brand-700 hover:to-indigo-700 shadow-lg shadow-brand-500/30 transition-all hover:-translate-y-0.5 active:translate-y-0">
                                <i data-lucide="upload-cloud" class="w-5 h-5 mr-2"></i>
                                อัปโหลดรูปภาพตอนนี้
                            </Link>
                            <button @click="showAvatarModal = false" class="w-full px-6 py-2.5 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
                                ไว้ทีหลัง
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </div>
</template>

<style>
[x-cloak] { display: none !important; }

:root {
    --brand-50: #f5f7ff;
    --brand-100: #ebf0fe;
    --brand-500: #4f46e5;
    --brand-600: #4338ca;
    --glass-bg: rgba(255, 255, 255, 0.8);
    --glass-border: rgba(255, 255, 255, 0.5);
}

body {
    font-family: 'Sarabun', sans-serif;
    color: #1F2937;
}

.premium-shadow {
    box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 4px 10px -2px rgba(0, 0, 0, 0.02);
}

.glass-header {
    background: var(--glass-bg);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--glass-border);
}

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; transition: background 0.3s; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

.focus-ring:focus-visible {
    outline: 2px solid var(--brand-500);
    outline-offset: 2px;
}
</style>

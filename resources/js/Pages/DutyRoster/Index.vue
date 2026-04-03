<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({ days: Array, year: Number, month: Number, monthName: String, thaiYear: Number, seniorRosters: Array, monthlyFile: Object });

const isLoaded = ref(false);

const prevMonth = computed(() => {
    let m = props.month - 1, y = props.year;
    if (m < 1) { m = 12; y--; }
    return { year: y, month: m };
});
const nextMonth = computed(() => {
    let m = props.month + 1, y = props.year;
    if (m > 12) { m = 1; y++; }
    return { year: y, month: m };
});
const rosterCount = computed(() => (props.days || []).filter(d => d.roster).length);

function isToday(dateStr) {
    const today = new Date();
    const date = new Date(dateStr);
    return today.toDateString() === date.toDateString();
}
function isWeekend(dateStr) {
    const d = new Date(dateStr);
    return d.getDay() === 0 || d.getDay() === 6;
}

function getRowClasses(day) {
    const classes = [];
    if (isToday(day.date)) classes.push('bg-blue-50/60 shadow-[inset_4px_0_0_#3b82f6]');
    else if (isWeekend(day.date)) classes.push('bg-rose-50/40');
    else if (day.roster) classes.push('bg-white/50');
    return classes.join(' ');
}

function formatThaiDate(dateStr) {
    if (!dateStr) return '—';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    const day = date.getDate();
    const months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                   'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // Convert to Buddhist year
    return `${day} ${month} ${year}`;
}

function formatFileSize(bytes) {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function getFileIcon(name) {
    if (!name) return 'file';
    const ext = name.split('.').pop().toLowerCase();
    if (ext === 'pdf') return 'file-text';
    if (['jpg', 'jpeg', 'png'].includes(ext)) return 'image';
    if (['doc', 'docx'].includes(ext)) return 'file-type';
    if (['xls', 'xlsx'].includes(ext)) return 'table';
    return 'file';
}

function formatThaiDateRange(start, end) {
    if (!start || !end) return '—';
    const startDate = new Date(start);
    const endDate = new Date(end);
    
    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) return `${start} — ${end}`;
    
    const sDay = startDate.getDate();
    const sMonth = startDate.getMonth();
    const sYear = startDate.getFullYear();
    
    const eDay = endDate.getDate();
    const eMonth = endDate.getMonth();
    const eYear = endDate.getFullYear();
    
    const monthsShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                        
    const thaiYear = (y) => (y + 543).toString().slice(-2);

    if (sYear === eYear && sMonth === eMonth) {
        return `${sDay} - ${eDay} ${monthsShort[sMonth]}${thaiYear(sYear)}`;
    }
    
    if (sYear === eYear) {
        return `${sDay} ${monthsShort[sMonth]} - ${eDay} ${monthsShort[eMonth]}${thaiYear(sYear)}`;
    }
    
    return `${sDay} ${monthsShort[sMonth]}${thaiYear(sYear)} - ${eDay} ${monthsShort[eMonth]}${thaiYear(eYear)}`;
}

function getSwapInfo(day, position) {
    if (!day.guard_changes || !Array.isArray(day.guard_changes) || !day.roster) return null;
    
    // Show swap info if there's any guard change record for this date and position
    // regardless of who the current roster officer is — this prevents swap UI from
    // disappearing when an admin manually reassigns the roster after a guard change was submitted
    return day.guard_changes.find(gc => gc.duty_position === position) || null;
}

onMounted(() => { 
    setTimeout(() => { 
        if (window.lucide) window.lucide.createIcons(); 
        isLoaded.value = true;
    }, 100); 
});
</script>

<template>
    <AppLayout title="ตารางเวรประจำเดือน">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-slate-50 font-sans selection:bg-blue-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-blue-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[30%] left-[-10%] w-[500px] h-[500px] bg-indigo-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[10%] w-[700px] h-[700px] bg-cyan-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-8" :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-blue-100/50">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                        </span>
                        <span class="text-blue-700 text-[11px] font-black uppercase tracking-[0.2em]">Duty Roster Management</span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-3">
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">ตารางเวร</span> ประจำเดือน
                            </h1>
                            <p class="text-slate-500 font-medium text-lg flex items-center gap-2">
                                <i data-lucide="shield-check" class="w-5 h-5 text-indigo-500"></i>
                                ระบบจัดการเวรปฏิบัติหน้าที่ โรงเรียนพลาธิการ
                            </p>
                        </div>
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <!-- Legend -->
                            <div class="glass-card px-4 py-2 rounded-xl flex items-center gap-4 border border-white/60">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 shadow-sm"></span>
                                    <span class="text-xs font-bold text-slate-700">นายทหารเวร</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-pink-500 to-rose-600 shadow-sm"></span>
                                    <span class="text-xs font-bold text-slate-700">ผู้ช่วยนายทหารเวร</span>
                                </div>
                            </div>
                            <a :href="`/duty-roster/export-pdf?year=${year}&month=${month}`" target="_blank" 
                               class="glass-btn px-5 py-2.5 rounded-xl text-sm font-bold text-blue-700 hover:text-blue-800 hover:bg-white/80 flex items-center gap-2 transition-all">
                                <i data-lucide="printer" class="w-4 h-4"></i> พิมพ์ PDF
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Month Navigation -->
                <div class="glass-card rounded-[2rem] p-6 flex items-center justify-between mb-8 overflow-hidden relative group"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 150ms;">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50/40 via-transparent to-indigo-50/40 opacity-50 pointer-events-none"></div>
                    
                    <Link :href="`/duty-roster?year=${prevMonth.year}&month=${prevMonth.month}`" 
                          class="w-12 h-12 rounded-2xl flex items-center justify-center bg-white/60 text-slate-500 hover:bg-blue-50 hover:text-blue-600 border border-white/80 shadow-sm hover:shadow-md transition-all duration-300 relative z-10 group-hover:-translate-x-1">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </Link>
                    
                    <div class="text-center relative z-10">
                        <h2 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight mb-1">
                            {{ monthName }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">{{ thaiYear }}</span>
                        </h2>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50/50 border border-blue-100 mt-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                กำหนดเวรแล้ว <span class="text-emerald-500">{{ rosterCount }}</span> / <span class="text-slate-400">{{ days?.length || 0 }}</span> วัน
                            </p>
                        </div>
                    </div>
                    
                    <Link :href="`/duty-roster?year=${nextMonth.year}&month=${nextMonth.month}`" 
                          class="w-12 h-12 rounded-2xl flex items-center justify-center bg-white/60 text-slate-500 hover:bg-blue-50 hover:text-blue-600 border border-white/80 shadow-sm hover:shadow-md transition-all duration-300 relative z-10 group-hover:translate-x-1">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </Link>
                </div>

                <!-- Monthly File Attachment -->
                <section v-if="monthlyFile" class="mb-8"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 230ms;">
                    <div class="glass-card rounded-2xl p-5 border-l-4 border-l-violet-400">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-purple-100 text-violet-600 flex items-center justify-center border border-violet-200 shadow-sm">
                                    <i :data-lucide="getFileIcon(monthlyFile.name)" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-xs font-black text-violet-600 uppercase tracking-wider">ใบเวรยามประจำเดือน</span>
                                    </div>
                                    <h4 class="font-bold text-slate-800">{{ monthlyFile.name }}</h4>
                                    <p class="text-[10px] text-slate-400 font-medium">{{ formatFileSize(monthlyFile.size) }} · อัปเดตเมื่อ {{ monthlyFile.updated_at }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a :href="monthlyFile.url" target="_blank"
                                    class="glass-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-violet-700 hover:text-violet-800 transition-all border border-violet-100">
                                    <i data-lucide="external-link" class="w-4 h-4"></i> เปิดดู
                                </a>
                                <a :href="monthlyFile.url" download
                                    class="glass-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-all border border-emerald-100">
                                    <i data-lucide="download" class="w-4 h-4"></i> ดาวน์โหลด
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Senior Duty Rosters -->
                <section v-if="seniorRosters && seniorRosters.length > 0" class="mb-8"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 300ms;">
                    <div class="flex items-center gap-3 mb-4 pl-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-md">
                            <i data-lucide="star" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">นายทหารเวรอาวุโส</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        <div v-for="(sr, index) in seniorRosters" :key="sr.id" 
                             class="glass-card rounded-2xl p-5 border-l-4 border-l-amber-400 hover:-translate-y-1 transition-transform duration-300 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5 border border-amber-100 overflow-hidden shadow-sm">
                                <img v-if="sr.senior_officer?.avatar" :src="`/storage/${sr.senior_officer.avatar}`" class="w-full h-full object-cover">
                                <i v-else data-lucide="user-check" class="w-5 h-5 relative z-10"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 mb-0.5">{{ sr.senior_officer?.rank }} {{ sr.senior_officer?.name }}</h4>
                                <p class="text-xs font-bold text-slate-400 mb-2">นายทหารเวรอาวุโส</p>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 text-orange-600 text-[10px] font-black border border-orange-100">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    {{ formatThaiDateRange(sr.start_date, sr.end_date) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Daily Roster Table -->
                <main class="glass-card rounded-[2rem] overflow-hidden" :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 450ms;">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead>
                                <tr class="bg-white/40 border-b border-white/60">
                                    <th class="px-6 py-5 font-black text-slate-400 text-[11px] uppercase tracking-widest pl-8 w-1/3">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar-days" class="w-4 h-4"></i> วันที่
                                        </div>
                                    </th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[11px] uppercase tracking-widest w-1/3">
                                        <div class="flex items-center gap-2 text-blue-600">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> นายทหารเวร
                                        </div>
                                    </th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[11px] uppercase tracking-widest pr-8 w-1/3">
                                        <div class="flex items-center gap-2 text-pink-600">
                                            <span class="w-2 h-2 rounded-full bg-pink-500"></span> ผู้ช่วยนายทหารเวร
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/40 border-t border-white/60">
                                <tr v-for="(day, index) in days" :key="day.date" 
                                    class="group transition-colors duration-300 hover:bg-white/70"
                                    :class="getRowClasses(day)">
                                    
                                    <!-- Date column -->
                                    <td class="px-6 py-4 pl-8 align-middle">
                                        <div class="flex items-center gap-3 relative">
                                            <div v-if="isToday(day.date)" class="absolute -left-5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full animate-ping"></div>
                                            <div v-if="isToday(day.date)" class="absolute -left-5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-600 rounded-full shadow-[0_0_8px_rgba(37,99,235,0.8)]"></div>
                                            
                                            <span class="font-bold text-base" :class="isToday(day.date) ? 'text-blue-600' : isWeekend(day.date) ? 'text-rose-500' : 'text-slate-700'">
                                                {{ formatThaiDate(day.date) }}
                                            </span>
                                            <span v-if="isToday(day.date)" class="ml-2 px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-600 border border-blue-200 uppercase">วันนี้</span>
                                        </div>
                                    </td>

                                    <!-- Duty Officer column -->
                                    <td class="px-6 py-4 align-middle">
                                        <div v-if="day.roster?.duty_officer">
                                            <!-- Guard Change / Swap UI -->
                                            <div v-if="getSwapInfo(day, 'duty_officer')" class="inline-flex flex-col gap-2 p-2 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 shadow-sm relative overflow-hidden group/swap w-full max-w-[280px]">
                                                <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-amber-200/40 to-orange-200/40 rounded-full blur-xl -mr-8 -mt-8 pointer-events-none"></div>
                                                
                                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-black text-amber-700 bg-amber-100/90 border border-amber-200 w-fit shrink-0 tracking-widest uppercase mb-1 shadow-sm">
                                                    <i data-lucide="arrow-left-right" class="w-3 h-3"></i> แลกเปลี่ยนเวรยาม
                                                </div>
                                                
                                                <!-- Old User -->
                                                <div class="flex items-center gap-2 opacity-60 grayscale-[50%] transition-all duration-300 group-hover/swap:grayscale-0 group-hover/swap:opacity-100">
                                                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-black text-[10px] shrink-0 overflow-hidden relative">
                                                        <div class="absolute inset-0 bg-black/5 z-10 w-full h-full"></div>
                                                        <img v-if="getSwapInfo(day, 'duty_officer').user?.avatar" :src="`/storage/${getSwapInfo(day, 'duty_officer').user.avatar}`" class="w-full h-full object-cover relative z-0">
                                                        <span v-else class="relative z-0">{{ getSwapInfo(day, 'duty_officer').user?.name?.charAt(0) }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-slate-500 leading-tight line-through decoration-slate-400">{{ getSwapInfo(day, 'duty_officer').user?.rank }}</span>
                                                        <span class="text-[11px] font-bold text-slate-600 leading-tight line-through decoration-slate-400 truncate w-32 md:w-40" :title="getSwapInfo(day, 'duty_officer').user?.name">{{ getSwapInfo(day, 'duty_officer').user?.name }}</span>
                                                    </div>
                                                </div>

                                                <!-- New User (Current) -->
                                                <div class="flex flex-col gap-2 bg-white/80 backdrop-blur-md p-2 rounded-xl border border-amber-200/60 shadow-sm">
                                                    <div class="flex items-start gap-2">
                                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs shrink-0 border border-blue-200 overflow-hidden shadow-sm relative group-hover/swap:scale-105 transition-transform duration-300">
                                                            <div class="absolute inset-0 ring-2 ring-inset ring-amber-400 rounded-full z-10"></div>
                                                            <img v-if="getSwapInfo(day, 'duty_officer').replacement_user?.avatar" :src="`/storage/${getSwapInfo(day, 'duty_officer').replacement_user.avatar}`" class="w-full h-full object-cover relative z-0">
                                                            <span v-else class="relative z-0">{{ getSwapInfo(day, 'duty_officer').replacement_user?.name?.charAt(0) }}</span>
                                                        </div>
                                                        <div class="flex flex-col flex-1 min-w-0">
                                                            <span class="text-[10px] font-black text-blue-800 leading-tight">{{ getSwapInfo(day, 'duty_officer').replacement_user?.rank }}</span>
                                                            <span class="text-[13px] font-bold text-slate-800 leading-tight truncate" :title="getSwapInfo(day, 'duty_officer').replacement_user?.name">{{ getSwapInfo(day, 'duty_officer').replacement_user?.name }}</span>
                                                            <p v-if="getSwapInfo(day, 'duty_officer').remarks" class="mt-1.5 text-[10px] text-amber-800 bg-amber-100/60 border border-amber-200/50 px-2 py-1.5 rounded-lg leading-snug font-medium italic relative">
                                                                <i data-lucide="message-square-quote" class="absolute right-1 -top-1 w-3 h-3 text-amber-300 opacity-50"></i>
                                                                "{{ getSwapInfo(day, 'duty_officer').remarks }}"
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Standard Display -->
                                            <div v-else class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-blue-50/80 to-indigo-50/80 border border-blue-100 shadow-sm group-hover:shadow transition-shadow">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black text-xs shrink-0 border border-blue-200 overflow-hidden shadow-sm">
                                                    <img v-if="day.roster.duty_officer?.avatar" :src="`/storage/${day.roster.duty_officer.avatar}`" class="w-full h-full object-cover">
                                                    <span v-else>{{ day.roster.duty_officer.name?.charAt(0) }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-black text-blue-800 leading-tight">{{ day.roster.duty_officer.rank }}</span>
                                                    <span class="text-sm font-bold text-slate-800 leading-tight">{{ day.roster.duty_officer.name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-slate-300 font-bold px-4">—</div>
                                    </td>

                                    <!-- Assistant Duty Officer column -->
                                    <td class="px-6 py-4 pr-8 align-middle">
                                        <div v-if="day.roster?.assistant_duty_officer">
                                            <!-- Guard Change / Swap UI -->
                                            <div v-if="getSwapInfo(day, 'assistant_duty_officer')" class="inline-flex flex-col gap-2 p-2 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 shadow-sm relative overflow-hidden group/swap w-full max-w-[280px]">
                                                <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-amber-200/40 to-orange-200/40 rounded-full blur-xl -mr-8 -mt-8 pointer-events-none"></div>
                                                
                                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-black text-amber-700 bg-amber-100/90 border border-amber-200 w-fit shrink-0 tracking-widest uppercase mb-1 shadow-sm">
                                                    <i data-lucide="arrow-left-right" class="w-3 h-3"></i> แลกเปลี่ยนเวรยาม
                                                </div>
                                                
                                                <!-- Old User -->
                                                <div class="flex items-center gap-2 opacity-60 grayscale-[50%] transition-all duration-300 group-hover/swap:grayscale-0 group-hover/swap:opacity-100">
                                                    <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-black text-[10px] shrink-0 overflow-hidden relative">
                                                        <div class="absolute inset-0 bg-black/5 z-10 w-full h-full"></div>
                                                        <img v-if="getSwapInfo(day, 'assistant_duty_officer').user?.avatar" :src="`/storage/${getSwapInfo(day, 'assistant_duty_officer').user.avatar}`" class="w-full h-full object-cover relative z-0">
                                                        <span v-else class="relative z-0">{{ getSwapInfo(day, 'assistant_duty_officer').user?.name?.charAt(0) }}</span>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[9px] font-black text-slate-500 leading-tight line-through decoration-slate-400">{{ getSwapInfo(day, 'assistant_duty_officer').user?.rank }}</span>
                                                        <span class="text-[11px] font-bold text-slate-600 leading-tight line-through decoration-slate-400 truncate w-32 md:w-40" :title="getSwapInfo(day, 'assistant_duty_officer').user?.name">{{ getSwapInfo(day, 'assistant_duty_officer').user?.name }}</span>
                                                    </div>
                                                </div>

                                                <!-- New User (Current) -->
                                                <div class="flex flex-col gap-2 bg-white/80 backdrop-blur-md p-2 rounded-xl border border-amber-200/60 shadow-sm">
                                                    <div class="flex items-start gap-2">
                                                        <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-black text-xs shrink-0 border border-rose-200 overflow-hidden shadow-sm relative group-hover/swap:scale-105 transition-transform duration-300">
                                                            <div class="absolute inset-0 ring-2 ring-inset ring-amber-400 rounded-full z-10"></div>
                                                            <img v-if="getSwapInfo(day, 'assistant_duty_officer').replacement_user?.avatar" :src="`/storage/${getSwapInfo(day, 'assistant_duty_officer').replacement_user.avatar}`" class="w-full h-full object-cover relative z-0">
                                                            <span v-else class="relative z-0">{{ getSwapInfo(day, 'assistant_duty_officer').replacement_user?.name?.charAt(0) }}</span>
                                                        </div>
                                                        <div class="flex flex-col flex-1 min-w-0">
                                                            <span class="text-[10px] font-black text-rose-800 leading-tight">{{ getSwapInfo(day, 'assistant_duty_officer').replacement_user?.rank }}</span>
                                                            <span class="text-[13px] font-bold text-slate-800 leading-tight truncate" :title="getSwapInfo(day, 'assistant_duty_officer').replacement_user?.name">{{ getSwapInfo(day, 'assistant_duty_officer').replacement_user?.name }}</span>
                                                            <p v-if="getSwapInfo(day, 'assistant_duty_officer').remarks" class="mt-1.5 text-[10px] text-amber-800 bg-amber-100/60 border border-amber-200/50 px-2 py-1.5 rounded-lg leading-snug font-medium italic relative">
                                                                <i data-lucide="message-square-quote" class="absolute right-1 -top-1 w-3 h-3 text-amber-300 opacity-50"></i>
                                                                "{{ getSwapInfo(day, 'assistant_duty_officer').remarks }}"
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Standard Display -->
                                            <div v-else class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-rose-50/80 to-pink-50/80 border border-rose-100 shadow-sm group-hover:shadow transition-shadow">
                                                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-black text-xs shrink-0 border border-rose-200 overflow-hidden shadow-sm">
                                                    <img v-if="day.roster.assistant_duty_officer?.avatar" :src="`/storage/${day.roster.assistant_duty_officer.avatar}`" class="w-full h-full object-cover">
                                                    <span v-else>{{ day.roster.assistant_duty_officer.name?.charAt(0) }}</span>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-black text-rose-800 leading-tight">{{ day.roster.assistant_duty_officer.rank }}</span>
                                                    <span class="text-sm font-bold text-slate-800 leading-tight">{{ day.roster.assistant_duty_officer.name }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-slate-300 font-bold px-4">—</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Liquid Glass Aesthetic */
.glass-badge {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

.glass-btn {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05), inset 0 2px 4px rgba(255, 255, 255, 0.8);
}
.glass-btn:hover {
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 6px 20px -3px rgba(0, 0, 0, 0.08), inset 0 2px 4px rgba(255, 255, 255, 1);
}

.glass-card {
    background: rgba(255, 255, 255, 0.55);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.8);
}
.glass-card:hover {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(255, 255, 255, 0.9);
    box-shadow: 0 15px 50px -10px rgba(59, 130, 246, 0.08), inset 0 1px 0 rgba(255, 255, 255, 1);
}

/* Animations */
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
    animation: blob 15s infinite cubic-bezier(0.4, 0, 0.2, 1);
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.02);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(203, 213, 225, 0.5);
    border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.8);
}
</style>

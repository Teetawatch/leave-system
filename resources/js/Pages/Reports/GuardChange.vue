<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({ requests: Object, departments: Array, stats: Object });
const isLoaded = ref(false);

const dutyPositionMap = {
    senior_duty_officer: 'นายทหารเวรอาวุโส',
    duty_officer: 'นายทหารเวร',
    assistant_duty_officer: 'ผู้ช่วยนายทหารเวร',
};

const statusConfig = {
    fully_approved:   { label: 'อนุมัติสมบูรณ์',     bg: 'bg-emerald-50 text-emerald-600 border-emerald-100/50', icon: 'check-circle-2', dot: 'bg-emerald-500' },
    approved:         { label: 'ผู้แทนยืนยันแล้ว',   bg: 'bg-indigo-50 text-indigo-600 border-indigo-100/50', icon: 'check', dot: 'bg-indigo-500' },
    director_approved:{ label: 'รอ ผอ. อนุมัติ',     bg: 'bg-violet-50 text-violet-600 border-violet-100/50', icon: 'clock', dot: 'bg-violet-500' },
    pending:          { label: 'รอผู้แทนยืนยัน',     bg: 'bg-amber-50 text-amber-600 border-amber-100/50', icon: 'clock', dot: 'bg-amber-500' },
    rejected:         { label: 'ปฏิเสธ',              bg: 'bg-rose-50 text-rose-600 border-rose-100/50', icon: 'x-circle', dot: 'bg-rose-500' },
    cancelled:        { label: 'ยกเลิก',              bg: 'bg-slate-50 text-slate-500 border-slate-100/50', icon: 'ban', dot: 'bg-slate-400' },
};

function getStatusCls(s) { return statusConfig[s]?.bg || 'bg-amber-50 text-amber-600 border-amber-100/50'; }
function getStatusLabel(s) { return statusConfig[s]?.label || s; }
function getStatusIcon(s) { return statusConfig[s]?.icon || 'clock'; }
function getStatusDot(s) { return statusConfig[s]?.dot || 'bg-amber-500'; }

function getDutyPosition(p) { return dutyPositionMap[p] || p; }

function avatarUrl(avatar) {
    if (!avatar) return null;
    if (avatar.startsWith('http')) return avatar;
    return '/storage/' + avatar;
}

function formatDate(d) {
    if (!d) return '-';
    const date = new Date(d);
    if (isNaN(date.getTime())) return d;
    return date.toLocaleDateString('th-TH', { year: '2-digit', month: 'short', day: 'numeric' });
}

onMounted(() => { 
    setTimeout(() => { 
        if (window.lucide) window.lucide.createIcons(); 
        isLoaded.value = true;
    }, 150); 
});
</script>

<template>
    <AppLayout title="รายงานเปลี่ยนยาม">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-slate-50 font-sans selection:bg-indigo-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-indigo-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[30%] left-[-10%] w-[500px] h-[500px] bg-emerald-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[10%] w-[700px] h-[700px] bg-amber-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative z-10 max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-12" :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                        <div>
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-indigo-100/50">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
                                </span>
                                <span class="text-indigo-700 text-[11px] font-black uppercase tracking-[0.2em]">Guard Change System</span>
                            </div>
                            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-4">
                                รายงานขอ <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">เปลี่ยนเวร</span>
                            </h1>
                            <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">
                                สรุปข้อมูลการขอเปลี่ยนเวรยามทั้งหมดในระบบ พร้อมรายงานสถานะแบบเรียลไทม์
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Total requests -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 100ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-blue-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white text-indigo-600 flex items-center justify-center border border-white shadow-sm group-hover:shadow-md transition-all duration-300">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 flex items-center justify-center">
                                    <i data-lucide="layers" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-2">คำขอทั้งหมด</span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-5xl font-black text-slate-800 tracking-tight mb-4 group-hover:text-indigo-600 transition-colors">{{ stats?.total || 0 }}</h3>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-full rounded-full w-full"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 150ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white text-emerald-600 flex items-center justify-center border border-white shadow-sm group-hover:shadow-md transition-all duration-300">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center">
                                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-2">อนุมัติสมบูรณ์</span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-5xl font-black text-slate-800 tracking-tight mb-4 group-hover:text-emerald-600 transition-colors">{{ stats?.approved || 0 }}</h3>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-full rounded-full transition-all duration-1000" :style="{ width: ((stats?.total || 1) > 0 ? ((stats?.approved || 0) / (stats?.total || 1)) * 100 : 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 200ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white text-amber-600 flex items-center justify-center border border-white shadow-sm group-hover:shadow-md transition-all duration-300">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 flex items-center justify-center">
                                    <i data-lucide="clock" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-2">รอดำเนินการ</span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-5xl font-black text-slate-800 tracking-tight mb-4 group-hover:text-amber-600 transition-colors">{{ stats?.pending || 0 }}</h3>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-full rounded-full transition-all duration-1000" :style="{ width: ((stats?.total || 1) > 0 ? ((stats?.pending || 0) / (stats?.total || 1)) * 100 : 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 250ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-rose-500 to-pink-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-6 relative z-10">
                            <div class="w-14 h-14 rounded-[1.25rem] bg-white text-rose-600 flex items-center justify-center border border-white shadow-sm group-hover:shadow-md transition-all duration-300">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-50 to-pink-50 flex items-center justify-center">
                                    <i data-lucide="x-circle" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-2">ปฏิเสธ / ยกเลิก</span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-5xl font-black text-slate-800 tracking-tight mb-4 group-hover:text-rose-600 transition-colors">{{ stats?.rejected || 0 }}</h3>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-rose-400 to-pink-500 h-full rounded-full transition-all duration-1000" :style="{ width: ((stats?.total || 1) > 0 ? ((stats?.rejected || 0) / (stats?.total || 1)) * 100 : 0) + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="glass-card rounded-[2.5rem] overflow-hidden" 
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 350ms;">
                    <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-white/40 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                                <i data-lucide="arrow-right-left" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">รายการคำขอทั้งหมด</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Guard Change Roster</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center justify-center px-4 py-1.5 bg-white border border-slate-100 text-slate-600 font-black text-sm shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)] rounded-xl">{{ stats?.total || 0 }} รายการ</span>
                    </div>

                    <div v-if="(requests?.data || []).length === 0" class="p-20 text-center">
                        <div class="w-20 h-20 bg-white/50 border border-white rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-sm">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-600 mb-2">ไม่พบข้อมูลคำขอ</h4>
                        <p class="text-sm font-bold text-slate-400">ระบบยังไม่มีรายการขอเปลี่ยนเวรในขณะนี้</p>
                    </div>

                    <div v-else class="divide-y divide-white/40 border-t border-white/60 bg-white/10">
                        <div v-for="(r, index) in (requests?.data || [])" :key="r.id" 
                             class="p-6 md:p-8 hover:bg-white/60 transition-colors duration-300 group flex flex-col xl:flex-row gap-6 xl:items-center relative"
                             :style="`animation-delay: ${index * 50}ms;`">
                            
                            <!-- Detail 1: ID & Status -->
                            <div class="flex xl:flex-col items-center xl:items-start justify-between xl:justify-center gap-4 xl:w-48 flex-shrink-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50/80 border border-indigo-100 text-indigo-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                        <i data-lucide="shield" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase mb-0.5">ID: #{{ r.id }}</p>
                                        <p class="text-xs font-black text-slate-800">{{ getDutyPosition(r.duty_position) }}</p>
                                    </div>
                                </div>
                                <div class="px-3 py-1.5 rounded-full text-[10px] font-black inline-flex items-center gap-1.5 border shadow-sm" :class="getStatusCls(r.status)">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDot(r.status)"></span>
                                    <span>{{ getStatusLabel(r.status) }}</span>
                                </div>
                            </div>

                            <!-- Detail 2: People Swift & Approvals -->
                            <div class="flex flex-col gap-3 flex-1 min-w-0">
                                <div class="flex flex-col md:flex-row items-center gap-3 w-full bg-white/40 backdrop-blur-sm rounded-[1.5rem] p-3 border border-white shadow-sm">
                                    <!-- User -->
                                    <div class="flex items-center gap-3 flex-1 w-full bg-white p-2.5 rounded-[1.25rem] shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)] border border-slate-50">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-500 border border-slate-100 flex items-center justify-center font-black text-xs overflow-hidden flex-shrink-0 shadow-sm">
                                            <img v-if="avatarUrl(r.user?.avatar)" :src="avatarUrl(r.user?.avatar)" class="w-full h-full object-cover">
                                            <span v-else>{{ r.user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded text-[8px] font-black uppercase">ผู้ขอ</span>
                                                <p class="font-bold text-slate-800 truncate text-xs">{{ r.user?.rank }}{{ r.user?.name }}</p>
                                            </div>
                                            <p class="text-[10px] text-slate-400 font-bold truncate">{{ r.user?.position || r.user?.department || '-' }}</p>
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    <div class="w-6 h-6 rounded-full bg-white shadow flex items-center justify-center text-slate-400 md:-mx-4 z-10 flex-shrink-0 relative">
                                        <i data-lucide="arrow-right" class="w-3 h-3 md:block hidden"></i>
                                        <i data-lucide="arrow-down" class="w-3 h-3 md:hidden block"></i>
                                    </div>

                                    <!-- Replacement -->
                                    <div class="flex items-center gap-3 flex-1 w-full bg-white p-2.5 rounded-[1.25rem] shadow-[inset_0_1px_2px_rgba(0,0,0,0.02)] border border-slate-50">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 border border-indigo-100 flex items-center justify-center font-black text-xs overflow-hidden flex-shrink-0 shadow-sm">
                                            <img v-if="avatarUrl(r.replacement_user?.avatar)" :src="avatarUrl(r.replacement_user?.avatar)" class="w-full h-full object-cover">
                                            <span v-else>{{ r.replacement_user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[8px] font-black uppercase">รับแทน</span>
                                                <p class="font-bold text-slate-800 truncate text-xs">{{ r.replacement_user?.rank }}{{ r.replacement_user?.name }}</p>
                                            </div>
                                            <p class="text-[10px] text-slate-400 font-bold truncate">{{ r.replacement_user?.position || r.replacement_user?.department || '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Approvals Timeline & Note -->
                                <div class="flex flex-wrap items-center gap-1.5 px-2 mt-1" v-if="r.approved_at || r.director_approved_at || r.final_approved_at || r.remarks">
                                    <div v-if="r.remarks" class="px-2 py-1 rounded-md bg-white border border-slate-100 flex items-center gap-1 text-[9px] font-black text-slate-600 max-w-[150px] truncate shadow-sm" :title="r.remarks">
                                        <i data-lucide="message-square" class="w-3 h-3 text-amber-500 flex-shrink-0"></i> <span class="truncate">{{ r.remarks }}</span>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300" v-if="r.remarks && (r.approved_at || r.director_approved_at || r.final_approved_at)"></i>
                                    
                                    <div v-if="r.approved_at" class="flex flex-col">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase">ยืนยันแล้ว</span>
                                        <span class="text-[9px] font-black text-indigo-600">{{ formatDate(r.approved_at) }}</span>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300" v-if="r.approved_at && (r.director_approved_at || r.final_approved_at)"></i>
                                    
                                    <div v-if="r.director_approved_at" class="flex flex-col">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase">รอง ผอ. อนุมัติ</span>
                                        <span class="text-[9px] font-black text-violet-600">{{ formatDate(r.director_approved_at) }}</span>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300" v-if="r.director_approved_at && r.final_approved_at"></i>
                                    
                                    <div v-if="r.final_approved_at" class="flex flex-col">
                                        <span class="text-[8px] font-bold text-slate-400 uppercase">ผอ. อนุมัติ</span>
                                        <span class="text-[9px] font-black text-emerald-600">{{ formatDate(r.final_approved_at) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail 3: Meta & Action -->
                            <div class="flex flex-col sm:flex-row xl:flex-col items-center sm:items-end justify-between xl:justify-center gap-3 xl:w-48 flex-shrink-0 mt-4 xl:mt-0 pt-4 xl:pt-0 border-t border-white xl:border-0 pl-0 xl:pl-6 xl:border-l xl:border-slate-200/50">
                                <div class="flex flex-col gap-1 w-full">
                                    <div class="flex items-center justify-between text-[11px] bg-white/50 px-3 py-1.5 rounded-lg border border-white">
                                        <span class="font-bold text-slate-400">วันที่เวร:</span>
                                        <span class="font-black text-slate-800">{{ formatDate(r.duty_date) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px] bg-white/50 px-3 py-1.5 rounded-lg border border-white">
                                        <span class="font-bold text-slate-400">ยื่นเมื่อ:</span>
                                        <span class="font-black text-slate-800">{{ formatDate(r.created_at) }}</span>
                                    </div>
                                </div>
                                <a :href="`/guard-change/${r.id}/pdf`" target="_blank"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 hover:from-slate-700 hover:to-slate-800 text-white rounded-xl text-xs font-black shadow-md transition-all group/btn mt-1">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5 group-hover/btn:-translate-y-0.5 transition-transform text-slate-300"></i>
                                    <span>ใบคำขอ PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests?.links && requests.links.length > 3" class="mt-10 flex justify-center pb-8"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 500ms;">
                    <div class="bg-white/60 backdrop-blur-xl p-2 rounded-[1.5rem] shadow-sm border border-white flex gap-1">
                        <template v-for="(link, i) in requests.links" :key="i">
                            <Link v-if="link.url" :href="link.url" class="px-4 py-2.5 rounded-xl text-xs font-black transition-all"
                                :class="link.active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-transparent text-slate-500 hover:bg-white border border-transparent hover:border-slate-100 hover:text-slate-700'" v-html="link.label" />
                            <span v-else class="px-3 py-2.5 text-xs text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>
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
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 50px -10px rgba(99, 102, 241, 0.1), inset 0 1px 0 rgba(255, 255, 255, 1);
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
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>

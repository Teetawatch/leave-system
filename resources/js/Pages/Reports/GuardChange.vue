<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({ requests: Object, departments: Array, stats: Object });

const dutyPositionMap = {
    senior_duty_officer: 'นายทหารเวรอาวุโส',
    duty_officer: 'นายทหารเวร',
    assistant_duty_officer: 'ผู้ช่วยนายทหารเวร',
};

const statusConfig = {
    fully_approved:   { label: 'อนุมัติสมบูรณ์',     bg: 'bg-emerald-100', text: 'text-emerald-700', icon: 'check-circle-2' },
    approved:         { label: 'ผู้แทนยืนยันแล้ว',   bg: 'bg-violet-100', text: 'text-violet-700', icon: 'check' },
    director_approved:{ label: 'รอ ผอ. อนุมัติ',     bg: 'bg-amber-100', text: 'text-amber-700', icon: 'clock' },
    pending:          { label: 'รอผู้แทนยืนยัน',     bg: 'bg-amber-100', text: 'text-amber-700', icon: 'clock' },
    rejected:         { label: 'ปฏิเสธ',              bg: 'bg-rose-100', text: 'text-rose-700', icon: 'x-circle' },
    cancelled:        { label: 'ยกเลิก',              bg: 'bg-slate-100', text: 'text-slate-600', icon: 'ban' },
};

function getStatusCls(s) { return statusConfig[s]?.bg + ' ' + statusConfig[s]?.text || 'bg-amber-100 text-amber-700'; }
function getStatusLabel(s) { return statusConfig[s]?.label || s; }
function getStatusIcon(s) { return statusConfig[s]?.icon || 'clock'; }

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

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 150); });
</script>

<template>
    <AppLayout title="รายงานเปลี่ยนยาม">
        <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-emerald-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

            <!-- Header -->
            <div class="relative pt-16 pb-32">
                <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                        <div>
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                ระบบสับเปลี่ยนกำลังพล
                            </div>
                            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-6">
                                รายงานขอ <span class="text-indigo-600">เปลี่ยนเวร</span>
                            </h1>
                            <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">สรุปข้อมูลการขอเปลี่ยนเวรยามทั้งหมดในระบบ ติดตามสถานะและข้อมูลการสับเปลี่ยนได้อย่างรวดเร็วและครบถ้วน</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    <!-- Total requests -->
                    <div class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-500 shadow-sm border border-indigo-100 group-hover:rotate-12">
                                <i data-lucide="layers" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">คำขอทั้งหมด</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ stats?.total || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner"><div class="bg-indigo-500 h-full rounded-full w-full"></div></div>
                    </div>
                    <!-- Approved -->
                    <div class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm border border-emerald-100 group-hover:rotate-12">
                                <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">อนุมัติสมบูรณ์</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ stats?.approved || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="bg-emerald-500 h-full rounded-full" :style="{ width: ((stats?.total || 1) > 0 ? ((stats?.approved || 0) / (stats?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                    <!-- Pending -->
                    <div class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all duration-500 shadow-sm border border-amber-100 group-hover:rotate-12">
                                <i data-lucide="clock" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">รอดำเนินการ</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ stats?.pending || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="bg-amber-500 h-full rounded-full" :style="{ width: ((stats?.total || 1) > 0 ? ((stats?.pending || 0) / (stats?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                    <!-- Rejected -->
                    <div class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-rose-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-all duration-500 shadow-sm border border-rose-100 group-hover:rotate-12">
                                <i data-lucide="x-circle" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">ปฏิเสธ / ยกเลิก</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ stats?.rejected || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="bg-rose-500 h-full rounded-full" :style="{ width: ((stats?.total || 1) > 0 ? ((stats?.rejected || 0) / (stats?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="glass-panel rounded-[3.5rem] overflow-hidden shadow-2xl shadow-slate-900/5">
                    <div class="p-10 pb-6 border-b border-slate-100 bg-slate-50/30">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 rounded-[1.5rem] bg-slate-900 text-white flex items-center justify-center shadow-lg"><i data-lucide="arrow-right-left" class="w-7 h-7"></i></div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">รายการคำขอเปลี่ยนเวร</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">ALL GUARD CHANGE REQUESTS</p>
                            </div>
                            <span class="ml-auto bg-white border border-slate-100 text-slate-500 text-xs font-black shadow-sm px-4 py-2 rounded-xl">{{ stats?.total || 0 }} รายการ</span>
                        </div>
                    </div>

                    <div v-if="(requests?.data || []).length === 0" class="p-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-600 mb-2">ไม่พบข้อมูลการขอเปลี่ยนเวร</h4>
                        <p class="text-sm font-bold text-slate-400">ยังไม่มีรายการที่ตรงกับเงื่อนไขการค้นหา</p>
                    </div>

                    <div v-else class="divide-y divide-slate-50">
                        <div v-for="r in (requests?.data || [])" :key="r.id" class="p-8 hover:bg-white/60 transition-colors group flex flex-col xl:flex-row gap-8 xl:items-center">
                            <!-- Detail 1: ID & Status -->
                            <div class="flex xl:flex-col items-center xl:items-start justify-between xl:justify-center gap-4 xl:w-48 flex-shrink-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner">
                                        <i data-lucide="shield" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase mb-0.5">คำขอ #{{ r.id }}</p>
                                        <p class="text-sm font-black text-slate-800">{{ getDutyPosition(r.duty_position) }}</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1.5 rounded-full text-[10px] font-black inline-flex items-center gap-1.5 border" :class="[getStatusCls(r.status), 'border-current/20']">
                                    <i :data-lucide="getStatusIcon(r.status)" class="w-3 h-3"></i>
                                    {{ getStatusLabel(r.status) }}
                                </span>
                            </div>

                            <!-- Detail 2: People Swift & Approvals -->
                            <div class="flex flex-col gap-3 flex-1 min-w-0">
                                <div class="flex flex-col md:flex-row items-center gap-4 w-full bg-slate-50/50 rounded-[2rem] p-4 border border-slate-100/50">
                                    <!-- User -->
                                    <div class="flex items-center gap-4 flex-1 w-full bg-white p-3 rounded-2xl shadow-sm border border-slate-100/50">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0 shadow-inner">
                                            <img v-if="avatarUrl(r.user?.avatar)" :src="avatarUrl(r.user?.avatar)" class="w-full h-full object-cover">
                                            <span v-else>{{ r.user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="px-2 py-0.5 bg-violet-100 text-violet-700 rounded text-[9px] font-black uppercase">ผู้ขอ</span>
                                                <p class="font-black text-slate-800 truncate text-sm">{{ r.user?.rank }}{{ r.user?.name }}</p>
                                            </div>
                                            <p class="text-[11px] text-slate-500 font-bold truncate">{{ r.user?.position || r.user?.department || '-' }}</p>
                                        </div>
                                    </div>

                                    <!-- Arrow -->
                                    <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-300 md:-mx-6 z-10 flex-shrink-0 relative">
                                        <i data-lucide="arrow-right" class="w-4 h-4 md:block hidden"></i>
                                        <i data-lucide="arrow-down" class="w-4 h-4 md:hidden block"></i>
                                    </div>

                                    <!-- Replacement -->
                                    <div class="flex items-center gap-4 flex-1 w-full bg-white p-3 rounded-2xl shadow-sm border border-slate-100/50">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0 shadow-inner">
                                            <img v-if="avatarUrl(r.replacement_user?.avatar)" :src="avatarUrl(r.replacement_user?.avatar)" class="w-full h-full object-cover">
                                            <span v-else>{{ r.replacement_user?.name?.charAt(0) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-black uppercase">ผู้รับแทน</span>
                                                <p class="font-black text-slate-800 truncate text-sm">{{ r.replacement_user?.rank }}{{ r.replacement_user?.name }}</p>
                                            </div>
                                            <p class="text-[11px] text-slate-500 font-bold truncate">{{ r.replacement_user?.position || r.replacement_user?.department || '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Approvals Timeline & Note -->
                                <div class="flex flex-wrap items-center gap-2 px-2" v-if="r.approved_at || r.director_approved_at || r.final_approved_at || r.remarks">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-1">เพิ่มเติม:</span>
                                    
                                    <div v-if="r.remarks" class="px-2.5 py-1 rounded-lg bg-orange-50 border border-orange-100 flex items-center gap-1.5 text-[10px] font-black text-orange-700 max-w-[200px] truncate" :title="r.remarks">
                                        <i data-lucide="message-square" class="w-3 h-3 flex-shrink-0"></i> <span class="truncate">{{ r.remarks }}</span>
                                    </div>
                                    <div v-if="r.approved_at" class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center gap-1.5 text-[10px] font-black text-emerald-700">
                                        <i data-lucide="check" class="w-3 h-3 flex-shrink-0"></i> ผู้แทนยืนยัน: {{ formatDate(r.approved_at) }}
                                    </div>
                                    <div v-if="r.director_approved_at" class="px-2.5 py-1 rounded-lg bg-violet-50 border border-violet-100 flex items-center gap-1.5 text-[10px] font-black text-violet-700">
                                        <i data-lucide="shield-check" class="w-3 h-3 flex-shrink-0"></i> รอง ผอ.: {{ formatDate(r.director_approved_at) }}
                                    </div>
                                    <div v-if="r.final_approved_at" class="px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center gap-1.5 text-[10px] font-black text-indigo-700">
                                        <i data-lucide="award" class="w-3 h-3 flex-shrink-0"></i> ผอ.: {{ formatDate(r.final_approved_at) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Detail 3: Meta & Action -->
                            <div class="flex flex-col sm:flex-row xl:flex-col items-center sm:items-end justify-between xl:justify-center gap-4 xl:w-48 flex-shrink-0 mt-4 xl:mt-0 pt-4 xl:pt-0 border-t border-slate-100 xl:border-0 pl-0 xl:pl-4 xl:border-l">
                                <div class="flex flex-col gap-2 w-full">
                                    <div class="flex items-center gap-2 text-xs">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="font-bold text-slate-500">วันที่เวร:</span>
                                        <span class="font-black text-slate-800 ml-auto">{{ formatDate(r.duty_date) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                        <span class="font-bold text-slate-500">ยื่นคำขอ:</span>
                                        <span class="font-black text-slate-800 ml-auto">{{ formatDate(r.created_at) }}</span>
                                    </div>
                                </div>
                                <a :href="`/guard-change/${r.id}/pdf`" target="_blank"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black shadow-md transition-all group/btn mt-2 border border-slate-700">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5 group-hover/btn:-translate-y-0.5 transition-transform text-slate-300"></i>
                                    <span>ใบคำขอ PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests?.links && requests.links.length > 3" class="mt-12 flex justify-center">
                    <div class="bg-white/80 backdrop-blur-md p-3 rounded-[2rem] shadow-xl border border-white/50 flex gap-1">
                        <template v-for="link in requests.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-xl text-sm font-black transition-all"
                                :class="link.active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                            <span v-else class="px-4 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.premium-bg {
    min-height: 100vh;
    background: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.03) 0%, transparent 40%);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
}
.stats-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.stats-card:hover { transform: translateY(-8px); box-shadow: 0 30px 60px -15px rgba(15, 23, 42, 0.1); }
</style>

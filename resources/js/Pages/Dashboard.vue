<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    vacationBalance: Object,
    sickUsageDays: Number,
    sickUsageCount: Number,
    personalUsageDays: Number,
    personalUsageCount: Number,
    pendingCount: Number,
    todayLeaves: Array,
    recentRequests: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

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

onMounted(() => {
    updateClock();
    clockInterval = setInterval(updateClock, 1000);
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});

const vacPercent = computed(() => {
    const total = props.vacationBalance?.total_days || 1;
    const remaining = props.vacationBalance?.remaining_days || 0;
    return Math.min(100, Math.max(0, (remaining / total) * 100));
});

function statusMeta(req) {
    const slug = req.leave_type?.slug || '';
    const meta = slug === 'sick' ? { bg: 'bg-rose-50', text: 'text-rose-500', icon: 'thermometer' }
        : slug === 'vacation' ? { bg: 'bg-brand-50', text: 'text-brand-500', icon: 'palm-tree' }
        : { bg: 'bg-amber-50', text: 'text-amber-500', icon: 'briefcase-business' };

    const status = req.status === 'approved' ? { cls: 'bg-emerald-500 text-white', label: 'อนุมัติเรียบร้อย', dot: 'bg-emerald-200' }
        : req.status === 'rejected' ? { cls: 'bg-rose-500 text-white', label: 'ปฏิเสธคำขอ', dot: 'bg-rose-200' }
        : req.status === 'cancelled' ? { cls: 'bg-slate-200 text-slate-500', label: 'ยกเลิกรายการ', dot: 'bg-slate-300' }
        : { cls: 'bg-amber-100 text-amber-700', label: 'รอการตรวจสอบ', dot: 'bg-amber-400' };

    return { meta, status };
}

function avatarUrl(avatar) {
    return avatar ? `/storage/${avatar}` : null;
}
</script>

<template>
    <AppLayout title="แผงควบคุมหลัก (Smart Dashboard)">
        <div class="relative min-h-screen overflow-hidden font-thai">
            <div class="relative z-10 max-w-[1700px] mx-auto">

                <!-- Header -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 mb-12 items-center">
                    <div class="xl:col-span-7 space-y-4">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1]">
                            ยินดีต้อนรับกลับมา,<br>
                            <span class="text-slate-900">{{ user?.rank }} {{ user?.name }}</span>
                        </h1>
                        <p class="text-slate-400 font-bold text-lg flex items-center gap-3">
                            <i data-lucide="building-2" class="w-5 h-5 text-brand-500/50"></i>
                            {{ user?.department || 'ไม่มีสังกัด' }} • โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ
                        </p>
                    </div>
                    <div class="xl:col-span-5">
                        <div class="glass-panel rounded-[3rem] p-8 flex flex-col md:flex-row items-center justify-between gap-8 group">
                            <div class="text-center md:text-left space-y-2">
                                <p class="text-[10px] font-black text-brand-500 uppercase tracking-[0.3em]">เวลาปัจจุบัน</p>
                                <div class="font-outfit text-5xl font-black text-slate-900 tabular-nums">{{ clockTime }}</div>
                            </div>
                            <div class="relative">
                                <div class="w-24 h-24 rounded-[2.5rem] bg-indigo-50 border-4 border-white shadow-xl overflow-hidden">
                                    <img v-if="user?.avatar" :src="avatarUrl(user.avatar)" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center font-bold text-3xl text-brand-500 uppercase">
                                        {{ user?.name?.charAt(0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-8 space-y-8">

                        <!-- Quick Actions -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <Link href="/leave-request/create" class="group relative bg-brand-600 rounded-[2.5rem] p-8 text-white overflow-hidden shadow-2xl shadow-brand-600/30 transition-all hover:scale-[1.02] hover:-rotate-1 active:scale-95">
                                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-[40px] group-hover:scale-150 transition-transform"></div>
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="space-y-4">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-white text-brand-600 flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                                            <i data-lucide="plus-circle" class="w-8 h-8"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-black tracking-tight leading-none mb-2">ยื่นใบลาใหม่</h3>
                                            <p class="text-brand-100/70 text-sm font-medium">ทำรายการขออนุญาตลาหยุดระบบดิจิทัล</p>
                                        </div>
                                    </div>
                                    <i data-lucide="arrow-up-right" class="w-10 h-10 text-white/30 group-hover:translate-x-2 group-hover:-translate-y-2 transition-transform"></i>
                                </div>
                            </Link>
                            <Link href="/guard-change/create" class="group relative bg-slate-900 rounded-[2.5rem] p-8 text-white overflow-hidden shadow-2xl shadow-slate-900/30 transition-all hover:scale-[1.02] hover:rotate-1 active:scale-95">
                                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-[40px] group-hover:scale-150 transition-transform"></div>
                                <div class="relative z-10 flex items-center justify-between">
                                    <div class="space-y-4">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-white/10 text-brand-400 flex items-center justify-center shadow-lg group-hover:-rotate-12 transition-transform">
                                            <i data-lucide="shield-check" class="w-8 h-8"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-black tracking-tight leading-none mb-2">ขอเปลี่ยนยาม</h3>
                                            <p class="text-slate-400 text-sm font-medium">ส่งรายการขอแลกเปลี่ยนเวรยามรักษาการณ์</p>
                                        </div>
                                    </div>
                                    <i data-lucide="arrow-up-right" class="w-10 h-10 text-white/20 group-hover:translate-x-2 group-hover:-translate-y-2 transition-transform"></i>
                                </div>
                            </Link>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Vacation -->
                            <div class="bento-card group">
                                <div class="flex items-center justify-between mb-8">
                                    <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i data-lucide="palm-tree" class="w-7 h-7"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-brand-50 text-brand-600 px-4 py-1.5 rounded-full">พักผ่อนคงเหลือ</span>
                                </div>
                                <div class="flex items-baseline gap-3 mb-6">
                                    <span class="stat-number text-6xl font-black text-slate-900">{{ vacationBalance ? (vacationBalance.remaining_days || 0) : 0 }}</span>
                                    <span class="text-slate-300 font-bold text-lg">/ {{ vacationBalance ? (vacationBalance.total_days || 0) : 0 }} วัน</span>
                                </div>
                                <div class="h-3 bg-slate-100 rounded-full overflow-hidden shadow-inner">
                                    <div class="h-full bg-gradient-to-r from-brand-400 to-brand-600 transition-all duration-1000 ease-out shadow-[0_0_15px_rgba(14,165,233,0.4)]" :style="{ width: vacPercent + '%' }"></div>
                                </div>
                            </div>
                            <!-- Sick -->
                            <div class="bento-card group">
                                <div class="flex items-center justify-between mb-8 text-rose-500">
                                    <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i data-lucide="thermometer-sun" class="w-7 h-7"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-rose-50 text-rose-500 px-4 py-1.5 rounded-full">ลาป่วยปีนี้</span>
                                </div>
                                <div class="flex items-baseline gap-3 mb-2">
                                    <span class="stat-number text-6xl font-black text-slate-800">{{ sickUsageCount }}</span>
                                    <span class="text-slate-300 font-bold text-lg">ครั้ง</span>
                                </div>
                                <p class="text-xs font-bold text-slate-400 flex items-center gap-2">
                                    <i data-lucide="clock-3" class="w-3.5 h-3.5 opacity-50"></i>
                                    รวมระยะเวลา {{ sickUsageDays || 0 }} วัน
                                </p>
                            </div>
                            <!-- Personal -->
                            <div class="bento-card group">
                                <div class="flex items-center justify-between mb-8 text-amber-500">
                                    <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i data-lucide="award" class="w-7 h-7"></i>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] bg-amber-50 text-amber-600 px-4 py-1.5 rounded-full">ลากิจส่วนตัว</span>
                                </div>
                                <div class="flex items-baseline gap-3 mb-2">
                                    <span class="stat-number text-6xl font-black text-slate-800">{{ personalUsageCount }}</span>
                                    <span class="text-slate-300 font-bold text-lg">ครั้ง</span>
                                </div>
                                <p class="text-xs font-bold text-slate-400 flex items-center gap-2">
                                    <i data-lucide="clock-3" class="w-3.5 h-3.5 opacity-50"></i>
                                    รวมระยะเวลา {{ personalUsageDays || 0 }} วัน
                                </p>
                            </div>
                        </div>

                        <!-- Recent Requests -->
                        <div class="bento-card !p-0">
                            <div class="p-10 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-50/50">
                                <div class="flex items-center gap-5">
                                    <div class="w-16 h-16 rounded-3xl bg-slate-900 text-white flex items-center justify-center shadow-xl rotate-3 shrink-0">
                                        <i data-lucide="layout-list" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-1">ความเคลื่อนไหวล่าสุด</h3>
                                        <p class="text-xs text-slate-400 uppercase tracking-[0.2em] font-black">ประวัติการทำรายการของคุณ</p>
                                    </div>
                                </div>
                                <Link href="/leave-request" class="inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white text-brand-600 font-black text-xs uppercase tracking-widest shadow-sm hover:shadow-md hover:-translate-y-1 transition-all border border-slate-100 active:scale-95">
                                    <span>ดูประวัติทั้งหมด</span>
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </Link>
                            </div>
                            <div class="p-6 max-h-[600px] overflow-y-auto custom-scrollbar">
                                <div v-if="!recentRequests || recentRequests.length === 0" class="py-24 text-center">
                                    <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center mx-auto text-slate-200 mb-6">
                                        <i data-lucide="inbox" class="w-12 h-12"></i>
                                    </div>
                                    <p class="text-slate-400 font-black uppercase tracking-widest text-sm">ยังไม่มีความเคลื่อนไหวในขณะนี้</p>
                                </div>
                                <div v-else class="space-y-4">
                                    <Link v-for="req in recentRequests" :key="req.id" :href="`/leave-request/${req.id}`" class="group flex flex-col sm:flex-row sm:items-center gap-6 p-6 hover:bg-slate-50 rounded-[2rem] transition-all cursor-pointer border-2 border-transparent hover:border-slate-100 relative overflow-hidden">
                                        <div class="w-16 h-16 rounded-[1.5rem] flex items-center justify-center shrink-0 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform" :class="[statusMeta(req).meta.bg, statusMeta(req).meta.text]">
                                            <i :data-lucide="statusMeta(req).meta.icon" class="w-8 h-8"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-3 mb-2">
                                                <h4 class="font-black text-slate-800 text-xl tracking-tight leading-none">{{ req.leave_type?.name }}</h4>
                                                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-wider">{{ req.total_days }} วัน</span>
                                            </div>
                                            <div class="flex items-center gap-4 text-xs font-bold text-slate-400">
                                                <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> {{ req.start_date_thai }} - {{ req.end_date_thai }}</span>
                                                <span class="hidden sm:flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ req.created_at_human }}</span>
                                            </div>
                                        </div>
                                        <div class="sm:text-right flex items-center sm:block justify-between mt-4 sm:mt-0">
                                            <span class="inline-flex items-center gap-2 px-5 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg" :class="statusMeta(req).status.cls">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="statusMeta(req).status.dot"></span>
                                                {{ statusMeta(req).status.label }}
                                            </span>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="lg:col-span-4 space-y-8">
                        <!-- Today's leaves -->
                        <div class="bento-card group">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">สถานะวันนี้</h3>
                                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-500 flex items-center justify-center group-hover:rotate-12 transition-transform">
                                    <i data-lucide="calendar-heart" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center -space-x-4">
                                    <div v-for="leave in (todayLeaves || []).slice(0, 4)" :key="leave.id" class="w-12 h-12 rounded-[1.2rem] border-4 border-white bg-slate-100 flex items-center justify-center overflow-hidden shadow-md ring-1 ring-slate-100" :title="leave.user?.name">
                                        <img v-if="leave.user?.avatar" :src="avatarUrl(leave.user.avatar)" class="w-full h-full object-cover">
                                        <span v-else class="font-black text-slate-400 text-sm">{{ leave.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div v-if="todayLeaves && todayLeaves.length > 4" class="w-12 h-12 rounded-[1.2rem] border-4 border-white bg-brand-600 text-white flex items-center justify-center font-black text-xs shadow-md ring-1 ring-slate-100">
                                        +{{ todayLeaves.length - 4 }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-brand-500 uppercase tracking-widest leading-none mb-1">สถานะวันนี้</p>
                                    <p class="text-sm font-bold text-slate-500">มียอดลา {{ todayLeaves?.length || 0 }} นาย</p>
                                </div>
                            </div>
                        </div>

                        <!-- Regulation Card -->
                        <div class="relative bg-slate-900 rounded-[3rem] p-10 text-white overflow-hidden shadow-2xl">
                            <div class="absolute -top-10 -right-10 w-48 h-48 bg-brand-500 rounded-full blur-[100px] opacity-30"></div>
                            <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-indigo-500 rounded-full blur-[100px] opacity-20"></div>
                            <div class="relative z-10 space-y-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-brand-400">
                                        <i data-lucide="bookmark-check" class="w-7 h-7"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-black tracking-tight leading-none mb-1">สาระน่ารู้</h4>
                                        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">กฎระเบียบและข้อแนะนำ</p>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="flex gap-4 group cursor-help">
                                        <div class="w-1 h-12 bg-brand-500 rounded-full group-hover:scale-y-125 transition-transform"></div>
                                        <div>
                                            <p class="text-xs font-black text-brand-400 uppercase tracking-widest mb-1">การลาพักผ่อนสะสม</p>
                                            <p class="text-xs text-slate-400 leading-relaxed font-medium">วันลาพักผ่อนสามารถสะสมไปปีถัดไปได้ รวมไม่เกิน 20 วันทำการ (30 วันสำหรับผู้รับราชการ 10 ปีขึ้นไป)</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-4 group cursor-help">
                                        <div class="w-1 h-12 bg-rose-500 rounded-full group-hover:scale-y-125 transition-transform"></div>
                                        <div>
                                            <p class="text-xs font-black text-rose-400 uppercase tracking-widest mb-1">หลักฐานการลาป่วย</p>
                                            <p class="text-xs text-slate-400 leading-relaxed font-medium">กรณีลาป่วยเกิน 3 วันทำการ ต้องแนบใบรับรองแพทย์จากสถานพยาบาลทางทหารหรือที่รัฐบาลรับรอง</p>
                                        </div>
                                    </div>
                                </div>
                                <Link href="/calendar" class="w-full py-5 bg-brand-600 hover:bg-brand-500 transition-all text-white font-black rounded-3xl shadow-xl shadow-brand-600/30 active:scale-95 flex items-center justify-center gap-4 text-xs uppercase tracking-[0.2em]">
                                    <span>ภาพรวมปฏิบัติงาน</span>
                                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                                </Link>
                            </div>
                        </div>

                        <!-- Support Card -->
                        <div class="bento-card bg-gradient-to-br from-brand-50 to-white flex flex-col items-center text-center !p-12">
                            <div class="relative mb-6">
                                <div class="w-24 h-24 bg-white rounded-[2.5rem] flex items-center justify-center shadow-xl border border-brand-100 rotate-6">
                                    <i data-lucide="message-square-heart" class="w-12 h-12 text-brand-500"></i>
                                </div>
                            </div>
                            <h4 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-3">ต้องการความช่วยเหลือ?</h4>
                            <p class="text-slate-500 text-sm font-medium leading-relaxed mb-8">หากท่านมีข้อสงสัยหรือพบปัญหาในการใช้งานระบบ สามารถติดต่อฝ่ายกำลังพลได้ทันที</p>
                            <a href="tel:023456789" class="inline-flex items-center gap-3 text-brand-600 font-black text-sm uppercase tracking-[0.2em] border-b-4 border-brand-200 hover:border-brand-500 transition-all pb-1">
                                <span>ติดต่อเจ้าหน้าที่</span>
                                <i data-lucide="phone-call" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap');
.font-outfit { font-family: 'Outfit', sans-serif; }
.font-thai { font-family: 'IBM Plex Sans Thai', 'Sarabun', sans-serif; }
.glass-panel { background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.5); box-shadow: 0 8px 32px 0 rgba(31,38,135,0.07); }
.bento-card { background: white; border-radius: 2.5rem; padding: 2rem; border: 1px solid #f1f5f9; transition: all 0.4s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; }
.bento-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08); border-color: #e2e8f0; }
.stat-number { font-family: 'Outfit', sans-serif; letter-spacing: -0.05em; line-height: 1; }
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>

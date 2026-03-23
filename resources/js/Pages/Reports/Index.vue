<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick } from 'vue';

const props = defineProps({
    requests: Object, departments: Array, leaveTypes: Array,
    topLeavers: Array, popularLeaveTypes: Array, totalApprovedLeaves: Number,
    departmentStats: Array, monthlyTrend: Array, currentYear: Number,
});

const filterStartDate = ref('');
const filterEndDate = ref('');
const filterDepartment = ref('');
const filterLeaveType = ref('');
const filterStatus = ref('');

function applyFilter() {
    router.get('/reports', {
        start_date: filterStartDate.value, end_date: filterEndDate.value,
        department: filterDepartment.value, leave_type_id: filterLeaveType.value,
        status: filterStatus.value,
    }, { preserveState: true });
}
function resetFilter() { router.get('/reports'); }

const thaiMonths = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
const maxDays = computed(() => Math.max(...(props.monthlyTrend || []).map(m => m.total_days || 0), 1));
const totalDays = computed(() => (props.monthlyTrend || []).reduce((s, m) => s + (m.total_days || 0), 0));
const totalCount = computed(() => (props.monthlyTrend || []).reduce((s, m) => s + (m.count || 0), 0));
const activeMonths = computed(() => (props.monthlyTrend || []).filter(m => m.total_days > 0).length);

// Max days for department bar chart scaling
const maxDeptDays = computed(() => Math.max(...(props.departmentStats || []).map(d => d.total_days || 0), 1));

// Max usage_count for popular leave types scaling
const maxLeaveTypeCount = computed(() => Math.max(...(props.popularLeaveTypes || []).map(l => l.usage_count || 0), 1));

function statusLabel(status) {
    const map = { approved: 'อนุมัติ', rejected: 'ปฏิเสธ', cancelled: 'ยกเลิก' };
    return map[status] || 'รอตรวจสอบ';
}
function statusCls(status) {
    const map = { approved: 'bg-emerald-100 text-emerald-700', rejected: 'bg-rose-100 text-rose-700', cancelled: 'bg-slate-100 text-slate-600' };
    return map[status] || 'bg-amber-100 text-amber-700';
}

function isPdf(path) {
    return path && path.toLowerCase().endsWith('.pdf');
}

function getFileIcon(path) {
    if (!path) return 'file';
    const ext = path.split('.').pop()?.toLowerCase();
    if (ext === 'pdf') return 'file-text';
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) return 'image';
    if (['doc', 'docx', 'txt'].includes(ext)) return 'file-text';
    if (['xls', 'xlsx', 'csv'].includes(ext)) return 'file-spreadsheet';
    return 'file';
}

function isImage(path) {
    if (!path) return false;
    const ext = path.split('.').pop()?.toLowerCase();
    return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext);
}

const attachmentModal = ref(false);
const attachmentRequest = ref(null);
const attachmentType = ref(''); // 'file' | 'pdf'

function openAttachmentModal(req) {
    attachmentRequest.value = req;
    attachmentType.value = 'file';
    attachmentModal.value = true;
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
}
function openPdfModal(req) {
    attachmentRequest.value = req;
    attachmentType.value = 'pdf';
    attachmentModal.value = true;
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
}
function closeAttachmentModal() {
    attachmentModal.value = false;
    attachmentRequest.value = null;
    attachmentType.value = '';
}

const leaveTypeColors = ['bg-indigo-500','bg-emerald-500','bg-amber-500','bg-rose-500','bg-violet-500','bg-cyan-500','bg-pink-500','bg-teal-500'];

const isLoaded = ref(false);
onMounted(() => { 
    setTimeout(() => { 
        if (window.lucide) window.lucide.createIcons(); 
        isLoaded.value = true;
    }, 150); 
});
</script>


<template>
    <AppLayout title="รายงานสรุปสถิติการลา">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-slate-50 font-sans selection:bg-emerald-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-emerald-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[30%] left-[-10%] w-[500px] h-[500px] bg-teal-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[10%] w-[700px] h-[700px] bg-cyan-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-emerald-100/50">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="text-emerald-700 text-[11px] font-black uppercase tracking-[0.2em]">ระบบวิเคราะห์และรายงาน</span>
                        </div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-[1.25rem] flex items-center justify-center shadow-lg shadow-emerald-500/30 border border-white/20">
                                <i data-lucide="bar-chart-2" class="w-7 h-7 text-white"></i>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none">
                                รายงานสรุป<span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500">สถิติการลา</span>
                            </h1>
                        </div>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed ml-2 md:ml-[4.5rem]">
                            ระบบวิเคราะห์ทรัพยากรบุคคลแบบครบวงจร ติดตามแนวโน้มการลาของกำลังพล
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <a href="/reports/export" class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm rounded-[1.5rem] shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 uppercase tracking-widest gap-3 border border-emerald-400/30">
                            <i data-lucide="download" class="w-5 h-5 group-hover:-translate-y-1 transition-transform"></i>
                            ส่งออกรายงาน (Excel)
                        </a>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <!-- Total requests -->
                    <div class="glass-card rounded-[2rem] p-8 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 100ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-blue-500 opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.5rem] bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm border border-indigo-100 group-hover:rotate-6">
                                <i data-lucide="files" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">รายการทั้งหมด</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tight relative z-10">{{ requests?.total || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner"><div class="bg-indigo-500 h-full rounded-full w-full"></div></div>
                    </div>

                    <!-- Approved -->
                    <div class="glass-card rounded-[2rem] p-8 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 150ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm border border-emerald-100 group-hover:rotate-6">
                                <i data-lucide="check-circle" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">อนุมัติเรียบร้อย</span>
                        </div>
                        <h3 class="text-5xl font-black text-emerald-600 mb-6 tracking-tight relative z-10">{{ totalApprovedLeaves || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" :style="{ width: ((requests?.total || 1) > 0 ? ((totalApprovedLeaves || 0) / (requests?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>

                    <!-- Top 2 popular leave types as stat cards -->
                    <div v-for="(lt, i) in (popularLeaveTypes || []).slice(0, 2)" :key="i" class="glass-card rounded-[2rem] p-8 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" :style="`transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: ${200 + (i * 50)}ms;`">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-500 opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.5rem] bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-300 shadow-sm border border-amber-100 group-hover:-rotate-6">
                                <i data-lucide="bookmark" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2 text-right max-w-[120px] leading-tight">{{ lt.leave_type?.name }}</span>
                        </div>
                        <h3 class="text-5xl font-black text-amber-500 mb-6 tracking-tight relative z-10">{{ lt.usage_count }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner">
                            <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" :style="{ width: ((requests?.total || 1) > 0 ? ((lt.usage_count || 0) / (requests?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Filter Console -->
                <div class="glass-card rounded-[2.5rem] p-8 lg:p-10 mb-12 shadow-sm border border-white/60 relative overflow-hidden"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 300ms;">
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-[1.25rem] bg-slate-900 text-white flex items-center justify-center shadow-lg border border-slate-800">
                            <i data-lucide="sliders-horizontal" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">ตัวกรองข้อมูล</h3>
                            <p class="text-[10px] font-black text-slate-400 tracking-[0.25em] mt-1">กำหนดเงื่อนไขการสืบค้นข้อมูล</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 items-end">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2">ตั้งแต่วันที่</label>
                            <input v-model="filterStartDate" type="date" class="w-full px-5 py-3.5 filter-input rounded-2xl font-bold text-slate-700 text-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2">ถึงวันที่</label>
                            <input v-model="filterEndDate" type="date" class="w-full px-5 py-3.5 filter-input rounded-2xl font-bold text-slate-700 text-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2">หน่วยงาน/แผนก</label>
                            <select v-model="filterDepartment" class="w-full px-5 py-3.5 filter-input rounded-2xl font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                                <option value="">ทุกหน่วยงาน</option>
                                <option v-for="d in departments" :key="d.id" :value="d.name">{{ d.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2">ประเภทการลา</label>
                            <select v-model="filterLeaveType" class="w-full px-5 py-3.5 filter-input rounded-2xl font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                                <option value="">ทุกประเภท</option>
                                <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2">สถานะรายการ</label>
                            <select v-model="filterStatus" class="w-full px-5 py-3.5 filter-input rounded-2xl font-bold text-slate-700 text-sm appearance-none cursor-pointer">
                                <option value="">ทุกสถานะ</option>
                                <option value="approved">อนุมัติแล้ว</option>
                                <option value="pending">รอตรวจสอบ</option>
                                <option value="rejected">ปฏิเสธ</option>
                                <option value="cancelled">ยกเลิก</option>
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <button @click="applyFilter" class="flex-1 py-3.5 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-[0.1em] text-xs shadow-lg shadow-slate-900/20 hover:bg-emerald-600 transition-all hover:-translate-y-0.5 group flex items-center justify-center">
                                <i data-lucide="search" class="w-4 h-4 mr-2 group-hover:scale-125 transition-transform"></i> กรอง
                            </button>
                            <button @click="resetFilter" class="w-[3.25rem] h-[3.25rem] bg-white border border-slate-200/60 text-slate-400 rounded-2xl flex items-center justify-center hover:bg-slate-50 hover:text-slate-600 transition-all hover:rotate-180 shadow-sm flex-shrink-0">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend Chart -->
                <div v-if="monthlyTrend && monthlyTrend.length > 0" class="glass-card rounded-[2.5rem] p-8 lg:p-10 mb-12 shadow-sm border border-white/60"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 350ms;">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-[1.25rem] bg-violet-600 text-white flex items-center justify-center shadow-lg shadow-violet-600/30">
                            <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">แนวโน้มการลารายเดือน</h3>
                            <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase mt-1">สถิติการลาตลอดปี {{ (currentYear || new Date().getFullYear()) + 543 }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-end gap-2 sm:gap-4 h-48 mt-8">
                        <div v-for="m in monthlyTrend" :key="m.month" class="flex-1 flex flex-col items-center gap-3 group/bar">
                            <span class="text-[10px] font-black text-violet-600 opacity-0 group-hover/bar:opacity-100 transition-opacity translate-y-2 group-hover/bar:translate-y-0">{{ m.total_days }} วัน</span>
                            <div class="w-full relative rounded-t-2xl overflow-hidden bg-slate-100/50 flex flex-col justify-end h-full">
                                <div class="w-full rounded-t-2xl transition-all duration-1000 ease-out"
                                    :class="m.total_days > 0 ? 'bg-gradient-to-t from-violet-500 to-fuchsia-400 group-hover/bar:from-violet-600 group-hover/bar:to-fuchsia-500 shadow-lg shadow-violet-500/20' : 'bg-transparent'"
                                    :style="{ height: Math.max(Math.round((m.total_days / maxDays) * 100), m.total_days > 0 ? 8 : 0) + '%' }"></div>
                            </div>
                            <span class="text-[11px] font-black text-slate-500">{{ thaiMonths[m.month - 1] }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex items-center justify-center gap-8 sm:gap-16 border-t border-slate-200/60 pt-8">
                        <div class="text-center"><p class="text-3xl font-black text-violet-600">{{ totalDays }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1.5">วันรวม</p></div>
                        <div class="w-px h-12 bg-slate-200/60"></div>
                        <div class="text-center"><p class="text-3xl font-black text-slate-800">{{ totalCount }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1.5">ครั้งรวม</p></div>
                        <div class="w-px h-12 bg-slate-200/60"></div>
                        <div class="text-center"><p class="text-3xl font-black text-emerald-600">{{ activeMonths }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1.5">เดือนที่มีการลา</p></div>
                    </div>
                </div>

                <!-- Popular Leave Types + Top Leavers (side by side) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    <!-- Popular Leave Types -->
                    <div v-if="popularLeaveTypes && popularLeaveTypes.length > 0" class="glass-card rounded-[2.5rem] p-8 lg:p-10 shadow-sm border border-white/60"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 400ms;">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-[1.25rem] bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30">
                                <i data-lucide="pie-chart" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">ประเภทการลายอดนิยม</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">LEAVE TYPE BREAKDOWN</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div v-for="(lt, i) in popularLeaveTypes" :key="i" class="flex items-center gap-4 group">
                                <div class="w-10 h-10 rounded-[1.1rem] flex items-center justify-center text-white text-sm font-black flex-shrink-0 shadow-sm group-hover:scale-110 transition-transform" :class="leaveTypeColors[i % leaveTypeColors.length]">{{ i + 1 }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-black text-slate-700 truncate group-hover:text-slate-900 transition-colors">{{ lt.leave_type?.name || '-' }}</span>
                                        <div class="text-right">
                                            <span class="text-sm font-black text-slate-900 ml-2 block">{{ lt.usage_count }} ครั้ง</span>
                                            <span class="text-[10px] font-bold text-slate-400">รวม {{ lt.total_days }} วัน</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-100/80 rounded-full h-2.5 overflow-hidden shadow-inner hidden">
                                        <div class="h-full rounded-full transition-all duration-1000" :class="leaveTypeColors[i % leaveTypeColors.length]"
                                            :style="{ width: Math.round((lt.usage_count / maxLeaveTypeCount) * 100) + '%' }"></div>
                                    </div>
                                    <div class="w-full bg-slate-100/80 rounded-full h-2.5 overflow-hidden shadow-inner">
                                        <div class="h-full rounded-full transition-all duration-1000" :class="leaveTypeColors[i % leaveTypeColors.length]"
                                            :style="{ width: Math.round((lt.usage_count / maxLeaveTypeCount) * 100) + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Leavers -->
                    <div v-if="topLeavers && topLeavers.length > 0" class="glass-card rounded-[2.5rem] p-8 lg:p-10 shadow-sm border border-white/60"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 450ms;">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-[1.25rem] bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/30">
                                <i data-lucide="trophy" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">ลาเยอะที่สุด</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">TOP LEAVE USERS</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(t, i) in topLeavers" :key="t.user_id" class="flex items-center gap-4 p-4 rounded-[1.5rem] bg-white/40 hover:bg-white/70 border border-white/60 transition-colors group">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0"
                                    :class="i === 0 ? 'bg-amber-100 text-amber-700' : i === 1 ? 'bg-slate-200 text-slate-600' : i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500'">
                                    {{ i + 1 }}
                                </div>
                                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                    <img v-if="t.user?.avatar" :src="`/storage/${t.user.avatar}`" class="w-full h-full object-cover">
                                    <span v-else>{{ t.user?.name?.charAt(0) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-black text-slate-800 text-sm truncate group-hover:text-rose-600 transition-colors">{{ t.user?.rank }} {{ t.user?.name }}</p>
                                    <p class="text-[11px] text-slate-400 font-bold truncate">{{ t.user?.department }}</p>
                                </div>
                                <div class="text-right flex-shrink-0 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                                    <p class="text-lg font-black text-rose-600 leading-none">{{ t.total_leave_days }}</p>
                                    <p class="text-[9px] text-slate-400 font-black uppercase mt-1">วัน</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Stats -->
                <div v-if="departmentStats && departmentStats.length > 0" class="glass-card rounded-[2.5rem] p-8 lg:p-10 mb-12 shadow-sm border border-white/60"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 500ms;">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-[1.25rem] bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-600/30">
                            <i data-lucide="building-2" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">สถิติแยกตามแผนก</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">DEPARTMENT BREAKDOWN</p>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div v-for="(dept, di) in departmentStats" :key="dept.name" class="bg-white/40 border border-white/60 rounded-[2rem] overflow-hidden hover:bg-white/60 transition-colors">
                            <!-- Dept header -->
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 bg-white/50 border-b border-white/60">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-[1rem] bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-sm shadow-sm">
                                        {{ di + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="text-base font-black text-slate-900">{{ dept.name }}</h4>
                                        <p class="text-[11px] text-slate-500 font-bold">{{ dept.total_count }} รายการ · {{ dept.total_days }} วันรวม</p>
                                    </div>
                                </div>
                                <!-- Dept bar -->
                                <div class="flex items-center gap-3 flex-1 md:max-w-[200px]">
                                    <div class="flex-1 bg-slate-200/50 rounded-full h-2.5 overflow-hidden shadow-inner">
                                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000"
                                            :style="{ width: Math.round((dept.total_days / maxDeptDays) * 100) + '%' }"></div>
                                    </div>
                                    <span class="text-xs font-black text-indigo-600 w-12 text-right flex-shrink-0">{{ dept.total_days }} วัน</span>
                                </div>
                            </div>
                            <!-- Dept detail: leave type breakdown + person ranking -->
                            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-white/60">
                                <!-- Leave type breakdown -->
                                <div class="px-6 py-5">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">ประเภทการลา</p>
                                    <div class="space-y-2">
                                        <div v-for="(breakdown, typeName) in dept.leave_type_breakdown" :key="typeName" class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-indigo-400 flex-shrink-0 shadow-sm shadow-indigo-400/50"></span>
                                            <span class="text-[13px] font-bold text-slate-700 flex-1 truncate">{{ typeName }}</span>
                                            <span class="text-[11px] font-black text-slate-500 flex-shrink-0 bg-slate-100/80 px-2 py-0.5 rounded-md">{{ breakdown.count }} ครั้ง / {{ breakdown.days }} วัน</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Person ranking -->
                                <div class="px-6 py-5">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">อันดับบุคคล</p>
                                    <div class="space-y-2">
                                        <div v-for="(person, pi) in dept.person_ranking" :key="pi" class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-[0.4rem] flex items-center justify-center text-[9px] font-black flex-shrink-0 shadow-sm"
                                                :class="pi === 0 ? 'bg-amber-100 text-amber-700' : pi === 1 ? 'bg-slate-200 text-slate-600' : pi === 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500'">
                                                {{ pi + 1 }}
                                            </span>
                                            <span class="text-[13px] font-bold text-slate-700 flex-1 truncate">{{ person.user?.rank }} {{ person.user?.name }}</span>
                                            <span class="text-[11px] font-black text-indigo-600 flex-shrink-0 bg-indigo-50 px-2 py-0.5 rounded-md">{{ person.days }} วัน</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-sm border border-white/60"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 600ms;">
                    <div class="px-6 py-5 lg:px-8 border-b border-white/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white/30">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-[1.25rem] bg-slate-900 text-white flex items-center justify-center shadow-lg border border-slate-800 flex-shrink-0"><i data-lucide="table-2" class="w-5 h-5"></i></div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-1">รายการทั้งหมด</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ALL LEAVE RECORDS</p>
                            </div>
                        </div>
                        <span class="bg-indigo-50 text-indigo-600 text-xs font-black px-4 py-2 rounded-xl border border-indigo-100 shadow-sm whitespace-nowrap">{{ requests?.total || 0 }} รายการ</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead>
                                <tr class="bg-white/40 border-b border-white/60">
                                    <th class="px-8 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em]">ชื่อ</th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em]">ประเภท</th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em]">วันที่</th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em]">จำนวน</th>
                                    <th class="px-8 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em]">สถานะ</th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em] text-center">ใบลา PDF</th>
                                    <th class="px-6 py-5 font-black text-slate-400 text-[10px] uppercase tracking-[0.1em] text-center">หลักฐาน</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/40 border-t border-white/60 bg-white/10">
                                <tr v-if="(requests?.data || []).length === 0">
                                    <td colspan="7" class="px-10 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-20 h-20 bg-white/50 border border-white rounded-[2rem] flex items-center justify-center shadow-sm">
                                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                                            </div>
                                            <p class="font-black text-slate-500 mt-2">ไม่พบรายการที่ตรงกับเงื่อนไข</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="(r, index) in (requests?.data || [])" :key="r.id" 
                                    class="hover:bg-white/70 transition-colors duration-300 group"
                                    :style="`animation-delay: ${index * 30}ms;`">
                                    <td class="px-8 py-5 align-middle">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-500 border border-slate-200 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                                <img v-if="r.user?.avatar" :src="`/storage/${r.user.avatar}`" class="w-full h-full object-cover">
                                                <span v-else>{{ r.user?.name?.charAt(0) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-800 text-sm group-hover:text-emerald-700 transition-colors">{{ r.user?.rank }}{{ r.user?.name }}</p>
                                                <p class="text-[11px] text-slate-500 font-bold mt-0.5">{{ r.user?.department }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-middle">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[0.85rem] text-[10px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100/50 shadow-sm leading-none">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 hidden md:block"></span>
                                            {{ r.leave_type?.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 align-middle">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400 hidden lg:block"></i>
                                            <span class="text-xs font-black text-slate-700">{{ r.start_date_thai || r.start_date }} <span class="text-slate-400 mx-1 font-normal">—</span> {{ r.end_date_thai || r.end_date }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-middle">
                                        <div class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 text-slate-700 font-black text-xs rounded-xl shadow-inner min-w-[3.5rem] border border-slate-200/50">
                                            {{ r.total_days }} วัน
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 align-middle">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border shadow-sm font-black text-[10px]" :class="statusCls(r.status).replace('bg-','bg-opacity-50 border-').replace('100','200')">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="r.status==='approved'?'bg-emerald-500':r.status==='rejected'?'bg-rose-500':'bg-amber-500 animate-pulse'"></span>
                                            {{ statusLabel(r.status) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-middle text-center">
                                        <button @click="openPdfModal(r)"
                                            class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-black transition-all group bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 shadow-sm hover:shadow">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                                            <span>PDF</span>
                                        </button>
                                    </td>
                                    <td class="px-6 py-5 align-middle text-center">
                                        <button v-if="r.attachment_path" @click="openAttachmentModal(r)"
                                            class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-black transition-all group bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300 shadow-sm hover:shadow">
                                            <i :data-lucide="getFileIcon(r.attachment_path)" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                                            <span>{{ isImage(r.attachment_path) ? 'รูปภาพ' : 'เอกสาร' }}</span>
                                        </button>
                                        <span v-else class="text-slate-300 font-black text-xl leading-none block">-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests?.links && requests.links.length > 3" class="mt-10 flex justify-center pb-8"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 700ms;">
                    <div class="bg-white/60 backdrop-blur-xl p-2 rounded-[1.5rem] shadow-sm border border-white flex gap-1">
                        <template v-for="(link,i) in requests.links" :key="i">
                            <Link v-if="link.url" :href="link.url" class="px-4 py-2.5 rounded-xl text-xs font-black transition-all"
                                :class="link.active ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/20' : 'bg-transparent text-slate-500 hover:bg-white border border-transparent hover:border-slate-100 hover:text-slate-700'" v-html="link.label" />
                            <span v-else class="px-3 py-2.5 text-xs text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>

            </div>
        </div>

        <!-- Attachment / PDF Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="attachmentModal" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-10 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeAttachmentModal"></div>
                        <div class="bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-4xl flex flex-col" style="max-height: 90vh;">
                            <!-- Header -->
                            <div class="bg-white px-8 pt-8 pb-5 flex-shrink-0 border-b border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-[1.2rem] flex items-center justify-center shadow-inner"
                                            :class="attachmentType === 'pdf' ? 'bg-rose-50 text-rose-500' : 'bg-indigo-50 text-indigo-500'">
                                            <i :data-lucide="attachmentType === 'pdf' ? 'file-text' : getFileIcon(attachmentRequest?.attachment_path)" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-black text-slate-900 tracking-tight">
                                                {{ attachmentType === 'pdf' ? 'ใบลา PDF' : 'หลักฐานการลา' }}
                                            </h3>
                                            <p class="text-slate-400 text-xs font-bold mt-0.5">{{ attachmentRequest?.user?.rank }}{{ attachmentRequest?.user?.name }}</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="closeAttachmentModal" class="w-9 h-9 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Body -->
                            <div class="flex-1 overflow-auto bg-slate-50/50 p-6" style="min-height: 480px;">
                                <!-- PDF ใบลา -->
                                <template v-if="attachmentType === 'pdf'">
                                    <iframe
                                        :src="`/leave-request/${attachmentRequest?.id}/pdf`"
                                        class="w-full border border-slate-200 rounded-2xl shadow-sm bg-white"
                                        style="height: 60vh; min-height: 480px;"
                                        frameborder="0">
                                    </iframe>
                                </template>
                                <!-- หลักฐานการลา -->
                                <template v-else>
                                    <!-- รูปภาพ -->
                                    <div v-if="isImage(attachmentRequest?.attachment_path)" class="flex items-center justify-center h-full">
                                        <img :src="`/storage/${attachmentRequest?.attachment_path}`"
                                            class="max-w-full max-h-[60vh] rounded-2xl shadow-lg border border-slate-200 object-contain"
                                            alt="หลักฐานการลา">
                                    </div>
                                    <!-- PDF หลักฐาน -->
                                    <iframe
                                        v-else-if="isPdf(attachmentRequest?.attachment_path)"
                                        :src="`/storage/${attachmentRequest?.attachment_path}`"
                                        class="w-full border border-slate-200 rounded-2xl shadow-sm bg-white"
                                        style="height: 60vh; min-height: 480px;"
                                        frameborder="0">
                                    </iframe>
                                    <!-- ไฟล์อื่นๆ -->
                                    <div v-else class="flex items-center justify-center h-64 text-slate-400">
                                        <div class="text-center">
                                            <i data-lucide="file" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                                            <p class="text-lg font-bold text-slate-500 mb-1">ไม่สามารถแสดงตัวอย่างได้</p>
                                            <p class="text-xs text-slate-400">กรุณาดาวน์โหลดเพื่อเปิดไฟล์</p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <!-- Footer -->
                            <div class="bg-white px-8 py-4 flex-shrink-0 border-t border-slate-100 flex flex-col sm:flex-row-reverse gap-3">
                                <a v-if="attachmentType === 'pdf'"
                                    :href="`/leave-request/${attachmentRequest?.id}/pdf`" target="_blank"
                                    class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-lg shadow-rose-600/30 transition-all hover:-translate-y-0.5">
                                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                    เปิดในแท็บใหม่
                                </a>
                                <a v-else-if="attachmentRequest?.attachment_path"
                                    :href="`/storage/${attachmentRequest?.attachment_path}`" target="_blank"
                                    class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-lg shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                    ดาวน์โหลด
                                </a>
                                <button type="button" @click="closeAttachmentModal"
                                    class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-white border border-slate-200 text-slate-500 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-50 hover:text-slate-700 transition-all">
                                    ปิด
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
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
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 0, 0.8);
}
.glass-card:hover {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(255, 255, 255, 0.9);
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 50px -10px rgba(16, 185, 129, 0.1), inset 0 1px 0 rgba(255, 255, 255, 1);
}

.filter-input {
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
}
.filter-input:focus {
    background: white;
    border-color: rgba(16, 185, 129, 0.4);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), inset 0 2px 4px rgba(0, 0, 0, 0.01);
    outline: none;
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


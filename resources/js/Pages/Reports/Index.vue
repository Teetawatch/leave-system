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

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="รายงานสรุปสถิติการลา">
        <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-emerald-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

            <!-- Header -->
            <div class="relative pt-16 pb-32">
                <div class="max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12">
                        <div>
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-emerald-100">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                ระบบวิเคราะห์และรายงาน
                            </div>
                            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-6">
                                รายงานสรุป <span class="text-emerald-600">สถิติการลา</span>
                            </h1>
                            <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">ระบบวิเคราะห์ทรัพยากรบุคคลแบบครบวงจร ติดตามแนวโน้มการลาของกำลังพลเพื่อการวางแผนและจัดการองค์กรอย่างมีประสิทธิภาพสูงสุด</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            <a href="/reports/export" class="group inline-flex items-center justify-center px-8 py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm rounded-[2rem] shadow-xl hover:shadow-emerald-500/30 transition-all hover:-translate-y-1 uppercase tracking-widest gap-3">
                                <i data-lucide="download" class="w-5 h-5 group-hover:-translate-y-1 transition-transform"></i>
                                ส่งออกรายงาน (Excel)
                            </a>
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
                                <i data-lucide="files" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">รายการทั้งหมด</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ requests?.total || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner"><div class="bg-indigo-500 h-full rounded-full w-full"></div></div>
                    </div>
                    <!-- Approved -->
                    <div class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500 shadow-sm border border-emerald-100 group-hover:rotate-12">
                                <i data-lucide="check-circle" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2">อนุมัติเรียบร้อย</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ totalApprovedLeaves || 0 }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="bg-emerald-500 h-full rounded-full" :style="{ width: ((requests?.total || 1) > 0 ? ((totalApprovedLeaves || 0) / (requests?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                    <!-- Top 2 popular leave types as stat cards -->
                    <div v-for="(lt, i) in (popularLeaveTypes || []).slice(0, 2)" :key="i" class="glass-panel rounded-[3rem] p-8 stats-card relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="flex justify-between items-start mb-8 relative z-10">
                            <div class="w-16 h-16 rounded-[1.75rem] bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-all duration-500 shadow-sm border border-amber-100 group-hover:rotate-12">
                                <i data-lucide="bookmark" class="w-8 h-8"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] pt-2 text-right max-w-[120px] leading-tight">{{ lt.leave_type?.name }}</span>
                        </div>
                        <h3 class="text-5xl font-black text-slate-900 mb-6 tracking-tighter relative z-10">{{ lt.usage_count }}</h3>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="bg-amber-500 h-full rounded-full" :style="{ width: ((requests?.total || 1) > 0 ? ((lt.usage_count || 0) / (requests?.total || 1)) * 100 : 0) + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Filter Console -->
                <div class="glass-panel rounded-[3.5rem] p-10 mb-12 shadow-2xl shadow-slate-900/5">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-12 h-12 rounded-[1.25rem] bg-slate-900 text-white flex items-center justify-center shadow-lg">
                            <i data-lucide="sliders-horizontal" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">ตัวกรองข้อมูล</h3>
                            <p class="text-[10px] font-black text-slate-400 tracking-[0.25em] mt-1">กำหนดเงื่อนไขการสืบค้นข้อมูล</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-8 items-end">
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">ตั้งแต่วันที่</label>
                            <input v-model="filterStartDate" type="date" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">ถึงวันที่</label>
                            <input v-model="filterEndDate" type="date" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">หน่วยงาน/แผนก</label>
                            <select v-model="filterDepartment" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900 appearance-none cursor-pointer">
                                <option value="">ทุกหน่วยงาน</option>
                                <option v-for="d in departments" :key="d.id" :value="d.name">{{ d.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">ประเภทการลา</label>
                            <select v-model="filterLeaveType" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900 appearance-none cursor-pointer">
                                <option value="">ทุกประเภท</option>
                                <option v-for="lt in leaveTypes" :key="lt.id" :value="lt.id">{{ lt.name }}</option>
                            </select>
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">สถานะรายการ</label>
                            <select v-model="filterStatus" class="w-full px-6 py-4 filter-input rounded-[1.75rem] font-black text-slate-900 appearance-none cursor-pointer">
                                <option value="">ทุกสถานะ</option>
                                <option value="approved">อนุมัติแล้ว</option>
                                <option value="pending">รอตรวจสอบ</option>
                                <option value="rejected">ปฏิเสธ</option>
                                <option value="cancelled">ยกเลิก</option>
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <button @click="applyFilter" class="flex-1 py-4 bg-slate-900 text-white rounded-[1.75rem] font-black uppercase tracking-[0.1em] text-sm shadow-xl hover:bg-emerald-600 transition-all hover:-translate-y-1 group">
                                <i data-lucide="search" class="w-4 h-4 inline-block mr-2 group-hover:scale-125 transition-transform"></i> กรอง
                            </button>
                            <button @click="resetFilter" class="w-14 h-14 bg-white border border-slate-200 text-slate-400 rounded-[1.75rem] flex items-center justify-center hover:bg-slate-50 transition-all hover:rotate-180 shadow-sm">
                                <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend Chart -->
                <div v-if="monthlyTrend && monthlyTrend.length > 0" class="glass-panel rounded-[3.5rem] p-10 mb-12">
                    <div class="flex items-center gap-5 mb-10">
                        <div class="w-14 h-14 rounded-[1.5rem] bg-violet-600 text-white flex items-center justify-center shadow-lg">
                            <i data-lucide="bar-chart-2" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">แนวโน้มการลารายเดือน</h3>
                            <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase mt-1">สถิติการลาตลอดปี {{ (currentYear || new Date().getFullYear()) + 543 }}</p>
                        </div>
                    </div>
                    <div class="flex items-end gap-3 h-40">
                        <div v-for="m in monthlyTrend" :key="m.month" class="flex-1 flex flex-col items-center gap-2 group/bar">
                            <span class="text-[9px] font-black text-slate-400 opacity-0 group-hover/bar:opacity-100 transition-opacity">{{ m.total_days }}วัน</span>
                            <div class="w-full relative rounded-t-xl overflow-hidden bg-slate-100 flex flex-col justify-end" style="height: 88px">
                                <div class="w-full rounded-t-xl transition-all duration-700 ease-out"
                                    :class="m.total_days > 0 ? 'bg-violet-500 group-hover/bar:bg-violet-600' : 'bg-slate-100'"
                                    :style="{ height: Math.max(Math.round((m.total_days / maxDays) * 100), m.total_days > 0 ? 6 : 0) + '%' }"></div>
                            </div>
                            <span class="text-[10px] font-black text-slate-500">{{ thaiMonths[m.month - 1] }}</span>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-6 border-t border-slate-100 pt-6">
                        <div class="text-center"><p class="text-2xl font-black text-violet-600">{{ totalDays }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">วันรวม</p></div>
                        <div class="w-px h-10 bg-slate-100"></div>
                        <div class="text-center"><p class="text-2xl font-black text-slate-800">{{ totalCount }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">ครั้งรวม</p></div>
                        <div class="w-px h-10 bg-slate-100"></div>
                        <div class="text-center"><p class="text-2xl font-black text-emerald-600">{{ activeMonths }}</p><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">เดือนที่มีการลา</p></div>
                    </div>
                </div>

                <!-- Popular Leave Types + Top Leavers (side by side) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                    <!-- Popular Leave Types -->
                    <div v-if="popularLeaveTypes && popularLeaveTypes.length > 0" class="glass-panel rounded-[3.5rem] p-10">
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-14 h-14 rounded-[1.5rem] bg-amber-500 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="pie-chart" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">ประเภทการลายอดนิยม</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">LEAVE TYPE BREAKDOWN</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(lt, i) in popularLeaveTypes" :key="i" class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-black flex-shrink-0" :class="leaveTypeColors[i % leaveTypeColors.length]">{{ i + 1 }}</div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-black text-slate-700 truncate">{{ lt.leave_type?.name || '-' }}</span>
                                        <span class="text-sm font-black text-slate-900 ml-2 flex-shrink-0">{{ lt.usage_count }} ครั้ง</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-700" :class="leaveTypeColors[i % leaveTypeColors.length]"
                                            :style="{ width: Math.round((lt.usage_count / maxLeaveTypeCount) * 100) + '%' }"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold mt-0.5">รวม {{ lt.total_days }} วัน</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Leavers -->
                    <div v-if="topLeavers && topLeavers.length > 0" class="glass-panel rounded-[3.5rem] p-10">
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-14 h-14 rounded-[1.5rem] bg-rose-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="trophy" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">ลาเยอะที่สุด</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">TOP LEAVE USERS</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div v-for="(t, i) in topLeavers" :key="t.user_id" class="flex items-center gap-4 p-4 rounded-[1.5rem] hover:bg-slate-50/50 transition-colors">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm flex-shrink-0"
                                    :class="i === 0 ? 'bg-amber-100 text-amber-700' : i === 1 ? 'bg-slate-200 text-slate-600' : i === 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500'">
                                    {{ i + 1 }}
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0">
                                    <img v-if="t.user?.avatar" :src="`/storage/${t.user.avatar}`" class="w-full h-full object-cover">
                                    <span v-else>{{ t.user?.name?.charAt(0) }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-black text-slate-800 truncate">{{ t.user?.rank }} {{ t.user?.name }}</p>
                                    <p class="text-xs text-slate-400 font-bold truncate">{{ t.user?.department }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-lg font-black text-indigo-600">{{ t.total_leave_days }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold">วัน</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department Stats -->
                <div v-if="departmentStats && departmentStats.length > 0" class="glass-panel rounded-[3.5rem] p-10 mb-12">
                    <div class="flex items-center gap-5 mb-10">
                        <div class="w-14 h-14 rounded-[1.5rem] bg-indigo-600 text-white flex items-center justify-center shadow-lg">
                            <i data-lucide="building-2" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">สถิติแยกตามแผนก</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">DEPARTMENT BREAKDOWN</p>
                        </div>
                    </div>
                    <div class="space-y-8">
                        <div v-for="(dept, di) in departmentStats" :key="dept.name" class="border border-slate-100 rounded-[2rem] overflow-hidden">
                            <!-- Dept header -->
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-8 py-6 bg-slate-50/60">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-[1.25rem] bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg">
                                        {{ di + 1 }}
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-black text-slate-900">{{ dept.name }}</h4>
                                        <p class="text-xs text-slate-400 font-bold">{{ dept.total_count }} รายการ · {{ dept.total_days }} วันรวม</p>
                                    </div>
                                </div>
                                <!-- Dept bar -->
                                <div class="flex items-center gap-3 flex-1 max-w-xs">
                                    <div class="flex-1 bg-slate-200 rounded-full h-3 overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-700"
                                            :style="{ width: Math.round((dept.total_days / maxDeptDays) * 100) + '%' }"></div>
                                    </div>
                                    <span class="text-sm font-black text-indigo-600 w-16 text-right flex-shrink-0">{{ dept.total_days }} วัน</span>
                                </div>
                            </div>
                            <!-- Dept detail: leave type breakdown + person ranking -->
                            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                                <!-- Leave type breakdown -->
                                <div class="px-8 py-6">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">ประเภทการลา</p>
                                    <div class="space-y-3">
                                        <div v-for="(breakdown, typeName) in dept.leave_type_breakdown" :key="typeName" class="flex items-center gap-3">
                                            <span class="w-2 h-2 rounded-full bg-indigo-400 flex-shrink-0"></span>
                                            <span class="text-sm font-bold text-slate-700 flex-1 truncate">{{ typeName }}</span>
                                            <span class="text-xs font-black text-slate-500 flex-shrink-0">{{ breakdown.count }} ครั้ง / {{ breakdown.days }} วัน</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Person ranking -->
                                <div class="px-8 py-6">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">อันดับบุคคล</p>
                                    <div class="space-y-3">
                                        <div v-for="(person, pi) in dept.person_ranking" :key="pi" class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0"
                                                :class="pi === 0 ? 'bg-amber-100 text-amber-700' : pi === 1 ? 'bg-slate-200 text-slate-600' : pi === 2 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500'">
                                                {{ pi + 1 }}
                                            </span>
                                            <span class="text-sm font-bold text-slate-700 flex-1 truncate">{{ person.user?.rank }} {{ person.user?.name }}</span>
                                            <span class="text-xs font-black text-indigo-600 flex-shrink-0">{{ person.days }} วัน</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="glass-panel rounded-[3.5rem] overflow-hidden shadow-2xl shadow-slate-900/5">
                    <div class="p-10 pb-0">
                        <div class="flex items-center gap-5 mb-8">
                            <div class="w-14 h-14 rounded-[1.5rem] bg-slate-900 text-white flex items-center justify-center shadow-lg"><i data-lucide="table-2" class="w-7 h-7"></i></div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight">รายการทั้งหมด</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mt-1">ALL LEAVE RECORDS</p>
                            </div>
                            <span class="ml-auto bg-slate-100 text-slate-500 text-xs font-black px-4 py-2 rounded-xl">{{ requests?.total || 0 }} รายการ</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-100">
                                    <th class="px-10 py-5 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ</th>
                                    <th class="px-6 py-5 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ประเภท</th>
                                    <th class="px-6 py-5 text-left font-black text-slate-400 text-xs uppercase tracking-widest">วันที่</th>
                                    <th class="px-6 py-5 text-left font-black text-slate-400 text-xs uppercase tracking-widest">จำนวน</th>
                                    <th class="px-10 py-5 text-left font-black text-slate-400 text-xs uppercase tracking-widest">สถานะ</th>
                                    <th class="px-6 py-5 text-center font-black text-slate-400 text-xs uppercase tracking-widest">ใบลา PDF</th>
                                    <th class="px-6 py-5 text-center font-black text-slate-400 text-xs uppercase tracking-widest">หลักฐาน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="(requests?.data || []).length === 0">
                                    <td colspan="7" class="px-10 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center">
                                                <i data-lucide="inbox" class="w-8 h-8 text-slate-200"></i>
                                            </div>
                                            <p class="font-black text-slate-400">ไม่พบรายการที่ตรงกับเงื่อนไข</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="r in (requests?.data || [])" :key="r.id" class="border-b border-slate-50 hover:bg-slate-50/80 transition-colors">
                                    <td class="px-10 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0">
                                                <img v-if="r.user?.avatar" :src="`/storage/${r.user.avatar}`" class="w-full h-full object-cover">
                                                <span v-else>{{ r.user?.name?.charAt(0) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-black text-slate-800">{{ r.user?.rank }}{{ r.user?.name }}</p>
                                                <p class="text-[10px] text-slate-400 font-bold">{{ r.user?.department }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100">{{ r.leave_type?.name }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-xs font-bold text-slate-500">
                                        {{ r.start_date_thai || r.start_date }} — {{ r.end_date_thai || r.end_date }}
                                    </td>
                                    <td class="px-6 py-5 font-black text-slate-800">{{ r.total_days }} วัน</td>
                                    <td class="px-10 py-5">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black" :class="statusCls(r.status)">{{ statusLabel(r.status) }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex justify-center">
                                            <button @click="openPdfModal(r)"
                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-black transition-all group bg-red-50 text-red-600 hover:bg-red-100 border border-red-100">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                                                <span>PDF</span>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div v-if="r.attachment_path" class="flex justify-center">
                                            <button @click="openAttachmentModal(r)"
                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-black transition-all group bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border border-indigo-100">
                                                <i :data-lucide="getFileIcon(r.attachment_path)" class="w-3.5 h-3.5 group-hover:scale-110 transition-transform"></i>
                                                <span>{{ isImage(r.attachment_path) ? 'รูป' : 'ดู' }}</span>
                                            </button>
                                        </div>
                                        <div v-else class="flex justify-center">
                                            <span class="text-slate-300 text-xs font-medium">-</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests?.links && requests.links.length > 3" class="mt-12 flex justify-center">
                    <div class="bg-white/80 backdrop-blur-md p-3 rounded-[2rem] shadow-xl border border-white/50 flex gap-1">
                        <template v-for="link in requests.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-xl text-sm font-black transition-all"
                                :class="link.active ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                            <span v-else class="px-4 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
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
                                            :class="attachmentType === 'pdf' ? 'bg-red-50 text-red-500' : 'bg-indigo-50 text-indigo-500'">
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
                            <div class="flex-1 overflow-auto bg-slate-50 p-6" style="min-height: 480px;">
                                <!-- PDF ใบลา -->
                                <template v-if="attachmentType === 'pdf'">
                                    <iframe
                                        :src="`/leave-request/${attachmentRequest?.id}/pdf`"
                                        class="w-full border-0 rounded-2xl shadow bg-white"
                                        style="height: 60vh; min-height: 480px;"
                                        frameborder="0">
                                    </iframe>
                                </template>
                                <!-- หลักฐานการลา -->
                                <template v-else>
                                    <!-- รูปภาพ -->
                                    <div v-if="isImage(attachmentRequest?.attachment_path)" class="flex items-center justify-center h-full">
                                        <img :src="`/storage/${attachmentRequest?.attachment_path}`"
                                            class="max-w-full max-h-[60vh] rounded-2xl shadow-lg object-contain"
                                            alt="หลักฐานการลา">
                                    </div>
                                    <!-- PDF หลักฐาน -->
                                    <iframe
                                        v-else-if="isPdf(attachmentRequest?.attachment_path)"
                                        :src="`/storage/${attachmentRequest?.attachment_path}`"
                                        class="w-full border-0 rounded-2xl shadow bg-white"
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
                                    class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-lg transition-all hover:-translate-y-0.5">
                                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                    เปิดในแท็บใหม่
                                </a>
                                <a v-else-if="attachmentRequest?.attachment_path"
                                    :href="`/storage/${attachmentRequest?.attachment_path}`" target="_blank"
                                    class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-lg transition-all hover:-translate-y-0.5">
                                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                    ดาวน์โหลด
                                </a>
                                <button type="button" @click="closeAttachmentModal"
                                    class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-white border border-slate-200 text-slate-500 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-50 transition-all">
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
.premium-bg {
    min-height: 100vh;
    background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
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
.filter-input {
    background: rgba(248, 250, 252, 0.8);
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: all 0.3s ease;
}
.filter-input:focus { background: white; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
</style>

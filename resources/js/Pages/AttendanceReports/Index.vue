<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    logs: Object,
    courses: Array,
    courseId: [Number, String],
    startDate: String,
    endDate: String,
    sort: String,
    empSort: String,
    totalScansCount: Number,
    uniqueStudentsCount: Number,
    totalStudents: Number,
    absentStudents: [Array, Object],
    lateStudents: [Array, Object],
    absentCount: Number,
    lateCount: Number,
    onLeaveStudentCount: Number,
    onLeaveStudents: [Array, Object],
    employeeLogs: Object,
    lateEmployees: [Array, Object],
    absentEmployees: [Array, Object],
    onLeaveEmployees: [Array, Object],
    totalEmployees: Number,
    presentEmployeeCount: Number,
    lateEmployeeCount: Number,
    absentEmployeeCount: Number,
    onLeaveCount: Number,
    totalEmployeeScans: Number,
    uniqueEmployeesCount: Number,
});

const filterCourse = ref(props.courseId || '');
const filterStart = ref(props.startDate || '');
const filterEnd = ref(props.endDate || '');
const filterSort = ref(props.sort || 'date_desc');
const filterEmpSort = ref(props.empSort || 'date_desc');

function applyFilter() {
    router.get('/attendance-reports', {
        course_id: filterCourse.value || undefined,
        start_date: filterStart.value || undefined,
        end_date: filterEnd.value || undefined,
        sort: filterSort.value || undefined,
        emp_sort: filterEmpSort.value || undefined,
    }, { preserveState: true });
}

function faPhotoUrl(path, type = 'student') {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    const baseUrl = type === 'employee' 
        ? 'https://faceattendance.nass.ac.th/storage-file?path='
        : 'https://faceattendance.nass.ac.th/storage-file?path=';
    return baseUrl + encodeURIComponent(path);
}

function snapshotUrl(path, type = 'student') {
    if (!path) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    const baseUrl = type === 'employee'
        ? 'https://faceattendance.nass.ac.th/storage-file?path='
        : 'https://faceattendance.nass.ac.th/storage-file?path=';
    return baseUrl + encodeURIComponent(path);
}

function studentFullName(log) {
    const s = log.student;
    if (!s) return '-';
    return [s.first_name, s.last_name].filter(Boolean).join(' ') || s.name || '-';
}

function employeeFullName(log) {
    const e = log.employee;
    if (!e) return '-';
    return [e.first_name, e.last_name].filter(Boolean).join(' ') || e.name || '-';
}

function formatTime(val) {
    if (!val) return '-';
    const str = String(val);
    const match = str.match(/(\d{2}:\d{2})/);
    if (!match) return str;
    return match[1];
}

function formatDate(val) {
    if (!val) return '-';
    const dateStr = String(val).substring(0, 10);
    const [y, m, d] = dateStr.split('-').map(Number);
    if (!y || !m || !d) return dateStr;
    const date = new Date(y, m - 1, d);
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        calendar: 'buddhist',
    });
}

const absentStudentsList = computed(() => {
    const d = props.absentStudents;
    if (!d) return [];
    if (Array.isArray(d)) return d;
    return Object.values(d);
});

const lateStudentsList = computed(() => {
    const d = props.lateStudents;
    if (!d) return [];
    if (Array.isArray(d)) return d;
    return Object.values(d);
});

const onLeaveStudentsList = computed(() => {
    const d = props.onLeaveStudents;
    if (!d) return [];
    if (Array.isArray(d)) return d;
    return Object.values(d);
});

const absentEmployeesList = computed(() => {
    const d = props.absentEmployees;
    if (!d) return [];
    if (Array.isArray(d)) return d;
    return Object.values(d);
});

const lateEmployeesList = computed(() => {
    const d = props.lateEmployees;
    if (!d) return [];
    if (Array.isArray(d)) return d;
    return Object.values(d);
});

const onLeaveEmployeesList = computed(() => {
    const d = props.onLeaveEmployees;
    if (!d) return [];
    if (Array.isArray(d)) return d;
    return Object.values(d);
});

const activeTab = ref('student');

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="รายงานการเข้าเรียน/ทำงาน">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i data-lucide="scan-face" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">รายงานการเข้าเรียน/ทำงาน</h2>
                    <p class="text-sm text-slate-400 font-medium mt-0.5">ข้อมูลจากระบบ Face Attendance</p>
                </div>
                <div class="ml-auto">
                    <a :href="`/attendance-reports/export-pdf?course_id=${filterCourse}&date=${filterStart}`"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs uppercase tracking-widest shadow-lg shadow-rose-500/20 transition-all hover:-translate-y-0.5">
                        <i data-lucide="file-text" class="w-4 h-4"></i> ส่งออก PDF
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="glass-panel rounded-2xl p-6 mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100"><i data-lucide="filter" class="w-4 h-4"></i></div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">ตัวกรอง</h3>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
                    <select v-model="filterCourse" class="flex-1 min-w-[160px] px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        <option value="">ทุกหลักสูตร</option>
                        <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <input v-model="filterStart" type="date" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                    <input v-model="filterEnd" type="date" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                    <select v-model="filterSort" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        <option value="date_desc">นักเรียน: วันล่าสุดก่อน</option>
                        <option value="earliest">นักเรียน: มาเร็วสุดก่อน</option>
                        <option value="latest">นักเรียน: มาช้าสุดก่อน</option>
                    </select>
                    <select v-model="filterEmpSort" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        <option value="date_desc">ข้าราชการ: วันล่าสุดก่อน</option>
                        <option value="earliest">ข้าราชการ: มาเร็วสุดก่อน</option>
                        <option value="latest">ข้าราชการ: มาช้าสุดก่อน</option>
                    </select>
                    <button @click="applyFilter" class="px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i> กรอง
                    </button>
                </div>
            </div>

            <!-- ========== TABS ========== -->
            <div class="flex gap-2 mb-8">
                <button @click="activeTab = 'student'"
                    class="flex items-center gap-2 px-6 py-3 rounded-2xl font-black text-sm transition-all"
                    :class="activeTab === 'student' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200'">
                    <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    นักเรียนหลักสูตร
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black" :class="activeTab === 'student' ? 'bg-white/20 text-white' : 'bg-indigo-50 text-indigo-600'">{{ totalStudents ?? 0 }}</span>
                </button>
                <button @click="activeTab = 'employee'"
                    class="flex items-center gap-2 px-6 py-3 rounded-2xl font-black text-sm transition-all"
                    :class="activeTab === 'employee' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'bg-white text-slate-500 hover:bg-slate-50 border border-slate-200'">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    ข้าราชการ
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-black" :class="activeTab === 'employee' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600'">{{ totalEmployees ?? 0 }}</span>
                </button>
            </div>

            <!-- ==================== STUDENT TAB ==================== -->
            <div v-show="activeTab === 'student'">

                <!-- Student Stats -->
                <div class="mb-3"><p class="text-xs font-black text-indigo-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i data-lucide="graduation-cap" class="w-4 h-4"></i> สรุปนักเรียน</p></div>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center"><i data-lucide="scan" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">สแกนทั้งหมด</p></div>
                        <p class="text-2xl font-black text-slate-800">{{ totalScansCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i data-lucide="users" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">ทั้งหมด</p></div>
                        <p class="text-2xl font-black text-indigo-600">{{ totalStudents ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">มาเรียน</p></div>
                        <p class="text-2xl font-black text-emerald-600">{{ uniqueStudentsCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i data-lucide="clock" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">มาสาย</p></div>
                        <p class="text-2xl font-black text-amber-600">{{ lateCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i data-lucide="user-x" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">ขาดเรียน</p></div>
                        <p class="text-2xl font-black text-rose-600">{{ absentCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center"><i data-lucide="calendar-off" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">ลา/ราชการ</p></div>
                        <p class="text-2xl font-black text-sky-600">{{ onLeaveStudentCount ?? 0 }}</p>
                    </div>
                </div>

                <!-- Student Scan Logs Table -->
                <div class="glass-panel rounded-[2rem] overflow-hidden mb-8">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center"><i data-lucide="graduation-cap" class="w-4 h-4"></i></div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight">บันทึกการสแกน — นักเรียน</h3>
                            <p class="text-xs text-slate-400 font-medium">จัดกลุ่มตามนักเรียนและวัน (เช้า/บ่าย)</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">วันที่</th>
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">รูปประจำตัว</th>
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ-สกุล</th>
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">หลักสูตร</th>
                                    <th class="px-6 py-3 text-center font-black text-slate-400 text-xs uppercase tracking-widest">สแกนเช้า</th>
                                    <th class="px-6 py-3 text-center font-black text-slate-400 text-xs uppercase tracking-widest">สแกนบ่าย</th>
                                    <th class="px-6 py-3 text-center font-black text-slate-400 text-xs uppercase tracking-widest">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in (logs?.data || [])" :key="`${log.student_id}-${log.scan_date}`" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors align-top">
                                    <td class="px-6 py-3 text-xs font-bold text-slate-500 whitespace-nowrap">{{ formatDate(log.scan_date) }}</td>
                                    <td class="px-6 py-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                                            <img v-if="faPhotoUrl(log.student?.photo_path, 'student')" :src="faPhotoUrl(log.student?.photo_path, 'student')" class="w-full h-full object-cover" alt="">
                                            <div v-else class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-black">{{ (log.student?.first_name || '?').charAt(0) }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 font-bold text-slate-800">{{ studentFullName(log) }}</td>
                                    <td class="px-6 py-3 text-xs text-slate-500 font-medium">{{ log.student?.course?.name || '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <template v-if="log.morning">
                                            <div class="flex flex-col items-center gap-1.5">
                                                <span class="text-xs font-bold" :class="log.morning.is_late ? 'text-amber-600' : 'text-emerald-600'">{{ formatTime(log.morning.scan_time) }}</span>
                                                <span v-if="log.morning.is_late" class="text-[9px] font-black text-amber-500 bg-amber-50 rounded px-1">สาย</span>
                                                <div v-if="snapshotUrl(log.morning.snapshot_path, 'student')" class="w-16 h-16 rounded-lg overflow-hidden border border-slate-200 shadow-sm">
                                                    <img :src="snapshotUrl(log.morning.snapshot_path, 'student')" class="w-full h-full object-cover" alt="snapshot">
                                                </div>
                                                <div v-else class="w-16 h-16 rounded-lg bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center"><i data-lucide="image-off" class="w-4 h-4 text-slate-300"></i></div>
                                            </div>
                                        </template>
                                        <span v-else class="text-slate-300 text-xs font-bold">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <template v-if="log.afternoon">
                                            <div class="flex flex-col items-center gap-1.5">
                                                <span class="text-xs font-bold text-slate-600">{{ formatTime(log.afternoon.scan_time) }}</span>
                                                <div v-if="snapshotUrl(log.afternoon.snapshot_path, 'student')" class="w-16 h-16 rounded-lg overflow-hidden border border-slate-200 shadow-sm">
                                                    <img :src="snapshotUrl(log.afternoon.snapshot_path, 'student')" class="w-full h-full object-cover" alt="snapshot">
                                                </div>
                                                <div v-else class="w-16 h-16 rounded-lg bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center"><i data-lucide="image-off" class="w-4 h-4 text-slate-300"></i></div>
                                            </div>
                                        </template>
                                        <span v-else class="text-slate-300 text-xs font-bold">-</span>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <span v-if="log.leave_info" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-sky-50 text-sky-600 border border-sky-100 whitespace-nowrap">ลา: {{ log.leave_type_name || 'ลางาน' }}</span>
                                        <span v-else-if="log.morning?.is_late" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-50 text-amber-600 border border-amber-100">มาสาย</span>
                                        <span v-else-if="log.morning" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100">ปกติ</span>
                                        <span v-else class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-50 text-rose-500 border border-rose-100">ไม่มีข้อมูล</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="!logs?.data || logs.data.length === 0" class="p-16 text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner"><i data-lucide="inbox" class="w-7 h-7 text-slate-200"></i></div>
                        <p class="text-slate-400 font-bold text-sm">ไม่มีข้อมูลนักเรียน</p>
                    </div>
                    <div v-if="logs?.links && logs.links.length > 3" class="px-6 py-4 border-t border-slate-50 flex justify-center gap-1">
                        <template v-for="link in logs.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-4 py-2 rounded-xl text-xs font-black transition-all" :class="link.active ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                            <span v-else class="px-3 py-2 text-xs text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>

                <!-- Student Late / Absent / OnLeave -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-amber-50/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4"></i></div>
                            <h4 class="text-sm font-black text-slate-800">นักเรียนมาสาย <span class="text-amber-500">({{ lateStudentsList.length }})</span></h4>
                        </div>
                        <div v-if="lateStudentsList.length > 0" class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <div v-for="log in lateStudentsList" :key="log.id" class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/50">
                                <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 text-xs font-black">{{ (log.student?.first_name || '?').charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ [log.student?.first_name, log.student?.last_name].filter(Boolean).join(' ') || '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ log.student?.course?.name || '-' }}</p>
                                </div>
                                <span class="text-xs font-black text-amber-600 whitespace-nowrap">{{ formatTime(log.scan_time) }}</span>
                            </div>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-sm text-slate-400 font-bold">ไม่มีนักเรียนมาสาย</div>
                    </div>
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-rose-50/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><i data-lucide="user-x" class="w-4 h-4"></i></div>
                            <h4 class="text-sm font-black text-slate-800">นักเรียนขาดเรียน <span class="text-rose-500">({{ absentStudentsList.length }})</span></h4>
                        </div>
                        <div v-if="absentStudentsList.length > 0" class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <div v-for="student in absentStudentsList" :key="student.id" class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/50">
                                <div class="w-7 h-7 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0 text-xs font-black">{{ (student.first_name || '?').charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ [student.first_name, student.last_name].filter(Boolean).join(' ') || '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ student.course?.name || '-' }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-black bg-rose-50 text-rose-500 border border-rose-100 rounded">ขาด</span>
                            </div>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-sm text-slate-400 font-bold">ไม่มีนักเรียนขาดเรียน</div>
                    </div>
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-sky-50/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center"><i data-lucide="calendar-off" class="w-4 h-4"></i></div>
                            <h4 class="text-sm font-black text-slate-800">นักเรียนลา/ราชการ <span class="text-sky-500">({{ onLeaveStudentsList.length }})</span></h4>
                        </div>
                        <div v-if="onLeaveStudentsList.length > 0" class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <div v-for="student in onLeaveStudentsList" :key="student.id" class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/50">
                                <div class="w-7 h-7 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-xs font-black">{{ (student.first_name || '?').charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ [student.first_name, student.last_name].filter(Boolean).join(' ') || '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ student.course?.name || '-' }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-black bg-sky-50 text-sky-600 border border-sky-100 rounded whitespace-nowrap">{{ student.leave_type_name || 'ลางาน' }}</span>
                            </div>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-sm text-slate-400 font-bold">ไม่มีนักเรียนลา</div>
                    </div>
                </div>

            </div><!-- /student tab -->

            <!-- ==================== EMPLOYEE TAB ==================== -->
            <div v-show="activeTab === 'employee'">

                <!-- Employee Stats -->
                <div class="mb-3"><p class="text-xs font-black text-blue-500 uppercase tracking-widest mb-3 flex items-center gap-2"><i data-lucide="shield" class="w-4 h-4"></i> สรุปข้าราชการ</p></div>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="users" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">ทั้งหมด</p></div>
                        <p class="text-2xl font-black text-blue-600">{{ totalEmployees ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="user-check" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">มาทำงาน</p></div>
                        <p class="text-2xl font-black text-emerald-600">{{ presentEmployeeCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i data-lucide="clock" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">มาสาย</p></div>
                        <p class="text-2xl font-black text-amber-600">{{ lateEmployeeCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center"><i data-lucide="user-x" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">ขาด</p></div>
                        <p class="text-2xl font-black text-rose-600">{{ absentEmployeeCount ?? 0 }}</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-2 mb-2"><div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center"><i data-lucide="calendar-off" class="w-3.5 h-3.5"></i></div><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">ลา/ราชการ</p></div>
                        <p class="text-2xl font-black text-sky-600">{{ onLeaveCount ?? 0 }}</p>
                    </div>
                </div>

                <!-- Employee Scan Logs Table -->
                <div class="glass-panel rounded-[2rem] overflow-hidden mb-8">
                    <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center"><i data-lucide="shield" class="w-4 h-4"></i></div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 tracking-tight">บันทึกการสแกน — ข้าราชการ</h3>
                            <p class="text-xs text-slate-400 font-medium">เฉพาะช่วงเช้า</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">วันที่</th>
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">รูปประจำตัว</th>
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ-สกุล</th>
                                    <th class="px-6 py-3 text-left font-black text-slate-400 text-xs uppercase tracking-widest">แผนก</th>
                                    <th class="px-6 py-3 text-center font-black text-slate-400 text-xs uppercase tracking-widest">สแกนเช้า</th>
                                    <th class="px-6 py-3 text-center font-black text-slate-400 text-xs uppercase tracking-widest">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in (employeeLogs?.data || [])" :key="`emp-${log.employee_id}-${log.scan_date}`" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors align-top">
                                    <td class="px-6 py-3 text-xs font-bold text-slate-500 whitespace-nowrap">{{ formatDate(log.scan_date) }}</td>
                                    <td class="px-6 py-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0">
                                            <img v-if="faPhotoUrl(log.employee?.photo_path, 'employee')" :src="faPhotoUrl(log.employee?.photo_path, 'employee')" class="w-full h-full object-cover" alt="">
                                            <div v-else class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-black">{{ (log.employee?.first_name || '?').charAt(0) }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 font-bold text-slate-800">{{ employeeFullName(log) }}</td>
                                    <td class="px-6 py-3 text-xs text-slate-500 font-medium">{{ log.employee?.department || '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <template v-if="log.morning">
                                            <div class="flex flex-col items-center gap-1.5">
                                                <span class="text-xs font-bold" :class="log.morning.is_late ? 'text-amber-600' : 'text-emerald-600'">{{ formatTime(log.morning.scan_time) }}</span>
                                                <span v-if="log.morning.is_late" class="text-[9px] font-black text-amber-500 bg-amber-50 rounded px-1">สาย</span>
                                                <div v-if="snapshotUrl(log.morning.snapshot_path, 'employee')" class="w-16 h-16 rounded-lg overflow-hidden border border-slate-200 shadow-sm">
                                                    <img :src="snapshotUrl(log.morning.snapshot_path, 'employee')" class="w-full h-full object-cover" alt="snapshot">
                                                </div>
                                                <div v-else class="w-16 h-16 rounded-lg bg-slate-50 border border-dashed border-slate-200 flex items-center justify-center"><i data-lucide="image-off" class="w-4 h-4 text-slate-300"></i></div>
                                            </div>
                                        </template>
                                        <span v-else class="text-slate-300 text-xs font-bold">-</span>
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <span v-if="log.leave_info" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-sky-50 text-sky-600 border border-sky-100 whitespace-nowrap">{{ log.leave_type_name || 'ลางาน' }}</span>
                                        <span v-else-if="log.morning?.is_late" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-amber-50 text-amber-600 border border-amber-100">มาสาย</span>
                                        <span v-else-if="log.morning" class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100">ปกติ</span>
                                        <span v-else class="px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-50 text-rose-500 border border-rose-100">ไม่มีข้อมูล</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="!employeeLogs?.data || employeeLogs.data.length === 0" class="p-16 text-center">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner"><i data-lucide="inbox" class="w-7 h-7 text-slate-200"></i></div>
                        <p class="text-slate-400 font-bold text-sm">ไม่มีข้อมูลข้าราชการ</p>
                    </div>
                    <div v-if="employeeLogs?.links && employeeLogs.links.length > 3" class="px-6 py-4 border-t border-slate-50 flex justify-center gap-1">
                        <template v-for="link in employeeLogs.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-4 py-2 rounded-xl text-xs font-black transition-all" :class="link.active ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                            <span v-else class="px-3 py-2 text-xs text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>

                <!-- Employee Late / Absent / OnLeave -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-amber-50/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4"></i></div>
                            <h4 class="text-sm font-black text-slate-800">ข้าราชการมาสาย <span class="text-amber-500">({{ lateEmployeesList.length }})</span></h4>
                        </div>
                        <div v-if="lateEmployeesList.length > 0" class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <div v-for="log in lateEmployeesList" :key="log.id" class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/50">
                                <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 text-xs font-black">{{ (log.employee?.first_name || '?').charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ [log.employee?.first_name, log.employee?.last_name].filter(Boolean).join(' ') || '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ log.employee?.department || '-' }}</p>
                                </div>
                                <span class="text-xs font-black text-amber-600 whitespace-nowrap">{{ formatTime(log.scan_time) }}</span>
                            </div>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-sm text-slate-400 font-bold">ไม่มีข้าราชการมาสาย</div>
                    </div>
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-rose-50/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"><i data-lucide="user-x" class="w-4 h-4"></i></div>
                            <h4 class="text-sm font-black text-slate-800">ข้าราชการขาด <span class="text-rose-500">({{ absentEmployeesList.length }})</span></h4>
                        </div>
                        <div v-if="absentEmployeesList.length > 0" class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <div v-for="emp in absentEmployeesList" :key="emp.id" class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/50">
                                <div class="w-7 h-7 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0 text-xs font-black">{{ (emp.first_name || '?').charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ [emp.first_name, emp.last_name].filter(Boolean).join(' ') || '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ emp.department || '-' }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-black bg-rose-50 text-rose-500 border border-rose-100 rounded">ขาด</span>
                            </div>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-sm text-slate-400 font-bold">ไม่มีข้าราชการขาด</div>
                    </div>
                    <div class="glass-panel rounded-2xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-sky-50/40 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center"><i data-lucide="calendar-off" class="w-4 h-4"></i></div>
                            <h4 class="text-sm font-black text-slate-800">ข้าราชการลา/ราชการ <span class="text-sky-500">({{ onLeaveEmployeesList.length }})</span></h4>
                        </div>
                        <div v-if="onLeaveEmployeesList.length > 0" class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                            <div v-for="emp in onLeaveEmployeesList" :key="emp.id" class="px-6 py-3 flex items-center gap-3 hover:bg-slate-50/50">
                                <div class="w-7 h-7 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-xs font-black">{{ (emp.first_name || '?').charAt(0) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ [emp.first_name, emp.last_name].filter(Boolean).join(' ') || '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ emp.department || '-' }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-[9px] font-black bg-sky-50 text-sky-600 border border-sky-100 rounded whitespace-nowrap">{{ emp.leave_type_name || 'ลางาน' }}</span>
                            </div>
                        </div>
                        <div v-else class="px-6 py-8 text-center text-sm text-slate-400 font-bold">ไม่มีข้าราชการลา</div>
                    </div>
                </div>

            </div><!-- /employee tab -->

        </div>
    </AppLayout>
</template>


<style scoped>
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
}
</style>

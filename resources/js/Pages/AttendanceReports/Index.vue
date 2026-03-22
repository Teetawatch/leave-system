<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    logs: Object, courses: Array, courseId: [Number, String],
    startDate: String, endDate: String,
    totalScansCount: Number, uniqueStudentsCount: Number, totalStudents: Number,
    absentStudents: [Array, Object], lateStudents: [Array, Object],
    employeeLogs: [Array, Object], lateEmployees: [Array, Object],
    absentEmployees: [Array, Object], onLeaveEmployees: [Array, Object],
    totalEmployees: Number, presentEmployeeCount: Number,
    lateEmployeeCount: Number, absentEmployeeCount: Number, onLeaveCount: Number,
});

const filterCourse = ref(props.courseId || '');
const filterStart = ref(props.startDate || '');
const filterEnd = ref(props.endDate || '');

function applyFilter() {
    router.get('/attendance-reports', {
        course_id: filterCourse.value || undefined,
        start_date: filterStart.value || undefined,
        end_date: filterEnd.value || undefined,
    }, { preserveState: true });
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="รายงานการเข้าเรียน/ทำงาน">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i data-lucide="scan-face" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">รายงานการเข้าเรียน/ทำงาน</h2>
                    <p class="text-sm text-slate-400 font-medium mt-0.5">ข้อมูลจากระบบ Face Attendance</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="glass-panel rounded-2xl p-6 mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100"><i data-lucide="filter" class="w-4 h-4"></i></div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">ตัวกรอง</h3>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <select v-model="filterCourse" class="flex-1 px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                        <option value="">ทุกหลักสูตร</option>
                        <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <input v-model="filterStart" type="date" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                    <input v-model="filterEnd" type="date" class="px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                    <button @click="applyFilter" class="px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-0.5">กรอง</button>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center"><i data-lucide="scan" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">สแกนทั้งหมด</p></div>
                    <p class="text-3xl font-black text-slate-800">{{ totalScansCount || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><i data-lucide="graduation-cap" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">นักเรียน</p></div>
                    <p class="text-3xl font-black text-indigo-600">{{ totalStudents || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="check-circle" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">มาเรียน</p></div>
                    <p class="text-3xl font-black text-emerald-600">{{ uniqueStudentsCount || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i data-lucide="users" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ข้าราชการ</p></div>
                    <p class="text-3xl font-black text-blue-600">{{ totalEmployees || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="user-check" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ข้าราชการมา</p></div>
                    <p class="text-3xl font-black text-emerald-600">{{ presentEmployeeCount || 0 }}</p>
                </div>
            </div>

            <!-- Scan Logs Table -->
            <div class="glass-panel rounded-[2rem] overflow-hidden mb-8">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center"><i data-lucide="list" class="w-5 h-5"></i></div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">บันทึกการสแกน</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">เวลาสแกน</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">สถานะ</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="log in (logs?.data || [])" :key="log.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ log.student?.name || log.employee?.name || '-' }}</td>
                                <td class="px-6 py-4 text-slate-500 text-xs font-bold">{{ log.scan_time }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg text-xs font-black border" :class="log.is_late ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100'">
                                        {{ log.is_late ? 'สาย' : 'ตรงเวลา' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="!logs?.data || logs.data.length === 0" class="p-20 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner"><i data-lucide="inbox" class="w-8 h-8 text-slate-200"></i></div>
                    <p class="text-slate-400 font-bold">ไม่มีข้อมูล</p>
                </div>
            </div>

            <div v-if="logs?.links && logs.links.length > 3" class="flex justify-center gap-1 mt-8">
                <template v-for="link in logs.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-xl text-sm font-black transition-all" :class="link.active ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                    <span v-else class="px-4 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
                </template>
            </div>
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

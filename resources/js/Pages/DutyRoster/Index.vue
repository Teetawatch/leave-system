<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const props = defineProps({ days: Array, year: Number, month: Number, monthName: String, thaiYear: Number, seniorRosters: Array });

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
    return dateStr === new Date().toISOString().split('T')[0];
}
function isWeekend(dateStr) {
    const d = new Date(dateStr);
    return d.getDay() === 0 || d.getDay() === 6;
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="ตารางเวรประจำเดือน">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <i data-lucide="shield" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">ตารางเวรประจำเดือน</h1>
                            <p class="text-xs text-slate-400 font-medium">Duty Roster Schedule</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl border border-slate-100 text-xs font-medium">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-br from-blue-400 to-blue-600"></div>
                            <span class="text-slate-600">นายทหารเวร</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl border border-slate-100 text-xs font-medium">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-br from-pink-400 to-pink-600"></div>
                            <span class="text-slate-600">ผู้ช่วยนายทหารเวร</span>
                        </div>
                        <a :href="`/duty-roster/export-pdf?year=${year}&month=${month}`" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-100 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs">
                            <i data-lucide="file-text" class="w-4 h-4"></i> ส่งออก PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Month Navigation -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 mb-6 flex items-center justify-between">
                <Link :href="`/duty-roster?year=${prevMonth.year}&month=${prevMonth.month}`"
                    class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-500 flex items-center justify-center hover:border-indigo-500 hover:text-indigo-500 hover:scale-105 transition-all">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </Link>
                <div class="text-center">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800">{{ monthName }} {{ thaiYear }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กำหนดเวรแล้ว {{ rosterCount }} วัน / {{ days?.length || 0 }} วัน</p>
                </div>
                <Link :href="`/duty-roster?year=${nextMonth.year}&month=${nextMonth.month}`"
                    class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-500 flex items-center justify-center hover:border-indigo-500 hover:text-indigo-500 hover:scale-105 transition-all">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </Link>
            </div>

            <!-- Senior Duty Rosters -->
            <div v-if="seniorRosters && seniorRosters.length > 0" class="mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="sr in seniorRosters" :key="sr.id"
                        class="senior-card relative overflow-hidden rounded-2xl p-4 border border-amber-300 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-amber-400 to-amber-600 rounded-l-2xl"></div>
                        <div class="ml-3">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">นายทหารเวรอาวุโส</p>
                            <p class="font-bold text-slate-800 text-sm">{{ sr.senior_officer?.rank }} {{ sr.senior_officer?.name }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ sr.start_date }} — {{ sr.end_date }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Roster Table -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">วันที่</th>
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">นายทหารเวร</th>
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ผู้ช่วยนายทหารเวร</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="day in days" :key="day.date"
                                class="border-b border-slate-50 transition-colors"
                                :class="[
                                    isToday(day.date) ? 'bg-indigo-50/50 ring-2 ring-indigo-500/20 ring-inset' : '',
                                    isWeekend(day.date) && !isToday(day.date) ? 'bg-rose-50/30' : '',
                                    !isWeekend(day.date) && !isToday(day.date) && day.roster ? 'bg-emerald-50/20' : '',
                                    'hover:bg-slate-50/80'
                                ]">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span v-if="isToday(day.date)" class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                        <span class="font-bold" :class="isToday(day.date) ? 'text-indigo-600' : isWeekend(day.date) ? 'text-rose-500' : 'text-slate-700'">{{ day.date }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="day.roster?.duty_officer" class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                                        {{ day.roster.duty_officer.rank }} {{ day.roster.duty_officer.name }}
                                    </span>
                                    <span v-else class="text-slate-300">—</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="day.roster?.assistant_duty_officer" class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-pink-50 text-pink-700 text-xs font-semibold border border-pink-100">
                                        {{ day.roster.assistant_duty_officer.rank }} {{ day.roster.assistant_duty_officer.name }}
                                    </span>
                                    <span v-else class="text-slate-300">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.senior-card {
    background: linear-gradient(135deg, #fefce8 0%, #fef9c3 50%, #fde68a 100%);
}
</style>

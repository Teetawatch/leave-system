<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onMounted, nextTick } from 'vue';

const props = defineProps({ departments: Array, leaveTypes: Array });

const selectedDepartment = ref('all');
const showGuardChange = ref(true);
const events = ref([]);
const loading = ref(false);

// Calendar navigation
const today = new Date();
const currentYear = ref(today.getFullYear());
const currentMonth = ref(today.getMonth()); // 0-indexed

const thaiMonths = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
const thaiDaysShort = ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'];

const currentMonthLabel = computed(() => `${thaiMonths[currentMonth.value]} ${currentYear.value + 543}`);

function getMonthRange() {
    const s = new Date(currentYear.value, currentMonth.value, 1).toISOString().split('T')[0];
    const e = new Date(currentYear.value, currentMonth.value + 1, 0).toISOString().split('T')[0];
    return { s, e };
}

async function fetchEvents() {
    loading.value = true;
    try {
        const { s, e } = getMonthRange();
        const params = new URLSearchParams({ start: s, end: e, department: selectedDepartment.value, show_guard_change: showGuardChange.value ? 'true' : 'false' });
        const res = await fetch(`/calendar/events?${params}`);
        events.value = await res.json();
    } catch (err) { console.error(err); }
    loading.value = false;
    await nextTick();
    if (window.lucide) window.lucide.createIcons();
}

function prevMonth() {
    if (currentMonth.value === 0) { currentMonth.value = 11; currentYear.value--; }
    else { currentMonth.value--; }
    fetchEvents();
}
function nextMonth() {
    if (currentMonth.value === 11) { currentMonth.value = 0; currentYear.value++; }
    else { currentMonth.value++; }
    fetchEvents();
}
function goToday() {
    currentYear.value = today.getFullYear();
    currentMonth.value = today.getMonth();
    fetchEvents();
}

function onFilterChange() { fetchEvents(); }

// Build calendar grid cells
const calendarDays = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay(); // 0=Sun
    const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
    const prevMonthDays = new Date(currentYear.value, currentMonth.value, 0).getDate();
    const cells = [];

    // Leading days from previous month
    for (let i = firstDay - 1; i >= 0; i--) {
        cells.push({ day: prevMonthDays - i, currentMonth: false, date: null });
    }
    // Days of current month
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        cells.push({ day: d, currentMonth: true, date: dateStr });
    }
    // Trailing days to fill last row
    const remaining = 42 - cells.length;
    for (let d = 1; d <= remaining; d++) {
        cells.push({ day: d, currentMonth: false, date: null });
    }
    return cells;
});

// Map events to each date (handle multi-day spans)
function getEventsForDate(dateStr) {
    if (!dateStr) return [];
    return events.value.filter(ev => {
        const start = ev.start;
        // FullCalendar end is exclusive (+1 day), so we subtract 1 day for display
        const endExclusive = ev.end || ev.start;
        const endInclusive = endExclusive > start
            ? new Date(new Date(endExclusive).getTime() - 86400000).toISOString().split('T')[0]
            : ev.start;
        return dateStr >= start && dateStr <= endInclusive;
    });
}

const todayStr = today.toISOString().split('T')[0];

const onLeaveToday = computed(() => {
    return events.value.filter(ev => {
        const start = ev.start;
        const endExclusive = ev.end || ev.start;
        const endInclusive = endExclusive > start
            ? new Date(new Date(endExclusive).getTime() - 86400000).toISOString().split('T')[0]
            : ev.start;
        return todayStr >= start && todayStr <= endInclusive;
    }).length;
});

// Modal
const selectedEvent = ref(null);
function openModal(ev) { selectedEvent.value = ev; }
function closeModal() { selectedEvent.value = null; }

// Format date display (YYYY-MM-DD → DD/MM/YYYY Thai)
function formatDate(d) {
    if (!d) return '-';
    const [y, m, day] = d.split('-');
    return `${day}/${m}/${parseInt(y) + 543}`;
}

onMounted(() => {
    fetchEvents();
});
</script>

<template>
    <AppLayout title="ปฏิทินการลา">
        <div class="min-h-screen pb-12">
            <div class="max-w-7xl mx-auto">

                <!-- Page Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 flex-shrink-0">
                            <i data-lucide="calendar-days" class="w-7 h-7 text-indigo-600"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">ปฏิทินการลา</h1>
                            <p class="text-sm text-slate-500 font-medium mt-1">ภาพรวมและปฏิทินแสดงการลาของกำลังพลในหน่วย</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3">
                            <span class="flex h-2.5 w-2.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            </span>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none">ลาวันนี้</span>
                                <span class="text-base font-bold text-slate-800 leading-none mt-1">{{ onLeaveToday }}</span>
                            </div>
                        </div>
                        <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none">เดือนนี้</span>
                                <span class="text-base font-bold text-slate-800 leading-none mt-1">{{ events.length }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.05] transition-opacity duration-300">
                            <i data-lucide="users" class="w-28 h-28 text-slate-900"></i>
                        </div>
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            <div class="w-12 h-12 rounded-[1rem] bg-rose-50 flex items-center justify-center border border-rose-100">
                                <i data-lucide="user-minus" class="w-6 h-6 text-rose-500"></i>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">กำลังลาวันนี้</p>
                        <div class="flex items-baseline gap-2 relative z-10">
                            <span class="text-4xl font-extrabold text-slate-800 tabular-nums">{{ onLeaveToday }}</span>
                            <span class="text-sm font-medium text-slate-400">คน</span>
                        </div>
                    </div>
                    <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            <div class="w-12 h-12 rounded-[1rem] bg-indigo-50 flex items-center justify-center border border-indigo-100">
                                <i data-lucide="calendar-check" class="w-6 h-6 text-indigo-500"></i>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">คำขอลาเดือนนี้</p>
                        <div class="flex items-baseline gap-2 relative z-10">
                            <span class="text-4xl font-extrabold text-slate-800 tabular-nums">{{ events.length }}</span>
                            <span class="text-sm font-medium text-slate-400">รายการ</span>
                        </div>
                    </div>
                    <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            <div class="w-12 h-12 rounded-[1rem] bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                <i data-lucide="palmtree" class="w-6 h-6 text-emerald-500"></i>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">ลาพักร้อน</p>
                        <div class="flex items-baseline gap-2 relative z-10">
                            <span class="text-4xl font-extrabold text-slate-800 tabular-nums">{{ events.filter(e => e.extendedProps?.leaveTypeSlug === 'vacation').length }}</span>
                            <span class="text-sm font-medium text-slate-400">รายการ</span>
                        </div>
                    </div>
                    <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4 relative z-10">
                            <div class="w-12 h-12 rounded-[1rem] bg-amber-50 flex items-center justify-center border border-amber-100">
                                <i data-lucide="thermometer" class="w-6 h-6 text-amber-500"></i>
                            </div>
                        </div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">ลาป่วย</p>
                        <div class="flex items-baseline gap-2 relative z-10">
                            <span class="text-4xl font-extrabold text-slate-800 tabular-nums">{{ events.filter(e => e.extendedProps?.leaveTypeSlug === 'sick').length }}</span>
                            <span class="text-sm font-medium text-slate-400">รายการ</span>
                        </div>
                    </div>
                </div>

                <!-- Filters & Legend -->
                <div class="glass-panel p-4 rounded-[2rem] mb-6 relative z-20">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 w-full lg:w-auto">
                            <div class="relative group w-full sm:w-auto">
                                <select v-model="selectedDepartment" @change="onFilterChange"
                                    class="w-full sm:w-auto appearance-none bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 pr-10 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer min-w-[220px] hover:bg-white hover:border-slate-300 shadow-sm">
                                    <option value="all">ทุกแผนก / ทั้งหน่วย</option>
                                    <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
                                </select>
                            </div>
                            <label class="w-full sm:w-auto flex items-center justify-between sm:justify-start gap-3 cursor-pointer select-none bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 transition-colors shadow-sm">
                                <span class="text-sm font-bold text-slate-700">แสดงวันเปลี่ยนเวร</span>
                                <div class="relative">
                                    <input type="checkbox" v-model="showGuardChange" @change="onFilterChange" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:ring-4 peer-focus:ring-indigo-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                                </div>
                            </label>
                        </div>
                        <div class="flex flex-wrap items-center justify-center gap-3 px-2">
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm"></span>
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-widest">พักร้อน</span>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                <span class="w-3 h-3 rounded-full bg-rose-500 shadow-sm"></span>
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-widest">ลาป่วย</span>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm"></span>
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-widest">ลากิจ</span>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                <span class="w-3 h-3 rounded-full bg-violet-500 shadow-sm"></span>
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-widest">ลาอื่นๆ</span>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                <span class="w-3 h-3 rounded-full bg-slate-400 shadow-sm"></span>
                                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-widest">เปลี่ยนเวร</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="glass-panel rounded-[2rem] overflow-hidden">
                    <!-- Calendar Header: Month Nav -->
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <button @click="prevMonth" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-500 flex items-center justify-center transition-all">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </button>
                            <button @click="nextMonth" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-500 flex items-center justify-center transition-all">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </button>
                            <h2 class="text-lg font-extrabold text-slate-800 tracking-tight ml-1">{{ currentMonthLabel }}</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <div v-if="loading" class="flex items-center gap-2 text-indigo-500">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                <span class="text-xs font-bold">กำลังโหลด...</span>
                            </div>
                            <button @click="goToday" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition-all shadow-sm shadow-indigo-500/20">
                                วันนี้
                            </button>
                        </div>
                    </div>

                    <!-- Day headers -->
                    <div class="grid grid-cols-7 border-b border-slate-100">
                        <div v-for="(d, i) in thaiDaysShort" :key="d"
                            class="py-3 text-center text-[11px] font-extrabold uppercase tracking-widest"
                            :class="i === 0 ? 'text-rose-400' : i === 6 ? 'text-indigo-400' : 'text-slate-400'">
                            {{ d }}
                        </div>
                    </div>

                    <!-- Calendar cells -->
                    <div class="grid grid-cols-7 divide-x divide-y divide-slate-100">
                        <div v-for="(cell, idx) in calendarDays" :key="idx"
                            class="min-h-[110px] p-1.5 relative"
                            :class="[
                                !cell.currentMonth ? 'bg-slate-50/60' : 'bg-white hover:bg-indigo-50/20 transition-colors',
                                cell.date === todayStr ? 'bg-indigo-50/40' : '',
                            ]">
                            <!-- Day number -->
                            <div class="flex justify-end mb-1 px-1">
                                <span class="text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full transition-colors"
                                    :class="[
                                        cell.date === todayStr ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : '',
                                        !cell.currentMonth ? 'text-slate-300' : (idx % 7 === 0 ? 'text-rose-400' : idx % 7 === 6 ? 'text-indigo-400' : 'text-slate-600'),
                                    ]">
                                    {{ cell.day }}
                                </span>
                            </div>

                            <!-- Events on this day -->
                            <div class="space-y-0.5">
                                <template v-if="cell.date">
                                    <button
                                        v-for="ev in getEventsForDate(cell.date).slice(0, 3)"
                                        :key="ev.id"
                                        @click="openModal(ev)"
                                        class="w-full text-left px-1.5 py-0.5 rounded text-[10px] font-bold truncate leading-4 transition-opacity hover:opacity-80"
                                        :style="{ backgroundColor: ev.backgroundColor || '#6B7280', color: '#fff' }"
                                        :title="ev.title">
                                        {{ ev.title }}
                                    </button>
                                    <button v-if="getEventsForDate(cell.date).length > 3"
                                        @click="openModal(getEventsForDate(cell.date)[3])"
                                        class="w-full text-left px-1.5 py-0.5 rounded text-[10px] font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                                        +{{ getEventsForDate(cell.date).length - 3 }} รายการ
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event List below calendar -->
                <div class="glass-panel rounded-[2rem] overflow-hidden p-6 md:p-8 mt-6">
                    <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="list" class="w-5 h-5 text-indigo-500"></i>
                        รายการทั้งหมดในเดือน {{ currentMonthLabel }}
                        <span class="ml-auto text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">{{ events.length }} รายการ</span>
                    </h3>
                    <div v-if="loading" class="py-12 text-center">
                        <div class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                            <i data-lucide="loader-2" class="w-8 h-8 text-indigo-400"></i>
                        </div>
                        <p class="text-slate-400 font-bold">กำลังโหลดข้อมูล...</p>
                    </div>
                    <div v-else-if="events.length === 0" class="py-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="calendar-x" class="w-8 h-8 text-slate-200"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-800 mb-1">ไม่มีรายการในเดือนนี้</h3>
                        <p class="text-slate-400 text-sm font-medium">ยังไม่มีคำขอลาหรือเปลี่ยนเวรในช่วงเวลาที่เลือก</p>
                    </div>
                    <div v-else class="space-y-2">
                        <button v-for="event in events" :key="event.id"
                            @click="openModal(event)"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50/80 transition-all duration-200 group text-left">
                            <div class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm ring-4 ring-white" :style="{ background: event.backgroundColor || '#6B7280' }"></div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800 text-sm truncate group-hover:text-indigo-600 transition-colors">{{ event.title }}</p>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">
                                    {{ formatDate(event.start) }}
                                    <template v-if="event.extendedProps?.endDate && event.extendedProps.endDate !== event.extendedProps.startDate">
                                        — {{ event.extendedProps.endDate }}
                                    </template>
                                    <template v-if="event.extendedProps?.department">
                                        · {{ event.extendedProps.department }}
                                    </template>
                                </p>
                            </div>
                            <span v-if="event.extendedProps?.totalDays" class="px-3 py-1.5 bg-slate-100 rounded-xl text-xs font-black text-slate-500 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors shrink-0">{{ event.extendedProps.totalDays }} วัน</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Event Detail Modal -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="closeModal">
                <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95 translate-y-4" enter-to-class="opacity-100 scale-100 translate-y-0" leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100 scale-100 translate-y-0" leave-to-class="opacity-0 scale-95 translate-y-4">
                    <div v-if="selectedEvent" class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
                        <!-- Modal Header -->
                        <div class="px-6 pt-6 pb-4 flex items-start justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-10 rounded-full shrink-0" :style="{ background: selectedEvent.backgroundColor || '#6B7280' }"></div>
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-800 leading-tight">{{ selectedEvent.title }}</h3>
                                    <p class="text-xs text-slate-400 font-medium mt-0.5 uppercase tracking-wide">
                                        {{ selectedEvent.extendedProps?.type === 'guard_change' ? 'เปลี่ยนเวร' : selectedEvent.extendedProps?.leaveType }}
                                    </p>
                                </div>
                            </div>
                            <button @click="closeModal" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors shrink-0">
                                <i data-lucide="x" class="w-4 h-4 text-slate-500"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-6 pb-6 space-y-3">
                            <div class="bg-slate-50 rounded-2xl p-4 space-y-3">
                                <!-- Leave request details -->
                                <template v-if="selectedEvent.extendedProps?.type === 'leave'">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">วันที่เริ่มลา</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps?.startDate || formatDate(selectedEvent.start) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">วันที่สิ้นสุด</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps?.endDate }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">จำนวนวัน</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps?.totalDays }} วัน</span>
                                    </div>
                                    <div v-if="selectedEvent.extendedProps?.department" class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">แผนก</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps.department }}</span>
                                    </div>
                                    <div v-if="selectedEvent.extendedProps?.reason" class="pt-1 border-t border-slate-200">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">เหตุผล</span>
                                        <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ selectedEvent.extendedProps.reason }}</p>
                                    </div>
                                </template>

                                <!-- Guard change details -->
                                <template v-else-if="selectedEvent.extendedProps?.type === 'guard_change'">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">วันที่</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps?.originalDate || formatDate(selectedEvent.start) }}</span>
                                    </div>
                                    <div v-if="selectedEvent.extendedProps?.department" class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">แผนก</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps.department }}</span>
                                    </div>
                                    <div v-if="selectedEvent.extendedProps?.substituteUser" class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">คนรับเวร</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps.substituteUser }}</span>
                                    </div>
                                    <div v-if="selectedEvent.extendedProps?.dutyPosition" class="flex justify-between items-center">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">ตำแหน่งเวร</span>
                                        <span class="text-sm font-bold text-slate-700">{{ selectedEvent.extendedProps.dutyPosition }}</span>
                                    </div>
                                    <div v-if="selectedEvent.extendedProps?.reason" class="pt-1 border-t border-slate-200">
                                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">หมายเหตุ</span>
                                        <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ selectedEvent.extendedProps.reason }}</p>
                                    </div>
                                </template>
                            </div>

                            <button @click="closeModal" class="w-full py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-colors">
                                ปิด
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>

    </AppLayout>
</template>

<style scoped>
.glass-panel {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
}
.glass-panel:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
}
</style>

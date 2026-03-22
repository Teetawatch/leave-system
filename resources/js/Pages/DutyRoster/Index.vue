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
    if (isToday(day.date)) classes.push('today');
    else if (isWeekend(day.date)) classes.push('weekend');
    else if (day.roster) classes.push('assigned');
    return classes.join(' ');
}

function getDateClasses(day) {
    if (isToday(day.date)) return 'today';
    if (isWeekend(day.date)) return 'weekend';
    return 'normal';
}

function formatThaiDate(dateStr) {
    const date = new Date(dateStr);
    const day = date.getDate();
    const months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                   'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // Convert to Buddhist year
    return `${day} ${month} ${year}`;
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="ตารางเวรประจำเดือน">
        <div class="roster-container">
            <!-- Background decoration -->
            <div class="bg-decoration">
                <div class="bg-circle circle-1"></div>
                <div class="bg-circle circle-2"></div>
                <div class="bg-circle circle-3"></div>
            </div>

            <!-- Header -->
            <header class="roster-header">
                <div class="header-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    ตารางเวร
                </div>
                <h1 class="page-title">ตารางเวรประจำเดือน</h1>
                <p class="page-subtitle">ระบบจัดการเวรปฏิบัติหน้าที่ โรงเรียนพลาธิการ</p>

                <div class="header-actions">
                    <div class="legend-group">
                        <div class="legend-item">
                            <span class="legend-dot officer"></span>
                            <span class="legend-text">นายทหารเวร</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot assistant"></span>
                            <span class="legend-text">ผู้ช่วยนายทหารเวร</span>
                        </div>
                    </div>
                    <a :href="`/duty-roster/export-pdf?year=${year}&month=${month}`" target="_blank" class="export-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        ส่งออก PDF
                    </a>
                </div>
            </header>

            <!-- Month Navigation -->
            <div class="month-nav-card">
                <Link :href="`/duty-roster?year=${prevMonth.year}&month=${prevMonth.month}`" class="nav-btn nav-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </Link>
                <div class="month-info">
                    <h2 class="month-title">{{ monthName }} {{ thaiYear }}</h2>
                    <p class="month-stats">กำหนดเวรแล้ว <span class="stat-highlight">{{ rosterCount }}</span> วัน / <span class="stat-total">{{ days?.length || 0 }}</span> วัน</p>
                </div>
                <Link :href="`/duty-roster?year=${nextMonth.year}&month=${nextMonth.month}`" class="nav-btn nav-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </Link>
            </div>

            <!-- Senior Duty Rosters -->
            <section v-if="seniorRosters && seniorRosters.length > 0" class="senior-section">
                <h3 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    นายทหารเวรอาวุโส
                </h3>
                <div class="senior-grid">
                    <div v-for="sr in seniorRosters" :key="sr.id" class="senior-card">
                        <div class="senior-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="senior-content">
                            <div class="senior-label">นายทหารเวรอาวุโส</div>
                            <div class="senior-name">{{ sr.senior_officer?.rank }} {{ sr.senior_officer?.name }}</div>
                            <div class="senior-period">{{ sr.start_date }} — {{ sr.end_date }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Daily Roster Table -->
            <main class="roster-main">
                <div class="table-wrapper">
                    <table class="roster-table">
                        <thead>
                            <tr>
                                <th class="col-date">
                                    <div class="header-cell">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                        วันที่
                                    </div>
                                </th>
                                <th class="col-officer">
                                    <div class="header-cell">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        นายทหารเวร
                                    </div>
                                </th>
                                <th class="col-assistant">
                                    <div class="header-cell">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        ผู้ช่วยนายทหารเวร
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="day in days" :key="day.date" class="roster-row" :class="getRowClasses(day)">
                                <td class="cell-date">
                                    <div class="date-cell">
                                        <span v-if="isToday(day.date)" class="today-indicator"></span>
                                        <span class="date-text" :class="getDateClasses(day)">{{ formatThaiDate(day.date) }}</span>
                                    </div>
                                </td>
                                <td class="cell-officer">
                                    <div v-if="day.roster?.duty_officer" class="officer-badge officer">
                                        <span class="officer-rank">{{ day.roster.duty_officer.rank }}</span>
                                        <span class="officer-name">{{ day.roster.duty_officer.name }}</span>
                                    </div>
                                    <span v-else class="empty-cell">—</span>
                                </td>
                                <td class="cell-assistant">
                                    <div v-if="day.roster?.assistant_duty_officer" class="officer-badge assistant">
                                        <span class="officer-rank">{{ day.roster.assistant_duty_officer.rank }}</span>
                                        <span class="officer-name">{{ day.roster.assistant_duty_officer.name }}</span>
                                    </div>
                                    <span v-else class="empty-cell">—</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </AppLayout>
</template>

<style scoped>
/* ── Variables ─────────────────────────────────── */
:root {
    --navy: #1e3a5f;
    --navy-mid: #2d5282;
    --blue: #1a6db5;
    --blue-light: #3b82f6;
    --blue-pale: #dbeafe;
    --sky: #0ea5e9;
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-700: #334155;
    --gray-900: #0f172a;
    --amber: #f59e0b;
    --amber-light: #fef3c7;
    --amber-pale: #fefce8;
    --emerald: #10b981;
    --emerald-light: #d1fae5;
    --emerald-pale: #ecfdf5;
    --rose: #f43f5e;
    --rose-light: #fecaca;
    --rose-pale: #fef2f2;
    --radius: 0.75rem;
    --radius-sm: 0.5rem;
    --radius-lg: 1rem;
    --shadow: 0 1px 3px rgba(0,0,0,0.07), 0 4px 12px rgba(0,0,0,0.08);
    --shadow-lg: 0 4px 6px -1px rgba(0,0,0,0.07), 0 20px 40px -15px rgba(30,58,95,0.12);
}

/* ── Reset / Base ───────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Layout ─────────────────────────────────────── */
.roster-container {
    min-height: 100vh;
    font-family: 'Sarabun', 'Noto Sans Thai', 'Inter', sans-serif;
    background: var(--gray-50);
    position: relative;
    overflow: hidden;
    padding: 2rem 1.5rem;
}

/* ── Background decoration ──────────────────────── */
.bg-decoration { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
.bg-circle {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: breathe 8s ease-in-out infinite;
}
.circle-1 { width: 600px; height: 600px; background: radial-gradient(circle, #bfdbfe 0%, transparent 70%); top: -200px; right: -100px; opacity: 0.7; }
.circle-2 { width: 500px; height: 500px; background: radial-gradient(circle, #e0f2fe 0%, transparent 70%); bottom: -150px; left: -100px; opacity: 0.6; animation-delay: -3s; }
.circle-3 { width: 400px; height: 400px; background: radial-gradient(circle, #ede9fe 0%, transparent 70%); top: 40%; left: 30%; opacity: 0.4; animation-delay: -5s; }
@keyframes breathe { 0%,100% { transform: scale(1); } 50% { transform: scale(1.08); } }

/* ── Header ─────────────────────────────────────── */
.roster-header {
    position: relative;
    z-index: 1;
    text-align: center;
    margin-bottom: 2.5rem;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.header-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    background: rgba(26,109,181,0.1);
    border: 1px solid rgba(26,109,181,0.2);
    color: var(--blue);
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.375rem 0.875rem;
    border-radius: 100px;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(8px);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
}

.page-subtitle {
    font-size: 1rem;
    color: var(--gray-500);
    margin-bottom: 2rem;
}

.header-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.legend-group {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    background: var(--white);
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
}
.legend-dot.officer { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.legend-dot.assistant { background: linear-gradient(135deg, #ec4899, #db2777); }

.legend-text {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
}

.export-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--navy), var(--blue));
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: var(--radius-lg);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.25s;
    box-shadow: var(--shadow);
}
.export-btn:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-lg);
}

/* ── Month Navigation ───────────────────────────── */
.month-nav-card {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow);
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.nav-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px; height: 44px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    color: var(--gray-500);
    text-decoration: none;
    transition: all 0.2s;
}
.nav-btn:hover {
    background: var(--white);
    border-color: var(--blue-light);
    color: var(--blue-light);
    transform: scale(1.05);
}

.month-info { text-align: center; }
.month-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}
.month-stats {
    font-size: 0.875rem;
    color: var(--gray-500);
}
.stat-highlight { color: var(--emerald); font-weight: 600; }
.stat-total { color: var(--gray-400); }

/* ── Senior Section ───────────────────────────────── */
.senior-section {
    position: relative;
    z-index: 1;
    margin-bottom: 2rem;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 1rem;
}

.senior-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.senior-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, var(--amber-pale) 0%, var(--amber-light) 50%, #fde68a 100%);
    border: 1px solid var(--amber);
    border-radius: var(--radius-lg);
    padding: 1.25rem;
    transition: all 0.25s;
    position: relative;
    overflow: hidden;
}
.senior-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, var(--amber), #f59e0b);
}
.senior-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.senior-icon {
    width: 48px; height: 48px;
    background: rgba(245,158,11,0.15);
    border-radius: var(--radius);
    display: flex; align-items: center; justify-content: center;
    color: var(--amber);
    flex-shrink: 0;
}

.senior-content {
    flex: 1;
}
.senior-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--amber);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}
.senior-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}
.senior-period {
    font-size: 0.8125rem;
    color: var(--gray-500);
}

/* ── Roster Table ───────────────────────────────── */
.roster-main {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.table-wrapper {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
}

.roster-table {
    width: 100%;
    border-collapse: collapse;
}

.roster-table thead th {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 1rem 1.25rem;
    text-align: left;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
}

.header-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.roster-table tbody tr {
    border-bottom: 1px solid var(--gray-100);
    transition: background-color 0.2s;
}
.roster-table tbody tr:hover {
    background: var(--gray-50);
}

.roster-table tbody tr.today {
    background: var(--blue-pale);
    box-shadow: inset 3px 0 0 var(--blue-light);
}
.roster-table tbody tr.weekend {
    background: var(--rose-pale);
}
.roster-table tbody tr.assigned {
    background: var(--emerald-pale);
}

.roster-table tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
}

/* Date cell */
.date-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.today-indicator {
    width: 8px; height: 8px;
    background: var(--blue-light);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }

.date-text {
    font-weight: 600;
    font-size: 0.9375rem;
}
.date-text.today { color: var(--blue-light); }
.date-text.weekend { color: var(--rose); }
.date-text.normal { color: var(--gray-700); }

/* Officer badges */
.officer-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.875rem;
    border-radius: var(--radius-sm);
    font-size: 0.8125rem;
    font-weight: 600;
    border: 1px solid;
}
.officer-badge.officer {
    background: var(--blue-pale);
    color: var(--blue);
    border-color: var(--blue-light);
}
.officer-badge.assistant {
    background: var(--rose-pale);
    color: var(--rose);
    border-color: var(--rose-light);
}

.officer-rank {
    font-weight: 700;
}

.empty-cell {
    color: var(--gray-300);
    font-weight: 600;
}

/* ── Responsive ───────────────────────────────── */
@media (max-width: 768px) {
    .roster-container { padding: 1rem; }
    .page-title { font-size: 1.5rem; }
    .header-actions { flex-direction: column; gap: 1rem; }
    .legend-group { flex-direction: column; gap: 0.75rem; }
    .month-nav-card { flex-direction: column; gap: 1rem; }
    .senior-grid { grid-template-columns: 1fr; }
    .table-wrapper { overflow-x: auto; }
    .roster-table { min-width: 600px; }
}
</style>

<x-app-layout>
    @section('title', 'ปฏิทินการลา')

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Fira+Sans:wght@300;400;500;600;700&display=swap');

            :root {
                --primary: #7C3AED;
                --primary-glow: rgba(124, 58, 237, 0.15);
                --secondary: #A78BFA;
                --cta: #F97316;
                --bg-main: #FAF5FF;
                --text-main: #4C1D95;
                --glass-bg: rgba(255, 255, 255, 0.6);
                --glass-border: rgba(255, 255, 255, 0.4);
                --card-shadow: 0 20px 40px -12px rgba(76, 29, 149, 0.08);
            }

            body {
                font-family: 'Fira Sans', 'Sarabun', sans-serif;
                background-color: var(--bg-main);
            }

            h1, h2, h3, h4, .font-display {
                font-family: 'Fira Code', monospace;
            }

            /* Liquid Glass Utility */
            .liquid-glass {
                background: var(--glass-bg);
                backdrop-filter: blur(16px) saturate(180%);
                -webkit-backdrop-filter: blur(16px) saturate(180%);
                border: 1px solid var(--glass-border);
            }

            .glass-card {
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.4));
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: var(--card-shadow);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .glass-card:hover {
                transform: translateY(-4px) scale(1.01);
                box-shadow: 0 30px 60px -12px rgba(76, 29, 149, 0.12);
                border-color: rgba(124, 58, 237, 0.3);
            }

            /* Animated Background Blobs */
            .blob-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
                overflow: hidden;
                pointer-events: none;
            }

            .blob {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.2;
                animation: float 20s infinite alternate ease-in-out;
            }

            @keyframes float {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(100px, 100px) scale(1.2); }
            }

            /* Custom Calendar Styles */
            .fc {
                font-family: inherit;
                --fc-border-color: rgba(124, 58, 237, 0.05);
                --fc-daygrid-event-dot-width: 8px;
                --fc-today-bg-color: rgba(124, 58, 237, 0.08);
            }

            .fc .fc-toolbar-title {
                font-family: 'Fira Code', monospace;
                font-weight: 700;
                color: var(--text-main);
                font-size: 1.5rem !important;
            }

            .fc .fc-button {
                background: var(--primary);
                border: none;
                border-radius: 12px;
                padding: 8px 16px;
                font-weight: 600;
                font-size: 0.875rem;
                transition: all 0.2s;
                text-transform: capitalize;
            }

            .fc .fc-button:hover {
                background: #6D28D9;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
            }

            .fc .fc-button-primary:not(:disabled).fc-button-active {
                background: #4C1D95;
            }

            .fc .fc-col-header-cell {
                padding: 12px 0;
                background: rgba(124, 58, 237, 0.03);
            }

            .fc .fc-col-header-cell-cushion {
                font-family: 'Fira Code', monospace;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: var(--text-main);
                opacity: 0.7;
            }

            .fc .fc-daygrid-day-number {
                font-family: 'Fira Code', monospace;
                color: var(--text-main);
                font-weight: 500;
                padding: 8px;
            }

            .fc .fc-day-today .fc-daygrid-day-number {
                background: var(--primary);
                color: white;
                border-radius: 8px;
                margin: 4px;
            }

            .fc-event {
                border: none !important;
                padding: 4px 8px !important;
                border-radius: 8px !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s, box-shadow 0.2s !important;
            }

            .fc-event:hover {
                transform: scale(1.03) translateY(-1px) !important;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
                z-index: 5;
            }

            /* Custom Skeleton */
            .skeleton {
                background: linear-gradient(90deg, #f3e8ff 25%, #faf5ff 50%, #f3e8ff 75%);
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s infinite;
            }

            @keyframes skeleton-loading {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }

            /* Modal Glass */
            .modal-glass {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(24px);
                border: 1px solid rgba(255, 255, 255, 0.6);
                box-shadow: 0 50px 100px -20px rgba(76, 29, 149, 0.25);
            }
        </style>
    @endpush

    <div class="relative min-h-screen overflow-x-hidden pt-6 pb-12">
        <!-- Background Orbs -->
        <div class="blob-container">
            <div class="blob bg-purple-400 w-[500px] h-[500px] -top-24 -left-24"></div>
            <div class="blob bg-indigo-400 w-[600px] h-[600px] top-1/2 -right-32"></div>
            <div class="blob bg-orange-300 w-[400px] h-[400px] -bottom-24 left-1/4"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="relative mb-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div class="flex items-start gap-5">
                        <div class="relative group">
                            <div class="absolute -inset-1 bg-gradient-to-r from-primary to-cta rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                            <div class="relative w-16 h-16 bg-white rounded-3xl flex items-center justify-center shadow-2xl glass-card">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-4xl font-extrabold text-text-main tracking-tight mb-2">
                                ปฏิทิน<span class="text-primary italic">การลา</span>
                            </h1>
                            <div class="flex items-center gap-2 text-primary/70 font-medium">
                                <span class="w-8 h-[2px] bg-primary/30"></span>
                                <p class="text-sm">ภาพรวมความเคลื่อนไหวของกำลังพล</p>
                            </div>
                        </div>
                    </div>

                    <!-- Header Stats -->
                    <div class="flex items-center gap-3">
                        <div class="glass-card px-5 py-3 rounded-2xl flex items-center gap-3">
                            <div class="w-2 h-2 bg-rose-500 rounded-full animate-ping"></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-text-main/50 font-bold">ลาวันนี้</p>
                                <p class="text-lg font-bold text-text-main font-display" id="headerOnLeaveToday">-</p>
                            </div>
                        </div>
                        <div class="glass-card px-5 py-3 rounded-2xl flex items-center gap-3 border-primary/10">
                            <div class="w-2 h-2 bg-primary rounded-full"></div>
                            <div>
                                <p class="text-[10px] uppercase tracking-widest text-text-main/50 font-bold">เดือนนี้</p>
                                <p class="text-lg font-bold text-text-main font-display" id="headerMonthlyTotal">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Stats Bento Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Stat 1 -->
                <div class="glass-card p-6 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity duration-500">
                        <svg class="w-24 h-24 text-text-main" fill="currentColor" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5 text-primary"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-text-main/50 uppercase tracking-widest mb-1">ลาวันนี้</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-text-main font-display" id="onLeaveTodayCount">-</span>
                        <span class="text-sm font-medium text-text-main/40 uppercase">คน</span>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="glass-card p-6 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity duration-500">
                        <svg class="w-24 h-24 text-text-main" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13zM7 13a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h.01a1 1 0 100-2H10zm3 0a1 1 0 000 2h.01a1 1 0 100-2H13z" clip-rule="evenodd"></path></svg>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                            <i data-lucide="file-spreadsheet" class="w-5 h-5 text-cta"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-text-main/50 uppercase tracking-widest mb-1">คำขอเดือนนี้</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-text-main font-display" id="monthlyRequestsCount">-</span>
                        <span class="text-sm font-medium text-text-main/40 uppercase">รายการ</span>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="glass-card p-6 relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i data-lucide="palm-tree" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-text-main/50 uppercase tracking-widest mb-1">ลาพักร้อน</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-text-main font-display" id="vacationCount">-</span>
                        <span class="text-sm font-medium text-text-main/40 uppercase">รายการ</span>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="glass-card p-6 relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center">
                            <i data-lucide="thermometer" class="w-5 h-5 text-rose-600"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-text-main/50 uppercase tracking-widest mb-1">ลาป่วย</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-text-main font-display" id="sickCount">-</span>
                        <span class="text-sm font-medium text-text-main/40 uppercase">รายการ</span>
                    </div>
                </div>
            </div>

            <!-- Filter & Legend Strip -->
            <div class="glass-card p-4 rounded-[2rem] mb-8 bg-white/40 border-white/60">
                <div class="flex flex-col xl:flex-row items-start xl:items-center justify-between gap-6">
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="relative group">
                            <select id="departmentFilter"
                                class="appearance-none bg-white border-2 border-primary/10 rounded-2xl px-6 py-3 pr-12 text-sm font-bold text-text-main focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all cursor-pointer min-w-[200px] hover:bg-slate-50 shadow-sm">
                                <option value="all">ทุกแผนก / ทั้งหน่วย</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-primary absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-50"></i>
                        </div>

                        <label class="group relative flex items-center gap-4 bg-white/50 px-6 py-3 rounded-2xl border-2 border-primary/5 hover:border-primary/20 transition-all cursor-pointer shadow-sm">
                            <input type="checkbox" id="showGuardChange" checked class="sr-only peer">
                            <div class="w-10 h-6 bg-slate-200 rounded-full peer-checked:bg-primary transition-colors relative ring-4 ring-transparent peer-focus:ring-primary/10">
                                <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow-lg transition-transform peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-bold text-text-main/80 select-none">แสดงการเปลี่ยนเวร</span>
                        </label>
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap items-center gap-6 px-4">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/20"></span>
                            <span class="text-xs font-bold text-text-main/60 uppercase tracking-wider">พักร้อน</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-rose-500 shadow-lg shadow-rose-500/20"></span>
                            <span class="text-xs font-bold text-text-main/60 uppercase tracking-wider">ลาป่วย</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-lg shadow-amber-500/20"></span>
                            <span class="text-xs font-bold text-text-main/60 uppercase tracking-wider">ลากิจ</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-lg shadow-indigo-500/20"></span>
                            <span class="text-xs font-bold text-text-main/60 uppercase tracking-wider">เปลี่ยนเวร</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Calendar Card -->
            <div class="glass-card rounded-[2.5rem] overflow-hidden bg-white/50 border-white/40 p-1">
                <div class="bg-white/90 rounded-[2.4rem] p-6 lg:p-10 relative">
                    <!-- Loader Overlay -->
                    <div id="calendarLoading" class="hidden absolute inset-0 bg-white/60 backdrop-blur-md z-30 flex items-center justify-center rounded-[2.4rem] transition-opacity duration-300">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                            <p class="text-sm font-bold text-primary animate-pulse">กำลังดึงข้อมูลระบบ...</p>
                        </div>
                    </div>
                    <div id="calendar" class="min-h-[600px]"></div>
                </div>
            </div>

            <!-- Interactive Guide Section -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="glass-card p-6 border-white/40 group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="mouse-pointer-2" class="w-6 h-6 text-primary"></i>
                    </div>
                    <h4 class="font-bold text-text-main mb-2">ดูรายละเอียด</h4>
                    <p class="text-xs text-text-main/60 leading-relaxed font-medium">คลิกที่รายการบนปฏิทินเพื่อเรียกดูข้อมูลผู้ลา และสาเหตุการลาอย่างง่ายดาย</p>
                </div>
                <div class="glass-card p-6 border-white/40 group">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="layers" class="w-6 h-6 text-cta"></i>
                    </div>
                    <h4 class="font-bold text-text-main mb-2">สลับมุมมอง</h4>
                    <p class="text-xs text-text-main/60 leading-relaxed font-medium">เปลี่ยนการแสดงผลระหว่าง เดือน, สัปดาห์ หรือ รายการ เพื่อความชัดเจนในการวิเคราะห์</p>
                </div>
                <div class="glass-card p-6 border-white/40 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="filter" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <h4 class="font-bold text-text-main mb-2">กรองตามทีม</h4>
                    <p class="text-xs text-text-main/60 leading-relaxed font-medium">เลือกแผนกเฉพาะเพื่อตรวจสอบความพร้อมในการปฏิบัติงานของคนในสังกัด</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liquid Modal -->
    <div id="eventModal" class="fixed inset-0 z-[100] hidden overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="min-h-screen px-4 flex items-center justify-center">
            <div class="fixed inset-0 bg-text-main/40 backdrop-blur-sm transition-opacity" onclick="closeEventModal()"></div>

            <div class="relative w-full max-w-lg mx-auto transform transition-all animate-modal-in">
                <div class="modal-glass rounded-[3rem] overflow-hidden border-2 border-white/60">
                    <!-- Modal Header with Dynamic Color -->
                    <div id="modalHeader" class="relative px-8 pt-10 pb-12 transition-colors duration-500">
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_top_right,white,transparent)]"></div>
                        <button onclick="closeEventModal()"
                            class="absolute top-6 right-6 w-10 h-10 flex items-center justify-center bg-white/20 hover:bg-white/40 rounded-full transition-all text-white backdrop-blur-md cursor-pointer">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                        
                        <div id="modalHeaderIcon" class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-md rounded-[2rem] flex items-center justify-center mb-6 shadow-2xl border border-white/30">
                            <i data-lucide="clipboard-list" class="w-10 h-10 text-white"></i>
                        </div>
                        
                        <h3 id="modalTitle" class="text-2xl font-bold text-white text-center font-display">รายละเอียด</h3>
                        <div class="flex justify-center mt-3">
                            <span id="modalSubtitle" class="px-4 py-1 bg-white/20 backdrop-blur rounded-full text-white text-xs font-bold tracking-widest uppercase"></span>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div id="modalContent" class="p-8">
                        <!-- Content injected via JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Lucide Icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                const calendarEl = document.getElementById('calendar');
                const departmentFilter = document.getElementById('departmentFilter');
                const showGuardChangeCheckbox = document.getElementById('showGuardChange');
                const loadingEl = document.getElementById('calendarLoading');

                function getEventsUrl(start, end) {
                    const params = new URLSearchParams({
                        start: start,
                        end: end,
                        department: departmentFilter ? departmentFilter.value : 'all',
                        show_guard_change: showGuardChangeCheckbox ? (showGuardChangeCheckbox.checked ? '1' : '0') : '1'
                    });
                    return `{{ route('calendar.events') }}?${params}`;
                }

                function fetchEvents(info, successCallback, failureCallback) {
                    loadingEl.classList.remove('hidden');
                    const url = getEventsUrl(info.startStr, info.endStr);

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            loadingEl.classList.add('hidden');
                            successCallback(data);
                        })
                        .catch(error => {
                            loadingEl.classList.add('hidden');
                            console.error('Error fetching events:', error);
                            failureCallback(error);
                        });
                }

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'th',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,dayGridWeek,listMonth'
                    },
                    buttonText: {
                        today: 'วันนี้',
                        month: 'เดือน',
                        week: 'สัปดาห์',
                        list: 'รายการ'
                    },
                    height: 'auto',
                    dayMaxEvents: 4,
                    moreLinkClick: 'popover',
                    eventDisplay: 'block',
                    lazyFetching: false,
                    events: fetchEvents,
                    eventClick: function (info) {
                        showEventModal(info.event);
                    },
                    datesSet: function (info) {
                        updateSummary(info.view.currentStart, info.view.currentEnd);
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    },
                    eventDidMount: function (info) {
                        // Custom styling for specific types
                        if (info.event.extendedProps.type === 'guard_change') {
                            info.el.style.backgroundColor = '#6366f1'; // Indigo-500
                        }
                    }
                });

                calendar.render();

                // Filters
                if (departmentFilter) {
                    departmentFilter.addEventListener('change', () => {
                        calendar.refetchEvents();
                        updateSummary(calendar.view.currentStart, calendar.view.currentEnd);
                    });
                }

                if (showGuardChangeCheckbox) {
                    showGuardChangeCheckbox.addEventListener('change', () => calendar.refetchEvents());
                }

                function updateSummary(start, end) {
                    if (!start || !end) return;
                    const params = new URLSearchParams({
                        start: start.toISOString().split('T')[0],
                        end: end.toISOString().split('T')[0],
                        department: departmentFilter ? departmentFilter.value : 'all'
                    });

                    fetch(`{{ route('calendar.summary') }}?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            const mapping = {
                                'headerOnLeaveToday': data.onLeaveToday || 0,
                                'headerMonthlyTotal': data.totalRequests || 0,
                                'onLeaveTodayCount': data.onLeaveToday || 0,
                                'monthlyRequestsCount': data.totalRequests || 0,
                                'vacationCount': data.byType['ลาพักร้อน'] || data.byType['Vacation'] || 0,
                                'sickCount': data.byType['ลาป่วย'] || data.byType['Sick Leave'] || 0
                            };
                            Object.entries(mapping).forEach(([id, val]) => {
                                const el = document.getElementById(id);
                                if (el) el.textContent = val;
                            });
                        });
                }
            });

            function showEventModal(event) {
                const modal = document.getElementById('eventModal');
                const modalHeader = document.getElementById('modalHeader');
                const modalTitle = document.getElementById('modalTitle');
                const modalSubtitle = document.getElementById('modalSubtitle');
                const content = document.getElementById('modalContent');
                const props = event.extendedProps;

                const themes = {
                    'vacation': 'bg-gradient-to-br from-emerald-500 to-teal-600',
                    'sick': 'bg-gradient-to-br from-rose-500 to-pink-600',
                    'personal': 'bg-gradient-to-br from-amber-500 to-orange-600',
                    'guard_change': 'bg-gradient-to-br from-indigo-500 to-blue-600',
                    'default': 'bg-gradient-to-br from-primary to-purple-600'
                };

                let html = '';

                if (props.type === 'leave') {
                    const themeClass = themes[props.leaveTypeSlug] || themes['default'];
                    modalHeader.className = `relative px-8 pt-10 pb-12 ${themeClass}`;
                    modalTitle.textContent = props.leaveType;
                    modalSubtitle.textContent = `${props.totalDays} วัน`;

                    html = `
                        <div class="space-y-6">
                            <div class="flex items-center gap-5 p-5 bg-purple-50 rounded-[2rem] border-2 border-purple-100/50">
                                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-primary font-bold text-xl shadow-lg font-display border-2 border-primary/5">
                                    ${props.userName.charAt(0)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-lg text-text-main">${props.userName}</p>
                                    <p class="text-xs font-bold text-primary uppercase tracking-widest opacity-70">${props.department || 'ไม่ระบุแผนก'}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border-2 border-slate-100 shadow-sm relative overflow-hidden group">
                                    <div class="absolute right-0 top-0 w-24 h-24 bg-primary/5 rounded-full -mr-12 -mt-12 transition-transform group-hover:scale-150"></div>
                                    <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="calendar" class="w-6 h-6 text-primary"></i>
                                    </div>
                                    <div class="relative">
                                        <p class="text-[10px] font-bold text-text-main/40 uppercase tracking-[0.2em] mb-1">ระยะเวลา</p>
                                        <p class="font-bold text-text-main">${props.startDate} ถึง ${props.endDate}</p>
                                    </div>
                                </div>

                                ${props.reason ? `
                                <div class="p-6 bg-slate-50/50 rounded-3xl border-2 border-dashed border-slate-200">
                                    <p class="text-[10px] font-bold text-text-main/40 uppercase tracking-[0.2em] mb-3">หมายเหตุ / เหตุผล</p>
                                    <p class="text-sm text-text-main/80 leading-relaxed font-medium">${props.reason}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                } else if (props.type === 'guard_change') {
                    modalHeader.className = `relative px-8 pt-10 pb-12 ${themes['guard_change']}`;
                    modalTitle.textContent = 'เปลี่ยนเวรยาม';
                    modalSubtitle.textContent = 'ยืนยันการปฏิบัติแทน';

                    html = `
                        <div class="space-y-6">
                            <div class="flex items-center gap-5 p-5 bg-indigo-50 rounded-[2rem] border-2 border-indigo-100/50">
                                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-indigo-600 font-bold text-xl shadow-lg font-display border-2 border-indigo-500/5">
                                    ${props.userName.charAt(0)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-lg text-text-main">${props.userName}</p>
                                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest opacity-70">${props.department || 'ไม่ระบุแผนก'}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-5 bg-rose-50 rounded-3xl border-2 border-rose-100 text-center relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-2 opacity-10"><i data-lucide="calendar-x" class="w-8 h-8"></i></div>
                                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1">วันเดิม</p>
                                    <p class="font-extrabold text-text-main">${props.originalDate}</p>
                                </div>
                                <div class="p-5 bg-emerald-50 rounded-3xl border-2 border-emerald-100 text-center relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-2 opacity-10"><i data-lucide="calendar-check" class="w-8 h-8"></i></div>
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">วันใหม่</p>
                                    <p class="font-extrabold text-text-main">${props.newDate}</p>
                                </div>
                            </div>

                            ${props.substituteUser ? `
                            <div class="flex items-center gap-4 p-5 bg-white rounded-3xl border-2 border-slate-100 group">
                                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:bg-amber-200 transition-colors">
                                    <i data-lucide="user-check" class="w-6 h-6 text-amber-600"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-text-main/40 uppercase tracking-[0.2em] mb-1">ผู้เข้าเวรแทน</p>
                                    <p class="font-extrabold text-text-main">${props.substituteUser}</p>
                                </div>
                            </div>
                            ` : ''}

                            ${props.reason ? `
                             <div class="p-6 bg-slate-50/50 rounded-3xl border-2 border-dashed border-slate-200">
                                <p class="text-[10px] font-bold text-text-main/40 uppercase tracking-[0.2em] mb-3">สาเหตุการเปลี่ยน</p>
                                <p class="text-sm text-text-main/80 leading-relaxed font-medium">${props.reason}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                }

                content.innerHTML = html;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            function closeEventModal() {
                document.getElementById('eventModal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeEventModal();
            });
        </script>
    @endpush
</x-app-layout>
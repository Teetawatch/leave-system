<x-app-layout>
    @section('title', 'ปฏิทินการลา')

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
        <style>
            /* Glassmorphism Panel */
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

            /* FullCalendar Customization */
            .fc {
                font-family: inherit;
            }
            .fc .fc-toolbar-title {
                font-size: 1.5rem !important;
                font-weight: 700 !important;
                color: #1e293b; /* slate-800 */
                letter-spacing: -0.025em;
            }
            .fc .fc-button-primary {
                background-color: #6366f1 !important; /* indigo-500 */
                border-color: #6366f1 !important;
                border-radius: 0.75rem !important;
                padding: 0.5rem 1.25rem !important;
                font-weight: 600 !important;
                font-size: 0.875rem !important;
                text-transform: capitalize !important;
                transition: all 0.2s ease !important;
            }
            .fc .fc-button-primary:hover {
                background-color: #4f46e5 !important; /* indigo-600 */
                border-color: #4f46e5 !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            }
            .fc .fc-button-primary:not(:disabled).fc-button-active,
            .fc .fc-button-primary:not(:disabled):active {
                background-color: #4338ca !important; /* indigo-700 */
                border-color: #4338ca !important;
            }
            .fc-theme-standard td, .fc-theme-standard th {
                border-color: #f1f5f9 !important; /* slate-100 */
            }
            .fc .fc-col-header-cell {
                background-color: #f8fafc; /* slate-50 */
                padding: 1rem 0 !important;
            }
            .fc .fc-col-header-cell-cushion {
                font-weight: 700 !important;
                color: #64748b !important; /* slate-500 */
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .fc .fc-daygrid-day-number {
                font-weight: 600 !important;
                color: #475569 !important; /* slate-600 */
                padding: 0.5rem !important;
            }
            .fc .fc-day-today {
                background-color: #f8fafc !important; /* slate-50 */
            }
            .fc .fc-day-today .fc-daygrid-day-number {
                background-color: #6366f1 !important; /* indigo-500 */
                color: white !important;
                border-radius: 0.5rem;
                padding: 0.25rem 0.5rem !important;
                margin: 0.25rem !important;
                display: inline-block;
            }
            .fc .fc-event {
                border-radius: 0.5rem !important;
                padding: 0.25rem 0.5rem !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                border: none !important;
                margin: 1px 2px !important;
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease !important;
            }
            .fc .fc-event:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
                z-index: 10 !important;
                filter: brightness(0.95);
            }

            /* Animations */
            @keyframes slideInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-slide-in-up {
                animation: slideInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            
            /* Custom Scrollbar for Modal */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(0,0,0,0.05);
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(0,0,0,0.1);
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(0,0,0,0.2);
            }
        </style>
    @endpush

    <div class="min-h-screen pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-slide-in-up">
            
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

                <!-- Quick Header Stats -->
                <div class="flex items-center gap-3">
                    <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3">
                        <span class="flex h-2.5 w-2.5 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none">ลาวันนี้</span>
                            <span class="text-base font-bold text-slate-800 leading-none mt-1" id="headerOnLeaveToday">-</span>
                        </div>
                    </div>
                    <div class="glass-panel px-4 py-2 rounded-xl flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                        <div class="flex flex-col">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none">เดือนนี้</span>
                            <span class="text-base font-bold text-slate-800 leading-none mt-1" id="headerMonthlyTotal">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                
                <!-- On Leave Today -->
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
                        <span class="text-4xl font-extrabold text-slate-800 tabular-nums" id="onLeaveTodayCount">-</span>
                        <span class="text-sm font-medium text-slate-400">คน</span>
                    </div>
                </div>

                <!-- Monthly Requests -->
                <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.05] transition-opacity duration-300">
                        <i data-lucide="file-spreadsheet" class="w-28 h-28 text-slate-900"></i>
                    </div>
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-12 h-12 rounded-[1rem] bg-indigo-50 flex items-center justify-center border border-indigo-100">
                            <i data-lucide="calendar-check" class="w-6 h-6 text-indigo-500"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">คำขอลาเดือนนี้</p>
                    <div class="flex items-baseline gap-2 relative z-10">
                        <span class="text-4xl font-extrabold text-slate-800 tabular-nums" id="monthlyRequestsCount">-</span>
                        <span class="text-sm font-medium text-slate-400">รายการ</span>
                    </div>
                </div>

                <!-- Vacation Leave -->
                <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-12 h-12 rounded-[1rem] bg-emerald-50 flex items-center justify-center border border-emerald-100">
                            <i data-lucide="palm-tree" class="w-6 h-6 text-emerald-500"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">ลาพักร้อน</p>
                    <div class="flex items-baseline gap-2 relative z-10">
                        <span class="text-4xl font-extrabold text-slate-800 tabular-nums" id="vacationCount">-</span>
                        <span class="text-sm font-medium text-slate-400">รายการ</span>
                    </div>
                </div>

                <!-- Sick Leave -->
                <div class="glass-panel p-5 rounded-3xl relative overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center justify-between mb-4 relative z-10">
                        <div class="w-12 h-12 rounded-[1rem] bg-amber-50 flex items-center justify-center border border-amber-100">
                            <i data-lucide="thermometer-sun" class="w-6 h-6 text-amber-500"></i>
                        </div>
                    </div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">ลาป่วย</p>
                    <div class="flex items-baseline gap-2 relative z-10">
                        <span class="text-4xl font-extrabold text-slate-800 tabular-nums" id="sickCount">-</span>
                        <span class="text-sm font-medium text-slate-400">รายการ</span>
                    </div>
                </div>
            </div>

            <!-- Filters & Legend Area -->
            <div class="glass-panel p-4 rounded-[2rem] mb-8 relative z-20">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                    
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 w-full lg:w-auto">
                        <div class="relative group w-full sm:w-auto">
                            <select id="departmentFilter"
                                class="w-full sm:w-auto appearance-none bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 pr-10 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer min-w-[220px] hover:bg-white hover:border-slate-300 shadow-sm">
                                <option value="all">ทุกแผนก / ทั้งหน่วย</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none group-hover:text-slate-600 transition-colors"></i>
                        </div>

                        <label class="w-full sm:w-auto flex items-center justify-between sm:justify-start gap-3 cursor-pointer select-none bg-white hover:bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 transition-colors shadow-sm">
                            <span class="text-sm font-bold text-slate-700">แสดงวันเปลี่ยนเวร</span>
                            <div class="relative">
                                <input type="checkbox" id="showGuardChange" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                            </div>
                        </label>
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap items-center justify-center gap-4 px-2">
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
                            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm"></span>
                            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-widest">เปลี่ยนเวร</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Calendar Container -->
            <div class="glass-panel rounded-[2rem] overflow-hidden p-2 sm:p-4 md:p-6 mb-8 relative z-10">
                <!-- Loading Overlay -->
                <div id="calendarLoading" class="hidden absolute inset-0 bg-white/70 backdrop-blur-sm z-30 flex items-center justify-center rounded-[2rem] transition-opacity duration-300">
                    <div class="flex flex-col items-center gap-4 bg-white p-6 rounded-3xl shadow-xl border border-slate-100">
                        <div class="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-indigo-900">กำลังอัปเดตข้อมูล...</p>
                    </div>
                </div>
                
                <div id="calendar" class="bg-white rounded-[1.5rem] p-4 sm:p-6 shadow-sm border border-slate-100"></div>
            </div>

            <!-- Helpful Tips -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-panel p-5 rounded-3xl flex items-start gap-4 hover:bg-white/90 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0 text-slate-500 border border-slate-100">
                        <i data-lucide="pointer" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">ดูรายละเอียดรวดเร็ว</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">คลิกที่แถบสีในปฏิทินเพื่อดูข้อมูลผู้ลา แผนก และเหตุผลอย่างละเอียด</p>
                    </div>
                </div>
                <div class="glass-panel p-5 rounded-3xl flex items-start gap-4 hover:bg-white/90 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0 text-slate-500 border border-slate-100">
                        <i data-lucide="calendar-range" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">ปรับเปลี่ยนมุมมอง</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">เลือกแสดงผลแบบ เดือน, สัปดาห์ หรือ รายการ เพื่อการตรวจสอบที่แม่นยำขึ้น</p>
                    </div>
                </div>
                <div class="glass-panel p-5 rounded-3xl flex items-start gap-4 hover:bg-white/90 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0 text-slate-500 border border-slate-100">
                        <i data-lucide="filter" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">คัดกรองข้อมูล</h4>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">เลือกดูเฉพาะแผนกของคุณ หรือซ่อนรายการเปลี่ยนเวรเพื่อลดความซ้ำซ้อน</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventModal" class="fixed inset-0 z-[100] hidden overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="min-h-screen px-4 flex items-center justify-center py-10">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeEventModal()" aria-hidden="true"></div>

            <!-- Modal Panel -->
            <div class="relative w-full max-w-lg mx-auto transform transition-all animate-slide-in-up">
                <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-slate-100">
                    
                    <!-- Dynamic Header -->
                    <div id="modalHeader" class="relative px-6 pt-8 pb-10 transition-colors duration-300">
                        <!-- BG pattern overlay -->
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                        
                        <button onclick="closeEventModal()" class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center bg-white/20 hover:bg-white/30 rounded-full transition-colors text-white cursor-pointer z-10 backdrop-blur-md">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        
                        <div class="relative z-10 flex flex-col items-center">
                            <div id="modalIconContainer" class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-[1.25rem] border border-white/30 flex items-center justify-center mb-4 shadow-lg">
                                <i data-lucide="clipboard-list" class="w-8 h-8 text-white"></i>
                            </div>
                            <h3 id="modalTitle" class="text-xl md:text-2xl font-extrabold text-white text-center">รายละเอียดการลา</h3>
                            <div class="mt-2">
                                <span id="modalSubtitle" class="inline-flex items-center px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold uppercase tracking-wider border border-white/20"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div id="modalContent" class="p-6 md:p-8 bg-white custom-scrollbar overflow-y-auto max-h-[60vh]">
                        <!-- Injected by JavaScript -->
                    </div>
                    
                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                        <button onclick="closeEventModal()" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200">
                            ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Lucide Icons globally for the page
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
                    if (loadingEl) loadingEl.classList.remove('hidden');

                    const url = getEventsUrl(info.startStr, info.endStr);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if (loadingEl) loadingEl.classList.add('hidden');
                            successCallback(data);
                        })
                        .catch(error => {
                            if (loadingEl) loadingEl.classList.add('hidden');
                            console.error('Error fetching calendar events:', error);
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
                    dayMaxEvents: 3,
                    eventDisplay: 'block',
                    lazyFetching: false,
                    events: fetchEvents,
                    eventClick: function (info) {
                        showEventModal(info.event);
                    },
                    datesSet: function (info) {
                        updateSummary(info.view.currentStart, info.view.currentEnd);
                        // Re-initialize icons just in case toolbar buttons were re-rendered
                        setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                    },
                    eventDidMount: function(info) {
                        // Tailwind colors mapping for events
                        const type = info.event.extendedProps.type;
                        const leaveSlug = info.event.extendedProps.leaveTypeSlug;
                        
                        let bgColor = '#6366f1'; // default indigo-500
                        
                        if (type === 'guard_change') {
                            bgColor = '#6366f1'; // indigo-500
                        } else if (type === 'leave') {
                            if (leaveSlug === 'vacation') bgColor = '#10b981'; // emerald-500
                            else if (leaveSlug === 'sick') bgColor = '#f43f5e'; // rose-500
                            else if (leaveSlug === 'personal') bgColor = '#f59e0b'; // amber-500
                        }
                        
                        info.el.style.backgroundColor = bgColor;
                        info.el.title = info.event.title; // Add tooltip natively
                    }
                });

                calendar.render();

                // Listeners for filters
                if (departmentFilter) {
                    departmentFilter.addEventListener('change', () => {
                        calendar.refetchEvents();
                        updateSummary(calendar.view.currentStart, calendar.view.currentEnd);
                    });
                }

                if (showGuardChangeCheckbox) {
                    showGuardChangeCheckbox.addEventListener('change', () => {
                        calendar.refetchEvents();
                    });
                }

                function updateSummary(start, end) {
                    if (!start || !end) return;
                    
                    const params = new URLSearchParams({
                        start: start.toISOString().split('T')[0],
                        end: end.toISOString().split('T')[0],
                        department: departmentFilter ? departmentFilter.value : 'all'
                    });

                    fetch(`{{ route('calendar.summary') }}?${params}`)
                        .then(res => res.json())
                        .then(data => {
                            // Update dom mapping
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
                        })
                        .catch(err => console.error('Error fetching summary:', err));
                }
            });

            function showEventModal(event) {
                const modal = document.getElementById('eventModal');
                const modalHeader = document.getElementById('modalHeader');
                const modalTitle = document.getElementById('modalTitle');
                const modalSubtitle = document.getElementById('modalSubtitle');
                const content = document.getElementById('modalContent');
                const props = event.extendedProps;

                // Color mappings using Tailwind bg logic for the header gradient
                const themeClasses = {
                    'vacation': 'bg-gradient-to-br from-emerald-500 to-teal-600',
                    'sick': 'bg-gradient-to-br from-rose-500 to-pink-600',
                    'personal': 'bg-gradient-to-br from-amber-500 to-orange-500',
                    'guard_change': 'bg-gradient-to-br from-indigo-500 to-blue-600',
                    'default': 'bg-gradient-to-br from-slate-700 to-slate-900'
                };

                // Clear previous content
                content.innerHTML = '';

                if (props.type === 'leave') {
                    const theme = themeClasses[props.leaveTypeSlug] || themeClasses['default'];
                    modalHeader.className = `relative px-6 pt-8 pb-10 transition-colors duration-300 ${theme}`;
                    modalTitle.textContent = props.leaveType;
                    modalSubtitle.textContent = `จำนวน ${props.totalDays} วัน`;

                    content.innerHTML = `
                        <div class="space-y-5">
                            <!-- User Card -->
                            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="w-12 h-12 bg-white rounded-[10px] flex items-center justify-center text-slate-800 font-extrabold text-lg shadow-sm border border-slate-200">
                                    ${props.userName.charAt(0)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 text-lg truncate">${props.userName}</p>
                                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest truncate mt-0.5">${props.department || 'ไม่ระบุแผนก'}</p>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="space-y-3">
                                <div class="flex items-start gap-4 p-4 rounded-2xl border border-slate-100 bg-white shadow-sm">
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex flex-shrink-0 items-center justify-center border border-slate-100 mt-1">
                                        <i data-lucide="calendar" class="w-5 h-5 text-slate-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">ระยะเวลาการลา</p>
                                        <p class="text-sm font-bold text-slate-800">${props.startDate}</p>
                                        <p class="text-xs font-semibold text-slate-500 mt-0.5">ถึง ${props.endDate}</p>
                                    </div>
                                </div>

                                ${props.reason ? `
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                        <i data-lucide="align-left" class="w-3 h-3"></i> เหตุผล/หมายเหตุ
                                    </p>
                                    <p class="text-sm text-slate-700 font-medium leading-relaxed">${props.reason}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                } else if (props.type === 'guard_change') {
                    modalHeader.className = `relative px-6 pt-8 pb-10 transition-colors duration-300 ${themeClasses['guard_change']}`;
                    modalTitle.textContent = 'ขอเปลี่ยนเวรยาม';
                    modalSubtitle.textContent = 'รายการปฏิบัติหน้าที่แทน';

                    content.innerHTML = `
                        <div class="space-y-5">
                            <!-- Requester -->
                            <div class="flex items-center gap-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
                                <div class="w-12 h-12 bg-white rounded-[10px] flex items-center justify-center text-indigo-600 font-extrabold text-lg shadow-sm border border-indigo-100">
                                    ${props.userName.charAt(0)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 text-lg truncate">${props.userName}</p>
                                    <p class="text-[11px] font-bold text-indigo-500 uppercase tracking-widest truncate mt-0.5">${props.department || 'ผู้ขอเปลี่ยนเวร'}</p>
                                </div>
                            </div>

                            <!-- Date Compare -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-center relative">
                                    <div class="absolute top-2 right-2 opacity-20"><i data-lucide="calendar-off" class="w-6 h-6 text-rose-600"></i></div>
                                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1.5">เวรเดิม</p>
                                    <p class="text-sm font-extrabold text-slate-800">${props.originalDate}</p>
                                </div>
                                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-center relative">
                                    <div class="absolute top-2 right-2 opacity-20"><i data-lucide="calendar-check" class="w-6 h-6 text-emerald-600"></i></div>
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1.5">วันที่เปลี่ยน</p>
                                    <p class="text-sm font-extrabold text-slate-800">${props.newDate}</p>
                                </div>
                            </div>

                            ${props.substituteUser ? `
                            <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center border border-slate-200 shadow-sm flex-shrink-0">
                                    <i data-lucide="user-check" class="w-5 h-5 text-indigo-500"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">ผู้เข้าเวรแทน (รับมอบ)</p>
                                    <p class="text-sm font-bold text-slate-800">${props.substituteUser}</p>
                                </div>
                            </div>
                            ` : ''}

                            ${props.reason ? `
                             <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <i data-lucide="message-square" class="w-3 h-3"></i> เหตุผล
                                </p>
                                <p class="text-sm text-slate-700 font-medium leading-relaxed">${props.reason}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                }

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Initialize icons inside the dynamically injected modal content
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            function closeEventModal() {
                document.getElementById('eventModal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            // Close modal on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === "Escape") closeEventModal();
            });
        </script>
    @endpush
</x-app-layout>
<x-app-layout>
    @section('title', 'ปฏิทินการลา')

    <div class="min-h-screen bg-slate-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">

            <!-- Clean Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 tracking-tight">ปฏิทินการลา</h1>
                            <p class="text-slate-500 text-sm mt-0.5">ดูภาพรวมการลาของทีมและแผนก</p>
                        </div>
                    </div>

                    <!-- Quick Stats Pills -->
                    <div class="flex items-center gap-2">
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border border-slate-200">
                            <span class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></span>
                            <span class="text-sm font-semibold text-slate-700" id="headerOnLeaveToday">-</span>
                            <span class="text-xs text-slate-500">ลาวันนี้</span>
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border border-slate-200">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span class="text-sm font-semibold text-slate-700" id="headerMonthlyTotal">-</span>
                            <span class="text-xs text-slate-500">เดือนนี้</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- On Leave Today -->
                <div
                    class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">ลาวันนี้</p>
                            <p class="text-3xl font-bold text-slate-900" id="onLeaveTodayCount">-</p>
                        </div>
                        <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">คน</p>
                </div>

                <!-- This Month Total -->
                <div
                    class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">คำขอเดือนนี้</p>
                            <p class="text-3xl font-bold text-slate-900" id="monthlyRequestsCount">-</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">รายการ</p>
                </div>

                <!-- Vacation Leave -->
                <div
                    class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">ลาพักร้อน</p>
                            <p class="text-3xl font-bold text-slate-900" id="vacationCount">-</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">รายการ</p>
                </div>

                <!-- Sick Leave -->
                <div
                    class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">ลาป่วย</p>
                            <p class="text-3xl font-bold text-slate-900" id="sickCount">-</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-3">รายการ</p>
                </div>
            </div>

            <!-- Filters & Legend -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 md:p-5 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Department Filter -->
                        <div class="relative">
                            <select id="departmentFilter"
                                class="appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 pr-10 text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent cursor-pointer min-w-[160px] transition-all hover:bg-slate-100">
                                <option value="all">ทุกแผนก</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                            <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <!-- Guard Change Toggle -->
                        <label
                            class="inline-flex items-center gap-3 cursor-pointer select-none bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 transition-colors">
                            <div class="relative">
                                <input type="checkbox" id="showGuardChange" checked class="sr-only peer">
                                <div
                                    class="w-10 h-6 bg-slate-300 rounded-full peer-checked:bg-slate-900 transition-colors">
                                </div>
                                <div
                                    class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-4">
                                </div>
                            </div>
                            <span class="text-sm font-medium text-slate-600">แสดงเปลี่ยนเวร</span>
                        </label>
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap items-center gap-4">
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wide">สัญลักษณ์:</span>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                <span class="text-sm text-slate-600">พักร้อน</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                <span class="text-sm text-slate-600">ลาป่วย</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                <span class="text-sm text-slate-600">ลากิจ</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-slate-400"></span>
                                <span class="text-sm text-slate-600">เปลี่ยนเวร</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden relative">
                <!-- Calendar Loading -->
                <div id="calendarLoading"
                    class="hidden absolute inset-0 bg-white/90 backdrop-blur-sm z-20 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-8 h-8 border-3 border-slate-200 border-t-slate-900 rounded-full animate-spin">
                        </div>
                        <p class="text-sm text-slate-500 font-medium">กำลังโหลด...</p>
                    </div>
                </div>
                <div id="calendar" class="p-4 md:p-6"></div>
            </div>

            <!-- Help Section -->
            <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 mb-2">วิธีใช้งาน</h4>
                        <ul class="text-sm text-slate-600 space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full mt-2 flex-shrink-0"></span>
                                <span>คลิกที่รายการบนปฏิทินเพื่อดูรายละเอียดการลา</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full mt-2 flex-shrink-0"></span>
                                <span>เลือกมุมมอง เดือน/สัปดาห์/รายการ ที่มุมขวาบนของปฏิทิน</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full mt-2 flex-shrink-0"></span>
                                <span>กรองตามแผนกเพื่อดูเฉพาะทีมของคุณ</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="min-h-screen px-4 flex items-center justify-center">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeEventModal()"
                aria-hidden="true"></div>

            <!-- Modal Panel -->
            <div class="relative w-full max-w-md mx-auto transform transition-all animate-modal-in">
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                    <!-- Modal Header -->
                    <div id="modalHeader" class="relative px-6 pt-6 pb-8 bg-slate-900">
                        <button onclick="closeEventModal()"
                            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-full transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div id="modalHeaderIcon"
                            class="w-14 h-14 mx-auto bg-white/10 backdrop-blur rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 id="modalTitle" class="text-xl font-bold text-white text-center">รายละเอียดการลา</h3>
                        <p id="modalSubtitle" class="text-white/70 text-sm text-center mt-1"></p>
                    </div>

                    <!-- Modal Body -->
                    <div id="modalContent" class="p-6">
                        <!-- Content will be injected by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
        <style>
            /* Modal Animation */
            @keyframes modal-in {
                from {
                    opacity: 0;
                    transform: scale(0.96) translateY(8px);
                }

                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            .animate-modal-in {
                animation: modal-in 0.25s ease-out;
            }

            /* Minimal Calendar Styles */
            .fc {
                font-family: inherit;
            }

            .fc .fc-toolbar {
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }

            .fc .fc-toolbar-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: rgb(15 23 42);
                letter-spacing: -0.025em;
            }

            @media (max-width: 640px) {
                .fc .fc-toolbar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .fc .fc-toolbar-title {
                    font-size: 1.125rem;
                    text-align: center;
                    order: -1;
                    margin-bottom: 0.5rem;
                }

                .fc .fc-toolbar-chunk {
                    display: flex;
                    justify-content: center;
                }
            }

            .fc .fc-button {
                background: rgb(15 23 42);
                border: none;
                border-radius: 0.625rem;
                padding: 0.5rem 1rem;
                font-weight: 600;
                font-size: 0.8125rem;
                text-transform: none;
                box-shadow: none;
                transition: all 0.15s ease;
            }

            .fc .fc-button:hover:not(:disabled) {
                background: rgb(30 41 59);
            }

            .fc .fc-button:focus {
                box-shadow: 0 0 0 2px white, 0 0 0 4px rgb(15 23 42);
            }

            .fc .fc-button:disabled {
                opacity: 0.4;
                cursor: not-allowed;
            }

            .fc .fc-button-primary:not(:disabled).fc-button-active,
            .fc .fc-button-primary:not(:disabled):active {
                background: rgb(51 65 85);
            }

            .fc .fc-button-group {
                gap: 0.25rem;
            }

            .fc .fc-button-group .fc-button {
                border-radius: 0.5rem;
            }

            /* Today Highlight */
            .fc .fc-day-today {
                background: rgb(248 250 252) !important;
            }

            .fc .fc-day-today .fc-daygrid-day-number {
                background: rgb(15 23 42);
                color: white;
                border-radius: 0.5rem;
                padding: 0.25rem 0.625rem;
                font-weight: 700;
            }

            .fc .fc-daygrid-day-number {
                font-weight: 600;
                color: rgb(71 85 105);
                padding: 0.5rem;
                font-size: 0.875rem;
            }

            .fc .fc-col-header-cell-cushion {
                font-weight: 600;
                color: rgb(100 116 139);
                padding: 0.875rem 0.5rem;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            /* Events */
            .fc .fc-event {
                border-radius: 0.375rem;
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .fc .fc-event:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px -2px rgb(0 0 0 / 0.15);
                z-index: 10;
            }

            .fc .fc-daygrid-event-dot {
                display: none;
            }

            /* Borders */
            .fc-theme-standard td,
            .fc-theme-standard th {
                border-color: rgb(241 245 249);
            }

            .fc .fc-scrollgrid {
                border-radius: 1rem;
                overflow: hidden;
                border-color: rgb(241 245 249);
            }

            /* More Link */
            .fc .fc-more-link {
                color: rgb(51 65 85);
                font-weight: 600;
                font-size: 0.75rem;
                background: rgb(241 245 249);
                padding: 0.125rem 0.5rem;
                border-radius: 0.375rem;
                transition: all 0.15s;
            }

            .fc .fc-more-link:hover {
                background: rgb(226 232 240);
                color: rgb(15 23 42);
            }

            /* Popover */
            .fc .fc-popover {
                border-radius: 1rem;
                box-shadow: 0 20px 40px -12px rgb(0 0 0 / 0.2);
                border: 1px solid rgb(241 245 249);
                overflow: hidden;
            }

            .fc .fc-popover-header {
                background: rgb(15 23 42);
                color: white;
                padding: 0.75rem 1rem;
                font-weight: 700;
                font-size: 0.875rem;
            }

            .fc .fc-popover-body {
                padding: 0.5rem;
            }

            /* List View */
            .fc .fc-list {
                border-radius: 1rem;
                overflow: hidden;
            }

            .fc .fc-list-day-cushion {
                background: rgb(248 250 252);
                padding: 0.75rem 1rem;
            }

            .fc .fc-list-event:hover td {
                background: rgb(248 250 252);
            }

            /* Scrollbar */
            .fc ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .fc ::-webkit-scrollbar-track {
                background: rgb(248 250 252);
                border-radius: 3px;
            }

            .fc ::-webkit-scrollbar-thumb {
                background: rgb(203 213 225);
                border-radius: 3px;
            }

            .fc ::-webkit-scrollbar-thumb:hover {
                background: rgb(148 163 184);
            }

            /* Loading Spinner */
            .border-3 {
                border-width: 3px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const calendarEl = document.getElementById('calendar');
                const departmentFilter = document.getElementById('departmentFilter');
                const showGuardChangeCheckbox = document.getElementById('showGuardChange');
                const loadingEl = document.getElementById('calendarLoading');

                // Function to build events URL
                function getEventsUrl(start, end) {
                    const params = new URLSearchParams({
                        start: start,
                        end: end,
                        department: departmentFilter ? departmentFilter.value : 'all',
                        show_guard_change: showGuardChangeCheckbox ? (showGuardChangeCheckbox.checked ? '1' : '0') : '1'
                    });
                    return `{{ route('calendar.events') }}?${params}`;
                }

                // Function to fetch events
                function fetchEvents(info, successCallback, failureCallback) {
                    if (loadingEl) loadingEl.classList.remove('hidden');

                    const url = getEventsUrl(info.startStr, info.endStr);

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (loadingEl) loadingEl.classList.add('hidden');
                            successCallback(data);
                        })
                        .catch(error => {
                            if (loadingEl) loadingEl.classList.add('hidden');
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
                    dayMaxEvents: 3,
                    moreLinkClick: 'popover',
                    eventDisplay: 'block',
                    lazyFetching: false,
                    events: fetchEvents,
                    eventClick: function (info) {
                        showEventModal(info.event);
                    },
                    datesSet: function (info) {
                        updateSummary(info.view.currentStart, info.view.currentEnd);
                    },
                    eventDidMount: function (info) {
                        // Add tooltip
                        info.el.title = info.event.title;
                    }
                });

                calendar.render();

                // Force refetch events after a short delay to ensure everything is loaded
                setTimeout(function () {
                    calendar.refetchEvents();
                    if (calendar.view) {
                        updateSummary(calendar.view.currentStart, calendar.view.currentEnd);
                    }
                }, 100);

                // Filter change handlers
                if (departmentFilter) {
                    departmentFilter.addEventListener('change', function () {
                        calendar.refetchEvents();
                        updateSummary(calendar.view.currentStart, calendar.view.currentEnd);
                    });
                }

                if (showGuardChangeCheckbox) {
                    showGuardChangeCheckbox.addEventListener('change', function () {
                        calendar.refetchEvents();
                    });
                }

                // Update summary statistics
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
                            // Update header stats
                            const headerOnLeave = document.getElementById('headerOnLeaveToday');
                            const headerMonthly = document.getElementById('headerMonthlyTotal');
                            const onLeaveCount = document.getElementById('onLeaveTodayCount');
                            const monthlyCount = document.getElementById('monthlyRequestsCount');
                            const vacationEl = document.getElementById('vacationCount');
                            const sickEl = document.getElementById('sickCount');

                            if (headerOnLeave) headerOnLeave.textContent = data.onLeaveToday || 0;
                            if (headerMonthly) headerMonthly.textContent = data.totalRequests || 0;
                            if (onLeaveCount) onLeaveCount.textContent = data.onLeaveToday || 0;
                            if (monthlyCount) monthlyCount.textContent = data.totalRequests || 0;
                            if (vacationEl) vacationEl.textContent = data.byType['ลาพักร้อน'] || data.byType['Vacation'] || 0;
                            if (sickEl) sickEl.textContent = data.byType['ลาป่วย'] || data.byType['Sick Leave'] || 0;
                        })
                        .catch(error => console.error('Error fetching summary:', error));
                }
            });

            function showEventModal(event) {
                const modal = document.getElementById('eventModal');
                const modalHeader = document.getElementById('modalHeader');
                const modalTitle = document.getElementById('modalTitle');
                const modalSubtitle = document.getElementById('modalSubtitle');
                const content = document.getElementById('modalContent');
                const props = event.extendedProps;

                // Set header color based on type (minimal palette)
                const headerColors = {
                    'vacation': 'bg-emerald-600',
                    'sick': 'bg-rose-600',
                    'personal': 'bg-amber-600',
                    'guard_change': 'bg-slate-600',
                    'default': 'bg-slate-900'
                };

                let html = '';

                if (props.type === 'leave') {
                    const headerColor = headerColors[props.leaveTypeSlug] || headerColors['default'];
                    modalHeader.className = `relative px-6 pt-6 pb-8 ${headerColor}`;
                    modalTitle.textContent = props.leaveType;
                    modalSubtitle.textContent = `${props.totalDays} วัน`;

                    html = `
                        <div class="space-y-4">
                            <!-- User Info -->
                            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                                <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                    ${props.userName.charAt(0)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900">${props.userName}</p>
                                    <p class="text-sm text-slate-500">${props.department || 'ไม่ระบุแผนก'}</p>
                                </div>
                            </div>

                            <!-- Date Range -->
                            <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">ช่วงเวลาลา</p>
                                    <p class="font-semibold text-slate-900">${props.startDate} - ${props.endDate}</p>
                                </div>
                            </div>

                            ${props.reason ? `
                            <!-- Reason -->
                            <div class="p-4 bg-slate-50 rounded-xl">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">เหตุผลการลา</p>
                                <p class="text-slate-700">${props.reason}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                } else if (props.type === 'guard_change') {
                    modalHeader.className = `relative px-6 pt-6 pb-8 ${headerColors['guard_change']}`;
                    modalTitle.textContent = 'เปลี่ยนเวร';
                    modalSubtitle.textContent = 'คำขอเปลี่ยนเวร';

                    html = `
                        <div class="space-y-4">
                            <!-- User Info -->
                            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                                <div class="w-12 h-12 bg-slate-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                    ${props.userName.charAt(0)}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900">${props.userName}</p>
                                    <p class="text-sm text-slate-500">${props.department || 'ไม่ระบุแผนก'}</p>
                                </div>
                            </div>

                            <!-- Date Changes -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-4 bg-rose-50 rounded-xl text-center">
                                    <p class="text-xs font-medium text-rose-600 uppercase tracking-wider mb-1">วันเดิม</p>
                                    <p class="font-bold text-rose-700">${props.originalDate}</p>
                                </div>
                                <div class="p-4 bg-emerald-50 rounded-xl text-center">
                                    <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-1">วันใหม่</p>
                                    <p class="font-bold text-emerald-700">${props.newDate}</p>
                                </div>
                            </div>

                            ${props.substituteUser ? `
                            <div class="flex items-center gap-3 p-4 bg-amber-50 rounded-xl">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-amber-600 uppercase tracking-wider">ผู้เข้าเวรแทน</p>
                                    <p class="font-semibold text-slate-900">${props.substituteUser}</p>
                                </div>
                            </div>
                            ` : ''}

                            ${props.reason ? `
                            <div class="p-4 bg-slate-50 rounded-xl">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">เหตุผล</p>
                                <p class="text-slate-700">${props.reason}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                }

                content.innerHTML = html;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeEventModal() {
                document.getElementById('eventModal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            // Close modal on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeEventModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
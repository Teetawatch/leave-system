<x-app-layout>
    @section('title', 'ปฏิทินการลา')

    <div class="py-4 md:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Hero Header -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl p-6 md:p-8 mb-6 shadow-2xl">
                <!-- Animated Background Elements -->
                <div class="absolute inset-0 overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                </div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-4 bg-white/20 backdrop-blur-xl rounded-2xl shadow-lg border border-white/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">ปฏิทินการลา</h1>
                            <p class="text-white/80 text-sm md:text-base mt-1">ดูภาพรวมการลาของทีมและแผนก</p>
                        </div>
                    </div>
                    
                    <!-- Quick Stats in Header -->
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 backdrop-blur-xl rounded-2xl px-5 py-3 border border-white/30">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-white" id="headerOnLeaveToday">-</p>
                                <p class="text-white/70 text-xs font-medium mt-0.5">ลาวันนี้</p>
                            </div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-xl rounded-2xl px-5 py-3 border border-white/30">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-white" id="headerMonthlyTotal">-</p>
                                <p class="text-white/70 text-xs font-medium mt-0.5">เดือนนี้</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards Row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6">
                <!-- On Leave Today -->
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-100 p-4 md:p-5 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-red-400 rounded-xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity"></div>
                            <div class="relative p-3 bg-gradient-to-br from-red-400 to-rose-500 rounded-xl shadow-lg">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500 truncate">ลาวันนี้</p>
                            <p class="text-xl md:text-2xl font-bold text-slate-800" id="onLeaveTodayCount">-</p>
                        </div>
                    </div>
                </div>

                <!-- This Month Total -->
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-100 p-4 md:p-5 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-400 rounded-xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity"></div>
                            <div class="relative p-3 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl shadow-lg">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500 truncate">คำขอเดือนนี้</p>
                            <p class="text-xl md:text-2xl font-bold text-slate-800" id="monthlyRequestsCount">-</p>
                        </div>
                    </div>
                </div>

                <!-- Vacation Leave -->
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-100 p-4 md:p-5 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-emerald-400 rounded-xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity"></div>
                            <div class="relative p-3 bg-gradient-to-br from-emerald-400 to-green-500 rounded-xl shadow-lg">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500 truncate">ลาพักร้อน</p>
                            <p class="text-xl md:text-2xl font-bold text-slate-800" id="vacationCount">-</p>
                        </div>
                    </div>
                </div>

                <!-- Sick Leave -->
                <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl border border-slate-100 p-4 md:p-5 transition-all duration-300 hover:-translate-y-1 cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="absolute inset-0 bg-amber-400 rounded-xl blur-lg opacity-30 group-hover:opacity-50 transition-opacity"></div>
                            <div class="relative p-3 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl shadow-lg">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-500 truncate">ลาป่วย</p>
                            <p class="text-xl md:text-2xl font-bold text-slate-800" id="sickCount">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Legend Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 md:p-5 mb-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-3 md:gap-4">
                        <!-- Department Filter -->
                        <div class="flex items-center gap-2 bg-slate-50 rounded-xl px-3 py-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <select id="departmentFilter" class="bg-transparent border-0 text-sm font-medium text-slate-700 focus:ring-0 cursor-pointer pr-8 min-w-[140px]">
                                <option value="all">ทุกแผนก</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Guard Change Toggle -->
                        <label class="flex items-center gap-2.5 cursor-pointer select-none bg-slate-50 rounded-xl px-3 py-2 hover:bg-slate-100 transition-colors">
                            <div class="relative">
                                <input type="checkbox" id="showGuardChange" checked class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-300 rounded-full peer-checked:bg-indigo-500 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-4"></div>
                            </div>
                            <span class="text-sm font-medium text-slate-600">แสดงเปลี่ยนเวร</span>
                        </label>
                    </div>

                    <!-- Legend -->
                    <div class="flex flex-wrap items-center gap-2 md:gap-4">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">สัญลักษณ์</span>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-1.5 bg-emerald-50 px-2.5 py-1.5 rounded-lg">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></div>
                                <span class="text-xs font-medium text-emerald-700">พักร้อน</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-red-50 px-2.5 py-1.5 rounded-lg">
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm shadow-red-500/50"></div>
                                <span class="text-xs font-medium text-red-700">ลาป่วย</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-amber-50 px-2.5 py-1.5 rounded-lg">
                                <div class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></div>
                                <span class="text-xs font-medium text-amber-700">ลากิจ</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-slate-100 px-2.5 py-1.5 rounded-lg">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-sm"></div>
                                <span class="text-xs font-medium text-slate-600">เปลี่ยนเวร</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <!-- Calendar Loading -->
                <div id="calendarLoading" class="hidden absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                        <p class="text-sm text-slate-500 font-medium">กำลังโหลด...</p>
                    </div>
                </div>
                <div id="calendar" class="p-3 md:p-5"></div>
            </div>
            
            <!-- Help Tips -->
            <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-4 md:p-5 border border-indigo-100/50">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-800 text-sm">วิธีใช้งาน</h4>
                        <ul class="mt-1.5 text-xs text-slate-600 space-y-1">
                            <li class="flex items-center gap-1.5">
                                <span class="w-1 h-1 bg-indigo-400 rounded-full"></span>
                                คลิกที่รายการบนปฏิทินเพื่อดูรายละเอียดการลา
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="w-1 h-1 bg-indigo-400 rounded-full"></span>
                                เลือกมุมมอง เดือน/สัปดาห์/รายการ ที่มุมขวาบน
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="w-1 h-1 bg-indigo-400 rounded-full"></span>
                                กรองตามแผนกเพื่อดูเฉพาะทีมของคุณ
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div id="eventModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end sm:items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEventModal()" aria-hidden="true"></div>
            
            <!-- Modal Panel -->
            <div class="relative z-10 w-full max-w-md mx-auto transform transition-all animate-modal-in">
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                    <!-- Modal Header with gradient -->
                    <div id="modalHeader" class="relative px-6 pt-6 pb-8 bg-gradient-to-br from-indigo-500 to-purple-600">
                        <button onclick="closeEventModal()" class="absolute top-4 right-4 p-2 bg-white/20 hover:bg-white/30 rounded-full transition-colors">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                        <div id="modalHeaderIcon" class="w-16 h-16 mx-auto bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 id="modalTitle" class="text-xl font-bold text-white text-center">รายละเอียดการลา</h3>
                        <p id="modalSubtitle" class="text-white/80 text-sm text-center mt-1"></p>
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
                transform: scale(0.95) translateY(10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .animate-modal-in {
            animation: modal-in 0.3s ease-out;
        }
        
        /* FullCalendar Premium Styles */
        .fc {
            font-family: inherit;
        }
        
        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(30 41 59);
            letter-spacing: -0.025em;
        }
        
        @media (min-width: 768px) {
            .fc .fc-toolbar-title {
                font-size: 1.25rem;
            }
        }
        
        .fc .fc-button {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.625rem 1rem;
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: none;
            box-shadow: 0 4px 6px -1px rgb(99 102 241 / 0.3), 0 2px 4px -2px rgb(99 102 241 / 0.2);
            transition: all 0.2s ease;
        }
        
        .fc .fc-button:hover:not(:disabled) {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 12px -2px rgb(99 102 241 / 0.4), 0 4px 6px -2px rgb(99 102 241 / 0.2);
        }
        
        .fc .fc-button:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .fc .fc-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        }
        
        .fc .fc-button-group {
            gap: 0.25rem;
        }
        
        .fc .fc-button-group .fc-button {
            border-radius: 0.5rem;
        }
        
        .fc .fc-day-today {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%) !important;
        }
        
        .fc .fc-day-today .fc-daygrid-day-number {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-weight: 700;
        }
        
        .fc .fc-daygrid-day-number {
            font-weight: 600;
            color: rgb(71 85 105);
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        
        .fc .fc-col-header-cell-cushion {
            font-weight: 700;
            color: rgb(100 116 139);
            padding: 0.875rem 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .fc .fc-event {
            border-radius: 0.5rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
        
        .fc .fc-event:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 4px 8px -2px rgb(0 0 0 / 0.15);
            z-index: 10;
        }
        
        .fc .fc-daygrid-event-dot {
            display: none;
        }
        
        .fc-theme-standard td, 
        .fc-theme-standard th {
            border-color: rgb(241 245 249);
        }
        
        .fc .fc-scrollgrid {
            border-radius: 1rem;
            overflow: hidden;
            border-color: rgb(241 245 249);
        }
        
        .fc .fc-more-link {
            color: #6366f1;
            font-weight: 700;
            font-size: 0.75rem;
            background: rgb(238 242 255);
            padding: 0.125rem 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }
        
        .fc .fc-more-link:hover {
            background: rgb(224 231 255);
            color: #4f46e5;
        }
        
        .fc .fc-popover {
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            border: 1px solid rgb(241 245 249);
            overflow: hidden;
        }
        
        .fc .fc-popover-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 0.875rem 1rem;
            font-weight: 700;
            font-size: 0.875rem;
        }
        
        .fc .fc-popover-body {
            padding: 0.5rem;
        }
        
        .fc .fc-list {
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .fc .fc-list-day-cushion {
            background: linear-gradient(135deg, rgb(248 250 252) 0%, rgb(241 245 249) 100%);
            padding: 0.75rem 1rem;
        }
        
        .fc .fc-list-event:hover td {
            background: rgb(248 250 252);
        }
        
        /* Smooth scrollbar */
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
        
        /* Custom Toggle Style */
        #showGuardChange:checked ~ div:last-child {
            transform: translateX(1rem);
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                eventClick: function(info) {
                    showEventModal(info.event);
                },
                datesSet: function(info) {
                    updateSummary(info.view.currentStart, info.view.currentEnd);
                },
                eventDidMount: function(info) {
                    // Add tooltip
                    info.el.title = info.event.title;
                }
            });
            
            calendar.render();
            
            // Force refetch events after a short delay to ensure everything is loaded
            setTimeout(function() {
                calendar.refetchEvents();
                if (calendar.view) {
                    updateSummary(calendar.view.currentStart, calendar.view.currentEnd);
                }
            }, 100);
            
            // Filter change handlers
            if (departmentFilter) {
                departmentFilter.addEventListener('change', function() {
                    calendar.refetchEvents();
                    updateSummary(calendar.view.currentStart, calendar.view.currentEnd);
                });
            }
            
            if (showGuardChangeCheckbox) {
                showGuardChangeCheckbox.addEventListener('change', function() {
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
            
            // Set header gradient based on type
            const gradients = {
                'vacation': 'from-emerald-500 to-green-600',
                'sick': 'from-red-500 to-rose-600',
                'personal': 'from-amber-500 to-orange-600',
                'guard_change': 'from-slate-500 to-slate-700',
                'default': 'from-indigo-500 to-purple-600'
            };
            
            let html = '';
            
            if (props.type === 'leave') {
                const gradient = gradients[props.leaveTypeSlug] || gradients['default'];
                modalHeader.className = `relative px-6 pt-6 pb-8 bg-gradient-to-br ${gradient}`;
                modalTitle.textContent = props.leaveType;
                modalSubtitle.textContent = `${props.totalDays} วัน`;
                
                html = `
                    <div class="space-y-4">
                        <!-- User Info -->
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                ${props.userName.charAt(0)}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800">${props.userName}</p>
                                <p class="text-sm text-slate-500">${props.department || 'ไม่ระบุแผนก'}</p>
                            </div>
                        </div>
                        
                        <!-- Date Range -->
                        <div class="flex items-center gap-3 p-4 bg-indigo-50 rounded-2xl">
                            <div class="p-2.5 bg-indigo-100 rounded-xl">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-indigo-600 uppercase tracking-wider">ช่วงเวลาลา</p>
                                <p class="font-bold text-slate-800">${props.startDate} - ${props.endDate}</p>
                            </div>
                        </div>
                        
                        ${props.reason ? `
                        <!-- Reason -->
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">เหตุผลการลา</p>
                            <p class="text-slate-700">${props.reason}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
            } else if (props.type === 'guard_change') {
                modalHeader.className = `relative px-6 pt-6 pb-8 bg-gradient-to-br ${gradients['guard_change']}`;
                modalTitle.textContent = 'เปลี่ยนเวร';
                modalSubtitle.textContent = 'Guard Change Request';
                
                html = `
                    <div class="space-y-4">
                        <!-- User Info -->
                        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl">
                            <div class="w-12 h-12 bg-gradient-to-br from-slate-500 to-slate-700 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                ${props.userName.charAt(0)}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800">${props.userName}</p>
                                <p class="text-sm text-slate-500">${props.department || 'ไม่ระบุแผนก'}</p>
                            </div>
                        </div>
                        
                        <!-- Date Changes -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-red-50 rounded-2xl text-center">
                                <p class="text-xs font-medium text-red-600 uppercase tracking-wider mb-1">วันเดิม</p>
                                <p class="font-bold text-red-700">${props.originalDate}</p>
                            </div>
                            <div class="p-4 bg-emerald-50 rounded-2xl text-center">
                                <p class="text-xs font-medium text-emerald-600 uppercase tracking-wider mb-1">วันใหม่</p>
                                <p class="font-bold text-emerald-700">${props.newDate}</p>
                            </div>
                        </div>
                        
                        ${props.substituteUser ? `
                        <div class="flex items-center gap-3 p-4 bg-amber-50 rounded-2xl">
                            <div class="p-2.5 bg-amber-100 rounded-xl">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-amber-600 uppercase tracking-wider">ผู้เข้าเวรแทน</p>
                                <p class="font-bold text-slate-800">${props.substituteUser}</p>
                            </div>
                        </div>
                        ` : ''}
                        
                        ${props.reason ? `
                        <div class="p-4 bg-slate-50 rounded-2xl">
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
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEventModal();
            }
        });
    </script>
    @endpush
</x-app-layout>

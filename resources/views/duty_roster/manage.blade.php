@extends('layouts.app')

@section('title', 'จัดการตารางเวร')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
    .manage-day-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        padding: 16px;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .manage-day-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #e2e8f0;
        transition: all 0.3s;
    }

    .manage-day-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        border-color: #e2e8f0;
    }

    .manage-day-card.weekend::before {
        background: linear-gradient(180deg, #f43f5e, #e11d48);
    }

    .manage-day-card.has-data::before {
        background: linear-gradient(180deg, #10b981, #059669);
    }

    .manage-day-card.editing {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .manage-day-card.editing::before {
        background: linear-gradient(180deg, #4f46e5, #4338ca);
    }

    .manage-day-card.today {
        border-color: #4f46e5;
    }

    .manage-day-card.today::before {
        background: linear-gradient(180deg, #6366f1, #4f46e5);
    }

    /* We no longer need to style .select-officer natively if TomSelect takes over, but keep it for fallback */
    .select-officer {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        background: white;
        transition: all 0.2s;
        appearance: none;
    }

    .select-officer:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    /* TomSelect UI Customization */
    .ts-control {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        transition: all 0.2s;
        box-shadow: none !important;
        font-family: inherit;
        background-color: white;
    }
    .ts-control.focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
    }
    .ts-control > input {
        font-size: 13px;
        font-weight: 500;
        font-family: inherit;
    }
    .ts-wrapper.single .ts-control::after {
        border-color: #6b7280 transparent transparent transparent;
        border-width: 5px 4px 0 4px;
        right: 12px;
    }
    .ts-dropdown {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        font-size: 13px;
        font-weight: 500;
        padding: 4px;
        margin-top: 4px;
        z-index: 1000;
    }
    .ts-dropdown .option {
        border-radius: 6px;
        padding: 8px 12px;
        transition: all 0.1s;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #f8fafc;
        color: #4f46e5;
        font-weight: 600;
    }

    .save-btn-single {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }

    .save-btn-single.primary {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .save-btn-single.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .save-btn-single.danger {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: white;
        box-shadow: 0 2px 8px rgba(244, 63, 94, 0.3);
    }

    .save-btn-single.danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(244, 63, 94, 0.4);
    }

    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        padding: 12px 20px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        color: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        transform: translateX(120%);
        transition: transform 0.4s ease;
    }

    .toast-notification.show {
        transform: translateX(0);
    }

    .toast-notification.success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .toast-notification.error {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
    }

    .month-nav-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s;
    }

    .month-nav-btn:hover {
        background: #f8fafc;
        border-color: #4f46e5;
        color: #4f46e5;
        transform: scale(1.05);
    }

    .notes-input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 12px;
        color: #334155;
        transition: all 0.2s;
    }

    .notes-input:focus {
        outline: none;
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .senior-section {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fbbf24;
        border-radius: 20px;
        padding: 20px;
    }

    .senior-card-existing {
        background: white;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 14px 16px;
        position: relative;
        transition: all 0.2s;
    }

    .senior-card-existing:hover {
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }

    .date-input-sm {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        background: white;
        transition: all 0.2s;
    }

    .date-input-sm:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
    }

    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .saving-spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 fade-in" x-data="dutyRosterManager()">

    {{-- Toast Notification --}}
    <div x-show="toast.show" x-transition
        :class="'toast-notification ' + toast.type + (toast.show ? ' show' : '')"
        x-text="toast.message" style="display: none;"></div>

    {{-- Header --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i data-lucide="settings" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">จัดการตารางเวร</h1>
                        <p class="text-xs text-slate-400 font-medium">Manage Duty Roster</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('duty-roster.template', ['year' => $year, 'month' => $month]) }}"
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-emerald-50 text-emerald-600 border border-emerald-100 font-bold rounded-xl hover:bg-emerald-100 transition-all text-xs sm:text-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">โหลดแม่แบบ</span>
                </a>
                
                <button type="button" @click="$refs.importFile.click()"
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-brand-50 text-brand-600 border border-brand-100 font-bold rounded-xl hover:bg-brand-100 transition-all text-xs sm:text-sm">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">นำเข้าข้อมูล</span>
                </button>
                <form x-ref="importForm" action="{{ route('duty-roster.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="file" x-ref="importFile" accept=".xlsx,.xls,.csv" class="hidden" @change="$refs.importForm.submit()">
                </form>

                <a href="{{ route('duty-roster.index', ['year' => $year, 'month' => $month]) }}"
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-xs sm:text-sm">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">ดูตาราง</span>
                </a>

                <a href="{{ route('duty-roster.export-pdf', ['year' => $year, 'month' => $month]) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-rose-50 border border-rose-100 text-rose-600 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs sm:text-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">ส่งออก PDF</span>
                </a>



                <form method="POST" action="{{ route('duty-roster.clear-month') }}" class="inline-block m-0" onsubmit="return confirm('ยืนยันการล้างข้อมูลเวรทั้งหมดสำหรับเดือนนี้ให้เป็นค่าว่าง?')">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-rose-50 text-rose-600 border border-rose-100 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs sm:text-sm">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">ล้างข้อมูลทั้งเดือน</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Month Navigation --}}
    <div
        class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 mb-6 flex items-center justify-between">
        @php
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
            }
            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
            }

            $startOfMonth = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            @endphp

            <a href="{{ route('duty-roster.manage', ['year' => $prevYear, 'month' => $prevMonth]) }}"
                class="month-nav-btn">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </a>

            <div class="text-center">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">
                    {{ $monthName }} {{ $thaiYear }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">คลิกที่แต่ละวันเพื่อกำหนดเวร</p>
            </div>

            <a href="{{ route('duty-roster.manage', ['year' => $nextYear, 'month' => $nextMonth]) }}"
                class="month-nav-btn">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </a>
    </div>

    {{-- =====================================================
         Exemption Settings Section (ตั้งค่ารายชื่อผู้ได้รับการยกเว้นเวร)
         ===================================================== --}}
    <div class="senior-section mb-6">
        <form method="POST" action="{{ route('duty-roster.exemptions') }}" class="bg-white rounded-2xl border border-rose-100 p-4 sm:p-5 shadow-sm">
            @csrf
            
            <div class="flex flex-col md:flex-row gap-4 items-start md:items-end justify-between">
                <div class="w-full md:flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-400 to-rose-600 flex items-center justify-center shadow-md shadow-rose-500/20">
                            <i data-lucide="user-x" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-rose-800 uppercase tracking-wider">บุคคลที่ได้รับการยกเว้นการเข้าเวร</h3>
                            <p class="text-[10px] text-rose-600">บันทึกครั้งเดียวใช้ตลอดไป (ไม่ต้องมาตั้งค่าซ้ำทุกเดือน)</p>
                        </div>
                    </div>
                    <div>
                        <select name="exempt_users[]" class="select-officer w-full text-sm" multiple placeholder="พิมพ์และเลือกรายชื่อผู้ได้รับการยกเว้นเวรยาม...">
                            <option value="">-- พิมพ์เพื่อค้นหา --</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, $exemptUserIds) ? 'selected' : '' }}>
                                {{ $user->rank }} {{ $user->name }} ({{ $user->department ?? 'ไม่มีแผนก' }})
                            </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1">* ยกเว้นยศ นาย, นาง, นางสาว และคนที่อยู่ "หลักสูตรนายทหารพลาธิการฯ" โดยอัตโนมัติแล้ว</p>
                    </div>
                </div>
                <div class="w-full md:w-auto mt-2 md:mt-0 shrink-0">
                    <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 transition-all text-xs sm:text-sm shadow-md shadow-rose-500/20">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>บันทึกข้อยกเว้นถาวร</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- =====================================================
         Auto Schedule Section (จัดเวรอัตโนมัติ)
         ===================================================== --}}
    <div class="senior-section mb-6">
        <form method="POST" action="{{ route('duty-roster.auto-schedule') }}" class="bg-white rounded-2xl border border-indigo-100 p-4 sm:p-5 shadow-sm flex items-center justify-between" onsubmit="return confirm('ยืนยันการจัดเวรอัตโนมัติสำหรับเดือนนี้?\n(ข้อมูลเวรประจำวันทั้งหมดในเดือนนี้จะถูกล้างและจัดใหม่ ยกเว้นเวรอาวุโส)')">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            
            <div class="flex items-center gap-3">
                 <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
                     <i data-lucide="zap" class="w-5 h-5 text-white"></i>
                 </div>
                 <div>
                     <h3 class="text-sm font-bold text-indigo-800 uppercase tracking-wider">จัดเวรอัตโนมัติประจำเดือน</h3>
                     <p class="text-xs text-indigo-600">สุ่มเวรหลักและเวรสำรองให้อัตโนมัติ โดยจะข้ามบุคคลที่ถูกยกเว้นไว้</p>
                 </div>
            </div>
            
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition-all text-xs sm:text-sm shadow-md shadow-indigo-500/20">
                <i data-lucide="zap" class="w-4 h-4"></i>
                <span class="hidden sm:inline">จัดเวรอัตโนมัติ</span>
                <span class="sm:hidden">จัดเวร</span>
            </button>
        </form>
    </div>

    {{-- =====================================================
         Monthly Reserve Duty Officer Section (เวรสำรองประจำเดือน)
         ===================================================== --}}
    <div class="senior-section mb-6">
        <form method="POST" action="{{ route('duty-roster.set-monthly-reserve') }}" class="bg-white rounded-2xl border border-sky-100 p-4 sm:p-5 shadow-sm">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="month" value="{{ $month }}">
            
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end justify-between">
                <div class="w-full sm:w-auto">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center shadow-md shadow-sky-500/20">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-sky-800 uppercase tracking-wider">เวรสำรองประจำเดือน</h3>
                            <p class="text-[10px] text-sky-600">ตั้งค่าเวรสำรองสำหรับ 1 เดือน (1 คน/เดือน)</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-64">
                            <label class="text-[10px] font-bold text-sky-700 uppercase tracking-wider mb-1 block">นายทหารเวร (สำรอง)</label>
                            <select name="reserve_duty_officer_id" class="select-officer w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none text-sm transition-all">
                                <option value="">-- ไม่ระบุ --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (isset($days[0]['roster']) && $days[0]['roster']->reserve_duty_officer_id == $user->id) ? 'selected' : '' }}>
                                    {{ $user->rank }} {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full sm:w-64">
                            <label class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mb-1 block">ผู้ช่วยนายทหารเวร (สำรอง)</label>
                            <select name="reserve_assistant_duty_officer_id" class="select-officer w-full h-10 px-3 rounded-lg border border-slate-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 outline-none text-sm transition-all">
                                <option value="">-- ไม่ระบุ --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (isset($days[0]['roster']) && $days[0]['roster']->reserve_assistant_duty_officer_id == $user->id) ? 'selected' : '' }}>
                                    {{ $user->rank }} {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="w-full sm:w-auto mt-2 sm:mt-0">
                    <button type="submit" onclick="return confirm('ยืนยันตั้งค่าเวรสำรองประจำเดือนนี้? การทำรายการนี้จะเปลี่ยนเวรสำรองของทุกวันในเดือนนี้เป็นรายชื่อที่เลือก')" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sky-500 text-white font-bold rounded-xl hover:bg-sky-600 transition-all text-xs sm:text-sm shadow-md shadow-sky-500/20">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>บันทึกเวรสำรอง</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- =====================================================
         Senior Duty Officer Section (นายทหารเวรอาวุโส - ห้วงวัน)
         ===================================================== --}}
    <div class="senior-section mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md shadow-amber-500/20">
                    <i data-lucide="crown" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-800 uppercase tracking-wider">นายทหารเวรอาวุโส</h3>
                    <p class="text-[10px] text-amber-600">กำหนดเป็นห้วงเวลา (เช่น 1-8, 9-15 ...)</p>
                </div>
            </div>
        </div>

        {{-- Existing Senior Rosters --}}
        @if($seniorRosters->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
            @foreach($seniorRosters as $senior)
            <div class="senior-card-existing" id="senior-card-{{ $senior->id }}">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="crown" class="w-4 h-4 text-amber-600"></i>
                            <span class="text-sm font-bold text-amber-800">
                                {{ $senior->seniorOfficer ? $senior->seniorOfficer->rank . ' ' . $senior->seniorOfficer->name : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-amber-700">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            <span class="font-medium">
                                {{ $senior->start_date->format('j') }} - {{ $senior->end_date->format('j') }}
                                {{ $monthName }} {{ $thaiYear }}
                            </span>
                        </div>
                        @if($senior->notes)
                        <div class="mt-1 text-[11px] text-amber-600">📝 {{ $senior->notes }}</div>
                        @endif
                    </div>
                    <button type="button"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-100 transition-all cursor-pointer"
                        @click="deleteSenior({{ $senior->id }})">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Add New Senior Roster Form --}}
        <div class="bg-white rounded-xl border border-amber-200 p-4">
            <h4 class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                เพิ่มนายทหารเวรอาวุโส
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div>
                    <label class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1 block">วันเริ่มต้น</label>
                    <input type="date" class="date-input-sm w-full" x-model="seniorForm.start_date"
                        min="{{ $startOfMonth->format('Y-m-d') }}" max="{{ $endOfMonth->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1 block">วันสิ้นสุด</label>
                    <input type="date" class="date-input-sm w-full" x-model="seniorForm.end_date"
                        min="{{ $startOfMonth->format('Y-m-d') }}" max="{{ $endOfMonth->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1 block">นายทหารเวรอาวุโส</label>
                    <select class="select-officer" x-model="seniorForm.senior_officer_id">
                        <option value="">-- เลือก --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->rank }} {{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-amber-600 uppercase tracking-wider mb-1 block">หมายเหตุ</label>
                    <input type="text" class="notes-input" x-model="seniorForm.notes" placeholder="เพิ่มหมายเหตุ...">
                </div>
                <div>
                    <button type="button"
                        class="save-btn-single primary w-full justify-center py-2"
                        @click="saveSenior()"
                        :disabled="savingSenior">
                        <template x-if="savingSenior">
                            <span class="saving-spinner"></span>
                        </template>
                        <template x-if="!savingSenior">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </template>
                        <span x-text="savingSenior ? 'กำลังบันทึก...' : 'เพิ่ม'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================
         Daily Duty Officer Cards (นายทหารเวร + ผู้ช่วยฯ)
         ===================================================== --}}
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20">
            <i data-lucide="shield" class="w-4 h-4 text-white"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">นายทหารเวร & ผู้ช่วยนายทหารเวร</h3>
            <p class="text-[10px] text-slate-400">กำหนดรายวัน</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($days as $index => $day)
        @php
        $date = $day['date'];
        $roster = $day['roster'];
        $isWeekend = in_array($date->dayOfWeek, [0, 6]);
        $isToday = $date->isToday();
        $hasRoster = $roster !== null;
        $thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        $dayLabel = $thaiDays[$date->dayOfWeek];
        @endphp

        <div class="manage-day-card {{ $isWeekend ? 'weekend' : '' }} {{ $isToday ? 'today' : '' }} {{ $hasRoster ? 'has-data' : '' }}"
            :class="editingDate === '{{ $date->format('Y-m-d') }}' ? 'editing' : ''">

            {{-- Day Header --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-bold {{ $isToday ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : ($isWeekend ? 'bg-rose-50 text-rose-500' : 'bg-slate-50 text-slate-700') }}">
                        {{ $date->day }}
                    </span>
                    <div>
                        <span
                            class="text-sm font-bold {{ $isWeekend ? 'text-rose-500' : 'text-slate-700' }}">{{ $dayLabel }}</span>
                        @if($isToday)
                        <span
                            class="ml-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded-md">วันนี้</span>
                        @endif
                    </div>
                </div>
                @if($hasRoster)
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-md shadow-emerald-500/30"></div>
                @endif
            </div>

            {{-- Edit Form --}}
            <div class="space-y-3">
                {{-- Duty Officer Select --}}
                <div>
                    <label class="text-[10px] font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1 mb-1">
                        <i data-lucide="shield" class="w-3 h-3"></i> นายทหารเวร
                    </label>
                    <select class="select-officer"
                        id="duty_officer_{{ $date->format('Y-m-d') }}"
                        data-date="{{ $date->format('Y-m-d') }}"
                        data-field="duty_officer_id"
                        @change="markChanged('{{ $date->format('Y-m-d') }}')">
                        <option value="">-- ไม่มี --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ $roster && $roster->duty_officer_id == $user->id ? 'selected' : '' }}>
                            {{ $user->rank }} {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Reserve Duty Officer Select --}}
                <div>
                    <label class="text-[10px] font-bold text-sky-600 uppercase tracking-wider flex items-center gap-1 mb-1">
                        <i data-lucide="shield-alert" class="w-3 h-3"></i> นายทหารเวร (สำรอง)
                    </label>
                    <select class="select-officer"
                        id="reserve_duty_officer_{{ $date->format('Y-m-d') }}"
                        data-date="{{ $date->format('Y-m-d') }}"
                        data-field="reserve_duty_officer_id"
                        @change="markChanged('{{ $date->format('Y-m-d') }}')">
                        <option value="">-- ไม่มี --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ $roster && $roster->reserve_duty_officer_id == $user->id ? 'selected' : '' }}>
                            {{ $user->rank }} {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Assistant Duty Officer Select --}}
                <div>
                    <label class="text-[10px] font-bold text-pink-600 uppercase tracking-wider flex items-center gap-1 mb-1">
                        <i data-lucide="shield-check" class="w-3 h-3"></i> ผู้ช่วยนายทหารเวร
                    </label>
                    <select class="select-officer"
                        id="assistant_{{ $date->format('Y-m-d') }}"
                        data-date="{{ $date->format('Y-m-d') }}"
                        data-field="assistant_duty_officer_id"
                        @change="markChanged('{{ $date->format('Y-m-d') }}')">
                        <option value="">-- ไม่มี --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ $roster && $roster->assistant_duty_officer_id == $user->id ? 'selected' : '' }}>
                            {{ $user->rank }} {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Reserve Assistant Duty Officer Select --}}
                <div>
                    <label class="text-[10px] font-bold text-rose-500 uppercase tracking-wider flex items-center gap-1 mb-1">
                        <i data-lucide="shield-plus" class="w-3 h-3"></i> ผู้ช่วยนายทหารเวร (สำรอง)
                    </label>
                    <select class="select-officer"
                        id="reserve_assistant_{{ $date->format('Y-m-d') }}"
                        data-date="{{ $date->format('Y-m-d') }}"
                        data-field="reserve_assistant_duty_officer_id"
                        @change="markChanged('{{ $date->format('Y-m-d') }}')">
                        <option value="">-- ไม่มี --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            {{ $roster && $roster->reserve_assistant_duty_officer_id == $user->id ? 'selected' : '' }}>
                            {{ $user->rank }} {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1 mb-1">
                        <i data-lucide="message-square" class="w-3 h-3"></i> หมายเหตุ
                    </label>
                    <input type="text" class="notes-input"
                        id="notes_{{ $date->format('Y-m-d') }}"
                        data-date="{{ $date->format('Y-m-d') }}"
                        value="{{ $roster->notes ?? '' }}"
                        placeholder="เพิ่มหมายเหตุ..."
                        @input="markChanged('{{ $date->format('Y-m-d') }}')">
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 pt-1">
                    <button type="button"
                        class="save-btn-single primary flex-1"
                        @click="saveSingle('{{ $date->format('Y-m-d') }}')"
                        :disabled="saving === '{{ $date->format('Y-m-d') }}'">
                        <template x-if="saving === '{{ $date->format('Y-m-d') }}'">
                            <span class="saving-spinner"></span>
                        </template>
                        <template x-if="saving !== '{{ $date->format('Y-m-d') }}'">
                            <i data-lucide="check" class="w-3 h-3"></i>
                        </template>
                        <span x-text="saving === '{{ $date->format('Y-m-d') }}' ? 'กำลังบันทึก...' : 'บันทึก'"></span>
                    </button>
                    @if($hasRoster)
                    <button type="button"
                        class="save-btn-single danger"
                        @click="deleteSingle('{{ $date->format('Y-m-d') }}')"
                        :disabled="saving === '{{ $date->format('Y-m-d') }}'">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    function dutyRosterManager() {
        return {
            editingDate: null,
            saving: null,
            savingSenior: false,
            changedDates: new Set(),
            toast: {
                show: false,
                message: '',
                type: 'success',
            },
            seniorForm: {
                start_date: '',
                end_date: '',
                senior_officer_id: '',
                notes: '',
            },

            init() {
                // Initialize TomSelect on all current select-officer elements
                this.$nextTick(() => {
                    const selects = document.querySelectorAll('.select-officer');
                    selects.forEach(el => {
                        let isSeniorForm = el.hasAttribute('x-model') && el.getAttribute('x-model') === 'seniorForm.senior_officer_id';
                        
                        let instance = new TomSelect(el, {
                            create: false,
                            maxOptions: null,
                            searchField: ['text'],
                            placeholder: "พิมพ์เพื่อค้นหาชื่อ...",
                            onChange: (value) => {
                                // For Senior Form binding
                                if (isSeniorForm) {
                                    this.seniorForm.senior_officer_id = value;
                                } else {
                                    // For Daily Form changed event
                                    const date = el.getAttribute('data-date');
                                    if(date) {
                                        this.markChanged(date);
                                    }
                                }
                            }
                        });
                    });
                });
            },

            markChanged(date) {
                this.changedDates.add(date);
                this.editingDate = date;
            },

            showToast(message, type = 'success') {
                this.toast = {
                    show: true,
                    message: message,
                    type: type,
                };
                setTimeout(() => {
                    this.toast.show = false;
                }, 3000);
            },

            async saveSingle(date) {
                this.saving = date;

                const dutyOfficerId = document.getElementById('duty_officer_' + date)?.value || null;
                const reserveDOId = document.getElementById('reserve_duty_officer_' + date)?.value || null;
                const assistantId = document.getElementById('assistant_' + date)?.value || null;
                const reserveADOId = document.getElementById('reserve_assistant_' + date)?.value || null;
                const notes = document.getElementById('notes_' + date)?.value || null;

                try {
                    const response = await fetch('{{ route("duty-roster.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            duty_date: date,
                            duty_officer_id: dutyOfficerId || null,
                            reserve_duty_officer_id: reserveDOId || null,
                            assistant_duty_officer_id: assistantId || null,
                            reserve_assistant_duty_officer_id: reserveADOId || null,
                            notes: notes,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(data.message, 'success');
                        this.changedDates.delete(date);
                        this.editingDate = null;

                        const card = document.getElementById('duty_officer_' + date)?.closest('.manage-day-card');
                        if (card) {
                            if (dutyOfficerId || assistantId) {
                                card.classList.add('has-data');
                            }
                        }
                    } else {
                        this.showToast('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถบันทึกได้'), 'error');
                    }
                } catch (error) {
                    this.showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    console.error(error);
                }

                this.saving = null;
            },

            async deleteSingle(date) {
                if (!confirm('ต้องการลบข้อมูลเวรของวันนี้ใช่หรือไม่?')) {
                    return;
                }

                this.saving = date;

                try {
                    const response = await fetch('{{ route("duty-roster.destroy") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            duty_date: date,
                        }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        this.showToast('เกิดข้อผิดพลาด', 'error');
                    }
                } catch (error) {
                    this.showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    console.error(error);
                }

                this.saving = null;
            },

            // Senior Duty Officer Methods
            async saveSenior() {
                if (!this.seniorForm.start_date || !this.seniorForm.end_date || !this.seniorForm.senior_officer_id) {
                    this.showToast('กรุณากรอกข้อมูลให้ครบ (วันเริ่มต้น, วันสิ้นสุด, เจ้าหน้าที่)', 'error');
                    return;
                }

                this.savingSenior = true;

                try {
                    const response = await fetch('{{ route("duty-roster.senior.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.seniorForm),
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        this.showToast('เกิดข้อผิดพลาด: ' + (data.message || ''), 'error');
                    }
                } catch (error) {
                    this.showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    console.error(error);
                }

                this.savingSenior = false;
            },

            async deleteSenior(id) {
                if (!confirm('ต้องการลบข้อมูลนายทหารเวรอาวุโสนี้ใช่หรือไม่?')) {
                    return;
                }

                try {
                    const response = await fetch(`{{ url('/duty-roster/senior') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.showToast(data.message, 'success');
                        const card = document.getElementById('senior-card-' + id);
                        if (card) {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                            setTimeout(() => card.remove(), 300);
                        }
                    } else {
                        this.showToast('เกิดข้อผิดพลาด', 'error');
                    }
                } catch (error) {
                    this.showToast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    console.error(error);
                }
            },
        };
    }
</script>
@endpush

@endsection

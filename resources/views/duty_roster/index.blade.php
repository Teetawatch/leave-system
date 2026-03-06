@extends('layouts.app')

@section('title', 'ตารางเวรประจำเดือน')

@push('styles')
<style>
    .duty-calendar {
        border-collapse: separate;
        border-spacing: 0;
    }

    .duty-calendar th {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .day-cell {
        min-height: 90px;
        transition: all 0.2s ease;
    }

    .day-cell:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .day-cell.today {
        outline: 2px solid #4f46e5;
        outline-offset: -2px;
    }

    .day-cell.weekend {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }

    .day-cell.has-roster {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    }

    .officer-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .officer-badge:hover {
        transform: scale(1.02);
    }

    .officer-badge.duty-officer {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
    }

    .officer-badge.assistant-officer {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #9d174d;
    }

    .senior-officer-card {
        background: linear-gradient(135deg, #fefce8 0%, #fef9c3 50%, #fde68a 100%);
        border: 1px solid #fbbf24;
        border-radius: 16px;
        padding: 16px 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s;
    }

    .senior-officer-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #f59e0b, #d97706);
    }

    .senior-officer-card:hover {
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.2);
        transform: translateY(-1px);
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

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 10px;
        background: white;
        border: 1px solid #f1f5f9;
        font-size: 12px;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .duty-calendar-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .mobile-day-card {
            background: white;
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        .mobile-day-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .mobile-day-card.weekend {
            background: linear-gradient(135deg, #fef7f7, #fef2f2);
            border-color: #fecaca;
        }

        .mobile-day-card.today {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }

        .mobile-day-card.has-roster {
            background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
            border-color: #bbf7d0;
        }
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

    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 fade-in">

    {{-- Header Section --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i data-lucide="shield" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">ตารางเวรประจำเดือน</h1>
                        <p class="text-xs text-slate-400 font-medium">Duty Roster Schedule</p>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="flex flex-wrap gap-2">
                <div class="legend-item">
                    <div class="w-3 h-3 rounded-full bg-gradient-to-br from-amber-400 to-amber-600"></div>
                    <span class="text-slate-600">นายทหารเวรอาวุโส</span>
                </div>
                <div class="legend-item">
                    <div class="w-3 h-3 rounded-full bg-gradient-to-br from-blue-400 to-blue-600"></div>
                    <span class="text-slate-600">นายทหารเวร</span>
                </div>
                <div class="legend-item">
                    <div class="w-3 h-3 rounded-full bg-gradient-to-br from-pink-400 to-pink-600"></div>
                    <span class="text-slate-600">ผู้ช่วยนายทหารเวร</span>
                </div>
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
            @endphp

            <a href="{{ route('duty-roster.index', ['year' => $prevYear, 'month' => $prevMonth]) }}"
                class="month-nav-btn">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </a>

            <div class="text-center">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800">
                    {{ $monthName }} {{ $thaiYear }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    @php
                    $rosterCount = collect($days)->filter(fn($d) => $d['roster'])->count();
                    @endphp
                    กำหนดเวรแล้ว {{ $rosterCount }} วัน / {{ count($days) }} วัน
                </p>
            </div>

            <a href="{{ route('duty-roster.index', ['year' => $nextYear, 'month' => $nextMonth]) }}"
                class="month-nav-btn">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </a>
    </div>

    {{-- Senior Duty Officer Section (นายทหารเวรอาวุโส - ห้วงเวลา) --}}
    @if($seniorRosters->count() > 0)
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center">
                <i data-lucide="crown" class="w-3.5 h-3.5 text-white"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">นายทหารเวรอาวุโส</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($seniorRosters as $senior)
            <div class="senior-officer-card">
                <div class="pl-3">
                    <div class="flex items-center gap-2 mb-2">
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
                    <div class="mt-1.5 text-[11px] text-amber-600">📝 {{ $senior->notes }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Desktop Calendar View --}}
    <div class="hidden md:block bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full duty-calendar">
            <thead>
                <tr>
                    @foreach(['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'] as $idx => $dayName)
                    <th
                        class="py-3 px-2 text-xs font-bold uppercase tracking-wider {{ in_array($idx, [0, 6]) ? 'text-rose-500 bg-rose-50/50' : 'text-slate-500 bg-slate-50/50' }} border-b border-slate-100">
                        {{ $dayName }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                $firstDayOfWeek = \Carbon\Carbon::create($year, $month, 1)->dayOfWeek;
                $cellIndex = 0;
                @endphp

                <tr>
                    {{-- Empty cells for days before the 1st --}}
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                        <td class="border border-slate-50 bg-slate-25 p-2 align-top">
                            <div class="min-h-[90px]"></div>
                        </td>
                        @php $cellIndex++; @endphp
                        @endfor

                        @foreach($days as $day)
                        @php
                        $date = $day['date'];
                        $roster = $day['roster'];
                        $isWeekend = in_array($date->dayOfWeek, [0, 6]);
                        $isToday = $date->isToday();
                        $hasRoster = $roster !== null;

                        $cellClasses = 'day-cell border border-slate-50 p-2 align-top relative';
                        if ($isToday) $cellClasses .= ' today';
                        if ($isWeekend && !$hasRoster) $cellClasses .= ' weekend';
                        if ($hasRoster) $cellClasses .= ' has-roster';
                        @endphp

                        <td class="{{ $cellClasses }}">
                            <div class="min-h-[90px]">
                                {{-- Date Number --}}
                                <div class="flex items-center justify-between mb-2">
                                    <span
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-sm font-bold {{ $isToday ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : ($isWeekend ? 'text-rose-500' : 'text-slate-700') }}">
                                        {{ $date->day }}
                                    </span>
                                    @if($isToday)
                                    <span
                                        class="text-[9px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded-md">วันนี้</span>
                                    @endif
                                </div>

                                {{-- Roster Info --}}
                                @if($roster)
                                @if($roster->dutyOfficer)
                                <div class="officer-badge duty-officer mb-1">
                                    <i data-lucide="shield" class="w-3 h-3 flex-shrink-0"></i>
                                    <span class="truncate">{{ $roster->dutyOfficer->rank }}
                                        {{ $roster->dutyOfficer->name }}</span>
                                </div>
                                @endif
                                @if($roster->assistantDutyOfficer)
                                <div class="officer-badge assistant-officer">
                                    <i data-lucide="shield-check" class="w-3 h-3 flex-shrink-0"></i>
                                    <span class="truncate">{{ $roster->assistantDutyOfficer->rank }}
                                        {{ $roster->assistantDutyOfficer->name }}</span>
                                </div>
                                @endif
                                @if($roster->notes)
                                <div class="mt-1 text-[10px] text-slate-400 truncate" title="{{ $roster->notes }}">
                                    📝 {{ $roster->notes }}
                                </div>
                                @endif
                                @endif
                            </div>
                        </td>

                        @php $cellIndex++; @endphp

                        @if($cellIndex % 7 == 0 && !$loop->last)
                </tr>
                <tr>
                    @endif
                    @endforeach

                    {{-- Fill remaining cells --}}
                    @while($cellIndex % 7 != 0)
                    <td class="border border-slate-50 bg-slate-25 p-2 align-top">
                        <div class="min-h-[90px]"></div>
                    </td>
                    @php $cellIndex++; @endphp
                    @endwhile
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Mobile List View --}}
    <div class="md:hidden duty-calendar-grid">
        @foreach($days as $day)
        @php
        $date = $day['date'];
        $roster = $day['roster'];
        $isWeekend = in_array($date->dayOfWeek, [0, 6]);
        $isToday = $date->isToday();
        $hasRoster = $roster !== null;

        $thaiDays = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];
        $dayLabel = $thaiDays[$date->dayOfWeek];

        $cardClasses = 'mobile-day-card';
        if ($isToday) $cardClasses .= ' today';
        elseif ($isWeekend && !$hasRoster) $cardClasses .= ' weekend';
        if ($hasRoster) $cardClasses .= ' has-roster';
        @endphp

        <div class="{{ $cardClasses }}">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-bold {{ $isToday ? 'bg-indigo-600 text-white shadow-md' : ($isWeekend ? 'bg-rose-50 text-rose-500' : 'bg-slate-50 text-slate-700') }}">
                        {{ $date->day }}
                    </span>
                    <div>
                        <span
                            class="text-sm font-bold {{ $isWeekend ? 'text-rose-500' : 'text-slate-700' }}">{{ $dayLabel }}</span>
                        @if($isToday)
                        <span
                            class="ml-1.5 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded-md">วันนี้</span>
                        @endif
                    </div>
                </div>
                @if($hasRoster)
                <div class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></div>
                @endif
            </div>

            @if($roster)
            <div class="space-y-1.5 pl-10">
                @if($roster->dutyOfficer)
                <div class="officer-badge duty-officer">
                    <i data-lucide="shield" class="w-3 h-3 flex-shrink-0"></i>
                    <span>{{ $roster->dutyOfficer->rank }} {{ $roster->dutyOfficer->name }}</span>
                </div>
                @endif
                @if($roster->assistantDutyOfficer)
                <div class="officer-badge assistant-officer">
                    <i data-lucide="shield-check" class="w-3 h-3 flex-shrink-0"></i>
                    <span>{{ $roster->assistantDutyOfficer->rank }}
                        {{ $roster->assistantDutyOfficer->name }}</span>
                </div>
                @endif
                @if($roster->notes)
                <div class="text-xs text-slate-400 mt-1">📝 {{ $roster->notes }}</div>
                @endif
            </div>
            @else
            <div class="pl-10 text-xs text-slate-300 italic">ยังไม่กำหนดเวร</div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Admin Quick Access --}}
    @if(Auth::user()->role === 'admin')
    <div class="mt-6 text-center">
        <a href="{{ route('duty-roster.manage', ['year' => $year, 'month' => $month]) }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all hover:scale-105 text-sm">
            <i data-lucide="settings" class="w-4 h-4"></i>
            จัดการตารางเวรเดือนนี้
        </a>
    </div>
    @endif
</div>
@endsection

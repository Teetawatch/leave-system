@extends('layouts.app')

@section('title', 'จัดอันดับยอดเยี่ยม')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-700pb-12">
        <!-- Header Section -->
        <div class="relative overflow-hidden bg-slate-900 rounded-[2.5rem] shadow-2xl p-8 md:p-12 text-white">
            <!-- Background Decor -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-gradient-to-br from-amber-400/20 to-purple-600/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-gradient-to-tr from-cyan-400/20 to-emerald-400/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/50 border border-slate-700/50 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="text-xs font-bold text-amber-100/80 tracking-widest uppercase">Hall of Fame</span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-100 to-amber-300 drop-shadow-sm">
                        ทำเนียบคนเก่ง
                    </h1>
                    <p class="text-slate-400 text-lg font-medium max-w-xl">
                        สุดยอดบุคลากรประจำปี {{ $year }} @if($month) เดือน {{ \Carbon\Carbon::create()->month($month)->locale('th')->monthName }} @endif
                        จากทุกฝ่ายงาน
                    </p>
                </div>

                <form action="{{ route('ranking.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 bg-white/5 p-2 rounded-2xl backdrop-blur-sm border border-white/10">
                    <select name="year" class="bg-slate-800 border-none text-white text-sm font-bold rounded-xl focus:ring-2 focus:ring-amber-500 py-2.5 px-4 cursor-pointer hover:bg-slate-700 transition-colors">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>ปี {{ $y + 543 }}</option>
                        @endfor
                    </select>
                    <select name="month" class="bg-slate-800 border-none text-white text-sm font-bold rounded-xl focus:ring-2 focus:ring-amber-500 py-2.5 px-4 cursor-pointer hover:bg-slate-700 transition-colors">
                        <option value="">ตลอดทั้งปี</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                เดือน{{ \Carbon\Carbon::create()->month($m)->locale('th')->monthName }}
                            </option>
                        @endfor
                    </select>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-900 font-bold py-2.5 px-6 rounded-xl transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                        <i data-lucide="search" class="w-4 h-4 inline-block mr-2"></i>ค้นหา
                    </button>
                </form>
            </div>
        </div>

        <!-- Award Cards Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

            @php
                $rankings = [
                    // Attendance
                    [
                        'title' => 'มาเช้าดีเด่น',
                        'subtitle' => 'The Early Bird',
                        'icon' => 'sunrise',
                        'color' => 'amber',
                        'gradient' => 'from-amber-500 to-orange-600',
                        'data' => $earlyBirds,
                        'unit' => 'น.',
                        'is_time' => true,
                        'desc' => 'ผู้ที่สแกนนิ้วเข้างานเช้าที่สุดโดยเฉลี่ย'
                    ],
                    [
                        'title' => 'ไม่เคยสายดีเด่น',
                        'subtitle' => 'Mr. Perfect Timer',
                        'icon' => 'shield-check',
                        'color' => 'emerald',
                        'gradient' => 'from-emerald-500 to-teal-600',
                        'data' => $neverLate,
                        'unit' => 'วัน',
                        'is_time' => false,
                        'desc' => 'ผู้ที่ไม่เคยมาสายเลยแม้แต่วันเดียว'
                    ],
                    [
                        'title' => 'นักมาทำงานดีเด่น',
                        'subtitle' => 'The Consistent',
                        'icon' => 'award',
                        'color' => 'blue',
                        'gradient' => 'from-blue-500 to-indigo-600',
                        'data' => $mostScans,
                        'unit' => 'วัน',
                        'is_time' => false,
                        'desc' => 'ผู้ที่มีวันทำงานมากที่สุด'
                    ],
                    [
                        'title' => 'นาฬิกาชีวิตแม่นยำ',
                        'subtitle' => 'Atomic Clock',
                        'icon' => 'crosshair',
                        'color' => 'violet',
                        'gradient' => 'from-violet-500 to-purple-600',
                        'data' => $mostScans, // Using same dataset as requested roughly
                        'unit' => 'ครั้ง',
                        'is_time' => false,
                        'desc' => 'ผู้ที่รักษาเวลาได้คงที่ที่สุด'
                    ],
                    // Leave
                    [
                        'title' => 'นักลาดีเด่น',
                        'subtitle' => 'Vacation Expert',
                        'icon' => 'plane',
                        'color' => 'cyan',
                        'gradient' => 'from-cyan-500 to-sky-600',
                        'data' => $mostRequests,
                        'unit' => 'ครั้ง',
                        'is_time' => false,
                        'desc' => 'ผู้ที่ทำรายการลามากที่สุด'
                    ],
                    [
                        'title' => 'ราชาแห่งการลา',
                        'subtitle' => 'Legend of Leave',
                        'icon' => 'crown',
                        'color' => 'rose',
                        'gradient' => 'from-rose-500 to-pink-600',
                        'data' => $kingOfLeave,
                        'unit' => 'วัน',
                        'is_time' => false,
                        'desc' => 'ผู้ที่มีจำนวนวันลารวมมาที่สุด'
                    ],
                    [
                        'title' => 'ลาทีไร หายยาว',
                        'subtitle' => 'The Long Gone',
                        'icon' => 'calendar-clock',
                        'color' => 'fuchsia',
                        'gradient' => 'from-fuchsia-500 to-purple-600',
                        'data' => $longAbsence,
                        'unit' => 'วัน/ครั้ง',
                        'is_time' => false,
                        'desc' => 'เฉลี่ยวันลาต่อครั้งนานที่สุด'
                    ],
                    [
                        'title' => 'ขอลาได้ทุกสถานการณ์',
                        'subtitle' => 'Universal Leaver',
                        'icon' => 'globe',
                        'color' => 'lime',
                        'gradient' => 'from-lime-500 to-green-600',
                        'data' => $diverseLeave,
                        'unit' => 'ประเภท',
                        'is_time' => false,
                        'desc' => 'ลาครบทุกประเภทที่มี'
                    ],
                ];
            @endphp

            @foreach($rankings as $rank)
                <div class="group relative bg-white rounded-[2rem] p-1 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
                    <!-- Border Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br {{ $rank['gradient'] }} opacity-0 group-hover:opacity-10 rounded-[2rem] transition-opacity duration-500"></div>

                    <div class="h-full bg-white rounded-[1.8rem] overflow-hidden flex flex-col relative z-10">
                        <!-- Header -->
                        <div class="p-6 md:p-8 pb-4 flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="bg-{{ $rank['color'] }}-50 text-{{ $rank['color'] }}-600 p-2 rounded-xl">
                                        <i data-lucide="{{ $rank['icon'] }}" class="w-5 h-5"></i>
                                    </span>
                                    <span class="text-xs font-bold text-{{ $rank['color'] }}-600 tracking-wider uppercase bg-{{ $rank['color'] }}-50 px-2 py-1 rounded-md">{{ $rank['subtitle'] }}</span>
                                </div>
                                <h3 class="text-xl md:text-2xl font-bold text-slate-800">{{ $rank['title'] }}</h3>
                                <p class="text-sm text-slate-400 mt-1">{{ $rank['desc'] }}</p>
                            </div>
                            <!-- Top 1 Avatar -->
                            @if(isset($rank['data'][0]))
                                @php $top1 = $rank['data'][0];
                                $emp1 = $top1->employee ?? $top1->user; @endphp
                                <div class="relative hidden sm:block">
                                    <div class="absolute inset-0 bg-gradient-to-br {{ $rank['gradient'] }} blur-md opacity-40 rounded-full scale-110"></div>
                                    <div class="w-16 h-16 rounded-full p-1 bg-gradient-to-br {{ $rank['gradient'] }}">
                                        <div class="w-full h-full rounded-full bg-white overflow-hidden">
                                            @if($emp1 && isset($emp1->avatar) && $emp1->avatar)
                                                <img src="{{ asset('storage/' . $emp1->avatar) }}" class="w-full h-full object-cover" alt="Top 1">
                                            @else
                                                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xl">{{ substr($emp1->first_name ?? $emp1->name ?? '?', 0, 1) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 bg-gradient-to-r from-amber-300 to-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm border border-white">
                                        #1
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- List -->
                        <div class="flex-1 p-2 md:p-4">
                            <div class="space-y-1">
                                @forelse($rank['data'] as $index => $item)
                                    @php 
                                                                    $emp = $item->employee ?? $item->user;
                                        $val = $item->avg_sec ?? $item->count ?? $item->total_days ?? $item->avg_days ?? $item->types_count ?? 0;
                                        $isTop3 = $index < 3;
                                    @endphp
                                    <div class="flex items-center gap-3 p-3 rounded-2xl hover:bg-slate-50 transition-colors group/item {{ $index == 0 ? 'bg-gradient-to-r from-amber-50/50 to-transparent' : '' }}">
                                        <!-- Rank Badge -->
                                        <div class="w-8 h-8 flex-shrink-0 flex items-center justify-center font-black text-sm rounded-xl
                                            {{ $index == 0 ? 'text-amber-600 bg-amber-100' : '' }}
                                            {{ $index == 1 ? 'text-slate-600 bg-slate-100' : '' }}
                                            {{ $index == 2 ? 'text-orange-600 bg-orange-100' : '' }}
                                            {{ $index > 2 ? 'text-slate-400 bg-transparent' : '' }}
                                        ">
                                            {{ $index + 1 }}
                                        </div>

                                        <!-- Avatar (Small) -->
                                        <div class="w-10 h-10 rounded-full bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-100">
                                             @if($emp && isset($emp->avatar) && $emp->avatar)
                                                <img src="{{ asset('storage/' . $emp->avatar) }}" class="w-full h-full object-cover">
                                            @elseif($emp && isset($emp->photo_path) && $emp->photo_path)
                                                <img src="{{ asset('storage/' . $emp->photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-bold text-slate-400">
                                                    {{ substr($emp->first_name ?? $emp->name ?? '?', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-800 truncate group-hover/item:text-{{ $rank['color'] }}-600 transition-colors">
                                                {{ $emp->rank ?? '' }} {{ $emp->first_name ?? $emp->name ?? 'Unknown' }} {{ $emp->last_name ?? '' }}
                                            </p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate">
                                                {{ $emp->department ?? '-' }}
                                            </p>
                                        </div>

                                        <!-- Value -->
                                        <div class="text-right">
                                            <span class="block text-sm font-black text-slate-800">
                                                @if($rank['is_time'])
                                                    {{ gmdate("H:i", $val) }}
                                                @elseif(is_float($val) || strpos($val, '.') !== false)
                                                    {{ number_format($val, 1) }}
                                                @else
                                                    {{ $val }}
                                                @endif
                                                <span class="text-xs font-medium text-slate-400 ml-0.5">{{ $rank['unit'] }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center">
                                        <p class="text-sm text-slate-400">ไม่มีข้อมูลในปีนี้</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Shame Zone (Design separate) -->
            <div class="xl:col-span-2 mt-8">
                <div class="relative bg-slate-900 rounded-[2.5rem] p-1 shadow-2xl overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20"></div>

                    <div class="relative z-10 p-8 md:p-12 text-center">
                        <div class="inline-flex items-center gap-2 mb-4 px-4 py-1.5 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300">
                            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            <span class="text-xs font-bold uppercase tracking-widest">Needs Improvement</span>
                        </div>
                        <h2 class="text-3xl font-black text-white mb-2">มาสายยอดนิยม (ต้องปรับปรุง)</h2>
                        <p class="text-slate-400 mb-10 max-w-2xl mx-auto">สถิติเหล่านี้มีไว้เพื่อการตระหนักรู้และปรับปรุงตนเอง การตรงต่อเวลาคือหัวใจสำคัญของข้าราชการ</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                            @foreach($mostLate as $index => $item)
                                <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/10 flex flex-col items-center hover:bg-white/10 transition-colors">
                                    <div class="w-20 h-20 rounded-full bg-rose-500/10 mb-4 p-1 relative">
                                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-rose-500 rounded-full flex items-center justify-center font-bold text-white shadow-lg">
                                            {{ $index + 1 }}
                                        </div>
                                         <div class="w-full h-full rounded-full bg-slate-800 overflow-hidden">
                                            @if($item->employee && $item->employee->photo_path)
                                                <img src="{{ asset('storage/' . $item->employee->photo_path) }}" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity">
                                            @else
                                                 <div class="w-full h-full flex items-center justify-center text-rose-300 font-bold text-2xl">
                                                    {{ substr($item->employee->first_name ?? '?', 0, 1) }}
                                                 </div>
                                            @endif
                                        </div>
                                    </div>
                                    <h3 class="text-white font-bold text-lg truncate w-full">{{ $item->employee->first_name ?? 'Unknown' }}</h3>
                                    <p class="text-rose-400 text-xs uppercase font-bold tracking-widest mb-2">{{ $item->employee->department ?? '' }}</p>
                                    <div class="mt-auto px-4 py-1 rounded-lg bg-rose-500/20 text-rose-200 font-bold text-sm">
                                        สาย {{ $item->late_count }} ครั้ง
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
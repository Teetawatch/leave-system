<x-app-layout>
    @section('title', 'ตั้งค่าระบบ (System Settings)')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
                            radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.75);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
            }

            @keyframes slide-up {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }

            .setting-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .setting-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08);
            }

            .leave-type-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .leave-type-card:hover {
                background: rgba(248, 250, 252, 0.8);
            }

            /* Toggle Switch */
            .toggle-switch {
                position: relative;
                display: inline-block;
                width: 48px;
                height: 26px;
                flex-shrink: 0;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .toggle-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #e2e8f0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 26px;
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
            }

            .toggle-slider:before {
                position: absolute;
                content: "";
                height: 20px;
                width: 20px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 50%;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }

            .toggle-switch input:checked + .toggle-slider {
                background: linear-gradient(135deg, #6366f1, #4f46e5);
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(99, 102, 241, 0.3);
            }

            .toggle-switch input:checked + .toggle-slider:before {
                transform: translateX(22px);
            }

            .toggle-switch input:focus + .toggle-slider {
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            }

            /* Toggle Switch for Danger */
            .toggle-switch input.toggle-emerald:checked + .toggle-slider {
                background: linear-gradient(135deg, #10b981, #059669);
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(16, 185, 129, 0.3);
            }

            .toggle-switch input.toggle-amber:checked + .toggle-slider {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(245, 158, 11, 0.3);
            }

            .toggle-switch input.toggle-rose:checked + .toggle-slider {
                background: linear-gradient(135deg, #f43f5e, #e11d48);
                box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(244, 63, 94, 0.3);
            }

            .number-input {
                -moz-appearance: textfield;
            }

            .number-input::-webkit-outer-spin-button,
            .number-input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .rule-row {
                transition: all 0.3s ease;
            }

            .rule-row.disabled {
                opacity: 0.45;
                pointer-events: none;
            }

            @keyframes success-pop {
                0% { transform: scale(0.95); opacity: 0; }
                50% { transform: scale(1.02); }
                100% { transform: scale(1); opacity: 1; }
            }

            .success-toast {
                animation: success-pop 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-violet-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

        <!-- Success Toast -->
        @if(session('success'))
            <div class="fixed top-6 right-6 z-50 success-toast" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4">
                <div class="flex items-center gap-3 bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-2xl shadow-emerald-600/30">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-2 text-white/60 hover:text-white transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Cinematic Header -->
        <div class="relative pt-16 pb-32 animate-slide-up">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                            ตั้งค่าระบบ
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                            การตั้งค่า <span class="text-indigo-600">ระบบ</span>
                        </h1>
                        <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">
                            กำหนดค่าพารามิเตอร์ของระบบ ปรับแต่งเงื่อนไขประเภทการลา<br class="hidden md:block">
                            เปิด/ปิด กฎเกณฑ์ต่างๆ ได้จากส่วนนี้
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-8">

                @php
                    $colors = ['indigo', 'emerald', 'amber', 'rose', 'violet', 'cyan', 'orange', 'teal'];
                    $icons = [
                        'sick' => 'thermometer',
                        'personal' => 'briefcase',
                        'vacation' => 'palm-tree',
                        'temporary' => 'clock',
                        'official-duty' => 'landmark',
                    ];
                @endphp

                @foreach($leaveTypes as $index => $type)
                    @php
                        $color = $colors[$index % count($colors)];
                        $icon = $icons[$type->slug] ?? 'file-text';
                    @endphp

                    <div class="glass-panel rounded-[2rem] overflow-hidden animate-slide-up setting-card leave-type-card" style="animation-delay: {{ ($index + 1) * 0.08 }}s"
                         x-data="{
                            requiresAdvance: {{ $type->requires_advance_notice ? 'true' : 'false' }},
                            enforceAdvance: {{ $type->enforce_advance_notice ? 'true' : 'false' }},
                            allowsRetro: {{ $type->allows_retroactive ? 'true' : 'false' }},
                            enforceRetro: {{ $type->enforce_retroactive_check ? 'true' : 'false' }},
                            enforceBalance: {{ $type->enforce_balance_check ? 'true' : 'false' }},
                            requiresFile: {{ $type->requires_file ? 'true' : 'false' }},
                            expanded: false
                         }">

                        <!-- Card Header -->
                        <div class="bg-slate-900 px-6 sm:px-10 py-6 flex items-center gap-4 sm:gap-6 cursor-pointer" @click="expanded = !expanded">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-{{ $color }}-500/20 text-{{ $color }}-400 flex items-center justify-center border border-{{ $color }}-500/20 shadow-inner flex-shrink-0">
                                <i data-lucide="{{ $icon }}" class="w-6 h-6 sm:w-7 sm:h-7"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg sm:text-xl font-black text-white tracking-tight">{{ $type->name }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="text-[10px] font-black text-slate-500 tracking-[0.15em] uppercase">{{ $type->slug }}</span>
                                    <span class="text-slate-700">•</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          :class="enforceAdvance && requiresAdvance ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-700 text-slate-500'">
                                        <span x-text="enforceAdvance && requiresAdvance ? 'ล่วงหน้า ✓' : 'ล่วงหน้า ✗'"></span>
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          :class="enforceRetro ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-700 text-slate-500'">
                                        <span x-text="enforceRetro ? 'ย้อนหลัง ✓' : 'ย้อนหลัง ✗'"></span>
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                          :class="enforceBalance ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-500'">
                                        <span x-text="enforceBalance ? 'วันลา ✓' : 'วันลา ✗'"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <div class="text-right hidden sm:block">
                                    <div class="text-3xl font-black text-white">{{ $type->max_days_per_year }}</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">วัน/ปี</div>
                                </div>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-500 transition-transform duration-300" :class="expanded && 'rotate-180'"></i>
                            </div>
                        </div>

                        <!-- Card Body (Expandable) -->
                        <div x-show="expanded" x-collapse x-cloak>
                            <div class="p-6 sm:p-10 space-y-8">

                                <!-- Section 1: Basic Settings -->
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-8 h-8 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600 flex items-center justify-center">
                                            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">การตั้งค่าพื้นฐาน</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">จำนวนวันลาสูงสุด/ปี</label>
                                            <div class="flex items-center gap-3">
                                                <input type="number" name="leave_types[{{ $type->id }}][max_days]" value="{{ $type->max_days_per_year }}"
                                                       class="number-input block w-full rounded-xl border-slate-200 bg-white shadow-sm focus:border-{{ $color }}-500 focus:ring-4 focus:ring-{{ $color }}-500/10 text-center font-black text-2xl text-slate-800 py-3 transition-all" min="0">
                                                <span class="text-xs font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">วัน</span>
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">ต้องแนบไฟล์</label>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-sm font-bold" :class="requiresFile ? 'text-{{ $color }}-600' : 'text-slate-400'" x-text="requiresFile ? 'ต้องแนบ' : 'ไม่ต้องแนบ'"></span>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="leave_types[{{ $type->id }}][requires_file]" x-model="requiresFile" :checked="requiresFile">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                                <!-- Section 2: Advance Notice Rules -->
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                            <i data-lucide="calendar-clock" class="w-4 h-4"></i>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">เงื่อนไขการลาล่วงหน้า</h4>
                                    </div>

                                    <div class="space-y-4">
                                        <!-- Toggle: Requires Advance Notice -->
                                        <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100 rule-row">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800 text-sm">กำหนดให้ต้องลาล่วงหน้า</div>
                                                    <div class="text-xs text-slate-400 mt-0.5">เปิดใช้งานเงื่อนไขการยื่นล่วงหน้า</div>
                                                </div>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" name="leave_types[{{ $type->id }}][requires_advance_notice]" x-model="requiresAdvance" :checked="requiresAdvance">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>

                                        <!-- Number: Advance Notice Days -->
                                        <div class="rule-row" :class="!requiresAdvance && 'disabled'">
                                            <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center">
                                                        <i data-lucide="hash" class="w-5 h-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-sm">จำนวนวันล่วงหน้า</div>
                                                        <div class="text-xs text-slate-400 mt-0.5">ต้องยื่นก่อนวันลากี่วัน</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="number" name="leave_types[{{ $type->id }}][advance_notice_days]" value="{{ $type->advance_notice_days }}"
                                                           class="number-input w-20 rounded-xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-center font-black text-xl text-slate-800 py-2 transition-all" min="0">
                                                    <span class="text-xs font-bold text-slate-400">วัน</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Toggle: Enforce Advance Notice -->
                                        <div class="rule-row" :class="!requiresAdvance && 'disabled'">
                                            <div class="flex items-center justify-between bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100/50">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-sm">บังคับใช้เงื่อนไขล่วงหน้า</div>
                                                        <div class="text-xs text-slate-400 mt-0.5">ปิด = อนุญาตให้ลาได้โดยไม่ต้องล่วงหน้าตามกำหนด</div>
                                                    </div>
                                                </div>
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="leave_types[{{ $type->id }}][enforce_advance_notice]" x-model="enforceAdvance" :checked="enforceAdvance">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                                <!-- Section 3: Retroactive Rules -->
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                            <i data-lucide="history" class="w-4 h-4"></i>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">เงื่อนไขการลาย้อนหลัง</h4>
                                    </div>

                                    <div class="space-y-4">
                                        <!-- Toggle: Allows Retroactive -->
                                        <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100 rule-row">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                                    <i data-lucide="undo-2" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800 text-sm">อนุญาตให้ลาย้อนหลัง</div>
                                                    <div class="text-xs text-slate-400 mt-0.5">เปิด = สามารถยื่นลาย้อนหลังได้</div>
                                                </div>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" class="toggle-amber" name="leave_types[{{ $type->id }}][allows_retroactive]" x-model="allowsRetro" :checked="allowsRetro">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>

                                        <!-- Number: Max Retroactive Days -->
                                        <div class="rule-row" :class="!allowsRetro && 'disabled'">
                                            <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                                                        <i data-lucide="hash" class="w-5 h-5"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold text-slate-800 text-sm">ย้อนหลังได้สูงสุด</div>
                                                        <div class="text-xs text-slate-400 mt-0.5">จำนวนวันที่อนุญาตให้ย้อนหลังได้</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input type="number" name="leave_types[{{ $type->id }}][max_retroactive_days]" value="{{ $type->max_retroactive_days ?? 7 }}"
                                                           class="number-input w-20 rounded-xl border-slate-200 bg-white shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 text-center font-black text-xl text-slate-800 py-2 transition-all" min="0">
                                                    <span class="text-xs font-bold text-slate-400">วัน</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Toggle: Enforce Retroactive Check -->
                                        <div class="flex items-center justify-between bg-amber-50/50 rounded-2xl p-5 border border-amber-100/50 rule-row">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-800 text-sm">บังคับใช้เงื่อนไขย้อนหลัง</div>
                                                    <div class="text-xs text-slate-400 mt-0.5">ปิด = ไม่ตรวจสอบเงื่อนไขย้อนหลังทั้งหมด</div>
                                                </div>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" class="toggle-amber" name="leave_types[{{ $type->id }}][enforce_retroactive_check]" x-model="enforceRetro" :checked="enforceRetro">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                                <!-- Section 4: Balance Check -->
                                <div>
                                    <div class="flex items-center gap-3 mb-6">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                            <i data-lucide="calculator" class="w-4 h-4"></i>
                                        </div>
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">เงื่อนไขตรวจสอบวันลาคงเหลือ</h4>
                                    </div>

                                    <div class="flex items-center justify-between bg-emerald-50/50 rounded-2xl p-5 border border-emerald-100/50 rule-row">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 text-sm">บังคับตรวจสอบวันลาคงเหลือ</div>
                                                <div class="text-xs text-slate-400 mt-0.5">ปิด = อนุญาตให้ลาได้แม้วันลาไม่เพียงพอ</div>
                                            </div>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" class="toggle-emerald" name="leave_types[{{ $type->id }}][enforce_balance_check]" x-model="enforceBalance" :checked="enforceBalance">
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Submit Button -->
                <div class="pt-6 flex justify-end animate-slide-up" style="animation-delay: 0.5s">
                    <button type="submit" id="saveSettingsBtn" class="group inline-flex items-center justify-center px-10 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-[2rem] shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1 active:scale-95 uppercase tracking-widest gap-3">
                        <i data-lucide="save" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                        บันทึกการตั้งค่าทั้งหมด
                    </button>
                </div>

            </div>
        </form>

        <!-- Telegram Bot Settings -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 relative z-20">
            <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up setting-card" style="animation-delay: 0.55s">
                <div class="bg-slate-900 px-6 sm:px-10 py-6 flex items-center gap-4 sm:gap-6">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center border border-sky-500/20 shadow-inner flex-shrink-0">
                        <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg sm:text-xl font-black text-white tracking-tight">Telegram Bot แจ้งเตือน</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="text-[10px] font-black text-slate-500 tracking-[0.15em] uppercase">การตั้งค่า Telegram Bot</span>
                            <span class="text-slate-700">•</span>
                            @if(config('services.telegram.bot_token'))
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400">เชื่อมต่อแล้ว ✓</span>
                            @else
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400">ยังไม่ได้ตั้งค่า ✗</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-10 space-y-6">
                    <!-- Status Overview -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 text-center">
                            <div class="text-3xl font-black text-sky-600">{{ \App\Models\User::whereNotNull('telegram_chat_id')->count() }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ผู้ใช้เชื่อมต่อ Telegram</div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 text-center">
                            <div class="text-3xl font-black text-indigo-600">{{ \App\Models\User::whereNotNull('telegram_chat_id')->whereIn('role', ['admin', 'director', 'deputy_director', 'department_head'])->count() }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ผู้อนุมัติเชื่อมต่อ</div>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 text-center">
                            @if(config('services.telegram.bot_token'))
                                <div class="text-xl font-black text-emerald-500 flex items-center justify-center gap-1.5 mt-1"><i data-lucide="check-circle-2" class="w-6 h-6"></i> พร้อมใช้งาน</div>
                            @else
                                <div class="text-xl font-black text-rose-400 flex items-center justify-center gap-1.5 mt-1"><i data-lucide="alert-circle" class="w-6 h-6"></i> ยังไม่ตั้งค่า</div>
                            @endif
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">สถานะ Bot</div>
                        </div>
                    </div>

                    <!-- Setup Instructions -->
                    <div class="bg-sky-50/50 rounded-2xl p-6 border border-sky-100/50 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                            </div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">วิธีตั้งค่า Telegram Bot</h4>
                        </div>
                        <ol class="space-y-3 text-sm text-slate-600 font-medium">
                            <li class="flex gap-3">
                                <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">1</span>
                                <span>สร้าง Bot ใหม่ผ่าน <a href="https://t.me/BotFather" target="_blank" class="text-sky-600 font-bold underline underline-offset-2">@BotFather</a> บน Telegram → ได้รับ Bot Token</span>
                            </li>
                            <li class="flex gap-3">
                                <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">2</span>
                                <span>เพิ่มค่าใน <code class="px-2 py-0.5 bg-white rounded-lg text-xs font-bold border border-slate-200">.env</code> :<br>
                                    <code class="inline-block mt-1 px-3 py-1.5 bg-slate-800 text-emerald-400 rounded-lg text-xs font-bold">TELEGRAM_BOT_TOKEN=your_bot_token_here</code><br>
                                    <code class="inline-block mt-1 px-3 py-1.5 bg-slate-800 text-emerald-400 rounded-lg text-xs font-bold">TELEGRAM_BOT_USERNAME=YourBotUsername</code><br>
                                    <code class="inline-block mt-1 px-3 py-1.5 bg-slate-800 text-amber-400 rounded-lg text-xs font-bold">TELEGRAM_WEBHOOK_SECRET=your_random_secret_here</code>
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">3</span>
                                <span>ตั้ง Webhook โดยรันคำสั่ง: <code class="px-3 py-1.5 bg-slate-800 text-emerald-400 rounded-lg text-xs font-bold">php artisan telegram:set-webhook</code></span>
                            </li>
                            <li class="flex gap-3">
                                <span class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xs font-black flex-shrink-0 mt-0.5">4</span>
                                <span>ตั้ง Cron Job (Shared Hosting) สำหรับสรุปประจำวัน:<br>
                                    <code class="inline-block mt-1 px-3 py-1.5 bg-slate-800 text-amber-400 rounded-lg text-xs font-bold break-all">GET /telegram-daily-summary/{secret} ← ทุกวัน 07:00</code><br>
                                    <code class="inline-block mt-1 px-3 py-1.5 bg-slate-800 text-amber-400 rounded-lg text-xs font-bold break-all">GET /telegram-duty-roster/{secret} ← ทุกวัน 07:00</code>
                                </span>
                            </li>
                        </ol>
                    </div>

                    <!-- Features List -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="bell-ring" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">แจ้งเตือนอัตโนมัติ</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">ส่งแจ้งเตือนเมื่อมีใบลาใหม่ / สถานะเปลี่ยน</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="mouse-pointer-click" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Quick Reply อนุมัติ/ปฏิเสธ</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">กดปุ่มอนุมัติ/ไม่อนุมัติจาก Telegram ได้ทันที</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Daily Summary</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">สรุปการลาประจำวันส่งให้ผู้บริหารทุกเช้า</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-8 h-8 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="shield" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Duty Roster แจ้งเตือน</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">แจ้งเตือนผู้อยู่เวรวันนี้ + พรุ่งนี้ทุกเช้า</p>
                            </div>
                        </div>
                    </div>

                    <!-- Linked Users Table -->
                    @php
                        $linkedUsers = \App\Models\User::whereNotNull('telegram_chat_id')->orderBy('name')->get();
                    @endphp
                    @if($linkedUsers->isNotEmpty())
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                                    <i data-lucide="users" class="w-4 h-4"></i>
                                </div>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">ผู้ใช้ที่เชื่อมต่อ Telegram ({{ $linkedUsers->count() }})</h4>
                            </div>
                            <div class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-slate-100/50">
                                            <th class="text-left px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ชื่อ</th>
                                            <th class="text-left px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">ตำแหน่ง</th>
                                            <th class="text-left px-4 py-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Chat ID</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($linkedUsers as $lu)
                                            <tr class="hover:bg-white/80 transition-colors">
                                                <td class="px-4 py-3 font-bold text-slate-700">{{ $lu->rank }} {{ $lu->name }}</td>
                                                <td class="px-4 py-3 text-slate-500 text-xs">{{ $lu->role }}</td>
                                                <td class="px-4 py-3 text-slate-400 font-mono text-xs">{{ $lu->telegram_chat_id }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Organization Settings (Coming Soon) -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 relative z-20">
            <div class="glass-panel rounded-[3rem] overflow-hidden animate-slide-up setting-card" style="animation-delay: 0.6s">
                <div class="p-10 flex flex-col items-center text-center py-20 relative overflow-hidden group">
                    <div class="absolute inset-0 opacity-[0.02]" style="background-image: url('data:image/svg+xml,%3Csvg width=%2260%22 height=%2260%22 viewBox=%220 0 60 60%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cg fill=%22none%22 fill-rule=%22evenodd%22%3E%3Cg fill=%22%23000000%22 fill-opacity=%221%22%3E%3Cpath d=%22M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%22/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    <div class="relative z-10">
                        <div class="w-24 h-24 rounded-[2rem] bg-slate-100 flex items-center justify-center mx-auto mb-8 shadow-inner border border-slate-200 group-hover:scale-110 transition-transform duration-500">
                            <i data-lucide="building-2" class="w-12 h-12 text-slate-300"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-800 mb-3 tracking-tight">ตั้งค่าองค์กร</h3>
                        <p class="text-slate-400 font-bold text-lg max-w-sm mx-auto mb-8 leading-relaxed">
                            ข้อมูลบริษัทและวันหยุดประจำปี
                        </p>
                        <div class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-400 rounded-full text-[11px] font-black uppercase tracking-widest border border-slate-200">
                            <i data-lucide="construction" class="w-4 h-4"></i>
                            Coming Soon
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual End -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 flex flex-col items-center justify-center gap-6 opacity-30">
            <div class="w-1 bg-gradient-to-b from-indigo-500 to-transparent h-20 rounded-full"></div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.5em]">สิ้นสุดหน้าตั้งค่าระบบ</div>
        </div>
    </div>
</x-app-layout>

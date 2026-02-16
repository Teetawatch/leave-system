<x-app-layout>
    @section('title', 'ยื่นคำขอลาปฏิบัติราชการ')

    @push('styles')
        <style>
            .premium-bg {
                min-height: 100vh;
                background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
                    radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            }

            .step-active {
                background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
                box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            }

            .type-card-active {
                background: white;
                border-color: #4f46e5;
                box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1);
                transform: translateY(-4px) scale(1.02);
            }

            .premium-input {
                background: rgba(255, 255, 255, 0.6);
                border: 1px solid rgba(226, 232, 240, 0.8);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .premium-input:focus {
                background: white;
                border-color: #4338ca;
                box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            }

            @keyframes slide-up {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-slide-up {
                animation: slide-up 0.5s ease-out forwards;
            }

            .ticket-gradient {
                background: linear-gradient(165deg, #1e293b 0%, #0f172a 100%);
            }
        </style>
    @endpush

    <div class="premium-bg -m-4 md:-m-8 pb-20 relative overflow-hidden" x-data="leaveFormApp()">

        <!-- Background Decorations -->
        <div
            class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-100/30 rounded-full blur-[100px] -ml-48 -mb-48">
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">

            <!-- Header Section -->
            <div class="flex flex-col items-center text-center mb-16 space-y-6 animate-slide-up">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] shadow-sm border border-indigo-100">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    ระบบบริหารจัดการงานกำลังพล
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none">
                    แบบฟอร์มยื่นคำขอลา
                </h1>
                <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">
                    ยื่นคำขอลาออนไลน์ผ่านระบบบริหารจัดการกำลังพลแบบเรียลไทม์<br class="hidden md:block">
                    รวดเร็ว โปร่งใส และตรวจสอบสถานะได้ทันที
                </p>

                <!-- Modern Stepper UI -->
                <div
                    class="flex items-center justify-center gap-4 mt-10 w-full max-w-3xl overflow-x-auto pb-4 no-scrollbar">
                    <template x-for="(step, index) in steps">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3 group">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-xl transition-all duration-500"
                                    :class="currentStep >= index + 1 ? 'step-active text-white' : 'bg-white text-slate-300 border border-slate-100'">
                                    <span x-text="index + 1" x-show="!isStepComplete(index + 1)"></span>
                                    <i data-lucide="check" class="w-6 h-6" x-show="isStepComplete(index + 1)"></i>
                                </div>
                                <span class="hidden md:block font-bold text-base uppercase tracking-wider"
                                    :class="currentStep >= index + 1 ? 'text-slate-800' : 'text-slate-300'"
                                    x-text="step.name"></span>
                            </div>
                            <div x-show="index < steps.length - 1"
                                class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 transition-all duration-700"
                                    :style="'width: ' + (currentStep > index + 1 ? '100%' : '0%')"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <form action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm"
                class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                @csrf

                <!-- Left Content Area -->
                <div class="lg:col-span-8 space-y-10">

                    <!-- STEP 1: Type Selection -->
                    <section
                        class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-2xl shadow-indigo-500/5 border-indigo-50/50"
                        x-show="currentStep >= 1" x-transition>
                        <div class="flex items-center gap-6 mb-12">
                            <div
                                class="w-16 h-16 rounded-[2rem] bg-slate-900 text-white flex items-center justify-center shadow-xl rotate-3 flex-shrink-0">
                                <i data-lucide="layers" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black text-slate-900 tracking-tight">1. เลือกประเภทการลา</h3>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">
                                    ระบุวัตถุประสงค์หลักของการลาครั้งนี้</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($leaveTypes as $type)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="leave_type_id" value="{{ $type->id }}" class="peer sr-only"
                                        x-model="leaveType">
                                    <div
                                        class="h-full p-8 rounded-[2.5rem] border-2 border-slate-50 bg-white/40 backdrop-blur-md text-center transition-all duration-300 peer-checked:type-card-active hover:border-indigo-100 hover:bg-white/80">
                                        <div
                                            class="w-20 h-20 mx-auto rounded-[2rem] flex items-center justify-center text-4xl mb-6 shadow-inner transition-all duration-500 group-hover:scale-110 group-hover:-rotate-6
                                                                                                                        {{ $type->slug == 'vacation' ? 'bg-blue-50 text-blue-500' : ($type->slug == 'sick' ? 'bg-rose-50 text-rose-500' : ($type->slug == 'temporary' ? 'bg-purple-50 text-purple-500' : 'bg-amber-50 text-amber-500')) }}">
                                            @if($type->slug == 'vacation') <i data-lucide="palmtree" class="w-10 h-10"></i>
                                            @elseif($type->slug == 'sick') <i data-lucide="thermometer"
                                                class="w-10 h-10"></i>
                                            @elseif($type->slug == 'temporary') <i data-lucide="clock"
                                                class="w-10 h-10"></i>
                                            @else <i data-lucide="briefcase" class="w-10 h-10"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-lg font-black text-slate-900 tracking-tight">{{ $type->name }}
                                        </h4>
                                        <div
                                            class="absolute top-5 right-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-all transform scale-50 peer-checked:scale-100">
                                            <div class="p-1 bg-indigo-50 rounded-full">
                                                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </section>

                    <!-- STEP 2: Logic Driven Sections (Dynamic) -->
                    <div x-show="leaveType" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 translate-y-10">

                        <!-- Address for Sick Leave -->
                        <section x-show="isSick"
                            class="glass-panel rounded-[3.5rem] p-8 md:p-12 mb-10 shadow-xl border-rose-50/50">
                            <div class="flex items-center gap-6 mb-12">
                                <div
                                    class="w-16 h-16 rounded-[2rem] bg-rose-500 text-white flex items-center justify-center shadow-xl -rotate-3 flex-shrink-0">
                                    <i data-lucide="map-pin" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight text-rose-500">
                                        ที่อยู่ที่สามารถติดต่อได้</h3>
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">
                                        กรณีฉุกเฉินหรือต้องการแจ้งผลการตรวจ</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">บ้านเลขที่</label>
                                    <input type="text" name="addr_house"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="123/45...">
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">ถนน</label>
                                    <input type="text" name="addr_road"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="ถนนพลาธิการ...">
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">ตำบล
                                        / แขวง</label>
                                    <input type="text" name="addr_tambon"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="ระบุตำบล...">
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">อำเภอ
                                        / เขต</label>
                                    <input type="text" name="addr_amphoe"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="ระบุอำเภอ...">
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">จังหวัด</label>
                                    <input type="text" name="addr_province" list="provinces"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="ระบุจังหวัด...">
                                </div>
                            </div>
                        </section>

                        <!-- Address for Personal Leave -->
                        <section x-show="isPersonal"
                            class="glass-panel rounded-[3.5rem] p-8 md:p-12 mb-10 shadow-xl border-amber-50/50">
                            <div class="flex items-center gap-6 mb-12">
                                <div
                                    class="w-16 h-16 rounded-[2rem] bg-amber-500 text-white flex items-center justify-center shadow-xl rotate-2 flex-shrink-0">
                                    <i data-lucide="map-pin" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight text-amber-500">
                                        สถานที่ติดต่อระหว่างลา</h3>
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">
                                        ระบุสถานที่ที่สามารถติดต่อได้กรณีเร่งด่วน</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">สถานที่
                                        / บ้านเลขที่</label>
                                    <input type="text" name="personal_location"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="เช่น บ้านพักต่างจังหวัด...">
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">จังหวัด</label>
                                    <input type="text" name="personal_province" list="provinces"
                                        class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 transition-all text-lg"
                                        placeholder="ระบุจังหวัด...">
                                </div>
                            </div>
                        </section>

                        <!-- Date Range Selection -->
                        <section class="glass-panel rounded-[3.5rem] p-8 md:p-12 mb-10 shadow-xl border-indigo-50/50">
                            <div class="flex items-center gap-6 mb-12">
                                <div
                                    class="w-16 h-16 rounded-[2rem] bg-indigo-600 text-white flex items-center justify-center shadow-xl rotate-3 flex-shrink-0">
                                    <i data-lucide="calendar-days" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">2. กำหนดช่วงเวลาการลา
                                    </h3>
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2"
                                        x-text="isTemporary ? 'ระบุวันและช่วงเวลาที่ต้องการลา' : 'เลือกวันเริ่มต้นถึงวันสิ้นสุดการปฏิบัติราชการ'">
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row items-center gap-8">
                                <!-- Start Date -->
                                <div class="flex-1 w-full relative group">
                                    <label
                                        class="absolute -top-3 left-8 px-3 bg-white text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] rounded-full z-10 border border-indigo-100 shadow-sm">วันเริ่มต้น</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none text-slate-300 transition-colors group-focus-within:text-indigo-500">
                                            <i data-lucide="calendar" class="w-6 h-6"></i>
                                        </div>
                                        <input type="date" name="start_date" x-model="startDate" required
                                            :min="isSick ? '' : todayDate"
                                            class="w-full pl-20 pr-8 py-6 premium-input rounded-[2.5rem] font-black text-slate-900 text-xl shadow-inner">
                                    </div>
                                </div>

                                <div class="hidden md:flex flex-col items-center">
                                    <div
                                        class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 shadow-inner group-hover:text-indigo-500 transition-colors">
                                        <i data-lucide="arrow-right" class="w-7 h-7"></i>
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="flex-1 w-full relative group" x-show="!isTemporary">
                                    <label
                                        class="absolute -top-3 left-8 px-3 bg-white text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] rounded-full z-10 border border-indigo-100 shadow-sm">วันสิ้นสุด</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none text-slate-300 transition-colors group-focus-within:text-indigo-500">
                                            <i data-lucide="calendar-check-2" class="w-6 h-6"></i>
                                        </div>
                                        <input type="date" name="end_date" x-model="endDate" required :min="startDate"
                                            class="w-full pl-20 pr-8 py-6 premium-input rounded-[2.5rem] font-black text-slate-900 text-xl shadow-inner">
                                    </div>
                                </div>

                                <!-- Period Selection for Temporary -->
                                <div x-show="isTemporary"
                                    class="flex-1 w-full flex bg-slate-50 p-2 rounded-[2.5rem] border border-slate-100">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="temporary_leave_period" value="morning"
                                            class="sr-only peer" x-model="temporaryPeriod">
                                        <div
                                            class="py-4 text-center rounded-[2rem] font-black text-base peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-xl transition-all duration-300 text-slate-400">
                                            ช่วงเช้า
                                            <span class="block text-[10px] mt-1 opacity-70 font-bold">(ก่อน 07:30
                                                น.)</span>
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="temporary_leave_period" value="afternoon"
                                            class="sr-only peer" x-model="temporaryPeriod">
                                        <div
                                            class="py-4 text-center rounded-[2rem] font-black text-base peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-xl transition-all duration-300 text-slate-400">
                                            ช่วงบ่าย
                                            <span class="block text-[10px] mt-1 opacity-70 font-bold">(ก่อน 11:00
                                                น.)</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </section>

                        <!-- Details & Documents -->
                        <section class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-xl border-emerald-50/50">
                            <div class="flex items-center gap-6 mb-12">
                                <div
                                    class="w-16 h-16 rounded-[2rem] bg-emerald-500 text-white flex items-center justify-center shadow-xl -rotate-3 flex-shrink-0">
                                    <i data-lucide="file-text" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">3. รายละเอียดเพิ่มเติม
                                    </h3>
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">
                                        ระบุเหตุผลความจำเป็นและความประสงค์</p>
                                </div>
                            </div>

                            <div class="space-y-10">
                                <div class="relative">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 ml-1">เหตุผลหรือความจำเป็นในการลา
                                        <span class="text-rose-500 font-bold">*</span></label>
                                    <textarea name="reason" rows="4" required
                                        class="w-full px-8 py-6 premium-input rounded-[2.5rem] font-bold text-slate-900 text-xl resize-none shadow-inner"
                                        placeholder="ระบุเหตุผลการลา..."></textarea>
                                </div>

                                <div class="relative group">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 ml-1">เอกสารประกอบ
                                        (ถ้ามี)</label>
                                    <div class="file-drop relative rounded-[3rem] border-2 border-dashed border-slate-200 p-12 text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer group/upload"
                                        @dragover.prevent="$el.classList.add('bg-indigo-50', 'border-indigo-500')"
                                        @dragleave.prevent="$el.classList.remove('bg-indigo-50', 'border-indigo-500')"
                                        @drop.prevent="handleFileDrop($event)">
                                        <input type="file" name="attachment"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            id="fileInput" @change="handleFileChange($event)">
                                        <div class="flex flex-col items-center gap-6">
                                            <div
                                                class="w-24 h-24 rounded-[2.5rem] bg-white text-indigo-500 flex items-center justify-center group-hover/upload:scale-110 group-hover/upload:rotate-12 transition-all shadow-xl border border-indigo-50">
                                                <i data-lucide="upload-cloud" class="w-12 h-12"></i>
                                            </div>
                                            <div x-show="!fileName">
                                                <p class="text-2xl font-black text-slate-900">ลากไฟล์มาวาง หรือ
                                                    คลิกเพื่ออัปโหลด</p>
                                                <p
                                                    class="text-xs font-black text-slate-400 mt-3 uppercase tracking-[0.2em]">
                                                    PDF, JPG, PNG (ไม่เกิน 5MB)</p>
                                            </div>
                                            <div x-show="fileName" x-cloak
                                                class="flex items-center gap-4 bg-white px-8 py-4 rounded-[2rem] shadow-2xl border border-indigo-100 scale-105">
                                                <div
                                                    class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                                    <i data-lucide="file-check" class="w-6 h-6"></i>
                                                </div>
                                                <span class="text-lg font-black text-slate-800 truncate max-w-[250px]"
                                                    x-text="fileName"></span>
                                                <button type="button" @click.prevent="clearFile()"
                                                    class="p-2 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition-colors cursor-pointer">
                                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Action Button -->
                        <div class="pt-12 flex flex-col md:flex-row items-center gap-8">
                            <button type="submit"
                                class="w-full md:w-auto flex-1 py-7 bg-gradient-to-r from-indigo-600 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 text-white font-black text-2xl rounded-[3rem] shadow-[0_25px_60px_-15px_rgba(79,70,229,0.5)] hover:shadow-[0_35px_70px_-12px_rgba(79,70,229,0.6)] transition-all hover:-translate-y-2 active:scale-95 flex items-center justify-center gap-5 group cursor-pointer">
                                <i data-lucide="shield-check"
                                    class="w-8 h-8 group-hover:rotate-12 transition-transform"></i>
                                <span>ส่งคำขอยืนยันใบลา</span>
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="w-full md:w-auto px-12 py-7 bg-white text-slate-400 hover:text-rose-500 font-black text-xl rounded-[3rem] transition-all hover:bg-rose-50 border border-slate-100 text-center shadow-sm">
                                ยกเลิก
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Premium Summary Ticket -->
                <div class="lg:col-span-4 lg:sticky lg:top-24">
                    <div
                        class="glass-panel rounded-[3.5rem] p-8 pb-12 relative overflow-hidden ticket-gradient shadow-2xl border-slate-800">
                        <!-- Background Pattern -->
                        <div
                            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.05]">
                        </div>
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-full blur-3xl -mr-16 -mt-16">
                        </div>

                        <div class="relative z-10 space-y-10">
                            <div class="flex items-center justify-between">
                                <h4 class="text-2xl font-black text-white tracking-tight uppercase">สรุปรายการลา</h4>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-indigo-400 border border-white/10">
                                    <i data-lucide="award" class="w-8 h-8"></i>
                                </div>
                            </div>

                            <!-- Digital Ticket Look -->
                            <div
                                class="bg-white rounded-[3rem] p-8 shadow-2xl space-y-8 relative group overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>

                                <div class="space-y-8 relative z-10">
                                    <div class="flex items-center gap-5 border-b border-slate-50 pb-6">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black">
                                            <i data-lucide="tag" class="w-7 h-7"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                ประเภทการลา</p>
                                            <p class="text-lg font-black text-slate-900 leading-none mt-1.5"
                                                x-text="getLeaveTypeName() || 'รอเลือกประเภท...'"></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                วันเริ่มต้น</p>
                                            <p class="text-lg font-black text-slate-800 mt-2"
                                                x-text="formatDate(startDate) || '-'"></p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                                วันสิ้นสุด</p>
                                            <p class="text-lg font-black text-slate-800 mt-2"
                                                x-text="formatDate(endDate) || '-'"></p>
                                        </div>
                                    </div>

                                    <div
                                        class="pt-8 border-t border-dashed border-slate-200 flex items-center justify-between">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ยอดรวมการลาครั้งนี้</span>
                                            <div class="flex items-baseline gap-1 mt-2">
                                                <span class="text-5xl font-black text-indigo-600"
                                                    x-text="duration > 0 ? duration : 0"></span>
                                                <span class="text-xl font-black text-slate-400">วัน</span>
                                            </div>
                                        </div>
                                        <div class="w-16 h-16 rounded-full border-4 border-indigo-50 border-t-indigo-500 animate-spin"
                                            x-show="duration > 0 && !isTemporary"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Real-time Validation Message -->
                            <div class="bg-white/5 rounded-[2rem] p-6 border border-white/10">
                                <div class="flex items-start gap-5">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-500 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-lg font-black text-white">ข้อควรทราบ</p>
                                        <p class="text-[13px] font-medium text-slate-400 leading-relaxed">
                                            กรุณาตรวจสอบสิทธิ์วันลาคงเหลือของท่านให้ถูกต้องก่อนกดยันยัน
                                            เพื่อความรวดเร็วในการอนุมัติ
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Persistence Datalist (Provinces) -->
    <datalist id="provinces">
        @foreach(['กรุงเทพมหานคร', 'ระยอง', 'ชลบุรี', 'จันทบุรี', 'ตราด', 'เชียงใหม่', 'เชียงราย', 'ภูเก็ต', 'สงขลา', 'นครราชสีมา', 'ขอนแก่น'] as $province)
            <option value="{{ $province }}">
        @endforeach
    </datalist>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('leaveFormApp', () => ({
                    currentStep: 1,
                    leaveType: '{{ old('leave_type_id') }}',
                    startDate: '{{ old('start_date') }}',
                    endDate: '{{ old('end_date') }}',
                    temporaryPeriod: '{{ old('temporary_leave_period', 'morning') }}',
                    todayDate: new Date().toLocaleDateString('en-CA'),
                    leaveTypes: @json($leaveTypes),
                    fileName: '',
                    steps: [
                        { name: 'เลือกประเภท' },
                        { name: 'ระบุวันลา' },
                        { name: 'กรอกเหตุผล' }
                    ],

                    init() {
                        this.$watch('leaveType', (val) => {
                            if (val) {
                                this.currentStep = 2;
                                if (this.isTemporary) {
                                    if (!this.startDate) {
                                        this.startDate = this.todayDate;
                                    }
                                    this.endDate = this.startDate;
                                }
                            }
                            this.$nextTick(() => window.lucide.createIcons());
                        });
                        this.$watch('startDate', (val) => {
                            if (this.isTemporary) {
                                this.endDate = val;
                            } else if (this.endDate && val > this.endDate) {
                                this.endDate = val;
                            }
                            this.updateStep();
                        });
                        this.$watch('endDate', (val) => {
                            if (!this.isTemporary && this.startDate && val < this.startDate) {
                                this.startDate = val;
                            }
                            this.updateStep();
                        });
                        this.updateStep();
                        window.lucide.createIcons();
                    },

                    updateStep() {
                        if (this.leaveType && this.startDate && this.endDate) {
                            this.currentStep = 3;
                        } else if (this.leaveType) {
                            this.currentStep = 2;
                        }
                        this.$nextTick(() => window.lucide.createIcons());
                    },

                    isStepComplete(stepNum) {
                        if (stepNum === 1) return !!this.leaveType;
                        if (stepNum === 2) return !!this.startDate && !!this.endDate;
                        return false;
                    },

                    get isSick() { return this.getSlug() === 'sick'; },
                    get isPersonal() { return this.getSlug() === 'personal'; },
                    get isTemporary() { return this.getSlug() === 'temporary'; },
                    getSlug() {
                        const type = this.leaveTypes.find(t => t.id == this.leaveType);
                        return type ? type.slug : null;
                    },

                    get duration() {
                        if (this.startDate && this.endDate) {
                            const start = new Date(this.startDate);
                            const end = new Date(this.endDate);
                            const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
                            return diffDays > 0 ? diffDays : 0;
                        }
                        return 0;
                    },

                    getLeaveTypeName() {
                        const type = this.leaveTypes.find(t => t.id == this.leaveType);
                        return type ? type.name : null;
                    },

                    formatDate(dateString) {
                        if (!dateString) return null;
                        return new Date(dateString).toLocaleDateString('th-TH', {
                            year: 'numeric', month: 'short', day: 'numeric'
                        });
                    },

                    handleFileChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            if (file.size > 5 * 1024 * 1024) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ขนาดไฟล์เกินกำหนด',
                                    text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB',
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonColor: '#ef4444'
                                });
                                this.clearFile();
                                return;
                            }
                            this.fileName = file.name;
                        } else {
                            this.fileName = '';
                        }
                    },
                    handleFileDrop(e) {
                        const file = e.dataTransfer.files[0];
                        if (file) {
                            if (file.size > 5 * 1024 * 1024) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'ขนาดไฟล์เกินกำหนด',
                                    text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB',
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonColor: '#ef4444'
                                });
                                return;
                            }
                            this.fileName = file.name;
                            document.getElementById('fileInput').files = e.dataTransfer.files;
                        }
                    },
                    clearFile() { this.fileName = ''; document.getElementById('fileInput').value = ''; }
                }));
            });
        </script>
    @endpush
</x-app-layout>
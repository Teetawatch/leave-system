<x-app-layout>
    @section('title', 'ยื่นคำขอลาปฏิบัติราชการ')

    @push('styles')
        <style>
            .premium-bg {
                background: radial-gradient(circle at top right, #f8fafc, #eff6ff);
                min-height: 100vh;
            }

            .glass-panel {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            }

            .step-active {
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            }

            .type-card-active {
                background: white;
                border-color: #4f46e5;
                box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1), 0 10px 10px -5px rgba(79, 70, 229, 0.04);
                transform: translateY(-4px);
            }

            @keyframes pulse-soft {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 0.5;
                }

                50% {
                    transform: scale(1.05);
                    opacity: 0.8;
                }
            }

            .glow-effect {
                position: absolute;
                width: 150px;
                height: 150px;
                background: radial-gradient(circle, rgba(79, 70, 229, 0.1) 0%, transparent 70%);
                pointer-events: none;
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
            <div class="flex flex-col items-center text-center mb-16 space-y-4">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-50 text-brand-600 text-sm font-bold uppercase tracking-widest shadow-sm">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    E-Leave System
                </div>
                <h1 class="text-5xl md:text-6xl font-bold text-slate-800 tracking-tight">แบบฟอร์มยื่นคำขอลา</h1>
                <p class="text-slate-500 font-semibold text-xl max-w-2xl">กรุณาเลือกประเภทและระบุรายละเอียดการลา
                    ระบบจะดำเนินการส่งเรื่องไปยังผู้อนุมัติตามลำดับสายงานอัตโนมัติ</p>

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
                                <div class="h-full bg-brand-500 transition-all duration-700"
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
                    <section class="glass-panel rounded-[3rem] p-8 md:p-12" x-show="currentStep >= 1" x-transition>
                        <div class="flex items-center gap-5 mb-10">
                            <div
                                class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-xl rotate-3">
                                <i data-lucide="layers" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 tracking-tight">1. เลือกประเภทการลา</h3>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    ระบุวัตถุประสงค์หลักของการลาครั้งนี้</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
                            @foreach($leaveTypes as $type)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="leave_type_id" value="{{ $type->id }}" class="peer sr-only"
                                        x-model="leaveType">
                                    <div
                                        class="h-full p-6 rounded-[2.5rem] border-2 border-slate-50 bg-white/50 backdrop-blur-md text-center transition-all duration-300 peer-checked:type-card-active group-hover:border-brand-200">
                                        <div
                                            class="w-16 h-16 mx-auto rounded-3xl flex items-center justify-center text-3xl mb-5 shadow-inner transition-transform group-hover:scale-110 group-active:scale-95
                                                                {{ $type->slug == 'vacation' ? 'bg-blue-50 text-blue-500' : ($type->slug == 'sick' ? 'bg-rose-50 text-rose-500' : ($type->slug == 'temporary' ? 'bg-purple-50 text-purple-500' : 'bg-amber-50 text-amber-500')) }}">
                                            @if($type->slug == 'vacation') <i data-lucide="palmtree" class="w-8 h-8"></i>
                                            @elseif($type->slug == 'sick') <i data-lucide="thermometer" class="w-8 h-8"></i>
                                            @elseif($type->slug == 'temporary') <i data-lucide="clock" class="w-8 h-8"></i>
                                            @else <i data-lucide="briefcase" class="w-8 h-8"></i>
                                            @endif
                                        </div>
                                        <h4 class="text-base font-bold text-slate-800 tracking-tight mb-1">{{ $type->name }}
                                        </h4>
                                        <div
                                            class="absolute top-4 right-4 text-brand-500 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
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
                        <section x-show="isSick" class="glass-panel rounded-[3rem] p-8 md:p-12 mb-10">
                            <div class="flex items-center gap-5 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-rose-500 text-white flex items-center justify-center shadow-lg -rotate-3">
                                    <i data-lucide="map-pin" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">
                                        ที่อยู่ที่สามารถติดต่อได้</h3>
                                    <p class="text-base font-semibold text-slate-400 uppercase tracking-widest mt-1">
                                        กรณีฉุกเฉินหรือต้องการแจ้งผลการตรวจ</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">บ้านเลขที่
                                        / ถนน / ซอย</label>
                                    <input type="text" name="addr_house"
                                        class="w-full px-6 py-4 bg-white/50 border border-slate-100 rounded-2xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-semibold text-slate-700 transition-all text-lg"
                                        placeholder="123/45 ถนนพลาธิการ...">
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">จังหวัด</label>
                                    <input type="text" name="addr_province" list="provinces"
                                        class="w-full px-6 py-4 bg-white/50 border border-slate-100 rounded-2xl focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-semibold text-slate-700 transition-all text-lg"
                                        placeholder="พิมพ์ชื่อจังหวัด...">
                                </div>
                            </div>
                        </section>

                        <!-- Date Range Selection -->
                        <section class="glass-panel rounded-[3rem] p-8 md:p-12 mb-10">
                            <div class="flex items-center gap-5 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-brand-500 text-white flex items-center justify-center shadow-lg rotate-3">
                                    <i data-lucide="calendar-days" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">2. กำหนดช่วงเวลาการลา
                                    </h3>
                                    <p class="text-base font-semibold text-slate-400 uppercase tracking-widest mt-1"
                                        x-text="isTemporary ? 'ระบุวันและช่วงเวลาที่ต้องการลา' : 'เลือกวันเริ่มต้นถึงวันสิ้นสุดการปฏิบัติราชการ'">
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row items-center gap-6">
                                <!-- Start Date -->
                                <div class="flex-1 w-full relative group">
                                    <label
                                        class="absolute -top-3 left-6 px-2 bg-white text-xs font-bold text-brand-500 uppercase tracking-widest rounded-full z-10 border border-brand-100 shadow-sm">วันเริ่มต้น</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-slate-300">
                                            <i data-lucide="calendar" class="w-5 h-5"></i>
                                        </div>
                                        <input type="date" name="start_date" x-model="startDate" required
                                            class="w-full pl-16 pr-6 py-5 bg-white/80 border-2 border-slate-50 rounded-[2rem] focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-800 text-xl transition-all shadow-sm">
                                    </div>
                                </div>

                                <div class="hidden md:flex flex-col items-center">
                                    <div
                                        class="w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-500 shadow-inner">
                                        <i data-lucide="arrow-right" class="w-6 h-6"></i>
                                    </div>
                                </div>

                                <!-- End Date -->
                                <div class="flex-1 w-full relative group" x-show="!isTemporary">
                                    <label
                                        class="absolute -top-3 left-6 px-2 bg-white text-xs font-bold text-indigo-500 uppercase tracking-widest rounded-full z-10 border border-indigo-100 shadow-sm">วันสิ้นสุด</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-slate-300">
                                            <i data-lucide="calendar-check-2" class="w-5 h-5"></i>
                                        </div>
                                        <input type="date" name="end_date" x-model="endDate" required
                                            class="w-full pl-16 pr-6 py-5 bg-white/80 border-2 border-slate-50 rounded-[2rem] focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 font-bold text-slate-800 text-xl transition-all shadow-sm">
                                    </div>
                                </div>

                                <!-- Period Selection for Temporary -->
                                <div x-show="isTemporary" class="flex-1 w-full flex bg-slate-100 p-1.5 rounded-[2rem]">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="temporary_leave_period" value="morning"
                                            class="sr-only peer" x-model="temporaryPeriod">
                                        <div
                                            class="py-4 text-center rounded-[1.75rem] font-bold text-base peer-checked:bg-white peer-checked:text-brand-600 peer-checked:shadow-lg transition-all text-slate-400">
                                            ช่วงเช้า</div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="temporary_leave_period" value="afternoon"
                                            class="sr-only peer" x-model="temporaryPeriod">
                                        <div
                                            class="py-4 text-center rounded-[1.75rem] font-bold text-base peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-lg transition-all text-slate-400">
                                            ช่วงบ่าย</div>
                                    </label>
                                </div>
                            </div>
                        </section>

                        <!-- Details & Documents -->
                        <section class="glass-panel rounded-[3rem] p-8 md:p-12">
                            <div class="flex items-center gap-5 mb-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg -rotate-3">
                                    <i data-lucide="file-text" class="w-7 h-7"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-slate-800 tracking-tight">3. รายละเอียดเพิ่มเติม
                                    </h3>
                                    <p class="text-base font-semibold text-slate-400 uppercase tracking-widest mt-1">
                                        ระบุเหตุผลความจำเป็นและความประสงค์</p>
                                </div>
                            </div>

                            <div class="space-y-8">
                                <div class="relative">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">เหตุผลหรือความจำเป็นในการลา
                                        <span class="text-rose-500">*</span></label>
                                    <textarea name="reason" rows="4" required
                                        class="w-full px-8 py-6 bg-white/50 border border-slate-100 rounded-[2rem] focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-semibold text-slate-800 text-xl transition-all resize-none shadow-inner"
                                        placeholder="ระบุเหตุผล เช่น ติดธุระส่วนตัวไม่สามารถมาปฏิบัติราชการได้..."></textarea>
                                </div>

                                <div class="relative group">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-3 ml-1">เอกสารประกอบ
                                        (ถ้ามี)</label>
                                    <div class="file-drop relative rounded-[2rem] border-2 border-dashed border-slate-200 p-10 text-center hover:border-brand-500 hover:bg-brand-50/30 transition-all cursor-pointer group/upload"
                                        @dragover.prevent="$el.classList.add('bg-brand-50', 'border-brand-500')"
                                        @dragleave.prevent="$el.classList.remove('bg-brand-50', 'border-brand-500')"
                                        @drop.prevent="handleFileDrop($event)">
                                        <input type="file" name="attachment"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                            id="fileInput" @change="handleFileChange($event)">
                                        <div class="flex flex-col items-center gap-4">
                                            <div
                                                class="w-20 h-20 rounded-[1.75rem] bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover/upload:scale-110 group-hover/upload:rotate-12 transition-all shadow-inner">
                                                <i data-lucide="upload-cloud" class="w-10 h-10"></i>
                                            </div>
                                            <div x-show="!fileName" class="animate-pulse">
                                                <p class="text-2xl font-bold text-slate-800">ลากไฟล์มาวางที่นี่ หรือ
                                                    คลิกเพื่อเลือก</p>
                                                <p
                                                    class="text-sm font-semibold text-slate-400 mt-2 uppercase tracking-widest leading-relaxed">
                                                    รองรับไฟล์ PDF, JPG, PNG ขนาดไม่เกิน 5MB</p>
                                            </div>
                                            <div x-show="fileName" x-cloak
                                                class="flex items-center gap-3 bg-white px-6 py-3 rounded-2xl shadow-xl border border-indigo-100 scale-105 transition-transform">
                                                <i data-lucide="file-check" class="w-5 h-5 text-emerald-500"></i>
                                                <span class="text-base font-bold text-brand-600 truncate max-w-[200px]"
                                                    x-text="fileName"></span>
                                                <button type="button" @click.prevent="clearFile()"
                                                    class="p-1.5 bg-rose-50 text-rose-500 rounded-lg hover:bg-rose-100 transition-colors">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Action Button -->
                        <div class="pt-10 flex flex-col md:flex-row items-center gap-6">
                            <button type="submit"
                                class="w-full md:w-auto flex-1 py-6 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 text-white font-bold text-3xl rounded-[2.5rem] shadow-[0_20px_50px_-15px_rgba(79,70,229,0.4)] hover:shadow-[0_25px_60px_-12px_rgba(79,70,229,0.5)] transition-all hover:-translate-y-2 active:scale-95 flex items-center justify-center gap-4 group">
                                <i data-lucide="shield-check"
                                    class="w-8 h-8 group-hover:rotate-12 transition-transform"></i>
                                <span>ยืนยันและส่งใบลา</span>
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="w-full md:w-auto px-10 py-6 bg-white text-slate-400 hover:text-slate-600 font-bold text-xl rounded-[2.5rem] transition-all hover:bg-slate-50 border border-slate-100 text-center">
                                ยกเลิกรายการ
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar: Premium Summary Ticket -->
                <div class="lg:col-span-4 lg:sticky lg:top-24">
                    <div class="glass-panel rounded-[3rem] p-8 pb-12 relative overflow-hidden bg-slate-900 shadow-2xl">
                        <!-- Background Pattern -->
                        <div
                            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.05]">
                        </div>
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-brand-500/20 rounded-full blur-3xl -mr-16 -mt-16">
                        </div>

                        <div class="relative z-10 space-y-8">
                            <div class="flex items-center justify-between">
                                <h4 class="text-2xl font-bold text-white tracking-tight uppercase">Dashboard Preview
                                </h4>
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-brand-400 border border-white/10">
                                    <i data-lucide="monitor" class="w-6 h-6"></i>
                                </div>
                            </div>

                            <!-- Digital Ticket Look -->
                            <div class="bg-white rounded-3xl p-6 shadow-2xl space-y-6 relative group overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-br from-brand-50 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                </div>
                                <div
                                    class="absolute -left-3 top-1/2 -mt-3 w-6 h-6 bg-slate-900 rounded-full shadow-inner">
                                </div>
                                <div
                                    class="absolute -right-3 top-1/2 -mt-3 w-6 h-6 bg-slate-900 rounded-full shadow-inner">
                                </div>

                                <div class="space-y-6 relative z-10">
                                    <div class="flex items-center gap-4 border-b border-slate-50 pb-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center font-black">
                                            <i data-lucide="tag" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                                                ประเภทการลา</p>
                                            <p class="text-lg font-bold text-slate-800 leading-none mt-1"
                                                x-text="getLeaveTypeName() || 'รอเลือกประเภท...'"></p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                                                วันเริ่มต้น</p>
                                            <p class="text-base font-bold text-slate-700 mt-1"
                                                x-text="formatDate(startDate) || '-'"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                                                วันสิ้นสุด</p>
                                            <p class="text-base font-bold text-slate-700 mt-1"
                                                x-text="formatDate(endDate) || '-'"></p>
                                        </div>
                                    </div>

                                    <div
                                        class="pt-6 border-t border-dashed border-slate-200 flex items-center justify-between">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-bold text-slate-400 uppercase tracking-widest">ยอดรวมการลาครั้งนี้</span>
                                            <span
                                                class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-indigo-600"
                                                x-text="(duration > 0 ? duration : 0) + ' วัน'"></span>
                                        </div>
                                        <div class="w-14 h-14 rounded-full border-4 border-indigo-50 border-t-indigo-500 animate-spin"
                                            x-show="duration > 0 && !isTemporary"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Real-time Validation Message -->
                            <div class="bg-white/5 rounded-2xl p-5 border border-white/10">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="info" class="w-5 h-5"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-base font-bold text-slate-200">ข้อควรระวัง</p>
                                        <p class="text-sm font-semibold text-slate-500 leading-relaxed">
                                            กรุณาตรวจสอบข้อมูลและวันลาคงเหลือของท่านก่อนกดยืนยัน
                                            เพื่อป้องกันการไม่อนุมัติจากสายงาน</p>
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
                    todayDate: new Date().toISOString().split('T')[0],
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
                                    this.startDate = this.todayDate;
                                    this.endDate = this.todayDate;
                                }
                            }
                            this.$nextTick(() => window.lucide.createIcons());
                        });
                        this.$watch('startDate', () => this.updateStep());
                        this.$watch('endDate', () => this.updateStep());
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

                    handleFileChange(e) { this.fileName = e.target.files[0]?.name || ''; },
                    handleFileDrop(e) {
                        const file = e.dataTransfer.files[0];
                        if (file) {
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
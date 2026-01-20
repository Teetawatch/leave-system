<x-app-layout>
    @section('title', 'ยื่นคำขอลา')

    @push('styles')
        <style>
            /* Light Animated Background */
            .leave-bg {
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 25%, #f5f3ff 50%, #fdf4ff 75%, #fff1f2 100%);
                background-size: 400% 400%;
                animation: gradientShift 20s ease infinite;
            }

            @keyframes gradientShift {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            /* Glassmorphism */
            .glass-card {
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            .glass-dark {
                background: rgba(30, 41, 59, 0.9);
                backdrop-filter: blur(20px);
            }

            /* Floating Animation */
            @keyframes float {

                0%,
                100% {
                    transform: translateY(0px);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            .float-animation {
                animation: float 6s ease-in-out infinite;
            }

            /* Shine Effect */
            .shine-btn {
                position: relative;
                overflow: hidden;
            }

            .shine-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.5s;
            }

            .shine-btn:hover::before {
                left: 100%;
            }

            /* Card Hover */
            .leave-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .leave-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            }

            /* Stepper */
            .step-connector {
                background: linear-gradient(90deg, #e2e8f0, #cbd5e1);
                transition: all 0.5s ease;
            }

            .step-connector.active {
                background: linear-gradient(90deg, #6366f1, #8b5cf6);
            }

            /* Input Focus */
            .premium-input {
                transition: all 0.3s ease;
            }

            .premium-input:focus {
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
            }

            /* Type Card */
            .type-card {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .type-card:hover {
                transform: translateY(-4px);
            }

            .type-card.selected {
                border-color: #6366f1;
                background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%);
                box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.3);
            }

            /* Pulse Ring */
            @keyframes pulse-ring {
                0% {
                    transform: scale(0.8);
                    opacity: 1;
                }

                100% {
                    transform: scale(1.5);
                    opacity: 0;
                }
            }

            .pulse-ring::before {
                content: '';
                position: absolute;
                inset: -4px;
                border-radius: 50%;
                border: 2px solid currentColor;
                animation: pulse-ring 1.5s ease-out infinite;
            }

            /* File Upload */
            .file-drop {
                transition: all 0.3s ease;
            }

            .file-drop:hover,
            .file-drop.dragover {
                border-color: #6366f1;
                background: rgba(99, 102, 241, 0.05);
            }

            /* Mobile Responsive Stepper */
            @media (max-width: 640px) {
                .mobile-step-text {
                    display: none;
                }

                .mobile-step-num {
                    width: 2rem;
                    height: 2rem;
                    font-size: 0.75rem;
                }
            }
        </style>
    @endpush

    <!-- Main Container with Animated BG -->
    <div class="min-h-screen -m-4 md:-m-8 leave-bg relative overflow-hidden" x-data="leaveFormApp()">
        <!-- Floating Decorative Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-indigo-200/40 rounded-full blur-3xl float-animation"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-purple-200/30 rounded-full blur-3xl float-animation"
                style="animation-delay: -3s;"></div>
            <div class="absolute top-1/2 left-1/2 w-64 h-64 bg-pink-200/30 rounded-full blur-3xl float-animation"
                style="animation-delay: -6s;"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-6xl mx-auto px-4 py-6 md:py-10">

            <!-- Premium Header -->
            <div class="text-center mb-8 md:mb-10">
                <div
                    class="inline-flex items-center gap-2 bg-indigo-100 backdrop-blur-sm px-4 py-2 rounded-full text-indigo-600 text-sm font-medium mb-4 shadow-sm">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    <span>ระบบยื่นใบลาออนไลน์</span>
                </div>
                <h1
                    class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-800 mb-3 tracking-tight">
                    แบบฟอร์มขออนุมัติการลา
                </h1>
                <p class="text-slate-500 text-base md:text-lg max-w-xl mx-auto">
                    กรอกข้อมูลให้ครบถ้วนเพื่อส่งเรื่องไปยังผู้อนุมัติของคุณ
                </p>

                <!-- Stepper -->
                <div class="flex items-center justify-center gap-2 md:gap-4 mt-8 flex-wrap">
                    <!-- Step 1 -->
                    <div class="flex items-center gap-2">
                        <div class="mobile-step-num w-8 h-8 md:w-10 md:h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm md:text-base shadow-lg relative"
                            :class="{'ring-4 ring-indigo-200': true}">
                            <span x-show="!leaveType">1</span>
                            <i data-lucide="check" class="w-4 h-4 md:w-5 md:h-5" x-show="leaveType" x-cloak></i>
                        </div>
                        <span class="mobile-step-text text-sm font-semibold text-slate-700">เลือกประเภท</span>
                    </div>

                    <div class="w-8 md:w-16 h-1 rounded-full step-connector" :class="{'active': leaveType}"></div>

                    <!-- Step 2 -->
                    <div class="flex items-center gap-2">
                        <div class="mobile-step-num w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-sm md:text-base transition-all duration-300"
                            :class="leaveType ? 'bg-indigo-600 text-white shadow-lg' : 'bg-slate-200 text-slate-400'">
                            <span x-show="!startDate || !endDate">2</span>
                            <i data-lucide="check" class="w-4 h-4 md:w-5 md:h-5" x-show="startDate && endDate"
                                x-cloak></i>
                        </div>
                        <span class="mobile-step-text text-sm font-semibold transition-colors"
                            :class="leaveType ? 'text-slate-700' : 'text-slate-400'">ระบุวันลา</span>
                    </div>

                    <div class="w-8 md:w-16 h-1 rounded-full step-connector" :class="{'active': startDate && endDate}">
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-center gap-2">
                        <div class="mobile-step-num w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center font-bold text-sm md:text-base transition-all duration-300"
                            :class="(startDate && endDate) ? 'bg-indigo-600 text-white shadow-lg' : 'bg-slate-200 text-slate-400'">
                            3
                        </div>
                        <span class="mobile-step-text text-sm font-semibold transition-colors"
                            :class="(startDate && endDate) ? 'text-slate-700' : 'text-slate-400'">รายละเอียด</span>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                <!-- Form Column -->
                <div class="lg:col-span-8 space-y-6">
                    <form action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data"
                        id="leaveForm">
                        @csrf

                        <!-- Section 1: Leave Type Selection -->
                        <div class="glass-card rounded-3xl p-6 md:p-8 shadow-xl leave-card">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                                    <i data-lucide="layers" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">เลือกประเภทการลา</h3>
                                    <p class="text-sm text-slate-500">กรุณาเลือกประเภทการลาที่ต้องการ</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($leaveTypes as $type)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="leave_type_id" value="{{ $type->id }}"
                                            class="peer sr-only" x-model="leaveType">
                                        <div class="type-card relative p-6 rounded-2xl border-2 border-slate-100 bg-white text-center peer-checked:selected"
                                            :class="{'selected': leaveType == '{{ $type->id }}'}">
                                            <div
                                                class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center text-3xl mb-4 transition-transform group-hover:scale-110
                                                    {{ $type->slug == 'vacation' ? 'bg-blue-100 text-blue-600' : ($type->slug == 'sick' ? 'bg-rose-100 text-rose-600' : ($type->slug == 'temporary' ? 'bg-purple-100 text-purple-600' : 'bg-amber-100 text-amber-600')) }}">
                                                @if($type->slug == 'vacation')
                                                    <i data-lucide="plane" class="w-8 h-8"></i>
                                                @elseif($type->slug == 'sick')
                                                    <i data-lucide="heart-pulse" class="w-8 h-8"></i>
                                                @elseif($type->slug == 'temporary')
                                                    <i data-lucide="clock" class="w-8 h-8"></i>
                                                @else
                                                    <i data-lucide="briefcase" class="w-8 h-8"></i>
                                                @endif
                                            </div>
                                            <h4 class="text-lg font-bold text-slate-800 mb-1">{{ $type->name }}</h4>

                                            <!-- Check Icon -->
                                            <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-indigo-500 text-white flex items-center justify-center opacity-0 scale-0 transition-all peer-checked:opacity-100 peer-checked:scale-100"
                                                :class="{'opacity-100 scale-100': leaveType == '{{ $type->id }}'}">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('leave_type_id')
                                <div class="mt-4 flex items-center gap-2 text-rose-600 bg-rose-50 px-4 py-3 rounded-xl">
                                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                                    <span class="text-sm font-medium">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Section: Address (Sick Leave) -->
                        <div x-show="isSick" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" x-cloak
                            class="glass-card rounded-3xl p-6 md:p-8 shadow-xl leave-card mt-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center text-white shadow-lg">
                                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">ที่อยู่ที่ติดต่อได้</h3>
                                    <p class="text-sm text-slate-500">ระบุที่อยู่ระหว่างการลาป่วย</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">บ้านเลขที่ <span
                                            class="text-rose-500">*</span></label>
                                    <input type="text" name="addr_house"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none"
                                        placeholder="123/45">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">ถนน</label>
                                    <input type="text" name="addr_road"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">ตำบล / แขวง</label>
                                    <input type="text" name="addr_tambon"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">อำเภอ / เขต</label>
                                    <input type="text" name="addr_amphoe"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">จังหวัด <span
                                            class="text-rose-500">*</span></label>
                                    <input type="text" name="addr_province" list="provinces"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none"
                                        placeholder="เลือกจังหวัด">
                                    <datalist id="provinces">
                                        @foreach(['กรุงเทพมหานคร', 'กระบี่', 'กาญจนบุรี', 'กาฬสินธุ์', 'กำแพงเพชร', 'ขอนแก่น', 'จันทบุรี', 'ฉะเชิงเทรา', 'ชลบุรี', 'ชัยนาท', 'ชัยภูมิ', 'ชุมพร', 'เชียงราย', 'เชียงใหม่', 'ตรัง', 'ตราด', 'ตาก', 'นครนายก', 'นครปฐม', 'นครพนม', 'นครราชสีมา', 'นครศรีธรรมราช', 'นครสวรรค์', 'นนทบุรี', 'นราธิวาส', 'น่าน', 'บึงกาฬ', 'บุรีรัมย์', 'ปทุมธานี', 'ประจวบคีรีขันธ์', 'ปราจีนบุรี', 'ปัตตานี', 'พระนครศรีอยุธยา', 'พะเยา', 'พังงา', 'พัทลุง', 'พิจิตร', 'พิษณุโลก', 'เพชรบุรี', 'เพชรบูรณ์', 'แพร่', 'ภูเก็ต', 'มหาสารคาม', 'มุกดาหาร', 'แม่ฮ่องสอน', 'ยโสธร', 'ยะลา', 'ร้อยเอ็ด', 'ระนอง', 'ระยอง', 'ราชบุรี', 'ลพบุรี', 'ลำปาง', 'ลำพูน', 'เลย', 'ศรีสะเกษ', 'สกลนคร', 'สงขลา', 'สตูล', 'สมุทรปราการ', 'สมุทรสงคราม', 'สมุทรสาคร', 'สระแก้ว', 'สระบุรี', 'สิงห์บุรี', 'สุโขทัย', 'สุพรรณบุรี', 'สุราษฎร์ธานี', 'สุรินทร์', 'หนองคาย', 'หนองบัวลำภู', 'อ่างทอง', 'อำนาจเจริญ', 'อุดรธานี', 'อุตรดิตถ์', 'อุทัยธานี', 'อุบลราชธานี'] as $province)
                                            <option value="{{ $province }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Personal Leave Location -->
                        <div x-show="isPersonal" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" x-cloak
                            class="glass-card rounded-3xl p-6 md:p-8 shadow-xl leave-card mt-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg">
                                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">สถานที่ระหว่างลา</h3>
                                    <p class="text-sm text-slate-500">ระบุสถานที่ที่จะไป</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">จะไปที่ <span
                                            class="text-rose-500">*</span></label>
                                    <input type="text" name="personal_location"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none"
                                        placeholder="ระบุสถานที่">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">จังหวัด <span
                                            class="text-rose-500">*</span></label>
                                    <input type="text" name="personal_province" list="provinces"
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none"
                                        placeholder="เลือกจังหวัด">
                                </div>
                            </div>
                        </div>

                        <!-- Section: Temporary Leave Period -->
                        <div x-show="isTemporary" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" x-cloak
                            class="glass-card rounded-3xl p-6 md:p-8 shadow-xl leave-card mt-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center text-white shadow-lg">
                                    <i data-lucide="clock" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">เลือกช่วงเวลาลา</h3>
                                    <p class="text-sm text-slate-500">เลือกช่วงเช้าหรือบ่าย</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="temporary_leave_period" value="morning"
                                        class="peer sr-only" x-model="temporaryPeriod">
                                    <div
                                        class="type-card p-6 rounded-2xl border-2 border-slate-100 bg-white text-center peer-checked:selected">
                                        <div
                                            class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white mb-4 shadow-lg">
                                            <i data-lucide="sunrise" class="w-8 h-8"></i>
                                        </div>
                                        <h4 class="text-lg font-bold text-slate-800 mb-2">ช่วงเช้า</h4>
                                        <p class="text-sm text-slate-500">ต้องยื่นก่อน <span
                                                class="font-bold text-purple-600">07:30 น.</span></p>
                                    </div>
                                </label>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="temporary_leave_period" value="afternoon"
                                        class="peer sr-only" x-model="temporaryPeriod">
                                    <div
                                        class="type-card p-6 rounded-2xl border-2 border-slate-100 bg-white text-center peer-checked:selected">
                                        <div
                                            class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white mb-4 shadow-lg">
                                            <i data-lucide="sunset" class="w-8 h-8"></i>
                                        </div>
                                        <h4 class="text-lg font-bold text-slate-800 mb-2">ช่วงบ่าย</h4>
                                        <p class="text-sm text-slate-500">ต้องยื่นก่อน <span
                                                class="font-bold text-purple-600">11:00 น.</span></p>
                                    </div>
                                </label>
                            </div>
                            @error('temporary_leave_period')
                                <div class="mt-4 flex items-center gap-2 text-rose-600 bg-rose-50 px-4 py-3 rounded-xl">
                                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                                    <span class="text-sm font-medium">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Section 2: Date Selection -->
                        <div class="glass-card rounded-3xl p-6 md:p-8 shadow-xl leave-card mt-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white shadow-lg">
                                    <i data-lucide="calendar" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800"
                                        x-text="isTemporary ? 'วันที่ลา' : 'ระบุวันลา'"></h3>
                                    <p class="text-sm text-slate-500">เลือกวันเริ่มต้นและสิ้นสุด</p>
                                </div>
                            </div>

                            <!-- Temporary: Single Day -->
                            <div x-show="isTemporary" class="text-center py-6">
                                <div
                                    class="inline-flex flex-col items-center p-8 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-3xl border-2 border-purple-100">
                                    <div
                                        class="w-20 h-20 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center mb-4 shadow-xl">
                                        <i data-lucide="calendar-check" class="w-10 h-10"></i>
                                    </div>
                                    <p class="text-lg font-bold text-slate-700 mb-2">ลาวันนี้</p>
                                    <p class="text-2xl font-black text-purple-600" x-text="formatDate(todayDate)"></p>
                                    <p class="text-sm text-slate-500 mt-3">ลาชั่วกาลสามารถลาได้เฉพาะวันนี้</p>
                                </div>
                            </div>

                            <!-- Normal: Date Range -->
                            <div x-show="!isTemporary" class="flex flex-col md:flex-row items-stretch gap-4 md:gap-6">
                                <div
                                    class="flex-1 bg-slate-50 p-5 rounded-2xl border-2 border-transparent hover:border-indigo-200 transition-colors focus-within:border-indigo-500 focus-within:bg-white">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">วันเริ่มต้น</label>
                                    <input type="date" name="start_date" x-model="startDate"
                                        class="w-full bg-transparent border-0 p-0 text-slate-800 font-bold text-lg focus:ring-0 cursor-pointer"
                                        required>
                                </div>

                                <div class="hidden md:flex flex-col items-center justify-center">
                                    <div class="w-12 h-1 bg-slate-200 rounded-full mb-2"></div>
                                    <span x-show="duration > 0"
                                        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold px-4 py-2 rounded-full shadow-lg"
                                        x-text="duration + ' วัน'"></span>
                                </div>

                                <div
                                    class="flex-1 bg-slate-50 p-5 rounded-2xl border-2 border-transparent hover:border-indigo-200 transition-colors focus-within:border-indigo-500 focus-within:bg-white">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">วันสิ้นสุด</label>
                                    <input type="date" name="end_date" x-model="endDate"
                                        class="w-full bg-transparent border-0 p-0 text-slate-800 font-bold text-lg focus:ring-0 cursor-pointer"
                                        required>
                                </div>
                            </div>

                            <!-- Mobile Duration Display -->
                            <div x-show="!isTemporary && duration > 0" class="md:hidden text-center mt-4">
                                <span
                                    class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold px-4 py-2 rounded-full shadow-lg"
                                    x-text="'รวม ' + duration + ' วัน'"></span>
                            </div>

                            @error('start_date')
                                <div class="mt-4 flex items-center gap-2 text-rose-600 bg-rose-50 px-4 py-3 rounded-xl">
                                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                                    <span class="text-sm font-medium">{{ $message }}</span>
                                </div>
                            @enderror
                            @error('end_date')
                                <div class="mt-4 flex items-center gap-2 text-rose-600 bg-rose-50 px-4 py-3 rounded-xl">
                                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                                    <span class="text-sm font-medium">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Section 3: Details -->
                        <div class="glass-card rounded-3xl p-6 md:p-8 shadow-xl leave-card mt-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg">
                                    <i data-lucide="file-text" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">รายละเอียดการลา</h3>
                                    <p class="text-sm text-slate-500">ระบุเหตุผลและแนบเอกสาร</p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">
                                        <span
                                            x-text="isPersonal ? 'ขออนุญาตลาหยุดราชการเพื่อ' : 'เหตุผลความจำเป็น'"></span>
                                        <span class="text-rose-500">*</span>
                                    </label>
                                    <textarea name="reason" rows="4" required
                                        class="premium-input w-full px-4 py-3 rounded-xl border-2 border-slate-100 bg-white focus:border-indigo-500 focus:outline-none resize-none"
                                        placeholder="เช่น ป่วยเป็นไข้หวัด, ติดธุระสำคัญ..."></textarea>
                                    @error('reason')
                                        <p class="text-rose-500 text-sm mt-2 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">แนบเอกสาร (ถ้ามี)</label>
                                    <div class="file-drop relative rounded-2xl border-2 border-dashed border-slate-200 p-6 text-center cursor-pointer hover:border-indigo-400"
                                        @dragover.prevent="$el.classList.add('dragover')"
                                        @dragleave.prevent="$el.classList.remove('dragover')"
                                        @drop.prevent="handleFileDrop($event)">
                                        <input type="file" name="attachment"
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            id="fileInput" @change="handleFileChange($event)">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                                                <i data-lucide="cloud-upload" class="w-7 h-7"></i>
                                            </div>
                                            <div x-show="!fileName">
                                                <p class="text-sm font-bold text-indigo-600">คลิกเพื่อเลือกไฟล์</p>
                                                <p class="text-xs text-slate-400 mt-1">หรือลากไฟล์มาวางที่นี่ • PDF,
                                                    JPG, PNG</p>
                                            </div>
                                            <div x-show="fileName" x-cloak
                                                class="flex items-center gap-2 bg-indigo-50 px-4 py-2 rounded-lg">
                                                <i data-lucide="file" class="w-4 h-4 text-indigo-600"></i>
                                                <span class="text-sm font-medium text-indigo-700"
                                                    x-text="fileName"></span>
                                                <button type="button" @click.prevent="clearFile()"
                                                    class="text-slate-400 hover:text-rose-500">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8">
                            <button type="submit"
                                class="shine-btn w-full py-5 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 text-white font-bold text-lg md:text-xl rounded-2xl shadow-2xl hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3">
                                <i data-lucide="send" class="w-5 h-5 md:w-6 md:h-6"></i>
                                <span>ส่งใบลาเพื่อขออนุมัติ</span>
                            </button>
                            <p class="text-center text-slate-500 text-sm mt-4">
                                <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                                เมื่อกดยืนยัน ระบบจะส่งการแจ้งเตือนไปยังหัวหน้างานของคุณทันที
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Summary Column -->
                <div class="lg:col-span-4">
                    <div class="lg:sticky lg:top-8 space-y-6">
                        <!-- Summary Card -->
                        <div class="glass-dark rounded-3xl p-6 shadow-2xl text-white">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="font-bold text-lg">สรุปรายการ</h4>
                                <span
                                    class="bg-emerald-500 text-white text-xs font-bold px-3 py-1 rounded-full">Preview</span>
                            </div>

                            <!-- Ticket Style -->
                            <div class="bg-white/10 rounded-2xl p-5 relative overflow-hidden">
                                <div class="absolute -left-3 top-1/2 -mt-3 w-6 h-6 bg-slate-900 rounded-full"></div>
                                <div class="absolute -right-3 top-1/2 -mt-3 w-6 h-6 bg-slate-900 rounded-full"></div>

                                <div class="space-y-4">
                                    <div>
                                        <p class="text-xs text-white/50 font-bold uppercase mb-1">ประเภทการลา</p>
                                        <p class="text-lg font-bold" x-text="getLeaveTypeName() || 'ยังไม่ได้เลือก'">
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-white/50 font-bold uppercase mb-1">เริ่มต้น</p>
                                            <p class="text-sm font-bold" x-text="formatDate(startDate) || '-'"></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-white/50 font-bold uppercase mb-1">สิ้นสุด</p>
                                            <p class="text-sm font-bold" x-text="formatDate(endDate) || '-'"></p>
                                        </div>
                                    </div>

                                    <div
                                        class="pt-4 border-t border-dashed border-white/20 flex items-center justify-between">
                                        <span class="text-sm text-white/70">รวมทั้งหมด</span>
                                        <span
                                            class="text-2xl font-black bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent"
                                            x-text="(duration > 0 ? duration : 0) + ' วัน'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips Card -->
                        <div class="glass-card rounded-3xl p-6 shadow-xl">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                    <i data-lucide="lightbulb" class="w-5 h-5"></i>
                                </div>
                                <h4 class="font-bold text-slate-800">เคล็ดลับ</h4>
                            </div>
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle"
                                        class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>กรอกข้อมูลให้ครบถ้วนเพื่อการอนุมัติที่รวดเร็ว</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle"
                                        class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>แนบเอกสารประกอบหากมี (ใบรับรองแพทย์)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle"
                                        class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                                    <span>ติดตามสถานะได้ที่เมนู "ประวัติการลา"</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('leaveFormApp', () => ({
                    leaveType: '{{ old('leave_type_id') }}',
                    startDate: '{{ old('start_date') }}',
                    endDate: '{{ old('end_date') }}',
                    temporaryPeriod: '{{ old('temporary_leave_period') }}',
                    todayDate: new Date().toISOString().split('T')[0],
                    leaveTypes: @json($leaveTypes),
                    fileName: '',

                    init() {
                        this.$watch('leaveType', (value) => {
                            if (this.isTemporary) {
                                this.startDate = this.todayDate;
                                this.endDate = this.todayDate;
                            }
                        });
                        // Re-init icons after Alpine renders
                        this.$nextTick(() => {
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        });
                    },

                    get isSick() {
                        if (!this.leaveType) return false;
                        const type = this.leaveTypes.find(t => t.id == this.leaveType);
                        return type && type.slug === 'sick';
                    },

                    get isPersonal() {
                        if (!this.leaveType) return false;
                        const type = this.leaveTypes.find(t => t.id == this.leaveType);
                        return type && type.slug === 'personal';
                    },

                    get isTemporary() {
                        if (!this.leaveType) return false;
                        const type = this.leaveTypes.find(t => t.id == this.leaveType);
                        return type && type.slug === 'temporary';
                    },

                    get duration() {
                        if (this.startDate && this.endDate) {
                            const start = new Date(this.startDate);
                            const end = new Date(this.endDate);
                            const diffTime = Math.abs(end - start);
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                            return diffDays > 0 ? diffDays : 0;
                        }
                        return 0;
                    },

                    getLeaveTypeName() {
                        if (!this.leaveType) return null;
                        const type = this.leaveTypes.find(t => t.id == this.leaveType);
                        return type ? type.name : null;
                    },

                    formatDate(dateString) {
                        if (!dateString) return null;
                        const date = new Date(dateString);
                        return date.toLocaleDateString('th-TH', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                    },

                    handleFileChange(event) {
                        const file = event.target.files[0];
                        this.fileName = file ? file.name : '';
                    },

                    handleFileDrop(event) {
                        const file = event.dataTransfer.files[0];
                        if (file) {
                            this.fileName = file.name;
                            document.getElementById('fileInput').files = event.dataTransfer.files;
                        }
                        event.target.classList.remove('dragover');
                    },

                    clearFile() {
                        this.fileName = '';
                        document.getElementById('fileInput').value = '';
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>
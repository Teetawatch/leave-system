<x-app-layout>
    @section('title', 'ยื่นคำขอลา (New Leave Request)')

    <div class="max-w-6xl mx-auto py-10" x-data="leaveForm()">
        
        <!-- Premium Header with Stepper -->
        <div class="mb-10 text-center">
            <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">แบบฟอร์มขออนุมัติการลา</h2>
            <p class="text-slate-500 text-lg">กรอกข้อมูลให้ครบถ้วนเพื่อส่งเรื่องไปยังผู้อนุมัติ</p>
            
            <!-- Simple Stepper Visual -->
            <div class="flex items-center justify-center gap-4 mt-8">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shadow-lg shadow-brand-500/30">1</div>
                    <span class="text-sm font-semibold text-slate-700">เลือกประเภท</span>
                </div>



                <div class="w-12 h-1 bg-slate-100 rounded-full"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm" :class="{'!bg-brand-600 !text-white !shadow-lg !shadow-brand-500/30': leaveType}">2</div>
                    <span class="text-sm font-semibold text-slate-500" :class="{'!text-slate-700': leaveType}">ระบุวันลา</span>
                </div>
                 <div class="w-12 h-1 bg-slate-100 rounded-full"></div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm" :class="{'!bg-brand-600 !text-white !shadow-lg !shadow-brand-500/30': startDate && endDate}">3</div>
                    <span class="text-sm font-semibold text-slate-500" :class="{'!text-slate-700': startDate && endDate}">รายละเอียด</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Form Column -->
            <div class="lg:col-span-8 space-y-8">
                
                <form action="{{ route('leave-request.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm">
                    @csrf

                    <!-- Section 1: Type Selection (Large Cards) -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="layers" class="w-4 h-4 text-8xl text-brand-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 text-brand-600 text-lg">
                                <i data-lucide="list" class="w-4 h-4"></i>
                            </span>
                            เลือกประเภทการลา
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 relative z-10">
                            @foreach($leaveTypes as $type)
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="leave_type_id" value="{{ $type->id }}" class="peer sr-only" x-model="leaveType">
                                <div class="px-6 py-8 rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-brand-200 hover:shadow-lg transition-all duration-200 text-center peer-checked:border-brand-500 peer-checked:bg-white peer-checked:shadow-xl peer-checked:shadow-brand-500/10 peer-checked:scale-[1.02]">
                                    <div class="w-16 h-16 mx-auto rounded-2xl bg-white shadow-sm flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform duration-300
                                                {{ $type->slug == 'vacation' ? 'text-blue-500' : ($type->slug == 'sick' ? 'text-rose-500' : ($type->slug == 'temporary' ? 'text-purple-500' : 'text-amber-500')) }}">
                                        @if($type->slug == 'vacation')
                                            <i data-lucide="plane-takeoff" class="w-7 h-7"></i>
                                        @elseif($type->slug == 'sick')
                                            <i data-lucide="heart-pulse" class="w-7 h-7"></i>
                                        @elseif($type->slug == 'temporary')
                                            <i data-lucide="clock" class="w-7 h-7"></i>
                                        @else
                                            <i data-lucide="briefcase" class="w-7 h-7"></i>
                                        @endif
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-800 mb-1">{{ $type->name }}</h4>
                                    
                                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full bg-brand-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity shadow-lg scale-0 peer-checked:scale-100">
                                        <i data-lucide="check" class="w-4 h-4 text-xs"></i>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                         @error('leave_type_id') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg inline-block border border-rose-100">{{ $message }}</p> @enderror
                    </div>

                    <!-- Address Section (Conditional for Sick Leave) -->
                    <div x-show="isSick" x-transition.opacity class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                         <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="map-pin" class="w-4 h-4 text-8xl text-rose-900"></i>
                         </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-rose-50 text-rose-600 text-lg">
                                <i data-lucide="hospital" class="w-4 h-4"></i>
                            </span>
                            ที่อยู่ที่ติดต่อได้ (ระหว่างลา)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">บ้านเลขที่ <span class="text-rose-500">*</span></label>
                                <input type="text" name="addr_house" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3" placeholder="เช่น 123/45">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">ถนน</label>
                                <input type="text" name="addr_road" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">ตำบล / แขวง</label>
                                <input type="text" name="addr_tambon" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">อำเภอ / เขต</label>
                                <input type="text" name="addr_amphoe" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3">
                            </div>
                             <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">จังหวัด <span class="text-rose-500">*</span></label>
                                <input type="text" name="addr_province" list="provinces" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3" placeholder="ระบุจังหวัด">
                                <datalist id="provinces">
                                    @foreach(['กรุงเทพมหานคร','กระบี่','กาญจนบุรี','กาฬสินธุ์','กำแพงเพชร','ขอนแก่น','จันทบุรี','ฉะเชิงเทรา','ชลบุรี','ชัยนาท','ชัยภูมิ','ชุมพร','เชียงราย','เชียงใหม่','ตรัง','ตราด','ตาก','นครนายก','นครปฐม','นครพนม','นครราชสีมา','นครศรีธรรมราช','นครสวรรค์','นนทบุรี','นราธิวาส','น่าน','บึงกาฬ','บุรีรัมย์','ปทุมธานี','ประจวบคีรีขันธ์','ปราจีนบุรี','ปัตตานี','พระนครศรีอยุธยา','พะเยา','พังงา','พัทลุง','พิจิตร','พิษณุโลก','เพชรบุรี','เพชรบูรณ์','แพร่','ภูเก็ต','มหาสารคาม','มุกดาหาร','แม่ฮ่องสอน','ยโสธร','ยะลา','ร้อยเอ็ด','ระนอง','ระยอง','ราชบุรี','ลพบุรี','ลำปาง','ลำพูน','เลย','ศรีสะเกษ','สกลนคร','สงขลา','สตูล','สมุทรปราการ','สมุทรสงคราม','สมุทรสาคร','สระแก้ว','สระบุรี','สิงห์บุรี','สุโขทัย','สุพรรณบุรี','สุราษฎร์ธานี','สุรินทร์','หนองคาย','หนองบัวลำภู','อ่างทอง','อำนาจเจริญ','อุดรธานี','อุตรดิตถ์','อุทัยธานี','อุบลราชธานี'] as $province)
                                        <option value="{{ $province }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Leave Info (Conditional for Personal Leave) -->
                    <div x-show="isPersonal" x-transition.opacity class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                         <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="map-pin" class="w-4 h-4 text-8xl text-amber-900"></i>
                         </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-600 text-lg">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                            </span>
                            ข้อมูลระบุสถานที่ (ระหว่างลา)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                             <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">จะไปที่ <span class="text-rose-500">*</span></label>
                                <input type="text" name="personal_location" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3" placeholder="ระบุสถานที่ที่จะไป">
                            </div>
                             <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 mb-2">จังหวัด <span class="text-rose-500">*</span></label>
                                <input type="text" name="personal_province" list="provinces" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-3" placeholder="เลือกจังหวัด">
                            </div>
                        </div>
                    </div>

                    <!-- Temporary Leave Period Selection (Conditional for Temporary Leave) -->
                    <div x-show="isTemporary" x-transition.opacity class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                         <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="clock" class="w-4 h-4 text-8xl text-purple-900"></i>
                         </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-purple-50 text-purple-600 text-lg">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </span>
                            เลือกช่วงเวลาลาชั่วกาล <span class="text-rose-500">*</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative z-10">
                            <!-- Morning Period -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="temporary_leave_period" value="morning" class="peer sr-only" x-model="temporaryPeriod">
                                <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-purple-200 hover:shadow-lg transition-all duration-200 text-center peer-checked:border-purple-500 peer-checked:bg-white peer-checked:shadow-xl peer-checked:shadow-purple-500/10">
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-sm flex items-center justify-center text-white mb-4">
                                        <i data-lucide="sunrise" class="w-7 h-7"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-800 mb-2">ช่วงเช้า</h4>
                                    <p class="text-sm text-slate-500">ต้องยื่นก่อน <span class="font-bold text-purple-600">06:00 น.</span></p>
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full bg-purple-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity shadow-lg scale-0 peer-checked:scale-100">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                    </div>
                                </div>
                            </label>
                            <!-- Afternoon Period -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="temporary_leave_period" value="afternoon" class="peer sr-only" x-model="temporaryPeriod">
                                <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-purple-200 hover:shadow-lg transition-all duration-200 text-center peer-checked:border-purple-500 peer-checked:bg-white peer-checked:shadow-xl peer-checked:shadow-purple-500/10">
                                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-blue-400 to-indigo-500 shadow-sm flex items-center justify-center text-white mb-4">
                                        <i data-lucide="sunset" class="w-7 h-7"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-slate-800 mb-2">ช่วงบ่าย</h4>
                                    <p class="text-sm text-slate-500">ต้องยื่นก่อน <span class="font-bold text-purple-600">11:00 น.</span></p>
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full bg-purple-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity shadow-lg scale-0 peer-checked:scale-100">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('temporary_leave_period') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg inline-block border border-rose-100">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section 2: Timeline Selection -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="calendar-check" class="w-12 h-12 text-blue-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 text-lg">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </span>
                            <span x-text="isTemporary ? 'วันที่ลา' : 'ระบุวันและเวลา'">ระบุวันและเวลา</span>
                        </h3>

                        <!-- Temporary Leave: Single Day (Today Only) -->
                        <div x-show="isTemporary" class="relative z-10">
                            <div class="bg-purple-50 border border-purple-200 rounded-2xl p-6 text-center">
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-purple-500 text-white flex items-center justify-center mb-4">
                                    <i data-lucide="calendar-check" class="w-8 h-8"></i>
                                </div>
                                <p class="text-lg font-bold text-slate-800 mb-2">ลาวันนี้</p>
                                <p class="text-2xl font-black text-purple-600" x-text="formatDate(todayDate)"></p>
                                <p class="text-sm text-slate-500 mt-2">ลาชั่วกาลสามารถลาได้เฉพาะวันนี้เท่านั้น</p>
                            </div>
                        </div>

                        <!-- Normal Leave: Date Range -->
                        <div x-show="!isTemporary" class="flex flex-col md:flex-row items-center justify-center gap-6 relative z-10">
                            <!-- Start -->
                            <div class="flex-1 w-full bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-slate-300 transition-colors focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">วันเริ่มต้น</label>
                                <input type="date" name="start_date" x-model="startDate" class="bg-transparent border-0 p-0 text-slate-800 font-bold text-lg w-full focus:ring-0 cursor-pointer" required>
                            </div>

                            <!-- Duration Arrow -->
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-1 bg-slate-200 rounded-full mb-2"></div>
                                <span class="bg-slate-800 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg" x-show="duration > 0" x-text="duration + ' วัน'"></span>
                            </div>

                            <!-- End -->
                            <div class="flex-1 w-full bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-slate-300 transition-colors focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">วันสิ้นสุด</label>
                                <input type="date" name="end_date" x-model="endDate" class="bg-transparent border-0 p-0 text-slate-800 font-bold text-lg w-full focus:ring-0 cursor-pointer" required>
                            </div>
                        </div>
                        @error('start_date') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg block border border-rose-100">{{ $message }}</p> @enderror
                        @error('end_date') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg block border border-rose-100">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section 3: Details -->
                     <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                         <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="align-left" class="w-8 h-8 text-8xl text-purple-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-purple-50 text-purple-600 text-lg">
                                <i data-lucide="pencil" class="w-5 h-5"></i>
                            </span>
                            รายละเอียดการลา
                        </h3>

                        <div class="space-y-6 relative z-10 w-full">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    <span x-text="isPersonal ? 'ขออนุญาตลาหยุดราชการเพื่อ' : 'เหตุผลความจำเป็น'"></span> <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="reason" rows="3" required class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-4 text-base resize-none" placeholder="เช่น ป่วยเป็นไข้หวัด, ติดต่อราชการ..."></textarea>
                                @error('reason') <p class="text-rose-500 text-sm mt-2 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">แนบเอกสาร (ถ้ามี)</label>
                                <div class="bg-slate-50 rounded-2xl p-4 border border-dashed border-slate-300 hover:border-brand-400 hover:bg-brand-50/20 transition-all text-center">
                                    <input type="file" name="attachment" class="hidden" id="fileInput">
                                     <label for="fileInput" class="cursor-pointer flex flex-col items-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-white text-slate-400 shadow-sm flex items-center justify-center">
                                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                                        </div>
                                        <span class="text-sm font-bold text-brand-600">คลิกเพื่อเลือกไฟล์</span>
                                        <span class="text-xs text-slate-400">รองรับ PDF, JPG, PNG</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Action -->
                    <div class="mt-8">
                        <button type="submit" class="w-full py-5 bg-slate-900 hover:bg-brand-600 text-white font-bold text-xl rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-brand-500/20 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 group">
                            <span>ส่งใบลาเพื่อขออนุมัติ</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                         <p class="text-center text-slate-400 text-sm mt-4">
                            เมื่อกดยืนยัน ระบบจะส่งการแจ้งเตือนไปยังหัวหน้างานของคุณทันที
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Column: Smart Summary Sticky -->
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky top-8 space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white rounded-3xl p-6 shadow-lg shadow-slate-200/50 border border-slate-100">
                         <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-50">
                            <h4 class="font-bold text-slate-800 text-lg">สรุปรายการ</h4>
                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-lg">สรุป</span>
                        </div>
                        
                        <!-- Visual Ticket -->
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 relative overflow-hidden">
                             <!-- Decorative Circles -->
                            <div class="absolute -left-3 top-1/2 -mt-3 w-6 h-6 bg-white rounded-full border border-slate-100"></div>
                            <div class="absolute -right-3 top-1/2 -mt-3 w-6 h-6 bg-white rounded-full border border-slate-100"></div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-slate-400 font-bold uppercase mb-1">ประเภท</p>
                                    <p class="text-lg font-bold text-slate-800" x-text="getLeaveTypeName() || '...'"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                     <div>
                                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">เริ่ม</p>
                                        <p class="text-sm font-bold text-slate-800" x-text="formatDate(startDate) || '...'"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-bold uppercase mb-1">สิ้นสุด</p>
                                        <p class="text-sm font-bold text-slate-800" x-text="formatDate(endDate) || '...'"></p>
                                    </div>
                                </div>
                                 <div class="pt-4 border-t border-dashed border-slate-200 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-slate-500">รวมทั้งหมด</span>
                                    <span class="text-xl font-black text-brand-600" x-text="(duration > 0 ? duration : 0) + ' วัน'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Alpine Logic -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('leaveForm', () => ({
                leaveType: '{{ old('leave_type_id') }}',
                startDate: '{{ old('start_date') }}',
                endDate: '{{ old('end_date') }}',
                temporaryPeriod: '{{ old('temporary_leave_period') }}',
                todayDate: new Date().toISOString().split('T')[0],
                leaveTypes: @json($leaveTypes),

                init() {
                    // Watch for leave type changes to auto-set dates for temporary leave
                    this.$watch('leaveType', (value) => {
                        if (this.isTemporary) {
                            this.startDate = this.todayDate;
                            this.endDate = this.todayDate;
                        }
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
                }
            }))
        })
    </script>
</x-app-layout>

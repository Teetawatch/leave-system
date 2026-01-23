<x-app-layout>
    @section('title', 'แก้ไขข้อมูลข้าราชการ')

    <div class="max-w-5xl mx-auto pb-12">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">แก้ไขข้อมูลข้าราชการ</h2>
                    <span
                        class="px-3 py-1 bg-brand-100 text-brand-700 text-xs font-black rounded-full uppercase tracking-wider">กำลังแก้ไข</span>
                </div>
                <p class="text-slate-500">ปรับปรุงข้อมูลสายการบังคับบัญชา หรือสิทธิ์วันลาของ <span
                        class="font-bold text-slate-700">{{ $employee->rank }}{{ $employee->name }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('employees.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 hover:border-slate-300 transition-all active:scale-95 shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    กลับ
                </a>

                {{-- Delete Action --}}
                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST"
                    onsubmit="return confirm('ยืนยันการลบพนักงานคนนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-rose-50 text-rose-600 font-bold rounded-2xl hover:bg-rose-100 transition-all active:scale-95 border border-rose-100">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        ลบออก
                    </button>
                </form>
            </div>
        </div>

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: ข้อมูลส่วนตัว -->
            <div
                class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/60">
                <div
                    class="px-8 py-6 border-b border-slate-50 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 shadow-inner">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 text-lg">ข้อมูลส่วนตัวและตำแหน่ง</h3>
                        <p class="text-sm text-slate-400">ระบุชื่อ ยศ และสังกัดของข้าราชการ</p>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Rank -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">ยศ <span
                                    class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="award" class="w-4 h-4"></i>
                                </div>
                                <select name="rank" required
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                                    <option value="">ระบุยศ</option>
                                    @foreach(['น.อ.', 'น.ท.', 'น.ต.', 'น.ต.หญิง', 'ร.อ.', 'ร.ท.', 'ร.ต.', 'พ.จ.อ.', 'พ.จ.ท.', 'พ.จ.ต.', 'จ.อ.', 'จ.ท.', 'จ.ท.หญิง', 'จ.ต.', 'นาย', 'นางสาว',] as $rank)
                                        <option value="{{ $rank }}" {{ old('rank', $employee->rank) == $rank ? 'selected' : '' }}>{{ $rank }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                            @error('rank') <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1"><i
                            data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
                        </div>

                        <!-- Name -->
                        <div class="lg:col-span-2 space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">ชื่อ - นามสกุล <span
                                    class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="user-pen" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-slate-700 font-medium"
                                    placeholder="เช่น นายสมชาย ใจดี">
                            </div>
                            @error('name') <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1"><i
                            data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">แผนก</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="building-2" class="w-4 h-4"></i>
                                </div>
                                <select name="department"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                                    <option value="">ระบุแผนก</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}" {{ old('department', $employee->department) == $dept->name ? 'selected' : '' }}>{{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="lg:col-span-2 space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">ตำแหน่ง</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="position" value="{{ old('position', $employee->position) }}"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-slate-700 font-medium"
                                    placeholder="เช่น ประจำแผนก, ผู้ช่วยธุรการ">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: การเข้าใช้งาน -->
            <div
                class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/60">
                <div
                    class="px-8 py-6 border-b border-slate-50 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shadow-inner">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 text-lg">การเข้าสู่ระบบและสิทธิ์การใช้งาน</h3>
                        <p class="text-sm text-slate-400">ตั้งค่าบัญชีผู้ใช้และระดับการเข้าถึงข้อมูล</p>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Email -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">อีเมล (Login) <span
                                    class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-slate-700 font-medium"
                                    placeholder="example@navy.mi.th">
                            </div>
                            @error('email') <p class="mt-1 text-xs text-rose-500 font-medium flex items-center gap-1"><i
                            data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
                        </div>

                        <!-- Role -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">บทบาท (Role) <span
                                    class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </div>
                                <select name="role" required
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                                    <option value="employee" {{ old('role', $employee->role) == 'employee' ? 'selected' : '' }}>ข้าราชการ</option>
                                    <option value="department_head" {{ old('role', $employee->role) == 'department_head' ? 'selected' : '' }}>หัวหน้าแผนก</option>
                                    <option value="deputy_director" {{ old('role', $employee->role) == 'deputy_director' ? 'selected' : '' }}>รอง ผอ.รพธ.พธ.ทร.</option>
                                    <option value="director" {{ old('role', $employee->role) == 'director' ? 'selected' : '' }}>ผอ.รพธ.พธ.ทร.</option>
                                    <option value="admin" {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>
                                        ผู้ดูแลระบบ (Admin)</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="space-y-2 lg:col-span-1">
                            <label class="block text-sm font-black text-slate-700 ml-1">เปลี่ยนรหัสผ่าน
                                (ถ้าต้องการ)</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="key-round" class="w-4 h-4"></i>
                                </div>
                                <input type="password" name="password"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-slate-700 font-medium"
                                    placeholder="ตั้งรหัสผ่านใหม่...">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 ml-1 leading-relaxed">
                                เว้นว่างไว้หากไม่ต้องการเปลี่ยนแปลงรหัสผ่านเดิม</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: การอนุมัติและสิทธิ์วันลา -->
            <div
                class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transition-all hover:shadow-2xl hover:shadow-slate-200/60">
                <div
                    class="px-8 py-6 border-b border-slate-50 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                        <i data-lucide="clipboard-list" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-800 text-lg">การจัดการวันลาและผู้อนุมัติ</h3>
                        <p class="text-sm text-slate-400">กำหนดสายการบังคับบัญชาและสิทธิ์การลาพักผ่อน</p>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Supervisor -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">หัวหน้าแผนก (Approver 1)</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="user-check" class="w-4 h-4"></i>
                                </div>
                                <select name="supervisor_id"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                                    <option value="">ไม่มี</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supervisor_id', $employee->supervisor_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 ml-1 leading-relaxed">ผู้อนุมัติขั้นที่ 1
                                และผู้อนุมัติลาป่วย/กิจ</p>
                        </div>

                        <!-- Deputy (Acknowledgment) -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">รองผู้บังคับบัญชา
                                (Acknowledgement)</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </div>
                                <select name="deputy_id"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                                    <option value="">ไม่มี</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('deputy_id', $employee->deputy_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 ml-1 leading-relaxed">ผู้รับทราบการขอลา</p>
                        </div>

                        <!-- Manager (Step 2) -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">ผู้บังคับบัญชา (Approver
                                2)</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="user-cog" class="w-4 h-4"></i>
                                </div>
                                <select name="manager_id"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all appearance-none cursor-pointer text-slate-700 font-medium">
                                    <option value="">ไม่มี</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('manager_id', $employee->manager_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 ml-1 leading-relaxed">ผู้อนุมัติขั้นที่ 2
                                (เฉพาะลาพักผ่อน)</p>
                        </div>

                        <!-- Start Date -->
                        <div class="space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">วันที่เริ่มงาน</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <input type="date" name="start_date"
                                    value="{{ old('start_date', $employee->start_date?->format('Y-m-d')) }}"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-slate-700 font-medium">
                            </div>
                        </div>

                        <!-- Vacation Leave Quota -->
                        <div class="lg:col-span-2 space-y-2">
                            <label class="block text-sm font-black text-slate-700 ml-1">สิทธิ์วันลาพักผ่อน
                                (วัน/ปี)</label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                                    <i data-lucide="file-signature" class="w-4 h-4"></i>
                                </div>
                                <input type="number" step="0.5" name="vacation_leave_days"
                                    value="{{ old('vacation_leave_days', $currentVacationQuota ?? 10) }}"
                                    class="block w-full pl-11 pr-4 py-3 rounded-2xl border-slate-200 bg-slate-50/30 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all text-slate-700 font-medium"
                                    placeholder="ค่าเริ่มต้น: 10">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1 ml-1 leading-relaxed">
                                สามารถปรับเพิ่มหรือลดสิทธิ์วันลาพักผ่อนที่ได้รับสิทธิ์ต่อปีได้ที่นี่</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6">
                <a href="{{ route('employees.index') }}"
                    class="w-full sm:w-auto px-8 py-3.5 bg-white border border-slate-200 text-slate-600 font-black rounded-2xl hover:bg-slate-50 transition-all active:scale-95 text-center shadow-sm">
                    ยกเลิกการแก้ไข
                </a>
                <button type="submit"
                    class="w-full sm:w-auto px-10 py-3.5 bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 text-white font-black rounded-2xl shadow-xl shadow-brand-500/20 hover:shadow-brand-500/30 active:scale-95 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            /* Custom styles for better appearance */
            input[type="date"]::-webkit-calendar-picker-indicator {
                background: transparent;
                bottom: 0;
                color: transparent;
                cursor: pointer;
                height: auto;
                left: 0;
                position: absolute;
                right: 0;
                top: 0;
                width: auto;
            }
        </style>
    @endpush
</x-app-layout>
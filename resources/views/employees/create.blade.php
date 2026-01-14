<x-app-layout>
    @section('title', 'เพิ่มพนักงานใหม่')

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">เพิ่มพนักงานใหม่</h3>
                    <p class="text-sm text-slate-400">กรอกข้อมูลเพื่อสร้างบัญชีผู้ใช้งานใหม่</p>
                </div>
            </div>

            <form action="{{ route('employees.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Rank -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ยศ <span class="text-red-500">*</span></label>
                        <select name="rank" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                             <option value="">ระบุยศ</option>
                             @foreach(['น.อ.', 'น.ท.', 'น.ต.','น.ต.หญิง', 'ร.อ.', 'ร.ท.', 'ร.ต.','พ.จ.อ.','พ.จ.ท.','พ.จ.ต.','จ.อ.','จ.ท.','จ.ท.หญิง','จ.ต.','นาย','นางสาว',] as $rank)
                                <option value="{{ $rank }}" {{ old('rank') == $rank ? 'selected' : '' }}>{{ $rank }}</option>
                             @endforeach
                        </select>
                         @error('rank') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ชื่อ - นามสกุล <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="เช่น นายสมชาย ใจดี">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">อีเมล (Login) <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="example@company.com">
                         @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">รหัสผ่าน <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="********">
                        @error('password') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">แผนก</label>
                        <select name="department" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                             <option value="">ระบุแผนก</option>
                             @foreach($departments as $dept)
                                <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                             @endforeach
                        </select>
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ตำแหน่ง</label>
                        <input type="text" name="position" value="{{ old('position') }}" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="เช่น Engineer, Sales">
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">บทบาท (Role) <span class="text-red-500">*</span></label>
                        <select name="role" required class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>ข้าราชการ</option>
                            <option value="department_head" {{ old('role') == 'department_head' ? 'selected' : '' }}>หัวหน้าแผนก</option>
                            <option value="deputy_director" {{ old('role') == 'deputy_director' ? 'selected' : '' }}>รองผู้อำนวยการโรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</option>
                            <option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>ผู้อำนวยการโรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>

                    <!-- Supervisor -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">หัวหน้าแผนก (Approver 1)</label>
                        <select name="supervisor_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">ไม่มี</option>
                            @foreach($supervisors as $sup)
                                <option value="{{ $sup->id }}" {{ old('supervisor_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">ผู้อนุมัติขั้นที่ 1 (และผู้อนุมัติลาป่วย/กิจ)</p>
                    </div>

                    <!-- Deputy (Acknowledgment) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">รองผู้บังคับบัญชา (Acknowledgement)</label>
                        <select name="deputy_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">ไม่มี</option>
                            @foreach($supervisors as $sup)
                                <option value="{{ $sup->id }}" {{ old('deputy_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">ผู้รับทราบการลา</p>
                    </div>

                    <!-- Manager (Step 2) -->
                     <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">ผู้บังคับบัญชา (Approver 2)</label>
                        <select name="manager_id" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">ไม่มี</option>
                            @foreach($supervisors as $sup)
                                <option value="{{ $sup->id }}" {{ old('manager_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">ผู้อนุมัติขั้นที่ 2 (เฉพาะลาพักผ่อน)</p>
                    </div>
                    
                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">วันที่เริ่มงาน</label>
                         <input type="date" name="start_date" value="{{ old('start_date') }}" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>

                    <!-- Vacation Leave Quota -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">สิทธิ์วันลาพักผ่อน (วัน/ปี)</label>
                        <input type="number" step="0.5" name="vacation_leave_days" value="{{ old('vacation_leave_days', 10) }}" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="ค่าเริ่มต้น: 10">
                        <p class="text-xs text-slate-400 mt-1">กำหนดจำนวนวันลาพักผ่อนต่อปีสำหรับพนักงานคนนี้</p>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-4 mt-8 pt-4 border-t border-slate-50">
                    <a href="{{ route('employees.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                        ยกเลิก
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-1"></i> เพิ่มพนักงาน
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>

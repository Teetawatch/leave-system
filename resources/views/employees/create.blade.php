<x-app-layout>
    @section('title', 'จัดการข้อมูลกำลังพล - เพิ่มข้าราชการใหม่')

    <div class="min-h-screen bg-slate-50/50 pb-20">

        <!-- Header Section -->
        <div class="bg-white border-b border-slate-100 py-10 mb-12 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-20 h-20 rounded-[2rem] bg-emerald-600 text-white flex items-center justify-center shadow-2xl shadow-emerald-500/30 -rotate-3">
                            <i data-lucide="user-plus" class="w-10 h-10"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h1 class="text-3xl font-black text-slate-900 tracking-tight">เพิ่มข้าราชการใหม่</h1>
                                <span
                                    class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full uppercase tracking-widest border border-emerald-200">New
                                    Registration</span>
                            </div>
                            <p class="text-slate-500 font-bold text-lg">ลงทะเบียนกำลังพลใหม่และกำหนดสิทธิ์เข้าใช้ระบบ
                                E-Leave</p>
                        </div>
                    </div>

                    <a href="{{ route('employees.index') }}"
                        class="inline-flex items-center justify-center px-8 py-4 bg-white border border-slate-200 text-slate-600 font-black rounded-2xl hover:bg-slate-50 hover:border-slate-300 transition-all active:scale-95 shadow-sm">
                        <i data-lucide="arrow-left" class="w-5 h-5 mr-3"></i>
                        กลับหน้ารายชื่อ
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('employees.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                @csrf

                <!-- Left Column: Core Data Forms -->
                <div class="lg:col-span-8 space-y-10">

                    <!-- Section: Personal Info -->
                    <div
                        class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50 overflow-hidden group">
                        <div
                            class="px-10 py-8 border-b border-slate-50 bg-gradient-to-r from-slate-50/50 to-white flex items-center gap-6">
                            <div
                                class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                <i data-lucide="id-card" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-800 text-xl tracking-tight">ข้อมูลพื้นฐานและสังกัด</h3>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mt-1 italic">
                                    Identify New Personnel</p>
                            </div>
                        </div>

                        <div class="p-10 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ยศ
                                        <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="rank" required
                                            class="w-full pl-6 pr-12 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-700 transition-all appearance-none cursor-pointer">
                                            <option value="">-- เลือก --</option>
                                            @foreach(['พล.ร.อ.', 'พล.ร.ท.', 'พล.ร.ต.', 'น.อ.พิเศษ', 'น.อ.', 'น.อ.หญิง', 'ว่าที่ น.อ.', 'ว่าที่ น.อ.หญิง', 'น.ท.', 'น.ท.หญิง', 'ว่าที่ น.ท.', 'ว่าที่ น.ท.หญิง', 'น.ต.', 'น.ต.หญิง', 'ว่าที่ น.ต.', 'ว่าที่ น.ต.หญิง', 'ร.อ.', 'ร.อ.หญิง', 'ว่าที่ ร.อ.', 'ว่าที่ ร.อ.หญิง', 'ร.ท.', 'ร.ท.หญิง', 'ว่าที่ ร.ท.', 'ว่าที่ ร.ท.หญิง', 'ร.ต.', 'ร.ต.หญิง', 'ว่าที่ ร.ต.', 'ว่าที่ ร.ต.หญิง', 'พ.จ.อ.', 'พ.จ.อ.หญิง', 'พ.จ.ท.', 'พ.จ.ท.หญิง', 'พ.จ.ต.', 'พ.จ.ต.หญิง', 'จ.อ.', 'จ.อ.หญิง', 'จ.ท.', 'จ.ท.หญิง', 'จ.ต.', 'จ.ต.หญิง', 'พลฯ', 'นาย', 'นาง', 'นางสาว'] as $rank)
                                                <option value="{{ $rank }}" {{ old('rank') == $rank ? 'selected' : '' }}>
                                                    {{ $rank }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-300">
                                            <i data-lucide="award" class="w-5 h-5"></i>
                                        </div>
                                    </div>
                                    @error('rank') <p class="text-[10px] font-bold text-rose-500 mt-1 ml-1 uppercase">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                                <div class="md:col-span-2 space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อ-นามสกุล
                                        <span class="text-rose-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="w-full px-8 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-800 text-lg transition-all"
                                        placeholder="ป้อนชื่อและนามสกุล...">
                                    @error('name') <p class="text-[10px] font-bold text-rose-500 mt-1 ml-1 uppercase">
                                        {{ $message }}
                                    </p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">แผนก
                                        / สังกัด</label>
                                    <div class="relative">
                                        <select name="department"
                                            class="w-full pl-6 pr-12 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-700 transition-all appearance-none cursor-pointer">
                                            <option value="">-- เลือกแผนก --</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none text-slate-300">
                                            <i data-lucide="building" class="w-5 h-5"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <label
                                        class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ตำแหน่งทางการ</label>
                                    <input type="text" name="position" value="{{ old('position') }}"
                                        class="w-full px-8 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 font-bold text-slate-800 transition-all"
                                        placeholder="ป้อนตำแหน่งงาน...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Approval Workflow -->
                    <div
                        class="bg-slate-900 rounded-[3rem] shadow-2xl shadow-slate-900/20 py-12 px-10 relative overflow-hidden group">
                        <div
                            class="absolute top-0 right-0 w-64 h-64 bg-emerald-600 rounded-full blur-[100px] opacity-10 group-hover:opacity-20 transition-opacity">
                        </div>

                        <div class="flex items-center gap-6 mb-12">
                            <div
                                class="w-14 h-14 rounded-2xl bg-white/10 text-white flex items-center justify-center border border-white/10 shadow-xl group-hover:rotate-6 transition-transform">
                                <i data-lucide="list-tree" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-white text-xl tracking-tight">กำหนดสายการอนุมัติใบลา</h3>
                                <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mt-1 italic">Define
                                    Decision Hierarchy</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            <!-- Approver 1 -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black">01</span>
                                    <label
                                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest">ผู้อนุมัติขั้นที่
                                        1 (หัวหน้าแผนก)</label>
                                </div>
                                <select name="supervisor_id"
                                    class="w-full px-6 py-4 rounded-2xl border-white/5 bg-white/5 focus:bg-white/10 focus:border-emerald-500 text-white font-bold transition-all appearance-none cursor-pointer">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supervisor_id') == $sup->id ? 'selected' : '' }} class="bg-slate-900">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Deputy -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black">02</span>
                                    <label
                                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest">ผู้รับทราบ
                                        (รอง ผอ.)</label>
                                </div>
                                <select name="deputy_id"
                                    class="w-full px-6 py-4 rounded-2xl border-white/5 bg-white/5 focus:bg-white/10 focus:border-blue-500 text-white font-bold transition-all appearance-none cursor-pointer">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('deputy_id') == $sup->id ? 'selected' : '' }}
                                            class="bg-slate-900">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Approver 2 -->
                            <div class="space-y-4 md:col-span-2">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-black">03</span>
                                    <label
                                        class="text-[11px] font-black text-slate-400 uppercase tracking-widest">ผู้อนุมัติขั้นที่
                                        2 (ผอ.) สำหรับลาพักผ่อน</label>
                                </div>
                                <select name="manager_id"
                                    class="w-full px-6 py-4 rounded-2xl border-white/5 bg-white/5 focus:bg-white/10 focus:border-amber-500 text-white font-bold transition-all appearance-none cursor-pointer">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ old('manager_id') == $sup->id ? 'selected' : '' }}
                                            class="bg-slate-900">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Security & Action Buttons -->
                <div class="lg:col-span-4 space-y-10">

                    <!-- Section: Security -->
                    <div
                        class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-50 overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-50 bg-emerald-50/50 flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="key" class="w-5 h-5"></i>
                            </div>
                            <h3 class="font-black text-slate-800 text-sm tracking-tight uppercase">การตั้งค่าบัญชีระบบ
                            </h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">อีเมลเข้าใช้งาน
                                    <span class="text-rose-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-6 py-3.5 rounded-xl border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 font-bold text-slate-700 transition-all"
                                    placeholder="example@navy.mi.th">
                                @error('email') <p class="text-[9px] font-bold text-rose-500 mt-1 ml-1">{{ $message }}
                                </p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">กำหนดรหัสผ่าน
                                    <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required
                                    class="w-full px-6 py-3.5 rounded-xl border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 font-bold text-slate-700 transition-all"
                                    placeholder="********">
                                @error('password') <p class="text-[9px] font-bold text-rose-500 mt-1 ml-1">
                                    {{ $message }}
                                </p> @enderror
                            </div>

                            <div class="space-y-2 pt-4 border-t border-slate-50">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">ระดับสิทธิ์
                                    (System Role)</label>
                                <select name="role" required
                                    class="w-full px-6 py-3.5 rounded-xl border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 font-black text-slate-700 transition-all cursor-pointer">
                                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>
                                        ข้าราชการทั่วไป</option>
                                    <option value="department_head" {{ old('role') == 'department_head' ? 'selected' : '' }}>หัวหน้าแผนก</option>
                                    <option value="deputy_director" {{ old('role') == 'deputy_director' ? 'selected' : '' }}>รอง ผอ.</option>
                                    <option value="director" {{ old('role') == 'director' ? 'selected' : '' }}>ผอ.
                                        (Director)</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้ดูแลระบบ
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Quota -->
                    <div class="bg-emerald-600 rounded-[3rem] shadow-2xl shadow-emerald-200/60 p-8 text-white group">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform">
                                <i data-lucide="sparkles" class="w-6 h-6"></i>
                            </div>
                            <h3 class="font-black text-lg tracking-tight uppercase">สิทธิ์การลาเริ่มต้น</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="relative">
                                <input type="number" step="0.5" name="vacation_leave_days"
                                    value="{{ old('vacation_leave_days', 10) }}"
                                    class="w-full px-6 py-5 rounded-[1.5rem] bg-white/10 border-white/20 focus:bg-white focus:text-slate-900 text-white font-black text-4xl transition-all shadow-inner">
                                <span
                                    class="absolute right-6 top-1/2 -translate-y-1/2 font-black text-white/30 uppercase tracking-widest">วัน/ปี</span>
                            </div>
                            <div class="flex items-start gap-4 p-4 bg-white/10 rounded-2xl">
                                <i data-lucide="calendar-plus" class="w-6 h-6 text-white/40 flex-shrink-0"></i>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-white/50 uppercase tracking-widest">
                                        วันที่เริ่มบรรจุ/ทำงาน</p>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                                        class="bg-transparent border-none p-0 text-white font-black focus:ring-0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Action Hub -->
                    <div class="pt-8 space-y-4">
                        <button type="submit"
                            class="w-full py-6 bg-slate-900 hover:bg-black text-white font-black text-xl rounded-[2.5rem] shadow-2xl transition-all hover:-translate-y-2 active:scale-95 flex items-center justify-center gap-3">
                            <i data-lucide="user-plus" class="w-6 h-6"></i>
                            บันทึกพนักงานใหม่
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>window.addEventListener('load', () => window.lucide.createIcons());</script>
    @endpush
</x-app-layout>
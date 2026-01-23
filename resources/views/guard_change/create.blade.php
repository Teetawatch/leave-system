<x-app-layout>
    @section('title', 'ขออนุญาตเปลี่ยนยาม')

    <div class="min-h-screen bg-[#f8fafc] pb-20" x-data="guardChangeForm()">
        <!-- Cinematic Header -->
        <div class="relative bg-[#0f172a] pt-16 pb-24 overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0">
                <div
                    class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[120px] -mr-48 -mt-48">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/10 rounded-full blur-[100px] -ml-24 -mb-24">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <nav
                    class="flex justify-center items-center gap-2 text-emerald-300/60 transition-all mb-4 text-sm font-bold tracking-widest uppercase">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    <span>Duty Management</span>
                    <span class="w-1 h-1 rounded-full bg-emerald-500/40"></span>
                    <span class="text-emerald-400">Request New Change</span>
                </nav>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-4">
                    แบบฟอร์มขออนุญาตเปลี่ยนยาม
                </h1>
                <p class="text-indigo-100/60 max-w-2xl mx-auto text-lg font-medium">
                    กรุณากรอกข้อมูลการเปลี่ยนเวรยามให้ครบถ้วน เพื่อดำเนินการส่งขออนุมัติไปยังผู้บังคับบัญชาตามลำดับ
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Main Form Column -->
                <div class="lg:col-span-8 space-y-8">
                    <form action="{{ route('guard-change.store') }}" method="POST" id="guardChangeForm">
                        @csrf

                        <!-- Section 1: Replacement User Selection -->
                        <div
                            class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 relative overflow-hidden group">
                            <!-- Background Decoration -->
                            <div
                                class="absolute top-0 right-0 w-40 h-40 bg-slate-50 rounded-bl-[4rem] -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                            </div>

                            <div class="relative z-10">
                                <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                                        <i data-lucide="user-plus" class="w-6 h-6"></i>
                                    </div>
                                    เลือกผู้ปฏิบัติหน้าที่แทน
                                </h3>

                                <div class="relative" x-data="userAutocomplete()">
                                    <input type="hidden" name="replacement_user_id" x-model="replacementUserId">

                                    <!-- Search Input -->
                                    <div class="relative group/input">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-indigo-500 transition-colors">
                                            <i data-lucide="search" class="w-5 h-5"></i>
                                        </div>
                                        <input type="text" x-model="searchQuery" @focus="isOpen = true"
                                            @click="isOpen = true" @input="filterUsers()"
                                            @keydown.escape="isOpen = false"
                                            @keydown.arrow-down.prevent="highlightNext()"
                                            @keydown.arrow-up.prevent="highlightPrev()"
                                            @keydown.enter.prevent="selectHighlighted()"
                                            placeholder="ค้นหาชื่อ หรือตำแหน่ง..."
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all py-5 pl-14 pr-12 text-lg font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium"
                                            autocomplete="off">

                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" x-show="selectedUser"
                                                class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center transition-colors">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Selected User Badge -->
                                    <template x-if="selectedUser">
                                        <div
                                            class="mt-4 flex flex-col sm:flex-row items-center gap-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-indigo-200">
                                                <span x-text="selectedUser?.name.substring(0, 1)"></span>
                                            </div>
                                            <div class="flex-1 text-center sm:text-left">
                                                <p class="text-sm font-black text-indigo-900"
                                                    x-text="selectedUser?.rank + selectedUser?.name"></p>
                                                <p class="text-xs font-bold text-indigo-600/60 uppercase tracking-widest mt-0.5"
                                                    x-text="selectedUser?.position || 'บุคลากร'"></p>
                                            </div>
                                            <div
                                                class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-xs font-black flex items-center gap-2 shadow-lg shadow-emerald-200">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                                READY TO REPLACE
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Dropdown Results -->
                                    <div x-show="isOpen && filteredUsers.length > 0" x-cloak
                                        @click.away="isOpen = false"
                                        class="absolute left-0 right-0 mt-3 bg-white rounded-3xl shadow-2xl border border-slate-100 max-h-80 overflow-y-auto z-[100] p-2">
                                        <template x-for="(user, index) in filteredUsers" :key="user.id">
                                            <div @click="selectUser(user)" @mouseenter="highlightedIndex = index"
                                                :class="{'bg-indigo-50 text-indigo-700': highlightedIndex === index, 'hover:bg-slate-50': highlightedIndex !== index}"
                                                class="p-4 cursor-pointer transition-all rounded-2xl flex items-center gap-4 mb-1 last:mb-0">
                                                <div
                                                    class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 font-bold text-lg flex-shrink-0 group-hover:scale-110 transition-transform">
                                                    <span x-text="user.name.substring(0, 1)"></span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-black text-slate-800" x-text="user.rank + user.name">
                                                    </p>
                                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-0.5"
                                                        x-text="user.position || 'บุคลากร'"></p>
                                                </div>
                                                <i data-lucide="chevron-right"
                                                    class="w-4 h-4 opacity-0 transition-opacity"
                                                    :class="{'opacity-100': highlightedIndex === index}"></i>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- No Results -->
                                    <div x-show="isOpen && searchQuery.length > 0 && filteredUsers.length === 0" x-cloak
                                        class="absolute left-0 right-0 mt-3 bg-white rounded-3xl shadow-2xl border border-slate-100 p-10 text-center z-[100]">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i data-lucide="user-x" class="w-8 h-8 text-slate-300"></i>
                                        </div>
                                        <p class="text-slate-500 font-bold">ไม่พบข้อมูลรายชื่อที่ค้นหา</p>
                                        <p class="text-slate-400 text-sm mt-1">กรุณาลองพิมพ์ชื่อหรือตำแหน่งใหม่อีกครั้ง
                                        </p>
                                    </div>
                                </div>

                                @error('replacement_user_id')
                                    <div
                                        class="mt-4 flex items-center gap-2 text-rose-500 font-bold text-sm bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 2: Duty Position Selection -->
                        <div
                            class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 relative mt-8 overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-[4rem] -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                            </div>

                            <div class="relative z-10">
                                <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                                        <i data-lucide="award" class="w-6 h-6"></i>
                                    </div>
                                    ระบุตำแหน่งเวรยาม
                                </h3>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                    @foreach($dutyPositions as $key => $label)
                                        <label class="cursor-pointer group/card relative">
                                            <input type="radio" name="duty_position" value="{{ $key }}" class="peer sr-only"
                                                x-model="dutyPosition" required>
                                            <div
                                                class="h-full p-6 rounded-3xl bg-slate-50 border-2 border-slate-100 group-hover/card:bg-white group-hover/card:border-emerald-200 group-hover/card:shadow-xl group-hover/card:shadow-emerald-500/5 transition-all duration-300 text-center peer-checked:bg-white peer-checked:border-emerald-500 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-checked:scale-[1.05]">
                                                <div
                                                    class="w-14 h-14 mx-auto rounded-2xl bg-white shadow-sm flex items-center justify-center mb-4 group-hover/card:scale-110 transition-transform duration-300 text-emerald-500 border border-slate-100 group-hover/card:border-emerald-100">
                                                    @if($key == 'senior_duty_officer')
                                                        <i data-lucide="star" class="w-7 h-7"></i>
                                                    @elseif($key == 'duty_officer')
                                                        <i data-lucide="shield" class="w-7 h-7"></i>
                                                    @else
                                                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                                                    @endif
                                                </div>
                                                <p
                                                    class="text-sm font-black text-slate-700 tracking-tight leading-tight px-2">
                                                    {{ $label }}</p>

                                                <div
                                                    class="absolute top-4 right-4 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all scale-0 peer-checked:scale-100 shadow-lg shadow-emerald-500/30">
                                                    <i data-lucide="check" class="w-4 h-4"></i>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('duty_position')
                                    <div
                                        class="mt-6 flex items-center gap-2 text-rose-500 font-bold text-sm bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 3: Duty Date & Remarks -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                            <!-- Duty Date -->
                            <div
                                class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 group relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-[3rem] -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                                </div>

                                <div class="relative z-10 h-full flex flex-col">
                                    <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                                            <i data-lucide="calendar" class="w-6 h-6"></i>
                                        </div>
                                        วันที่เข้าเวร
                                    </h3>

                                    <div class="flex-1 flex flex-col justify-center">
                                        <div
                                            class="group/input focus-within:ring-4 focus-within:ring-blue-500/10 rounded-3xl overflow-hidden transition-all">
                                            <label
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">SELECT
                                                DATE</label>
                                            <input type="date" name="duty_date" x-model="dutyDate"
                                                class="block w-full bg-slate-50 border-2 border-slate-100 group-focus-within/input:bg-white group-focus-within/input:border-blue-500 p-6 rounded-3xl text-xl font-black text-slate-800 transition-all cursor-pointer"
                                                required>
                                        </div>
                                    </div>

                                    @error('duty_date')
                                        <div
                                            class="mt-6 flex items-center gap-2 text-rose-500 font-bold text-sm bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div
                                class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 group relative overflow-hidden">
                                <div
                                    class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-[3rem] -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none">
                                </div>

                                <div class="relative z-10 h-full flex flex-col">
                                    <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm">
                                            <i data-lucide="message-square" class="w-6 h-6"></i>
                                        </div>
                                        เหตุผล / หมายเหตุ
                                    </h3>

                                    <div class="flex-1 flex flex-col justify-center">
                                        <div
                                            class="group/input focus-within:ring-4 focus-within:ring-purple-500/10 rounded-3xl overflow-hidden transition-all">
                                            <label
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">REASON
                                                (OPTIONAL)</label>
                                            <textarea name="remarks" rows="2" x-model="remarks"
                                                class="block w-full bg-slate-50 border-2 border-slate-100 group-focus-within/input:bg-white group-focus-within/input:border-purple-500 p-6 rounded-3xl text-lg font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium transition-all resize-none"
                                                placeholder="ระบุเหตุผล เช่น ไปราชการ กทม..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Submit -->
                        <div class="mt-12 flex flex-col items-center">
                            <button type="submit"
                                class="w-full sm:w-auto min-w-[300px] flex items-center justify-center gap-4 px-10 py-6 bg-slate-900 hover:bg-indigo-600 text-white font-black text-xl rounded-full shadow-2xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all duration-300 hover:-translate-y-2 group">
                                <i data-lucide="send"
                                    class="w-6 h-6 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                ยืนยันและส่งคำขอเปลี่ยนยาม
                            </button>
                            <p class="text-slate-400 font-bold text-sm mt-6 flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                ข้อมูลจะถูกส่งไปยังระบบเพื่อดำเนินการตามขั้นตอนการอนุมัติ
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Visual Summary -->
                <div class="lg:col-span-4 lg:sticky lg:top-8">
                    <div class="bg-[#0f172a] rounded-[2.5rem] p-8 shadow-2xl overflow-hidden relative group">
                        <!-- Abstract Background -->
                        <div class="absolute inset-0 opacity-20 pointer-events-none">
                            <div
                                class="absolute top-0 right-0 w-40 h-40 bg-indigo-500 rounded-full blur-[60px] -mr-20 -mt-20">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 w-40 h-40 bg-emerald-500 rounded-full blur-[60px] -ml-20 -mb-20">
                            </div>
                        </div>

                        <div class="relative z-10">
                            <h4
                                class="text-xs font-black text-indigo-300/60 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                                <span class="w-8 h-px bg-indigo-500/30"></span>
                                Live Preview
                            </h4>

                            <div class="space-y-10">
                                <!-- Status Badge -->
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-white/10 rounded-full">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-white uppercase tracking-widest">DRAFTING
                                        STATUS</span>
                                </div>

                                <!-- Main Visual Card -->
                                <div
                                    class="bg-gradient-to-br from-white/10 to-white/5 rounded-3xl p-6 border border-white/10 backdrop-blur-sm">
                                    <div class="flex items-start justify-between mb-10">
                                        <div>
                                            <p
                                                class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-1">
                                                Position Duty</p>
                                            <p class="text-xl font-black text-white leading-tight"
                                                x-text="getDutyPositionName() || 'โปรดเลือกตำแหน่ง...'"></p>
                                        </div>
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-indigo-500/20 text-indigo-300 flex items-center justify-center border border-indigo-500/30">
                                            <i data-lucide="shield" class="w-6 h-6"></i>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center border border-white/10">
                                                <i data-lucide="calendar" class="w-5 h-5 text-indigo-300"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[10px] font-black text-indigo-300/40 uppercase tracking-widest">
                                                    Target Date</p>
                                                <p class="text-sm font-bold text-white mt-0.5"
                                                    x-text="formatDate(dutyDate) || 'ยังไม่กำหนด'"></p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center border border-white/10">
                                                <i data-lucide="repeat" class="w-5 h-5 text-emerald-400"></i>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-[10px] font-black text-indigo-300/40 uppercase tracking-widest">
                                                    Replacement</p>
                                                <p class="text-sm font-bold text-white mt-0.5"
                                                    x-text="searchQuery || 'รอระบุรายชื่อ...'"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dash Line -->
                                    <div class="my-6 border-t border-dashed border-white/10"></div>

                                    <div>
                                        <p
                                            class="text-[10px] font-black text-indigo-300/40 uppercase tracking-widest mb-2 flex items-center gap-2">
                                            <i data-lucide="message-square" class="w-3 h-3"></i>
                                            Remarks
                                        </p>
                                        <p class="text-sm text-white/60 font-medium italic break-words"
                                            x-text="remarks || '- ไม่ระบุหมายเหตุ -'"></p>
                                    </div>
                                </div>

                                <!-- Security Tip -->
                                <div class="bg-indigo-500/10 rounded-2xl p-5 border border-indigo-500/20">
                                    <div class="flex gap-4">
                                        <i data-lucide="shield-alert" class="w-6 h-6 text-indigo-400 flex-shrink-0"></i>
                                        <p class="text-xs font-bold text-indigo-100/60 leading-relaxed italic">
                                            "กรุณาตรวจสอบข้อมูลและปรึกษาผู้รับหน้าที่แทนก่อนส่งคำขอ
                                            เพื่อความถูกต้องในการปฏิบัติหน้าที่เวรยาม"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine Logic & Lucide -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('guardChangeForm', () => ({
                replacementUserId: '{{ old("replacement_user_id") }}',
                dutyPosition: '{{ old("duty_position") }}',
                dutyDate: '{{ old("duty_date") }}',
                remarks: '{{ old("remarks") }}',
                dutyPositions: @json($dutyPositions),

                getDutyPositionName() {
                    if (!this.dutyPosition) return null;
                    return this.dutyPositions[this.dutyPosition] || null;
                },

                formatDate(dateString) {
                    if (!dateString) return null;
                    const date = new Date(dateString);
                    return date.toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }
            }));

            Alpine.data('userAutocomplete', () => ({
                searchQuery: '',
                isOpen: false,
                highlightedIndex: 0,
                selectedUser: null,
                replacementUserId: '{{ old("replacement_user_id") }}',
                users: [
                    @foreach($users as $user)
                        {
                            id: {{ $user->id }},
                            rank: "{{ $user->rank }}",
                            name: "{{ $user->name }}",
                            position: "{{ $user->position ?? 'ไม่ระบุตำแหน่ง' }}",
                            display: "{{ $user->rank }}{{ $user->name }}"
                        },
                    @endforeach
                ],
                filteredUsers: [],

                init() {
                    this.filteredUsers = this.users;
                    if (this.replacementUserId) {
                        const existingUser = this.users.find(u => u.id == this.replacementUserId);
                        if (existingUser) {
                            this.selectUser(existingUser);
                        }
                    }
                },

                filterUsers() {
                    const query = this.searchQuery.toLowerCase().trim();
                    if (query.length === 0) {
                        this.filteredUsers = this.users;
                    } else {
                        this.filteredUsers = this.users.filter(user =>
                            (user.rank + user.name).toLowerCase().includes(query) ||
                            (user.position && user.position.toLowerCase().includes(query))
                        );
                    }
                    this.highlightedIndex = 0;
                    this.isOpen = true;
                },

                selectUser(user) {
                    this.selectedUser = user;
                    this.replacementUserId = user.id;
                    this.searchQuery = user.rank + user.name;
                    this.isOpen = false;
                    // Sync to parent component
                    this.$root.__x.$data.replacementUserId = user.id;
                },

                clearSelection() {
                    this.selectedUser = null;
                    this.replacementUserId = '';
                    this.searchQuery = '';
                    this.filteredUsers = this.users;
                    this.$root.__x.$data.replacementUserId = '';
                },

                highlightNext() {
                    if (this.highlightedIndex < this.filteredUsers.length - 1) {
                        this.highlightedIndex++;
                    }
                },

                highlightPrev() {
                    if (this.highlightedIndex > 0) {
                        this.highlightedIndex--;
                    }
                },

                selectHighlighted() {
                    if (this.filteredUsers.length > 0) {
                        this.selectUser(this.filteredUsers[this.highlightedIndex]);
                    }
                }
            }));
        })
    </script>
</x-app-layout>
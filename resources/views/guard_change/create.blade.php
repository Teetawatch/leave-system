<x-app-layout>
    @section('title', 'ขออนุญาตเปลี่ยนยาม')

    <div class="max-w-4xl mx-auto py-10" x-data="guardChangeForm()">
        
        <!-- Premium Header -->
        <div class="mb-10 text-center">
            <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-3">แบบฟอร์มขออนุญาตเปลี่ยนยาม</h2>
            <p class="text-slate-500 text-lg">กรอกข้อมูลให้ครบถ้วนเพื่อส่งเรื่องขอเปลี่ยนเวรยาม</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Form Column -->
            <div class="lg:col-span-8 space-y-8">
                
                <form action="{{ route('guard-change.store') }}" method="POST" id="guardChangeForm" style="position: relative; z-index: 1;">
                    @csrf

                    <!-- Section 1: Replacement User Selection -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative group hover:shadow-md transition-all duration-300">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="users" class="w-12 h-12 text-brand-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 text-brand-600 text-lg">
                                <i data-lucide="user-plus" class="w-5 h-5"></i>
                            </span>
                            เลือกผู้ที่จะมาเปลี่ยนแทน
                        </h3>
                        
                        <div class="relative" x-data="userAutocomplete()" style="z-index: 100;">
                            <input type="hidden" name="replacement_user_id" x-model="replacementUserId">
                            
                            <!-- Search Input -->
                            <div class="relative">
                                <input type="text" 
                                    x-model="searchQuery"
                                    @focus="isOpen = true"
                                    @click="isOpen = true"
                                    @input="filterUsers()"
                                    @keydown.escape="isOpen = false"
                                    @keydown.arrow-down.prevent="highlightNext()"
                                    @keydown.arrow-up.prevent="highlightPrev()"
                                    @keydown.enter.prevent="selectHighlighted()"
                                    placeholder="พิมพ์ชื่อเพื่อค้นหา..."
                                    class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-4 text-base pr-12"
                                    autocomplete="off">
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4" x-show="!selectedUser"></i>
                                    <button type="button" @click="clearSelection()" x-show="selectedUser" class="hover:text-rose-500 transition-colors">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Selected User Badge -->
                            <div x-show="selectedUser" x-cloak class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-brand-50 text-brand-700 rounded-xl text-sm font-bold border border-brand-100">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                                <span x-text="selectedUser?.display"></span>
                            </div>

                            <!-- Dropdown Results -->
                            <div x-show="isOpen && filteredUsers.length > 0" 
                                 x-cloak
                                 @click.away="isOpen = false"
                                 class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 max-h-64 overflow-y-auto"
                                 style="z-index: 9999;">
                                <template x-for="(user, index) in filteredUsers" :key="user.id">
                                    <div @click="selectUser(user)"
                                         @mouseenter="highlightedIndex = index"
                                         :class="{'bg-brand-50 text-brand-700': highlightedIndex === index, 'hover:bg-slate-50': highlightedIndex !== index}"
                                         class="px-4 py-3 cursor-pointer transition-colors border-b border-slate-50 last:border-0 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-sm flex-shrink-0">
                                            <span x-text="user.name.substring(0, 1)"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-slate-800 truncate" x-text="user.rank + user.name"></p>
                                            <p class="text-xs text-slate-500 truncate" x-text="user.position || 'ไม่ระบุตำแหน่ง'"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- No Results -->
                            <div x-show="isOpen && searchQuery.length > 0 && filteredUsers.length === 0" 
                                 x-cloak
                                 class="absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 p-6 text-center"
                                 style="z-index: 9999;">
                                <i data-lucide="user-x" class="w-8 h-8 text-slate-300 mb-2"></i>
                                <p class="text-slate-500 font-medium">ไม่พบผลลัพธ์</p>
                            </div>
                        </div>
                        @error('replacement_user_id') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg inline-block border border-rose-100">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section 2: Duty Position Selection -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 group hover:shadow-md transition-all duration-300 mt-6" style="position: relative; z-index: 1;">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="shield" class="w-4 h-4 text-8xl text-emerald-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 text-lg">
                                <i data-lucide="badge" class="w-4 h-4"></i>
                            </span>
                            เลือกตำแหน่งเวรยาม
                        </h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 relative z-10">
                            @foreach($dutyPositions as $key => $label)
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="duty_position" value="{{ $key }}" class="peer sr-only" x-model="dutyPosition" required>
                                <div class="px-6 py-6 rounded-2xl border-2 border-slate-100 bg-slate-50/50 hover:bg-white hover:border-emerald-200 hover:shadow-lg transition-all duration-200 text-center peer-checked:border-emerald-500 peer-checked:bg-white peer-checked:shadow-xl peer-checked:shadow-emerald-500/10 peer-checked:scale-[1.02]">
                                    <div class="w-12 h-12 mx-auto rounded-2xl bg-white shadow-sm flex items-center justify-center text-xl mb-3 group-hover:scale-110 transition-transform duration-300 text-emerald-500">
                                        @if($key == 'senior_duty_officer')
                                            <i data-lucide="star" class="w-5 h-5"></i>
                                        @elseif($key == 'duty_officer')
                                            <i data-lucide="shield" class="w-5 h-5"></i>
                                        @else
                                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                                        @endif
                                    </div>
                                    <h4 class="text-base font-bold text-slate-800">{{ $label }}</h4>
                                    
                                    <div class="absolute top-3 right-3 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity shadow-lg scale-0 peer-checked:scale-100">
                                        <i data-lucide="check" class="w-4 h-4 text-xs"></i>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('duty_position') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg inline-block border border-rose-100">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section 3: Duty Date -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="calendar-check" class="w-12 h-12 text-blue-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-50 text-blue-600 text-lg">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                            </span>
                            วันที่เข้าเวร
                        </h3>

                        <div class="relative z-10">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-slate-300 transition-colors focus-within:ring-2 focus-within:ring-brand-500/20 focus-within:border-brand-500">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">เลือกวันที่</label>
                                <input type="date" name="duty_date" x-model="dutyDate" class="bg-transparent border-0 p-0 text-slate-800 font-bold text-lg w-full focus:ring-0 cursor-pointer" required>
                            </div>
                        </div>
                        @error('duty_date') <p class="text-rose-500 text-sm mt-3 font-medium bg-rose-50 px-3 py-2 rounded-lg block border border-rose-100">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section 4: Remarks -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-md transition-all duration-300 mt-6">
                         <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none group-hover:opacity-10 transition-opacity">
                            <i data-lucide="align-left" class="w-8 h-8 text-8xl text-purple-900"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3 relative z-10">
                            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-purple-50 text-purple-600 text-lg">
                                <i data-lucide="pencil" class="w-5 h-5"></i>
                            </span>
                            หมายเหตุ / เหตุผล
                        </h3>

                        <div class="relative z-10">
                            <textarea name="remarks" rows="3" x-model="remarks" class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition-all p-4 text-base resize-none" placeholder="ระบุเหตุผลหรือสถานที่ที่จะไป เช่น ไปราชการ กทม., ลาพักผ่อน เป็นต้น"></textarea>
                        </div>
                    </div>

                    <!-- Submit Action -->
                    <div class="mt-8">
                        <button type="submit" class="w-full py-5 bg-slate-900 hover:bg-brand-600 text-white font-bold text-xl rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-brand-500/20 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 group">
                            <span>ส่งคำขอเปลี่ยนยาม</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </button>
                         <p class="text-center text-slate-400 text-sm mt-4">
                            เมื่อกดยืนยัน ระบบจะบันทึกคำขอเปลี่ยนยามของคุณ
                        </p>
                    </div>
                </form>
            </div>

            <!-- Right Column: Summary Sticky -->
            <div class="lg:col-span-4 space-y-6">
                <div class="sticky top-8 space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white rounded-3xl p-6 shadow-lg shadow-slate-200/50 border border-slate-100">
                         <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-50">
                            <h4 class="font-bold text-slate-800 text-lg">สรุปรายการ</h4>
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-1 rounded-lg">Draft</span>
                        </div>
                        
                        <!-- Visual Card -->
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 relative overflow-hidden">
                             <!-- Decorative Circles -->
                            <div class="absolute -left-3 top-1/2 -mt-3 w-6 h-6 bg-white rounded-full border border-slate-100"></div>
                            <div class="absolute -right-3 top-1/2 -mt-3 w-6 h-6 bg-white rounded-full border border-slate-100"></div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-slate-400 font-bold uppercase mb-1">ตำแหน่งเวร</p>
                                    <p class="text-lg font-bold text-slate-800" x-text="getDutyPositionName() || '...'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold uppercase mb-1">วันที่เข้าเวร</p>
                                    <p class="text-sm font-bold text-slate-800" x-text="formatDate(dutyDate) || '...'"></p>
                                </div>
                                 <div class="pt-4 border-t border-dashed border-slate-200">
                                    <p class="text-xs text-slate-400 font-bold uppercase mb-1">หมายเหตุ</p>
                                    <p class="text-sm font-medium text-slate-600" x-text="remarks || '-'"></p>
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
            Alpine.data('guardChangeForm', () => ({
                replacementUserId: '',
                dutyPosition: '',
                dutyDate: '',
                remarks: '',
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
                replacementUserId: '',
                users: [
                    @foreach($users as $user)
                    { id: {{ $user->id }}, rank: "{{ $user->rank }}", name: "{{ $user->name }}", position: "{{ $user->position ?? 'ไม่ระบุตำแหน่ง' }}", display: "{{ $user->rank }}{{ $user->name }} - {{ $user->position ?? 'ไม่ระบุตำแหน่ง' }}" },
                    @endforeach
                ],
                filteredUsers: [],
                dropdownStyle: '',

                init() {
                    this.filteredUsers = this.users;
                },

                openDropdown() {
                    this.isOpen = true;
                    this.updateDropdownPosition();
                },

                updateDropdownPosition() {
                    const input = this.$refs.searchInput;
                    if (input) {
                        const rect = input.getBoundingClientRect();
                        this.dropdownStyle = `top: ${rect.bottom + 8}px; left: ${rect.left}px; width: ${rect.width}px;`;
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
                },

                selectUser(user) {
                    this.selectedUser = user;
                    this.replacementUserId = user.id;
                    this.searchQuery = user.rank + user.name;
                    this.isOpen = false;
                },

                clearSelection() {
                    this.selectedUser = null;
                    this.replacementUserId = '';
                    this.searchQuery = '';
                    this.filteredUsers = this.users;
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

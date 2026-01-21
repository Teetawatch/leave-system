<x-app-layout>
    @section('title', 'จัดการพนักงาน')

    <div class="max-w-[95rem] mx-auto py-8 sm:px-6 lg:px-8" 
         x-data="{ 
            viewMode: localStorage.getItem('employeeViewMode') || 'grid',
            toggleView(mode) {
                this.viewMode = mode;
                localStorage.setItem('employeeViewMode', mode);
            },
            showOfficialDutyModal: false,
            selectedEmployee: { id: '', name: '' },
            openOfficialDutyModal(id, name) {
                this.selectedEmployee = { id: id, name: name };
                this.showOfficialDutyModal = true;
            }
         }">
        
        <!-- Header & Stats -->
        <div class="mb-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">กำลังพลทั้งหมด</h1>
                    <p class="text-slate-500 mt-1">จัดการข้อมูลรายชื่อ ตำแหน่ง และสิทธิ์การใช้งาน</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('employees.export') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 font-medium hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                        Export
                    </a>
                    
                    <a href="{{ route('employees.import') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 font-medium hover:bg-slate-50 hover:text-emerald-600 transition-colors shadow-sm">
                        <i data-lucide="upload" class="w-4 h-4 mr-2"></i>
                        Import
                    </a>

                    <a href="{{ route('employees.create') }}" class="inline-flex items-center px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        เพิ่มพนักงาน
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Employees -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between group hover:border-indigo-100 transition-all">
                    <div>
                        <p class="text-sm font-medium text-slate-500">พนักงานทั้งหมด</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-1 group-hover:text-indigo-600 transition-colors">
                            {{ \App\Models\User::count() }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>

                <!-- Departments -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between group hover:border-emerald-100 transition-all">
                    <div>
                        <p class="text-sm font-medium text-slate-500">แผนกทั้งหมด</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-1 group-hover:text-emerald-600 transition-colors">
                            {{ $departments->count() }}
                        </h3>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="building-2" class="w-6 h-6"></i>
                    </div>
                </div>

                <!-- Pending -->
                @php
                    $pendingCount = \App\Models\User::where('is_registered', true)->where('registration_status', 'pending')->count();
                @endphp
                <a href="{{ route('employees.pending-registrations') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center justify-between group hover:border-amber-100 transition-all relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-sm font-medium text-slate-500">รออนุมัติลงทะเบียน</p>
                        <h3 class="text-3xl font-bold text-slate-800 mt-1 group-hover:text-amber-500 transition-colors">
                            {{ $pendingCount }}
                        </h3>
                    </div>
                    <div class="relative z-10 w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="user-plus" class="w-6 h-6"></i>
                    </div>
                    @if($pendingCount > 0)
                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
                    @endif
                </a>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6 flex flex-col lg:flex-row items-center justify-between gap-4 sticky top-24 z-30">
            <!-- Search & Filter -->
            <form action="{{ route('employees.index') }}" method="GET" class="w-full lg:w-auto flex flex-col sm:flex-row gap-3 flex-1">
                <div class="relative flex-1 max-w-lg">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-0 rounded-xl text-sm transition-all" 
                        placeholder="ค้นหาชื่อ, อีเมล, ตำแหน่ง...">
                </div>

                <div class="relative min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="filter" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <select name="department" onchange="this.form.submit()" class="block w-full pl-10 pr-10 py-2.5 bg-slate-50 border-transparent focus:bg-white focus:border-indigo-500 focus:ring-0 rounded-xl text-sm appearance-none cursor-pointer transition-all">
                        <option value="">ทุกแผนก</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-slate-500">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </form>

            <!-- View Toggle & Bulk Action -->
            <div class="flex items-center gap-3 w-full lg:w-auto justify-between lg:justify-end">
                <button id="bulk-delete-btn" class="hidden items-center px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 font-medium rounded-xl transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                    ลบ <span id="selected-count" class="mx-1 font-bold">0</span> คน
                </button>

                <div class="flex bg-slate-100 p-1 rounded-xl">
                    <button @click="toggleView('grid')" :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="p-2 rounded-lg transition-all">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </button>
                    <button @click="toggleView('list')" :class="viewMode === 'list' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="p-2 rounded-lg transition-all">
                        <i data-lucide="list" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="min-h-[500px]">
            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                @foreach($employees as $emp)
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-md hover:border-indigo-100 transition-all group relative">
                    
                    <!-- Selection Checkbox (Absolute) -->
                    <div class="absolute top-4 right-4 z-10">
                        <input type="checkbox" name="selected_users[]" value="{{ $emp->id }}" class="user-checkbox rounded border-slate-300 text-indigo-600 focus:ring-0 w-5 h-5 cursor-pointer">
                    </div>

                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                             @if(isset($emp->avatar) && $emp->avatar)
                                <img class="h-16 w-16 rounded-2xl object-cover shadow-sm group-hover:scale-105 transition-transform" src="{{ asset('storage/'.$emp->avatar) }}" alt="">
                            @else
                                <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-bold shadow-sm group-hover:scale-105 transition-transform">
                                    {{ substr($emp->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0 pt-1">
                            <h3 class="text-base font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">
                                <a href="{{ route('employees.edit', $emp->id) }}">{{ $emp->name }}</a>
                            </h3>
                            <p class="text-sm text-slate-500 truncate mt-0.5">{{ $emp->position ?? 'เจ้าหน้าที่' }}</p>
                            
                            <div class="flex items-center gap-2 mt-2">
                                @php
                                    $roleColor = match($emp->role) {
                                        'admin' => 'bg-red-50 text-red-700 ring-red-600/10',
                                        'director' => 'bg-purple-50 text-purple-700 ring-purple-600/10',
                                        'deputy_director' => 'bg-violet-50 text-violet-700 ring-violet-600/10',
                                        'department_head' => 'bg-orange-50 text-orange-700 ring-orange-600/10',
                                        default => 'bg-slate-50 text-slate-600 ring-slate-500/10'
                                    };
                                    $roleLabel = match($emp->role) {
                                        'admin' => 'Admin',
                                        'director' => 'ผอ.',
                                        'deputy_director' => 'รอง ผอ.',
                                        'department_head' => 'หน. แผนก',
                                        'employee' => 'User',
                                        default => ucfirst($emp->role)
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium ring-1 ring-inset {{ $roleColor }}">
                                    {{ $roleLabel }}
                                </span>
                                @if($emp->department)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/10 truncate max-w-[100px]">
                                        {{ $emp->department }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3 text-sm text-slate-500">
                             <a href="mailto:{{ $emp->email }}" class="flex items-center hover:text-indigo-600 transition-colors" title="{{ $emp->email }}">
                                <i data-lucide="mail" class="w-4 h-4 mr-1.5"></i>
                                <span class="truncate max-w-[140px]">{{ $emp->email }}</span>
                            </a>
                        </div>
                        
                        <div class="flex items-center gap-1">
                            <button @click="openOfficialDutyModal('{{ $emp->id }}', '{{ $emp->name }}')" class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="บันทึกการไปราชการ">
                                <i data-lucide="plane" class="w-4 h-4"></i>
                            </button>
                            <a href="{{ route('employees.edit', $emp->id) }}" class="p-2 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- List View -->
            <div x-show="viewMode === 'list'" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left">
                                    <input type="checkbox" id="select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-0">
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">ตำแหน่ง/แผนก</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">สถานะ</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">หัวหน้างาน</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($employees as $emp)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="selected_users[]" value="{{ $emp->id }}" class="user-checkbox rounded border-slate-300 text-indigo-600 focus:ring-0">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if(isset($emp->avatar) && $emp->avatar)
                                                <img class="h-10 w-10 rounded-full object-cover shadow-sm bg-white" src="{{ asset('storage/'.$emp->avatar) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                                    {{ substr($emp->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                                {{ $emp->name }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $emp->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-slate-700 font-medium">{{ $emp->position ?? '-' }}</span>
                                        <span class="text-xs text-slate-500">{{ $emp->department ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        // Reuse role styling logic
                                        $roleColor = match($emp->role) {
                                            'admin' => 'bg-red-50 text-red-700 ring-red-600/10',
                                            'director' => 'bg-purple-50 text-purple-700 ring-purple-600/10',
                                            'deputy_director' => 'bg-violet-50 text-violet-700 ring-violet-600/10',
                                            'department_head' => 'bg-orange-50 text-orange-700 ring-orange-600/10',
                                            default => 'bg-slate-50 text-slate-600 ring-slate-500/10'
                                        };
                                        $roleLabel = match($emp->role) {
                                            'admin' => 'ผู้ดูแลระบบ',
                                            'director' => 'ผอ.',
                                            'deputy_director' => 'รอง ผอ.',
                                            'department_head' => 'หน. แผนก',
                                            'employee' => 'ข้าราชการ',
                                            default => $emp->role
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $roleColor }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($emp->supervisor)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 font-bold">
                                                {{ substr($emp->supervisor->name, 0, 1) }}
                                            </div>
                                            <div class="text-xs text-slate-600">{{ $emp->supervisor->name }}</div>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openOfficialDutyModal('{{ $emp->id }}', '{{ $emp->name }}')" class="text-slate-400 hover:text-blue-600 transition-colors inline-block p-1" title="ไปราชการ">
                                            <i data-lucide="plane" class="w-4 h-4"></i>
                                        </button>
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors inline-block p-1">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $employees->links() }}
            </div>
            
            <!-- Empty State -->
            @if($employees->count() === 0)
                <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-200 mt-6">
                    <div class="mx-auto w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="search-x" class="w-8 h-8 text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-medium text-slate-900">ไม่พบข้อมูลพนักงาน</h3>
                    <p class="text-slate-500 mt-1">ลองเปลี่ยนคำค้นหาหรือตัวกรองดูใหม่อีกครั้ง</p>
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center mt-4 text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                        <i data-lucide="refresh-ccw" class="w-4 h-4 mr-1.5"></i> ล้างค่าการค้นหา
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Scripts for Bulk Action -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            // Since we have multiple views, checkboxes might be in grid or list. 
            // We target all checkboxes with name 'selected_users[]'
            
            function getCheckboxes() {
                return document.querySelectorAll('input[name="selected_users[]"]');
            }

            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCountSpan = document.getElementById('selected-count');

            function updateBulkDeleteBtn() {
                const checkboxes = getCheckboxes();
                const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                selectedCountSpan.textContent = checkedCount;
                
                if (checkedCount > 0) {
                    bulkDeleteBtn.classList.remove('hidden');
                    bulkDeleteBtn.classList.add('inline-flex');
                } else {
                    bulkDeleteBtn.classList.add('hidden');
                    bulkDeleteBtn.classList.remove('inline-flex');
                }
            }

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    const checkboxes = getCheckboxes();
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateBulkDeleteBtn();
                });
            }

            // Delegation for dynamically toggled views (though Alpine handles visibility, elements exist in DOM)
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('user-checkbox')) {
                    const checkboxes = getCheckboxes();
                    if (!e.target.checked && selectAll) {
                        selectAll.checked = false;
                    } 
                    // Optional: check selectAll if all checked
                    updateBulkDeleteBtn();
                }
            });

            bulkDeleteBtn.addEventListener('click', async function() {
                if (!confirm('คุณแน่ใจหรือไม่ที่จะลบพนักงานที่เลือก? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
                    return;
                }

                const checkboxes = getCheckboxes();
                const selectedIds = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
                
                if (selectedIds.length === 0) return;

                try {
                    const response = await fetch('{{ route("employees.bulk-destroy") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                }
            });
        });
        <!-- Official Duty Modal -->
        <div x-show="showOfficialDutyModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" @click="showOfficialDutyModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                    
                    <div class="px-6 py-6 bg-slate-50 border-b border-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i data-lucide="plane" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">บันทึกการไปราชการ</h3>
                                    <p class="text-xs text-slate-500 font-medium" x-text="'ข้าราชการ: ' + selectedEmployee.name"></p>
                                </div>
                            </div>
                            <button @click="showOfficialDutyModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div>
                    </div>

                    <form :action="'/employees/' + selectedEmployee.id + '/official-duty'" method="POST" class="p-6 space-y-5">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">วันที่เริ่มต้น</label>
                                <input type="date" name="start_date" required 
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">วันที่สิ้นสุด</label>
                                <input type="date" name="end_date" required 
                                       class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">สถานที่ไปราชการ (จังหวัด)</label>
                            <input type="text" name="location" required 
                                   class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all"
                                   placeholder="เช่น กรุงเทพมหานคร">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">วัตถุประสงค์ / รายละเอียด</label>
                            <textarea name="reason" rows="3" required 
                                      class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-100 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:border-blue-500 focus:ring-0 transition-all resize-none"
                                      placeholder="เช่น เข้าร่วมงานสัมมนา..."></textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl shadow-xl shadow-blue-100 transition-all active:scale-95 flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                บันทึกข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Force re-init icons if needed
            if(window.lucide) lucide.createIcons();
        });
    </script>
    @endpush
</x-app-layout>

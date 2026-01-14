<x-app-layout>
    @section('title', 'จัดการพนักงาน (Employee Management)')

    <div class="max-w-[85rem] mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Premium Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6 relative">
            <div class="relative z-10 p-2">
                <div class="absolute -left-10 -top-10 w-32 h-32 bg-indigo-500 rounded-full blur-3xl opacity-10 pointer-events-none"></div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <i data-lucide="user" class="w-4 h-4s-gear"></i>
                    </span>
                    จัดการพนักงาน
                </h1>
                <p class="text-slate-500 mt-2 text-lg pl-1">บริหารจัดการข้อมูลผู้ใช้งานและสิทธิ์การเข้าถึง</p>
            </div>
            
            <a href="{{ route('employees.create') }}" class="group inline-flex items-center justify-center px-6 py-4 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-lg rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-300 transform hover:-translate-y-1">
                <div class="mr-3 bg-white/20 p-1.5 rounded-lg group-hover:bg-white/30 transition-colors">
                     <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                เพิ่มพนักงานใหม่
            </a>
            
            <div class="flex gap-3">
                <a href="{{ route('employees.import') }}" class="group inline-flex items-center justify-center px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i>
                    Import Excel
                </a>
                
                @php
                    $pendingCount = \App\Models\User::where('is_registered', true)->where('registration_status', 'pending')->count();
                @endphp
                
                <a href="{{ route('employees.pending-registrations') }}" class="group inline-flex items-center justify-center px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 relative">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                    รออนุมัติ
                    @if($pendingCount > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden relative">
            
            <!-- Decor -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

            <!-- Header & Search -->
            <div class="px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-800">รายชื่อพนักงานทั้งหมด</h3>
                    <p class="text-sm text-slate-500">จัดการข้อมูลและสิทธิ์การใช้งานของบุคลากร</p>
                </div>
                <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('employees.index') }}" method="GET" class="relative group flex gap-3">
                         <!-- Department Filter -->
                        <div class="relative">
                            <select name="department" onchange="this.form.submit()" class="block w-full sm:w-48 pl-3 pr-10 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm font-medium text-slate-700 appearance-none cursor-pointer">
                                <option value="">ทุกแผนก</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ request('department') == $dept->name ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-slate-500">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                class="block w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm font-medium placeholder:text-slate-400" 
                                placeholder="ค้นหาชื่อ, อีเมล...">
                        </div>
                    </form>

                     <!-- Bulk Delete Button (Hidden by default) -->
                    <button id="bulk-delete-btn" class="hidden items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition-all animate-fade-in-up">
                        <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
                        <span id="selected-count">0</span> รายการ
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 align-middle">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col" class="px-4 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider w-10">
                                <input type="checkbox" id="select-all" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th scope="col" class="px-4 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">พนักงาน</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">แผนก / ตำแหน่ง</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">บทบาท</th>
                            <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">หัวหน้างาน</th>
                            <th scope="col" class="px-8 py-5 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @foreach($employees as $emp)
                        <tr class="hover:bg-indigo-50/10 transition-colors duration-150 group">
                            <td class="px-4 py-5 whitespace-nowrap">
                                <input type="checkbox" name="selected_users[]" value="{{ $emp->id }}" class="user-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 h-12 w-12 relative">
                                        @if(isset($emp->avatar) && $emp->avatar)
                                            <img class="h-12 w-12 rounded-xl object-cover border-2 border-white shadow-md group-hover:shadow-indigo-500/20 group-hover:scale-105 transition-all" src="{{ asset('storage/'.$emp->avatar) }}" alt="">
                                        @else
                                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-100 to-violet-200 flex items-center justify-center text-indigo-700 font-bold border-2 border-white shadow-md group-hover:shadow-indigo-500/20 group-hover:scale-105 transition-all text-lg">
                                                {{ substr($emp->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <!-- Online Mockup Dot -->
                                        {{-- <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div> --}}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 transition-colors">{{ $emp->name }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                            <i data-lucide="mail" class="w-4 h-4 text-[10px]"></i> {{ $emp->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-700">{{ $emp->department ?? '-' }}</div>
                                <div class="text-xs font-medium text-slate-400 mt-1 bg-slate-100/50 inline-block px-2 py-0.5 rounded-lg border border-slate-100">
                                    {{ $emp->position ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @php
                                    $roleColor = match($emp->role) {
                                        'admin' => 'bg-red-50 text-red-600 ring-1 ring-red-100',
                                        'director' => 'bg-violet-50 text-violet-600 ring-1 ring-violet-100',
                                        'deputy_director' => 'bg-blue-50 text-blue-600 ring-1 ring-blue-100',
                                        'department_head' => 'bg-orange-50 text-orange-600 ring-1 ring-orange-100',
                                        default => 'bg-slate-50 text-slate-600 ring-1 ring-slate-100'
                                    };
                                    $roleLabel = match($emp->role) {
                                        'admin' => 'ผู้ดูแลระบบ',
                                        'director' => 'ผอ. รร.พธ.ฯ',
                                        'deputy_director' => 'รอง ผอ. รร.พธ.ฯ',
                                        'department_head' => 'หน. แผนก',
                                        'employee' => 'ข้าราชการ',
                                        default => $emp->role
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold {{ $roleColor }}">
                                    @if($emp->role === 'admin') <i data-lucide="shield" class="w-4 h-4 text-[10px]"></i>
                                    @elseif($emp->role === 'director' || $emp->role === 'deputy_director') <i data-lucide="crown" class="w-4 h-4 text-[10px]"></i>
                                    @elseif($emp->role === 'department_head') <i data-lucide="briefcase" class="w-4 h-4 text-[10px]"></i>
                                    @else <i data-lucide="user" class="w-4 h-4 text-[10px]"></i>
                                    @endif
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @if($emp->supervisor)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                            {{ substr($emp->supervisor->name, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-medium text-slate-600">{{ $emp->supervisor->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-300 italic">-</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('employees.edit', $emp->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 transition-all shadow-sm">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
             <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCountSpan = document.getElementById('selected-count');

            function updateBulkDeleteBtn() {
                const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                selectedCountSpan.textContent = checkedCount;
                if (checkedCount > 0) {
                    bulkDeleteBtn.classList.remove('hidden');
                    bulkDeleteBtn.classList.add('flex');
                } else {
                    bulkDeleteBtn.classList.add('hidden');
                    bulkDeleteBtn.classList.remove('flex');
                }
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkDeleteBtn();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAll.checked = false;
                    } else if (document.querySelectorAll('.user-checkbox:checked').length === checkboxes.length) {
                        selectAll.checked = true;
                    }
                    updateBulkDeleteBtn();
                });
            });

            bulkDeleteBtn.addEventListener('click', async function() {
                if (!confirm('คุณแน่ใจหรือไม่ที่จะลบพนักงานที่เลือก? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
                    return;
                }

                const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
                
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
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                }
            });
        });
    </script>
</x-app-layout>

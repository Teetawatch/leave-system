<x-app-layout>
    @section('title', 'รออนุมัติการลงทะเบียน')

    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            
            <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">รออนุมัติการลงทะเบียน</h3>
                        <p class="text-sm text-slate-400">พนักงานที่ลงทะเบียนแล้วรอการอนุมัติ</p>
                    </div>
                </div>
                
                <a href="{{ route('employees.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-800 font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    กลับ
                </a>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mx-8 mt-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </div>
                    <span class="text-emerald-700 font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($pendingUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">พนักงาน</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">อีเมล</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">แผนก / ตำแหน่ง</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ลงทะเบียนเมื่อ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($pendingUsers as $user)
                                <tr class="hover:bg-amber-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800">{{ $user->rank }} {{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-600">{{ $user->email }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-700">{{ $user->department ?? '-' }}</div>
                                        <div class="text-xs text-slate-400">{{ $user->position ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-slate-500">{{ $user->updated_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('employees.approve-registration', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg transition-all">
                                                    <i data-lucide="check" class="w-4 h-4 mr-1"></i>
                                                    อนุมัติ
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('employees.reject-registration', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('ปฏิเสธการลงทะเบียน? พนักงานจะสามารถลงทะเบียนใหม่ได้')">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-600 font-bold text-sm rounded-lg transition-all">
                                                    <i data-lucide="x" class="w-4 h-4 mr-1"></i>
                                                    ปฏิเสธ
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $pendingUsers->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                        <i data-lucide="inbox" class="w-6 h-6 text-3xl"></i>
                    </div>
                    <p class="text-slate-500 font-medium">ไม่มีรายการรออนุมัติ</p>
                    <p class="text-sm text-slate-400 mt-1">เมื่อพนักงานลงทะเบียนใหม่ รายการจะแสดงที่นี่</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

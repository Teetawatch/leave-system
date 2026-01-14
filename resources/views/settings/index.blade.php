<x-app-layout>
    @section('title', 'ตั้งค่าระบบ (System Settings)')

    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Leave Configuration Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="sliders" class="w-4 h-4 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">ตั้งค่าประเภทการลา</h3>
                    <p class="text-sm text-slate-400">กำหนดจำนวนวันลาสูงสุดต่อปีสำหรับแต่ละประเภท</p>
                </div>
            </div>
            
            <form action="{{ route('settings.update') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    @foreach($leaveTypes as $type)
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-brand-200 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold">
                                {{ mb_substr($type->name, 0, 1) }}
                            </div>
                            <div>
                                <label for="type_{{ $type->id }}" class="block text-sm font-bold text-slate-700">{{ $type->name }}</label>
                                <p class="text-xs text-slate-400">Slug: {{ $type->slug }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" name="leave_types[{{ $type->id }}][max_days]" value="{{ $type->max_days_per_year }}" 
                                   class="block w-24 rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-center font-bold text-slate-700">
                             <span class="text-sm text-slate-500">วัน/ปี</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> บันทึกการตั้งค่า
                    </button>
                </div>
            </form>
        </div>

        <!-- General Settings (Placeholder) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden opacity-60">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                 <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                    <i data-lucide="building" class="w-5 h-5 text-xl"></i>
                 </div>
                 <div>
                    <h3 class="font-bold text-slate-800">ตั้งค่าองค์กร</h3>
                    <p class="text-sm text-slate-400">ข้อมูลบริษัทและวันหยุด (Coming Soon)</p>
                 </div>
            </div>
            <div class="p-6 text-center text-slate-400 py-12">
                <i data-lucide="construction" class="w-6 h-6 text-3xl mb-3"></i>
                <p>ฟีเจอร์นี้อยู่ระหว่างการพัฒนา</p>
            </div>
        </div>

    </div>
</x-app-layout>

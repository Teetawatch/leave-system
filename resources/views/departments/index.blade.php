<x-app-layout>
    @section('title', 'จัดการแผนก (Departments)')

    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Create Form -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sticky top-24">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-brand-600"></i> เพิ่มแผนกใหม่
                </h3>
                
                <form action="{{ route('departments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">ชื่อแผนก</label>
                        <input type="text" name="name" placeholder="ระบุชื่อแผนก..." class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl shadow-lg shadow-brand-500/30 transition-all">
                        <i data-lucide="save" class="w-4 h-4 mr-1"></i> บันทึก
                    </button>
                </form>
            </div>
        </div>

        <!-- List -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">รายชื่อแผนกทั้งหมด</h3>
                    <span class="bg-slate-200 text-slate-600 text-xs px-2 py-1 rounded-full font-bold">{{ $departments->total() }}</span>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @forelse($departments as $dept)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-sm">
                                {{ mb_substr($dept->name, 0, 1) }}
                            </div>
                            <!-- Edit Form (Hidden by default, shown via Alpine/JS? No, let's keep it simple: Text) -->
                            <!-- For simplicity, just text and a delete button. Edit needs a separate page or modal. -->
                            <!-- Let's try to do a simple inline edit form? No, too complex for plain Blade. -->
                            <!-- Just Display Name -->
                            <span class="text-slate-700 font-medium">{{ $dept->name }}</span>
                        </div>
                        
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{-- Edit Button (Trigger Modal? Or just a link?) --}}
                            {{-- <button class="text-slate-400 hover:text-brand-600 p-2"><i data-lucide="edit" class="w-4 h-4"></i></button> --}}
                            
                            <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('ยืนยันการลบแผนก {{ $dept->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors" title="ลบแผนก">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400">
                        <i data-lucide="folder-open" class="w-6 h-6 text-3xl mb-2"></i>
                        <p>ยังไม่มีข้อมูลแผนก</p>
                    </div>
                    @endforelse
                </div>

                @if($departments->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $departments->links() }}
                </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>

<!-- Approval Modal -->
<template x-teleport="body">
    <div x-show="openApprove" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openApprove = false">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"></div>
            </div>

            <div class="bg-white rounded-[4rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-2xl border-t-8 border-emerald-500"
                x-data="signaturePad({{ $req->id }})">

                <form action="{{ route('approvals.approve', $req->id) }}" method="POST"
                    id="form-approve-{{ $req->id }}">
                    @csrf
                    <input type="hidden" name="signature" id="signature-input-{{ $req->id }}">

                    <div class="bg-white p-10 md:p-14">
                        <div class="flex items-center justify-between mb-12">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-20 h-20 rounded-[2rem] bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner">
                                    <i data-lucide="check-circle" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">CONFIRMATION</h3>
                                    <p class="text-emerald-500 font-black text-xs uppercase tracking-widest">Decision
                                        Required</p>
                                </div>
                            </div>
                            <button type="button" @click="openApprove = false"
                                class="w-14 h-14 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                <i data-lucide="x" class="w-8 h-8"></i>
                            </button>
                        </div>

                        <div class="mb-10 p-6 bg-slate-50/80 rounded-[2.5rem] border border-slate-100">
                            <p class="text-sm font-bold text-slate-500">ยืนยันการพิจารณาคำขอของ:</p>
                            <p class="text-2xl font-black text-slate-900 mt-1">
                                {{ $req->user->rank }}{{ $req->user->name }}</p>
                            <div class="mt-4 flex items-center gap-3">
                                <span
                                    class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm border border-slate-100 uppercase">{{ $req->leaveType->name }}</span>
                                <span
                                    class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm border border-slate-100 uppercase">{{ $req->total_days + 0 }}
                                    วัน</span>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <!-- Toggle Signature Mode -->
                            @if(Auth::user()->signature)
                                <div class="flex p-2 bg-slate-100 rounded-[2rem] border-2 border-slate-200">
                                    <button type="button" @click="useSaved = false; $nextTick(() => { resizeCanvas(); })"
                                        :class="!useSaved ? 'bg-white shadow-xl text-emerald-600 scale-[1.02]' : 'text-slate-400 hover:text-slate-600'"
                                        class="flex-1 py-4 rounded-[1.8rem] text-xs font-black uppercase tracking-widest transition-all duration-300">
                                        ลงนามใหม่
                                    </button>
                                    <button type="button" @click="useSaved = true"
                                        :class="useSaved ? 'bg-white shadow-xl text-emerald-600 scale-[1.02]' : 'text-slate-400 hover:text-slate-600'"
                                        class="flex-1 py-4 rounded-[1.8rem] text-xs font-black uppercase tracking-widest transition-all duration-300">
                                        ใช้ลายเซ็นเดิม
                                    </button>
                                </div>
                                <input type="hidden" name="use_saved_signature" :value="useSaved ? '1' : '0'">
                            @else
                                <input type="hidden" name="use_saved_signature" value="0">
                            @endif

                            <!-- Signature Area (Conditional) -->
                            @php
                                $needsSignature = in_array($req->status, ['pending_supervisor', 'pending_manager', 'pending_director']);
                            @endphp

                            @if($needsSignature)
                                <!-- Draw Pad -->
                                <div x-show="!useSaved" class="space-y-4">
                                    <div class="flex justify-between items-center px-4">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">AUTHORITY
                                            SIGNATURE</label>
                                        <button type="button" @click="clearSignature()"
                                            class="text-xs font-black text-rose-500 hover:text-rose-600 flex items-center gap-2 transition-colors">
                                            <i data-lucide="eraser" class="w-4 h-4"></i>
                                            ล้าง
                                        </button>
                                    </div>
                                    <div
                                        class="bg-slate-50 border-4 border-dashed border-slate-200 rounded-[3rem] h-64 relative cursor-crosshair group/pad hover:border-emerald-400 hover:bg-emerald-50/5 transition-all duration-500">
                                        <canvas id="signature-canvas-{{ $req->id }}" class="w-full h-full"></canvas>
                                        <div x-show="isCanvasEmpty"
                                            class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none transition-opacity duration-300">
                                            <div
                                                class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                                <i data-lucide="edit-3" class="w-8 h-8 text-slate-300"></i>
                                            </div>
                                            <p class="text-xs font-black text-slate-300 uppercase tracking-widest">Sign Here
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Saved Signature -->
                                @if(Auth::user()->signature)
                                    <div x-show="useSaved" class="space-y-4">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-4">REGISTERED
                                            IDENTITY</label>
                                        <div
                                            class="bg-emerald-50/30 border-4 border-solid border-emerald-100 rounded-[3rem] h-64 flex items-center justify-center p-12 relative overflow-hidden group/saved">
                                            <img src="{{ asset('storage/' . Auth::user()->signature) }}"
                                                class="max-h-full max-w-full object-contain relative z-10 filter drop-shadow-2xl">
                                            <div
                                                class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover/saved:opacity-100 transition-opacity">
                                            </div>
                                            <div
                                                class="absolute bottom-6 left-1/2 -translate-x-1/2 bg-white/80 backdrop-blur-md border border-emerald-100 px-6 py-2 rounded-full text-[10px] font-black text-emerald-600 shadow-lg uppercase tracking-widest">
                                                Verified Profile
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- No signature message for Deputy stage -->
                                <div
                                    class="p-8 bg-indigo-50/50 rounded-[2.5rem] border-2 border-indigo-100/50 flex flex-col items-center text-center">
                                    <div
                                        class="w-16 h-16 rounded-2xl bg-white shadow-md text-indigo-500 flex items-center justify-center mb-4">
                                        <i data-lucide="info" class="w-8 h-8"></i>
                                    </div>
                                    <p class="text-sm font-bold text-indigo-700 uppercase tracking-widest">Acknowledgement
                                        Step</p>
                                    <p class="text-xs font-medium text-slate-500 mt-2">ขั้นตอนนี้เป็นการรับทราบคำขอ
                                        ไม่ต้องลงลายมือชื่อ</p>
                                </div>
                                <script>
                                    // In deputy stage, we don't start with saved signature hidden
                                    document.addEventListener('alpine:init', () => {
                                        // useSaved is already true by default in index.blade.php
                                    });
                                </script>
                            @endif

                            <div class="space-y-4">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-4">OFFICIAL
                                    OBSERVATION / COMMENT</label>
                                <textarea name="comment" rows="3"
                                    class="block w-full rounded-[2rem] border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-8 focus:ring-emerald-500/5 transition-all p-6 text-base font-bold text-slate-700 placeholder:text-slate-300 resize-none shadow-inner"
                                    placeholder="ระบุข้อความเพิ่มเติม (ถ้ามี)..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 px-10 py-10 md:px-14 md:py-12 flex flex-col sm:flex-row-reverse gap-6 border-t border-slate-100">
                        <button type="submit" @click="submit($event)"
                            class="relative flex-[2] inline-flex justify-center items-center px-10 py-6 bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] shadow-2xl shadow-slate-900/40 hover:shadow-emerald-600/40 hover:bg-emerald-600 transition-all hover:-translate-y-2 group/btn">
                            <i data-lucide="shield-check"
                                class="w-6 h-6 mr-3 group-hover/btn:scale-125 transition-transform"></i>
                            ยืนยันการพิจารณา
                        </button>
                        <button type="button" @click="openApprove = false"
                            class="flex-1 inline-flex justify-center items-center px-10 py-6 bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] hover:bg-slate-100 hover:text-slate-600 transition-all shadow-sm">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<!-- Reject Modal -->
<template x-teleport="body">
    <div x-show="openReject" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openReject = false">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md"></div>
            </div>

            <div
                class="bg-white rounded-[4rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-lg border-t-8 border-rose-500">
                <form action="{{ route('approvals.reject', $req->id) }}" method="POST">
                    @csrf
                    <div class="bg-white p-10 md:p-14">
                        <div class="flex items-start justify-between mb-8">
                            <div
                                class="w-20 h-20 rounded-[2rem] bg-rose-50 text-rose-500 flex items-center justify-center shadow-inner">
                                <i data-lucide="alert-triangle" class="w-10 h-10"></i>
                            </div>
                            <button type="button" @click="openReject = false"
                                class="w-14 h-14 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                <i data-lucide="x" class="w-8 h-8"></i>
                            </button>
                        </div>

                        <h3 class="text-3xl font-black text-slate-900 tracking-tighter mb-4">REJECTION</h3>
                        <p class="text-slate-500 font-bold mb-10 leading-relaxed uppercase tracking-wider text-sm">
                            ระบุเหตุผลในการปฏิเสธคำขอของ <span
                                class="text-rose-600 underline decoration-rose-200 decoration-4 underline-offset-4">{{ $req->user->name }}</span>
                        </p>

                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-rose-500 uppercase tracking-[0.3em] px-4">REASON
                                FOR REJECTION * REQUIRED</label>
                            <textarea name="comment" rows="4" required
                                class="block w-full rounded-[2.5rem] border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-8 focus:ring-rose-500/5 transition-all p-8 text-base font-bold text-slate-700 placeholder:text-slate-300 resize-none shadow-inner"
                                placeholder="ระบุเหตุผลความจำเป็นในการปฏิเสธ..."></textarea>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 px-10 py-10 md:px-14 md:py-12 flex flex-col sm:flex-row-reverse gap-6 border-t border-slate-100">
                        <button type="submit"
                            class="flex-1 inline-flex justify-center items-center px-10 py-6 bg-rose-500 text-white font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] shadow-2xl shadow-rose-500/20 hover:bg-rose-600 transition-all hover:-translate-y-2 group/btn">
                            <i data-lucide="x-circle"
                                class="w-6 h-6 mr-3 group-hover/btn:scale-125 transition-transform"></i>
                            ยืนยันปฏิเสธ
                        </button>
                        <button type="button" @click="openReject = false"
                            class="flex-1 inline-flex justify-center items-center px-10 py-6 bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] hover:bg-slate-100 hover:text-slate-600 transition-all">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
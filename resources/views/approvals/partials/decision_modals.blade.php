<!-- Approval Modal -->
<template x-teleport="body">
    <div x-show="openApprove" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;"
        x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-400"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openApprove = false">
                <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-xl"></div>
            </div>

            <div class="bg-white rounded-[4.5rem] text-left overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] transform transition-all relative w-full max-w-2xl border-t-8 border-emerald-500 animate-slide-up"
                x-data="signaturePad({{ $req->id }})">

                <form action="{{ route('approvals.approve', $req->id) }}" method="POST"
                    id="form-approve-{{ $req->id }}">
                    @csrf
                    <input type="hidden" name="signature" id="signature-input-{{ $req->id }}">

                    <div class="bg-white p-12 md:p-16">
                        <div class="flex items-center justify-between mb-12">
                            <div class="flex items-center gap-6">
                                <div
                                    class="w-20 h-20 rounded-[2.5rem] bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner border border-emerald-100">
                                    <i data-lucide="shield-check" class="w-10 h-10"></i>
                                </div>
                                <div>
                                    <h3
                                        class="text-4xl font-black text-slate-900 tracking-tighter leading-none mb-2 uppercase">
                                        Official Decision</h3>
                                    <p class="text-emerald-500 font-black text-[10px] uppercase tracking-[0.4em]">
                                        Authorization Protocol Required</p>
                                </div>
                            </div>
                            <button type="button" @click="openApprove = false"
                                class="w-16 h-16 rounded-[2rem] bg-slate-50 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center text-slate-400 transition-all active:scale-90">
                                <i data-lucide="x" class="w-8 h-8"></i>
                            </button>
                        </div>

                        <div
                            class="mb-12 p-10 bg-slate-50/80 rounded-[3.5rem] border border-slate-100 relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700">
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-3">Target
                                Personnel</p>
                            <p class="text-3xl font-black text-slate-900 tracking-tighter mb-4">
                                {{ $req->user->rank }}{{ $req->user->name }}
                            </p>
                            <div class="flex items-center gap-4">
                                <span
                                    class="px-5 py-2 bg-white rounded-full text-[10px] font-black text-indigo-600 shadow-sm border border-indigo-100 uppercase tracking-widest">{{ $req->leaveType->name }}</span>
                                <span
                                    class="px-5 py-2 bg-white rounded-full text-[10px] font-black text-emerald-600 shadow-sm border border-emerald-100 uppercase tracking-widest">{{ $req->total_days + 0 }}
                                    วัน</span>
                            </div>
                        </div>

                        <div class="space-y-10">
                            <!-- Toggle Signature Mode -->
                            @if(Auth::user()->signature)
                                <div class="flex p-2.5 bg-slate-100 rounded-full border border-slate-200">
                                    <button type="button" @click="useSaved = false; $nextTick(() => { resizeCanvas(); })"
                                        :class="!useSaved ? 'bg-white shadow-xl text-emerald-600' : 'text-slate-400 hover:text-slate-600'"
                                        class="flex-1 py-5 rounded-full text-xs font-black uppercase tracking-[0.2em] transition-all duration-500">
                                        Manual Signature
                                    </button>
                                    <button type="button" @click="useSaved = true"
                                        :class="useSaved ? 'bg-white shadow-xl text-emerald-600' : 'text-slate-400 hover:text-slate-600'"
                                        class="flex-1 py-5 rounded-full text-xs font-black uppercase tracking-[0.2em] transition-all duration-500">
                                        Registered Identity
                                    </button>
                                </div>
                                <input type="hidden" name="use_saved_signature" :value="useSaved ? '1' : '0'">
                            @else
                                <input type="hidden" name="use_saved_signature" value="0">
                            @endif

                            <!-- Signature Area -->
                            @php
                                $needsSignature = in_array($req->status, ['pending_supervisor', 'pending_manager', 'pending_director']);
                            @endphp

                            @if($needsSignature)
                                <!-- Draw Pad -->
                                <div x-show="!useSaved" class="space-y-6 animate-slide-up" style="animation-duration: 0.4s">
                                    <div class="flex justify-between items-center px-6">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Electronic
                                            Signature Pad</label>
                                        <button type="button" @click="clearSignature()"
                                            class="text-[10px] font-black text-rose-500 hover:text-rose-600 flex items-center gap-3 transition-colors uppercase tracking-[0.2em]">
                                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                            Clear Pad
                                        </button>
                                    </div>
                                    <div
                                        class="bg-slate-50 border-4 border-dashed border-slate-200 rounded-[4rem] h-72 relative cursor-crosshair group/pad hover:border-emerald-400 hover:bg-emerald-50/10 transition-all duration-500 shadow-inner">
                                        <canvas id="signature-canvas-{{ $req->id }}" class="w-full h-full"></canvas>
                                        <div x-show="isCanvasEmpty"
                                            class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none opacity-50">
                                            <div
                                                class="w-20 h-20 rounded-full bg-white shadow-lg flex items-center justify-center mb-4 group-hover/pad:scale-110 transition-transform">
                                                <i data-lucide="pen-tool" class="w-10 h-10 text-slate-300"></i>
                                            </div>
                                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">
                                                ลงชื่อพิจารณาสั่งการ</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Saved Signature -->
                                @if(Auth::user()->signature)
                                    <div x-show="useSaved" class="space-y-6 animate-slide-up" style="animation-duration: 0.4s">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] px-6">System
                                            Registered Persona</label>
                                        <div
                                            class="bg-white border-4 border-solid border-emerald-50 rounded-[4rem] h-72 flex items-center justify-center p-16 relative overflow-hidden group/saved shadow-2xl shadow-emerald-500/5">
                                            <div class="absolute inset-0 bg-gradient-to-tr from-emerald-50/50 to-transparent">
                                            </div>
                                            <img src="{{ asset('storage/' . Auth::user()->signature) }}"
                                                class="max-h-full max-w-full object-contain relative z-10 filter drop-shadow-2xl brightness-90 group-hover:brightness-100 transition-all duration-700">
                                            <div
                                                class="absolute bottom-8 left-1/2 -translate-x-1/2 bg-emerald-500 text-white px-8 py-3 rounded-full text-[10px] font-black shadow-2xl uppercase tracking-[0.3em]">
                                                Verified Identity
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- No signature message for Deputy stage -->
                                <div
                                    class="p-12 bg-indigo-50/50 rounded-[3.5rem] border-2 border-indigo-100/50 flex flex-col items-center text-center group">
                                    <div
                                        class="w-20 h-20 rounded-[2rem] bg-white shadow-xl text-indigo-500 flex items-center justify-center mb-8 group-hover:rotate-12 transition-transform">
                                        <i data-lucide="info" class="w-10 h-10"></i>
                                    </div>
                                    <p class="text-lg font-black text-indigo-900 uppercase tracking-[0.2em] mb-3">
                                        Acknowledgement Protocol</p>
                                    <p
                                        class="text-sm font-bold text-slate-400 max-w-xs leading-relaxed uppercase tracking-widest">
                                        ขั้นตอนนี้เป็นการยืนยันรับทราบข้อมูลในระบบ โดยไม่ต้องลงลายนิ้วมืออิเล็กทรอนิกส์</p>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] px-6">Official
                                    Command / Observation</label>
                                <textarea name="comment" rows="4"
                                    class="block w-full rounded-[3rem] border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-[12px] focus:ring-emerald-500/5 transition-all p-10 text-lg font-black text-slate-800 placeholder:text-slate-300 resize-none shadow-inner"
                                    placeholder="เพิ่มข้อสั่งการ หรือข้อสังเกตการณ์ในการลา..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 px-12 py-12 md:px-16 md:py-16 flex flex-col sm:flex-row-reverse gap-8 border-t border-slate-100">
                        <button type="submit" @click="submit($event)"
                            class="relative flex-[2] inline-flex justify-center items-center px-12 py-8 bg-slate-950 text-white font-black uppercase tracking-[0.3em] text-sm rounded-full shadow-[0_25px_50px_-15px_rgba(0,0,0,0.4)] hover:shadow-emerald-600/50 hover:bg-emerald-600 transition-all hover:-translate-y-2 group/btn">
                            <i data-lucide="shield-check"
                                class="w-7 h-7 mr-4 group-hover/btn:scale-125 transition-transform"></i>
                            Execute Decision
                        </button>
                        <button type="button" @click="openApprove = false"
                            class="flex-1 inline-flex justify-center items-center px-12 py-8 bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-[0.3em] text-sm rounded-full hover:bg-slate-100 hover:text-slate-600 transition-all shadow-sm">
                            Cancel
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
        x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-400"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openReject = false">
                <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-xl"></div>
            </div>

            <div
                class="bg-white rounded-[4.5rem] text-left overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] transform transition-all relative w-full max-w-xl border-t-8 border-rose-500 animate-slide-up">
                <form action="{{ route('approvals.reject', $req->id) }}" method="POST">
                    @csrf
                    <div class="bg-white p-12 md:p-16">
                        <div class="flex items-start justify-between mb-12">
                            <div
                                class="w-20 h-20 rounded-[2.5rem] bg-rose-50 text-rose-500 flex items-center justify-center shadow-inner border border-rose-100">
                                <i data-lucide="alert-triangle" class="w-10 h-10"></i>
                            </div>
                            <button type="button" @click="openReject = false"
                                class="w-16 h-16 rounded-[2rem] bg-slate-50 hover:bg-rose-50 hover:text-rose-500 flex items-center justify-center text-slate-400 transition-all active:scale-90">
                                <i data-lucide="x" class="w-8 h-8"></i>
                            </button>
                        </div>

                        <h3 class="text-4xl font-black text-slate-900 tracking-tighter mb-4 uppercase leading-none">
                            REJECTION PROTOCOL</h3>
                        <p class="text-slate-500 font-bold mb-12 leading-relaxed uppercase tracking-[0.15em] text-sm">
                            ยืนยันการปฏิเสธคำขอรับการพิจารณาของ <span
                                class="text-rose-600 underline decoration-rose-200 decoration-4 underline-offset-8">{{ $req->user->name }}</span>
                        </p>

                        <div class="space-y-6">
                            <label
                                class="text-[10px] font-black text-rose-500 uppercase tracking-[0.4em] px-6">Mandatory
                                Rejection Comment</label>
                            <textarea name="comment" rows="6" required
                                class="block w-full rounded-[3.5rem] border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-[12px] focus:ring-rose-500/5 transition-all p-12 text-lg font-black text-slate-800 placeholder:text-slate-300 resize-none shadow-inner"
                                placeholder="ระบุเหตุผลความจำเป็นในการปฏิเสธคำขอให้ชัดเจน..."></textarea>
                        </div>
                    </div>

                    <div
                        class="bg-slate-50 px-12 py-12 md:px-16 md:py-16 flex flex-col sm:flex-row-reverse gap-8 border-t border-slate-100">
                        <button type="submit"
                            class="flex-1 inline-flex justify-center items-center px-12 py-8 bg-rose-500 text-white font-black uppercase tracking-[0.3em] text-sm rounded-full shadow-[0_25px_50px_-15px_rgba(244,63,94,0.4)] hover:bg-rose-600 transition-all hover:-translate-y-2 group/btn">
                            <i data-lucide="x-circle"
                                class="w-7 h-7 mr-4 group-hover/btn:scale-125 transition-transform"></i>
                            Confirm Reject
                        </button>
                        <button type="button" @click="openReject = false"
                            class="flex-1 inline-flex justify-center items-center px-12 py-8 bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-[0.3em] text-sm rounded-full hover:bg-slate-100 hover:text-slate-600 transition-all shadow-sm">
                            Go Back
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
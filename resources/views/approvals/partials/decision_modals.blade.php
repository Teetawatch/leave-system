@php
    $needsSignature = in_array($req->status, ['pending_supervisor', 'pending_manager', 'pending_director']);
@endphp

<!-- Approval Modal -->
<template x-teleport="body">
    <div x-show="openApprove" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openApprove = false">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            </div>

            <div class="bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-2xl animate-slide-up"
                x-data="signaturePad({{ $req->id }})">

                <form action="{{ route('approvals.approve', $req->id) }}" method="POST" id="form-approve-{{ $req->id }}">
                    @csrf
                    <input type="hidden" name="signature" id="signature-input-{{ $req->id }}">

                    <div class="bg-white p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="shield-check" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">ยืนยันการอนุมัติ</h3>
                                    <p class="text-emerald-600 text-xs font-medium">กรุณาตรวจสอบข้อมูลก่อนดำเนินการ</p>
                                </div>
                            </div>
                            <button type="button" @click="openApprove = false"
                                class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- User Summary -->
                        <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-200 overflow-hidden flex-shrink-0">
                                @if($req->user->avatar)
                                    <img src="{{ asset('storage/' . $req->user->avatar) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold bg-white">
                                        {{ substr($req->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">ผู้ขออนุมัติ</p>
                                <p class="text-lg font-bold text-slate-800">{{ $req->user->rank }}{{ $req->user->name }}</p>
                            </div>
                            <div class="ml-auto text-right">
                                <span class="block text-xs font-bold text-slate-400 mb-1">ประเภทการลา</span>
                                <span class="inline-block px-3 py-1 bg-white rounded-lg text-xs font-bold text-indigo-600 border border-indigo-100 shadow-sm">
                                    {{ $req->leaveType->name }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Toggle Signature Mode -->
                            @if(Auth::user()->signature)
                                <div class="flex p-1 bg-slate-100 rounded-xl border border-slate-200">
                                    <button type="button" @click="useSaved = false; $nextTick(() => { resizeCanvas(); })"
                                        :class="!useSaved ? 'bg-white shadow text-slate-800' : 'text-slate-500 hover:text-slate-700'"
                                        class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all">
                                        เซ็นชื่อใหม่
                                    </button>
                                    <button type="button" @click="useSaved = true"
                                        :class="useSaved ? 'bg-white shadow text-slate-800' : 'text-slate-500 hover:text-slate-700'"
                                        class="flex-1 py-2.5 rounded-lg text-xs font-bold transition-all">
                                        ใช้ลายเซ็นเดิม
                                    </button>
                                </div>
                                <input type="hidden" name="use_saved_signature" :value="useSaved ? '1' : '0'">
                            @else
                                <input type="hidden" name="use_saved_signature" value="0">
                            @endif

                            @if($needsSignature)
                                <!-- Draw Pad -->
                                <div x-show="!useSaved" class="space-y-4">
                                    <div class="flex justify-between items-center px-1">
                                        <label class="text-xs font-bold text-slate-500 uppercase">พื้นที่เซ็นชื่ออิเล็กทรอนิกส์</label>
                                        <button type="button" @click="clearSignature()" class="text-xs font-bold text-rose-500 hover:text-rose-600 flex items-center gap-1 transition-colors">
                                            <i data-lucide="refresh-cw" class="w-3 h-3"></i> ล้าง
                                        </button>
                                    </div>
                                    <div class="bg-white border-2 border-dashed border-slate-300 rounded-2xl h-48 relative cursor-crosshair hover:border-emerald-400 transition-colors">
                                        <canvas id="signature-canvas-{{ $req->id }}" class="w-full h-full rounded-2xl"></canvas>
                                        <div x-show="isCanvasEmpty" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-slate-300">
                                            <i data-lucide="pen-tool" class="w-8 h-8 mb-2"></i>
                                            <p class="text-xs font-bold uppercase">เซ็นชื่อที่นี่</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Saved Signature -->
                                @if(Auth::user()->signature)
                                    <div x-show="useSaved" class="space-y-4">
                                        <label class="text-xs font-bold text-slate-500 uppercase px-1">ลายเซ็นที่บันทึกไว้</label>
                                        <div class="bg-slate-50 border border-slate-200 rounded-2xl h-48 flex items-center justify-center p-8 relative overflow-hidden">
                                            <img src="{{ asset('storage/' . Auth::user()->signature) }}" class="max-h-full max-w-full object-contain filter drop-shadow-sm">
                                            <div class="absolute bottom-4 right-4 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-2">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i> ยืนยันแล้ว
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Acknowledgement Only -->
                                <div class="p-8 bg-indigo-50 rounded-2xl border border-indigo-100 flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full bg-white text-indigo-500 flex items-center justify-center mb-4 shadow-sm">
                                        <i data-lucide="info" class="w-6 h-6"></i>
                                    </div>
                                    <h5 class="font-bold text-indigo-900 mb-2">รับทราบคำขอ</h5>
                                    <p class="text-sm text-indigo-700/80">ขั้นตอนนี้เป็นการยืนยันรับทราบข้อมูลในระบบ โดยไม่ต้องลงลายมือชื่อ</p>
                                </div>
                            @endif

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-500 uppercase px-1">ข้อคิดเห็น / สั่งการ (ถ้ามี)</label>
                                <textarea name="comment" rows="3"
                                    class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm transition-all resize-none placeholder:text-slate-400"
                                    placeholder="ระบุข้อความเพิ่มเติม..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-4 border-t border-slate-100 rounded-b-3xl">
                        <button type="button" @click="submit($event)"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/20">
                            <i data-lucide="check" class="w-5 h-5 mr-2"></i>
                            ยืนยันการอนุมัติ
                        </button>
                        <button type="button" @click="openApprove = false"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors">
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
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openReject = false">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
            </div>

            <div class="bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-xl animate-slide-up">
                <form action="{{ route('approvals.reject', $req->id) }}" method="POST">
                    @csrf
                    <div class="bg-white p-8">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center">
                                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800">ยืนยันการปฏิเสธ</h3>
                                    <p class="text-rose-500 text-xs font-medium">การดำเนินการนี้ไม่สามารถเรียกคืนได้</p>
                                </div>
                            </div>
                            <button type="button" @click="openReject = false"
                                class="w-10 h-10 rounded-xl hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="mb-8 text-center px-4">
                            <p class="text-slate-600 text-sm">
                                คุณกำลังจะปฏิเสธคำขอลาของ <span class="font-bold text-slate-800">{{ $req->user->rank }}{{ $req->user->name }}</span>
                            </p>
                        </div>

                        <div class="space-y-4">
                            <label class="text-xs font-bold text-rose-500 uppercase px-1">เหตุผลการปฏิเสธ (จำเป็น)</label>
                            <textarea name="comment" rows="4" required
                                class="block w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-rose-500 sm:text-sm transition-all resize-none placeholder:text-slate-400"
                                placeholder="โปรดระบุเหตุผล..."></textarea>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-4 border-t border-slate-100 rounded-b-3xl">
                        <button type="submit"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-rose-500 text-white font-bold rounded-xl hover:bg-rose-600 transition-all shadow-lg shadow-rose-500/30">
                            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                            ยืนยันปฏิเสธ
                        </button>
                        <button type="button" @click="openReject = false"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors">
                            ยกเลิก
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
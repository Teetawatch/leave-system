<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { thaiFullDate } from '@/utils/date';

const props = defineProps({ requests: Object });
const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const savedSignatureUrl = computed(() => authUser.value?.signature ? `/storage/${authUser.value.signature}` : null);

const approveForm = useForm({ comment: '', signature: '', use_saved_signature: '0' });
const rejectForm = useForm({ comment: '' });
const activeModal = ref(null);
const activeRequest = ref(null);

// Signature pad
const signatureCanvas = ref(null);
const isDrawing = ref(false);
const signatureMode = ref('saved'); // 'saved' | 'draw'
let lastX = 0, lastY = 0;

function openApprove(req) {
    activeRequest.value = req;
    activeModal.value = 'approve';
    approveForm.signature = '';
    approveForm.use_saved_signature = savedSignatureUrl.value ? '1' : '0';
    signatureMode.value = savedSignatureUrl.value ? 'saved' : 'draw';
    nextTick(() => {
        if (window.lucide) window.lucide.createIcons();
        if (signatureMode.value === 'draw') initCanvas();
    });
}
function openReject(req) {
    activeRequest.value = req;
    activeModal.value = 'reject';
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
}
function openAttachment(req) {
    activeRequest.value = req;
    activeModal.value = 'attachment';
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
}
function closeModal() {
    activeModal.value = null;
    activeRequest.value = null;
    approveForm.reset();
    rejectForm.reset();
    signatureMode.value = 'saved';
}

function switchSignatureMode(mode) {
    signatureMode.value = mode;
    if (mode === 'saved') {
        approveForm.use_saved_signature = '1';
        approveForm.signature = '';
    } else {
        approveForm.use_saved_signature = '0';
        approveForm.signature = '';
        nextTick(() => initCanvas());
    }
}

function initCanvas() {
    const canvas = signatureCanvas.value;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.strokeStyle = '#1e293b';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
}

function getPos(e, canvas) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    if (e.touches) {
        return {
            x: (e.touches[0].clientX - rect.left) * scaleX,
            y: (e.touches[0].clientY - rect.top) * scaleY,
        };
    }
    return {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY,
    };
}

function startDraw(e) {
    e.preventDefault();
    isDrawing.value = true;
    const canvas = signatureCanvas.value;
    const pos = getPos(e, canvas);
    lastX = pos.x; lastY = pos.y;
}

function draw(e) {
    e.preventDefault();
    if (!isDrawing.value) return;
    const canvas = signatureCanvas.value;
    const ctx = canvas.getContext('2d');
    const pos = getPos(e, canvas);
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    lastX = pos.x; lastY = pos.y;
}

function stopDraw(e) {
    if (!isDrawing.value) return;
    isDrawing.value = false;
    const canvas = signatureCanvas.value;
    approveForm.signature = canvas.toDataURL('image/png');
}

function clearCanvas() {
    approveForm.signature = '';
    initCanvas();
}

function submitApprove() {
    approveForm.post(`/approvals/${activeRequest.value.id}/approve`, { onSuccess: () => closeModal() });
}
function submitReject() {
    rejectForm.post(`/approvals/${activeRequest.value.id}/reject`, { onSuccess: () => closeModal() });
}

const formatDate = thaiFullDate;

function actionLabel(status) {
    const map = {
        pending_supervisor: 'อนุญาต',
        pending_deputy_director: 'รับทราบ',
    };
    return map[status] || 'อนุมัติ';
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="ระบบอนุมัติใบลา">
        <div class="min-h-screen bg-[#f8fafc] -m-4 md:-m-8">
            <!-- Light Cinematic Header -->
            <div class="relative bg-gradient-to-br from-white via-indigo-50/60 to-violet-50/40 pt-16 pb-28 overflow-hidden">
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-indigo-200/20 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-violet-200/20 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                    <div class="absolute top-1/3 right-1/4 w-[300px] h-[300px] bg-sky-200/15 rounded-full blur-[80px]"></div>
                </div>
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, #6366f1 1px, transparent 1px); background-size: 24px 24px;"></div>

                <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div>
                            <nav class="flex items-center gap-2 text-indigo-400/80 transition-all mb-4 text-sm font-black tracking-widest uppercase">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>ระบบอนุมัติ</span>
                                <span class="w-1 h-1 rounded-full bg-indigo-400/40"></span>
                                <span class="text-indigo-500">การจัดการใบลา</span>
                            </nav>
                            <h1 class="text-4xl md:text-6xl font-black text-slate-800 tracking-tight mb-4">ระบบอนุมัติใบลา</h1>
                            <p class="text-slate-500 max-w-2xl text-lg font-medium leading-relaxed">ตรวจสอบและพิจารณาคำขอลาของบุคลากร ดำเนินการอนุมัติหรือปฏิเสธพร้อมลงลายมือชื่ออิเล็กทรอนิกส์</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-white/80 border border-slate-200/60 rounded-3xl px-8 py-6 backdrop-blur-md shadow-xl shadow-indigo-500/5">
                                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.3em] mb-1">รอดำเนินการ</p>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-4xl font-black text-slate-800">{{ requests.data?.length || requests.total || 0 }}</span>
                                    <span class="text-sm font-bold text-slate-400 uppercase">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20">
                <!-- Empty State -->
                <div v-if="!requests.data || requests.data.length === 0" class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-32 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
                    <div class="relative z-10">
                        <div class="w-40 h-40 bg-gradient-to-br from-indigo-50 to-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 border-8 border-white shadow-inner group">
                            <i data-lucide="inbox" class="w-20 h-20 text-slate-200 group-hover:scale-110 group-hover:text-indigo-400 transition-all duration-700"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">ไม่มีรายการรออนุมัติ</h3>
                        <p class="text-slate-500 max-w-sm mx-auto text-xl font-medium">ขณะนี้ไม่มีคำขอลาที่ต้องดำเนินการ ขอบคุณที่บริหารจัดการอย่างรวดเร็ว</p>
                    </div>
                </div>

                <!-- Request Cards -->
                <div v-else class="grid grid-cols-1 gap-10">
                    <div v-for="req in requests.data" :key="req.id"
                        class="group bg-white rounded-[3rem] shadow-2xl shadow-slate-200/40 border border-slate-100 p-10 md:p-12 hover:shadow-indigo-500/10 transition-all duration-700 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-50 rounded-bl-full -mr-20 -mt-20 opacity-30 group-hover:scale-125 transition-transform duration-1000 pointer-events-none"></div>

                        <div class="flex flex-col xl:flex-row gap-12 relative z-10">
                            <!-- Requester Profile -->
                            <div class="flex-shrink-0 flex flex-row xl:flex-col items-center xl:items-start gap-8 xl:w-64 min-w-0 border-b xl:border-b-0 xl:border-r border-slate-100 pb-10 xl:pb-0 xl:pr-12">
                                <div class="relative">
                                    <div class="w-28 h-28 rounded-[2.5rem] bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 flex items-center justify-center text-4xl font-black shadow-xl overflow-hidden ring-8 ring-white">
                                        <img v-if="req.user?.avatar" :src="`/storage/${req.user.avatar}`" class="w-full h-full object-cover transform scale-110 group-hover:scale-125 transition-transform duration-700">
                                        <span v-else>{{ req.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white shadow-2xl border-4 border-white transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                                        <i data-lucide="file-text" class="w-6 h-6"></i>
                                    </div>
                                </div>
                                <div class="flex-1 xl:flex-none flex flex-wrap items-center gap-3 min-w-0 max-w-full">
                                    <h4 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight tracking-tight break-words w-full">{{ req.user?.rank }}{{ req.user?.name }}</h4>
                                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] bg-indigo-50 px-3 py-1 rounded-full">ผู้ขอลา</span>
                                    <span class="text-sm font-bold text-slate-400 flex items-center gap-1">
                                        <i data-lucide="building-2" class="w-4 h-4"></i>
                                        {{ req.user?.department }}
                                    </span>
                                </div>
                            </div>

                            <!-- Body Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Leave Type & Date Badges -->
                                <div class="flex flex-wrap items-center gap-4 mb-8">
                                    <span class="inline-flex items-center gap-3 px-6 py-3 bg-indigo-50 text-indigo-700 rounded-2xl text-xs font-black tracking-[0.1em] shadow-sm border border-indigo-100 uppercase">
                                        <i data-lucide="bookmark" class="w-4 h-4 text-indigo-400"></i>
                                        {{ req.leave_type?.name }}
                                    </span>
                                    <span class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-slate-900 rounded-2xl text-xs font-black shadow-sm uppercase">
                                        <i data-lucide="calendar" class="w-4 h-4 text-indigo-500"></i>
                                        {{ req.start_date_thai || formatDate(req.start_date) }} — {{ req.end_date_thai || formatDate(req.end_date) }}
                                    </span>
                                    <span class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-slate-900 rounded-2xl text-xs font-black shadow-sm uppercase">
                                        <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                                        {{ req.total_days }} วัน
                                    </span>
                                </div>

                                <!-- Reason -->
                                <div v-if="req.reason" class="bg-slate-50/80 rounded-[2rem] p-8 border border-slate-100 relative group/quote mb-6">
                                    <i data-lucide="quote" class="absolute top-4 right-6 w-12 h-12 text-slate-200/50 group-hover/quote:text-indigo-200/50 transition-colors"></i>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">เหตุผลการลา</p>
                                    <p class="text-lg text-slate-600 font-medium italic relative z-10">"{{ req.reason }}"</p>
                                </div>

                                <!-- Attachment -->
                                <div v-if="req.attachment_path" class="mt-6">
                                    <button @click="openAttachment(req)"
                                        class="inline-flex items-center gap-3 px-6 py-3 bg-indigo-50 text-indigo-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-100 transition-colors group/attach">
                                        <i data-lucide="paperclip" class="w-4 h-4 group-hover/attach:rotate-12 transition-transform"></i>
                                        ดูเอกสารแนบ
                                        <i data-lucide="maximize-2" class="w-3 h-3 opacity-50"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Executive Decisions -->
                            <div class="flex-shrink-0 xl:w-64 flex flex-col justify-center items-center gap-6 xl:pl-12 xl:border-l-4 xl:border-slate-50 border-double">
                                <button @click="openApprove(req)"
                                    class="w-full group/btn relative px-8 py-6 bg-indigo-600 text-white rounded-[2rem] shadow-2xl shadow-indigo-600/20 hover:shadow-indigo-600/40 transition-all hover:-translate-y-2 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-indigo-700 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                    <div class="relative flex items-center justify-center gap-4">
                                        <i data-lucide="check-circle-2" class="w-6 h-6 group-hover/btn:scale-125 transition-transform"></i>
                                        <span class="text-sm font-black uppercase tracking-[0.2em]">{{ actionLabel(req.status) }}</span>
                                    </div>
                                </button>
                                <button @click="openReject(req)"
                                    class="w-full group/btn relative px-8 py-5 bg-white text-slate-400 border-2 border-slate-200 rounded-[2rem] shadow-sm hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 hover:shadow-rose-500/10 transition-all hover:-translate-y-1">
                                    <div class="flex items-center justify-center gap-3">
                                        <i data-lucide="x-circle" class="w-5 h-5 group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-sm font-black uppercase tracking-[0.15em]">ไม่อนุมัติ</span>
                                    </div>
                                </button>
                                <Link :href="`/leave-request/${req.id}`"
                                    class="w-full text-center px-6 py-3 text-xs font-black text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i> ดูรายละเอียด
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests.links && requests.links.length > 3" class="mt-16 flex justify-center">
                    <div class="bg-white/80 backdrop-blur-md p-4 rounded-[3rem] shadow-2xl border border-white/50 flex gap-1">
                        <template v-for="link in requests.links" :key="link.label">
                            <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-2xl text-sm font-black transition-all"
                                :class="link.active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                            <span v-else class="px-5 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="activeModal === 'approve'" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-xl">
                            <form @submit.prevent="submitApprove">
                                <div class="bg-white p-8 md:p-10">
                                    <div class="flex items-start justify-between mb-6">
                                        <div class="w-14 h-14 rounded-[1.5rem] bg-indigo-50 text-indigo-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="check-circle" class="w-7 h-7"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-1">ยืนยันการอนุมัติ</h3>
                                    <p class="text-slate-500 font-medium mb-6 text-sm">พิจารณาอนุมัติคำขอลาของ <span class="font-black text-slate-900">{{ activeRequest?.user?.rank }}{{ activeRequest?.user?.name }}</span></p>

                                    <div class="mb-5 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100 flex items-center gap-3">
                                        <i data-lucide="bookmark" class="w-5 h-5 text-indigo-400 flex-shrink-0"></i>
                                        <div>
                                            <p class="text-xs font-bold text-indigo-600">{{ activeRequest?.leave_type?.name }}</p>
                                            <p class="text-[10px] text-slate-500">{{ activeRequest?.total_days }} วัน • {{ activeRequest?.start_date_thai }} - {{ activeRequest?.end_date_thai }}</p>
                                        </div>
                                    </div>

                                    <!-- Signature Section -->
                                    <div class="mb-5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-3">
                                            <i data-lucide="pen-tool" class="w-3.5 h-3.5"></i>
                                            ลายเซ็นอิเล็กทรอนิกส์
                                        </label>

                                        <!-- Mode Toggle -->
                                        <div class="flex gap-2 mb-4">
                                            <button type="button" @click="switchSignatureMode('saved')"
                                                :class="signatureMode === 'saved' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-2xl text-xs font-black uppercase tracking-wider transition-all">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                                ใช้ลายเซ็นที่บันทึก
                                            </button>
                                            <button type="button" @click="switchSignatureMode('draw')"
                                                :class="signatureMode === 'draw' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-2xl text-xs font-black uppercase tracking-wider transition-all">
                                                <i data-lucide="pen" class="w-3.5 h-3.5"></i>
                                                เซ็นใหม่
                                            </button>
                                        </div>

                                        <!-- Saved Signature Display -->
                                        <div v-if="signatureMode === 'saved'">
                                            <div v-if="savedSignatureUrl" class="relative bg-white border-2 border-dashed border-indigo-200 rounded-2xl p-4 flex flex-col items-center justify-center min-h-[120px]">
                                                <img :src="savedSignatureUrl" alt="ลายเซ็น" class="max-h-24 max-w-full object-contain">
                                                <span class="mt-2 text-[10px] font-bold text-indigo-400 uppercase tracking-widest">ลายเซ็นของคุณ</span>
                                            </div>
                                            <div v-else class="bg-amber-50 border-2 border-dashed border-amber-200 rounded-2xl p-6 text-center">
                                                <i data-lucide="alert-circle" class="w-8 h-8 text-amber-400 mx-auto mb-2"></i>
                                                <p class="text-sm font-bold text-amber-700">ยังไม่มีลายเซ็นที่บันทึกไว้</p>
                                                <p class="text-xs text-amber-500 mt-1">กรุณาไปที่โปรไฟล์เพื่ออัปโหลดลายเซ็น หรือเลือก "เซ็นใหม่"</p>
                                            </div>
                                        </div>

                                        <!-- Draw Signature Canvas -->
                                        <div v-if="signatureMode === 'draw'" class="space-y-2">
                                            <div class="relative bg-white border-2 border-dashed border-slate-200 rounded-2xl overflow-hidden" style="touch-action: none;">
                                                <canvas ref="signatureCanvas" width="500" height="140"
                                                    class="w-full cursor-crosshair block"
                                                    @mousedown="startDraw" @mousemove="draw" @mouseup="stopDraw" @mouseleave="stopDraw"
                                                    @touchstart="startDraw" @touchmove="draw" @touchend="stopDraw">
                                                </canvas>
                                                <div v-if="!approveForm.signature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                    <span class="text-slate-300 text-sm font-bold italic">เซ็นลายมือที่นี่...</span>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="button" @click="clearCanvas"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                                    <i data-lucide="eraser" class="w-3.5 h-3.5"></i>
                                                    ล้างลายเซ็น
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Comment -->
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">COMMENT (OPTIONAL)</label>
                                        <textarea v-model="approveForm.comment" rows="2"
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium resize-none"
                                            placeholder="ระบุความคิดเห็นของคุณ..."></textarea>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-8 py-5 md:px-10 flex flex-col sm:flex-row-reverse gap-3">
                                    <button type="submit" :disabled="approveForm.processing || (signatureMode === 'saved' && !savedSignatureUrl && signatureMode !== 'draw')"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-1 disabled:opacity-60">
                                        <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                                        {{ approveForm.processing ? 'กำลังดำเนินการ...' : 'ยืนยันการอนุมัติ' }}
                                    </button>
                                    <button type="button" @click="closeModal"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-white border border-slate-200 text-slate-400 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-100 transition-all">
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Reject Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="activeModal === 'reject'" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-lg">
                            <form @submit.prevent="submitReject">
                                <div class="bg-white p-8 md:p-12">
                                    <div class="flex items-start justify-between mb-8">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-rose-50 text-rose-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                            <i data-lucide="x" class="w-6 h-6"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mb-2">ไม่อนุมัติคำขอ</h3>
                                    <p class="text-slate-500 font-medium mb-8">คุณกำลังจะปฏิเสธคำขอลาของ <span class="font-black text-slate-900">{{ activeRequest?.user?.rank }}{{ activeRequest?.user?.name }}</span></p>
                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                            REASON FOR REJECTION <span class="text-rose-300 font-normal">* REQUIRED</span>
                                        </label>
                                        <textarea v-model="rejectForm.comment" rows="4" required
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium resize-none shadow-inner"
                                            placeholder="ระบุเหตุผลในการไม่อนุมัติครั้งนี้..."></textarea>
                                        <p v-if="rejectForm.errors.comment" class="text-rose-500 text-xs font-bold">{{ rejectForm.errors.comment }}</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-8 py-6 md:px-12 md:py-8 flex flex-col sm:flex-row-reverse gap-4">
                                    <button type="submit" :disabled="rejectForm.processing"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-rose-500/20 transition-all hover:-translate-y-1 disabled:opacity-60">
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                        {{ rejectForm.processing ? 'กำลังดำเนินการ...' : 'ยืนยันไม่อนุมัติ' }}
                                    </button>
                                    <button type="button" @click="closeModal"
                                         class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-white border border-slate-200 text-slate-400 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-100 transition-all">
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Attachment Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="activeModal === 'attachment'" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-4xl max-h-[90vh] flex flex-col">
                            <div class="bg-white p-6 md:p-8 flex-shrink-0">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-14 h-14 rounded-[1.5rem] bg-indigo-50 text-indigo-500 flex items-center justify-center shadow-inner">
                                        <i data-lucide="paperclip" class="w-7 h-7"></i>
                                    </div>
                                    <button type="button" @click="closeModal" class="w-10 h-10 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>
                                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">เอกสารแนบ</h3>
                                <p class="text-slate-500 font-medium text-sm">เอกสารแนบจากคำขอลาของ <span class="font-black text-slate-900">{{ activeRequest?.user?.rank }}{{ activeRequest?.user?.name }}</span></p>
                            </div>
                            <div class="flex-1 overflow-auto p-6 md:p-8 bg-slate-50">
                                <div class="bg-white rounded-2xl shadow-lg overflow-hidden" style="min-height: 500px;">
                                    <iframe 
                                        v-if="activeRequest?.attachment_path"
                                        :src="`/storage/${activeRequest.attachment_path}`" 
                                        class="w-full h-full min-h-[500px] border-0"
                                        frameborder="0">
                                    </iframe>
                                    <div v-else class="flex items-center justify-center h-96 text-slate-400">
                                        <div class="text-center">
                                            <i data-lucide="file-x" class="w-16 h-16 mx-auto mb-4 text-slate-300"></i>
                                            <p class="text-lg font-medium">ไม่พบเอกสารแนบ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white px-6 py-4 md:px-8 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100">
                                <a :href="`/storage/${activeRequest?.attachment_path}`" target="_blank"
                                    class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-1">
                                    <i data-lucide="download" class="w-4 h-4 mr-2"></i>
                                    ดาวน์โหลดเอกสาร
                                </a>
                                <button type="button" @click="closeModal"
                                    class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-white border border-slate-200 text-slate-400 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-100 transition-all">
                                    ปิด
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

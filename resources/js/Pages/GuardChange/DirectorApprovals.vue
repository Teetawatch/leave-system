<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { thaiFullDate, thaiDate } from '@/utils/date';

const props = defineProps({ requests: Array });
const activeReq = ref(null);
const showModal = ref(false);
const showRejectModal = ref(false);

const form = useForm({ comment: '', signature: '', use_saved_signature: '0' });
const rejectForm = useForm({ comment: '' });

const dutyPositions = {
    senior_duty_officer: 'นายทหารเวรอาวุโส',
    duty_officer: 'นายทหารเวร',
    assistant_duty_officer: 'ผู้ช่วยนายทหารเวร',
};

const formatDate = thaiFullDate;
const formatShortDate = thaiDate;

// User & Signature
const page = usePage();
const currentUser = computed(() => page.props.auth.user);
const savedSignatureUrl = computed(() => currentUser.value.signature ? `/storage/${currentUser.value.signature}` : null);

const signatureMode = ref('saved');
const signatureCanvas = ref(null);
const isDrawing = ref(false);

let ctx = null;
let lastX = 0, lastY = 0;

function switchSignatureMode(mode) {
    signatureMode.value = mode;
    form.signature = '';
    if (mode === 'draw') {
        setTimeout(() => {
            if (signatureCanvas.value) {
                ctx = signatureCanvas.value.getContext('2d');
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#6B21A8'; // purple-800
            }
        }, 100);
    }
}

function startDraw(e) {
    if (signatureMode.value !== 'draw' || !signatureCanvas.value) return;
    isDrawing.value = true;
    const rect = signatureCanvas.value.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    lastX = clientX - rect.left;
    lastY = clientY - rect.top;
}

function draw(e) {
    if (!isDrawing.value) return;
    e.preventDefault();
    const rect = signatureCanvas.value.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    const currentX = clientX - rect.left;
    const currentY = clientY - rect.top;

    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
    ctx.lineTo(currentX, currentY);
    ctx.stroke();

    lastX = currentX;
    lastY = currentY;
}

function stopDraw() {
    if (!isDrawing.value) return;
    isDrawing.value = false;
    form.signature = signatureCanvas.value.toDataURL('image/png');
}

function clearCanvas() {
    if (!signatureCanvas.value) return;
    ctx.clearRect(0, 0, signatureCanvas.value.width, signatureCanvas.value.height);
    form.signature = '';
}

// Modals
function openApprove(req) { 
    activeReq.value = req; 
    showModal.value = true; 
    switchSignatureMode('saved');
}
function openReject(req) {
    activeReq.value = req;
    showRejectModal.value = true;
}

function closeModal() { 
    showModal.value = false; 
    showRejectModal.value = false;
    activeReq.value = null; 
    form.reset(); 
    rejectForm.reset();
}

function submit() { 
    if (signatureMode.value === 'saved') {
        form.use_saved_signature = '1';
        form.signature = '';
    } else {
        form.use_saved_signature = '0';
    }
    form.post(`/guard-change/${activeReq.value.id}/director-approve`, { onSuccess: () => closeModal() }); 
}

function submitReject() {
    rejectForm.post(`/guard-change/${activeReq.value.id}/director-reject`, { onSuccess: () => closeModal() });
}

const isLoaded = ref(false);
onMounted(() => {
    setTimeout(() => { 
        if (window.lucide) window.lucide.createIcons(); 
        isLoaded.value = true;
    }, 150);
});
</script>

<template>
    <AppLayout title="อนุมัติคำขอเปลี่ยนยาม (รอง ผอ.)">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-[#f8fafc] font-sans selection:bg-purple-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-purple-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[30%] left-[-10%] w-[500px] h-[500px] bg-indigo-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[10%] w-[700px] h-[700px] bg-fuchsia-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-purple-100/50">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-purple-500"></span>
                            </span>
                            <span class="text-purple-700 text-[11px] font-black uppercase tracking-[0.2em]">การพิจารณาของ รอง ผอ.</span>
                        </div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-[1.25rem] flex items-center justify-center shadow-lg shadow-purple-500/30 border border-white/20">
                                <i data-lucide="shield-check" class="w-7 h-7 text-white"></i>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none">
                                อนุมัติการเข้า<span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-indigo-500">เปลี่ยนยาม</span>
                            </h1>
                        </div>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed ml-2 md:ml-[4.5rem]">
                            พิจารณาอนุมัติคำขอเปลี่ยนเวรยามที่ผ่านการยินยอมจากผู้ปฏิบัติหน้าที่แทนเรียบร้อยแล้ว พร้อมลงลายมือชื่ออิเล็กทรอนิกส์ในฐานะ รอง ผอ.
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="glass-card rounded-[2rem] px-8 py-5 flex items-center gap-6 shadow-sm">
                            <div class="w-12 h-12 rounded-[1rem] bg-amber-50 text-amber-500 flex items-center justify-center">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.3em] mb-0.5">รอพิจารณา</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-slate-800 leading-none">{{ requests?.length || 0 }}</span>
                                    <span class="text-xs font-bold text-slate-400 uppercase">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!requests || requests.length === 0" 
                     class="glass-card rounded-[3rem] p-32 text-center relative overflow-hidden flex flex-col items-center"
                     :class="{ 'opacity-100 scale-100': isLoaded, 'opacity-0 scale-95': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 200ms;">
                    <div class="w-40 h-40 bg-gradient-to-br from-purple-50 to-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 shadow-inner group border border-white relative">
                        <div class="absolute inset-0 bg-purple-400/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                        <i data-lucide="stamp" class="w-20 h-20 text-purple-300 group-hover:scale-110 group-hover:text-purple-400 transition-all duration-700 relative z-10"></i>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">ไม่มีรายการค้างพิจารณา</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg font-medium">รายการทั้งหมดได้รับการตรวจสอบและพิจารณาเรียบร้อยแล้วในฐานะ รอง ผอ.</p>
                </div>

                <!-- Request Cards -->
                <div v-else class="grid grid-cols-1 gap-8 mb-12">
                    <div v-for="(req, index) in requests" :key="req.id"
                         class="glass-card rounded-[2.5rem] p-8 md:p-10 hover:shadow-xl hover:shadow-purple-500/10 transition-all duration-500 relative overflow-hidden group/card"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-12': !isLoaded }" :style="`transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: ${150 + (index * 100)}ms;`">
                        <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-bl from-purple-100/40 to-transparent rounded-bl-full -mr-20 -mt-20 opacity-0 group-hover/card:opacity-100 transition-all duration-1000 pointer-events-none group-hover/card:scale-110"></div>

                        <div class="flex flex-col lg:flex-row gap-10 lg:gap-14 relative z-10">
                            <!-- Requester Profile -->
                            <div class="flex-shrink-0 flex flex-row lg:flex-col items-center lg:items-start gap-6 lg:w-[15rem] min-w-0 pb-6 lg:pb-0 border-b lg:border-b-0 lg:border-r border-slate-200/60 lg:pr-10">
                                <div class="relative">
                                    <div class="w-24 h-24 lg:w-28 lg:h-28 rounded-[2rem] bg-gradient-to-br from-purple-50 to-white text-purple-400 flex items-center justify-center text-4xl font-black shadow-lg overflow-hidden border-4 border-white">
                                        <img v-if="req.user?.avatar" :src="`/storage/${req.user.avatar}`" class="w-full h-full object-cover transform group-hover/card:scale-110 transition-transform duration-700">
                                        <span v-else>{{ req.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg border-2 border-white transform rotate-6 group-hover/card:-rotate-6 transition-transform duration-500">
                                        <i data-lucide="shield" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                                    </div>
                                </div>
                                <div class="flex-1 lg:flex-none flex flex-wrap items-center lg:items-start lg:flex-col gap-2 min-w-0 max-w-full overflow-hidden">
                                    <h4 class="text-xl font-black text-slate-900 leading-tight tracking-tight truncate w-full group-hover/card:text-purple-700 transition-colors">{{ req.user?.rank }}{{ req.user?.name }}</h4>
                                    <span class="text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] bg-purple-50 border border-purple-100/50 px-3 py-1 rounded-full whitespace-nowrap">ผู้ขอเปลี่ยนเวร</span>
                                    <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5 truncate w-full mt-1">
                                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-400"></i>
                                        {{ req.user?.department }}
                                    </span>
                                </div>
                            </div>

                            <!-- Body Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Duty Badges -->
                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-[1rem] text-xs font-black tracking-wide shadow-lg shadow-slate-200">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-purple-400"></i>
                                        {{ dutyPositions[req.duty_position] || req.duty_position }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50/80 border border-purple-200 text-purple-700 rounded-[1rem] text-xs font-black shadow-sm">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-purple-500"></i>
                                        ปฏิบัติหน้าที่วันที่ {{ formatDate(req.duty_date) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Replacement Info -->
                                    <div class="bg-white/60 rounded-[1.5rem] p-5 border border-white relative shadow-sm flex flex-col justify-center">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">ผู้ปฏิบัติหน้าที่แทน (Replacement)</p>
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-[1.2rem] bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold relative border-2 border-white shadow-sm">
                                                <img v-if="req.replacement_user?.avatar" :src="`/storage/${req.replacement_user.avatar}`" class="w-full h-full object-cover rounded-[1rem]">
                                                <span v-else><i data-lucide="user-check" class="w-6 h-6"></i></span>
                                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center text-white border-2 border-white">
                                                    <i data-lucide="check" class="w-3 h-3"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-800">{{ req.replacement_user?.rank }}{{ req.replacement_user?.name }}</p>
                                                <p class="text-[10px] font-bold text-slate-500 mt-0.5">{{ req.replacement_user?.department || 'บุคลากร' }}</p>
                                                <span class="text-[9px] font-black text-emerald-600 tracking-wider bg-emerald-50 px-2 py-0.5 rounded-full mt-1 inline-block border border-emerald-100">ผู้รับหน้าที่ยินยอมแล้ว</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remarks -->
                                    <div class="bg-white/60 rounded-[1.5rem] p-6 border border-white relative group/quote shadow-sm">
                                        <i data-lucide="message-square" class="absolute top-5 right-5 w-8 h-8 text-slate-100 group-hover/quote:text-purple-100 transition-colors"></i>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">เหตุผลประกอบการขอเปลี่ยนเวร</p>
                                        <p class="text-[14px] text-slate-700 font-bold leading-relaxed relative z-10">{{ req.remarks || '-' }}</p>
                                    </div>
                                    
                                    <!-- Status / Approver Info (if applicable) -->
                                    <div class="col-span-1 md:col-span-2 bg-indigo-50/50 rounded-[1.5rem] p-5 border border-indigo-100 flex items-start gap-4 shadow-inner">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-500 flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm border border-white">
                                            <i data-lucide="git-commit" class="w-5 h-5"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-0.5">สถานะปัจจุบัน</p>
                                                    <p class="text-[13px] font-black text-indigo-700 break-words pr-4">รอการอนุมัติระดับบริหาร (รอง ผอ.)</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-[11px] font-medium text-slate-500 pl-3 border-l-2 border-indigo-200">
                                                <span class="font-bold text-slate-600 block mb-1">ความเห็นผู้รับเวรแทน:</span>
                                                "{{ req.approval_comment || 'ไม่มีการให้เหตุผลเพิ่มเติม' }}" 
                                                <span class="text-slate-400 block mt-1">(เซ็นรับทราบแล้วเมื่อ {{ req.approved_at ? formatShortDate(req.approved_at) : '-' }})</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Executive Decisions -->
                            <div class="flex-shrink-0 lg:w-[15rem] flex flex-row lg:flex-col justify-center items-center gap-4 lg:pl-10 lg:border-l border-slate-200/60 pt-6 lg:pt-0 border-t lg:border-t-0">
                                <button @click="openApprove(req)"
                                    class="flex-1 lg:flex-none w-full group/btn relative px-6 py-4 lg:py-5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-[1.5rem] shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 transition-all hover:-translate-y-1 overflow-hidden border border-purple-400/50">
                                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-indigo-700 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                    <div class="relative flex items-center justify-center gap-2.5">
                                        <i data-lucide="stamp" class="w-5 h-5 group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-[13px] font-black uppercase tracking-[0.15em] whitespace-nowrap">พิจารณาอนุมัติ</span>
                                    </div>
                                </button>
                                <button @click="openReject(req)"
                                    class="flex-1 lg:flex-none w-full group/btn relative px-6 py-4 bg-white/80 text-rose-500 border border-white rounded-[1.5rem] shadow-sm hover:bg-rose-50 hover:border-rose-200 transition-all hover:-translate-y-0.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <i data-lucide="x-circle" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-xs font-black uppercase tracking-[0.1em] whitespace-nowrap">ไม่อนุมัติ</span>
                                    </div>
                                </button>
                                <a :href="`/guard-change/${req.id}/export-pdf`" target="_blank"
                                    class="relative mt-2 lg:mt-4 inline-flex items-center justify-center gap-1.5 px-6 py-2 bg-amber-50 text-amber-600 rounded-xl text-[10px] font-black tracking-widest uppercase hover:bg-amber-100 transition-colors w-full lg:w-auto">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> พิมพ์เอกสาร
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal with Signature -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showModal" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white/95 backdrop-blur-3xl rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-xl border border-white">
                            <form @submit.prevent="submit">
                                <div class="p-8 md:p-10">
                                    <div class="flex items-start justify-between mb-8">
                                        <div class="w-14 h-14 rounded-[1.2rem] bg-purple-50 text-purple-600 flex items-center justify-center shadow-inner">
                                            <i data-lucide="stamp" class="w-7 h-7"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full bg-slate-100/50 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">ยืนยันการอนุมัติ <span class="text-purple-600">(รอง ผอ.)</span></h3>
                                    <p class="text-slate-500 font-medium mb-6 text-sm">พิจารณาอนุมัติคำขอเปลี่ยนยามของ <span class="font-black text-purple-600">{{ activeReq?.user?.rank }}{{ activeReq?.user?.name }}</span></p>

                                    <div class="mb-6 p-4 bg-white rounded-2xl border border-purple-100/50 shadow-sm flex items-center gap-4">
                                        <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500 flex-shrink-0">
                                            <i data-lucide="shield" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-black text-slate-800">{{ dutyPositions[activeReq?.duty_position] || activeReq?.duty_position }}</p>
                                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">เข้าเวรวันที่ {{ formatDate(activeReq?.duty_date) }}</p>
                                        </div>
                                    </div>

                                    <!-- Signature Section -->
                                    <div class="mb-6">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] flex items-center gap-2 mb-3">
                                            <i data-lucide="pen-tool" class="w-3.5 h-3.5"></i>
                                            ลายเซ็นอิเล็กทรอนิกส์ (E-SIGNATURE)
                                        </label>

                                        <!-- Mode Toggle -->
                                        <div class="flex gap-2 mb-4 bg-slate-100 p-1.5 rounded-2xl">
                                            <button type="button" @click="switchSignatureMode('saved')"
                                                :class="signatureMode === 'saved' ? 'bg-white text-purple-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-[1.1rem] text-xs font-black uppercase tracking-wider transition-all">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i> ใช้ลายเซ็นที่บันทึก
                                            </button>
                                            <button type="button" @click="switchSignatureMode('draw')"
                                                :class="signatureMode === 'draw' ? 'bg-white text-purple-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-[1.1rem] text-xs font-black uppercase tracking-wider transition-all">
                                                <i data-lucide="pen" class="w-3.5 h-3.5"></i> เซ็นใหม่
                                            </button>
                                        </div>

                                        <!-- Saved Signature Display -->
                                        <div v-if="signatureMode === 'saved'">
                                            <div v-if="savedSignatureUrl" class="relative bg-white border border-purple-100 shadow-inner rounded-2xl p-6 flex flex-col items-center justify-center min-h-[140px]">
                                                <img :src="savedSignatureUrl" alt="ลายเซ็น" class="max-h-24 max-w-full object-contain drop-shadow-sm">
                                            </div>
                                            <div v-else class="bg-amber-50/50 border border-amber-200/50 rounded-2xl p-6 text-center">
                                                <i data-lucide="alert-circle" class="w-8 h-8 text-amber-400 mx-auto mb-3"></i>
                                                <p class="text-sm font-bold text-amber-600">ยังไม่มีลายเซ็นที่บันทึกไว้</p>
                                                <p class="text-xs text-amber-500/80 mt-1">ตั้งค่าลายเซ็นในโปรไฟล์ หรือคลิก "เซ็นใหม่"</p>
                                            </div>
                                        </div>

                                        <!-- Draw Signature Canvas -->
                                        <div v-if="signatureMode === 'draw'" class="space-y-3">
                                            <div class="relative bg-white border border-slate-200 shadow-inner rounded-2xl overflow-hidden" style="touch-action: none;">
                                                <canvas ref="signatureCanvas" width="500" height="140"
                                                    class="w-full cursor-crosshair block bg-white"
                                                    @mousedown="startDraw" @mousemove="draw" @mouseup="stopDraw" @mouseleave="stopDraw"
                                                    @touchstart="startDraw" @touchmove="draw" @touchend="stopDraw">
                                                </canvas>
                                                <div v-if="!form.signature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                    <span class="text-slate-300 text-sm font-bold tracking-widest uppercase flex items-center gap-2"><i data-lucide="pen-tool" class="w-4 h-4 opacity-50"></i> ลงลายมือชื่อที่นี่...</span>
                                                </div>
                                            </div>
                                            <div class="flex justify-end">
                                                <button type="button" @click="clearCanvas"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-[11px] font-black text-slate-500 bg-white shadow-sm border border-slate-200 hover:bg-slate-50 hover:text-slate-700 rounded-xl transition-all uppercase tracking-widest">
                                                    <i data-lucide="eraser" class="w-3 h-3"></i> ล้างข้อมูล
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Comment -->
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ข้อความกำกับ (ถ้ามี)</label>
                                        <textarea v-model="form.comment" rows="2"
                                            class="block w-full rounded-2xl border-slate-200 bg-white shadow-inner focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-300 resize-none"
                                            placeholder="ระบุความคิดเห็นของคุณ..."></textarea>
                                    </div>
                                </div>
                                <div class="bg-white/80 border-t border-slate-100 p-6 md:px-10 flex flex-col sm:flex-row-reverse gap-3">
                                    <button type="submit" :disabled="form.processing || (signatureMode === 'saved' && !savedSignatureUrl && signatureMode !== 'draw')"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-black uppercase tracking-widest text-xs rounded-[1.2rem] shadow-lg shadow-purple-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:hover:translate-y-0">
                                        <i data-lucide="stamp" class="w-4 h-4 mr-2"></i>
                                        {{ form.processing ? 'กำลังดำเนินการ...' : 'ยืนยันการอนุมัติ (รอง ผอ.)' }}
                                    </button>
                                    <button type="button" @click="closeModal"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-slate-50 border border-slate-200 text-slate-500 font-black uppercase tracking-widest text-xs rounded-[1.2rem] hover:bg-slate-100 transition-all">
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
                <div v-if="showRejectModal" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white/90 backdrop-blur-3xl rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-lg border border-white">
                            <form @submit.prevent="submitReject">
                                <div class="p-8 md:p-10">
                                    <div class="flex items-start justify-between mb-8">
                                        <div class="w-14 h-14 rounded-[1.2rem] bg-rose-50 text-rose-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full bg-slate-100/50 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">ไม่อนุมัติคำขอ</h3>
                                    <p class="text-slate-500 font-medium mb-8 text-sm">คุณกำลังจะปฏิเสธคำขอเปลี่ยนยามของ <span class="font-black text-rose-600">{{ activeReq?.user?.rank }}{{ activeReq?.user?.name }}</span></p>
                                    
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] flex items-center justify-between">
                                            <span>เหตุผลประกอบ <span class="opacity-60 ml-1 font-normal">*จำเป็น</span></span>
                                        </label>
                                        <textarea v-model="rejectForm.comment" rows="4" required
                                            class="block w-full rounded-2xl border-rose-100 bg-rose-50/30 focus:bg-white focus:border-rose-400 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-rose-300 resize-none shadow-inner"
                                            placeholder="ระบุเหตุผลในการไม่อนุมัติในครั้งนี้ เพื่อแจ้งกลับไปยังผู้ยื่นคำขอ..."></textarea>
                                        <p v-if="rejectForm.errors.comment" class="text-rose-500 text-[10px] font-black tracking-wide uppercase mt-2">{{ rejectForm.errors.comment }}</p>
                                    </div>
                                </div>
                                <div class="bg-white/80 border-t border-slate-100 p-6 md:px-10 flex flex-col sm:flex-row-reverse gap-3">
                                    <button type="submit" :disabled="rejectForm.processing"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white font-black uppercase tracking-widest text-xs rounded-[1.2rem] shadow-lg shadow-rose-500/20 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                        {{ rejectForm.processing ? 'กำลังดำเนินการ...' : 'ยืนยันไม่อนุมัติ' }}
                                    </button>
                                    <button type="button" @click="closeModal"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-slate-50 border border-slate-200 text-slate-500 font-black uppercase tracking-widest text-xs rounded-[1.2rem] hover:bg-slate-100 transition-all">
                                        ยกเลิก
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
/* Liquid Glass Aesthetic */
.glass-badge {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.glass-card {
    background: rgba(255, 255, 255, 0.65);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 1);
}
.glass-card:hover {
    background: rgba(255, 255, 255, 0.8);
    border-color: rgba(255, 255, 255, 1);
}

/* Animations */
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
    animation: blob 15s infinite cubic-bezier(0.4, 0, 0.2, 1);
}
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
</style>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import { thaiFullDate } from '@/utils/date';

const props = defineProps({ requests: Object });
const page = usePage();
const authUser = computed(() => page.props.auth?.user);
const savedSignatureUrl = computed(() => {
    const sig = authUser.value?.signature;
    if (!sig) return null;
    // strip leading slash if any, normalize path
    const path = sig.replace(/^\/storage\//, '');
    return `/storage/${path}`;
});

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
    approveForm.reset();
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

function getPendingApprover(req) {
    if (req.status === 'pending_supervisor' && req.user?.supervisor) {
        return { name: (req.user.supervisor.rank || '') + req.user.supervisor.name, role: 'ผู้บังคับบัญชาชั้นต้น', avatar: req.user.supervisor.avatar };
    }
    if (req.status === 'pending_manager' && req.user?.manager) {
        return { name: (req.user.manager.rank || '') + req.user.manager.name, role: 'ผู้บังคับบัญชาตามลำดับชั้น', avatar: req.user.manager.avatar };
    }
    if (req.status === 'pending_deputy_director') {
        return { name: 'รองผู้อำนวยการ', role: 'ผู้ตรวจสอบ', avatar: null };
    }
    if (req.status === 'pending_director') {
        return { name: 'ผู้อำนวยการ', role: 'ผู้อนุมัติ', avatar: null };
    }
    return { name: 'อยู่ระหว่างดำเนินการ', role: req.status ? req.status.replace(/_/g, ' ') : 'PENDING', avatar: null };
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
    <AppLayout title="ระบบอนุมัติใบลา">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-slate-50 font-sans selection:bg-indigo-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-indigo-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[30%] left-[-10%] w-[500px] h-[500px] bg-violet-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[10%] w-[700px] h-[700px] bg-sky-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <!-- Main Content -->
            <div class="relative z-10 max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-indigo-100/50">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-500"></span>
                            </span>
                            <span class="text-indigo-700 text-[11px] font-black uppercase tracking-[0.2em]">ระบบอนุมัติการทำงาน</span>
                        </div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-[1.25rem] flex items-center justify-center shadow-lg shadow-indigo-500/30 border border-white/20">
                                <i data-lucide="shield-check" class="w-7 h-7 text-white"></i>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none">
                                ระบบ<span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-500">อนุมัติใบลา</span>
                            </h1>
                        </div>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed ml-2 md:ml-[4.5rem]">
                            ตรวจสอบและพิจารณาคำขอลาของบุคลากรในความปกครอง ดำเนินการอนุมัติหรือปฏิเสธพร้อมลงลายมือชื่ออิเล็กทรอนิกส์
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="glass-card rounded-[2rem] px-8 py-5 flex items-center gap-6 shadow-sm">
                            <div class="w-12 h-12 rounded-[1rem] bg-amber-50 text-amber-500 flex items-center justify-center">
                                <i data-lucide="inbox" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-amber-500 uppercase tracking-[0.3em] mb-0.5">รอดำเนินการ</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-slate-800 leading-none">{{ requests?.data?.length || requests?.total || 0 }}</span>
                                    <span class="text-xs font-bold text-slate-400 uppercase">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="!requests?.data || requests?.data?.length === 0" 
                     class="glass-card rounded-[3rem] p-32 text-center relative overflow-hidden flex flex-col items-center"
                     :class="{ 'opacity-100 scale-100': isLoaded, 'opacity-0 scale-95': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 200ms;">
                    <div class="w-40 h-40 bg-gradient-to-br from-indigo-50 to-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 shadow-inner group border border-white relative">
                        <div class="absolute inset-0 bg-indigo-400/20 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                        <i data-lucide="check-circle" class="w-20 h-20 text-indigo-300 group-hover:scale-110 group-hover:text-indigo-400 transition-all duration-700 relative z-10"></i>
                    </div>
                    <h3 class="text-3xl font-black text-slate-900 mb-4 tracking-tight">ไม่มีรายการรออนุมัติ</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg font-medium">ขณะนี้ไม่มีคำขอลาที่ต้องดำเนินการ ขอบคุณที่บริหารจัดการอย่างรวดเร็ว</p>
                </div>

                <!-- Request Cards -->
                <div v-else class="grid grid-cols-1 gap-8 mb-12">
                    <div v-for="(req, index) in requests.data" :key="req.id"
                         class="glass-card rounded-[2.5rem] p-8 md:p-10 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-500 relative overflow-hidden group/card"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-12': !isLoaded }" :style="`transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: ${150 + (index * 100)}ms;`">
                        <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-bl from-indigo-100/40 to-transparent rounded-bl-full -mr-20 -mt-20 opacity-0 group-hover/card:opacity-100 transition-all duration-1000 pointer-events-none group-hover/card:scale-110"></div>

                        <div class="flex flex-col lg:flex-row gap-10 lg:gap-14 relative z-10">
                            <!-- Requester Profile -->
                            <div class="flex-shrink-0 flex flex-row lg:flex-col items-center lg:items-start gap-6 lg:w-[15rem] min-w-0 pb-6 lg:pb-0 border-b lg:border-b-0 lg:border-r border-slate-200/60 lg:pr-10">
                                <div class="relative">
                                    <div class="w-24 h-24 lg:w-28 lg:h-28 rounded-[2rem] bg-gradient-to-br from-indigo-50 to-white text-indigo-400 flex items-center justify-center text-4xl font-black shadow-lg overflow-hidden border-4 border-white">
                                        <img v-if="req.user?.avatar" :src="`/storage/${req.user.avatar}`" class="w-full h-full object-cover transform group-hover/card:scale-110 transition-transform duration-700">
                                        <span v-else>{{ req.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center text-white shadow-lg border-2 border-white transform rotate-6 group-hover/card:-rotate-6 transition-transform duration-500">
                                        <i data-lucide="user" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                                    </div>
                                </div>
                                <div class="flex-1 lg:flex-none flex flex-wrap items-center lg:items-start lg:flex-col gap-2 min-w-0 max-w-full overflow-hidden">
                                    <h4 class="text-xl font-black text-slate-900 leading-tight tracking-tight truncate w-full group-hover/card:text-indigo-700 transition-colors">{{ req.user?.rank }}{{ req.user?.name }}</h4>
                                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] bg-indigo-50 border border-indigo-100/50 px-3 py-1 rounded-full whitespace-nowrap">ผู้ขอลา</span>
                                    <span class="text-xs font-bold text-slate-500 flex items-center gap-1.5 truncate w-full mt-1">
                                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-slate-400"></i>
                                        {{ req.user?.department }}
                                    </span>
                                </div>
                            </div>

                            <!-- Body Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Leave Type & Date Badges -->
                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100/50 text-indigo-700 rounded-[1rem] text-xs font-black tracking-[0.05em] shadow-sm border border-indigo-200/50">
                                        <i data-lucide="bookmark" class="w-3.5 h-3.5"></i>
                                        {{ req.leave_type?.name }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/70 border border-white/80 text-slate-800 rounded-[1rem] text-xs font-black shadow-sm">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-indigo-500"></i>
                                        {{ req.start_date_thai || formatDate(req.start_date) }} <span class="text-slate-400 font-normal mx-1">—</span> {{ req.end_date_thai || formatDate(req.end_date) }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-100 text-amber-700 rounded-[1rem] text-[11px] font-black shadow-sm uppercase">
                                        <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-500"></i>
                                        รวม {{ req.total_days }} วัน
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                    <!-- Request Details -->
                                    <div class="space-y-4">
                                        <!-- Reason -->
                                        <div v-if="req.reason" class="bg-white/60 rounded-[1.5rem] p-6 border border-white relative group/quote hover:bg-white transition-colors shadow-sm">
                                            <i data-lucide="message-square" class="absolute top-5 right-5 w-8 h-8 text-slate-100 group-hover/quote:text-indigo-50 transition-colors"></i>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1.5">เหตุผลการลา</p>
                                            <p class="text-[14px] text-slate-700 font-bold leading-relaxed relative z-10">{{ req.reason }}</p>
                                        </div>
                                        
                                        <!-- Contact Address -->
                                        <div v-if="req.contact_address" class="bg-white/60 rounded-[1.5rem] p-5 border border-white relative hover:bg-white transition-colors shadow-sm flex gap-4 items-start">
                                            <div class="w-8 h-8 rounded-full bg-violet-50 text-violet-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-0.5">ติดต่อได้ที่</p>
                                                <p class="text-[13px] text-slate-600 font-bold leading-snug">
                                                    {{ req.contact_address?.address }} 
                                                    {{ req.contact_address?.sub_district }} {{ req.contact_address?.district }}
                                                    {{ req.contact_address?.province }} {{ req.contact_address?.zip_code }}
                                                    <span v-if="req.contact_address?.phone" class="block pt-1.5 text-indigo-600 font-black flex items-center gap-1"><i data-lucide="phone" class="w-3 h-3"></i>{{ req.contact_address.phone }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Attachment -->
                                        <div v-if="req.attachment_path">
                                            <button @click="openAttachment(req)"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/70 text-indigo-700 border border-indigo-100 rounded-[1.2rem] text-xs font-black tracking-wide hover:bg-indigo-50 shadow-sm transition-all group/attach">
                                                <i data-lucide="paperclip" class="w-4 h-4 group-hover/attach:-rotate-12 group-hover/attach:scale-110 transition-transform"></i>
                                                เปิดดูเอกสารแนบ
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Approval Timeline -->
                                    <div class="bg-white/40 rounded-[1.5rem] p-6 border border-white shadow-[inset_0_2px_10px_rgba(255,255,255,0.4)]">
                                        <div class="flex items-center gap-2 mb-6">
                                            <div class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-500 border border-indigo-100 flex items-center justify-center shadow-sm">
                                                <i data-lucide="git-branch" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <h4 class="text-xs font-black text-slate-700 tracking-wider uppercase">TIMELINE การอนุมัติ</h4>
                                        </div>
                                        
                                        <div class="relative pl-7 border-l-2 border-slate-200/80 space-y-7 mt-2">
                                            <!-- Previous Approvals -->
                                            <div v-for="(approval, aIdx) in req.approvals" :key="aIdx" class="relative group/time">
                                                <!-- Node point -->
                                                <div class="absolute -left-[35px] top-2 w-4 h-4 rounded-full border-4 border-white shadow-sm transition-transform group-hover/time:scale-125"
                                                     :class="approval.status === 'approved' ? 'bg-emerald-500' : (approval.status === 'rejected' ? 'bg-rose-500' : 'bg-slate-400')"></div>
                                                
                                                <div class="flex items-start gap-3">
                                                    <!-- Avatar -->
                                                    <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 shadow-sm overflow-hidden flex-shrink-0 flex items-center justify-center text-slate-400 font-black text-xs">
                                                        <img v-if="approval.approver?.avatar" :src="`/storage/${approval.approver.avatar}`" class="w-full h-full object-cover">
                                                        <span v-else>{{ approval.approver?.name?.charAt(0) || '?' }}</span>
                                                    </div>
                                                    <!-- Info -->
                                                    <div class="flex flex-col gap-0.5 flex-1 min-w-0 pb-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <p class="text-[12px] font-black text-slate-800">{{ approval.approver?.rank || '' }}{{ approval.approver?.name || 'ผู้พิจารณา' }}</p>
                                                            <span class="text-[9px] font-black tracking-wide uppercase px-2 py-0.5 rounded-md"
                                                                :class="approval.status === 'approved' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : (approval.status === 'rejected' ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-slate-50 text-slate-600 border border-slate-200')">
                                                                {{ approval.status === 'approved' ? 'อนุมัติแล้ว' : (approval.status === 'rejected' ? 'ไม่อนุมัติ' : 'พิจารณาแล้ว') }}
                                                            </span>
                                                        </div>
                                                        <p class="text-[10px] font-bold text-slate-400" v-if="approval.approved_at">{{ new Date(approval.approved_at).toLocaleDateString('th-TH', {day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'}) }} น.</p>
                                                        <div v-if="approval.comment" class="mt-2 text-[11px] text-slate-600 font-medium italic relative">
                                                            <div class="bg-white/70 px-3 py-2 rounded-xl border border-white shadow-sm relative z-10 inline-block">
                                                                "{{ approval.comment }}"
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Current Pending Step -->
                                            <div class="relative group/time">
                                                <div class="absolute -left-[35px] top-2 w-4 h-4 rounded-full border-4 border-amber-50 bg-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.5)] animate-pulse"></div>
                                                
                                                <div class="flex items-start gap-3 opacity-90">
                                                    <!-- Avatar -->
                                                    <div class="w-9 h-9 rounded-full bg-amber-50 border border-amber-200 shadow-sm overflow-hidden flex-shrink-0 flex items-center justify-center text-amber-500 font-black text-xs relative">
                                                        <div class="absolute inset-0 bg-amber-400/20 animate-pulse"></div>
                                                        <img v-if="getPendingApprover(req).avatar" :src="`/storage/${getPendingApprover(req).avatar}`" class="w-full h-full object-cover">
                                                        <i v-else data-lucide="clock" class="w-4 h-4 relative z-10 text-amber-500"></i>
                                                    </div>
                                                    <div class="pb-1">
                                                        <span class="text-[10px] font-black text-amber-600 tracking-wide uppercase flex items-center gap-1.5 mb-0.5">รอพิจารณาขั้นต่อไป</span>
                                                        <p class="text-[12px] font-black text-slate-700">{{ getPendingApprover(req).name }}</p>
                                                        <p class="text-[10px] font-bold text-slate-400">{{ getPendingApprover(req).role }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Executive Decisions -->
                            <div class="flex-shrink-0 lg:w-[15rem] flex flex-row lg:flex-col justify-center items-center gap-4 lg:pl-10 lg:border-l border-slate-200/60 pt-6 lg:pt-0 border-t lg:border-t-0">
                                <button @click="openApprove(req)"
                                    class="flex-1 lg:flex-none w-full group/btn relative px-6 py-4 lg:py-5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-[1.5rem] shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all hover:-translate-y-1 overflow-hidden border border-emerald-400/50">
                                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-teal-600 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                    <div class="relative flex items-center justify-center gap-2.5">
                                        <i data-lucide="check-circle-2" class="w-5 h-5 group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-[13px] font-black uppercase tracking-[0.15em] whitespace-nowrap">{{ actionLabel(req.status) }}</span>
                                    </div>
                                </button>
                                <button @click="openReject(req)"
                                    class="flex-1 lg:flex-none w-full group/btn relative px-6 py-4 bg-white/80 text-rose-500 border border-white rounded-[1.5rem] shadow-sm hover:bg-rose-50 hover:border-rose-200 transition-all hover:-translate-y-0.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <i data-lucide="x-circle" class="w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-xs font-black uppercase tracking-[0.1em] whitespace-nowrap">ไม่อนุมัติ</span>
                                    </div>
                                </button>
                                <Link :href="`/leave-request/${req.id}`"
                                    class="hidden lg:flex w-full items-center justify-center gap-1.5 px-6 py-3 text-[10px] font-black text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50/50 rounded-xl uppercase tracking-widest transition-all">
                                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> เปิดดูเต็มจอ
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests?.links && requests.links.length > 3" class="mt-8 flex justify-center pb-12"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 400ms;">
                    <div class="glass-badge p-2 rounded-[1.5rem] shadow-sm border border-white flex gap-1">
                        <template v-for="(link, i) in requests.links" :key="i">
                            <Link v-if="link.url" :href="link.url" class="px-5 py-2.5 rounded-xl text-xs font-black transition-all"
                                :class="link.active ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : 'bg-transparent text-slate-500 hover:bg-white border border-transparent hover:border-white hover:text-slate-700'" v-html="link.label" />
                            <span v-else class="px-4 py-2.5 text-xs text-slate-300 font-bold" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shared Modals (Approve, Reject, Attachment) that existed before -->
        <!-- Approve Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="activeModal === 'approve'" class="fixed inset-0 z-[100] overflow-y-auto w-full h-full">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white/90 backdrop-blur-3xl rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-xl border border-white">
                            <form @submit.prevent="submitApprove">
                                <div class="p-8 md:p-10">
                                    <div class="flex items-start justify-between mb-8">
                                        <div class="w-14 h-14 rounded-[1.2rem] bg-indigo-50 text-indigo-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="check-circle" class="w-7 h-7"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full bg-slate-100/50 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                            <i data-lucide="x" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">ยืนยันการอนุมัติ</h3>
                                    <p class="text-slate-500 font-medium mb-6 text-sm">พิจารณาอนุมัติคำขอลาของ <span class="font-black text-indigo-600">{{ activeRequest?.user?.rank }}{{ activeRequest?.user?.name }}</span></p>

                                    <div class="mb-6 p-4 bg-white rounded-2xl border border-indigo-100/50 shadow-sm flex items-center gap-4">
                                        <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500 flex-shrink-0">
                                            <i data-lucide="bookmark" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-[13px] font-black text-slate-800">{{ activeRequest?.leave_type?.name }}</p>
                                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">{{ activeRequest?.total_days }} วัน <span class="font-normal text-slate-400 mx-1">•</span> {{ activeRequest?.start_date_thai }} <span class="font-normal text-slate-400 mx-1">-</span> {{ activeRequest?.end_date_thai }}</p>
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
                                                :class="signatureMode === 'saved' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-[1.1rem] text-xs font-black uppercase tracking-wider transition-all">
                                                <i data-lucide="image" class="w-3.5 h-3.5"></i> ใช้ลายเซ็นที่บันทึก
                                            </button>
                                            <button type="button" @click="switchSignatureMode('draw')"
                                                :class="signatureMode === 'draw' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                                class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-[1.1rem] text-xs font-black uppercase tracking-wider transition-all">
                                                <i data-lucide="pen" class="w-3.5 h-3.5"></i> เซ็นใหม่
                                            </button>
                                        </div>

                                        <!-- Saved Signature Display -->
                                        <div v-if="signatureMode === 'saved'">
                                            <div v-if="savedSignatureUrl" class="relative bg-white border border-indigo-100 shadow-inner rounded-2xl p-6 flex flex-col items-center justify-center min-h-[140px]">
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
                                                <div v-if="!approveForm.signature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
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
                                        <textarea v-model="approveForm.comment" rows="2"
                                            class="block w-full rounded-2xl border-slate-200 bg-white shadow-inner focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-300 resize-none"
                                            placeholder="ระบุความคิดเห็นของคุณ..."></textarea>
                                    </div>
                                </div>
                                <div class="bg-white/80 border-t border-slate-100 p-6 md:px-10 flex flex-col sm:flex-row-reverse gap-3">
                                    <button type="submit" :disabled="approveForm.processing || (signatureMode === 'saved' && !savedSignatureUrl && signatureMode !== 'draw')"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-[1.2rem] shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:hover:translate-y-0">
                                        <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                                        {{ approveForm.processing ? 'กำลังดำเนินการ...' : 'ยืนยันการอนุมัติ' }}
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
                <div v-if="activeModal === 'reject'" class="fixed inset-0 z-[100] overflow-y-auto">
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
                                    <p class="text-slate-500 font-medium mb-8 text-sm">คุณกำลังจะปฏิเสธคำขอลาของ <span class="font-black text-rose-600">{{ activeRequest?.user?.rank }}{{ activeRequest?.user?.name }}</span></p>
                                    
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

        <!-- Attachment Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="activeModal === 'attachment'" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white/95 backdrop-blur-3xl rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-4xl flex flex-col border border-white max-h-[90vh]">
                            <div class="p-6 md:p-8 flex-shrink-0 border-b border-slate-100/50">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-[1rem] bg-indigo-50 text-indigo-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="paperclip" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-black text-slate-900 tracking-tight">เอกสารแนบประกอบการลา</h3>
                                            <p class="text-slate-500 font-bold text-xs mt-0.5">จากคำขอลาของ <span class="text-slate-700">{{ activeRequest?.user?.rank }}{{ activeRequest?.user?.name }}</span></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="closeModal" class="w-10 h-10 rounded-full bg-slate-100/50 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex-1 overflow-auto bg-slate-50/50 p-6 md:p-8">
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden" style="min-height: 500px; height: 100%;">
                                    <iframe 
                                        v-if="activeRequest?.attachment_path"
                                        :src="`/storage/${activeRequest.attachment_path}`" 
                                        class="w-full h-full min-h-[500px] border-0"
                                        frameborder="0">
                                    </iframe>
                                    <div v-else class="flex flex-col items-center justify-center h-[500px] text-slate-400">
                                        <i data-lucide="file-x" class="w-16 h-16 mb-4 text-slate-300"></i>
                                        <p class="text-[13px] font-black uppercase tracking-widest text-slate-400">ไม่พบเอกสารแนบ</p>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-5 md:px-8 flex flex-col sm:flex-row-reverse gap-3 border-t border-slate-100/50 bg-white">
                                <a :href="`/storage/${activeRequest?.attachment_path}`" target="_blank"
                                    class="flex-1 inline-flex justify-center items-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-lg shadow-indigo-500/20 transition-all hover:-translate-y-0.5">
                                    <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                                    เปิดในแท็บใหม่ / ดาวน์โหลด
                                </a>
                                <button type="button" @click="closeModal"
                                    class="flex-1 inline-flex justify-center items-center px-8 py-3.5 bg-slate-50 border border-slate-200 text-slate-500 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-100 transition-all">
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

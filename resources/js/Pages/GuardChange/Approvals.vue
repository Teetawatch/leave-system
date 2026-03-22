<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { thaiFullDate } from '@/utils/date';

const props = defineProps({ requests: Array });
const page = usePage();

const activeModal = ref(null);
const activeReq = ref(null);
const approveForm = useForm({ comment: '', signature: '', use_saved_signature: '0' });
const rejectForm = useForm({ comment: '' });

const dutyPositions = {
    senior_duty_officer: 'นายทหารเวรอาวุโส',
    duty_officer: 'นายทหารเวร',
    assistant_duty_officer: 'ผู้ช่วยนายทหารเวร',
};

const formatDate = thaiFullDate;

function openApprove(req) { activeReq.value = req; activeModal.value = 'approve'; }
function openReject(req) { activeReq.value = req; activeModal.value = 'reject'; }
function closeModal() { activeModal.value = null; activeReq.value = null; approveForm.reset(); rejectForm.reset(); }

function submitApprove() { approveForm.post(`/guard-change/${activeReq.value.id}/approve`, { onSuccess: () => closeModal() }); }
function submitReject() { rejectForm.post(`/guard-change/${activeReq.value.id}/reject`, { onSuccess: () => closeModal() }); }

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="อนุมัติคำขอเปลี่ยนยาม">
        <div class="min-h-screen bg-[#f8fafc] -m-4 md:-m-8">
            <!-- Cinematic Executive Header -->
            <div class="relative bg-white pt-16 pb-28 overflow-hidden border-b border-slate-100">
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                </div>
                <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div>
                            <nav class="flex items-center gap-2 text-emerald-600/60 transition-all mb-4 text-sm font-bold tracking-widest uppercase">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>ศูนย์การอนุมัติ</span>
                                <span class="w-1 h-1 rounded-full bg-emerald-500/20"></span>
                                <span class="text-emerald-600">การเปลี่ยนยาม</span>
                            </nav>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">คำขอเปลี่ยนยามรออนุมัติ</h1>
                            <p class="text-slate-500 max-w-xl text-lg font-medium leading-relaxed">ตรวจสอบและพิจารณาคำขอเปลี่ยนเวรยามของกำลังพล เพื่อความต่อเนื่องและประสิทธิภาพในการระวังป้องกันสถานที่</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl px-6 py-4 shadow-sm">
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-1">คำขอที่รออนุมัติ</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-black text-slate-900">{{ requests?.length || 0 }}</span>
                                    <span class="text-sm font-bold text-slate-400 uppercase">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20">
                <!-- Empty State -->
                <div v-if="!requests || requests.length === 0" class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-24 text-center">
                    <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 border-4 border-white shadow-inner group">
                        <i data-lucide="inbox" class="w-16 h-16 text-slate-200 group-hover:scale-110 group-hover:text-emerald-400 transition-all duration-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-3">ไม่มีรายการค้างพิจารณา</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg font-medium">คำขอเปลี่ยนเวรยามทั้งหมดได้รับการตรวจสอบเรียบร้อยแล้ว</p>
                </div>

                <!-- Request Cards -->
                <div v-else class="grid grid-cols-1 gap-8">
                    <div v-for="req in requests" :key="req.id"
                        class="group bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8 md:p-10 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-20 -mt-20 opacity-50 group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>

                        <div class="flex flex-col lg:flex-row gap-10 relative z-10">
                            <!-- User Column -->
                            <div class="flex-shrink-0 flex flex-row lg:flex-col items-center lg:items-start gap-6 lg:w-48 border-b lg:border-b-0 lg:border-r border-slate-100 pb-8 lg:pb-0 lg:pr-10">
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 flex items-center justify-center text-3xl font-black shadow-lg overflow-hidden ring-4 ring-white">
                                        <img v-if="req.user?.avatar" :src="`/storage/${req.user.avatar}`" class="w-full h-full object-cover">
                                        <span v-else>{{ req.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg border-2 border-white">
                                        <i data-lucide="user" class="w-5 h-5"></i>
                                    </div>
                                </div>
                                <div class="flex-1 lg:flex-none">
                                    <h4 class="text-xl font-black text-slate-900 leading-tight">{{ req.user?.rank }}{{ req.user?.name }}</h4>
                                    <p class="text-xs font-black text-indigo-500/60 uppercase tracking-widest mt-1 bg-indigo-50 px-2 py-0.5 rounded-md inline-block">{{ req.user?.department }}</p>
                                </div>
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-black tracking-wide shadow-lg shadow-slate-200">
                                        <i data-lucide="shield" class="w-3 h-3 text-indigo-400"></i>
                                        {{ dutyPositions[req.duty_position] || req.duty_position }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-black border border-indigo-100">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ formatDate(req.duty_date) }}
                                    </span>
                                </div>

                                <div class="bg-slate-50/80 rounded-3xl p-6 border border-slate-200/60 relative mb-6">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                                        <div class="flex-shrink-0 w-16 h-16 bg-white rounded-2xl flex items-center justify-center border border-slate-200 shadow-sm">
                                            <i data-lucide="repeat" class="w-8 h-8 text-emerald-500"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">REPLACEMENT PERSON</p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg font-black text-slate-800">{{ req.replacement_user?.rank }}{{ req.replacement_user?.name }}</span>
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            </div>
                                            <p class="text-xs font-bold text-slate-500">{{ req.replacement_user?.position || 'บุคลากร' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="req.remarks" class="flex gap-4 p-4 bg-indigo-50/30 rounded-2xl border border-dashed border-indigo-200">
                                    <i data-lucide="quote" class="w-5 h-5 text-indigo-300 flex-shrink-0"></i>
                                    <p class="text-sm text-slate-600 font-medium italic leading-relaxed">"{{ req.remarks }}"</p>
                                </div>
                            </div>

                            <!-- Action Column -->
                            <div class="flex-shrink-0 lg:w-48 flex flex-col justify-center items-center gap-4 lg:pl-10 lg:border-l border-slate-100">
                                <button @click="openApprove(req)"
                                    class="w-full group flex items-center justify-center gap-3 px-6 py-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 font-black uppercase tracking-widest text-xs">
                                    <i data-lucide="check" class="w-5 h-5 group-hover:scale-125 transition-transform"></i>
                                    อนุมัติ
                                </button>
                                <button @click="openReject(req)"
                                    class="w-full group flex items-center justify-center gap-3 px-6 py-5 bg-white border-2 border-slate-100 text-slate-400 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 rounded-2xl transition-all font-black uppercase tracking-widest text-xs">
                                    <i data-lucide="x" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                    ปฏิเสธ
                                </button>
                            </div>
                        </div>
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
                                <div class="bg-white p-8 md:p-12">
                                    <div class="flex items-start justify-between mb-8">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-emerald-50 text-emerald-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="check-circle" class="w-8 h-8"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                            <i data-lucide="x" class="w-6 h-6"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mb-2">ยืนยันการอนุมัติ</h3>
                                    <p class="text-slate-500 font-medium mb-8">พิจารณาอนุมัติคำขอเปลี่ยนเวรยามของ <span class="font-black text-slate-900">{{ activeReq?.user?.rank }}{{ activeReq?.user?.name }}</span></p>
                                    <div class="space-y-6">
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">COMMENT (OPTIONAL)</label>
                                            <textarea v-model="approveForm.comment" rows="2"
                                                class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium resize-none"
                                                placeholder="ระบุความคิดเห็นของคุณ..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-8 py-6 md:px-12 md:py-8 flex flex-col sm:flex-row-reverse gap-4">
                                    <button type="submit" :disabled="approveForm.processing"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 disabled:opacity-60">
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
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mb-2">ปฏิเสธคำขอ</h3>
                                    <p class="text-slate-500 font-medium mb-8">คุณกำลังจะไม่เห็นด้วยกับคำขอเปลี่ยนยามของ <span class="font-black text-slate-900">{{ activeReq?.user?.rank }}{{ activeReq?.user?.name }}</span></p>
                                    <div class="space-y-3">
                                        <label class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                            REASON FOR REJECTION <span class="text-rose-300 font-normal">* REQUIRED</span>
                                        </label>
                                        <textarea v-model="rejectForm.comment" rows="4" required
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium resize-none shadow-inner"
                                            placeholder="ระบุเหตุผลในการปฏิเสธครั้งนี้..."></textarea>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-8 py-6 md:px-12 md:py-8 flex flex-col sm:flex-row-reverse gap-4">
                                    <button type="submit" :disabled="rejectForm.processing"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-rose-500/20 transition-all hover:-translate-y-1 disabled:opacity-60">
                                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i>
                                        {{ rejectForm.processing ? 'กำลังดำเนินการ...' : 'ยืนยันปฏิเสธ' }}
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
    </AppLayout>
</template>

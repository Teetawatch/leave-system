<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { thaiFullDate, thaiDate } from '@/utils/date';

const props = defineProps({ requests: Array });
const activeReq = ref(null);
const showModal = ref(false);
const form = useForm({ comment: '', signature: '', use_saved_signature: '0' });

const dutyPositions = {
    senior_duty_officer: 'นายทหารเวรอาวุโส',
    duty_officer: 'นายทหารเวร',
    assistant_duty_officer: 'ผู้ช่วยนายทหารเวร',
};

const formatDate      = thaiFullDate;
const formatShortDate = thaiDate;

function openApprove(req) { activeReq.value = req; showModal.value = true; }
function closeModal() { showModal.value = false; activeReq.value = null; form.reset(); }
function submit() { form.post(`/guard-change/${activeReq.value.id}/final-approve`, { onSuccess: () => closeModal() }); }

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="อนุมัติคำขอเปลี่ยนยาม (ผอ.)">
        <div class="min-h-screen bg-[#f8fafc] -m-4 md:-m-8">
            <!-- Cinematic Command Header (Dark) -->
            <div class="relative bg-[#0f172a] pt-16 pb-28 overflow-hidden">
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-rose-500/10 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/10 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                </div>
                <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div>
                            <nav class="flex items-center gap-2 text-rose-300/60 transition-all mb-4 text-sm font-black tracking-widest uppercase">
                                <i data-lucide="crown" class="w-4 h-4"></i>
                                <span>การตัดสินใจ</span>
                                <span class="w-1 h-1 rounded-full bg-rose-500/40"></span>
                                <span class="text-rose-400">ผอ. อนุมัติขั้นสุดท้าย</span>
                            </nav>
                            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight mb-4">คำขอเปลี่ยนยามรอ ผอ. อนุมัติ</h1>
                            <p class="text-indigo-100/60 max-w-2xl text-lg font-medium leading-relaxed">ขั้นตอนสุดท้ายในการพิจารณาคำขอเปลี่ยนเวรยาม ท่านกำลังดำเนินการตรวจสอบความถูกต้อง หลังจากผ่านการเห็นชอบจากระดับฝ่ายการเจ้าหน้าที่และรองผู้อำนวยการเรียบร้อยแล้ว</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-white/5 border border-white/10 rounded-3xl px-8 py-6 backdrop-blur-md shadow-2xl">
                                <p class="text-[10px] font-black text-rose-400 uppercase tracking-[0.3em] mb-1">รอดำเนินการ</p>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-4xl font-black text-white">{{ requests?.length || 0 }}</span>
                                    <span class="text-sm font-bold text-indigo-300/40 uppercase">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 -mt-16 pb-20 relative z-20">
                <!-- Empty State -->
                <div v-if="!requests || requests.length === 0" class="bg-white rounded-[3rem] shadow-2xl shadow-slate-200/50 border border-slate-100 p-32 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-32 -mt-32"></div>
                    <div class="relative z-10">
                        <div class="w-40 h-40 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-10 border-8 border-white shadow-inner group">
                            <i data-lucide="crown" class="w-20 h-20 text-slate-200 group-hover:scale-110 group-hover:text-rose-400 transition-all duration-700"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">ไม่มีคำขอค้างพิจารณา</h3>
                        <p class="text-slate-500 max-w-sm mx-auto text-xl font-medium">ทุกรายการได้รับการตัดสินใจขั้นสุดท้ายโดยท่านเรียบร้อยแล้ว</p>
                    </div>
                </div>

                <!-- Request Cards -->
                <div v-else class="grid grid-cols-1 gap-10">
                    <div v-for="req in requests" :key="req.id"
                        class="group bg-white rounded-[3rem] shadow-2xl shadow-slate-200/40 border border-slate-100 p-10 md:p-12 hover:shadow-rose-500/10 transition-all duration-700 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-80 h-80 bg-rose-50 rounded-bl-full -mr-20 -mt-20 opacity-30 group-hover:scale-125 transition-transform duration-1000 pointer-events-none"></div>

                        <div class="flex flex-col xl:flex-row gap-12 relative z-10">
                            <!-- Requester Profile -->
                            <div class="flex-shrink-0 flex flex-row xl:flex-col items-center xl:items-start gap-8 xl:w-56 border-b xl:border-b-0 xl:border-r border-slate-100 pb-10 xl:pb-0 xl:pr-12">
                                <div class="relative">
                                    <div class="w-28 h-28 rounded-[2.5rem] bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 flex items-center justify-center text-4xl font-black shadow-xl overflow-hidden ring-8 ring-white">
                                        <img v-if="req.user?.avatar" :src="`/storage/${req.user.avatar}`" class="w-full h-full object-cover transform scale-110 group-hover:scale-125 transition-transform duration-700">
                                        <span v-else>{{ req.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-rose-600 rounded-2xl flex items-center justify-center text-white shadow-2xl border-4 border-white transform rotate-12 group-hover:rotate-0 transition-transform duration-500">
                                        <i data-lucide="star" class="w-6 h-6"></i>
                                    </div>
                                </div>
                                <div class="flex-1 xl:flex-none">
                                    <h4 class="text-2xl font-black text-slate-900 leading-tight tracking-tight">{{ req.user?.rank }}{{ req.user?.name }}</h4>
                                    <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] mt-2 bg-rose-50 px-3 py-1 rounded-full inline-block">ผู้ขอเปลี่ยนเวร</p>
                                    <p class="text-sm font-bold text-slate-400 mt-2 flex items-center gap-2">
                                        <i data-lucide="building-2" class="w-4 h-4"></i>
                                        {{ req.user?.department }}
                                    </p>
                                </div>
                            </div>

                            <!-- Body Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-4 mb-8">
                                    <span class="inline-flex items-center gap-3 px-6 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black tracking-[0.1em] shadow-xl shadow-slate-200 uppercase">
                                        <i data-lucide="shield" class="w-4 h-4 text-rose-400"></i>
                                        {{ dutyPositions[req.duty_position] || req.duty_position }}
                                    </span>
                                    <span class="inline-flex items-center gap-3 px-6 py-3 bg-white border-2 border-slate-100 text-slate-900 rounded-2xl text-xs font-black shadow-sm uppercase">
                                        <i data-lucide="calendar" class="w-4 h-4 text-indigo-500"></i>
                                        {{ formatDate(req.duty_date) }}
                                    </span>
                                </div>

                                <!-- Approval Chain Track -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                                    <div class="bg-emerald-50/50 rounded-[2.5rem] p-6 border-2 border-emerald-100 flex items-center gap-5">
                                        <div class="h-14 w-14 rounded-2xl bg-white shadow-md text-emerald-500 flex items-center justify-center">
                                            <i data-lucide="user-check" class="w-7 h-7"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-emerald-600/60 uppercase tracking-widest mb-1">ผู้รับเปลี่ยนเวร</p>
                                            <p class="text-base font-black text-slate-800">{{ req.replacement_user?.rank }}{{ req.replacement_user?.name }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-purple-50/50 rounded-[2.5rem] p-6 border-2 border-purple-100 flex items-center gap-5">
                                        <div class="h-14 w-14 rounded-2xl bg-white shadow-md text-purple-500 flex items-center justify-center">
                                            <i data-lucide="pen-line" class="w-7 h-7"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-black text-purple-600/60 uppercase tracking-widest mb-1">รอง ผอ. เห็นชอบ</p>
                                            <p class="text-base font-black text-slate-800">
                                                <template v-if="req.director_approver">{{ req.director_approver?.rank }}{{ req.director_approver?.name }}</template>
                                                <span v-else class="text-slate-300">-</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="req.remarks" class="bg-slate-50/80 rounded-[2rem] p-8 border border-slate-100 relative group/quote">
                                    <i data-lucide="quote" class="absolute top-4 right-6 w-12 h-12 text-slate-200/50 group-hover/quote:text-rose-200/50 transition-colors"></i>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">เหตุผลการเปลี่ยนเวร</p>
                                    <p class="text-lg text-slate-600 font-medium italic relative z-10">"{{ req.remarks }}"</p>
                                </div>
                            </div>

                            <!-- Executive Decision -->
                            <div class="flex-shrink-0 xl:w-64 flex flex-col justify-center items-center gap-6 xl:pl-12 xl:border-l-4 xl:border-slate-50">
                                <button @click="openApprove(req)"
                                    class="w-full group/btn relative px-8 py-6 bg-rose-600 text-white rounded-[2rem] shadow-2xl shadow-rose-600/20 hover:shadow-rose-600/40 transition-all hover:-translate-y-2 overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-r from-rose-500 to-rose-700 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                                    <div class="relative flex items-center justify-center gap-4">
                                        <i data-lucide="crown" class="w-6 h-6 group-hover/btn:scale-125 transition-transform"></i>
                                        <span class="text-sm font-black uppercase tracking-[0.2em]">อนุมัติ</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Approval Modal -->
        <Teleport to="body">
            <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <div v-if="showModal" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl" @click="closeModal"></div>
                        <div class="bg-white rounded-[4rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-2xl border-t-8 border-rose-600">
                            <form @submit.prevent="submit">
                                <div class="bg-white p-10 md:p-14">
                                    <div class="flex items-center justify-between mb-12">
                                        <div class="flex items-center gap-5">
                                            <div class="w-20 h-20 rounded-[2rem] bg-rose-50 text-rose-600 flex items-center justify-center shadow-inner">
                                                <i data-lucide="crown" class="w-10 h-10"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-4xl font-black text-slate-900 tracking-tighter">อนุมัติขั้นสุดท้าย</h3>
                                                <p class="text-rose-500 font-black text-xs uppercase tracking-widest">FINAL EXECUTIVE APPROVAL</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-14 h-14 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                            <i data-lucide="x" class="w-8 h-8"></i>
                                        </button>
                                    </div>

                                    <div class="mb-10 p-6 bg-slate-50/80 rounded-[2.5rem] border border-slate-100">
                                        <p class="text-sm font-bold text-slate-500">ยืนยันการอนุมัติขั้นสุดท้ายสำหรับ:</p>
                                        <p class="text-2xl font-black text-slate-900 mt-1">{{ activeReq?.user?.rank }}{{ activeReq?.user?.name }}</p>
                                        <div class="mt-4 flex items-center gap-3">
                                            <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm border border-slate-100 uppercase">{{ dutyPositions[activeReq?.duty_position] || activeReq?.duty_position }}</span>
                                            <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm border border-slate-100 uppercase">{{ formatShortDate(activeReq?.duty_date) }}</span>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-4">ความเห็น / ข้อสังเกต</label>
                                        <textarea v-model="form.comment" rows="3"
                                            class="block w-full rounded-[2rem] border-2 border-slate-100 bg-slate-50 focus:bg-white focus:border-rose-500 focus:ring-8 focus:ring-rose-500/5 transition-all p-6 text-base font-bold text-slate-700 placeholder:text-slate-300 resize-none shadow-inner"
                                            placeholder="ระบุข้อความคำสั่งเพิ่มเติม (ถ้ามี)..."></textarea>
                                    </div>
                                </div>

                                <div class="bg-slate-50 px-10 py-10 md:px-14 md:py-12 flex flex-col sm:flex-row-reverse gap-6 border-t border-slate-100">
                                    <button type="submit" :disabled="form.processing"
                                        class="relative flex-[2] inline-flex justify-center items-center px-10 py-6 bg-slate-900 text-white font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] shadow-2xl shadow-slate-900/40 hover:shadow-rose-600/40 hover:bg-rose-600 transition-all hover:-translate-y-2 group/submit disabled:opacity-60">
                                        <i data-lucide="check" class="w-6 h-6 mr-3 group-hover/submit:scale-125 transition-transform"></i>
                                        {{ form.processing ? 'กำลังดำเนินการ...' : 'บันทึกการตัดสินใจ' }}
                                    </button>
                                    <button type="button" @click="closeModal"
                                        class="flex-1 inline-flex justify-center items-center px-10 py-6 bg-white border-2 border-slate-200 text-slate-400 font-black uppercase tracking-[0.2em] text-sm rounded-[2rem] hover:bg-slate-100 hover:text-slate-600 transition-all shadow-sm">
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

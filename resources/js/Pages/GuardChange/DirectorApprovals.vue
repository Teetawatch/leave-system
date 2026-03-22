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

const formatDate = thaiFullDate;
const formatShortDate = thaiDate;

function openApprove(req) { activeReq.value = req; showModal.value = true; }
function closeModal() { showModal.value = false; activeReq.value = null; form.reset(); }
function submit() { form.post(`/guard-change/${activeReq.value.id}/director-approve`, { onSuccess: () => closeModal() }); }

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="อนุมัติคำขอเปลี่ยนยาม (รอง ผอ.)">
        <div class="min-h-screen bg-[#f8fafc] -m-4 md:-m-8">
            <!-- Executive Header -->
            <div class="relative bg-white pt-16 pb-28 overflow-hidden border-b border-slate-100">
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-purple-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                </div>
                <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div>
                            <nav class="flex items-center gap-2 text-purple-600/60 transition-all mb-4 text-sm font-bold tracking-widest uppercase">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>การอนุมัติระดับบริหาร</span>
                                <span class="w-1 h-1 rounded-full bg-purple-500/20"></span>
                                <span class="text-purple-600">การพิจารณาของ รอง ผอ.</span>
                            </nav>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">คำขอเปลี่ยนยามรอ รอง ผอ. อนุมัติ</h1>
                            <p class="text-slate-500 max-w-xl text-lg font-medium leading-relaxed">พิจารณาอนุมัติคำขอเปลี่ยนเวรยามที่ผ่านการยินยอมจากผู้ปฏิบัติหน้าที่แทนเรียบร้อยแล้ว เพื่อดำเนินการตามขั้นตอนสุดท้ายต่อไป</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="bg-purple-50 border border-purple-100 rounded-2xl px-6 py-4 shadow-sm">
                                <p class="text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] mb-1">รอการพิจารณา</p>
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
                        <i data-lucide="stamp" class="w-16 h-16 text-slate-200 group-hover:scale-110 group-hover:text-purple-400 transition-all duration-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-3">ไม่มีรายการค้างพิจารณา</h3>
                    <p class="text-slate-500 max-w-sm mx-auto text-lg font-medium">รายการทั้งหมดได้รับการตรวจสอบและพิจารณาเรียบร้อยแล้วในฐานะ รอง ผอ.</p>
                </div>

                <!-- Request Cards -->
                <div v-else class="grid grid-cols-1 gap-8">
                    <div v-for="req in requests" :key="req.id"
                        class="group bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/40 border border-slate-100 p-8 md:p-10 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-bl-full -mr-20 -mt-20 opacity-50 group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>

                        <div class="flex flex-col lg:flex-row gap-10 relative z-10">
                            <!-- User Column -->
                            <div class="flex-shrink-0 flex flex-row lg:flex-col items-center lg:items-start gap-6 lg:w-48 border-b lg:border-b-0 lg:border-r border-slate-100 pb-8 lg:pb-0 lg:pr-10">
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400 flex items-center justify-center text-3xl font-black shadow-lg overflow-hidden ring-4 ring-white">
                                        <img v-if="req.user?.avatar" :src="`/storage/${req.user.avatar}`" class="w-full h-full object-cover">
                                        <span v-else>{{ req.user?.name?.charAt(0) }}</span>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg border-2 border-white">
                                        <i data-lucide="award" class="w-5 h-5"></i>
                                    </div>
                                </div>
                                <div class="flex-1 lg:flex-none">
                                    <h4 class="text-xl font-black text-slate-900 leading-tight">{{ req.user?.rank }}{{ req.user?.name }}</h4>
                                    <p class="text-xs font-black text-purple-500/60 uppercase tracking-widest mt-1 bg-purple-50 px-2 py-0.5 rounded-md inline-block">{{ req.user?.department }}</p>
                                </div>
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-6">
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-black tracking-wide shadow-lg shadow-slate-200">
                                        <i data-lucide="shield" class="w-3 h-3 text-purple-400"></i>
                                        {{ dutyPositions[req.duty_position] || req.duty_position }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-black border border-indigo-100">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ formatDate(req.duty_date) }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-xs font-black border border-emerald-100">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        ผู้รับหน้าที่แทนยินยอมแล้ว
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="bg-slate-50/80 rounded-3xl p-5 border border-slate-200/60">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">REPLACEMENT PERSON</p>
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                                <i data-lucide="user-check" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-slate-800">{{ req.replacement_user?.rank }}{{ req.replacement_user?.name }}</p>
                                                <p class="text-[10px] font-bold text-slate-500">{{ req.replacement_user?.position || 'บุคลากร' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="req.remarks" class="bg-indigo-50/30 rounded-3xl p-5 border border-dashed border-indigo-200">
                                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-1">REASON / REMARKS</p>
                                        <p class="text-xs text-slate-600 font-medium italic truncate">"{{ req.remarks }}"</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Column -->
                            <div class="flex-shrink-0 lg:w-48 flex flex-col justify-center items-center gap-4 lg:pl-10 lg:border-l border-slate-100">
                                <button @click="openApprove(req)"
                                    class="w-full group flex items-center justify-center gap-3 px-6 py-5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-2xl shadow-xl shadow-purple-500/20 hover:shadow-purple-500/40 transition-all hover:-translate-y-1 font-black uppercase tracking-widest text-xs">
                                    <i data-lucide="stamp" class="w-5 h-5 group-hover:scale-125 transition-transform"></i>
                                    อนุมัติ
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
                <div v-if="showModal" class="fixed inset-0 z-[100] overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal"></div>
                        <div class="bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all relative w-full max-w-xl">
                            <form @submit.prevent="submit">
                                <div class="bg-white p-8 md:p-12">
                                    <div class="flex items-start justify-between mb-8">
                                        <div class="w-16 h-16 rounded-[1.5rem] bg-purple-50 text-purple-500 flex items-center justify-center shadow-inner">
                                            <i data-lucide="stamp" class="w-8 h-8"></i>
                                        </div>
                                        <button type="button" @click="closeModal" class="w-10 h-10 rounded-full hover:bg-slate-50 flex items-center justify-center text-slate-400 transition-colors">
                                            <i data-lucide="x" class="w-6 h-6"></i>
                                        </button>
                                    </div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mb-2">ยืนยันการอนุมัติ (รอง ผอ.)</h3>
                                    <p class="text-slate-500 font-medium mb-8">พิจารณาอนุมัติคำขอเปลี่ยนยามของ <span class="font-black text-slate-900">{{ activeReq?.user?.rank }}{{ activeReq?.user?.name }}</span></p>
                                    <div class="space-y-6">
                                        <div class="space-y-3">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">OBSERVATIONS / COMMENTS</label>
                                            <textarea v-model="form.comment" rows="2"
                                                class="block w-full rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all text-sm font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-medium resize-none"
                                                placeholder="ระบุความเห็นชอบเพิ่มเติม..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-slate-50 px-8 py-6 md:px-12 md:py-8 flex flex-col sm:flex-row-reverse gap-4">
                                    <button type="submit" :disabled="form.processing"
                                        class="flex-1 inline-flex justify-center items-center px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-purple-500/20 transition-all hover:-translate-y-1 disabled:opacity-60">
                                        <i data-lucide="stamp" class="w-4 h-4 mr-2"></i>
                                        {{ form.processing ? 'กำลังดำเนินการ...' : 'ลงนามอนุมัติ (รอง ผอ.)' }}
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

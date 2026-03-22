<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { confirmCancel } from '@/utils/swal';
import { thaiMonth, thaiDay, thaiYear, thaiDateTime } from '@/utils/date';

const props = defineProps({ guardChange: Object });

const dutyPositions = {
    senior_duty_officer: 'นายทหารเวรอาวุโส',
    duty_officer: 'นายทหารเวร',
    assistant_duty_officer: 'ผู้ช่วยนายทหารเวร',
};

function statusConfig(status) {
    const map = {
        approved: { bg: 'bg-emerald-50', text: 'text-emerald-700', icon: 'check-circle-2', label: 'อนุมัติเรียบร้อย' },
        rejected: { bg: 'bg-rose-50', text: 'text-rose-700', icon: 'x-circle', label: 'ถูกปฏิเสธ' },
        cancelled: { bg: 'bg-slate-100', text: 'text-slate-600', icon: 'ban', label: 'ยกเลิกแล้ว' },
    };
    return map[status] || { bg: 'bg-amber-50', text: 'text-amber-700', icon: 'clock', label: 'รอการดำเนินการ' };
}

const formatMonth    = (d) => thaiMonth(d).toUpperCase();
const formatDay      = thaiDay;
const formatYear     = thaiYear;
const formatDateTime = thaiDateTime;

async function cancelRequest() {
    const result = await confirmCancel({ title: 'ยืนยันการยกเลิก?', text: 'ยืนยันการยกเลิกคำขอเปลี่ยนเวรนี้?', confirmText: 'ยกเลิกคำขอ' });
    if (result.isConfirmed) {
        router.put(`/guard-change/${props.guardChange.id}/cancel`);
    }
}

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="รายละเอียดคำขอเปลี่ยนเวร">
        <div class="min-h-screen premium-bg-light -m-4 md:-m-8 pb-20">
            <!-- Sticky Header -->
            <div class="bg-white/50 backdrop-blur-sm border-b border-white/50 sticky top-0 z-30 mb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <nav class="flex items-center gap-2 text-slate-400 mb-4 text-sm font-medium">
                        <Link href="/guard-change" class="hover:text-indigo-600 transition-colors flex items-center gap-1">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i> กลับไปหน้าประวัติ
                        </Link>
                    </nav>
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                                </span>
                                <span class="text-indigo-600 font-bold tracking-wide uppercase text-xs">คำขอเปลี่ยนเวร</span>
                            </div>
                            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">รายละเอียดการเปลี่ยนเวร</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="status-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold"
                                :class="[statusConfig(guardChange.status).bg, statusConfig(guardChange.status).text]">
                                <i :data-lucide="statusConfig(guardChange.status).icon" class="w-4 h-4"></i>
                                {{ statusConfig(guardChange.status).label }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Core Information -->
                        <div class="glass-card p-8">
                            <div class="flex flex-col md:flex-row items-center gap-8 mb-8 pb-8 border-b border-slate-100">
                                <!-- Date Box -->
                                <div class="flex flex-col items-center justify-center w-32 h-32 bg-indigo-50 rounded-2xl border-2 border-indigo-100 shadow-sm">
                                    <span class="text-sm text-indigo-600 font-bold uppercase tracking-wider">{{ formatMonth(guardChange.duty_date) }}</span>
                                    <span class="text-5xl font-extrabold text-slate-800 leading-none my-1">{{ formatDay(guardChange.duty_date) }}</span>
                                    <span class="text-xs text-slate-400 font-bold">{{ formatYear(guardChange.duty_date) }}</span>
                                </div>
                                <div class="flex-1 text-center md:text-left">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ตำแหน่งเวรรับผิดชอบ</p>
                                    <h3 class="text-2xl font-extrabold text-slate-800">{{ dutyPositions[guardChange.duty_position] || guardChange.duty_position }}</h3>
                                    <p class="text-sm font-medium text-slate-500 mt-2 flex items-center justify-center md:justify-start gap-2">
                                        <span>เลขที่อ้างอิง:</span>
                                        <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600 text-xs">#{{ String(guardChange.id).padStart(5, '0') }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Requester -->
                                <div class="bg-slate-50/50 rounded-2xl p-4 border border-slate-100">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">ผู้ขอเปลี่ยนเวร</p>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white text-indigo-600 shadow-sm flex items-center justify-center border border-indigo-50">
                                            <i data-lucide="user-minus" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-slate-800">{{ guardChange.user?.rank }}{{ guardChange.user?.name }}</p>
                                            <p class="text-sm text-slate-500">{{ guardChange.user?.department }}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Replacement -->
                                <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100">
                                    <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-4">ผู้รับหน้าที่แทน</p>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-white text-emerald-600 shadow-sm flex items-center justify-center border border-emerald-50">
                                            <i data-lucide="user-plus" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-slate-800">{{ guardChange.replacement_user?.rank }}{{ guardChange.replacement_user?.name }}</p>
                                            <p class="text-sm text-slate-500">{{ guardChange.replacement_user?.department }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="guardChange.remarks" class="mt-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="quote" class="w-5 h-5 text-indigo-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">เหตุผลความจำเป็น / หมายเหตุ</p>
                                        <p class="text-base text-slate-700 font-medium italic">"{{ guardChange.remarks }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Timeline -->
                        <div class="glass-card p-8">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                    <i data-lucide="route" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800">สถานะและขั้นตอนการอนุมัติ</h3>
                            </div>

                            <div class="space-y-8 pl-2">
                                <!-- Step 1: Replacement User -->
                                <div class="relative flex gap-6">
                                    <div class="timeline-line"></div>
                                    <div class="timeline-dot" :class="guardChange.is_replacement_accepted ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-slate-100 text-slate-400 border-slate-50'">
                                        <i v-if="guardChange.is_replacement_accepted" data-lucide="check" class="w-5 h-5"></i>
                                        <span v-else class="text-xs font-bold">1</span>
                                    </div>
                                    <div class="flex-1 py-1">
                                        <h4 class="font-bold text-slate-800 text-sm">ผู้รับหน้าที่แทนยินยอม</h4>
                                        <p class="text-sm text-slate-500 mt-1">
                                            {{ guardChange.replacement_user?.rank }}{{ guardChange.replacement_user?.name }}
                                            <template v-if="guardChange.is_replacement_accepted">
                                                <span class="text-emerald-600 font-medium"> ยืนยันแล้ว</span>
                                                <span v-if="guardChange.replacement_accepted_at" class="text-slate-400"> เมื่อ {{ formatDateTime(guardChange.replacement_accepted_at) }}</span>
                                            </template>
                                            <span v-else class="text-amber-500 font-medium"> รอการตอบรับ</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 2: Deputy Director -->
                                <div class="relative flex gap-6">
                                    <div class="timeline-line"></div>
                                    <div class="timeline-dot" :class="guardChange.director_approved_at ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-slate-100 text-slate-400 border-slate-50'">
                                        <i v-if="guardChange.director_approved_at" data-lucide="check" class="w-5 h-5"></i>
                                        <span v-else class="text-xs font-bold">2</span>
                                    </div>
                                    <div class="flex-1 py-1">
                                        <h4 class="font-bold text-slate-800 text-sm">การพิจารณาตรวจสอบ (รอง ผอ.)</h4>
                                        <p class="text-sm text-slate-500 mt-1">
                                            <template v-if="guardChange.director_approved_at">
                                                <span class="text-emerald-600 font-medium">อนุมัติแล้ว</span>
                                                <span class="text-slate-400"> เมื่อ {{ formatDateTime(guardChange.director_approved_at) }}</span>
                                            </template>
                                            <span v-else class="text-slate-400">รอการตรวจสอบ</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 3: Director -->
                                <div class="relative flex gap-6">
                                    <div class="timeline-dot" :class="guardChange.status === 'fully_approved' ? 'bg-emerald-500 text-white border-emerald-100' : 'bg-slate-100 text-slate-400 border-slate-50'">
                                        <i v-if="guardChange.status === 'fully_approved'" data-lucide="check" class="w-5 h-5"></i>
                                        <span v-else class="text-xs font-bold">3</span>
                                    </div>
                                    <div class="flex-1 py-1">
                                        <h4 class="font-bold text-slate-800 text-sm">การอนุมัติขั้นสุดท้าย (ผอ.)</h4>
                                        <p class="text-sm text-slate-500 mt-1">
                                            <template v-if="guardChange.status === 'fully_approved'">
                                                <span class="text-emerald-600 font-medium">อนุมัติแล้ว</span>
                                                <span v-if="guardChange.updated_at" class="text-slate-400"> เมื่อ {{ formatDateTime(guardChange.updated_at) }}</span>
                                            </template>
                                            <span v-else-if="guardChange.status === 'rejected'" class="text-rose-500 font-medium">ปฏิเสธคำขอ</span>
                                            <span v-else class="text-slate-400">รอการอนุมัติ</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-6">
                        <!-- User Card -->
                        <div class="glass-card p-6 border-t-4 border-t-indigo-500">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-24 h-24 rounded-2xl bg-slate-100 mb-4 overflow-hidden border-4 border-white shadow-lg">
                                    <img v-if="guardChange.user?.avatar" :src="`/storage/${guardChange.user.avatar}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-300 text-3xl font-bold">
                                        {{ guardChange.user?.name?.charAt(0) }}
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">{{ guardChange.user?.rank }}{{ guardChange.user?.name }}</h3>
                                <span class="text-sm font-medium text-slate-500">{{ guardChange.user?.department }}</span>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-100 space-y-3">
                                <a :href="`/guard-change/${guardChange.id}/pdf`" target="_blank"
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all shadow-lg shadow-indigo-200 font-bold text-sm">
                                    <i data-lucide="file-down" class="w-4 h-4"></i> ดาวน์โหลดเอกสาร (PDF)
                                </a>
                                <button v-if="guardChange.status === 'pending'" type="button" @click="cancelRequest"
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-white border-2 border-slate-100 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 text-slate-600 rounded-xl transition-all font-bold text-sm">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i> ยกเลิกคำขอ
                                </button>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5"></i>
                                <div>
                                    <h4 class="font-bold text-blue-800 text-sm mb-1">ข้อควรรู้</h4>
                                    <p class="text-xs leading-relaxed text-blue-700/80">
                                        รายการเปลี่ยนเวรนี้จะสมบูรณ์เมื่อผู้บังคับบัญชาตามลำดับชั้นอนุมัติครบถ้วน ท่านสามารถติดตามสถานะได้จากหน้านี้
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.premium-bg-light {
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
}
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border-radius: 1.5rem;
}
.timeline-line {
    position: absolute;
    left: 1.5rem;
    top: 2.5rem;
    bottom: -1rem;
    width: 2px;
    background-color: #e2e8f0;
}
.timeline-dot {
    position: relative;
    z-index: 10;
    width: 3rem;
    height: 3rem;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 4px solid white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}
</style>

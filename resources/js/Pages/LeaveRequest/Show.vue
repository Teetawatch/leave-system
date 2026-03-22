<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { thaiDate, thaiFullDate, thaiMonth, thaiMonthLong, thaiDay, thaiYear, thaiDateTime } from '@/utils/date';

const props = defineProps({ leaveRequest: Object });

function statusConfig(status) {
    const map = {
        approved: { bg: 'bg-emerald-50', text: 'text-emerald-700', icon: 'check-circle-2', label: 'อนุมัติแล้ว' },
        rejected: { bg: 'bg-rose-50', text: 'text-rose-700', icon: 'x-circle', label: 'ถูกปฏิเสธ' },
        cancelled: { bg: 'bg-slate-100', text: 'text-slate-600', icon: 'ban', label: 'ยกเลิกแล้ว' },
    };
    return map[status] || { bg: 'bg-amber-50', text: 'text-amber-700', icon: 'clock', label: 'รอดำเนินการ' };
}

function approvalStepLabel(step) {
    const map = { 1: 'หัวหน้างาน', 2: 'ผู้จัดการ', 3: 'รอง ผอ.', 4: 'ผอ.' };
    return map[step] || `ขั้นที่ ${step}`;
}

const formatDate     = thaiDate;
const formatMonth    = (d) => thaiMonth(d).toUpperCase();
const formatDay      = thaiDay;
const formatYear     = thaiYear;

function cancelRequest() {
    if (confirm('คุณต้องการยกเลิกคำขอนี้หรือไม่?')) {
        router.put(`/leave-request/${props.leaveRequest.id}/cancel`);
    }
}

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="รายละเอียดการลา">
        <div class="min-h-screen premium-bg-light -m-4 md:-m-8 pb-20">
            <!-- Sticky Header -->
            <div class="bg-white/50 backdrop-blur-sm border-b border-white/50 sticky top-0 z-30 mb-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <nav class="flex items-center gap-2 text-slate-400 mb-4 text-sm font-medium">
                        <Link href="/leave-request" class="hover:text-emerald-600 transition-colors flex items-center gap-1">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i> กลับไปประวัติการลา
                        </Link>
                    </nav>
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                                </span>
                                <span class="text-emerald-600 font-bold tracking-wide uppercase text-xs">คำขอลา</span>
                            </div>
                            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ leaveRequest.leave_type?.name || 'รายละเอียดการลา' }}</h1>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="status-badge inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold"
                                :class="[statusConfig(leaveRequest.status).bg, statusConfig(leaveRequest.status).text]">
                                <i :data-lucide="statusConfig(leaveRequest.status).icon" class="w-4 h-4"></i>
                                {{ statusConfig(leaveRequest.status).label }}
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
                                <!-- Date Range -->
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center justify-center w-28 h-28 bg-emerald-50 rounded-2xl border-2 border-emerald-100 shadow-sm">
                                        <span class="text-xs text-emerald-600 font-bold uppercase tracking-wider">{{ formatMonth(leaveRequest.start_date) }}</span>
                                        <span class="text-4xl font-extrabold text-slate-800 leading-none my-1">{{ formatDay(leaveRequest.start_date) }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold">{{ formatYear(leaveRequest.start_date) }}</span>
                                    </div>
                                    <div class="flex flex-col items-center gap-1">
                                        <i data-lucide="arrow-right" class="w-5 h-5 text-slate-300"></i>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">ถึง</span>
                                    </div>
                                    <div class="flex flex-col items-center justify-center w-28 h-28 bg-rose-50 rounded-2xl border-2 border-rose-100 shadow-sm">
                                        <span class="text-xs text-rose-600 font-bold uppercase tracking-wider">{{ formatMonth(leaveRequest.end_date) }}</span>
                                        <span class="text-4xl font-extrabold text-slate-800 leading-none my-1">{{ formatDay(leaveRequest.end_date) }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold">{{ formatYear(leaveRequest.end_date) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 text-center md:text-left">
                                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-3">
                                        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-slate-900 text-white">
                                            <i data-lucide="calendar" class="w-3 h-3 text-emerald-400"></i>
                                            {{ leaveRequest.total_days }} วัน
                                        </span>
                                        <span v-if="leaveRequest.temporary_leave_period" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ leaveRequest.temporary_leave_period === 'morning' ? 'ครึ่งเช้า' : 'ครึ่งบ่าย' }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500 flex items-center justify-center md:justify-start gap-2">
                                        <span>เลขที่อ้างอิง:</span>
                                        <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600 text-xs">#{{ String(leaveRequest.id).padStart(5, '0') }}</span>
                                    </p>
                                    <p v-if="leaveRequest.created_at_human" class="text-xs text-slate-400 mt-1">ส่งเมื่อ {{ leaveRequest.created_at_human }}</p>
                                </div>
                            </div>

                            <!-- Reason -->
                            <div v-if="leaveRequest.reason" class="p-6 bg-slate-50 rounded-2xl border border-slate-100 mb-6">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="quote" class="w-5 h-5 text-emerald-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">เหตุผลการลา</p>
                                        <p class="text-base text-slate-700 font-medium italic leading-relaxed">"{{ leaveRequest.reason }}"</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Address -->
                            <div v-if="leaveRequest.contact_address" class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 mb-6">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="map-pin" class="w-5 h-5 text-indigo-400 mt-1"></i>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">ที่อยู่ระหว่างลา</p>
                                        <p class="text-sm text-slate-700 font-medium">{{ leaveRequest.contact_address }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attachment -->
                            <div v-if="leaveRequest.attachment_path" class="flex items-center gap-4 p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                <div class="w-12 h-12 rounded-xl bg-white text-indigo-600 shadow-sm flex items-center justify-center border border-indigo-50">
                                    <i data-lucide="paperclip" class="w-6 h-6"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">เอกสารแนบ</p>
                                    <a :href="`/storage/${leaveRequest.attachment_path}`" target="_blank" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                                        คลิกเพื่อดูเอกสาร <i data-lucide="external-link" class="w-3 h-3 inline"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Timeline -->
                        <div v-if="leaveRequest.approvals && leaveRequest.approvals.length > 0" class="glass-card p-8">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                    <i data-lucide="route" class="w-5 h-5"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800">สถานะและขั้นตอนการอนุมัติ</h3>
                            </div>

                            <div class="space-y-8 pl-2">
                                <div v-for="(approval, index) in leaveRequest.approvals" :key="approval.step" class="relative flex gap-6">
                                    <div v-if="index < leaveRequest.approvals.length - 1" class="timeline-line"></div>
                                    <div class="timeline-dot" :class="approval.status === 'approved' ? 'bg-emerald-500 text-white' : approval.status === 'rejected' ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-400'">
                                        <i v-if="approval.status === 'approved'" data-lucide="check" class="w-5 h-5"></i>
                                        <i v-else-if="approval.status === 'rejected'" data-lucide="x" class="w-5 h-5"></i>
                                        <span v-else class="text-xs font-bold">{{ approval.step }}</span>
                                    </div>
                                    <div class="flex-1 py-1">
                                        <h4 class="font-bold text-slate-800 text-sm">{{ approvalStepLabel(approval.step) }}</h4>
                                        <p v-if="approval.approver" class="text-sm text-slate-500 mt-1">
                                            {{ approval.approver.rank }} {{ approval.approver.name }}
                                            <span v-if="approval.status === 'approved'" class="text-emerald-600 font-medium"> — อนุมัติแล้ว</span>
                                            <span v-else-if="approval.status === 'rejected'" class="text-rose-600 font-medium"> — ปฏิเสธ</span>
                                        </p>
                                        <p v-if="approval.comment" class="text-sm text-slate-400 mt-1 italic">"{{ approval.comment }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-6">
                        <!-- User Card -->
                        <div class="glass-card p-6 border-t-4 border-t-emerald-500">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-24 h-24 rounded-2xl bg-slate-100 mb-4 overflow-hidden border-4 border-white shadow-lg">
                                    <img v-if="leaveRequest.user?.avatar" :src="`/storage/${leaveRequest.user.avatar}`" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full flex items-center justify-center bg-emerald-50 text-emerald-300 text-3xl font-bold">
                                        {{ leaveRequest.user?.name?.charAt(0) }}
                                    </div>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800">{{ leaveRequest.user?.rank }}{{ leaveRequest.user?.name }}</h3>
                                <span class="text-sm font-medium text-slate-500">{{ leaveRequest.user?.department }}</span>
                            </div>

                            <div class="mt-6 pt-6 border-t border-slate-100 space-y-3">
                                <a :href="`/leave-request/${leaveRequest.id}/pdf`" target="_blank"
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-all shadow-lg shadow-emerald-200 font-bold text-sm">
                                    <i data-lucide="file-down" class="w-4 h-4"></i> ดาวน์โหลดเอกสาร (PDF)
                                </a>
                                <button v-if="leaveRequest.status && leaveRequest.status.startsWith('pending')" type="button" @click="cancelRequest"
                                    class="flex items-center justify-center gap-2 w-full py-3 bg-white border-2 border-slate-100 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-600 text-slate-600 rounded-xl transition-all font-bold text-sm">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i> ยกเลิกคำขอ
                                </button>
                            </div>
                        </div>

                        <!-- Leave Type Info -->
                        <div class="glass-card p-6">
                            <h4 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4 text-emerald-500"></i> ข้อมูลประเภทการลา
                            </h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">ประเภท</span>
                                    <span class="text-sm font-bold text-slate-800">{{ leaveRequest.leave_type?.name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">จำนวนวัน</span>
                                    <span class="text-sm font-bold text-slate-800">{{ leaveRequest.total_days }} วัน</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">วันเริ่มต้น</span>
                                    <span class="text-sm font-bold text-slate-800">{{ leaveRequest.start_date_thai || formatDate(leaveRequest.start_date) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">วันสิ้นสุด</span>
                                    <span class="text-sm font-bold text-slate-800">{{ leaveRequest.end_date_thai || formatDate(leaveRequest.end_date) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100">
                            <div class="flex items-start gap-3">
                                <i data-lucide="info" class="w-5 h-5 text-blue-500 mt-0.5"></i>
                                <div>
                                    <h4 class="font-bold text-blue-800 text-sm mb-1">ข้อควรรู้</h4>
                                    <p class="text-xs leading-relaxed text-blue-700/80">
                                        คำขอลาจะสมบูรณ์เมื่อผู้บังคับบัญชาตามลำดับชั้นอนุมัติครบถ้วน ท่านสามารถติดตามสถานะได้จากหน้านี้
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
    background: linear-gradient(135deg, #f8fafc 0%, #ecfdf5 100%);
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

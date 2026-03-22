<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted } from 'vue';

const props = defineProps({
    totalEmployees: Number, totalLeaveRequests: Number, approvedLeaves: Number,
    pendingLeaves: Number, rejectedLeaves: Number, leaveByDepartment: Array,
    monthlyTrend: Array, topLeaveTakers: Array, leaveTypeDistribution: Array,
    todayOnLeave: Array, recentRequests: Array, departmentStats: Array,
    departments: Array, thisMonthLeaves: Number, lastMonthLeaves: Number,
    leaveChangePercent: Number, currentYear: Number,
});

const leaveTypeColors = { vacation: 'bg-emerald-500', sick: 'bg-rose-500', personal: 'bg-amber-500', temporary: 'bg-purple-500', default: 'bg-slate-400' };
const statCards = [
    { label: 'ข้าราชการ', key: 'totalEmployees', color: 'slate', icon: 'users' },
    { label: 'ลาทั้งหมด', key: 'totalLeaveRequests', color: 'indigo', icon: 'file-text' },
    { label: 'อนุมัติ', key: 'approvedLeaves', color: 'emerald', icon: 'check-circle' },
    { label: 'รอดำเนินการ', key: 'pendingLeaves', color: 'amber', icon: 'clock' },
    { label: 'ปฏิเสธ', key: 'rejectedLeaves', color: 'rose', icon: 'x-circle' },
];

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="แดชบอร์ดผู้บริหาร">
        <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-indigo-100/30 rounded-full blur-[120px] -mr-72 -mt-72"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-violet-100/30 rounded-full blur-[100px] -ml-36 -mb-36"></div>

            <!-- Header -->
            <div class="relative pt-16 pb-28">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        ผู้บริหาร
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                        แดชบอร์ด <span class="text-indigo-600">ผู้บริหาร</span>
                    </h1>
                    <p class="text-slate-500 font-medium text-lg">ภาพรวมสถิติการลาประจำปี {{ (currentYear || 2024) + 543 }}</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div v-for="sc in statCards" :key="sc.key" class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center" :class="`bg-${sc.color}-50 text-${sc.color}-600`">
                                <i :data-lucide="sc.icon" class="w-4 h-4"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ sc.label }}</p>
                        </div>
                        <p class="text-3xl font-black" :class="`text-${sc.color}-600`">{{ props[sc.key] || 0 }}</p>
                    </div>
                </div>

                <!-- Month Comparison -->
                <div class="glass-panel rounded-2xl p-6">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100"><i data-lucide="trending-up" class="w-6 h-6"></i></div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-500">เดือนนี้: <span class="text-slate-800 font-black">{{ thisMonthLeaves }}</span> ครั้ง</p>
                            <p class="text-sm font-bold text-slate-500">เดือนที่แล้ว: <span class="text-slate-800 font-black">{{ lastMonthLeaves }}</span> ครั้ง</p>
                        </div>
                        <span class="px-5 py-2.5 rounded-2xl text-sm font-black" :class="leaveChangePercent > 0 ? 'bg-rose-50 text-rose-600 border border-rose-100' : leaveChangePercent < 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-500 border border-slate-100'">
                            {{ leaveChangePercent > 0 ? '+' : '' }}{{ leaveChangePercent }}%
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Leave Type Distribution -->
                    <div class="glass-panel rounded-[2rem] overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center"><i data-lucide="pie-chart" class="w-5 h-5"></i></div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight">สัดส่วนตามประเภทการลา</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div v-for="lt in leaveTypeDistribution" :key="lt.id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                                <div class="w-4 h-4 rounded-full" :class="leaveTypeColors[lt.slug] || leaveTypeColors.default"></div>
                                <span class="font-bold text-slate-700 flex-1">{{ lt.name }}</span>
                                <span class="text-xs font-black text-slate-400">{{ lt.request_count }} ครั้ง</span>
                                <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">{{ lt.total_days }} วัน</span>
                            </div>
                        </div>
                    </div>

                    <!-- Today On Leave -->
                    <div class="glass-panel rounded-[2rem] overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center"><i data-lucide="calendar-off" class="w-5 h-5"></i></div>
                            <h3 class="text-lg font-black text-slate-900 tracking-tight">ลาวันนี้</h3>
                            <span v-if="todayOnLeave?.length" class="ml-auto px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-black border border-amber-100">{{ todayOnLeave.length }}</span>
                        </div>
                        <div class="p-6">
                            <template v-if="todayOnLeave && todayOnLeave.length > 0">
                                <div v-for="leave in todayOnLeave" :key="leave.id" class="flex items-center gap-4 py-3 border-b border-slate-50 last:border-0">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-sm shrink-0 border border-indigo-100">{{ leave.user?.name?.charAt(0) }}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-slate-800 text-sm truncate">{{ leave.user?.rank }} {{ leave.user?.name }}</p>
                                        <p class="text-xs text-slate-400">{{ leave.leave_type?.name }}</p>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="py-10 text-center">
                                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3"><i data-lucide="smile" class="w-7 h-7 text-slate-200"></i></div>
                                <p class="text-slate-400 font-bold">ไม่มีผู้ลาวันนี้</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Leave Takers -->
                <div v-if="topLeaveTakers && topLeaveTakers.length > 0" class="glass-panel rounded-[2rem] overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center"><i data-lucide="trophy" class="w-5 h-5"></i></div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">ลามากที่สุด (Top 10)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">#</th>
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ</th>
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">แผนก</th>
                                <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">ครั้ง</th>
                                <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">วัน</th>
                            </tr></thead>
                            <tbody>
                                <tr v-for="(t, i) in topLeaveTakers" :key="t.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-black text-slate-300">{{ i + 1 }}</td>
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ t.rank }} {{ t.name }}</td>
                                    <td class="px-6 py-4 text-slate-500 text-xs">{{ t.department }}</td>
                                    <td class="px-6 py-4 text-right text-slate-600">{{ t.leave_count }}</td>
                                    <td class="px-6 py-4 text-right font-black text-indigo-600">{{ t.total_days }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Department Stats -->
                <div v-if="leaveByDepartment && leaveByDepartment.length > 0" class="glass-panel rounded-[2rem] overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center"><i data-lucide="building-2" class="w-5 h-5"></i></div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">สถิติตามแผนก</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">แผนก</th>
                                <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">ข้าราชการ</th>
                                <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">วันลารวม</th>
                                <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">เฉลี่ย/คน</th>
                            </tr></thead>
                            <tbody>
                                <tr v-for="d in leaveByDepartment" :key="d.department" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ d.department }}</td>
                                    <td class="px-6 py-4 text-right text-slate-600">{{ d.total_employees }}</td>
                                    <td class="px-6 py-4 text-right font-black text-indigo-600">{{ d.total_days }}</td>
                                    <td class="px-6 py-4 text-right text-slate-500">{{ d.avg_days }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.premium-bg {
    min-height: 100vh;
    background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
}
</style>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
    totalEmployees: Number, totalLeaveRequests: Number, approvedLeaves: Number,
    pendingLeaves: Number, rejectedLeaves: Number, leaveByDepartment: Array,
    monthlyTrend: Array, topLeaveTakers: Array, leaveTypeDistribution: Array,
    todayOnLeave: Array, recentRequests: Array, departmentStats: Array,
    departments: Array, thisMonthLeaves: Number, lastMonthLeaves: Number,
    leaveChangePercent: Number, currentYear: Number,
});

const isLoaded = ref(false);

const leaveTypeColors = { 
    vacation: 'bg-emerald-400 shadow-[0_0_15px_rgba(52,211,153,0.5)]', 
    sick: 'bg-rose-400 shadow-[0_0_15px_rgba(251,113,133,0.5)]', 
    personal: 'bg-blue-400 shadow-[0_0_15px_rgba(96,165,250,0.5)]', 
    temporary: 'bg-purple-400 shadow-[0_0_15px_rgba(192,132,252,0.5)]', 
    default: 'bg-slate-400 shadow-[0_0_15px_rgba(148,163,184,0.5)]' 
};

const leaveTypeGradients = { 
    vacation: 'from-emerald-400 to-emerald-600', 
    sick: 'from-rose-400 to-rose-600', 
    personal: 'from-blue-400 to-blue-600', 
    temporary: 'from-purple-400 to-purple-600', 
    default: 'from-slate-400 to-slate-600' 
};

const statCards = [
    { label: 'ข้าราชการ', key: 'totalEmployees', color: 'blue', icon: 'users', gradient: 'from-blue-500 to-cyan-400' },
    { label: 'ลาทั้งหมด', key: 'totalLeaveRequests', color: 'indigo', icon: 'file-text', gradient: 'from-indigo-500 to-purple-400' },
    { label: 'อนุมัติ', key: 'approvedLeaves', color: 'emerald', icon: 'check-circle', gradient: 'from-emerald-500 to-teal-400' },
    { label: 'รอดำเนินการ', key: 'pendingLeaves', color: 'amber', icon: 'clock', gradient: 'from-amber-400 to-orange-400' },
    { label: 'ปฏิเสธ', key: 'rejectedLeaves', color: 'rose', icon: 'x-circle', gradient: 'from-rose-500 to-pink-500' },
];

onMounted(() => { 
    setTimeout(() => { 
        if (window.lucide) window.lucide.createIcons(); 
        isLoaded.value = true;
    }, 100); 
});
</script>

<template>
    <AppLayout title="แดชบอร์ดผู้บริหาร">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-slate-50 font-sans selection:bg-blue-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-blue-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[20%] left-[-10%] w-[500px] h-[500px] bg-indigo-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[20%] w-[700px] h-[700px] bg-cyan-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-12" :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-blue-100/50">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                        </span>
                        <span class="text-blue-700 text-[11px] font-black uppercase tracking-[0.2em]">Executive Overview</span>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none mb-3">
                                แดชบอร์ด <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500">ผู้บริหาร</span>
                            </h1>
                            <p class="text-slate-500 font-medium text-lg flex items-center gap-2">
                                <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i>
                                ภาพรวมปีงบประมาณ {{ (currentYear || 2024) + 543 }}
                            </p>
                        </div>
                        <div class="flex gap-3">
                            <button class="glass-btn px-4 py-2 rounded-xl text-sm font-bold text-slate-700 hover:text-blue-600 flex items-center gap-2 transition-all">
                                <i data-lucide="download" class="w-4 h-4"></i> รายงาน
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6 mb-8">
                    <div v-for="(sc, index) in statCards" :key="sc.key" 
                         class="glass-card rounded-[1.5rem] p-6 group hover:-translate-y-2 transition-all duration-500 cursor-pointer overflow-hidden relative"
                         :style="`transition-delay: ${index * 75}ms;`"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }">
                        
                        <!-- Glow effect on hover -->
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-10 transition-opacity duration-500" :class="sc.gradient"></div>
                        
                        <div class="relative z-10">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-white border border-white shadow-sm group-hover:shadow-md transition-all duration-300">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br text-white" :class="sc.gradient">
                                        <i :data-lucide="sc.icon" class="w-5 h-5"></i>
                                    </div>
                                </div>
                                <!-- Micro trend indicator (decorative) -->
                                <div class="w-8 h-8 rounded-full bg-slate-50/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all duration-300">
                                    <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-400"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-3xl font-black text-slate-800 mb-1 tracking-tight">{{ props[sc.key] || 0 }}</h3>
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">{{ sc.label }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 mb-8">
                    <!-- Month Comparison (Takes 1 col on large) -->
                    <div class="glass-card rounded-[2rem] p-6 lg:p-8 flex flex-col justify-center relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 300ms;">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-50 rounded-full blur-2xl opacity-50 group-hover:bg-cyan-100 transition-colors duration-700"></div>
                        
                        <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> เปรียบเทียบรายเดือน
                        </h3>
                        
                        <div class="flex items-end gap-4 mb-8 relative z-10">
                            <div>
                                <p class="text-sm font-bold text-slate-400 mb-1">เดือนนี้</p>
                                <p class="text-5xl font-black text-slate-800 tracking-tighter">{{ thisMonthLeaves }}</p>
                            </div>
                            <div class="pb-1" v-if="leaveChangePercent !== undefined">
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-sm font-black shadow-sm"
                                      :class="leaveChangePercent > 0 ? 'bg-rose-50 text-rose-600 border border-rose-100/50' : leaveChangePercent < 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100/50' : 'bg-slate-50 text-slate-500 border border-slate-100/50'">
                                    <i :data-lucide="leaveChangePercent > 0 ? 'trending-up' : leaveChangePercent < 0 ? 'trending-down' : 'minus'" class="w-4 h-4"></i>
                                    {{ leaveChangePercent > 0 ? '+' : '' }}{{ leaveChangePercent }}%
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between py-4 border-t border-slate-100/50 relative z-10">
                            <span class="text-sm font-bold text-slate-500">เดือนที่แล้ว</span>
                            <span class="text-lg font-black text-slate-700">{{ lastMonthLeaves }} ครั้ง</span>
                        </div>
                    </div>

                    <!-- Leave Type Distribution (Takes 2 cols on large) -->
                    <div class="glass-card rounded-[2rem] lg:col-span-2 overflow-hidden flex flex-col"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 400ms;">
                        <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-white/40 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20">
                                    <i data-lucide="pie-chart" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 tracking-tight">สัดส่วนประเภทการลา</h3>
                                    <p class="text-xs font-bold text-slate-400">ภาพรวมการใช้วันลาแต่ละประเภท</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 lg:p-8 flex-1 flex flex-col justify-center">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                                <div v-for="(lt, idx) in leaveTypeDistribution" :key="lt.id" 
                                     class="group relative p-4 rounded-2xl border border-white/60 bg-white/40 hover:bg-white/60 transition-all duration-300 cursor-pointer overflow-hidden">
                                     
                                    <!-- Decorative background bar -->
                                    <div class="absolute bottom-0 left-0 h-1 w-full bg-slate-100">
                                        <div class="h-full rounded-r-full transition-all duration-1000 ease-out" 
                                             :class="leaveTypeGradients[lt.slug] || leaveTypeGradients.default"
                                             :style="`width: ${isLoaded ? Math.min((lt.request_count / Math.max(1, totalLeaveRequests)) * 100 + 10, 100) : 0}%`"></div>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 relative z-10">
                                        <div class="w-3 h-3 rounded-full" :class="leaveTypeColors[lt.slug] || leaveTypeColors.default"></div>
                                        <div class="flex-1">
                                            <p class="font-black text-slate-800 text-sm md:text-base">{{ lt.name }}</p>
                                            <p class="text-xs font-bold text-slate-400">{{ lt.request_count }} รายการ</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xl font-black text-slate-800">{{ lt.total_days }}<span class="text-sm text-slate-400 ml-1">วัน</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
                    <!-- Today On Leave -->
                    <div class="glass-card rounded-[2rem] overflow-hidden lg:col-span-1"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 500ms;">
                        <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-white/40 flex items-center justify-between relative overflow-hidden">
                            <!-- Background gradient for header -->
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-50 to-transparent opacity-80 pointer-events-none"></div>
                            
                            <div class="flex items-center gap-3 relative z-10">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/20">
                                    <i data-lucide="calendar-off" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 tracking-tight">ลาวันนี้</h3>
                                </div>
                            </div>
                            <span v-if="todayOnLeave?.length" class="relative z-10 px-3 py-1 rounded-full bg-white text-amber-600 text-xs font-black shadow-sm border border-amber-100">
                                {{ todayOnLeave.length }} คน
                            </span>
                        </div>
                        <div class="p-2">
                            <template v-if="todayOnLeave && todayOnLeave.length > 0">
                                <div class="max-h-[350px] overflow-y-auto px-4 py-2 custom-scrollbar">
                                    <div v-for="(leave, index) in todayOnLeave" :key="leave.id" 
                                         class="group flex items-center gap-4 p-3 mb-2 rounded-2xl hover:bg-white/50 border border-transparent hover:border-white/80 transition-all duration-300"
                                         :style="`animation-delay: ${index * 100}ms;`">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-black text-lg shrink-0 border border-white shadow-inner group-hover:scale-110 transition-transform duration-300">
                                            {{ leave.user?.name?.charAt(0) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-slate-800 text-sm truncate">{{ leave.user?.rank }} {{ leave.user?.name }}</p>
                                            <p class="text-xs font-medium text-slate-400 mt-0.5 truncate flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="leaveTypeColors[leave.leave_type?.slug]?.split(' ')[0] || 'bg-slate-400'"></span>
                                                {{ leave.leave_type?.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="py-16 text-center px-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-slate-50 to-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-white shadow-sm">
                                    <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-400"></i>
                                </div>
                                <h4 class="text-slate-700 font-black text-lg mb-1">ทำงานครบทุกคน</h4>
                                <p class="text-slate-400 font-medium text-sm">ไม่มีบุคลากรลางานในวันนี้</p>
                            </div>
                        </div>
                    </div>

                    <!-- Top Leave Takers & Dept Stats -->
                    <div class="lg:col-span-2 space-y-6 md:space-y-8">
                         <!-- Top Leave Takers -->
                        <div v-if="topLeaveTakers && topLeaveTakers.length > 0" class="glass-card rounded-[2rem] overflow-hidden"
                             :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 600ms;">
                            <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-white/40 flex items-center gap-3 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-rose-50 to-transparent opacity-80 pointer-events-none"></div>
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-400 to-pink-500 text-white flex items-center justify-center shadow-lg shadow-rose-500/20 relative z-10">
                                    <i data-lucide="trophy" class="w-5 h-5"></i>
                                </div>
                                <div class="relative z-10">
                                    <h3 class="text-lg font-black text-slate-900 tracking-tight">อันดับการลาสูงสุด</h3>
                                    <p class="text-xs font-bold text-slate-400">บุคลากรที่ใช้วันลามากที่สุด</p>
                                </div>
                            </div>
                            <div class="overflow-x-auto pb-4">
                                <table class="w-full text-sm text-left">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest pl-8">อันดับ</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest">ชื่อ-สกุล</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest">แผนก</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">จำนวนครั้ง</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-right pr-8">รวมวัน</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/40">
                                        <tr v-for="(t, i) in topLeaveTakers.slice(0, 5)" :key="t.id" class="group hover:bg-white/40 transition-colors duration-200">
                                            <td class="px-6 py-3.5 pl-8">
                                                <div class="w-7 h-7 rounded-[9px] flex items-center justify-center font-black text-xs"
                                                     :class="i === 0 ? 'bg-amber-100 text-amber-600' : i === 1 ? 'bg-slate-100 text-slate-600' : i === 2 ? 'bg-orange-100 text-orange-600' : 'bg-transparent text-slate-400'">
                                                    {{ i + 1 }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-3.5 font-bold text-slate-800">{{ t.rank }}{{ t.name }}</td>
                                            <td class="px-6 py-3.5">
                                                <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200/50">
                                                    {{ t.department }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3.5 text-center font-bold text-slate-500">{{ t.leave_count }}</td>
                                            <td class="px-6 py-3.5 text-right font-black text-blue-600 pr-8">{{ t.total_days }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Department Stats -->
                        <div v-if="leaveByDepartment && leaveByDepartment.length > 0" class="glass-card rounded-[2rem] overflow-hidden"
                             :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 700ms;">
                            <div class="px-6 py-5 lg:px-8 lg:py-6 border-b border-white/40 flex items-center gap-3 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 to-transparent opacity-80 pointer-events-none"></div>
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/20 relative z-10">
                                    <i data-lucide="building-2" class="w-5 h-5"></i>
                                </div>
                                <div class="relative z-10">
                                    <h3 class="text-lg font-black text-slate-900 tracking-tight">สถิติตามหน่วยงาน</h3>
                                    <p class="text-xs font-bold text-slate-400">ข้อมูลการลาจำแนกตามแผนก</p>
                                </div>
                            </div>
                            <div class="overflow-x-auto pb-4">
                                <table class="w-full text-sm text-left">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest pl-8">แผนก</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">บุคลากร</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">รวมวันลา</th>
                                            <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-right pr-8">เฉลี่ย/คน</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/40">
                                        <tr v-for="d in leaveByDepartment.slice(0, 5)" :key="d.department" class="group hover:bg-white/40 transition-colors duration-200">
                                            <td class="px-6 py-3.5 font-bold text-slate-800 pl-8 flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                {{ d.department || 'ไม่ระบุ' }}
                                            </td>
                                            <td class="px-6 py-3.5 text-center font-bold text-slate-500">{{ d.total_employees }}</td>
                                            <td class="px-6 py-3.5 text-center font-black text-slate-700">{{ d.total_days }}</td>
                                            <td class="px-6 py-3.5 text-right font-black text-emerald-600 pr-8">{{ d.avg_days }} วัน</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Liquid Glass Aesthetic */
.glass-badge {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

.glass-btn {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.05), inset 0 2px 4px rgba(255, 255, 255, 0.8);
}
.glass-btn:hover {
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 6px 20px -3px rgba(0, 0, 0, 0.08), inset 0 2px 4px rgba(255, 255, 255, 1);
}

.glass-card {
    background: rgba(255, 255, 255, 0.55);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.8);
}
.glass-card:hover {
    background: rgba(255, 255, 255, 0.7);
    border-color: rgba(255, 255, 255, 0.9);
    box-shadow: 0 15px 50px -10px rgba(59, 130, 246, 0.08), inset 0 1px 0 rgba(255, 255, 255, 1);
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

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.02);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(203, 213, 225, 0.5);
    border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.8);
}
</style>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';

const props = defineProps({
    requests: Object, departments: Array,
    totalCount: Number, approvedCount: Number, pendingCount: Number,
    morningCount: Number, afternoonCount: Number,
});

const isLoaded = ref(false);
const expandedUsers = ref(new Set());
const deletingId = ref(null);

function avatarUrl(avatar) {
    if (!avatar) return null;
    if (avatar.startsWith('http')) return avatar;
    return '/storage/' + avatar;
}

// Group requests by user_id, each request counts as 0.5 day
const groupedRequests = computed(() => {
    const items = props.requests?.data || [];
    const map = new Map();

    items.forEach(r => {
        const uid = r.user?.id ?? r.user_id;
        if (!map.has(uid)) {
            map.set(uid, {
                user: r.user,
                records: [],
                totalDays: 0,
            });
        }
        const group = map.get(uid);
        group.records.push(r);
        group.totalDays += 0.5; // each temporary leave = 0.5 day
    });

    return Array.from(map.values());
});

function toggleUser(uid) {
    if (expandedUsers.value.has(uid)) {
        expandedUsers.value.delete(uid);
    } else {
        expandedUsers.value.add(uid);
    }
    expandedUsers.value = new Set(expandedUsers.value); // trigger reactivity
}

function isExpanded(uid) {
    return expandedUsers.value.has(uid);
}

function deleteRequest(id) {
    if (!confirm('ยืนยันการลบรายการลานี้?')) return;
    deletingId.value = id;
    router.delete(`/leave-request/${id}`, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            deletingId.value = null;
        },
        onError: (errors) => {
            deletingId.value = null;
            const msg = Object.values(errors).join('\n') || 'เกิดข้อผิดพลาดในการลบ';
            alert(msg);
        },
        onFinish: () => {
            deletingId.value = null;
        },
    });
}

onMounted(() => { 
    setTimeout(() => { 
        if (window.lucide) window.lucide.createIcons(); 
        isLoaded.value = true;
    }, 150); 
});
</script>

<template>
    <AppLayout title="รายงานลาชั่วกาล">
        <div class="premium-wrapper min-h-screen -m-4 md:-m-8 pb-32 relative overflow-hidden bg-slate-50 font-sans selection:bg-violet-200">
            <!-- Animated Liquid Background Shapes -->
            <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
                <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-violet-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob"></div>
                <div class="absolute top-[30%] left-[-10%] w-[500px] h-[500px] bg-fuchsia-400/20 rounded-full blur-[80px] mix-blend-multiply animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-10%] right-[10%] w-[700px] h-[700px] bg-cyan-300/20 rounded-full blur-[100px] mix-blend-multiply animate-blob animation-delay-4000"></div>
            </div>

            <div class="relative z-10 max-w-[95rem] mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <!-- Header -->
                <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6" 
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-4': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-badge mb-6 shadow-sm border border-violet-100/50">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-violet-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-violet-500"></span>
                            </span>
                            <span class="text-violet-700 text-[11px] font-black uppercase tracking-[0.2em]">Temporary Leave System</span>
                        </div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-[1.25rem] flex items-center justify-center shadow-lg shadow-violet-500/30 border border-white/20">
                                <i data-lucide="clock-4" class="w-7 h-7 text-white"></i>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none">
                                รายงาน<span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-fuchsia-500">ลาชั่วกาล</span>
                            </h1>
                        </div>
                        <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed ml-2 md:ml-[4.5rem]">
                            สถิติสรุปการขออนุญาตออกนอกบริเวณโรงเรียน (ลาครึ่งวัน = 0.5 วัน)
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-5 mb-10">
                    <!-- Total -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 100ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-slate-500 to-slate-600 opacity-0 group-hover:opacity-5 transition-opacity duration-500"></div>
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center border border-white shadow-sm group-hover:bg-slate-600 group-hover:text-white transition-all duration-300">
                                <i data-lucide="layers" class="w-5 h-5"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-1">ทั้งหมด</p>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-slate-800 tracking-tight">{{ totalCount }}</h3>
                            <p class="text-xs text-slate-400 font-bold mt-1">{{ (totalCount * 0.5).toFixed(1) }} วัน</p>
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 150ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-sm group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-1">อนุมัติ</p>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-emerald-600 tracking-tight group-hover:text-emerald-700 transition-colors">{{ approvedCount }}</h3>
                            <p class="text-xs text-emerald-400 font-bold mt-1">{{ (approvedCount * 0.5).toFixed(1) }} วัน</p>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 200ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-orange-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shadow-sm group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                                <i data-lucide="clock" class="w-5 h-5"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-1">รอดำเนินการ</p>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-amber-500 tracking-tight group-hover:text-amber-600 transition-colors">{{ pendingCount }}</h3>
                            <p class="text-xs text-amber-400 font-bold mt-1">{{ (pendingCount * 0.5).toFixed(1) }} วัน</p>
                        </div>
                    </div>

                    <!-- Morning -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 250ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-sm group-hover:bg-blue-500 group-hover:text-white transition-all duration-300">
                                <i data-lucide="sunrise" class="w-5 h-5"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-1">ครึ่งเช้า</p>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-blue-500 tracking-tight group-hover:text-blue-600 transition-colors">{{ morningCount }}</h3>
                            <p class="text-xs text-blue-400 font-bold mt-1">{{ (morningCount * 0.5).toFixed(1) }} วัน</p>
                        </div>
                    </div>

                    <!-- Afternoon -->
                    <div class="glass-card rounded-[2rem] p-6 stats-card relative overflow-hidden group col-span-2 md:col-span-1 xl:col-span-1"
                         :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 300ms;">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-fuchsia-500 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
                        <div class="flex items-center gap-3 mb-4 relative z-10">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100 shadow-sm group-hover:bg-purple-500 group-hover:text-white transition-all duration-300">
                                <i data-lucide="sunset" class="w-5 h-5"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest pt-1">ครึ่งบ่าย</p>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-purple-500 tracking-tight group-hover:text-purple-600 transition-colors">{{ afternoonCount }}</h3>
                            <p class="text-xs text-purple-400 font-bold mt-1">{{ (afternoonCount * 0.5).toFixed(1) }} วัน</p>
                        </div>
                    </div>
                </div>

                <!-- Main Data Table (Grouped by User) -->
                <div class="glass-card rounded-[2.5rem] overflow-hidden" 
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 400ms;">
                    <div class="px-6 py-5 lg:px-8 border-b border-white/40 flex items-center justify-between bg-white/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-lg">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight leading-none mb-1">รายการลาชั่วกาลรวมตามบุคคล</h3>
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">1 ครั้ง = 0.5 วัน · คลิกเพื่อดูรายละเอียด</p>
                            </div>
                        </div>
                        <div class="text-xs font-black text-slate-400 bg-white/60 px-3 py-1.5 rounded-xl border border-white">
                            {{ groupedRequests.length }} คน
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead>
                                <tr class="bg-white/40 border-b border-white/60">
                                    <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest w-8"></th>
                                    <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest">ชื่อ-สกุล</th>
                                    <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">จำนวนครั้ง</th>
                                    <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">รวมวันลา</th>
                                    <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">ครึ่งเช้า</th>
                                    <th class="px-6 py-4 font-black text-slate-400 text-[10px] uppercase tracking-widest text-center">ครึ่งบ่าย</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/40 border-t border-white/60 bg-white/10">
                                <template v-for="(group, index) in groupedRequests" :key="group.user?.id ?? index">
                                    <!-- Group Header Row -->
                                    <tr class="hover:bg-white/70 transition-colors duration-200 cursor-pointer group"
                                        @click="toggleUser(group.user?.id ?? index)">
                                        
                                        <!-- Expand Icon -->
                                        <td class="px-6 py-4 align-middle">
                                            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center transition-all duration-300"
                                                 :class="isExpanded(group.user?.id ?? index) ? 'bg-violet-100 rotate-90' : ''">
                                                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-500"
                                                   :class="isExpanded(group.user?.id ?? index) ? 'text-violet-600' : ''"></i>
                                            </div>
                                        </td>

                                        <!-- User Column -->
                                        <td class="px-6 py-4 align-middle">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 border border-slate-200 flex items-center justify-center font-black text-sm overflow-hidden flex-shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                                                    <img v-if="avatarUrl(group.user?.avatar)" :src="avatarUrl(group.user?.avatar)" class="w-full h-full object-cover">
                                                    <span v-else>{{ group.user?.name?.charAt(0) }}</span>
                                                </div>
                                                <div>
                                                    <h4 class="font-black text-slate-800 text-sm group-hover:text-violet-700 transition-colors">{{ group.user?.rank }} {{ group.user?.name }}</h4>
                                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">{{ group.user?.position || group.user?.department || '-' }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Count -->
                                        <td class="px-6 py-4 align-middle text-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-violet-50 text-violet-700 border border-violet-100 font-black text-sm">
                                                <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                                                {{ group.records.length }} ครั้ง
                                            </span>
                                        </td>

                                        <!-- Total Days -->
                                        <td class="px-6 py-4 align-middle text-center">
                                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl font-black text-base shadow-sm"
                                                  :class="group.totalDays >= 1 ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
                                                {{ group.totalDays % 1 === 0 ? group.totalDays.toFixed(0) : group.totalDays.toFixed(1) }} วัน
                                            </span>
                                        </td>

                                        <!-- Morning Count -->
                                        <td class="px-6 py-4 align-middle text-center">
                                            <span class="font-black text-blue-500 text-sm">
                                                {{ group.records.filter(r => r.temporary_leave_period === 'morning').length }}
                                            </span>
                                        </td>

                                        <!-- Afternoon Count -->
                                        <td class="px-6 py-4 align-middle text-center">
                                            <span class="font-black text-purple-500 text-sm">
                                                {{ group.records.filter(r => r.temporary_leave_period === 'afternoon').length }}
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Expanded Sub-Rows -->
                                    <template v-if="isExpanded(group.user?.id ?? index)">
                                        <tr v-for="(r, ri) in group.records" :key="r.id"
                                            class="bg-slate-50/80 border-l-4 border-violet-300 hover:bg-white/60 transition-colors duration-150"
                                            :style="`animation-delay: ${ri * 40}ms;`">
                                            
                                            <!-- Indent -->
                                            <td class="px-6 py-3 align-middle">
                                                <div class="w-1 h-6 bg-violet-200 rounded-full mx-auto"></div>
                                            </td>

                                            <!-- Sub-row: User (blank / indent) -->
                                            <td class="px-6 py-3 align-middle pl-14">
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0"></i>
                                                    <span class="font-bold text-slate-700 text-xs">{{ r.start_date_thai || r.start_date }}</span>
                                                </div>
                                            </td>

                                            <!-- Sub-row: Count (0.5) -->
                                            <td class="px-6 py-3 align-middle text-center">
                                                <span class="text-xs font-black text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">0.5 วัน</span>
                                            </td>

                                            <!-- Sub-row: Period -->
                                            <td class="px-6 py-3 align-middle text-center" colspan="1">
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border font-black text-[10px]"
                                                     :class="r.temporary_leave_period === 'morning' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-purple-50 text-purple-600 border-purple-100'">
                                                    <i :data-lucide="r.temporary_leave_period === 'morning' ? 'sunrise' : 'sunset'" class="w-3 h-3"></i>
                                                    {{ r.temporary_leave_period === 'morning' ? 'ครึ่งเช้า' : 'ครึ่งบ่าย' }}
                                                </div>
                                            </td>

                                            <!-- Sub-row: Status -->
                                            <td class="px-6 py-3 align-middle text-center">
                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border font-black text-[10px]"
                                                     :class="r.status === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100/50' : 'bg-amber-50 text-amber-600 border-amber-100/50'">
                                                    <span class="w-1.5 h-1.5 rounded-full" :class="r.status === 'approved' ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                                                    {{ r.status === 'approved' ? 'อนุมัติ' : 'รอดำเนินการ' }}
                                                </div>
                                            </td>

                                            <!-- Delete Button -->
                                            <td class="px-6 py-3 align-middle text-center">
                                                <button @click.stop="deleteRequest(r.id)"
                                                        :disabled="deletingId === r.id"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-500 border border-rose-100 hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all duration-200 font-black text-[10px] shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <i :data-lucide="deletingId === r.id ? 'loader-2' : 'trash-2'" 
                                                       class="w-3.5 h-3.5" 
                                                       :class="deletingId === r.id ? 'animate-spin' : ''"></i>
                                                    {{ deletingId === r.id ? 'กำลังลบ...' : 'ลบ' }}
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="!groupedRequests.length" class="p-20 text-center">
                        <div class="w-20 h-20 bg-white/50 border border-white rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-sm">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-600 mb-2">ไม่มีข้อมูลการลาชั่วกาล</h4>
                        <p class="text-sm font-bold text-slate-400">ระบบยังไม่มีรายการขออนุญาตออกนอกบริเวณโรงเรียนในขณะนี้</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="requests?.links && requests.links.length > 3" class="mt-10 flex justify-center pb-8"
                     :class="{ 'opacity-100 translate-y-0': isLoaded, 'opacity-0 translate-y-8': !isLoaded }" style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); transition-delay: 500ms;">
                    <div class="bg-white/60 backdrop-blur-xl p-2 rounded-[1.5rem] shadow-sm border border-white flex gap-1">
                        <template v-for="(link, i) in requests.links" :key="i">
                            <Link v-if="link.url" :href="link.url" class="px-4 py-2.5 rounded-xl text-xs font-black transition-all"
                                :class="link.active ? 'bg-violet-600 text-white shadow-md shadow-violet-500/20' : 'bg-transparent text-slate-500 hover:bg-white border border-transparent hover:border-slate-100 hover:text-slate-700'" v-html="link.label" />
                            <span v-else class="px-3 py-2.5 text-xs text-slate-300 font-bold" v-html="link.label" />
                        </template>
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
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 50px -10px rgba(139, 92, 246, 0.1), inset 0 1px 0 rgba(255, 255, 255, 1);
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

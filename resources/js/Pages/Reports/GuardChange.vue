<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({ requests: Object, departments: Array, stats: Object });

const statusMap = {
    fully_approved: { label: 'อนุมัติสมบูรณ์', cls: 'bg-emerald-50 text-emerald-600 border-emerald-100' },
    rejected: { label: 'ปฏิเสธ', cls: 'bg-rose-50 text-rose-600 border-rose-100' },
    cancelled: { label: 'ยกเลิก', cls: 'bg-slate-50 text-slate-500 border-slate-200' },
};
function getStatus(s) { return statusMap[s] || { label: s, cls: 'bg-amber-50 text-amber-600 border-amber-100' }; }

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="รายงานเปลี่ยนยาม">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                    <i data-lucide="repeat" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">รายงานเปลี่ยนยาม</h2>
                    <p class="text-sm text-slate-400 font-medium mt-0.5">สถิติการเปลี่ยนยามทั้งหมด</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center"><i data-lucide="layers" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ทั้งหมด</p></div>
                    <p class="text-3xl font-black text-slate-800">{{ stats?.total || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="check-circle" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">อนุมัติ</p></div>
                    <p class="text-3xl font-black text-emerald-600">{{ stats?.approved || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><i data-lucide="clock" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">รอดำเนินการ</p></div>
                    <p class="text-3xl font-black text-amber-600">{{ stats?.pending || 0 }}</p>
                </div>
                <div class="glass-panel rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition-all">
                    <div class="flex items-center gap-3 mb-2"><div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center"><i data-lucide="x-circle" class="w-4 h-4"></i></div><p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">ปฏิเสธ/ยกเลิก</p></div>
                    <p class="text-3xl font-black text-rose-600">{{ stats?.rejected || 0 }}</p>
                </div>
            </div>
            <div class="glass-panel rounded-[2rem] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ผู้ขอ</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ผู้แทน</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">วันที่</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">สถานะ</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="r in (requests?.data || [])" :key="r.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ r.user?.rank }} {{ r.user?.name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ r.replacement_user?.rank }} {{ r.replacement_user?.name }}</td>
                                <td class="px-6 py-4 text-slate-500 text-xs font-bold">{{ r.duty_date }}</td>
                                <td class="px-6 py-4"><span class="px-3 py-1 rounded-lg text-xs font-black border" :class="getStatus(r.status).cls">{{ getStatus(r.status).label }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="!requests?.data?.length" class="p-20 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner"><i data-lucide="inbox" class="w-8 h-8 text-slate-200"></i></div>
                    <p class="text-slate-400 font-bold">ไม่มีข้อมูล</p>
                </div>
            </div>
            <div v-if="requests?.links && requests.links.length > 3" class="flex justify-center gap-1 mt-8">
                <template v-for="link in requests.links" :key="link.label">
                    <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-xl text-sm font-black transition-all" :class="link.active ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                    <span v-else class="px-4 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
}
</style>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    mostScans: Array, earlyBirds: Array, neverLate: Array, mostLate: Array,
    kingOfLeave: Array, mostRequests: Array, longAbsence: Array, diverseLeave: Array,
    year: Number, month: [Number, String],
});

const filterYear = ref(props.year);
const filterMonth = ref(props.month || '');

function applyFilter() {
    router.get('/ranking', { year: filterYear.value, month: filterMonth.value || undefined }, { preserveState: true });
}

const medals = ['🥇', '🥈', '🥉', '4️⃣', '5️⃣'];

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="สถิติเด่น">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <i data-lucide="trophy" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">สถิติเด่น</h2>
                        <p class="text-sm text-slate-400 font-medium mt-0.5">อันดับต่างๆ ของข้าราชการ ปี {{ year + 543 }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <select v-model="filterYear" @change="applyFilter" class="px-5 py-3 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option v-for="y in [year-1, year, year+1]" :key="y" :value="y">{{ y + 543 }}</option>
                    </select>
                    <select v-model="filterMonth" @change="applyFilter" class="px-5 py-3 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                        <option value="">ทั้งปี</option>
                        <option v-for="m in 12" :key="m" :value="m">เดือน {{ m }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="glass-panel rounded-[2rem] overflow-hidden hover:shadow-lg transition-all">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-blue-50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center"><i data-lucide="scan-line" class="w-5 h-5"></i></div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">นาฬิกาชีวิตแม่นยำ</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div v-for="(item, i) in mostScans" :key="item.employee_id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-xl w-8 text-center">{{ medals[i] || '' }}</span>
                            <p class="flex-1 font-bold text-slate-700 text-sm">{{ item.employee?.name }}</p>
                            <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100">{{ item.count }} ครั้ง</span>
                        </div>
                        <p v-if="!mostScans || mostScans.length === 0" class="text-slate-400 text-sm text-center py-6">ไม่มีข้อมูล</p>
                    </div>
                </div>

                <div class="glass-panel rounded-[2rem] overflow-hidden hover:shadow-lg transition-all">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center"><i data-lucide="sunrise" class="w-5 h-5"></i></div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">มาเช้าดีเด่น</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div v-for="(item, i) in earlyBirds" :key="item.employee_id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-xl w-8 text-center">{{ medals[i] || '' }}</span>
                            <p class="flex-1 font-bold text-slate-700 text-sm">{{ item.employee?.name }}</p>
                        </div>
                        <p v-if="!earlyBirds || earlyBirds.length === 0" class="text-slate-400 text-sm text-center py-6">ไม่มีข้อมูล</p>
                    </div>
                </div>

                <div class="glass-panel rounded-[2rem] overflow-hidden hover:shadow-lg transition-all">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-rose-50 to-pink-50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center"><i data-lucide="crown" class="w-5 h-5"></i></div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">ราชาแห่งการลา (วันลารวม)</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div v-for="(item, i) in kingOfLeave" :key="item.user_id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-xl w-8 text-center">{{ medals[i] || '' }}</span>
                            <p class="flex-1 font-bold text-slate-700 text-sm">{{ item.user?.rank }} {{ item.user?.name }}</p>
                            <span class="text-xs font-black text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">{{ item.total_days }} วัน</span>
                        </div>
                        <p v-if="!kingOfLeave || kingOfLeave.length === 0" class="text-slate-400 text-sm text-center py-6">ไม่มีข้อมูล</p>
                    </div>
                </div>

                <div class="glass-panel rounded-[2rem] overflow-hidden hover:shadow-lg transition-all">
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-indigo-50 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500 text-white flex items-center justify-center"><i data-lucide="medal" class="w-5 h-5"></i></div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">นักลาดีเด่น (จำนวนครั้ง)</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <div v-for="(item, i) in mostRequests" :key="item.user_id" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                            <span class="text-xl w-8 text-center">{{ medals[i] || '' }}</span>
                            <p class="flex-1 font-bold text-slate-700 text-sm">{{ item.user?.rank }} {{ item.user?.name }}</p>
                            <span class="text-xs font-black text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-100">{{ item.count }} ครั้ง</span>
                        </div>
                        <p v-if="!mostRequests || mostRequests.length === 0" class="text-slate-400 text-sm text-center py-6">ไม่มีข้อมูล</p>
                    </div>
                </div>
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

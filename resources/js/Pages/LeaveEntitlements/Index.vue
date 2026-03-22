<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({ employees: Object, departments: Array, vacationType: Object });

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="สิทธิ์ลาพักร้อน">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8 flex items-center gap-4">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100">
                    <i data-lucide="sun" class="w-6 h-6 text-amber-600"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">สิทธิ์ลาพักร้อน</h2>
                    <p class="text-sm text-slate-400 font-medium mt-0.5">จัดการสิทธิ์ลาพักร้อนของข้าราชการทั้งหมด</p>
                </div>
            </div>
            <div class="glass-panel rounded-[2rem] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">แผนก</th>
                            <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">สิทธิ์ทั้งหมด</th>
                            <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">ใช้ไป</th>
                            <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">คงเหลือ</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="emp in (employees?.data || employees || [])" :key="emp.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ emp.rank }} {{ emp.name }}</td>
                                <td class="px-6 py-4"><span v-if="emp.department" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">{{ emp.department }}</span><span v-else class="text-slate-300">—</span></td>
                                <td class="px-6 py-4 text-right font-black text-slate-700">{{ emp.vacation_total ?? '-' }}</td>
                                <td class="px-6 py-4 text-right font-black text-amber-600">{{ emp.vacation_used ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex px-3 py-1 rounded-lg text-xs font-black" :class="(emp.vacation_remaining ?? 0) > 5 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : (emp.vacation_remaining ?? 0) > 0 ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-rose-50 text-rose-600 border border-rose-100'">{{ emp.vacation_remaining ?? '-' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="(!employees?.data && !Array.isArray(employees)) || (employees?.data || employees || []).length === 0" class="p-20 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner"><i data-lucide="inbox" class="w-8 h-8 text-slate-200"></i></div>
                    <p class="text-slate-400 font-bold">ไม่มีข้อมูล</p>
                </div>
            </div>
            <div v-if="employees?.links && employees.links.length > 3" class="flex justify-center gap-1 mt-8">
                <template v-for="link in employees.links" :key="link.label">
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

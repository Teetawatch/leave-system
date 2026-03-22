<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({ pendingUsers: Object });

function approve(id) { router.post(`/employees/${id}/approve-registration`); }
function reject(id) { if (confirm('ปฏิเสธการลงทะเบียนนี้?')) router.post(`/employees/${id}/reject-registration`); }

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="รอลงทะเบียน">
        <div class="max-w-5xl mx-auto">
            <div class="mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100">
                        <i data-lucide="user-check" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">รอการอนุมัติลงทะเบียน</h2>
                        <p class="text-sm text-slate-400 font-medium mt-0.5">ข้าราชการที่ลงทะเบียนเข้าระบบ รอผู้ดูแลอนุมัติ</p>
                    </div>
                    <div v-if="pendingUsers?.data?.length" class="ml-auto px-5 py-2 rounded-full bg-amber-50 text-amber-600 text-sm font-black border border-amber-100">
                        {{ pendingUsers.data.length }} <span class="text-amber-400 text-xs">รายการ</span>
                    </div>
                </div>
            </div>
            <div class="glass-card rounded-[2rem] shadow-xl overflow-hidden">
                <div class="divide-y divide-slate-50">
                    <div v-for="user in (pendingUsers?.data || [])" :key="user.id" class="flex items-center gap-5 px-8 py-5 hover:bg-slate-50/50 transition-colors group">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-lg border border-amber-100 group-hover:bg-amber-600 group-hover:text-white transition-all shrink-0">
                            {{ user.name?.charAt(0) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-black text-slate-800">{{ user.rank }} {{ user.name }}</p>
                            <div class="flex items-center gap-3 mt-0.5">
                                <span class="text-xs text-slate-400">{{ user.email }}</span>
                                <span v-if="user.department" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100">{{ user.department }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <button @click="approve(user.id)" class="px-5 py-2.5 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-black hover:bg-emerald-600 hover:text-white border border-emerald-100 hover:border-emerald-600 transition-all hover:-translate-y-0.5">
                                <i data-lucide="check" class="w-4 h-4 inline -mt-0.5"></i> อนุมัติ
                            </button>
                            <button @click="reject(user.id)" class="px-5 py-2.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-black hover:bg-rose-600 hover:text-white border border-rose-100 hover:border-rose-600 transition-all hover:-translate-y-0.5">
                                <i data-lucide="x" class="w-4 h-4 inline -mt-0.5"></i> ปฏิเสธ
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="!pendingUsers?.data || pendingUsers.data.length === 0" class="p-20 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i data-lucide="inbox" class="w-10 h-10 text-slate-200"></i>
                    </div>
                    <h4 class="text-xl font-black text-slate-900 mb-2 tracking-tight">ไม่มีผู้รอการอนุมัติ</h4>
                    <p class="text-slate-400 font-medium text-sm">ขณะนี้ยังไม่มีการลงทะเบียนใหม่</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.8);
}
</style>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({ employees: Object, departments: Array });
const search = ref('');
const selectedDept = ref('');
const viewMode = ref('grid');

function applyFilter() {
    router.get('/employees', { search: search.value, department: selectedDept.value }, { preserveState: true });
}

function deleteEmployee(id) {
    if (confirm('คุณต้องการลบพนักงานนี้หรือไม่?')) router.delete(`/employees/${id}`);
}

function roleLabel(role) {
    const map = { admin: 'Admin', director: 'ผอ.', deputy_director: 'รอง ผอ.', department_head: 'หน.แผนก', employee: 'User' };
    return map[role] || role;
}
function roleBadge(role) {
    const map = {
        admin: 'bg-rose-50 text-rose-600 border-rose-100',
        director: 'bg-purple-50 text-purple-600 border-purple-100',
        deputy_director: 'bg-violet-50 text-violet-600 border-violet-100',
        department_head: 'bg-amber-50 text-amber-600 border-amber-100',
    };
    return map[role] || 'bg-slate-50 text-slate-600 border-slate-100';
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="จัดการข้าราชการ">
        <div class="max-w-[95rem] mx-auto">
            <!-- Header -->
            <div class="mb-10 space-y-8">
                <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
                    <div class="relative">
                        <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-brand-500 rounded-full"></div>
                        <h1 class="text-4xl font-black text-slate-800 tracking-tight">บุคลากรทั้งหมด</h1>
                        <p class="text-slate-500 mt-1 text-lg">บริหารจัดการข้อมูลรายชื่อ ยศ ตำแหน่ง และสิทธิ์การใช้งานระบบ</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100">
                            <a href="/employees/export" class="inline-flex items-center px-4 py-2 text-slate-600 font-black text-sm hover:text-brand-600 transition-colors gap-2 group">
                                <i data-lucide="download" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i> Export
                            </a>
                            <div class="w-px h-4 bg-slate-200 mx-2"></div>
                            <Link href="/employees/import" class="inline-flex items-center px-4 py-2 text-slate-600 font-black text-sm hover:text-emerald-600 transition-colors gap-2 group">
                                <i data-lucide="upload" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i> Import
                            </Link>
                        </div>
                        <Link href="/employees/create" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 text-white font-black rounded-2xl shadow-xl shadow-brand-500/20 transition-all hover:-translate-y-1 gap-2">
                            <i data-lucide="user-plus" class="w-5 h-5"></i> เพิ่มข้าราชการใหม่
                        </Link>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50 group hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">ยอดรวมกำลังพล</p>
                                <h3 class="text-4xl font-black text-slate-800 group-hover:text-indigo-600 transition-colors">{{ employees.total || employees.data?.length || 0 }}</h3>
                                <p class="text-[10px] text-slate-400 mt-2 font-bold"><span class="text-emerald-500">Active</span> ใช้งานอยู่</p>
                            </div>
                            <div class="w-16 h-16 rounded-3xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-inner group-hover:rotate-6 transition-transform">
                                <i data-lucide="users" class="w-8 h-8"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50 group hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">แผนกทั้งหมด</p>
                                <h3 class="text-4xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors">{{ departments?.length || 0 }}</h3>
                                <p class="text-[10px] text-slate-400 mt-2 font-bold">ตาม <span class="text-brand-500">โครงสร้างองค์กร</span></p>
                            </div>
                            <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner group-hover:-rotate-6 transition-transform">
                                <i data-lucide="building-2" class="w-8 h-8"></i>
                            </div>
                        </div>
                    </div>
                    <Link href="/employees/pending-registrations" class="relative overflow-hidden bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-50 group hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform duration-500"></div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">ค้างอนุมัติลงทะเบียน</p>
                                <h3 class="text-4xl font-black text-slate-800">—</h3>
                                <p class="text-[10px] text-slate-400 mt-2 font-bold">คลิกเพื่อ <span class="text-brand-500">ตรวจสอบ</span></p>
                            </div>
                            <div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-400 flex items-center justify-center shadow-inner group-hover:rotate-12 transition-transform">
                                <i data-lucide="user-plus" class="w-8 h-8"></i>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Sticky Toolbar -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl shadow-slate-200/40 border border-white p-4 mb-8 flex flex-col lg:flex-row items-center justify-between gap-6 sticky top-24 z-30">
                <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-4 flex-1">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <input v-model="search" @keyup.enter="applyFilter" type="text" placeholder="พิมพ์เพื่อค้นหาชื่อ, อีเมล, หรือตำแหน่ง..."
                            class="block w-full pl-12 pr-4 py-3.5 bg-slate-100/50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl text-sm font-bold transition-all text-slate-700">
                    </div>
                    <div class="relative min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                        </div>
                        <select v-model="selectedDept" @change="applyFilter"
                            class="block w-full pl-11 pr-10 py-3.5 bg-slate-100/50 border-transparent focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 rounded-2xl text-sm font-bold appearance-none cursor-pointer transition-all text-slate-700">
                            <option value="">ทุกแผนก/ฝ่าย</option>
                            <option v-for="dept in departments" :key="dept.id" :value="dept.name">{{ dept.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex bg-slate-100 p-1.5 rounded-[1.25rem]">
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-brand-600 shadow-md scale-105' : 'text-slate-500 hover:text-slate-800'" class="p-2.5 rounded-xl transition-all duration-300">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                    </button>
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white text-brand-600 shadow-md scale-105' : 'text-slate-500 hover:text-slate-800'" class="p-2.5 rounded-xl transition-all duration-300">
                        <i data-lucide="list" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8 min-h-[500px]">
                <div v-for="emp in employees.data" :key="emp.id"
                    class="bg-white rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border border-slate-50 hover:shadow-2xl hover:border-brand-200 transition-all duration-500 group relative flex flex-col h-full overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-brand-50 transition-colors duration-500"></div>
                    <div class="relative flex flex-col items-center flex-1">
                        <div class="relative mb-4 group-hover:-translate-y-2 transition-transform duration-500">
                            <div class="absolute -inset-1 rounded-[2rem] bg-gradient-to-br from-brand-500 to-indigo-500 blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                            <img v-if="emp.avatar" :src="`/storage/${emp.avatar}`" class="relative h-24 w-24 rounded-[1.75rem] object-cover ring-4 ring-white shadow-xl">
                            <div v-else class="relative h-24 w-24 rounded-[1.75rem] bg-gradient-to-br from-brand-50 to-indigo-50 text-brand-600 flex items-center justify-center text-3xl font-black ring-4 ring-white shadow-xl">
                                {{ emp.name?.charAt(0) }}
                            </div>
                            <div class="absolute -bottom-2 -right-2 p-1 bg-white rounded-xl shadow-lg border border-slate-50">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center" :class="emp.role === 'admin' ? 'bg-rose-50 text-rose-500' : 'bg-brand-50 text-brand-500'">
                                    <i :data-lucide="emp.role === 'admin' ? 'shield-check' : 'user'" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-center w-full px-2">
                            <h3 class="text-lg font-black text-slate-800 line-clamp-1 group-hover:text-brand-600 transition-colors duration-300">{{ emp.rank }}{{ emp.name }}</h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1 mb-3 truncate">{{ emp.position || 'ข้าราชการ/เจ้าหน้าที่' }}</p>
                            <div class="flex flex-wrap items-center justify-center gap-2 mb-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border" :class="roleBadge(emp.role)">{{ roleLabel(emp.role) }}</span>
                                <span v-if="emp.department" class="px-3 py-1 rounded-full text-[10px] font-black bg-slate-50 text-slate-500 border border-slate-100 truncate max-w-[120px]">{{ emp.department }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-4 border-t border-slate-100 mt-auto">
                        <Link :href="`/employees/${emp.id}/edit`" class="flex-1 text-center py-2.5 rounded-xl bg-brand-50 text-brand-600 text-xs font-black hover:bg-brand-100 transition-all">แก้ไข</Link>
                        <button @click="deleteEmployee(emp.id)" class="flex-1 text-center py-2.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-black hover:bg-rose-100 transition-all">ลบ</button>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div v-else class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden min-h-[500px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">แผนก</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ตำแหน่ง</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">บทบาท</th>
                            <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">จัดการ</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="emp in employees.data" :key="emp.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-50 to-indigo-50 text-brand-600 flex items-center justify-center font-black overflow-hidden ring-2 ring-white shadow">
                                            <img v-if="emp.avatar" :src="`/storage/${emp.avatar}`" class="w-full h-full object-cover">
                                            <span v-else>{{ emp.name?.charAt(0) }}</span>
                                        </div>
                                        <span class="font-black text-slate-800">{{ emp.rank }}{{ emp.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-medium">{{ emp.department || '-' }}</td>
                                <td class="px-6 py-4 text-slate-600 font-medium">{{ emp.position || '-' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border" :class="roleBadge(emp.role)">{{ roleLabel(emp.role) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Link :href="`/employees/${emp.id}/edit`" class="px-4 py-2 rounded-xl bg-brand-50 text-brand-600 text-xs font-black hover:bg-brand-100 transition">แก้ไข</Link>
                                        <button @click="deleteEmployee(emp.id)" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-black hover:bg-rose-100 transition">ลบ</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="employees.links && employees.links.length > 3" class="mt-12 flex justify-center">
                <div class="bg-white/80 backdrop-blur-md p-3 rounded-[2rem] shadow-xl border border-white/50 flex gap-1">
                    <template v-for="link in employees.links" :key="link.label">
                        <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-xl text-sm font-black transition-all"
                            :class="link.active ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                        <span v-else class="px-4 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

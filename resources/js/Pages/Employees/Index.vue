<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, computed } from 'vue';
import { confirmDelete, toastError, toastSuccess } from '@/utils/swal';

const props = defineProps({ 
    employees: Object, 
    departments: Array,
    allEmployees: Array 
});
const search = ref('');
const selectedDept = ref('');
const viewMode = ref('grid');
const selectedEmployees = ref([]);
const showOfficialDutyModal = ref(false);
const officialDutyForm = ref({
    start_date: '',
    end_date: '',
    reason: '',
    location: '',
    attachment: null
});

const searchInModal = ref('');
const filteredEmployeesForModal = computed(() => {
    if (!props.allEmployees) return [];
    if (!searchInModal.value) return props.allEmployees;
    const s = searchInModal.value.toLowerCase();
    return props.allEmployees.filter(e => 
        (e.name && e.name.toLowerCase().includes(s)) || 
        (e.rank && e.rank.toLowerCase().includes(s)) ||
        (e.department && e.department.toLowerCase().includes(s))
    );
});

function applyFilter() {
    router.get('/employees', { search: search.value, department: selectedDept.value }, { preserveState: true });
}

async function deleteEmployee(id) {
    const result = await confirmDelete({ title: 'ลบข้อมูลบุคลากร?', text: 'คุณต้องการลบบุคลากรนี้หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้' });
    if (result.isConfirmed) router.delete(`/employees/${id}`);
}

function toggleSelect(id) {
    const index = selectedEmployees.value.indexOf(id);
    if (index > -1) selectedEmployees.value.splice(index, 1);
    else selectedEmployees.value.push(id);
}

function toggleSelectAll() {
    if (selectedEmployees.value.length === props.employees.data.length) {
        selectedEmployees.value = [];
    } else {
        selectedEmployees.value = props.employees.data.map(e => e.id);
    }
}

function openOfficialDutyModal() {
    showOfficialDutyModal.value = true;
    nextTick(() => {
        if (window.lucide) window.lucide.createIcons();
    });
}

function submitOfficialDuty() {
    if (selectedEmployees.value.length === 0) {
        toastError('กรุณาเลือกบุคลากรอย่างน้อย 1 คน');
        return;
    }
    const formData = new FormData();
    selectedEmployees.value.forEach(id => formData.append('employee_ids[]', id));
    formData.append('start_date', officialDutyForm.value.start_date);
    formData.append('end_date', officialDutyForm.value.end_date);
    formData.append('reason', officialDutyForm.value.reason);
    formData.append('location', officialDutyForm.value.location);
    if (officialDutyForm.value.attachment) {
        formData.append('attachment', officialDutyForm.value.attachment);
    }

    router.post('/employees/bulk-official-duty', formData, {
        onSuccess: () => {
            showOfficialDutyModal.value = false;
            selectedEmployees.value = [];
            officialDutyForm.value = { start_date: '', end_date: '', reason: '', location: '', attachment: null };
            toastSuccess('บันทึกข้อมูลการไปราชการเรียบร้อยแล้ว');
        }
    });
}

function handleFileChange(e) {
    officialDutyForm.value.attachment = e.target.files[0];
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
                            <div class="w-px h-4 bg-slate-200 mx-2"></div>
                            <Link href="/employees/pending-registrations" class="inline-flex items-center px-4 py-2 text-slate-600 font-black text-sm hover:text-amber-600 transition-colors gap-2 group relative">
                                <i data-lucide="user-check" class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform"></i> คำขอลงทะเบียน
                            </Link>
                        </div>
                        <button :disabled="selectedEmployees.length === 0" @click.stop="openOfficialDutyModal" 
                            class="inline-flex items-center px-6 py-3 font-black rounded-2xl border-2 transition-all gap-2 disabled:opacity-50 disabled:cursor-not-allowed group"
                            :class="selectedEmployees.length > 0 ? 'bg-brand-50 text-brand-600 border-brand-200 hover:bg-brand-100 hover:-translate-y-1 shadow-lg shadow-brand-500/10' : 'bg-slate-50 text-slate-400 border-slate-100'"
                            :title="selectedEmployees.length === 0 ? 'กรุณาเลือกบุคลากรอย่างน้อย 1 คนเพื่อบันทึกไปราชการ' : ''">
                            <i data-lucide="briefcase" class="w-5 h-5 group-hover:scale-110 transition-transform"></i> ไปราชการ {{ selectedEmployees.length > 0 ? `(${selectedEmployees.length})` : '' }}
                        </button>
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
                    @click="toggleSelect(emp.id)"
                    :class="selectedEmployees.includes(emp.id) ? 'ring-2 ring-brand-500 bg-brand-50/30' : 'bg-white border-slate-50'"
                    class="rounded-[2rem] p-6 shadow-xl shadow-slate-200/40 border hover:shadow-2xl hover:border-brand-200 transition-all duration-500 group relative flex flex-col h-full overflow-hidden cursor-pointer">
                    <div class="absolute top-4 left-4 z-20">
                        <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                            :class="selectedEmployees.includes(emp.id) ? 'bg-brand-500 border-brand-500' : 'bg-white/80 border-slate-200 group-hover:border-brand-300'">
                            <i v-if="selectedEmployees.includes(emp.id)" data-lucide="check" class="w-4 h-4 text-white"></i>
                        </div>
                    </div>
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
                        <Link @click.stop :href="`/employees/${emp.id}/edit`" class="flex-1 text-center py-2.5 rounded-xl bg-brand-50 text-brand-600 text-xs font-black hover:bg-brand-100 transition-all">แก้ไข</Link>
                        <button @click.stop="deleteEmployee(emp.id)" class="flex-1 text-center py-2.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-black hover:bg-rose-100 transition-all">ลบ</button>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div v-else class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden min-h-[500px]">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-left w-12">
                                <button @click="toggleSelectAll" class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                                    :class="selectedEmployees.length === employees.data.length ? 'bg-brand-500 border-brand-500' : 'bg-white border-slate-200'">
                                    <i v-if="selectedEmployees.length === employees.data.length" data-lucide="check" class="w-4 h-4 text-white"></i>
                                </button>
                            </th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ชื่อ</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">แผนก</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">ตำแหน่ง</th>
                            <th class="px-6 py-4 text-left font-black text-slate-400 text-xs uppercase tracking-widest">บทบาท</th>
                            <th class="px-6 py-4 text-right font-black text-slate-400 text-xs uppercase tracking-widest">จัดการ</th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="emp in employees.data" :key="emp.id"
                                @click="toggleSelect(emp.id)"
                                :class="selectedEmployees.includes(emp.id) ? 'bg-brand-50/50' : 'hover:bg-slate-50/50'"
                                class="border-b border-slate-50 transition-colors cursor-pointer">
                                <td class="px-6 py-4">
                                    <div class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition-all"
                                        :class="selectedEmployees.includes(emp.id) ? 'bg-brand-500 border-brand-500' : 'bg-white border-slate-200'">
                                        <i v-if="selectedEmployees.includes(emp.id)" data-lucide="check" class="w-4 h-4 text-white"></i>
                                    </div>
                                </td>
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
                                        <Link @click.stop :href="`/employees/${emp.id}/edit`" class="px-4 py-2 rounded-xl bg-brand-50 text-brand-600 text-xs font-black hover:bg-brand-100 transition">แก้ไข</Link>
                                        <button @click.stop="deleteEmployee(emp.id)" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-black hover:bg-rose-100 transition">ลบ</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="employees.links && employees.links.length > 3" class="mt-12 flex justify-center overflow-x-auto pb-4 custom-scrollbar">
                <div class="bg-white/80 backdrop-blur-md p-2 rounded-[2rem] shadow-xl border border-white/50 flex gap-1 px-3">
                    <template v-for="(link, index) in employees.links" :key="index">
                        <Link 
                            v-if="link.url" 
                            :href="link.url" 
                            :preserve-scroll="true"
                            class="px-4 py-2.5 rounded-xl text-sm font-black transition-all flex items-center justify-center min-w-[40px]"
                            :class="[
                                link.active 
                                    ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' 
                                    : 'bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 border border-slate-100'
                            ]"
                            v-html="link.label" 
                        />
                        <span 
                            v-else 
                            class="px-4 py-2.5 text-sm text-slate-300 font-bold min-w-[40px] flex items-center justify-center" 
                            v-html="link.label" 
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Official Duty Modal -->
        <div v-if="showOfficialDutyModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showOfficialDutyModal = false"></div>
            <div class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
                <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-brand-600 to-brand-500 text-white">
                    <div>
                        <h3 class="text-2xl font-black">บันทึกข้อมูลไปราชการ</h3>
                        <p class="text-brand-100 text-sm mt-1">
                            <span v-if="selectedEmployees.length > 0">เลือกบุคลากรแล้ว {{ selectedEmployees.length }} ท่าน</span>
                            <span v-else>กรุณาเลือกบุคลากรที่ต้องการ</span>
                        </p>
                    </div>
                    <button @click="showOfficialDutyModal = false" class="p-2 hover:bg-white/20 rounded-xl transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <div class="p-8 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    <form @submit.prevent="submitOfficialDuty" class="space-y-6">
                        <!-- Search and Select Employees Section -->
                        <div class="space-y-4 p-6 bg-slate-50 rounded-[2rem] border-2 border-slate-100">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-black text-slate-800">เลือกผู้ไปราชการ</label>
                                <button type="button" @click="selectedEmployees = []" class="text-xs text-rose-500 font-bold hover:underline">ล้างทั้งหมด</button>
                            </div>
                            
                            <!-- Search Box In Modal -->
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                                <input v-model="searchInModal" type="text" placeholder="ค้นหาชื่อ หรือ ยศ..." 
                                    class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
                            </div>

                            <!-- Searchable Employee List -->
                            <div class="max-h-48 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                                <div v-for="emp in filteredEmployeesForModal" :key="emp.id" 
                                    @click="toggleSelect(emp.id)"
                                    class="flex items-center justify-between p-3 rounded-xl border transition-all cursor-pointer group"
                                    :class="selectedEmployees.includes(emp.id) ? 'bg-brand-50 border-brand-200' : 'bg-white border-slate-100 hover:border-brand-200'">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                                            <img v-if="emp.avatar" :src="`/storage/${emp.avatar}`" class="w-full h-full object-cover">
                                            <span v-else class="text-[10px] font-bold text-slate-400">{{ emp.name?.charAt(0) }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-800">{{ emp.rank }}{{ emp.name }}</span>
                                            <span class="text-[10px] text-slate-500">{{ emp.department || '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all"
                                        :class="selectedEmployees.includes(emp.id) ? 'bg-brand-500 border-brand-500' : 'bg-white border-slate-200 group-hover:border-brand-300'">
                                        <i v-if="selectedEmployees.includes(emp.id)" data-lucide="check" class="w-3 h-3 text-white"></i>
                                    </div>
                                </div>
                                <div v-if="filteredEmployeesForModal.length === 0" class="py-8 text-center">
                                    <p class="text-xs text-slate-400 font-bold">ไม่พบรายชื่อที่ค้นหา</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-sm font-black text-slate-700 ml-1">วันที่เริ่มต้น</label>
                                <input v-model="officialDutyForm.start_date" type="date" required
                                    class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:bg-white focus:border-brand-500 rounded-2xl font-bold transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-black text-slate-700 ml-1">วันที่สิ้นสุด</label>
                                <input v-model="officialDutyForm.end_date" type="date" required
                                    class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:bg-white focus:border-brand-500 rounded-2xl font-bold transition-all outline-none">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-black text-slate-700 ml-1">สถานที่/จังหวัด</label>
                            <input v-model="officialDutyForm.location" type="text" required placeholder="เช่น จ.เชียงใหม่ หรือ ศูนย์ฝึกอบรม..."
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:bg-white focus:border-brand-500 rounded-2xl font-bold transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-black text-slate-700 ml-1">เหตุผล/โครงการ</label>
                            <textarea v-model="officialDutyForm.reason" rows="3" required placeholder="ระบุวัตถุประสงค์ของการไปราชการ..."
                                class="w-full px-5 py-4 bg-slate-50 border-2 border-transparent focus:bg-white focus:border-brand-500 rounded-2xl font-bold transition-all outline-none resize-none"></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-black text-slate-700 ml-1">เอกสารแนบ (PDF - ถ้ามี)</label>
                            <div class="relative group">
                                <input type="file" @change="handleFileChange" accept=".pdf"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-full px-5 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl font-bold group-hover:border-brand-400 transition-all flex items-center justify-center gap-3 text-slate-500 group-hover:text-brand-600">
                                    <i data-lucide="file-up" class="w-5 h-5"></i>
                                    {{ officialDutyForm.attachment ? officialDutyForm.attachment.name : 'คลิกเพื่อเลือกไฟล์ PDF' }}
                                </div>
                            </div>
                        </div>

                        <!-- Selected Employees List -->
                        <div class="bg-slate-50 rounded-2xl p-4">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">รายชื่อที่เลือก</h4>
                            <div class="flex flex-wrap gap-2">
                                <div v-for="id in selectedEmployees" :key="id" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-2">
                                    {{ allEmployees.find(e => e.id === id)?.rank }}{{ allEmployees.find(e => e.id === id)?.name }}
                                    <button @click.stop="toggleSelect(id)" class="text-slate-300 hover:text-rose-500 transition-colors">
                                        <i data-lucide="x" class="w-3 h-3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="showOfficialDutyModal = false"
                                class="flex-1 py-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black rounded-2xl transition-all">ยกเลิก</button>
                            <button type="submit"
                                class="flex-[2] py-4 bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 text-white font-black rounded-2xl shadow-xl shadow-brand-500/20 transition-all hover:-translate-y-1">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

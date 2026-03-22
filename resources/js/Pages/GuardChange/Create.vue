<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick } from 'vue';

const props = defineProps({ users: Array, dutyPositions: Object });

const form = useForm({
    replacement_user_id: '',
    duty_position: '',
    duty_date: '',
    remarks: '',
});

const searchQuery = ref('');
const isOpen = ref(false);
const highlightedIndex = ref(0);
const selectedUser = ref(null);

const filteredUsers = computed(() => {
    const q = searchQuery.value.toLowerCase().trim();
    if (!q) return props.users || [];
    return (props.users || []).filter(u =>
        ((u.rank || '') + (u.name || '')).toLowerCase().includes(q) ||
        (u.position && u.position.toLowerCase().includes(q)) ||
        (u.department && u.department.toLowerCase().includes(q))
    );
});

function selectUser(user) {
    selectedUser.value = user;
    form.replacement_user_id = user.id;
    searchQuery.value = `${user.rank || ''}${user.name}`;
    isOpen.value = false;
}

function clearSelection() {
    selectedUser.value = null;
    form.replacement_user_id = '';
    searchQuery.value = '';
}

function highlightNext() { if (highlightedIndex.value < filteredUsers.value.length - 1) highlightedIndex.value++; }
function highlightPrev() { if (highlightedIndex.value > 0) highlightedIndex.value--; }
function selectHighlighted() { if (filteredUsers.value.length > 0) selectUser(filteredUsers.value[highlightedIndex.value]); }

function getDutyPositionName() {
    if (!form.duty_position || !props.dutyPositions) return null;
    return props.dutyPositions[form.duty_position] || null;
}

function dutyIcon(key) {
    if (key === 'senior_duty_officer') return 'star';
    if (key === 'duty_officer') return 'shield';
    return 'shield-check';
}

function formatDate(dateStr) {
    if (!dateStr) return null;
    return new Date(dateStr).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric' });
}

function submit() {
    form.post('/guard-change');
}

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="ขออนุญาตเปลี่ยนยาม">
        <div class="min-h-screen bg-[#f8fafc] -m-4 md:-m-8 pb-20">
            <!-- Cinematic Header -->
            <div class="relative bg-white pt-16 pb-24 overflow-hidden border-b border-slate-100">
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-500/5 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                </div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <nav class="flex justify-center items-center gap-2 text-emerald-600/60 transition-all mb-4 text-sm font-bold tracking-widest uppercase">
                        <i data-lucide="shield" class="w-4 h-4"></i>
                        <span>การจัดการเวรยาม</span>
                        <span class="w-1 h-1 rounded-full bg-emerald-500/20"></span>
                        <span class="text-emerald-600">ขอเปลี่ยนเวรใหม่</span>
                    </nav>
                    <h1 class="text-3xl md:text-5xl font-bold text-slate-900 tracking-tight mb-4">แบบฟอร์มขออนุญาตเปลี่ยนยาม</h1>
                    <p class="text-slate-500 max-w-2xl mx-auto text-lg font-semibold">กรุณากรอกข้อมูลการเปลี่ยนเวรยามให้ครบถ้วน เพื่อดำเนินการส่งขออนุมัติไปยังผู้บังคับบัญชาตามลำดับ</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- Main Form -->
                    <div class="lg:col-span-8 space-y-8">
                        <form @submit.prevent="submit">

                            <!-- Section 1: Replacement User -->
                            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 relative z-30 group">
                                <div class="absolute inset-0 rounded-[2.5rem] overflow-hidden pointer-events-none">
                                    <div class="absolute top-0 right-0 w-40 h-40 bg-slate-50 rounded-bl-[4rem] -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition-transform duration-500"></div>
                                </div>
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                                            <i data-lucide="user-plus" class="w-6 h-6"></i>
                                        </div>
                                        เลือกผู้ปฏิบัติหน้าที่แทน
                                    </h3>

                                    <div class="relative">
                                        <div class="relative group/input">
                                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-indigo-500 transition-colors">
                                                <i data-lucide="search" class="w-5 h-5"></i>
                                            </div>
                                            <input type="text" v-model="searchQuery" @focus="isOpen = true" @click="isOpen = true"
                                                @input="highlightedIndex = 0; isOpen = true"
                                                @keydown.escape="isOpen = false"
                                                @keydown.arrow-down.prevent="highlightNext()"
                                                @keydown.arrow-up.prevent="highlightPrev()"
                                                @keydown.enter.prevent="selectHighlighted()"
                                                placeholder="ค้นหาชื่อ หรือตำแหน่ง..."
                                                class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all py-5 pl-14 pr-12 text-lg font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-semibold"
                                                autocomplete="off">
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                                <button v-if="selectedUser" type="button" @click="clearSelection()" class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 hover:bg-rose-100 flex items-center justify-center transition-colors">
                                                    <i data-lucide="x" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Selected Badge -->
                                        <div v-if="selectedUser" class="mt-4 flex flex-col sm:flex-row items-center gap-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                                            <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-indigo-200">
                                                {{ selectedUser.name?.charAt(0) }}
                                            </div>
                                            <div class="flex-1 text-center sm:text-left">
                                                <p class="text-base font-bold text-indigo-900">{{ selectedUser.rank }}{{ selectedUser.name }}</p>
                                                <p class="text-sm font-semibold text-indigo-600/60 uppercase tracking-widest mt-0.5">{{ selectedUser.position || 'บุคลากร' }}</p>
                                            </div>
                                            <div class="px-4 py-2 bg-emerald-500 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-emerald-200">
                                                <i data-lucide="check-circle" class="w-3 h-3"></i> พร้อมปฏิบัติแทน
                                            </div>
                                        </div>

                                        <!-- Dropdown -->
                                        <div v-if="isOpen && filteredUsers.length > 0 && !selectedUser" @click.self="isOpen = false"
                                            class="absolute left-0 right-0 mt-3 bg-white rounded-3xl shadow-2xl border border-slate-100 max-h-80 overflow-y-auto z-[100] p-2">
                                            <div v-for="(user, idx) in filteredUsers.slice(0, 15)" :key="user.id" @click="selectUser(user)" @mouseenter="highlightedIndex = idx"
                                                :class="highlightedIndex === idx ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-50'"
                                                class="p-4 cursor-pointer transition-all rounded-2xl flex items-center gap-4 mb-1 last:mb-0">
                                                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400 font-bold text-lg flex-shrink-0">
                                                    {{ user.name?.charAt(0) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-bold text-slate-800 text-base">{{ user.rank }}{{ user.name }}</p>
                                                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-widest mt-0.5">{{ user.position || user.department || 'บุคลากร' }}</p>
                                                </div>
                                                <i data-lucide="chevron-right" class="w-4 h-4 transition-opacity" :class="highlightedIndex === idx ? 'opacity-100' : 'opacity-0'"></i>
                                            </div>
                                        </div>

                                        <!-- No Results -->
                                        <div v-if="isOpen && searchQuery.length > 0 && filteredUsers.length === 0 && !selectedUser"
                                            class="absolute left-0 right-0 mt-3 bg-white rounded-3xl shadow-2xl border border-slate-100 p-10 text-center z-[100]">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i data-lucide="user-x" class="w-8 h-8 text-slate-300"></i>
                                            </div>
                                            <p class="text-slate-500 font-bold text-base">ไม่พบข้อมูลรายชื่อที่ค้นหา</p>
                                            <p class="text-slate-400 text-base mt-1">กรุณาลองพิมพ์ชื่อหรือตำแหน่งใหม่อีกครั้ง</p>
                                        </div>
                                    </div>

                                    <div v-if="form.errors.replacement_user_id" class="mt-4 flex items-center gap-2 text-rose-500 font-bold text-base bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ form.errors.replacement_user_id }}
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Duty Position -->
                            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 relative mt-8 overflow-hidden z-20 group">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-[4rem] -mr-10 -mt-10 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none"></div>
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                                            <i data-lucide="award" class="w-6 h-6"></i>
                                        </div>
                                        ระบุตำแหน่งเวรยาม
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                        <label v-for="(label, key) in dutyPositions" :key="key" class="cursor-pointer group/card relative">
                                            <input type="radio" :value="key" v-model="form.duty_position" class="peer sr-only" required>
                                            <div class="h-full p-6 rounded-3xl bg-slate-50 border-2 border-slate-100 group-hover/card:bg-white group-hover/card:border-emerald-200 group-hover/card:shadow-xl group-hover/card:shadow-emerald-500/5 transition-all duration-300 text-center peer-checked:bg-white peer-checked:border-emerald-500 peer-checked:ring-4 peer-checked:ring-emerald-500/10 peer-checked:scale-[1.05]">
                                                <div class="w-14 h-14 mx-auto rounded-2xl bg-white shadow-sm flex items-center justify-center mb-4 group-hover/card:scale-110 transition-transform duration-300 text-emerald-500 border border-slate-100 group-hover/card:border-emerald-100">
                                                    <i :data-lucide="dutyIcon(key)" class="w-7 h-7"></i>
                                                </div>
                                                <p class="text-base font-bold text-slate-700 tracking-tight leading-tight px-2">{{ label }}</p>
                                                <div class="absolute top-4 right-4 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all scale-0 peer-checked:scale-100 shadow-lg shadow-emerald-500/30">
                                                    <i data-lucide="check" class="w-4 h-4"></i>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div v-if="form.errors.duty_position" class="mt-6 flex items-center gap-2 text-rose-500 font-bold text-base bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ form.errors.duty_position }}
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Date & Remarks -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 group relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-[3rem] -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none"></div>
                                    <div class="relative z-10 h-full flex flex-col">
                                        <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
                                                <i data-lucide="calendar" class="w-6 h-6"></i>
                                            </div>
                                            วันที่เข้าเวร
                                        </h3>
                                        <div class="flex-1 flex flex-col justify-center">
                                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">เลือกวันที่</label>
                                            <input type="date" v-model="form.duty_date" required
                                                class="block w-full bg-slate-50 border-2 border-slate-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 p-6 rounded-3xl text-xl font-bold text-slate-800 transition-all cursor-pointer">
                                        </div>
                                        <div v-if="form.errors.duty_date" class="mt-6 flex items-center gap-2 text-rose-500 font-bold text-base bg-rose-50 p-4 rounded-2xl border border-rose-100">
                                            <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ form.errors.duty_date }}
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-slate-200/50 border border-slate-100 group relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-bl-[3rem] -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform duration-500 pointer-events-none"></div>
                                    <div class="relative z-10 h-full flex flex-col">
                                        <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-sm">
                                                <i data-lucide="message-square" class="w-6 h-6"></i>
                                            </div>
                                            เหตุผล / หมายเหตุ
                                        </h3>
                                        <div class="flex-1 flex flex-col justify-center">
                                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">เหตุผล (ไม่บังคับ)</label>
                                            <textarea v-model="form.remarks" rows="2"
                                                class="block w-full bg-slate-50 border-2 border-slate-100 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 p-6 rounded-3xl text-lg font-bold text-slate-700 placeholder:text-slate-400 placeholder:font-semibold transition-all resize-none"
                                                placeholder="ระบุเหตุผล เช่น ไปราชการ กทม..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="mt-12 flex flex-col items-center">
                                <button type="submit" :disabled="form.processing"
                                    class="w-full sm:w-auto min-w-[300px] flex items-center justify-center gap-4 px-10 py-6 bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xl rounded-full shadow-2xl shadow-indigo-500/20 hover:shadow-indigo-500/40 transition-all duration-300 hover:-translate-y-2 group disabled:opacity-70 disabled:cursor-not-allowed">
                                    <i data-lucide="send" class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                    {{ form.processing ? 'กำลังส่ง...' : 'ยืนยันและส่งคำขอเปลี่ยนยาม' }}
                                </button>
                                <p class="text-slate-400 font-bold text-sm mt-6 flex items-center gap-2">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                    ข้อมูลจะถูกส่งไปยังระบบเพื่อดำเนินการตามขั้นตอนการอนุมัติ
                                </p>
                            </div>
                        </form>
                    </div>

                    <!-- Right Sidebar: Summary -->
                    <div class="lg:col-span-4 lg:sticky lg:top-8">
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative group">
                            <div class="absolute inset-0 opacity-10 pointer-events-none">
                                <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-500 rounded-full blur-[60px] -mr-20 -mt-20"></div>
                                <div class="absolute bottom-0 left-0 w-40 h-40 bg-emerald-500 rounded-full blur-[60px] -ml-20 -mb-20"></div>
                            </div>
                            <div class="relative z-10">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                                    <span class="w-8 h-px bg-slate-200"></span> ตัวอย่างข้อมูล
                                </h4>
                                <div class="space-y-10">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-full">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-slate-600 uppercase tracking-widest">กำลังกรอกข้อมูล</span>
                                    </div>

                                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100">
                                        <div class="flex items-start justify-between mb-10">
                                            <div>
                                                <p class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-1">ตำแหน่งเวรยาม</p>
                                                <p class="text-xl font-bold text-slate-900 leading-tight">{{ getDutyPositionName() || 'โปรดเลือกตำแหน่ง...' }}</p>
                                            </div>
                                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm border border-indigo-100">
                                                <i data-lucide="shield" class="w-6 h-6"></i>
                                            </div>
                                        </div>
                                        <div class="space-y-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-slate-100 shadow-sm">
                                                    <i data-lucide="calendar" class="w-5 h-5 text-indigo-500"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">วันที่เข้าเวร</p>
                                                    <p class="text-sm font-bold text-slate-700 mt-0.5">{{ formatDate(form.duty_date) || 'ยังไม่กำหนด' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center border border-slate-100 shadow-sm">
                                                    <i data-lucide="repeat" class="w-5 h-5 text-emerald-500"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">ผู้ปฏิบัติหน้าที่แทน</p>
                                                    <p class="text-sm font-bold text-slate-700 mt-0.5">{{ searchQuery || 'รอระบุรายชื่อ...' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="my-6 border-t border-dashed border-slate-200"></div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                                <i data-lucide="message-square" class="w-3 h-3"></i> หมายเหตุ
                                            </p>
                                            <p class="text-sm text-slate-600 font-semibold italic break-words">{{ form.remarks || '- ไม่ระบุหมายเหตุ -' }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-brand-50 rounded-2xl p-5 border border-brand-100">
                                        <div class="flex gap-4">
                                            <i data-lucide="shield-alert" class="w-6 h-6 text-brand-600 flex-shrink-0"></i>
                                            <p class="text-sm font-bold text-slate-600 leading-relaxed italic">"กรุณาตรวจสอบข้อมูลและปรึกษาผู้รับหน้าที่แทนก่อนส่งคำขอ เพื่อความถูกต้องในการปฏิบัติหน้าที่เวรยาม"</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

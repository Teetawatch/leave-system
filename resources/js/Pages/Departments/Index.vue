<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { confirmDelete } from '@/utils/swal';

const props = defineProps({ departments: Object });

const addForm = useForm({ name: '' });
const editForm = useForm({ name: '' });
const editingId = ref(null);

function submitAdd() { addForm.post('/departments', { onSuccess: () => addForm.reset() }); }
function startEdit(dept) { editingId.value = dept.id; editForm.name = dept.name; }
function cancelEdit() { editingId.value = null; editForm.reset(); }
function submitEdit(id) { editForm.put(`/departments/${id}`, { onSuccess: () => cancelEdit() }); }
async function deleteDept(id) { const result = await confirmDelete({ title: 'ลบแผนก?', text: 'ข้อมูลแผนกนี้จะถูกลบออกถาวร' }); if (result.isConfirmed) { useForm({}).delete(`/departments/${id}`); } }

const colors = ['indigo', 'emerald', 'amber', 'rose', 'violet', 'cyan', 'orange', 'teal'];
function colorCls(i) {
    const c = colors[i % colors.length];
    return {
        bg: `bg-${c}-50`, text: `text-${c}-600`, border: `border-${c}-100`,
        hoverBg: `group-hover:bg-${c}-600`, hoverText: `group-hover:text-white`,
    };
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="จัดการแผนก">
        <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-100/30 rounded-full blur-[120px] -mr-72 -mt-72"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-100/30 rounded-full blur-[100px] -ml-36 -mb-36"></div>

            <!-- Header -->
            <div class="relative pt-16 pb-32">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                        <div>
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-emerald-100">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                ผังองค์กร
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                                จัดการ <span class="text-emerald-600">แผนก</span>
                            </h1>
                            <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">สร้าง จัดการ และปรับโครงสร้างหน่วยงาน เพื่อความเป็นระเบียบในระบบบริหารจัดการ</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <!-- Create Form Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="glass-panel rounded-[3rem] overflow-hidden sticky top-24">
                            <div class="bg-emerald-600 px-8 py-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 text-white flex items-center justify-center border border-white/10 backdrop-blur-md">
                                    <i data-lucide="plus-circle" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-white text-lg tracking-tight">เพิ่มแผนกใหม่</h3>
                                    <p class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Create Department</p>
                                </div>
                            </div>
                            <form @submit.prevent="submitAdd" class="p-8 space-y-6">
                                <div class="space-y-3">
                                    <label class="block text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">ชื่อแผนก <span class="text-rose-500">*</span></label>
                                    <input v-model="addForm.name" type="text" placeholder="ระบุชื่อแผนก..." required
                                        class="w-full px-6 py-4 rounded-2xl border-slate-100 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 font-bold text-slate-800 text-lg transition-all">
                                    <p v-if="addForm.errors.name" class="text-[10px] font-bold text-rose-500 mt-1 ml-1">{{ addForm.errors.name }}</p>
                                </div>
                                <button type="submit" :disabled="addForm.processing"
                                    class="w-full py-5 bg-slate-900 hover:bg-emerald-600 text-white font-black text-sm rounded-[2rem] shadow-xl transition-all duration-300 hover:-translate-y-1 uppercase tracking-widest flex items-center justify-center gap-3 disabled:opacity-50">
                                    <i data-lucide="save" class="w-5 h-5"></i> บันทึกแผนก
                                </button>
                            </form>
                            <!-- Tip -->
                            <div class="mx-8 mb-8 p-5 bg-slate-900 rounded-2xl text-white relative overflow-hidden">
                                <div class="relative z-10 flex items-start gap-3">
                                    <i data-lucide="lightbulb" class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5"></i>
                                    <div>
                                        <p class="text-[10px] font-black text-white/50 uppercase tracking-widest mb-1">คำแนะนำ</p>
                                        <p class="text-xs text-slate-400 leading-relaxed">แผนกที่สร้างจะถูกนำไปใช้ในการจัดกลุ่มกำลังพลและการออกรายงานอัตโนมัติ</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="lg:col-span-2">
                        <div class="glass-panel rounded-[3rem] overflow-hidden">
                            <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg">
                                        <i data-lucide="building-2" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900 tracking-tight">รายชื่อแผนกทั้งหมด</h3>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Department Directory</p>
                                    </div>
                                </div>
                                <div class="px-5 py-2 rounded-full bg-slate-100 text-slate-600 text-sm font-black border border-slate-200 shadow-sm">
                                    {{ departments?.total || departments?.data?.length || 0 }} <span class="text-slate-400 text-xs">แผนก</span>
                                </div>
                            </div>

                            <div class="divide-y divide-slate-50 p-4">
                                <div v-for="(dept, index) in (departments?.data || [])" :key="dept.id"
                                    class="dept-row flex items-center justify-between p-5 rounded-2xl group">
                                    <!-- Editing -->
                                    <template v-if="editingId === dept.id">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-lg border border-amber-100 shadow-sm">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </div>
                                            <input v-model="editForm.name" type="text" @keyup.enter="submitEdit(dept.id)" @keyup.escape="cancelEdit"
                                                class="flex-1 px-5 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 font-bold text-slate-800 transition-all">
                                        </div>
                                        <div class="flex gap-2 shrink-0 ml-4">
                                            <button @click="submitEdit(dept.id)" class="px-5 py-2.5 rounded-xl bg-emerald-50 text-emerald-600 text-xs font-black hover:bg-emerald-100 transition border border-emerald-100">บันทึก</button>
                                            <button @click="cancelEdit" class="px-5 py-2.5 rounded-xl bg-slate-50 text-slate-500 text-xs font-black hover:bg-slate-100 transition border border-slate-100">ยกเลิก</button>
                                        </div>
                                    </template>
                                    <!-- Display -->
                                    <template v-else>
                                        <div class="flex items-center gap-5">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg shadow-sm transition-all duration-500"
                                                :class="`bg-${colors[index % colors.length]}-50 text-${colors[index % colors.length]}-600 border border-${colors[index % colors.length]}-100`">
                                                {{ dept.name?.charAt(0) }}
                                            </div>
                                            <div>
                                                <span class="font-black text-slate-800 text-lg">{{ dept.name }}</span>
                                                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest mt-0.5">ID: {{ String(dept.id).padStart(3, '0') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                            <button @click="startEdit(dept)" class="w-12 h-12 rounded-2xl bg-white text-slate-400 flex items-center justify-center hover:bg-brand-50 hover:text-brand-600 transition-all border border-slate-100 hover:border-brand-200 shadow-sm">
                                                <i data-lucide="pencil" class="w-5 h-5"></i>
                                            </button>
                                            <button @click="deleteDept(dept.id)" class="w-12 h-12 rounded-2xl bg-white text-slate-400 flex items-center justify-center hover:bg-rose-50 hover:text-rose-600 transition-all border border-slate-100 hover:border-rose-200 shadow-sm">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Empty -->
                            <div v-if="!departments?.data || departments.data.length === 0" class="p-20 text-center">
                                <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner">
                                    <i data-lucide="folder-open" class="w-12 h-12 text-slate-200"></i>
                                </div>
                                <h4 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">ยังไม่มีข้อมูลแผนก</h4>
                                <p class="text-slate-400 font-medium">เริ่มสร้างแผนกแรกของคุณจากแบบฟอร์มด้านซ้าย</p>
                            </div>

                            <!-- Pagination -->
                            <div v-if="departments?.links && departments.links.length > 3" class="px-10 py-6 border-t border-slate-100 bg-slate-50/30 flex justify-center gap-1">
                                <template v-for="link in departments.links" :key="link.label">
                                    <Link v-if="link.url" :href="link.url" class="px-5 py-3 rounded-xl text-sm font-black transition-all"
                                        :class="link.active ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-100'" v-html="link.label" />
                                    <span v-else class="px-4 py-3 text-sm text-slate-300 font-bold" v-html="link.label" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.premium-bg {
    min-height: 100vh;
    background: radial-gradient(circle at 10% 20%, rgba(16, 185, 129, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(79, 70, 229, 0.03) 0%, transparent 40%);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
}
.dept-row { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
.dept-row:hover { background: rgba(248, 250, 252, 0.8); transform: translateX(6px); }
</style>

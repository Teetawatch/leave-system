<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({ employee: Object, supervisors: Array, departments: Array, currentVacationQuota: Number });

const form = useForm({
    name: props.employee.name || '', email: props.employee.email || '', password: '',
    department: props.employee.department || '', position: props.employee.position || '',
    rank: props.employee.rank || '', role: props.employee.role || 'staff',
    supervisor_id: props.employee.supervisor_id || '', deputy_id: props.employee.deputy_id || '',
    manager_id: props.employee.manager_id || '', start_date: props.employee.start_date || '',
    vacation_leave_days: props.currentVacationQuota || 10,
});

function submit() { form.put(`/employees/${props.employee.id}`); }

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="แก้ไขข้อมูลข้าราชการ">
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <Link href="/employees" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-600 font-bold mb-4 transition-colors"><i data-lucide="arrow-left" class="w-4 h-4"></i> กลับหน้ารายชื่อ</Link>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100">
                        <i data-lucide="user-pen" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">แก้ไขข้อมูล</h2>
                        <p class="text-sm text-slate-400 font-medium mt-0.5">{{ employee.rank }} {{ employee.name }}</p>
                    </div>
                </div>
            </div>
            <form @submit.prevent="submit" class="glass-card rounded-[2rem] shadow-xl overflow-hidden">
                <div class="p-8 space-y-8">
                    <!-- Personal -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100"><i data-lucide="user" class="w-4 h-4"></i></div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">ข้อมูลส่วนบุคคล</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ยศ</label><input v-model="form.rank" type="text" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" required></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ชื่อ-สกุล</label><input v-model="form.name" type="text" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" required></div>
                        </div>
                    </div>
                    <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                    <!-- Account -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100"><i data-lucide="key-round" class="w-4 h-4"></i></div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">บัญชีเข้าสู่ระบบ</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">อีเมล</label><input v-model="form.email" type="email" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">รหัสผ่านใหม่ <span class="text-slate-300">(เว้นว่างถ้าไม่เปลี่ยน)</span></label><input v-model="form.password" type="password" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"></div>
                        </div>
                    </div>
                    <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                    <!-- Organization -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100"><i data-lucide="building-2" class="w-4 h-4"></i></div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">สังกัดและตำแหน่ง</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">แผนก</label><select v-model="form.department" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"><option value="">-- เลือก --</option><option v-for="d in departments" :key="d.id" :value="d.name">{{ d.name }}</option></select></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ตำแหน่ง</label><input v-model="form.position" type="text" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-5">
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">บทบาท</label>
                                <select v-model="form.role" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" required>
                                    <option value="staff">เจ้าหน้าที่</option><option value="supervisor">หัวหน้างาน</option><option value="department_head">หัวหน้าแผนก</option><option value="deputy_director">รอง ผอ.</option><option value="director">ผอ.</option><option value="admin">ผู้ดูแล</option>
                                </select></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">วันเริ่มงาน</label><input v-model="form.start_date" type="date" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"></div>
                        </div>
                    </div>
                    <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                    <!-- Supervisors -->
                    <div>
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center border border-violet-100"><i data-lucide="users" class="w-4 h-4"></i></div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">สายบังคับบัญชา</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">หัวหน้างาน</label><select v-model="form.supervisor_id" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"><option value="">-- ไม่ระบุ --</option><option v-for="s in supervisors" :key="s.id" :value="s.id">{{ s.rank }} {{ s.name }}</option></select></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">รอง ผอ.</label><select v-model="form.deputy_id" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"><option value="">-- ไม่ระบุ --</option><option v-for="s in supervisors" :key="s.id" :value="s.id">{{ s.rank }} {{ s.name }}</option></select></div>
                            <div class="space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ผู้จัดการ</label><select v-model="form.manager_id" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"><option value="">-- ไม่ระบุ --</option><option v-for="s in supervisors" :key="s.id" :value="s.id">{{ s.rank }} {{ s.name }}</option></select></div>
                        </div>
                        <div class="mt-5 space-y-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">สิทธิ์ลาพักร้อน (วัน/ปี)</label><input v-model="form.vacation_leave_days" type="number" min="0" class="w-full sm:w-48 px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all"></div>
                    </div>
                </div>
                <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-3">
                    <Link href="/employees" class="px-8 py-4 rounded-2xl border border-slate-200 text-slate-600 font-black text-xs uppercase tracking-widest hover:bg-slate-100 transition-all">ยกเลิก</Link>
                    <button type="submit" :disabled="form.processing" class="px-10 py-4 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-amber-500/20 transition-all hover:-translate-y-1 disabled:opacity-50">{{ form.processing ? 'กำลังบันทึก...' : 'บันทึกการแก้ไข' }}</button>
                </div>
            </form>
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

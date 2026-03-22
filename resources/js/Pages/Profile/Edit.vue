<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({ user: Object, status: String });

const profileForm = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    rank: props.user.rank || '',
    phone: props.user.phone || '',
    avatar: null,
    signature: null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const avatarPreview = ref(props.user.avatar ? `/storage/${props.user.avatar}` : null);
const signaturePreview = ref(props.user.signature ? `/storage/${props.user.signature}` : null);

function handleAvatar(e) {
    const file = e.target.files[0];
    if (file) { profileForm.avatar = file; avatarPreview.value = URL.createObjectURL(file); }
}
function handleSignature(e) {
    const file = e.target.files[0];
    if (file) { profileForm.signature = file; signaturePreview.value = URL.createObjectURL(file); }
}

function submitProfile() {
    profileForm.post('/profile', { method: 'patch', forceFormData: true, preserveScroll: true });
}
function submitPassword() {
    passwordForm.put('/password', { preserveScroll: true, onSuccess: () => passwordForm.reset() });
}

function roleLabel(role) {
    const map = { admin: 'ผู้ดูแลระบบ', director: 'ผอ.รพธ.พธ.ทร.', deputy_director: 'รอง ผอ.', department_head: 'หน.แผนก' };
    return map[role] || 'กำลังพล';
}

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="จัดการบัญชีและข้อมูลส่วนตัว">
        <div class="min-h-screen relative pb-20 bg-slate-50/30 -m-4 md:-m-8">
            <!-- Background Orbs -->
            <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-200/40 rounded-full blur-3xl mix-blend-multiply"></div>
                <div class="absolute top-[20%] right-[-10%] w-[30%] h-[50%] bg-rose-200/30 rounded-full blur-3xl mix-blend-multiply"></div>
                <div class="absolute bottom-[-10%] left-[20%] w-[40%] h-[40%] bg-amber-200/30 rounded-full blur-3xl mix-blend-multiply"></div>
            </div>

            <!-- Header -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-8">
                <div class="flex items-center gap-4 mb-2">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 flex-shrink-0 group hover:rotate-6 transition-transform">
                        <i data-lucide="user-cog" class="w-6 h-6 text-indigo-600"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">ตั้งค่าบัญชี</h1>
                        <p class="text-sm text-slate-500 font-medium mt-1">จัดการตัวตนดิจิทัลของคุณและรักษาความปลอดภัยของบัญชีผู้ใช้งาน</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <!-- Status Alert -->
                <div v-if="status" class="bg-white/80 backdrop-blur-md border-l-4 border-emerald-500 p-4 rounded-xl shadow-lg border border-slate-100 flex items-start gap-4">
                    <i data-lucide="check-circle" class="w-6 h-6 text-emerald-500 flex-shrink-0"></i>
                    <div>
                        <h3 class="text-sm font-bold text-emerald-800 tracking-tight">ดำเนินการเรียบร้อย</h3>
                        <p class="text-xs font-semibold text-emerald-600 mt-1">{{ status === 'profile-updated' ? 'อัปเดตโปรไฟล์เรียบร้อยแล้ว' : status }}</p>
                    </div>
                </div>

                <!-- Quick Identity Summary Card -->
                <div class="glass-card rounded-[2.5rem] shadow-xl shadow-indigo-900/5 p-6 sm:p-10 flex flex-col md:flex-row items-center gap-8 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/60 rounded-bl-full -mr-20 -mt-20 opacity-50 group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="relative flex-shrink-0">
                        <div class="w-28 h-28 rounded-[2rem] bg-slate-50 border-4 border-white shadow-lg overflow-hidden group-hover:-rotate-3 group-hover:scale-105 transition-transform duration-500">
                            <img v-if="avatarPreview" :src="avatarPreview" class="w-full h-full object-cover">
                            <div v-else class="w-full h-full flex items-center justify-center text-3xl font-black text-slate-300 uppercase bg-slate-100">{{ user.name?.charAt(0) }}</div>
                        </div>
                    </div>
                    <div class="flex-1 text-center md:text-left space-y-2 relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ user.rank }}{{ user.name }}</h2>
                            <span class="inline-block px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 text-[10px] font-bold uppercase tracking-widest shadow-sm">{{ roleLabel(user.role) }}</span>
                        </div>
                        <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">{{ user.department || 'สังกัดหน่วยงาน' }} / {{ user.position || '-' }}</p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white/80 backdrop-blur rounded-xl text-xs font-semibold text-slate-600 border border-slate-200/60 shadow-sm">
                                <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i> {{ user.email }}
                            </div>
                            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 rounded-xl text-xs font-semibold text-emerald-600 border border-emerald-100 shadow-sm">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> สถานะยืนยันแล้ว
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-8 space-y-8">
                        <!-- Profile Form -->
                        <div class="glass-card rounded-[2rem] shadow-xl shadow-indigo-900/5 overflow-hidden group/card hover:shadow-2xl transition-all duration-300">
                            <div class="px-6 sm:px-8 py-5 border-b border-slate-100/50 flex items-center gap-4 bg-white/40">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm group-hover/card:scale-110 group-hover/card:rotate-3 transition-transform">
                                    <i data-lucide="contact" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-800 text-lg tracking-tight">ข้อมูลส่วนตัวและระบบ</h3>
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">ส่วนสำหรับปรับปรุงข้อมูลการติดต่อและภาพประจำตัว</p>
                                </div>
                            </div>
                            <form @submit.prevent="submitProfile" class="p-6 sm:p-8 space-y-6">
                                <!-- Avatar Upload -->
                                <div class="flex items-center gap-6">
                                    <div class="relative group/avatar">
                                        <div class="w-20 h-20 rounded-[1.5rem] bg-slate-50 border-4 border-white shadow-lg overflow-hidden">
                                            <img v-if="avatarPreview" :src="avatarPreview" class="w-full h-full object-cover">
                                            <div v-else class="w-full h-full flex items-center justify-center text-2xl font-black text-slate-300 bg-slate-100">{{ user.name?.charAt(0) }}</div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-50 text-indigo-600 rounded-2xl cursor-pointer font-bold text-sm hover:bg-indigo-100 transition border border-indigo-100">
                                            <i data-lucide="camera" class="w-4 h-4"></i> เปลี่ยนรูปโปรไฟล์
                                            <input type="file" @change="handleAvatar" accept="image/*" class="hidden">
                                        </label>
                                        <p class="text-[10px] font-bold text-slate-400">JPG, PNG ขนาดไม่เกิน 2 MB</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ยศ</label>
                                        <input v-model="profileForm.rank" type="text" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ชื่อ-สกุล</label>
                                        <input v-model="profileForm.name" type="text" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                        <p v-if="profileForm.errors.name" class="text-rose-500 text-xs font-bold">{{ profileForm.errors.name }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">อีเมล</label>
                                    <input v-model="profileForm.email" type="email" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                    <p v-if="profileForm.errors.email" class="text-rose-500 text-xs font-bold">{{ profileForm.errors.email }}</p>
                                </div>

                                <!-- Signature -->
                                <div class="space-y-3">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ลายเซ็นอิเล็กทรอนิกส์</label>
                                    <div class="flex items-center gap-4">
                                        <div v-if="signaturePreview" class="w-40 h-20 rounded-2xl border-2 border-dashed border-slate-200 overflow-hidden bg-white flex items-center justify-center">
                                            <img :src="signaturePreview" class="max-w-full max-h-full object-contain p-2">
                                        </div>
                                        <label class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-50 text-slate-600 rounded-2xl cursor-pointer font-bold text-sm hover:bg-slate-100 transition border border-slate-200">
                                            <i data-lucide="pen-tool" class="w-4 h-4"></i> อัปโหลดลายเซ็น
                                            <input type="file" @change="handleSignature" accept="image/*" class="hidden">
                                        </label>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-4 border-t border-slate-100">
                                    <button type="submit" :disabled="profileForm.processing"
                                        class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl shadow-indigo-500/20 transition-all hover:-translate-y-1 disabled:opacity-60">
                                        {{ profileForm.processing ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-4 space-y-8">
                        <!-- Password Form -->
                        <div class="glass-card rounded-[2rem] shadow-xl shadow-indigo-900/5 overflow-hidden group/card hover:shadow-2xl transition-all duration-300">
                            <div class="px-6 py-5 border-b border-slate-100/50 bg-white/40 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center shadow-sm group-hover/card:scale-110 group-hover/card:rotate-3 transition-transform">
                                    <i data-lucide="key-round" class="w-4 h-4"></i>
                                </div>
                                <h3 class="font-extrabold text-slate-800 text-sm tracking-tight">ความปลอดภัยรหัสผ่าน</h3>
                            </div>
                            <form @submit.prevent="submitPassword" class="p-6 sm:p-8 space-y-5">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">รหัสผ่านปัจจุบัน</label>
                                    <input v-model="passwordForm.current_password" type="password" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                                    <p v-if="passwordForm.errors.current_password" class="text-rose-500 text-xs font-bold">{{ passwordForm.errors.current_password }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">รหัสผ่านใหม่</label>
                                    <input v-model="passwordForm.password" type="password" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                                    <p v-if="passwordForm.errors.password" class="text-rose-500 text-xs font-bold">{{ passwordForm.errors.password }}</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ยืนยันรหัสผ่านใหม่</label>
                                    <input v-model="passwordForm.password_confirmation" type="password" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                                </div>
                                <button type="submit" :disabled="passwordForm.processing"
                                    class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-black uppercase tracking-widest text-xs rounded-2xl shadow-xl transition-all hover:-translate-y-1 disabled:opacity-60">
                                    {{ passwordForm.processing ? 'กำลังเปลี่ยน...' : 'เปลี่ยนรหัสผ่าน' }}
                                </button>
                            </form>
                        </div>

                        <!-- Tip Card -->
                        <div class="bg-slate-800 rounded-[2rem] border border-slate-700 p-6 sm:p-8 text-white relative overflow-hidden group shadow-2xl shadow-slate-900/50">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/20 rounded-bl-full -mr-10 -mt-10 blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="relative z-10 space-y-5">
                                <div class="w-12 h-12 rounded-[1rem] bg-white/10 flex items-center justify-center text-amber-400 border border-white/10 backdrop-blur-md shadow-inner">
                                    <i data-lucide="lightbulb" class="w-6 h-6"></i>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="font-extrabold text-base tracking-tight text-white">ข้อแนะนำด้านไฟล์ภาพ</h4>
                                    <p class="text-[12px] font-medium text-slate-300 leading-relaxed">ลายเซ็นควรเป็นไฟล์ PNG แบบไม่มีพื้นหลัง เพื่อนำไปประทับในใบลาอิเล็กทรอนิกส์</p>
                                </div>
                                <div class="p-4 bg-black/20 border border-white/5 rounded-2xl flex flex-col gap-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">รองรับไฟล์</span>
                                        <span class="text-[11px] font-bold text-slate-200 px-2 py-0.5 bg-white/10 rounded-md">JPG, PNG</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">ขนาดสูงสุด</span>
                                        <span class="text-[11px] font-bold text-slate-200 px-2 py-0.5 bg-white/10 rounded-md">2 MB</span>
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

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.8);
}
</style>

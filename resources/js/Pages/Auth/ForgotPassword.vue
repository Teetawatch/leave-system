<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({ status: String });

const form = useForm({ email: '' });

function submit() {
    form.post('/forgot-password');
}
</script>

<template>
    <GuestLayout>
        <Head title="ลืมรหัสผ่าน" />
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50 p-4">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-brand-500 to-indigo-500"></div>
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <img src="/images/logonavy.png" alt="Logo" class="w-16 h-16 mx-auto mb-4">
                            <h1 class="text-2xl font-bold text-slate-900">ลืมรหัสผ่าน</h1>
                            <p class="text-sm text-slate-500 mt-2">กรอกอีเมลของคุณเพื่อรับลิงก์รีเซ็ตรหัสผ่าน</p>
                        </div>
                        <div v-if="status" class="mb-4 p-3 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-100">{{ status }}</div>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">อีเมล</label>
                                <input v-model="form.email" type="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required autofocus>
                                <p v-if="form.errors.email" class="text-rose-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <button type="submit" :disabled="form.processing" class="w-full py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-xl transition-all disabled:opacity-60">
                                {{ form.processing ? 'กำลังส่ง...' : 'ส่งลิงก์รีเซ็ตรหัสผ่าน' }}
                            </button>
                        </form>
                        <p class="text-center text-sm text-slate-500 mt-6">
                            <Link href="/login" class="text-brand-600 font-bold hover:underline">กลับไปหน้าเข้าสู่ระบบ</Link>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

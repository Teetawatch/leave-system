<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({ password: '' });

function submit() {
    form.post('/confirm-password', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="ยืนยันรหัสผ่าน" />
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50 p-4">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-brand-500 to-indigo-500"></div>
                    <div class="p-8">
                        <h1 class="text-2xl font-bold text-slate-900 text-center mb-4">ยืนยันรหัสผ่าน</h1>
                        <p class="text-sm text-slate-500 mb-6 text-center">กรุณายืนยันรหัสผ่านก่อนดำเนินการต่อ</p>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">รหัสผ่าน</label>
                                <input v-model="form.password" type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required autofocus>
                                <p v-if="form.errors.password" class="text-rose-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <button type="submit" :disabled="form.processing" class="w-full py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all disabled:opacity-60">
                                {{ form.processing ? 'กำลังตรวจสอบ...' : 'ยืนยัน' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

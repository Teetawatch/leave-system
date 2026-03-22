<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({ email: String, token: String });

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/reset-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="รีเซ็ตรหัสผ่าน" />
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50 p-4">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-brand-500 to-indigo-500"></div>
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <h1 class="text-2xl font-bold text-slate-900">รีเซ็ตรหัสผ่าน</h1>
                        </div>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">อีเมล</label>
                                <input v-model="form.email" type="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required autofocus>
                                <p v-if="form.errors.email" class="text-rose-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">รหัสผ่านใหม่</label>
                                <input v-model="form.password" type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required>
                                <p v-if="form.errors.password" class="text-rose-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">ยืนยันรหัสผ่านใหม่</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required>
                            </div>
                            <button type="submit" :disabled="form.processing" class="w-full py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-xl transition-all disabled:opacity-60">
                                {{ form.processing ? 'กำลังรีเซ็ต...' : 'รีเซ็ตรหัสผ่าน' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref } from 'vue';

const form = useForm({
    employee_id: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const searchQuery = ref('');
const searchResults = ref([]);
const selectedEmployee = ref(null);
const searching = ref(false);

async function searchEmployees() {
    if (searchQuery.value.length < 2) { searchResults.value = []; return; }
    searching.value = true;
    try {
        const res = await fetch(`/api/employees/search?q=${encodeURIComponent(searchQuery.value)}`);
        searchResults.value = await res.json();
    } catch (e) { searchResults.value = []; }
    searching.value = false;
}

function selectEmployee(emp) {
    selectedEmployee.value = emp;
    form.employee_id = emp.id;
    searchQuery.value = emp.display;
    searchResults.value = [];
}

function submit() {
    form.post('/employee-register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="ลงทะเบียนข้าราชการ" />
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50 p-4">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="h-1 bg-gradient-to-r from-brand-500 to-indigo-500"></div>
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <img src="/images/logonavy.png" alt="Logo" class="w-16 h-16 mx-auto mb-4">
                            <h1 class="text-2xl font-bold text-slate-900">ลงทะเบียนข้าราชการ</h1>
                            <p class="text-sm text-slate-500 mt-1">ค้นหาชื่อของคุณแล้วตั้งอีเมลและรหัสผ่าน</p>
                        </div>
                        <form @submit.prevent="submit" class="space-y-5">
                            <div class="relative">
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">ค้นหาชื่อ</label>
                                <input v-model="searchQuery" @input="searchEmployees" type="text" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" placeholder="พิมพ์ชื่อเพื่อค้นหา..." required>
                                <div v-if="searchResults.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg max-h-48 overflow-y-auto">
                                    <button v-for="emp in searchResults" :key="emp.id" type="button" @click="selectEmployee(emp)" class="w-full text-left px-4 py-3 hover:bg-slate-50 text-sm border-b border-slate-50 last:border-0">
                                        <span class="font-bold text-slate-800">{{ emp.rank }} {{ emp.name }}</span>
                                        <span v-if="emp.department" class="text-slate-400 ml-2">({{ emp.department }})</span>
                                    </button>
                                </div>
                                <p v-if="form.errors.employee_id" class="text-rose-500 text-xs mt-1">{{ form.errors.employee_id }}</p>
                            </div>
                            <div v-if="selectedEmployee" class="p-3 bg-brand-50 rounded-xl border border-brand-100 text-sm">
                                <p class="font-bold text-brand-800">{{ selectedEmployee.rank }} {{ selectedEmployee.name }}</p>
                                <p class="text-brand-600">{{ selectedEmployee.department }} • {{ selectedEmployee.position }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">อีเมล</label>
                                <input v-model="form.email" type="email" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required>
                                <p v-if="form.errors.email" class="text-rose-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">รหัสผ่าน</label>
                                <input v-model="form.password" type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required>
                                <p v-if="form.errors.password" class="text-rose-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">ยืนยันรหัสผ่าน</label>
                                <input v-model="form.password_confirmation" type="password" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition" required>
                            </div>
                            <button type="submit" :disabled="form.processing || !form.employee_id" class="w-full py-3 bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all disabled:opacity-60">
                                {{ form.processing ? 'กำลังลงทะเบียน...' : 'ลงทะเบียน' }}
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

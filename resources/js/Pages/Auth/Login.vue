<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { ref } from 'vue';

defineProps({
    canResetPassword: { type: Boolean, default: true },
    status: { type: String, default: '' },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <GuestLayout>
        <Head title="เข้าสู่ระบบ" />

        <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-slate-50">
            <!-- Animated Dynamic Background (Light Theme) -->
            <div class="absolute inset-0 w-full h-full pointer-events-none overflow-hidden">
                <!-- Soft glowing orbs -->
                <div class="absolute w-[600px] h-[600px] rounded-full bg-blue-400/20 blur-[100px] -top-32 -left-32 animate-blob mix-blend-multiply"></div>
                <div class="absolute w-[600px] h-[600px] rounded-full bg-cyan-300/20 blur-[120px] top-1/4 right-0 animate-blob animation-delay-2000 mix-blend-multiply"></div>
                <div class="absolute w-[500px] h-[500px] rounded-full bg-purple-300/20 blur-[100px] bottom-0 left-1/3 animate-blob animation-delay-4000 mix-blend-multiply"></div>
                
                <!-- Grid pattern overlay -->
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAwIDEwIEwgNDAgMTAgTSAxMCAwIEwgMTAgNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0icmdiYSgwLCAwLCAwLCAwLjAzKSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyaWQpIi8+PC9zdmc+')] z-0"></div>
            </div>

            <div class="container mx-auto px-4 py-8 z-10 flex justify-center items-center">
                <!-- Main Glass Card - Split Layout -->
                <div class="w-full max-w-5xl flex flex-col md:flex-row rounded-[2rem] overflow-hidden shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] border border-white/60 bg-white/40 backdrop-blur-xl transition-all duration-500 hover:shadow-[0_25px_65px_-15px_rgba(0,0,0,0.15)] ring-1 ring-slate-900/5 min-h-[600px]">
                    
                    <!-- Left Brand Side (Bright Premium Color) -->
                    <div class="w-full md:w-[45%] p-10 md:p-12 hidden md:flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-500 to-cyan-500 text-white">
                        <!-- Decorative shapes -->
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full border border-white/20"></div>
                        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-white/5 backdrop-blur-3xl border border-white/20"></div>
                        
                        <div class="relative z-10">
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold tracking-wider mb-8 uppercase shadow-lg">
                                <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse shadow-[0_0_8px_#86efac]"></span>
                               HRMIS
                            </div>
                            
                            <h2 class="text-4xl font-extrabold text-white leading-[1.3] mb-5 drop-shadow-sm">
ระบบบริหารจัดการงานกำลังพล<br/>
                            </h2>
                            <p class="text-white/80 mb-8 max-w-sm text-sm leading-relaxed font-light">
                                โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ
                            </p>
                        </div>

                        <div class="relative z-10 flex flex-col gap-6">
                            <!-- Feature Items -->
                            <div class="flex items-center gap-4 group cursor-default p-3 rounded-2xl hover:bg-white/10 transition-colors">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center border border-white/20 group-hover:-translate-y-1 transition-transform duration-300 shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold text-sm">รวดเร็ว และ ทันสมัย</h4>
                                    <p class="text-white/70 text-xs mt-0.5">ดำเนินการอนุมัติได้ทันทีแบบเรียลไทม์</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 group cursor-default p-3 rounded-2xl hover:bg-white/10 transition-colors">
                                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center border border-white/20 group-hover:-translate-y-1 transition-transform duration-300 shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold text-sm">ปลอดภัย แม่นยำ</h4>
                                    <p class="text-white/70 text-xs mt-0.5">ระบบมีความเสถียร จัดเก็บข้อมูลปลอดภัย</p>
                                </div>
                            </div>
                        </div>

                        <!-- Brand Logo Bottom -->
                        <div class="relative z-10 mt-12 pt-8 border-t border-white/20 flex justify-between items-end">
                            <img src="/images/logonavy.png" alt="Logo" class="w-16 h-16 drop-shadow-md">
                        </div>
                    </div>

                    <!-- Right Login Form Side (Pure Bright Clean) -->
                    <div class="w-full md:w-[55%] p-8 md:p-14 bg-white/95 relative flex flex-col justify-center">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-blue-50 to-transparent rounded-bl-full pointer-events-none opacity-60"></div>
                        
                        <!-- Mobile Logo -->
                        <div class="flex md:hidden justify-center mb-8">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-blue-100 rounded-full blur-xl animate-pulse"></div>
                                <img src="/images/logonavy.png" alt="Logo" class="w-20 h-20 drop-shadow-sm relative z-10">
                            </div>
                        </div>

                        <div class="mb-10 text-center md:text-left">
                            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 mb-3 tracking-tight">เข้าสู่ระบบ</h1>
                            <p class="text-slate-500 text-sm font-medium">โปรดกรอกข้อมูลเข้าสู่ระบบเพื่อใช้งาน</p>
                        </div>

                        <!-- Status Message -->
                        <div v-if="status" class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center gap-3 text-sm font-medium shadow-sm animate-fade-in">
                            <div class="bg-emerald-100 p-2 rounded-full">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Email -->
                            <div class="space-y-2.5">
                                <label for="email" class="text-sm font-bold text-slate-700">อีเมลแอดเดรส</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center group-focus-within:bg-blue-50 transition-colors">
                                            <svg class="h-4 w-4 text-slate-400 group-focus-within:text-blue-600 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                        </div>
                                    </div>
                                    <input 
                                        id="email" 
                                        type="email" 
                                        v-model="form.email" 
                                        class="w-full pl-14 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm font-medium focus:ring-[3px] focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition-all outline-none shadow-sm hover:border-slate-300" 
                                        :class="{ 'border-red-400 bg-red-50 focus:ring-red-100 focus:border-red-500 hover:border-red-400': form.errors.email }"
                                        placeholder="user@example.com" 
                                        required 
                                        autofocus 
                                        autocomplete="username"
                                    >
                                </div>
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5 flex items-center gap-1.5 font-bold animate-fade-in">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Password -->
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <label for="password" class="text-sm font-bold text-slate-700">รหัสผ่าน</label>
                                    <Link v-if="canResetPassword" href="/forgot-password" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                        ลืมรหัสผ่านใช่หรือไม่?
                                    </Link>
                                </div>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center group-focus-within:bg-blue-50 transition-colors">
                                            <svg class="h-4 w-4 text-slate-400 group-focus-within:text-blue-600 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </div>
                                    </div>
                                    <input 
                                        id="password" 
                                        :type="showPassword ? 'text' : 'password'" 
                                        v-model="form.password" 
                                        class="w-full pl-14 pr-12 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm font-medium focus:ring-[3px] focus:ring-blue-100 focus:border-blue-500 focus:bg-white transition-all outline-none shadow-sm hover:border-slate-300" 
                                        :class="{ 'border-red-400 bg-red-50 focus:ring-red-100 focus:border-red-500 hover:border-red-400': form.errors.password }"
                                        placeholder="············" 
                                        required 
                                        autocomplete="current-password"
                                    >
                                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors focus:outline-none" @click="showPassword = !showPassword">
                                        <svg v-if="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                    </button>
                                </div>
                                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5 flex items-center gap-1.5 font-bold animate-fade-in">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <!-- Remember Me & Submit -->
                            <div class="pt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                                <label class="flex items-center gap-3 cursor-pointer group w-fit">
                                    <div class="relative flex items-center justify-center">
                                        <input type="checkbox" v-model="form.remember" class="peer sr-only">
                                        <div class="w-5 h-5 border-2 border-slate-300 bg-slate-50 rounded-md peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all duration-200 peer-focus:ring-[3px] peer-focus:ring-blue-100 group-hover:border-blue-400 shadow-sm"></div>
                                        <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900 transition-colors">จดจำการเข้าสู่ระบบ</span>
                                </label>

                                <button type="submit" :disabled="form.processing" class="relative overflow-hidden rounded-xl bg-slate-800 text-white font-bold py-3.5 px-8 transition-all duration-300 hover:bg-blue-600 hover:shadow-[0_8px_25px_rgba(37,99,235,0.3)] disabled:opacity-70 disabled:cursor-not-allowed sm:w-auto w-full group">
                                    <div class="relative flex items-center justify-center gap-2">
                                        <svg v-if="form.processing" class="animate-spin -ml-1 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <template v-else>
                                            <span class="tracking-wide">เข้าสู่ระบบ</span>
                                            <svg class="w-5 h-5 group-hover:translate-x-1.5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </template>
                                    </div>
                                </button>
                            </div>
                        </form>

                        <!-- Footer Links -->
                        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                            <p class="text-sm font-semibold text-slate-500">
                                ยังไม่มีบัญชีผู้ใช้งานใช่ไหม? 
                                <Link href="/employee-register" class="font-bold text-blue-600 hover:text-blue-800 transition-colors inline-flex items-center gap-1 group ml-1">
                                    ลงทะเบียนข้าราชการ
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </Link>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer Text Outside -->
                <div class="absolute bottom-6 left-0 right-0 text-center z-10 pointer-events-none hidden md:block">
                    <p class="text-xs font-semibold text-slate-400 tracking-wider">
                        © {{ new Date().getFullYear() }} ระบบบริหารจัดการกำลังพล ออกแบบและพัฒนาระบบโดย จ.ท.ธีร์ธวัช พิพัฒน์เดชธน
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap');

* {
    font-family: 'Sarabun', sans-serif;
}

@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
    animation: blob 10s infinite alternate cubic-bezier(0.4, 0, 0.2, 1);
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Autofill styling fix for clean light mode UX */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px #f8fafc inset !important;
    -webkit-text-fill-color: #1e293b !important;
    transition: background-color 5000s ease-in-out 0s;
}
</style>

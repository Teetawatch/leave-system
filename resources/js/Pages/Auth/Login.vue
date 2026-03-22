<script setup>
import { useForm } from '@inertiajs/vue3';
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

        <div class="login-container">
            <!-- Background decoration -->
            <div class="bg-decoration">
                <div class="bg-circle circle-1"></div>
                <div class="bg-circle circle-2"></div>
                <div class="bg-circle circle-3"></div>
            </div>

            <!-- Left panel (branding) -->
            <div class="left-panel">
                <div class="left-panel-inner">
                    <div class="brand-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        HRMIS
                    </div>
                    <div class="brand-logo">
                        <img src="/images/logonavy.png" alt="Logo" class="brand-logo-img">
                    </div>
                    <h2 class="brand-title">ระบบบริหารจัดการ<br>งานธุรการด้านกำลังพล</h2>
                    <p class="brand-subtitle">โรงเรียนพลาธิการ กรมพลาธิการทหารเรือ</p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            </div>
                            <span>จัดการการลาออนไลน์</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <span>บริหารกำลังพล</span>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </div>
                            <span>รายงานและสถิติ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right panel (form) -->
            <div class="right-panel">
                <div class="login-card" :class="{ 'card-loading': form.processing }">
                    <div class="card-header">
                        <div class="card-logo-mobile">
                            <img src="/images/logonavy.png" alt="Logo" class="card-logo-img">
                        </div>
                        <h1 class="card-title">เข้าสู่ระบบ</h1>
                        <p class="card-subtitle">กรุณากรอกข้อมูลเพื่อเข้าใช้งาน</p>
                    </div>

                    <!-- Status -->
                    <div v-if="status" class="session-status">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        {{ status }}
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="login-form">
                        <!-- Email -->
                        <div class="form-field">
                            <label for="email" class="field-label">อีเมล</label>
                            <div class="input-box" :class="{ 'input-error': form.errors.email, 'input-focused': false }">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    class="field-input"
                                    placeholder="กรอกอีเมลของคุณ"
                                    required
                                    autofocus
                                    autocomplete="username"
                                >
                            </div>
                            <p v-if="form.errors.email" class="error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div class="form-field">
                            <div class="field-label-row">
                                <label for="password" class="field-label">รหัสผ่าน</label>
                                <Link v-if="canResetPassword" href="/forgot-password" class="forgot-link">ลืมรหัสผ่าน?</Link>
                            </div>
                            <div class="input-box" :class="{ 'input-error': form.errors.password }">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    class="field-input"
                                    placeholder="กรอกรหัสผ่าน"
                                    required
                                    autocomplete="current-password"
                                >
                                <button type="button" class="eye-btn" @click="showPassword = !showPassword" :title="showPassword ? 'ซ่อนรหัสผ่าน' : 'แสดงรหัสผ่าน'">
                                    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="error-msg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember me -->
                        <label class="remember-row">
                            <div class="custom-check">
                                <input type="checkbox" v-model="form.remember" class="check-input">
                                <span class="check-box">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                            </div>
                            <span class="remember-text">จดจำการเข้าสู่ระบบ</span>
                        </label>

                        <!-- Submit button -->
                        <button type="submit" class="submit-btn" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner"></span>
                            <span v-else class="btn-icon-left">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                            </span>
                            {{ form.processing ? 'กำลังเข้าสู่ระบบ...' : 'เข้าสู่ระบบ' }}
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="card-footer">
                        <span class="footer-text">ยังไม่มีบัญชี?</span>
                        <Link href="/employee-register" class="register-link">
                            ลงทะเบียนข้าราชการใหม่
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </Link>
                    </div>
                </div>

                <p class="copyright">
                    © {{ new Date().getFullYear() }} ระบบบริหารจัดการงานธุรการด้านกำลังพล ·
                    <span>ออกแบบโดย จ.ท.ธีร์ธวัช พิพัฒน์เดชธน</span>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* ── Variables ─────────────────────────────────── */
:root {
    --navy: #1e3a5f;
    --navy-mid: #2d5282;
    --blue: #1a6db5;
    --blue-light: #3b82f6;
    --blue-pale: #dbeafe;
    --sky: #0ea5e9;
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-700: #334155;
    --gray-900: #0f172a;
    --error: #dc2626;
    --error-bg: #fef2f2;
    --success: #059669;
    --success-bg: #ecfdf5;
    --radius: 0.75rem;
    --radius-sm: 0.5rem;
}

/* ── Reset / Base ───────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Layout ─────────────────────────────────────── */
.login-container {
    min-height: 100vh;
    display: flex;
    font-family: 'Sarabun', 'Noto Sans Thai', 'Inter', sans-serif;
    background: var(--gray-50);
    position: relative;
    overflow: hidden;
}

/* ── Background decoration ──────────────────────── */
.bg-decoration { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
.bg-circle {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    animation: breathe 8s ease-in-out infinite;
}
.circle-1 { width: 600px; height: 600px; background: radial-gradient(circle, #bfdbfe 0%, transparent 70%); top: -200px; right: -100px; opacity: 0.7; }
.circle-2 { width: 500px; height: 500px; background: radial-gradient(circle, #e0f2fe 0%, transparent 70%); bottom: -150px; left: -100px; opacity: 0.6; animation-delay: -3s; }
.circle-3 { width: 400px; height: 400px; background: radial-gradient(circle, #ede9fe 0%, transparent 70%); top: 40%; left: 30%; opacity: 0.4; animation-delay: -5s; }
@keyframes breathe { 0%,100% { transform: scale(1); } 50% { transform: scale(1.08); } }

/* ── Left panel ─────────────────────────────────── */
.left-panel {
    display: none;
    position: relative;
    z-index: 1;
    width: 45%;
    background: linear-gradient(160deg, #1e3a5f 0%, #1a6db5 50%, #0ea5e9 100%);
    padding: 3rem;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    overflow: hidden;
}
.left-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.left-panel::after {
    content: '';
    position: absolute;
    bottom: -80px; right: -80px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}
@media (min-width: 1024px) { .left-panel { display: flex; } }

.left-panel-inner { position: relative; z-index: 1; text-align: center; }

.brand-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.25);
    color: rgba(255,255,255,0.9);
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.375rem 0.875rem;
    border-radius: 100px;
    margin-bottom: 2rem;
    backdrop-filter: blur(8px);
}

.brand-logo { margin-bottom: 1.75rem; display: flex; justify-content: center; align-items: center; }
.brand-logo-img {
    width: 110px;
    height: 110px;
    object-fit: contain;
    filter: drop-shadow(0 8px 32px rgba(0,0,0,0.3)) brightness(1.05);
    animation: floatLogo 6s ease-in-out infinite;
}
@keyframes floatLogo { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

.brand-title {
    font-size: 1.625rem;
    font-weight: 700;
    color: white;
    line-height: 1.4;
    margin-bottom: 0.625rem;
    letter-spacing: -0.01em;
}
.brand-subtitle {
    font-size: 0.9375rem;
    color: rgba(255,255,255,0.75);
    margin-bottom: 3rem;
    line-height: 1.5;
}

.feature-list { display: flex; flex-direction: column; gap: 1rem; text-align: left; }
.feature-item {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    color: rgba(255,255,255,0.9);
    font-size: 0.9375rem;
}
.feature-icon {
    width: 38px; height: 38px;
    background: rgba(255,255,255,0.15);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.2);
}

/* ── Right panel ─────────────────────────────────── */
.right-panel {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    position: relative;
    z-index: 1;
    min-height: 100vh;
}

/* ── Login card ──────────────────────────────────── */
.login-card {
    width: 100%;
    max-width: 440px;
    background: var(--white);
    border-radius: 1.25rem;
    border: 1px solid var(--gray-200);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 20px 40px -15px rgba(30,58,95,0.12);
    overflow: hidden;
    animation: slideUp 0.5s cubic-bezier(0.16,1,0.3,1);
    transition: box-shadow 0.3s;
}
.login-card:focus-within {
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 25px 50px -15px rgba(30,58,95,0.18);
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ── Card header ────────────────────────────────── */
.card-header {
    padding: 2.25rem 2.25rem 0;
    text-align: center;
}
.card-logo-mobile {
    display: flex;
    justify-content: center;
    margin-bottom: 1.25rem;
}
@media (min-width: 1024px) { .card-logo-mobile { display: none; } }
.card-logo-img {
    width: 72px; height: 72px;
    object-fit: contain;
    filter: drop-shadow(0 4px 12px rgba(30,58,95,0.2));
}
.card-title {
    font-size: 1.625rem;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 0.375rem;
    letter-spacing: -0.02em;
}
.card-subtitle {
    font-size: 0.9375rem;
    color: var(--gray-500);
}

/* ── Session status ─────────────────────────────── */
.session-status {
    margin: 1.5rem 2.25rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--success-bg);
    border: 1px solid #a7f3d0;
    color: var(--success);
    padding: 0.75rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 500;
}

/* ── Form ────────────────────────────────────────── */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    padding: 1.75rem 2.25rem 2rem;
}

.form-field { display: flex; flex-direction: column; gap: 0.375rem; }

.field-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
}
.field-label-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0;
}

/* Input box */
.input-box {
    display: flex;
    align-items: center;
    background: var(--gray-50);
    border: 1.5px solid var(--gray-200);
    border-radius: var(--radius-sm);
    transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    position: relative;
    overflow: hidden;
}
.input-box:focus-within {
    background: var(--white);
    border-color: var(--blue-light);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
}
.input-box.input-error {
    border-color: var(--error);
    background: var(--error-bg);
}
.input-box.input-error:focus-within {
    box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
}

.input-icon {
    display: flex;
    align-items: center;
    padding: 0 0.875rem;
    color: var(--gray-400);
    flex-shrink: 0;
    transition: color 0.2s;
}
.input-box:focus-within .input-icon { color: var(--blue-light); }

.field-input {
    flex: 1;
    padding: 0.8125rem 0.75rem 0.8125rem 0;
    border: none;
    background: transparent;
    color: var(--gray-900);
    font-size: 0.9375rem;
    font-family: inherit;
    outline: none;
}
.field-input::placeholder { color: var(--gray-400); }

.eye-btn {
    background: none;
    border: none;
    padding: 0 0.875rem;
    color: var(--gray-400);
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: color 0.2s;
    flex-shrink: 0;
}
.eye-btn:hover { color: var(--blue-light); }

/* Error message */
.error-msg {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8125rem;
    color: var(--error);
    font-weight: 500;
}

/* Forgot link */
.forgot-link {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--blue-light);
    text-decoration: none;
    transition: color 0.2s;
}
.forgot-link:hover { color: var(--blue); text-decoration: underline; }

/* Remember me */
.remember-row {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    cursor: pointer;
    user-select: none;
}
.custom-check { position: relative; display: flex; }
.check-input {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}
.check-box {
    width: 18px; height: 18px;
    border: 1.5px solid var(--gray-200);
    border-radius: 5px;
    background: var(--gray-50);
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
    color: transparent;
    flex-shrink: 0;
}
.check-input:checked ~ .check-box {
    background: var(--blue-light);
    border-color: var(--blue-light);
    color: white;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
}
.remember-text { font-size: 0.875rem; color: var(--gray-500); }

/* Submit button */
.submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    width: 100%;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, #1a6db5 0%, #1e3a5f 100%);
    border: none;
    border-radius: var(--radius-sm);
    color: white;
    font-size: 1rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.25s;
    box-shadow: 0 4px 14px rgba(26,109,181,0.35);
    margin-top: 0.25rem;
    letter-spacing: 0.01em;
}
.submit-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #1e5fa0 0%, #162d4a 100%);
    box-shadow: 0 6px 20px rgba(26,109,181,0.45);
    transform: translateY(-1px);
}
.submit-btn:active:not(:disabled) { transform: translateY(0); box-shadow: 0 2px 8px rgba(26,109,181,0.3); }
.submit-btn:disabled { opacity: 0.65; cursor: not-allowed; }

.btn-icon-left { display: flex; }

/* Spinner */
.spinner {
    width: 18px; height: 18px;
    border: 2.5px solid rgba(255,255,255,0.35);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Card footer ─────────────────────────────────── */
.card-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.125rem 2.25rem;
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
}
.footer-text { font-size: 0.875rem; color: var(--gray-500); }
.register-link {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--blue-light);
    text-decoration: none;
    transition: all 0.2s;
}
.register-link:hover { color: var(--blue); gap: 0.5rem; }

/* ── Copyright ───────────────────────────────────── */
.copyright {
    margin-top: 1.5rem;
    font-size: 0.75rem;
    color: var(--gray-400);
    text-align: center;
    line-height: 1.6;
}
</style>

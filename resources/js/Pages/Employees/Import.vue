<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash || {});

const form = useForm({ file: null });

function handleFile(e) { form.file = e.target.files[0]; }
function submit() { form.post('/employees/import', { forceFormData: true }); }

onMounted(() => { setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); });
</script>

<template>
    <AppLayout title="นำเข้าข้อมูลข้าราชการ">
        <div class="max-w-2xl mx-auto">
            <div class="mb-8">
                <Link href="/employees" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-indigo-600 font-bold mb-4 transition-colors"><i data-lucide="arrow-left" class="w-4 h-4"></i> กลับหน้ารายชื่อ</Link>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg border border-slate-100">
                        <i data-lucide="upload-cloud" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">นำเข้าข้อมูลข้าราชการ</h2>
                        <p class="text-sm text-slate-400 font-medium mt-0.5">อัปโหลดไฟล์ Excel เพื่อนำเข้าข้อมูล</p>
                    </div>
                </div>
            </div>

            <div v-if="flash.success" class="mb-6 p-5 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0"><i data-lucide="check" class="w-4 h-4 text-emerald-600"></i></div>
                <span class="text-sm font-bold text-emerald-700">{{ flash.success }}</span>
            </div>
            <div v-if="flash.error" class="mb-6 p-5 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0"><i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i></div>
                <span class="text-sm font-bold text-rose-700">{{ flash.error }}</span>
            </div>

            <div class="glass-card rounded-[2rem] shadow-xl overflow-hidden">
                <div class="p-8 space-y-6">
                    <div class="text-center p-10 border-2 border-dashed border-slate-200 rounded-2xl hover:border-emerald-400 hover:bg-emerald-50/30 transition-all group">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-emerald-100 transition-all">
                            <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-300 group-hover:text-emerald-600 transition-colors"></i>
                        </div>
                        <p class="font-black text-slate-700 mb-1">อัปโหลดไฟล์ Excel</p>
                        <p class="text-xs text-slate-400 mb-5">รองรับ .xlsx, .xls, .csv (ไม่เกิน 10MB)</p>
                        <input type="file" @change="handleFile" accept=".xlsx,.xls,.csv" class="text-sm file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:font-bold file:bg-emerald-50 file:text-emerald-600 hover:file:bg-emerald-100 file:transition-all file:cursor-pointer">
                        <p v-if="form.errors.file" class="text-rose-500 text-xs font-bold mt-2">{{ form.errors.file }}</p>
                    </div>
                    <div class="flex gap-4">
                        <a href="/employees/download-template" class="flex-1 py-4 rounded-2xl border border-slate-200 text-slate-600 font-black text-xs uppercase tracking-widest text-center hover:bg-slate-50 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i> ดาวน์โหลดเทมเพลต
                        </a>
                        <button @click="submit" :disabled="form.processing || !form.file" class="flex-1 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1 disabled:opacity-50 flex items-center justify-center gap-2">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            {{ form.processing ? 'กำลังนำเข้า...' : 'นำเข้าข้อมูล' }}
                        </button>
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

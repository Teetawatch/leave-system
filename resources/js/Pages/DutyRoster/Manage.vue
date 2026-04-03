<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { confirmDialog, Toast } from '@/utils/swal';
import UserAutocomplete from '@/Components/UserAutocomplete.vue';

const props = defineProps({ days: Array, year: Number, month: Number, monthName: String, thaiYear: Number, users: Array, seniorRosters: Array, exemptUserIds: Array, monthlyFile: Object });

const prevMonth = computed(() => {
    let m = props.month - 1, y = props.year;
    if (m < 1) { m = 12; y--; }
    return { year: y, month: m };
});
const nextMonth = computed(() => {
    let m = props.month + 1, y = props.year;
    if (m > 12) { m = 1; y++; }
    return { year: y, month: m };
});

const rosterCount = computed(() => (props.days || []).filter(d => d.roster).length);

const dayForms = ref({});
function initDayForm(day) {
    const d = typeof day.date === 'string' ? day.date : day.date;
    if (!dayForms.value[d]) {
        dayForms.value[d] = {
            duty_officer_id: day.roster?.duty_officer_id || '',
            assistant_duty_officer_id: day.roster?.assistant_duty_officer_id || '',
            saving: false,
        };
    }
    return dayForms.value[d];
}

async function saveDay(dateStr) {
    const f = dayForms.value[dateStr];
    if (!f) return;
    f.saving = true;
    
    router.post('/duty-roster/store', {
        duty_date: dateStr,
        duty_officer_id: f.duty_officer_id || null,
        assistant_duty_officer_id: f.assistant_duty_officer_id || null
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            Toast.fire({ icon: 'success', title: 'บันทึกเวรเรียบร้อยแล้ว' });
            f.saving = false;
        },
        onError: () => {
            Toast.fire({ icon: 'error', title: 'ข้อผิดพลาดในการบันทึก กรุณาตรวจสอบข้อมูล' });
            f.saving = false;
        },
        onFinish: () => {
            f.saving = false;
        }
    });
}

async function autoSchedule() {
    const result = await confirmDialog({ title: 'ยืนยันการจัดเวรอัตโนมัติ?', text: 'ระบบจะจัดตารางเวรสำหรับเดือนนี้โดยอัตโนมัติ', icon: 'question', confirmText: 'จัดเวรอัตโนมัติ', confirmColor: '#4f46e5' });
    if (result.isConfirmed) {
        router.post('/duty-roster/auto-schedule', { year: props.year, month: props.month });
    }
}

async function clearMonth() {
    const result = await confirmDialog({ title: 'ล้างข้อมูลเวรทั้งหมด?', text: 'รายการเวรทั้งหมดของเดือนนี้จะถูกลบออก ไม่สามารถย้อนกลับได้', icon: 'warning', confirmText: 'ล้างข้อมูล', confirmColor: '#ef4444' });
    if (result.isConfirmed) {
        router.delete('/duty-roster/clear-month', { data: { year: props.year, month: props.month } });
    }
}

const fileInput = ref(null);
function triggerImport() {
    fileInput.value.click();
}

const importForm = useForm({
    file: null,
});

function handleFileChange(event) {
    const file = event.target.files[0];
    if (!file) return;

    importForm.file = file;
    importForm.post('/duty-roster/import', {
        onSuccess: () => {
            importForm.reset();
            if (fileInput.value) fileInput.value.value = '';
        },
        onError: (err) => {
            console.error(err);
        }
    });
}

const thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
function getDayOfWeek(dateStr) {
    return new Date(dateStr).getDay();
}
function isWeekend(dateStr) {
    const d = new Date(dateStr).getDay();
    return d === 0 || d === 6;
}
function isToday(dateStr) {
    return dateStr === new Date().toISOString().split('T')[0];
}

// ========== File Upload ==========
const isDragging = ref(false);
const isUploading = ref(false);

const uploadForm = useForm({
    year: props.year,
    month: props.month,
    file: null,
});

function formatFileSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function getFileIcon(name) {
    if (!name) return 'file';
    const ext = name.split('.').pop().toLowerCase();
    if (ext === 'pdf') return 'file-text';
    if (['jpg', 'jpeg', 'png'].includes(ext)) return 'image';
    if (['doc', 'docx'].includes(ext)) return 'file-type';
    if (['xls', 'xlsx'].includes(ext)) return 'table';
    return 'file';
}

function triggerFileInput() {
    fileInput.value?.click();
}

function handleUploadFileChange(e) {
    const file = e.target.files[0];
    if (file) uploadFile(file);
}

function handleDrop(e) {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    if (file) uploadFile(file);
}

function uploadFile(file) {
    isUploading.value = true;
    uploadForm.file = file;
    uploadForm.year = props.year;
    uploadForm.month = props.month;

    router.post('/duty-roster/upload-monthly-file', {
        year: props.year,
        month: props.month,
        file: file,
    }, {
        forceFormData: true,
        onFinish: () => {
            isUploading.value = false;
            if (fileInput.value) fileInput.value.value = '';
        },
    });
}

async function deleteFile() {
    const result = await confirmDialog({
        title: 'ลบไฟล์ใบเวรยาม?',
        text: 'ไฟล์จะถูกลบออกจากระบบ ไม่สามารถย้อนกลับได้',
        icon: 'warning',
        confirmText: 'ลบไฟล์',
        confirmColor: '#ef4444',
    });
    if (result.isConfirmed) {
        router.delete('/duty-roster/delete-monthly-file', {
            data: { year: props.year, month: props.month },
        });
    }
}

onMounted(() => {
    (props.days || []).forEach(day => initDayForm(day));
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="จัดการตารางเวร">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <i data-lucide="settings" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">จัดการตารางเวร</h1>
                            <p class="text-xs text-slate-400 font-medium">Manage Duty Roster</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button @click="autoSchedule"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-600 border border-indigo-100 font-bold rounded-xl hover:bg-indigo-100 transition-all text-xs">
                            <i data-lucide="zap" class="w-4 h-4"></i> จัดเวรอัตโนมัติ
                        </button>
                        <input type="file" ref="fileInput" class="hidden" accept=".xlsx,.xls,.csv" @change="handleFileChange">
                        <button @click="triggerImport" :disabled="importForm.processing"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-600 border border-emerald-100 font-bold rounded-xl hover:bg-emerald-100 transition-all text-xs"
                            :class="{ 'opacity-50 cursor-not-allowed': importForm.processing }">
                            <i v-if="!importForm.processing" data-lucide="file-up" class="w-4 h-4"></i>
                            <i v-else class="w-4 h-4 animate-spin border-2 border-emerald-600 border-t-transparent rounded-full"></i>
                            {{ importForm.processing ? 'กำลังนำเข้า...' : 'นำเข้า Excel' }}
                        </button>
                        <a :href="`/duty-roster/template?year=${year}&month=${month}`"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-600 border border-blue-100 font-bold rounded-xl hover:bg-blue-100 transition-all text-xs">
                            <i data-lucide="download" class="w-4 h-4"></i> แม่แบบ
                        </a>
                        <Link :href="`/duty-roster?year=${year}&month=${month}`"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-xs">
                            <i data-lucide="eye" class="w-4 h-4"></i> ดูตาราง
                        </Link>
                        <a :href="`/duty-roster/export-pdf?year=${year}&month=${month}`" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 text-rose-600 border border-rose-100 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs">
                            <i data-lucide="file-text" class="w-4 h-4"></i> PDF
                        </a>
                        <button @click="clearMonth"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 text-rose-600 border border-rose-100 font-bold rounded-xl hover:bg-rose-100 transition-all text-xs">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> ล้างทั้งเดือน
                        </button>
                    </div>
                </div>
            </div>

            <!-- Month Navigation -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 mb-6 flex items-center justify-between">
                <Link :href="`/duty-roster/manage?year=${prevMonth.year}&month=${prevMonth.month}`"
                    class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-500 flex items-center justify-center hover:border-indigo-500 hover:text-indigo-500 hover:scale-105 transition-all">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </Link>
                <div class="text-center">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800">{{ monthName }} {{ thaiYear }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">กำหนดเวรแล้ว {{ rosterCount }} วัน / {{ days?.length || 0 }} วัน · คลิกที่แต่ละวันเพื่อกำหนดเวร</p>
                </div>
                <Link :href="`/duty-roster/manage?year=${nextMonth.year}&month=${nextMonth.month}`"
                    class="w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-500 flex items-center justify-center hover:border-indigo-500 hover:text-indigo-500 hover:scale-105 transition-all">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </Link>
            </div>

            <!-- Monthly File Upload Section -->
            <div class="file-upload-section rounded-2xl p-5 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center shadow-md shadow-violet-500/20">
                        <i data-lucide="paperclip" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-violet-800 uppercase tracking-wider">ใบเวรยามประจำเดือน</h3>
                        <p class="text-[10px] text-violet-600">แนบไฟล์ใบเวรยามประจำเดือน (PDF, รูปภาพ, Word, Excel · ไม่เกิน 10MB)</p>
                    </div>
                </div>

                <!-- Current File Display -->
                <div v-if="monthlyFile" class="bg-white rounded-xl border border-violet-200 p-4 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center border border-violet-100">
                                <i :data-lucide="getFileIcon(monthlyFile.name)" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ monthlyFile.name }}</p>
                                <p class="text-[10px] text-slate-400">{{ formatFileSize(monthlyFile.size) }} · อัปเดตล่าสุด: {{ monthlyFile.updated_at }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a :href="monthlyFile.url" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-violet-50 text-violet-600 border border-violet-100 font-bold rounded-lg hover:bg-violet-100 transition-all text-xs">
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i> เปิดไฟล์
                            </a>
                            <a :href="monthlyFile.url" download
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-100 font-bold rounded-lg hover:bg-emerald-100 transition-all text-xs">
                                <i data-lucide="download" class="w-3.5 h-3.5"></i> ดาวน์โหลด
                            </a>
                            <button @click="deleteFile"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-100 font-bold rounded-lg hover:bg-rose-100 transition-all text-xs">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> ลบ
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload Drop Zone -->
                <div
                    class="upload-dropzone rounded-xl border-2 border-dashed p-6 text-center cursor-pointer transition-all"
                    :class="isDragging ? 'border-violet-500 bg-violet-50/50' : 'border-violet-200 bg-white/50 hover:border-violet-400 hover:bg-violet-50/30'"
                    @click="triggerFileInput"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop"
                >
                    <div v-if="isUploading" class="flex flex-col items-center gap-2">
                        <div class="w-8 h-8 border-3 border-violet-200 border-t-violet-600 rounded-full animate-spin"></div>
                        <p class="text-sm font-bold text-violet-600">กำลังอัปโหลด...</p>
                    </div>
                    <div v-else class="flex flex-col items-center gap-2">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 text-violet-400 flex items-center justify-center mx-auto">
                            <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-600">
                            {{ monthlyFile ? 'อัปโหลดไฟล์ใหม่ (แทนที่ไฟล์เดิม)' : 'คลิกหรือลากไฟล์มาวางที่นี่' }}
                        </p>
                        <p class="text-[10px] text-slate-400">รองรับ PDF, JPG, PNG, DOC, DOCX, XLS, XLSX (ไม่เกิน 10MB)</p>
                    </div>
                    <input ref="fileInput" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" @change="handleUploadFileChange" />
                </div>
            </div>

            <!-- Senior Duty Rosters -->
            <div v-if="seniorRosters && seniorRosters.length > 0" class="senior-section rounded-2xl p-5 mb-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md shadow-amber-500/20">
                        <i data-lucide="crown" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-amber-800 uppercase tracking-wider">นายทหารเวรอาวุโส</h3>
                        <p class="text-[10px] text-amber-600">กำหนดเป็นห้วงเวลา</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div v-for="sr in seniorRosters" :key="sr.id"
                        class="bg-white rounded-xl border border-amber-200 p-4 hover:shadow-md transition-all">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="crown" class="w-4 h-4 text-amber-600"></i>
                            <span class="text-sm font-bold text-amber-800">{{ sr.senior_officer?.rank }} {{ sr.senior_officer?.name }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-amber-700">
                            <i data-lucide="calendar" class="w-3 h-3"></i>
                            <span class="font-medium">{{ sr.start_date }} — {{ sr.end_date }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Roster Cards -->
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20">
                    <i data-lucide="shield" class="w-4 h-4 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">นายทหารเวร & ผู้ช่วยนายทหารเวร</h3>
                    <p class="text-[10px] text-slate-400">กำหนดรายวัน</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <div v-for="day in days" :key="day.date"
                    class="day-card relative rounded-2xl border p-4"
                    :class="[
                        isToday(day.date) ? 'border-indigo-400 bg-white' : isWeekend(day.date) ? 'border-rose-200 bg-rose-50/30' : day.roster ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-100 bg-white',
                    ]">
                    <div class="absolute top-0 left-0 w-1 h-full rounded-l-2xl"
                        :class="isToday(day.date) ? 'bg-gradient-to-b from-indigo-500 to-indigo-700' : isWeekend(day.date) ? 'bg-gradient-to-b from-rose-400 to-rose-600' : day.roster ? 'bg-gradient-to-b from-emerald-400 to-emerald-600' : 'bg-slate-200'"></div>

                    <!-- Day Header -->
                    <div class="flex items-center justify-between mb-3 ml-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-bold"
                                :class="isToday(day.date) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/30' : isWeekend(day.date) ? 'bg-rose-50 text-rose-500' : 'bg-slate-50 text-slate-700'">
                                {{ new Date(day.date).getDate() }}
                            </span>
                            <div>
                                <span class="text-sm font-bold" :class="isWeekend(day.date) ? 'text-rose-500' : 'text-slate-700'">{{ thaiDays[getDayOfWeek(day.date)] }}</span>
                                <span v-if="isToday(day.date)" class="ml-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded-md">วันนี้</span>
                            </div>
                        </div>
                        <div v-if="day.roster" class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-md shadow-emerald-500/30"></div>
                    </div>

                    <!-- Officer Selects -->
                    <div class="space-y-3 ml-2">
                        <div>
                            <label class="text-[10px] font-bold text-blue-600 uppercase tracking-wider flex items-center gap-1 mb-1">
                                <i data-lucide="shield" class="w-3 h-3"></i> นายทหารเวร
                            </label>
                            <UserAutocomplete 
                                v-if="dayForms[day.date]" 
                                v-model="dayForms[day.date].duty_officer_id"
                                :options="users"
                                placeholder="ค้นหาและเลือก..."
                            />
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-pink-600 uppercase tracking-wider flex items-center gap-1 mb-1">
                                <i data-lucide="shield-check" class="w-3 h-3"></i> ผู้ช่วยนายทหารเวร
                            </label>
                            <UserAutocomplete 
                                v-if="dayForms[day.date]" 
                                v-model="dayForms[day.date].assistant_duty_officer_id"
                                :options="users"
                                placeholder="ค้นหาและเลือก..."
                            />
                        </div>
                        <button @click="saveDay(day.date)" :disabled="dayForms[day.date]?.saving"
                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all"
                            :class="dayForms[day.date]?.saving ? 'bg-slate-100 text-slate-400' : 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 hover:-translate-y-0.5'">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            {{ dayForms[day.date]?.saving ? 'กำลังบันทึก...' : 'บันทึก' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.senior-section {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 1px solid #fbbf24;
}
.file-upload-section {
    background: linear-gradient(135deg, #f5f3ff, #ede9fe);
    border: 1px solid #c4b5fd;
}
.upload-dropzone {
    transition: all 0.3s ease;
}
.day-card { transition: all 0.3s ease; }
.day-card:hover { box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06); }
</style>

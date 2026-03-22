<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, nextTick } from 'vue';

const props = defineProps({
    leaveTypes: Array,
    leaveBalances: Object,
});

const form = useForm({
    leave_type_id: '',
    start_date: '',
    end_date: '',
    reason: '',
    contact_address: { province: '', house: '', road: '', tambon: '', amphoe: '' },
    temporary_leave_period: 'morning',
    attachment: null,
});

const currentStep = ref(1);
const submitting = ref(false);
const fileName = ref('');
const todayDate = new Date().toLocaleDateString('en-CA');

const selectedType = computed(() => props.leaveTypes?.find(t => t.id == form.leave_type_id));
const isTemporary = computed(() => selectedType.value?.slug === 'temporary');
const isSick = computed(() => selectedType.value?.slug === 'sick');
const isPersonal = computed(() => selectedType.value?.slug === 'personal');

const steps = [
    { name: 'เลือกประเภท' },
    { name: 'ระบุวันลา' },
    { name: 'กรอกเหตุผล' },
];

function isStepComplete(stepNum) {
    if (stepNum === 1) return !!form.leave_type_id;
    if (stepNum === 2) return !!form.start_date && !!form.end_date;
    return false;
}

// Balance helpers
function getBalance() {
    if (!form.leave_type_id || !props.leaveBalances) return null;
    return props.leaveBalances[form.leave_type_id] || Object.values(props.leaveBalances).find(b => b.leave_type_id == form.leave_type_id) || null;
}
const usedDays = computed(() => { const b = getBalance(); return b ? (b.used_days || 0) : 0; });
const remainingDays = computed(() => {
    const b = getBalance();
    if (b) return b.remaining_days || 0;
    return selectedType.value?.max_days_per_year || 0;
});
const totalDays = computed(() => {
    const b = getBalance();
    if (b) return b.total_days || 0;
    return selectedType.value?.max_days_per_year || 0;
});
const usagePercent = computed(() => {
    if (totalDays.value <= 0) return 0;
    return Math.min(Math.round((usedDays.value / totalDays.value) * 100), 100);
});

// Duration
const duration = computed(() => {
    if (form.start_date && form.end_date) {
        const start = new Date(form.start_date);
        const end = new Date(form.end_date);
        const diff = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
        return diff > 0 ? diff : 0;
    }
    return 0;
});

function formatDate(dateStr) {
    if (!dateStr) return null;
    return new Date(dateStr).toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric' });
}

function typeIcon(slug) {
    if (slug === 'vacation') return 'palmtree';
    if (slug === 'sick') return 'thermometer';
    if (slug === 'temporary') return 'clock';
    return 'briefcase';
}

function typeColor(slug) {
    if (slug === 'vacation') return 'bg-blue-50 text-blue-500';
    if (slug === 'sick') return 'bg-rose-50 text-rose-500';
    if (slug === 'temporary') return 'bg-purple-50 text-purple-500';
    return 'bg-amber-50 text-amber-500';
}

function updateStep() {
    if (form.leave_type_id && form.start_date && form.end_date) {
        currentStep.value = 3;
    } else if (form.leave_type_id) {
        currentStep.value = 2;
    }
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
}

// Watchers
watch(() => form.leave_type_id, (val) => {
    if (val) {
        currentStep.value = 2;
        if (isTemporary.value) {
            if (!form.start_date) form.start_date = todayDate;
            form.end_date = form.start_date;
        }
    }
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
});

watch(() => form.start_date, (val) => {
    if (isTemporary.value) { form.end_date = val; }
    else if (form.end_date && val > form.end_date) { form.end_date = val; }
    updateStep();
});

watch(() => form.end_date, (val) => {
    if (!isTemporary.value && form.start_date && val < form.start_date) { form.start_date = val; }
    updateStep();
});

function submit() {
    submitting.value = true;
    form.post('/leave-request', {
        forceFormData: true,
        onSuccess: () => form.reset(),
        onFinish: () => { submitting.value = false; },
    });
}

function handleFileChange(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            if (window.Swal) {
                window.Swal.fire({ icon: 'error', title: 'ขนาดไฟล์เกินกำหนด', text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB', confirmButtonText: 'ตกลง', confirmButtonColor: '#ef4444' });
            }
            clearFile(); return;
        }
        form.attachment = file;
        fileName.value = file.name;
    }
}

function handleFileDrop(e) {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            if (window.Swal) {
                window.Swal.fire({ icon: 'error', title: 'ขนาดไฟล์เกินกำหนด', text: 'กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 5MB', confirmButtonText: 'ตกลง', confirmButtonColor: '#ef4444' });
            }
            return;
        }
        form.attachment = file;
        fileName.value = file.name;
    }
}

function clearFile() {
    form.attachment = null;
    fileName.value = '';
}

const provinces = ['กรุงเทพมหานคร', 'ระยอง', 'ชลบุรี', 'จันทบุรี', 'ตราด', 'เชียงใหม่', 'เชียงราย', 'ภูเก็ต', 'สงขลา', 'นครราชสีมา', 'ขอนแก่น', 'นนทบุรี', 'ปทุมธานี', 'สมุทรปราการ', 'นครปฐม'];

onMounted(() => {
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
});
</script>

<template>
    <AppLayout title="ยื่นคำขอลาปฏิบัติราชการ">
        <div class="premium-bg -m-4 md:-m-8 pb-20 relative overflow-hidden">
            <!-- Background Decorations -->
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">

                <!-- Header Section -->
                <div class="flex flex-col items-center text-center mb-16 space-y-6 animate-slide-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] shadow-sm border border-indigo-100">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        ระบบบริหารจัดการงานกำลังพล
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-slate-900 tracking-tight leading-none">
                        แบบฟอร์มยื่นคำขอลา
                    </h1>
                    <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed">
                        ยื่นคำขอลาออนไลน์ผ่านระบบบริหารจัดการกำลังพลแบบเรียลไทม์<br class="hidden md:block">
                        รวดเร็ว โปร่งใส และตรวจสอบสถานะได้ทันที
                    </p>

                    <!-- Modern Stepper UI -->
                    <div class="flex items-center justify-center gap-4 mt-10 w-full max-w-3xl overflow-x-auto pb-4">
                        <template v-for="(st, index) in steps" :key="index">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-3 group">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-xl transition-all duration-500"
                                        :class="currentStep >= index + 1 ? 'step-active text-white' : 'bg-white text-slate-300 border border-slate-100'">
                                        <span v-if="!isStepComplete(index + 1)">{{ index + 1 }}</span>
                                        <i v-else data-lucide="check" class="w-6 h-6"></i>
                                    </div>
                                    <span class="hidden md:block font-bold text-base uppercase tracking-wider"
                                        :class="currentStep >= index + 1 ? 'text-slate-800' : 'text-slate-300'">{{ st.name }}</span>
                                </div>
                                <div v-if="index < steps.length - 1" class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 transition-all duration-700" :style="'width: ' + (currentStep > index + 1 ? '100%' : '0%')"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

                    <!-- Left Content Area -->
                    <div class="lg:col-span-8 space-y-10">

                        <!-- STEP 1: Type Selection -->
                        <section v-show="currentStep >= 1" class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-2xl shadow-indigo-500/5 border-indigo-50/50">
                            <div class="flex items-center gap-6 mb-12">
                                <div class="w-16 h-16 rounded-[2rem] bg-slate-900 text-white flex items-center justify-center shadow-xl rotate-3 flex-shrink-0">
                                    <i data-lucide="layers" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight">1. เลือกประเภทการลา</h3>
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">ระบุวัตถุประสงค์หลักของการลาครั้งนี้</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <label v-for="lt in leaveTypes" :key="lt.id" class="relative group cursor-pointer">
                                    <input type="radio" :value="lt.id" v-model="form.leave_type_id" class="peer sr-only">
                                    <div class="h-full p-8 rounded-[2.5rem] border-2 border-slate-50 bg-white/40 backdrop-blur-md text-center transition-all duration-300 peer-checked:type-card-active hover:border-indigo-100 hover:bg-white/80">
                                        <div class="w-20 h-20 mx-auto rounded-[2rem] flex items-center justify-center text-4xl mb-6 shadow-inner transition-all duration-500 group-hover:scale-110 group-hover:-rotate-6"
                                            :class="typeColor(lt.slug)">
                                            <i :data-lucide="typeIcon(lt.slug)" class="w-10 h-10"></i>
                                        </div>
                                        <h4 class="text-lg font-black text-slate-900 tracking-tight">{{ lt.name }}</h4>
                                        <div class="absolute top-5 right-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-all transform scale-50 peer-checked:scale-100">
                                            <div class="p-1 bg-indigo-50 rounded-full">
                                                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.leave_type_id" class="text-rose-500 text-xs mt-4 font-bold">{{ form.errors.leave_type_id }}</p>

                            <!-- Leave Balance Info Card -->
                            <Transition enter-active-class="transition ease-out duration-500" enter-from-class="opacity-0 translate-y-4 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100">
                                <div v-if="form.leave_type_id && !isTemporary" class="mt-8">
                                    <div class="balance-card rounded-[2.5rem] p-8 animate-fade-in-scale">
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg">
                                                <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-black text-slate-800 tracking-tight">สถิติการลา <span class="text-indigo-600">{{ selectedType?.name }}</span></h4>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em]">ปีงบประมาณ {{ new Date().getFullYear() + 543 }}</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 mb-6">
                                            <div class="bg-white rounded-[1.5rem] p-5 text-center border border-slate-100 shadow-sm group hover:shadow-md hover:border-rose-100 transition-all">
                                                <div class="w-10 h-10 mx-auto rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                    <i data-lucide="calendar-minus" class="w-5 h-5"></i>
                                                </div>
                                                <p class="text-3xl font-black text-rose-500">{{ usedDays }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] mt-1">ลาไปแล้ว (วัน)</p>
                                            </div>
                                            <div class="bg-white rounded-[1.5rem] p-5 text-center border border-slate-100 shadow-sm group hover:shadow-md hover:border-emerald-100 transition-all">
                                                <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                                                </div>
                                                <p class="text-3xl font-black text-emerald-500">{{ remainingDays }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] mt-1">คงเหลือ (วัน)</p>
                                            </div>
                                            <div class="bg-white rounded-[1.5rem] p-5 text-center border border-slate-100 shadow-sm group hover:shadow-md hover:border-indigo-100 transition-all">
                                                <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                                    <i data-lucide="calendar-range" class="w-5 h-5"></i>
                                                </div>
                                                <p class="text-3xl font-black text-indigo-500">{{ totalDays }}</p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.15em] mt-1">สิทธิ์ทั้งปี (วัน)</p>
                                            </div>
                                        </div>
                                        <div class="bg-slate-100 rounded-full h-3 overflow-hidden">
                                            <div class="h-full rounded-full balance-progress" :class="usagePercent > 80 ? 'bg-gradient-to-r from-rose-400 to-rose-500' : usagePercent > 50 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-emerald-400 to-emerald-500'" :style="'width: ' + usagePercent + '%'"></div>
                                        </div>
                                        <div class="flex justify-between mt-2">
                                            <span class="text-[10px] font-bold text-slate-400">ใช้ไป {{ usagePercent }}%</span>
                                            <span class="text-[10px] font-bold text-slate-400">คงเหลือ {{ 100 - usagePercent }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </section>

                        <!-- Dynamic Sections (shown after type selection) -->
                        <template v-if="form.leave_type_id">

                            <!-- Address for Sick Leave -->
                            <section v-if="isSick" class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-xl border-rose-50/50">
                                <div class="flex items-center gap-6 mb-12">
                                    <div class="w-16 h-16 rounded-[2rem] bg-rose-500 text-white flex items-center justify-center shadow-xl -rotate-3 flex-shrink-0">
                                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-black text-rose-500 tracking-tight">ที่อยู่ที่สามารถติดต่อได้</h3>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">กรณีฉุกเฉินหรือต้องการแจ้งผลการตรวจ</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">บ้านเลขที่</label>
                                        <input type="text" v-model="form.contact_address.house" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="123/45...">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">ถนน</label>
                                        <input type="text" v-model="form.contact_address.road" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="ถนนพลาธิการ...">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">ตำบล / แขวง</label>
                                        <input type="text" v-model="form.contact_address.tambon" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="ระบุตำบล...">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">อำเภอ / เขต</label>
                                        <input type="text" v-model="form.contact_address.amphoe" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="ระบุอำเภอ...">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">จังหวัด</label>
                                        <input type="text" v-model="form.contact_address.province" list="provinces" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="ระบุจังหวัด...">
                                    </div>
                                </div>
                            </section>

                            <!-- Address for Personal Leave -->
                            <section v-if="isPersonal" class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-xl border-amber-50/50">
                                <div class="flex items-center gap-6 mb-12">
                                    <div class="w-16 h-16 rounded-[2rem] bg-amber-500 text-white flex items-center justify-center shadow-xl rotate-2 flex-shrink-0">
                                        <i data-lucide="map-pin" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-black text-amber-500 tracking-tight">สถานที่ติดต่อระหว่างลา</h3>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">ระบุสถานที่ที่สามารถติดต่อได้กรณีเร่งด่วน</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">สถานที่ / บ้านเลขที่</label>
                                        <input type="text" v-model="form.contact_address.house" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="เช่น บ้านพักต่างจังหวัด...">
                                    </div>
                                    <div class="space-y-3">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] ml-1">จังหวัด</label>
                                        <input type="text" v-model="form.contact_address.province" list="provinces" class="w-full px-8 py-5 premium-input rounded-[2rem] font-bold text-slate-800 text-lg" placeholder="ระบุจังหวัด...">
                                    </div>
                                </div>
                            </section>

                            <!-- Date Range Selection -->
                            <section class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-xl border-indigo-50/50">
                                <div class="flex items-center gap-6 mb-12">
                                    <div class="w-16 h-16 rounded-[2rem] bg-indigo-600 text-white flex items-center justify-center shadow-xl rotate-3 flex-shrink-0">
                                        <i data-lucide="calendar-days" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">2. กำหนดช่วงเวลาการลา</h3>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">{{ isTemporary ? 'ระบุวันและช่วงเวลาที่ต้องการลา' : 'เลือกวันเริ่มต้นถึงวันสิ้นสุดการปฏิบัติราชการ' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row items-center gap-8">
                                    <!-- Start Date -->
                                    <div class="flex-1 w-full relative group">
                                        <label class="absolute -top-3 left-8 px-3 bg-white text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] rounded-full z-10 border border-indigo-100 shadow-sm">วันเริ่มต้น</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none text-slate-300 transition-colors group-focus-within:text-indigo-500">
                                                <i data-lucide="calendar" class="w-6 h-6"></i>
                                            </div>
                                            <input type="date" v-model="form.start_date" required :min="isSick ? '' : todayDate"
                                                class="w-full pl-20 pr-8 py-6 premium-input rounded-[2.5rem] font-black text-slate-900 text-xl shadow-inner">
                                        </div>
                                        <p v-if="form.errors.start_date" class="text-rose-500 text-xs mt-2 ml-4 font-bold">{{ form.errors.start_date }}</p>
                                    </div>

                                    <div class="hidden md:flex flex-col items-center" v-if="!isTemporary">
                                        <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 shadow-inner">
                                            <i data-lucide="arrow-right" class="w-7 h-7"></i>
                                        </div>
                                    </div>

                                    <!-- End Date -->
                                    <div v-if="!isTemporary" class="flex-1 w-full relative group">
                                        <label class="absolute -top-3 left-8 px-3 bg-white text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] rounded-full z-10 border border-indigo-100 shadow-sm">วันสิ้นสุด</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-8 flex items-center pointer-events-none text-slate-300 transition-colors group-focus-within:text-indigo-500">
                                                <i data-lucide="calendar-check-2" class="w-6 h-6"></i>
                                            </div>
                                            <input type="date" v-model="form.end_date" required :min="form.start_date"
                                                class="w-full pl-20 pr-8 py-6 premium-input rounded-[2.5rem] font-black text-slate-900 text-xl shadow-inner">
                                        </div>
                                        <p v-if="form.errors.end_date" class="text-rose-500 text-xs mt-2 ml-4 font-bold">{{ form.errors.end_date }}</p>
                                    </div>

                                    <!-- Period for Temporary -->
                                    <div v-if="isTemporary" class="flex-1 w-full flex bg-slate-50 p-2 rounded-[2.5rem] border border-slate-100">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" value="morning" v-model="form.temporary_leave_period" class="sr-only peer">
                                            <div class="py-4 text-center rounded-[2rem] font-black text-base peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-xl transition-all duration-300 text-slate-400">
                                                ช่วงเช้า <span class="block text-[10px] mt-1 opacity-70 font-bold">(ก่อน 07:30 น.)</span>
                                            </div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" value="afternoon" v-model="form.temporary_leave_period" class="sr-only peer">
                                            <div class="py-4 text-center rounded-[2rem] font-black text-base peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-xl transition-all duration-300 text-slate-400">
                                                ช่วงบ่าย <span class="block text-[10px] mt-1 opacity-70 font-bold">(ก่อน 11:00 น.)</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </section>

                            <!-- Details & Documents -->
                            <section class="glass-panel rounded-[3.5rem] p-8 md:p-12 shadow-xl border-emerald-50/50">
                                <div class="flex items-center gap-6 mb-12">
                                    <div class="w-16 h-16 rounded-[2rem] bg-emerald-500 text-white flex items-center justify-center shadow-xl -rotate-3 flex-shrink-0">
                                        <i data-lucide="file-text" class="w-8 h-8"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">3. รายละเอียดเพิ่มเติม</h3>
                                        <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-2">ระบุเหตุผลความจำเป็นและความประสงค์</p>
                                    </div>
                                </div>
                                <div class="space-y-10">
                                    <div class="relative">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 ml-1">เหตุผลหรือความจำเป็นในการลา <span class="text-rose-500 font-bold">*</span></label>
                                        <textarea v-model="form.reason" rows="4" required class="w-full px-8 py-6 premium-input rounded-[2.5rem] font-bold text-slate-900 text-xl resize-none shadow-inner" placeholder="ระบุเหตุผลการลา..."></textarea>
                                        <p v-if="form.errors.reason" class="text-rose-500 text-xs mt-2 ml-4 font-bold">{{ form.errors.reason }}</p>
                                    </div>

                                    <div class="relative group">
                                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 ml-1">เอกสารประกอบ (ถ้ามี)</label>
                                        <div class="file-drop relative rounded-[3rem] border-2 border-dashed border-slate-200 p-12 text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer group/upload"
                                            @dragover.prevent @drop="handleFileDrop">
                                            <input type="file" accept=".jpg,.jpeg,.png,.pdf" @change="handleFileChange"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                            <div class="flex flex-col items-center gap-6">
                                                <div class="w-24 h-24 rounded-[2.5rem] bg-white text-indigo-500 flex items-center justify-center group-hover/upload:scale-110 group-hover/upload:rotate-12 transition-all shadow-xl border border-indigo-50">
                                                    <i data-lucide="upload-cloud" class="w-12 h-12"></i>
                                                </div>
                                                <div v-if="!fileName">
                                                    <p class="text-2xl font-black text-slate-900">ลากไฟล์มาวาง หรือ คลิกเพื่ออัปโหลด</p>
                                                    <p class="text-xs font-black text-slate-400 mt-3 uppercase tracking-[0.2em]">PDF, JPG, PNG (ไม่เกิน 5MB)</p>
                                                </div>
                                                <div v-else class="flex items-center gap-4 bg-white px-8 py-4 rounded-[2rem] shadow-2xl border border-indigo-100 scale-105">
                                                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                                                        <i data-lucide="file-check" class="w-6 h-6"></i>
                                                    </div>
                                                    <span class="text-lg font-black text-slate-800 truncate max-w-[250px]">{{ fileName }}</span>
                                                    <button type="button" @click.prevent="clearFile" class="p-2 bg-rose-50 text-rose-500 rounded-xl hover:bg-rose-100 transition-colors cursor-pointer">
                                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <p v-if="form.errors.attachment" class="text-rose-500 text-xs mt-2 ml-4 font-bold">{{ form.errors.attachment }}</p>
                                    </div>
                                </div>
                            </section>

                            <!-- Action Buttons -->
                            <div class="pt-12 flex flex-col md:flex-row items-center gap-8">
                                <button type="submit" :disabled="submitting"
                                    class="w-full md:w-auto flex-1 py-7 bg-gradient-to-r from-indigo-600 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 text-white font-black text-2xl rounded-[3rem] shadow-[0_25px_60px_-15px_rgba(79,70,229,0.5)] hover:shadow-[0_35px_70px_-12px_rgba(79,70,229,0.6)] transition-all hover:-translate-y-2 active:scale-95 flex items-center justify-center gap-5 group cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                                    <template v-if="!submitting">
                                        <i data-lucide="shield-check" class="w-8 h-8 group-hover:rotate-12 transition-transform"></i>
                                        <span>ส่งคำขอยืนยันใบลา</span>
                                    </template>
                                    <template v-else>
                                        <svg class="animate-spin w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span>กำลังส่ง....</span>
                                    </template>
                                </button>
                                <Link href="/dashboard" class="w-full md:w-auto px-12 py-7 bg-white text-slate-400 hover:text-rose-500 font-black text-xl rounded-[3rem] transition-all hover:bg-rose-50 border border-slate-100 text-center shadow-sm">
                                    ยกเลิก
                                </Link>
                            </div>
                        </template>
                    </div>

                    <!-- Right Sidebar: Summary Ticket -->
                    <div class="lg:col-span-4 lg:sticky lg:top-24 mt-10 md:mt-0">
                        <div class="bg-white/90 backdrop-blur-2xl rounded-[3.5rem] p-8 pb-10 relative overflow-hidden shadow-2xl shadow-indigo-500/5 border border-white">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/80 rounded-full blur-[60px] -mr-20 -mt-20 opacity-70 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-50/80 rounded-full blur-[50px] -ml-10 -mb-10 opacity-70 pointer-events-none"></div>

                            <div class="relative z-10 space-y-8">
                                <div class="flex items-center gap-4 border-b border-slate-100 pb-6">
                                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm border border-indigo-100/50">
                                        <i data-lucide="receipt" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-black text-slate-800 tracking-tight">สรุปรายการลา</h4>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Live Summary
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-indigo-50/30 rounded-[2.5rem] p-7 shadow-sm border border-slate-100/80 space-y-7 relative group overflow-hidden hover:bg-white hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-100 transition-all duration-500">
                                    <div class="space-y-7 relative z-10">
                                        <!-- Leave Type -->
                                        <div class="flex items-start gap-5">
                                            <div class="w-12 h-12 rounded-[1.25rem] bg-white text-indigo-600 shadow-sm border border-slate-100 flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-500">
                                                <i data-lucide="tag" class="w-5 h-5"></i>
                                            </div>
                                            <div class="flex-1 mt-0.5">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ประเภทการลา</p>
                                                <p class="text-base font-black text-slate-800 leading-tight mt-1.5">{{ selectedType?.name || 'อัพเดทอัตโนมัติ...' }}</p>
                                            </div>
                                        </div>

                                        <!-- Dates -->
                                        <div class="grid grid-cols-2 gap-4 bg-white rounded-[1.5rem] p-4 border border-slate-100 shadow-sm relative group-hover:border-indigo-50 transition-colors duration-500">
                                            <div v-if="!isTemporary" class="absolute left-1/2 top-1/2 -translate-y-1/2 w-8 h-px bg-slate-200 -ml-4"></div>
                                            <div v-if="!isTemporary" class="absolute left-1/2 top-1/2 -translate-y-1/2 w-6 h-6 bg-slate-50 rounded-full flex items-center justify-center border border-slate-200 -ml-3 z-10 text-slate-400">
                                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                            </div>
                                            <div class="px-2">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">เริ่มต้น</p>
                                                <p class="text-[13px] font-black text-slate-800 mt-1 truncate">{{ formatDate(form.start_date) || 'ยังไม่ระบุ' }}</p>
                                            </div>
                                            <div v-if="!isTemporary" class="px-2 text-right lg:text-left">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">สิ้นสุด</p>
                                                <p class="text-[13px] font-black text-slate-800 mt-1 truncate">{{ formatDate(form.end_date) || 'ยังไม่ระบุ' }}</p>
                                            </div>
                                            <div v-else class="px-2 text-right lg:text-left">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ช่วงเวลา</p>
                                                <p class="text-[13px] font-black text-slate-800 mt-1 truncate">{{ form.temporary_leave_period === 'morning' ? 'ช่วงเช้า' : 'ช่วงบ่าย' }}</p>
                                            </div>
                                        </div>

                                        <!-- Duration -->
                                        <div class="pt-6 border-t border-dashed border-slate-200 flex items-center justify-between">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">จำนวนวันที่ใช้สิทธิ์</span>
                                                <div class="flex items-baseline gap-1 mt-1">
                                                    <span class="text-5xl font-black text-indigo-600 tracking-tighter">{{ duration > 0 && !isTemporary ? duration : (isTemporary && selectedType ? '0.5' : '0') }}</span>
                                                    <span class="text-sm font-black text-slate-400 ml-1">วัน</span>
                                                </div>
                                            </div>
                                            <div v-if="duration > 0 && !isTemporary" class="w-12 h-12 rounded-full border-[3px] border-indigo-50 border-t-indigo-500 animate-spin flex-shrink-0"></div>
                                            <div v-else class="w-12 h-12 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center border border-slate-100 flex-shrink-0">
                                                <i data-lucide="clock" class="w-5 h-5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Validation Warning -->
                                <div class="bg-amber-50/80 rounded-[2rem] p-5 border border-amber-100/80 hover:shadow-md transition-shadow">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 rounded-[1rem] bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="shield-alert" class="w-5 h-5"></i>
                                        </div>
                                        <div class="mt-0.5">
                                            <p class="text-sm font-black text-amber-900 tracking-tight">ข้อควรทราบก่อนยืนยัน</p>
                                            <p class="text-[11px] font-medium text-amber-700/80 leading-relaxed mt-1">กรุณาตรวจสอบสิทธิ์การลาคงเหลือของท่านให้เพียงพอกับจำนวนวันลาที่ระบุ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Province Datalist -->
        <datalist id="provinces">
            <option v-for="p in provinces" :key="p" :value="p"></option>
        </datalist>
    </AppLayout>
</template>

<style scoped>
.premium-bg {
    min-height: 100vh;
    background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
        radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
}
.step-active {
    background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
    box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
}
.type-card-active {
    background: white;
    border-color: #4f46e5;
    box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.1);
    transform: translateY(-4px) scale(1.02);
}
.premium-input {
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(226, 232, 240, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.premium-input:focus {
    background: white;
    border-color: #4338ca;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    outline: none;
}
.balance-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.85) 0%, rgba(248,250,252,0.9) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(226, 232, 240, 0.6);
}
.balance-progress { transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1); }
@keyframes slide-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.5s ease-out forwards; }
@keyframes fade-in-scale { from { opacity: 0; transform: scale(0.95) translateY(8px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.animate-fade-in-scale { animation: fade-in-scale 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
</style>

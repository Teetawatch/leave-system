<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, computed, nextTick } from 'vue';
import { toastSuccess, toastError } from '@/utils/swal';

const props = defineProps({ leaveTypes: Array });

const form = useForm({
    leave_types: Object.fromEntries((props.leaveTypes || []).map(lt => [lt.id, {
        max_days: lt.max_days_per_year, 
        advance_notice_days: lt.advance_notice_days || 0,
        max_retroactive_days: lt.max_retroactive_days || 0,
        requires_advance_notice: !!lt.requires_advance_notice, 
        enforce_advance_notice: !!lt.enforce_advance_notice,
        allows_retroactive: !!lt.allows_retroactive, 
        enforce_retroactive_check: !!lt.enforce_retroactive_check,
        enforce_balance_check: !!lt.enforce_balance_check, 
        requires_file: !!lt.requires_file,
    }])),
});

function submit() {
    form.put('/settings', {
        preserveScroll: true,
        onSuccess: () => toastSuccess('บันทึกการตั้งค่าเรียบร้อยแล้ว'),
        onError: () => toastError('เกิดข้อผิดพลาดในการบันทึกข้อมูล'),
    });
}

const expanded = ref(Object.fromEntries(props.leaveTypes.map((lt, i) => [lt.id, i === 0])));
function toggle(id) { 
    expanded.value[id] = !expanded.value[id]; 
    if (expanded.value[id]) {
        nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
    }
}

const allExpanded = computed(() => Object.values(expanded.value).every(v => v));
function toggleAll() {
    const target = !allExpanded.value;
    props.leaveTypes.forEach(lt => expanded.value[lt.id] = target);
    nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
}

const colors = ['indigo', 'emerald', 'amber', 'rose', 'violet', 'cyan', 'orange', 'teal'];
const icons = { sick: 'thermometer', personal: 'briefcase', vacation: 'palmtree', temporary: 'clock', 'official-duty': 'landmark' };

onMounted(() => { 
    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100); 
});
</script>

<template>
    <AppLayout title="ตั้งค่าระบบ">
        <div class="premium-bg -m-4 md:-m-8 pb-32 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-indigo-100/30 rounded-full blur-[120px] -mr-96 -mt-96"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-violet-100/30 rounded-full blur-[100px] -ml-48 -mb-48"></div>

            <!-- Header -->
            <div class="relative pt-16 pb-32">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                        <div>
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 text-indigo-600 text-[11px] font-black uppercase tracking-[0.2em] mb-6 shadow-sm border border-indigo-100">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                ตั้งค่าระบบ
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight leading-none mb-4">
                                การตั้งค่า <span class="text-indigo-600">ระบบ</span>
                            </h1>
                            <p class="text-slate-500 font-medium text-lg max-w-xl leading-relaxed">กำหนดค่าพารามิเตอร์ของระบบ ปรับแต่งเงื่อนไขประเภทการลา เปิด/ปิด กฎเกณฑ์ต่างๆ</p>
                        </div>
                        <div class="flex items-end gap-3">
                            <button type="button" @click="toggleAll" 
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-white/50 backdrop-blur-md border border-white text-slate-700 font-black text-sm hover:bg-white hover:shadow-xl transition-all group">
                                <i :data-lucide="allExpanded ? 'fold-vertical' : 'unfold-vertical'" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                                {{ allExpanded ? 'ย่อทั้งหมด' : 'ขยายทั้งหมด' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20 space-y-8">
                    <div v-for="(lt, index) in leaveTypes" :key="lt.id"
                        class="glass-panel rounded-[2rem] overflow-hidden setting-card">
                        <!-- Card Header (Dark) -->
                        <div class="bg-slate-900 px-6 sm:px-10 py-6 flex items-center gap-4 sm:gap-6 cursor-pointer" @click="toggle(lt.id)">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center border shadow-inner flex-shrink-0"
                                :class="`bg-${colors[index % colors.length]}-500/20 text-${colors[index % colors.length]}-400 border-${colors[index % colors.length]}-500/20`">
                                <i :data-lucide="icons[lt.slug] || 'file-text'" class="w-6 h-6 sm:w-7 sm:h-7"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg sm:text-xl font-black text-white tracking-tight">{{ lt.name }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                    <span class="text-[10px] font-black text-slate-500 tracking-[0.15em] uppercase">{{ lt.slug }}</span>
                                    <span class="text-slate-700">•</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                        :class="form.leave_types[lt.id].enforce_advance_notice && form.leave_types[lt.id].requires_advance_notice ? 'bg-indigo-500/20 text-indigo-400' : 'bg-slate-700 text-slate-500'">
                                        ล่วงหน้า {{ form.leave_types[lt.id].enforce_advance_notice && form.leave_types[lt.id].requires_advance_notice ? '✓' : '✗' }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                        :class="form.leave_types[lt.id].enforce_balance_check ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-700 text-slate-500'">
                                        วันลา {{ form.leave_types[lt.id].enforce_balance_check ? '✓' : '✗' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <div class="text-right hidden sm:block">
                                    <div class="text-3xl font-black text-white">{{ form.leave_types[lt.id].max_days }}</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">วัน/ปี</div>
                                </div>
                                <i data-lucide="chevron-down" class="w-5 h-5 text-slate-500 transition-transform duration-300" :class="expanded[lt.id] && 'rotate-180'"></i>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div v-show="expanded[lt.id]" class="p-6 sm:p-10 space-y-8">
                            <!-- Basic Settings -->
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center" :class="`bg-${colors[index % colors.length]}-50 text-${colors[index % colors.length]}-600`">
                                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">การตั้งค่าพื้นฐาน</h4>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">จำนวนวันลาสูงสุด/ปี</label>
                                        <div class="flex items-center gap-3">
                                            <input v-model="form.leave_types[lt.id].max_days" type="number" min="0"
                                                class="number-input block w-full rounded-xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-center font-black text-2xl text-slate-800 py-3 transition-all"
                                                :class="form.errors[`leave_types.${lt.id}.max_days`] && 'border-rose-500 ring-rose-500/10'">
                                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">วัน</span>
                                        </div>
                                        <div v-if="form.errors[`leave_types.${lt.id}.max_days`]" class="text-[10px] font-bold text-rose-500 mt-2 ml-1">
                                            {{ form.errors[`leave_types.${lt.id}.max_days`] }}
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-3">ต้องแนบไฟล์</label>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-sm font-bold" :class="form.leave_types[lt.id].requires_file ? 'text-indigo-600' : 'text-slate-400'">{{ form.leave_types[lt.id].requires_file ? 'ต้องแนบ' : 'ไม่ต้องแนบ' }}</span>
                                            <label class="toggle-switch">
                                                <input type="checkbox" v-model="form.leave_types[lt.id].requires_file">
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Advance Notice -->
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><i data-lucide="calendar-clock" class="w-4 h-4"></i></div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">เงื่อนไขการลาล่วงหน้า</h4>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center"><i data-lucide="calendar-plus" class="w-5 h-5"></i></div>
                                            <div><div class="font-bold text-slate-800 text-sm">กำหนดให้ต้องลาล่วงหน้า</div><div class="text-xs text-slate-400 mt-0.5">เปิดใช้งานเงื่อนไขการยื่นล่วงหน้า</div></div>
                                        </div>
                                        <label class="toggle-switch"><input type="checkbox" v-model="form.leave_types[lt.id].requires_advance_notice"><span class="toggle-slider"></span></label>
                                    </div>
                                    <div :class="!form.leave_types[lt.id].requires_advance_notice && 'opacity-45 pointer-events-none'">
                                        <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center"><i data-lucide="hash" class="w-5 h-5"></i></div>
                                                <div><div class="font-bold text-slate-800 text-sm">จำนวนวันล่วงหน้า</div><div class="text-xs text-slate-400 mt-0.5">ต้องยื่นก่อนวันลากี่วัน</div></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input v-model="form.leave_types[lt.id].advance_notice_days" type="number" min="0"
                                                    class="number-input w-20 rounded-xl border-slate-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 text-center font-black text-xl text-slate-800 py-2 transition-all"
                                                    :class="form.errors[`leave_types.${lt.id}.advance_notice_days`] && 'border-rose-500 ring-rose-500/10'">
                                                <span class="text-xs font-bold text-slate-400">วัน</span>
                                            </div>
                                        </div>
                                        <div v-if="form.errors[`leave_types.${lt.id}.advance_notice_days`]" class="text-[10px] font-bold text-rose-500 mt-2 text-right">
                                            {{ form.errors[`leave_types.${lt.id}.advance_notice_days`] }}
                                        </div>
                                    </div>
                                    <div :class="!form.leave_types[lt.id].requires_advance_notice && 'opacity-45 pointer-events-none'">
                                        <div class="flex items-center justify-between bg-indigo-50/50 rounded-2xl p-5 border border-indigo-100/50">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center"><i data-lucide="shield-check" class="w-5 h-5"></i></div>
                                                <div><div class="font-bold text-slate-800 text-sm">บังคับใช้เงื่อนไขล่วงหน้า</div><div class="text-xs text-slate-400 mt-0.5">ปิด = อนุญาตให้ลาได้โดยไม่ต้องล่วงหน้า</div></div>
                                            </div>
                                            <label class="toggle-switch"><input type="checkbox" v-model="form.leave_types[lt.id].enforce_advance_notice"><span class="toggle-slider"></span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Retroactive -->
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><i data-lucide="history" class="w-4 h-4"></i></div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">เงื่อนไขการลาย้อนหลัง</h4>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i data-lucide="undo-2" class="w-5 h-5"></i></div>
                                            <div><div class="font-bold text-slate-800 text-sm">อนุญาตให้ลาย้อนหลัง</div><div class="text-xs text-slate-400 mt-0.5">เปิด = สามารถยื่นลาย้อนหลังได้</div></div>
                                        </div>
                                        <label class="toggle-switch toggle-amber"><input type="checkbox" v-model="form.leave_types[lt.id].allows_retroactive"><span class="toggle-slider"></span></label>
                                    </div>
                                    <div :class="!form.leave_types[lt.id].allows_retroactive && 'opacity-45 pointer-events-none'">
                                        <div class="flex items-center justify-between bg-slate-50 rounded-2xl p-5 border border-slate-100">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center"><i data-lucide="hash" class="w-5 h-5"></i></div>
                                                <div><div class="font-bold text-slate-800 text-sm">ย้อนหลังได้สูงสุด</div><div class="text-xs text-slate-400 mt-0.5">จำนวนวันที่อนุญาตให้ย้อนหลังได้</div></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input v-model="form.leave_types[lt.id].max_retroactive_days" type="number" min="0"
                                                    class="number-input w-20 rounded-xl border-slate-200 bg-white shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 text-center font-black text-xl text-slate-800 py-2 transition-all"
                                                    :class="form.errors[`leave_types.${lt.id}.max_retroactive_days`] && 'border-rose-500 ring-rose-500/10'">
                                                <span class="text-xs font-bold text-slate-400">วัน</span>
                                            </div>
                                        </div>
                                        <div v-if="form.errors[`leave_types.${lt.id}.max_retroactive_days`]" class="text-[10px] font-bold text-rose-500 mt-2 text-right">
                                            {{ form.errors[`leave_types.${lt.id}.max_retroactive_days`] }}
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between bg-amber-50/50 rounded-2xl p-5 border border-amber-100/50">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center"><i data-lucide="shield-check" class="w-5 h-5"></i></div>
                                            <div><div class="font-bold text-slate-800 text-sm">บังคับใช้เงื่อนไขย้อนหลัง</div><div class="text-xs text-slate-400 mt-0.5">ปิด = ไม่ตรวจสอบเงื่อนไขย้อนหลัง</div></div>
                                        </div>
                                        <label class="toggle-switch toggle-amber"><input type="checkbox" v-model="form.leave_types[lt.id].enforce_retroactive_check"><span class="toggle-slider"></span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                            <!-- Balance Check -->
                            <div>
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i data-lucide="calculator" class="w-4 h-4"></i></div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">เงื่อนไขตรวจสอบวันลาคงเหลือ</h4>
                                </div>
                                <div class="flex items-center justify-between bg-emerald-50/50 rounded-2xl p-5 border border-emerald-100/50">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center"><i data-lucide="shield-check" class="w-5 h-5"></i></div>
                                        <div><div class="font-bold text-slate-800 text-sm">บังคับตรวจสอบวันลาคงเหลือ</div><div class="text-xs text-slate-400 mt-0.5">ปิด = อนุญาตให้ลาได้แม้วันลาไม่เพียงพอ</div></div>
                                    </div>
                                    <label class="toggle-switch toggle-emerald"><input type="checkbox" v-model="form.leave_types[lt.id].enforce_balance_check"><span class="toggle-slider"></span></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Bar (Sticky) -->
                    <div class="sticky bottom-8 z-50 mt-12">
                        <div class="glass-panel rounded-[2.5rem] p-4 flex items-center justify-between shadow-2xl border-white/50">
                            <div class="hidden md:flex items-center gap-4 ml-6">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i data-lucide="info" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-black text-slate-800">ตรวจสอบความถูกต้อง</div>
                                    <div class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">ระบบจะบังคับใช้เงื่อนไขทันทีที่บันทึก</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 w-full md:w-auto">
                                <button type="button" @click="form.reset()" :disabled="!form.isDirty || form.processing"
                                    class="flex-1 md:flex-none px-8 py-4 text-slate-500 font-black text-sm hover:text-rose-600 transition-colors disabled:opacity-30 disabled:pointer-events-none">
                                    คืนค่าเดิม
                                </button>
                                <button type="submit" :disabled="form.processing || !form.isDirty"
                                    class="flex-[2] md:flex-none group inline-flex items-center justify-center px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-sm rounded-2xl shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 hover:-translate-y-1 uppercase tracking-widest gap-3 disabled:opacity-50 disabled:translate-y-0 disabled:shadow-none">
                                    <i data-lucide="save" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                    {{ form.processing ? 'กำลังบันทึก...' : 'บันทึกการตั้งค่า' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.premium-bg {
    min-height: 100vh;
    background: radial-gradient(circle at 10% 20%, rgba(79, 70, 229, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
}
.glass-panel {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.04);
}
.setting-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.setting-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.08); }
.number-input { -moz-appearance: textfield; }
.number-input::-webkit-outer-spin-button,
.number-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.toggle-switch { position: relative; display: inline-block; width: 48px; height: 26px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background-color: #e2e8f0; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 26px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
}
.toggle-slider:before {
    position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px;
    background-color: white; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 50%; box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.toggle-switch input:checked + .toggle-slider {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(99, 102, 241, 0.3);
}
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }
.toggle-switch.toggle-emerald input:checked + .toggle-slider { background: linear-gradient(135deg, #10b981, #059669); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(16, 185, 129, 0.3); }
.toggle-switch.toggle-amber input:checked + .toggle-slider { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: inset 0 2px 4px rgba(0,0,0,0.1), 0 0 12px rgba(245, 158, 11, 0.3); }
</style>

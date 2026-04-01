<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'ค้นหาและเลือก...',
    },
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref(null);

const selectedUser = computed(() => {
    return props.options.find(u => u.id === props.modelValue) || null;
});

const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    const q = searchQuery.value.toLowerCase().replace(/\s+/g, '');
    return props.options.filter(u => {
        const fullName = `${u.rank || ''}${u.name || ''}`.toLowerCase().replace(/\s+/g, '');
        return fullName.includes(q);
    });
});

function selectUser(user) {
    if (user) {
        emit('update:modelValue', user.id);
        searchQuery.value = '';
    } else {
        emit('update:modelValue', null);
    }
    isOpen.value = false;
}

function handleClickOutside(e) {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false;
        searchQuery.value = '';
    }
}

// Re-initialize lucide icons if dropdown opens
watch(isOpen, async (val) => {
    if (val) {
        await nextTick();
        if (window.lucide) window.lucide.createIcons();
    }
});

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative w-full" ref="dropdownRef">
        <div class="relative flex items-center">
            <input 
                type="text" 
                :placeholder="selectedUser ? (selectedUser.rank + ' ' + selectedUser.name) : placeholder" 
                v-model="searchQuery"
                @focus="isOpen = true"
                class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 bg-white text-xs font-medium focus:border-indigo-500 focus:ring-2 outline-none transition-all placeholder-opacity-100"
                :class="[
                    selectedUser && !searchQuery ? 'placeholder-slate-800' : 'placeholder-slate-400',
                    'text-slate-800'
                ]"
            />
            
            <button 
                v-if="props.modelValue && !isOpen" 
                @click.stop="selectUser(null)" 
                class="absolute right-2 text-slate-400 hover:text-rose-500"
                type="button"
                title="ลบ"
            >
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
            <i v-else data-lucide="chevron-down" class="absolute right-2 w-4 h-4 text-slate-400 pointer-events-none"></i>
        </div>

        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div 
                v-if="isOpen" 
                class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] max-h-56 overflow-y-auto"
            >
                <div 
                    v-if="filteredOptions.length === 0" 
                    class="px-3 py-4 text-xs text-slate-500 text-center"
                >
                    ไม่พบข้อมูล
                </div>
                <!-- Optional clear button at top for easy clearing -->
                <button 
                    v-if="props.modelValue"
                    type="button"
                    @click="selectUser(null)"
                    class="w-full text-left px-3 py-2 text-xs text-rose-500 hover:bg-rose-50 transition-colors border-b border-slate-100 font-bold flex items-center"
                >
                    <i data-lucide="x" class="w-3 h-3 mr-1.5"></i> ลบผู้ที่ถูกเลือก
                </button>

                <button 
                    v-for="u in filteredOptions" 
                    :key="u.id" 
                    type="button"
                    @click="selectUser(u)"
                    class="w-full text-left px-3 py-2 text-xs hover:bg-slate-50 transition-colors flex items-center justify-between"
                    :class="{'bg-indigo-50 text-indigo-700 font-bold': props.modelValue === u.id}"
                >
                    <span>{{ u.rank }} {{ u.name }}</span>
                    <i v-if="props.modelValue === u.id" data-lucide="check" class="w-3.5 h-3.5 text-indigo-600"></i>
                </button>
            </div>
        </Transition>
    </div>
</template>

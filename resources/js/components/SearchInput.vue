<template>
  <div class="relative" :class="containerClass">
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    <input
      :value="modelValue"
      @input="handleInput"
      type="text"
      :placeholder="placeholder"
      class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm transition-colors"
      :class="inputClass"
    />
    <button v-if="modelValue" @click="clear" class="absolute right-3 top-1/2 -translate-y-1/2 p-0.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Rechercher...' },
  debounce: { type: Number, default: 300 },
  containerClass: { type: String, default: '' },
  inputClass: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue', 'search']);
let timer = null;

function handleInput(e) {
  const val = e.target.value;
  emit('update:modelValue', val);
  clearTimeout(timer);
  timer = setTimeout(() => emit('search', val), props.debounce);
}

function clear() {
  emit('update:modelValue', '');
  clearTimeout(timer);
  emit('search', '');
}
</script>

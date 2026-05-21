<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700" :class="[paddingClass, hoverClass]">
    <div v-if="$slots.header" class="flex items-center justify-between mb-4">
      <slot name="header" />
    </div>
    <slot />
    <div v-if="$slots.footer" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  padding: { type: [String, Boolean], default: true },
  hover: { type: Boolean, default: false },
});

const paddingClass = computed(() => {
  if (props.padding === false) return '';
  if (typeof props.padding === 'string') return props.padding;
  return 'p-6';
});

const hoverClass = computed(() => props.hover ? 'hover:shadow-md transition-shadow cursor-pointer' : '');
</script>

<template>
  <div v-if="totalPages > 1" class="flex items-center justify-between mt-6">
    <p class="text-sm text-gray-600 dark:text-gray-400">Showing {{ from }}-{{ to }} of {{ total }}</p>
    <div class="flex space-x-1">
      <button @click="$emit('page', currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50">Prev</button>
      <button v-for="p in visiblePages" :key="p" @click="$emit('page', p)" :class="[p === currentPage ? 'bg-indigo-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700']" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600">{{ p }}</button>
      <button @click="$emit('page', currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50">Next</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ currentPage: Number, totalPages: Number, from: Number, to: Number, total: Number });
defineEmits(['page']);
const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, props.currentPage - 2);
  const end = Math.min(props.totalPages, props.currentPage + 2);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});
</script>

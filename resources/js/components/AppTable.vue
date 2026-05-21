<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div v-if="loading" class="p-8">
      <div class="space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center space-x-4 animate-pulse">
          <div v-for="col in columns" :key="col.key" class="h-4 bg-gray-200 dark:bg-gray-700 rounded" :style="{ width: col.width || '25%' }"></div>
        </div>
      </div>
    </div>

    <div v-else-if="data.length === 0" class="flex flex-col items-center justify-center py-16">
      <slot name="empty">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">{{ emptyText }}</p>
      </slot>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <th v-for="col in columns" :key="col.key" @click="col.sortable && toggleSort(col.key)" class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium select-none" :class="{ 'cursor-pointer hover:text-gray-700 dark:hover:text-gray-200': col.sortable }">
              <div class="flex items-center space-x-1">
                <span>{{ col.label }}</span>
                <svg v-if="col.sortable && sortKey === col.key" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"/></svg>
              </div>
            </th>
            <th v-if="$slots.actions" class="text-right px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, rowIdx) in sortedData" :key="row.id || rowIdx" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors" :class="{ 'cursor-pointer': !!$attrs.onClick }" @click="$emit('rowClick', row)">
            <td v-for="col in columns" :key="col.key" class="px-4 py-3" :class="col.class">
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                <span class="text-gray-900 dark:text-white">{{ row[col.key] }}</span>
              </slot>
            </td>
            <td v-if="$slots.actions" class="px-4 py-3 text-right">
              <slot name="actions" :row="row" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="$slots.footer" class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  columns: { type: Array, required: true },
  data: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  emptyText: { type: String, default: 'Aucune donnée trouvée' },
  defaultSort: { type: String, default: '' },
});

defineEmits(['rowClick']);

const sortKey = ref(props.defaultSort);
const sortDir = ref('asc');

function toggleSort(key) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortDir.value = 'asc';
  }
}

const sortedData = computed(() => {
  if (!sortKey.value) return props.data;
  return [...props.data].sort((a, b) => {
    const aVal = a[sortKey.value];
    const bVal = b[sortKey.value];
    if (aVal == null) return 1;
    if (bVal == null) return -1;
    const cmp = typeof aVal === 'string' ? aVal.localeCompare(bVal) : aVal - bVal;
    return sortDir.value === 'asc' ? cmp : -cmp;
  });
});
</script>

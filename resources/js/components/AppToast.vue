<template>
  <teleport to="body">
    <div class="fixed top-4 right-4 z-[100] flex flex-col space-y-2 pointer-events-none">
      <transition-group name="toast">
        <div v-for="toast in toasts" :key="toast.id" class="pointer-events-auto flex items-start space-x-3 px-4 py-3 rounded-xl shadow-xl border max-w-sm" :class="toastClasses(toast.type)">
          <span v-html="toastIcon(toast.type)" class="w-5 h-5 shrink-0 mt-0.5"></span>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium" :class="toastTextClass(toast.type)">{{ toast.message }}</p>
            <p v-if="toast.description" class="text-xs mt-0.5 opacity-80">{{ toast.description }}</p>
          </div>
          <button @click="remove(toast.id)" class="shrink-0 p-0.5 opacity-60 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script setup>
import { ref } from 'vue';

const toasts = ref([]);
let nextId = 0;

function add({ message, description, type = 'success', duration = 4000 }) {
  const id = ++nextId;
  toasts.value.push({ id, message, description, type });
  if (duration > 0) {
    setTimeout(() => remove(id), duration);
  }
  return id;
}

function remove(id) {
  toasts.value = toasts.value.filter(t => t.id !== id);
}

function success(message, description) { return add({ message, description, type: 'success' }); }
function error(message, description) { return add({ message, description, type: 'error', duration: 6000 }); }
function warning(message, description) { return add({ message, description, type: 'warning', duration: 5000 }); }
function info(message, description) { return add({ message, description, type: 'info', duration: 4000 }); }

function toastClasses(type) {
  return {
    success: 'bg-emerald-50 dark:bg-emerald-900/50 border-emerald-200 dark:border-emerald-800',
    error: 'bg-red-50 dark:bg-red-900/50 border-red-200 dark:border-red-800',
    warning: 'bg-amber-50 dark:bg-amber-900/50 border-amber-200 dark:border-amber-800',
    info: 'bg-blue-50 dark:bg-blue-900/50 border-blue-200 dark:border-blue-800',
  }[type] || 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700';
}

function toastTextClass(type) {
  return {
    success: 'text-emerald-800 dark:text-emerald-200',
    error: 'text-red-800 dark:text-red-200',
    warning: 'text-amber-800 dark:text-amber-200',
    info: 'text-blue-800 dark:text-blue-200',
  }[type] || 'text-gray-800 dark:text-gray-200';
}

function toastIcon(type) {
  return {
    success: '<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    error: '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
    warning: '<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>',
    info: '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>',
  }[type] || '';
}

defineExpose({ add, remove, success, error, warning, info });
</script>

<style scoped>
.toast-enter-active { transition: all 0.3s ease; }
.toast-leave-active { transition: all 0.2s ease; }
.toast-enter-from { opacity: 0; transform: translateX(100%); }
.toast-leave-to { opacity: 0; transform: translateX(100%); }
</style>

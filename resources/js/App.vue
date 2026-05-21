<template>
  <div v-if="!authStore.initialized" class="min-h-screen flex items-center justify-center bg-slate-900">
    <div class="text-center">
      <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
      <p class="text-slate-400 text-sm">Chargement...</p>
    </div>
  </div>
  <router-view v-else />
</template>

<script setup>
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();
</script>

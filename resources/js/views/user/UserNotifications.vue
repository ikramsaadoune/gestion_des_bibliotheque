<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
        <p v-if="unreadCount > 0" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ unreadCount }} non lue{{ unreadCount > 1 ? 's' : '' }}</p>
      </div>
      <button @click="markAllRead" v-if="unreadCount > 0" :disabled="markingAll" class="px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 border border-indigo-600 dark:border-indigo-400 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 disabled:opacity-50 transition-colors">
        <svg v-if="markingAll" class="w-4 h-4 animate-spin inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
        {{ markingAll ? '...' : 'Tout marquer comme lu' }}
      </button>
    </div>

    <div v-if="loading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-4 animate-pulse">
        <div class="flex items-start gap-3">
          <div class="w-1 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <p class="text-red-500 dark:text-red-400">{{ error }}</p>
      <button @click="fetchNotifications" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Réessayer</button>
    </div>

    <div v-else-if="notifications.length === 0" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Aucune notification</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Vous serez notifié lorsque des événements importants se produiront.</p>
    </div>

    <div v-else class="space-y-2">
      <div v-for="n in notifications" :key="n.id" @click="markAsRead(n)" :class="['bg-white dark:bg-gray-800 rounded-xl shadow-sm border cursor-pointer transition-all hover:shadow-md', n.read_at ? 'border-gray-200 dark:border-gray-700' : 'border-indigo-200 dark:border-indigo-800']">
        <div class="flex items-stretch">
          <div :class="['w-1.5 shrink-0 rounded-l-xl', n.read_at ? 'bg-transparent' : 'bg-indigo-500']"></div>
          <div class="flex items-start gap-3 p-4 flex-1">
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-medium" :class="n.read_at ? 'text-gray-600 dark:text-gray-400' : 'text-gray-900 dark:text-white'">{{ n.title || n.message }}</p>
                <span v-if="!n.read_at" class="w-2 h-2 bg-indigo-500 rounded-full shrink-0"></span>
              </div>
              <p v-if="(n.title && n.message) || n.description" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ n.description || n.message }}</p>
              <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ formatRelative(n.created_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { notificationsAPI } from '../../services/api';

const notifications = ref([]);
const loading = ref(true);
const error = ref('');
const markingAll = ref(false);

const unreadCount = computed(() => notifications.value.filter(n => !n.read_at).length);

function formatRelative(date) {
  if (!date) return '';
  const d = new Date(date);
  const now = new Date();
  const diff = now - d;
  if (diff < 60000) return 'À l\'instant';
  if (diff < 3600000) return `Il y a ${Math.floor(diff / 60000)} min`;
  if (diff < 86400000) return `Il y a ${Math.floor(diff / 3600000)}h`;
  const days = Math.floor(diff / 86400000);
  if (days < 7) return `Il y a ${days} jour${days > 1 ? 's' : ''}`;
  return d.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
}

async function fetchNotifications() {
  loading.value = true;
  error.value = '';
  try {
    const { data } = await notificationsAPI.all();
    notifications.value = data.data || data;
  } catch (e) {
    error.value = 'Impossible de charger les notifications.';
    notifications.value = [];
  } finally {
    loading.value = false;
  }
}

async function markAsRead(n) {
  if (n.read_at) return;
  try {
    await notificationsAPI.markRead(n.id);
    n.read_at = new Date().toISOString();
  } catch (e) {}
}

async function markAllRead() {
  markingAll.value = true;
  try {
    await notificationsAPI.markAllRead();
    notifications.value.forEach(n => { n.read_at = n.read_at || new Date().toISOString(); });
  } catch (e) {}
  finally { markingAll.value = false; }
}

onMounted(fetchNotifications);
</script>

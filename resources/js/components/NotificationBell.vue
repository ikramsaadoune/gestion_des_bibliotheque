<template>
  <div class="relative" ref="container">
    <button @click="toggleDropdown" class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
      </svg>
      <span v-if="count > 0" class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm">{{ count > 9 ? '9+' : count }}</span>
    </button>

    <div v-if="open" @click.self="open = false" class="fixed inset-0 z-40"></div>

    <Transition name="dropdown">
      <div v-if="open" class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-800">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
          <div class="flex items-center gap-1">
            <button v-if="unreadCount > 0" @click="markAllRead" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium px-2 py-1 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20">Tout lire</button>
            <router-link to="/admin/notifications" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium px-2 py-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">Voir tout</router-link>
          </div>
        </div>

        <div class="overflow-y-auto flex-1">
          <div v-if="loading" class="p-4 space-y-3">
            <div v-for="i in 3" :key="i" class="flex items-start gap-3 animate-pulse">
              <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
              <div class="flex-1 space-y-1.5">
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
              </div>
            </div>
          </div>

          <div v-else-if="items.length === 0" class="flex flex-col items-center py-10">
            <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-xs text-gray-400">Aucune notification</p>
          </div>

          <div v-else>
            <div v-for="notif in items" :key="notif.id" @click="handleClick(notif)" class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors" :class="{ 'bg-indigo-50/30 dark:bg-indigo-900/10': !notif.read_at }">
              <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs" :class="iconBg(notif.type)">
                <span v-html="iconSvg(notif.type)"></span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-900 dark:text-white line-clamp-2" :class="{ 'font-semibold': !notif.read_at }">{{ notif.data?.message }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ timeAgo(notif.created_at) }}</p>
              </div>
              <div v-if="!notif.read_at" class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0 mt-1.5"></div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { notificationsAPI } from '../services/api';

const router = useRouter();
const open = ref(false);
const loading = ref(false);
const items = ref([]);
const count = ref(0);
const unreadCount = ref(0);
const container = ref(null);
let interval = null;

function iconBg(type) {
  if (type?.includes('Confirmed')) return 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400';
  if (type?.includes('Refused')) return 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400';
  if (type?.includes('Request')) return 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400';
  return 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400';
}

function iconSvg(type) {
  if (type?.includes('Confirmed')) return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
  if (type?.includes('Refused')) return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
  if (type?.includes('Request')) return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>';
  return '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
}

function timeAgo(date) {
  if (!date) return '';
  const d = new Date(date);
  const now = new Date();
  const diff = now - d;
  if (diff < 60000) return 'À l\'instant';
  if (diff < 3600000) return `${Math.floor(diff / 60000)} min`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h`;
  const days = Math.floor(diff / 86400000);
  if (days < 7) return `Il y a ${days}j`;
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
}

function toggleDropdown() {
  open.value = !open.value;
  if (open.value) fetchItems();
}

async function fetchItems() {
  loading.value = true;
  try {
    const { data } = await notificationsAPI.allAdmin({ type: 'unread' });
    items.value = data.data || [];
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

async function fetchCount() {
  try {
    const { data } = await notificationsAPI.unreadCountAdmin();
    count.value = data.count || 0;
    unreadCount.value = data.count || 0;
  } catch (e) { /* ignore */ }
}

async function markAllRead() {
  try {
    await notificationsAPI.markAllReadAdmin();
    items.value = [];
    count.value = 0;
    unreadCount.value = 0;
  } catch (e) { console.error(e); }
}

function handleClick(notif) {
  if (!notif.read_at) {
    notificationsAPI.markReadAdmin(notif.id).catch(() => {});
    notif.read_at = new Date().toISOString();
    count.value = Math.max(0, count.value - 1);
    unreadCount.value = Math.max(0, unreadCount.value - 1);
    items.value = items.value.filter(n => n.id !== notif.id);
  }
  open.value = false;
}

onMounted(() => {
  fetchCount();
  interval = setInterval(fetchCount, 30000);
});

onUnmounted(() => {
  if (interval) clearInterval(interval);
});
</script>

<style scoped>
.dropdown-enter-active, .dropdown-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>

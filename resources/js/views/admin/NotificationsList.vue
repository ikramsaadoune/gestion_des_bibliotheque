<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Centre de notifications</p>
      </div>
      <div class="flex items-center gap-2">
        <select v-model="filter" @change="fetchNotifications(1)" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl text-sm dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
          <option value="all">Toutes</option>
          <option value="unread">Non lues</option>
        </select>
        <button @click="markAllRead" v-if="unreadCount > 0" class="inline-flex items-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-colors">
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Tout marquer lu
        </button>
        <button @click="showDeleteMenu = !showDeleteMenu" class="relative inline-flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
          <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          Supprimer
          <div v-if="showDeleteMenu" @click.stop class="absolute right-0 top-full mt-1 w-52 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-1.5 z-50">
            <button @click="deleteAllRead" class="flex items-center w-full px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Supprimer les lues</button>
            <button @click="deleteAll" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Tout supprimer</button>
          </div>
        </button>
      </div>
    </div>

    <div v-if="loading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="bg-white dark:bg-gray-800 rounded-xl p-5 animate-pulse border border-gray-100 dark:border-gray-700">
        <div class="flex items-start space-x-4">
          <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="notifications.length === 0" class="flex flex-col items-center justify-center py-24">
      <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
      </div>
      <p class="text-gray-400 dark:text-gray-500 text-lg font-medium">Aucune notification</p>
      <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Vous serez notifié des activités importantes</p>
    </div>

    <div v-else class="space-y-2">
      <div v-for="notif in notifications" :key="notif.id" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md" :class="{ 'border-l-4 border-indigo-500': !notif.read_at }">
        <div class="flex items-start p-4 gap-4">
          <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" :class="iconBg(notif.type)">
            <span v-html="iconSvg(notif.type)"></span>
          </div>
          <div class="flex-1 min-w-0 cursor-pointer" @click="markRead(notif)">
            <div class="flex items-start justify-between">
              <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-1" :class="{ 'font-semibold': !notif.read_at }">{{ notif.data?.message }}</p>
              <span v-if="!notif.read_at" class="ml-2 shrink-0 inline-flex items-center px-2 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400 rounded-full">Nouveau</span>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex items-center">
              <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              {{ formatDate(notif.created_at) }}
            </p>
          </div>
          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button v-if="!notif.read_at" @click="markRead(notif)" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg" title="Marquer comme lu">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>
            <button @click="deleteNotification(notif.id)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" title="Supprimer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <Pagination v-if="meta" :currentPage="meta.current_page" :totalPages="meta.last_page" :from="meta.from" :to="meta.to" :total="meta.total" @page="fetchNotifications" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { notificationsAPI } from '../../services/api';
import Pagination from '../../components/Pagination.vue';

const notifications = ref([]);
const meta = ref(null);
const loading = ref(true);
const filter = ref('all');
const showDeleteMenu = ref(false);

const unreadCount = computed(() => notifications.value.filter(n => !n.read_at).length);

function formatDate(date) {
  if (!date) return '';
  const d = new Date(date);
  const now = new Date();
  const diff = now - d;
  if (diff < 60000) return 'À l\'instant';
  if (diff < 3600000) return `${Math.floor(diff / 60000)} min`;
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}h`;
  const days = Math.floor(diff / 86400000);
  if (days < 7) return `Il y a ${days} jour${days > 1 ? 's' : ''}`;
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: days > 365 ? 'numeric' : undefined });
}

function iconBg(type) {
  if (type?.includes('Confirmed')) return 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400';
  if (type?.includes('Refused')) return 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400';
  if (type?.includes('Request')) return 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400';
  return 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400';
}

function iconSvg(type) {
  if (type?.includes('Confirmed')) {
    return '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
  }
  if (type?.includes('Refused')) {
    return '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
  }
  if (type?.includes('Request')) {
    return '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>';
  }
  return '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>';
}

async function fetchNotifications(page = 1) {
  loading.value = true;
  showDeleteMenu.value = false;
  try {
    const params = { page };
    if (filter.value === 'unread') params.type = 'unread';
    const { data } = await notificationsAPI.allAdmin(params);
    notifications.value = data.data || [];
    meta.value = data.meta || null;
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

async function markRead(notif) {
  if (notif.read_at) return;
  try {
    await notificationsAPI.markReadAdmin(notif.id);
    notif.read_at = new Date().toISOString();
  } catch (e) { console.error(e); }
}

async function markAllRead() {
  try {
    await notificationsAPI.markAllReadAdmin();
    notifications.value.forEach(n => { n.read_at = n.read_at || new Date().toISOString(); });
  } catch (e) { console.error(e); }
}

async function deleteNotification(id) {
  try {
    await notificationsAPI.deleteAdmin(id);
    notifications.value = notifications.value.filter(n => n.id !== id);
  } catch (e) { console.error(e); }
}

async function deleteAllRead() {
  try {
    await notificationsAPI.deleteAllReadAdmin();
    notifications.value = notifications.value.filter(n => !n.read_at);
  } catch (e) { console.error(e); }
}

async function deleteAll() {
  if (!confirm('Supprimer toutes les notifications ?')) return;
  try {
    for (const n of notifications.value) {
      await notificationsAPI.deleteAdmin(n.id);
    }
    notifications.value = [];
  } catch (e) { console.error(e); }
}

onMounted(() => fetchNotifications());
</script>

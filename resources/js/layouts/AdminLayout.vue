<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950">
    <div v-if="sidebarOpen" @click="appStore.toggleSidebar()" class="fixed inset-0 z-20 bg-black/60 backdrop-blur-sm lg:hidden"></div>

    <aside :class="[
      'fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transform transition-all duration-300 ease-in-out lg:translate-x-0',
      sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'
    ]">
      <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100 dark:border-gray-800">
        <router-link to="/admin" class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          </div>
          <div>
            <h1 class="text-base font-bold text-gray-900 dark:text-white">Bibliothèque</h1>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 -mt-0.5">Panneau d'administration</p>
          </div>
        </router-link>
        <button @click="appStore.toggleSidebar()" class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <nav class="mt-4 px-3 space-y-0.5 overflow-y-auto" style="max-height: calc(100vh - 4rem)">
        <p class="px-3 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Général</p>
        <router-link v-for="item in navItems.main" :key="item.path" :to="item.path" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200" :class="isActive(item.path) ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200'">
          <span class="mr-3 w-5 h-5 flex items-center justify-center" v-html="item.icon"></span>
          <span>{{ item.label }}</span>
        </router-link>

        <p class="px-3 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mt-6 mb-2">Gestion</p>
        <router-link v-for="item in navItems.gestion" :key="item.path" :to="item.path" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200" :class="isActive(item.path) ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200'">
          <span class="mr-3 w-5 h-5 flex items-center justify-center" v-html="item.icon"></span>
          <span>{{ item.label }}</span>
        </router-link>

        <p class="px-3 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mt-6 mb-2">Système</p>
        <router-link v-for="item in navItems.system" :key="item.path" :to="item.path" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200" :class="isActive(item.path) ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200'">
          <span class="mr-3 w-5 h-5 flex items-center justify-center" v-html="item.icon"></span>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>
    </aside>

    <div class="lg:pl-64">
      <header class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6">
          <div class="flex items-center space-x-3">
            <button @click="appStore.toggleSidebar()" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
              <router-link to="/admin" class="hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard</router-link>
              <template v-if="$route.path !== '/admin'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900 dark:text-white font-medium">{{ currentPageName }}</span>
              </template>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <NotificationBell />
            <button @click="appStore.toggleDarkMode()" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
              <svg v-if="!appStore.isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/></svg>
            </button>

            <div class="relative">
              <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">
                  {{ (authStore.user?.name || 'A')[0] }}
                </div>
                <div class="hidden md:block text-left">
                  <p class="text-sm font-medium text-gray-700 dark:text-gray-300 leading-tight">{{ authStore.user?.name }}</p>
                  <p class="text-[10px] text-gray-500 dark:text-gray-400">Administrateur</p>
                </div>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div v-if="showUserMenu" @click.self="showUserMenu = false" class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-800 py-1.5 z-50">
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                </div>
                <router-link to="/" class="flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Voir le site</router-link>
                <hr class="my-1 border-gray-100 dark:border-gray-800">
                <button @click="logout" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Déconnexion</button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <main class="p-4 sm:p-6 lg:p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useAppStore } from '../stores/app';
import NotificationBell from '../components/NotificationBell.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const appStore = useAppStore();
const showUserMenu = ref(false);

const sidebarOpen = computed(() => appStore.sidebarOpen);

const currentPageName = computed(() => {
  const path = route.path;
  const segments = path.split('/').filter(Boolean);
  if (segments.length <= 1) return '';
  const name = segments[segments.length - 1];
  return name.charAt(0).toUpperCase() + name.slice(1).replace(/-/g, ' ');
});

function isActive(path) {
  if (path === '/admin') return route.path === '/admin';
  return route.path.startsWith(path);
}

const navItems = {
  main: [
    { path: '/admin', label: 'Dashboard', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' },
  ],
  gestion: [
    { path: '/admin/books', label: 'Livres', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
    { path: '/admin/users', label: 'Utilisateurs', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>' },
    { path: '/admin/borrowings', label: 'Emprunts', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>' },

    { path: '/admin/categories', label: 'Catégories', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>' },
    { path: '/admin/reviews', label: 'Avis', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>' },
    { path: '/admin/fines', label: 'Amendes', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' },
  ],
  system: [
    { path: '/admin/notifications', label: 'Notifications', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>' },
    { path: '/admin/settings', label: 'Paramètres', icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' },
  ],
};

async function logout() {
  await authStore.logout();
  router.push('/login');
}
</script>

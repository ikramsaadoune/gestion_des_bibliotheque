<template>
  <div class="min-h-screen bg-slate-50">
    <header class="sticky top-0 z-30 bg-white border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between h-14">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <span class="font-bold text-slate-900">MonBibliothèque</span>
          </router-link>

          <nav class="hidden md:flex items-center space-x-1">
            <router-link to="/books" class="px-3 py-2 text-sm rounded-lg" :class="$route.path.startsWith('/books') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">Explorer</router-link>
            <router-link v-if="authStore.isAuthenticated" to="/my-borrowings" class="px-3 py-2 text-sm rounded-lg" :class="$route.path.startsWith('/my-borrowings') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">Mes emprunts</router-link>
          </nav>

          <div class="flex items-center space-x-2">
            <template v-if="authStore.isAuthenticated">
              <button @click="showMobile = !showMobile" class="md:hidden p-2 text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
              </button>
              <router-link to="/favorites" class="hidden md:block p-2 text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
              </router-link>
              <div class="relative">
                <button @click="showMenu = !showMenu" class="flex items-center space-x-2 p-1.5 rounded-lg hover:bg-slate-100">
                  <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">{{ (authStore.user?.name || 'U')[0] }}</div>
                  <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div v-if="showMenu" @click.self="showMenu = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-50">
                  <router-link to="/profile" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Mon profil</router-link>
                  <router-link to="/my-borrowings" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Mes emprunts</router-link>
                  <router-link to="/favorites" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 md:hidden">Mes favoris</router-link>
                  <hr class="my-1 border-slate-100">
                  <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                </div>
              </div>
            </template>
            <template v-else>
              <router-link to="/login" class="px-4 py-2 text-sm text-blue-600 hover:text-blue-700 font-medium">Connexion</router-link>
              <router-link to="/register" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Inscription</router-link>
            </template>
          </div>
        </div>
      </div>
    </header>

    <div v-if="showMobile" class="md:hidden bg-white border-b border-slate-200 px-4 py-2">
      <nav class="flex flex-col space-y-1">
        <router-link @click="showMobile = false" to="/books" class="px-3 py-2 text-sm rounded-lg" :class="$route.path.startsWith('/books') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-100'">Explorer</router-link>
        <router-link v-if="authStore.isAuthenticated" @click="showMobile = false" to="/my-borrowings" class="px-3 py-2 text-sm rounded-lg" :class="$route.path.startsWith('/my-borrowings') ? 'text-blue-600 bg-blue-50 font-medium' : 'text-slate-600 hover:bg-slate-100'">Mes emprunts</router-link>
      </nav>
    </div>

    <main class="max-w-6xl mx-auto px-4 py-6">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const showMenu = ref(false);
const showMobile = ref(false);

async function logout() {
  await authStore.logout();
  router.push('/login');
}
</script>

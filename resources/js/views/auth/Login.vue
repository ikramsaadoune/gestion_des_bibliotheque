<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 px-4 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="w-full max-w-sm relative z-10">
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-600/30">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">MonBibliothèque</h1>
        <p class="text-blue-200/60 text-sm mt-1">Connectez-vous pour continuer</p>
      </div>
      <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/10">
        <form @submit.prevent="handleLogin" class="space-y-4">
          <div v-if="error" class="p-3 bg-red-500/20 border border-red-500/30 text-red-200 text-sm rounded-lg">{{ error }}</div>
          <div>
            <label class="block text-sm text-blue-200 mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="votre@email.com" required />
          </div>
          <div>
            <label class="block text-sm text-blue-200 mb-1">Mot de passe</label>
            <input v-model="form.password" type="password" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="••••••••" required />
          </div>
          <button type="submit" :disabled="loading" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all disabled:opacity-50 shadow-lg shadow-blue-600/25 flex items-center justify-center">
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ loading ? 'Connexion...' : 'Se connecter' }}
          </button>
        </form>
        <p class="text-center text-blue-200/50 text-sm mt-4">
          Pas de compte ? <router-link to="/register" class="text-blue-400 hover:text-blue-300">Inscrivez-vous</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);
const error = ref('');
const form = reactive({ email: '', password: '' });

async function handleLogin() {
  loading.value = true; error.value = '';
  try {
    const data = await authStore.login(form);
    if (data.user?.role === 'admin') {
      router.push('/admin');
    } else {
      router.push('/');
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Email ou mot de passe incorrect';
  } finally { loading.value = false; }
}
</script>

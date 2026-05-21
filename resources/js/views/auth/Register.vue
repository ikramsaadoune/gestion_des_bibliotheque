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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Créer un compte</h1>
        <p class="text-blue-200/60 text-sm mt-1">Rejoignez MonBibliothèque</p>
      </div>
      <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/10">
        <form @submit.prevent="handleRegister" class="space-y-4">
          <div v-if="error" class="p-3 bg-red-500/20 border border-red-500/30 text-red-200 text-sm rounded-lg">{{ error }}</div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm text-blue-200 mb-1">Prénom</label>
              <input v-model="form.first_name" type="text" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Jean" />
            </div>
            <div>
              <label class="block text-sm text-blue-200 mb-1">Nom</label>
              <input v-model="form.last_name" type="text" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Dupont" />
            </div>
          </div>
          <div>
            <label class="block text-sm text-blue-200 mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="votre@email.com" required />
          </div>
          <div>
            <label class="block text-sm text-blue-200 mb-1">Mot de passe</label>
            <input v-model="form.password" type="password" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="••••••••" required />
          </div>
          <div>
            <label class="block text-sm text-blue-200 mb-1">Confirmer le mot de passe</label>
            <input v-model="form.password_confirmation" type="password" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="••••••••" required />
          </div>
          <div>
            <label class="block text-sm text-blue-200 mb-1">Adresse (optionnelle)</label>
            <input v-model="form.address" type="text" class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="123 rue Exemple, Ville" />
          </div>
          <button type="submit" :disabled="loading" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all disabled:opacity-50 shadow-lg shadow-blue-600/25 flex items-center justify-center">
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ loading ? 'Inscription...' : "S'inscrire" }}
          </button>
        </form>
        <p class="text-center text-blue-200/50 text-sm mt-4">
          Déjà un compte ? <router-link to="/login" class="text-blue-400 hover:text-blue-300">Connectez-vous</router-link>
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
const form = reactive({ first_name: '', last_name: '', email: '', password: '', password_confirmation: '', address: '' });

async function handleRegister() {
  loading.value = true; error.value = '';
  try {
    await authStore.register(form);
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message || "Erreur lors de l'inscription";
  } finally { loading.value = false; }
}
</script>

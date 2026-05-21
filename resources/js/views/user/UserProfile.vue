<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-xl font-bold text-slate-900 mb-6">Mon profil</h1>

    <div v-if="loading" class="space-y-4 animate-pulse">
      <div class="h-24 w-24 bg-slate-200 rounded-full mx-auto"></div>
      <div class="h-12 bg-slate-200 rounded-xl"></div>
      <div class="h-12 bg-slate-200 rounded-xl"></div>
    </div>

    <template v-else>
      <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">Informations personnelles</h2>

        <div v-if="profileMsg" class="mb-4 p-3 rounded-xl text-sm" :class="profileMsg.type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'">
          {{ profileMsg.text }}
        </div>

        <div class="flex flex-col items-center mb-6">
          <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-md border-4 border-white">
            {{ (form.first_name || authStore.user?.first_name || authStore.user?.name || 'U')[0] }}
          </div>
        </div>

        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Prénom</label>
              <input v-model="form.first_name" type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Nom</label>
              <input v-model="form.last_name" type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Téléphone</label>
            <input v-model="form.phone" type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Adresse</label>
            <input v-model="form.address" type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="123 rue Exemple, Ville">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Bio</label>
            <textarea v-model="form.bio" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Parlez-nous de vous..."></textarea>
          </div>
          <div class="flex justify-end pt-2">
            <button @click="saveProfile" :disabled="savingProfile" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 text-sm font-medium shadow-lg shadow-blue-600/20 transition-all">
              <svg v-if="savingProfile" class="w-4 h-4 animate-spin inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              {{ savingProfile ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-5">Changer le mot de passe</h2>

        <div v-if="pwdMsg" class="mb-4 p-3 rounded-xl text-sm" :class="pwdMsg.type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'">
          {{ pwdMsg.text }}
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mot de passe actuel</label>
            <input v-model="passwordForm.current_password" type="password" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nouveau mot de passe</label>
            <input v-model="passwordForm.new_password" type="password" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirmer le mot de passe</label>
            <input v-model="passwordForm.new_password_confirmation" type="password" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div class="flex justify-end pt-2">
            <button @click="changePassword" :disabled="savingPwd" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 disabled:opacity-50 text-sm font-medium shadow-lg shadow-blue-600/20 transition-all">
              <svg v-if="savingPwd" class="w-4 h-4 animate-spin inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              {{ savingPwd ? 'Mise à jour...' : 'Mettre à jour' }}
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { authAPI } from '../../services/api';

const authStore = useAuthStore();
const loading = ref(true);
const savingProfile = ref(false);
const savingPwd = ref(false);
const profileMsg = ref(null);
const pwdMsg = ref(null);

const form = reactive({ first_name: '', last_name: '', email: '', phone: '', address: '', bio: '' });

const passwordForm = reactive({ current_password: '', new_password: '', new_password_confirmation: '' });

onMounted(() => {
  if (authStore.user) {
    form.first_name = authStore.user.first_name || '';
    form.last_name = authStore.user.last_name || '';
    form.email = authStore.user.email || '';
    form.phone = authStore.user.phone || '';
    form.address = authStore.user.address || '';
    form.bio = authStore.user.bio || '';
  }
  loading.value = false;
});

async function saveProfile() {
  savingProfile.value = true; profileMsg.value = null;
  try {
    const { data } = await authStore.updateProfile({ first_name: form.first_name, last_name: form.last_name, email: form.email, phone: form.phone, address: form.address, bio: form.bio });
    profileMsg.value = { type: 'success', text: 'Profil mis à jour avec succès !' };
  } catch (e) {
    profileMsg.value = { type: 'error', text: e.response?.data?.message || 'Échec de la mise à jour du profil.' };
  } finally { savingProfile.value = false; }
}

async function changePassword() {
  if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
    pwdMsg.value = { type: 'error', text: 'Les mots de passe ne correspondent pas.' };
    return;
  }
  savingPwd.value = true; pwdMsg.value = null;
  try {
    await authAPI.changePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.new_password,
      password_confirmation: passwordForm.new_password_confirmation,
    });
    pwdMsg.value = { type: 'success', text: 'Mot de passe modifié avec succès !' };
    passwordForm.current_password = '';
    passwordForm.new_password = '';
    passwordForm.new_password_confirmation = '';
  } catch (e) {
    pwdMsg.value = { type: 'error', text: e.response?.data?.message || 'Échec du changement de mot de passe.' };
  } finally { savingPwd.value = false; }
}
</script>

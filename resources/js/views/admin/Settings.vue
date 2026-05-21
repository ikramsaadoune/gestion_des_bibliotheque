<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Paramètres</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configuration de la bibliothèque</p>
    </div>

    <div class="max-w-3xl space-y-6">
      <div v-if="message" class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-xl text-sm flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ message }}
      </div>
      <div v-if="error" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm flex items-center">
        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ error }}
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8">
        <div class="flex items-center space-x-3 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
          <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bibliothèque</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Informations générales</p>
          </div>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom de la bibliothèque</label>
              <input v-model="form.library_name" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email de contact</label>
              <input v-model="form.library_email" type="email" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Téléphone</label>
              <input v-model="form.library_phone" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Adresse</label>
              <textarea v-model="form.library_address" rows="2" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
            </div>
          </div>

          <div class="flex items-center space-x-3 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
            <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Prêts</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">Configuration des emprunts</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Durée du prêt (jours)</label>
              <input v-model="form.loan_duration_days" type="number" min="1" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Livres max par utilisateur</label>
              <input v-model="form.max_books_per_user" type="number" min="1" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Amende par jour (MAD)</label>
              <input v-model="form.fine_per_day" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
            </div>
          </div>

          <div class="flex items-center space-x-3 mb-6">
            <div class="p-2.5 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Logo</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">Image de la bibliothèque</p>
            </div>
          </div>

          <div class="mb-6">
            <div class="flex items-center space-x-4">
              <img v-if="logoPreview" :src="logoPreview" class="h-16 w-auto object-contain rounded-lg border border-gray-200 dark:border-gray-700" />
              <div class="flex-1">
                <label class="cursor-pointer inline-flex items-center px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  Choisir un logo
                  <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden" />
                </label>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="submit" :disabled="submitting" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm">
              <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ submitting ? 'Enregistrement...' : 'Enregistrer les paramètres' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { settingsAPI } from '../../services/api';

const form = reactive({ library_name: '', library_email: '', library_phone: '', library_address: '', loan_duration_days: 14, fine_per_day: 5, max_books_per_user: 5 });
const submitting = ref(false);
const message = ref('');
const error = ref('');
const logoFile = ref(null);
const logoPreview = ref(null);

function handleLogoUpload(e) {
  const file = e.target.files[0];
  if (file) {
    logoFile.value = file;
    logoPreview.value = URL.createObjectURL(file);
  }
}

onMounted(async () => {
  try {
    const { data } = await settingsAPI.all();
    const s = data.settings || data.data || data || {};
    Object.keys(form).forEach(k => { if (s[k] !== undefined) form[k] = s[k]; });
    if (s.library_logo) logoPreview.value = s.library_logo;
  } catch (e) {}
});

async function handleSubmit() {
  submitting.value = true;
  message.value = '';
  error.value = '';
  try {
    const payload = new FormData();
    Object.keys(form).forEach(k => payload.append(k, form[k]));
    if (logoFile.value) payload.append('logo', logoFile.value);
    await settingsAPI.update(payload);
    message.value = 'Paramètres enregistrés avec succès.';
  } catch (e) {
    error.value = e.response?.data?.message || 'Erreur lors de l\'enregistrement';
  } finally { submitting.value = false; }
}
</script>

<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ isEdit ? 'Modifiez les informations de l\'utilisateur' : 'Créez un nouveau compte utilisateur' }}</p>
    </div>

    <div v-if="error" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm flex items-center">
      <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ error }}
    </div>
    <div v-if="validationErrors.length" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
      <p v-for="(err, i) in validationErrors" :key="i" class="text-sm text-red-600 dark:text-red-400 flex items-center">
        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ err }}
      </p>
    </div>

    <div class="max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div class="flex items-center space-x-4 pb-5 border-b border-gray-100 dark:border-gray-700">
          <div class="relative">
            <img :src="avatarPreview || 'https://ui-avatars.com/api/?name=' + (form.name || 'U') + '&background=6366f1&color=fff&size=128'" class="w-16 h-16 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700" />
            <button type="button" @click="$refs.avatarInput.click()" class="absolute -bottom-1 -right-1 p-1.5 bg-white dark:bg-gray-700 rounded-full shadow border border-gray-200 dark:border-gray-600 text-gray-400 hover:text-indigo-600 transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </button>
          </div>
          <input ref="avatarInput" type="file" @change="handleAvatarUpload" accept="image/*" class="hidden" />
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Photo de profil</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Optionnelle</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prénom</label>
            <input v-model="form.first_name" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom</label>
            <input v-model="form.last_name" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
            <input v-model="form.email" type="email" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ isEdit ? 'Nouveau mot de passe' : 'Mot de passe' }}</label>
            <input v-model="form.password" type="password" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" :required="!isEdit" :placeholder="isEdit ? 'Laisser vide pour conserver' : ''" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Téléphone</label>
            <input v-model="form.phone" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Adresse</label>
            <input v-model="form.address" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="123 rue Exemple, Ville" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rôle</label>
            <select v-model="form.role" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
              <option value="user">Utilisateur</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>
          <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
              <div class="w-11 h-6 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
              <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">Compte actif</span>
            </label>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-5 border-t border-gray-100 dark:border-gray-700">
          <router-link to="/admin/users" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">Annuler</router-link>
          <button type="submit" :disabled="submitting" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-sm">
            <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ submitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer l\'utilisateur') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { adminUsersAPI } from '../../services/api';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => !!route.params.id);
const submitting = ref(false);
const error = ref('');
const validationErrors = ref([]);
const avatarPreview = ref(null);
const avatarFile = ref(null);
const form = reactive({ first_name: '', last_name: '', email: '', password: '', phone: '', address: '', role: 'user', is_active: true });

function handleAvatarUpload(e) {
  const file = e.target.files[0];
  if (file) {
    avatarFile.value = file;
    avatarPreview.value = URL.createObjectURL(file);
  }
}

onMounted(async () => {
  if (isEdit.value) {
    try {
      const { data } = await adminUsersAPI.show(route.params.id);
      const u = data.data || data;
      form.first_name = u.first_name || '';
      form.last_name = u.last_name || '';
      form.email = u.email || '';
      form.phone = u.phone || '';
      form.address = u.address || '';
      form.role = u.role || 'user';
      form.is_active = u.is_active ?? true;
      if (u.avatar) avatarPreview.value = u.avatar;
    } catch (e) { error.value = 'Impossible de charger l\'utilisateur'; }
  }
});

async function handleSubmit() {
  submitting.value = true;
  error.value = '';
  validationErrors.value = [];
  try {
    const payload = { ...form, avatar: avatarFile.value };
    if (isEdit.value) {
      if (!payload.password) delete payload.password;
      await adminUsersAPI.update(route.params.id, payload);
    } else {
      await adminUsersAPI.create(payload);
    }
    router.push('/admin/users');
  } catch (e) {
    if (e.response?.status === 422) {
      const errs = e.response.data.errors;
      validationErrors.value = Object.values(errs).flat();
    } else {
      error.value = e.response?.data?.message || 'Erreur lors de l\'enregistrement';
    }
  } finally { submitting.value = false; }
}
</script>

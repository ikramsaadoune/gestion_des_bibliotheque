<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Auteurs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les auteurs de la bibliothèque</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter un auteur
      </button>
    </div>

    <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
      <div class="space-y-4">
        <div v-for="i in 5" :key="i" class="flex items-center space-x-4 animate-pulse">
          <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/6"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="authors.length === 0" class="flex flex-col items-center justify-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
      <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun auteur trouvé</p>
      <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Ajoutez votre premier auteur</p>
    </div>

    <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Photo</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Nom</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Nationalité</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Livres</th>
              <th class="text-right px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="author in authors" :key="author.id" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-4 py-3">
                <img :src="author.photo || 'https://ui-avatars.com/api/?name=' + author.name + '&background=6366f1&color=fff'" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700" />
              </td>
              <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ author.name }}</td>
              <td class="px-4 py-3">
                <span class="px-2.5 py-1 bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-400 text-xs rounded-md">{{ author.nationality || '-' }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="font-medium text-gray-900 dark:text-white">{{ author.books_count || 0 }}</span>
                <span class="text-gray-400 dark:text-gray-500 text-xs ml-1">livre{{ author.books_count > 1 ? 's' : '' }}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <button @click="openEditModal(author)" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="Modifier">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                  <button @click="confirmDelete(author)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="Supprimer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ editingAuthor ? 'Modifier l\'auteur' : 'Nouvel auteur' }}</h3>
        <form @submit.prevent="handleSave" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nationalité</label>
            <input v-model="form.nationality" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Française" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Photo</label>
            <div class="flex items-center space-x-4">
              <img v-if="previewUrl" :src="previewUrl" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700" />
              <label class="cursor-pointer px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                Choisir une image
                <input type="file" @change="handleFileUpload" accept="image/*" class="hidden" />
              </label>
            </div>
          </div>
          <div v-if="modalError" class="p-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm">{{ modalError }}</div>
          <div class="flex justify-end space-x-3 pt-2">
            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
            <button type="submit" :disabled="saving" class="px-4 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50 font-medium flex items-center">
              <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ saving ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDeleteModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer l'auteur</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Êtes-vous sûr de vouloir supprimer "{{ deleteAuthor?.name }}" ?</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="deleteAuthorAction" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { authorsAPI } from '../../services/api';

const authors = ref([]);
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingAuthor = ref(null);
const deleteAuthor = ref(null);
const saving = ref(false);
const loading = ref(true);
const modalError = ref('');
const previewUrl = ref(null);
const photoFile = ref(null);
const form = ref({ name: '', nationality: '' });

async function fetchAuthors() {
  loading.value = true;
  try {
    const { data } = await authorsAPI.all();
    authors.value = data.data || data || [];
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

function handleFileUpload(e) {
  const file = e.target.files[0];
  if (file) {
    photoFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
  }
}

function openCreateModal() {
  editingAuthor.value = null;
  form.value = { name: '', nationality: '' };
  previewUrl.value = null;
  photoFile.value = null;
  modalError.value = '';
  showModal.value = true;
}

function openEditModal(author) {
  editingAuthor.value = author;
  form.value = { name: author.name, nationality: author.nationality || '' };
  previewUrl.value = author.photo || null;
  photoFile.value = null;
  modalError.value = '';
  showModal.value = true;
}

async function handleSave() {
  saving.value = true;
  modalError.value = '';
  try {
    const payload = new FormData();
    payload.append('name', form.value.name);
    payload.append('nationality', form.value.nationality);
    if (photoFile.value) payload.append('photo', photoFile.value);
    if (editingAuthor.value) {
      payload.append('_method', 'PUT');
      await authorsAPI.update(editingAuthor.value.id, payload);
    } else {
      await authorsAPI.create(payload);
    }
    showModal.value = false;
    await fetchAuthors();
  } catch (e) {
    modalError.value = e.response?.data?.message || 'Erreur lors de l\'enregistrement';
  } finally { saving.value = false; }
}

function confirmDelete(author) {
  deleteAuthor.value = author;
  showDeleteModal.value = true;
}

async function deleteAuthorAction() {
  try {
    await authorsAPI.delete(deleteAuthor.value.id);
    authors.value = authors.value.filter(a => a.id !== deleteAuthor.value.id);
    showDeleteModal.value = false;
    deleteAuthor.value = null;
  } catch (e) { console.error(e); }
}

onMounted(fetchAuthors);
</script>

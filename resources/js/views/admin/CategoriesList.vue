<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Catégories</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les catégories de livres</p>
      </div>
      <button @click="openCreateModal" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Ajouter une catégorie
      </button>
    </div>

    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-white dark:bg-gray-800 rounded-xl p-6 animate-pulse border border-gray-100 dark:border-gray-700">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="categories.length === 0" class="flex flex-col items-center justify-center py-16 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
      <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
      <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucune catégorie</p>
      <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Créez votre première catégorie</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="cat in categories" :key="cat.id" class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
          <div class="flex items-center space-x-4">
            <div :class="catColor(cat.name)" class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl">{{ cat.icon || '📁' }}</div>
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ cat.name }}</h3>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ cat.books_count || 0 }} livre{{ cat.books_count > 1 ? 's' : '' }}</p>
            </div>
          </div>
          <span class="px-2 py-0.5 text-xs font-medium rounded-full" :class="cat.is_active ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400'">{{ cat.is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="flex items-center justify-end space-x-2 mt-4 pt-3 border-t border-gray-50 dark:border-gray-700/50 opacity-0 group-hover:opacity-100 transition-opacity">
          <button @click="openEditModal(cat)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 transition-colors">Modifier</button>
          <button @click="confirmDelete(cat)" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors">Supprimer</button>
        </div>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ editingCategory ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}</h3>
        <form @submit.prevent="handleSave" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nom</label>
            <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Icône (emoji)</label>
            <input v-model="form.icon" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="📚" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Statut</label>
            <select v-model="form.is_active" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
              <option :value="true">Active</option>
              <option :value="false">Inactive</option>
            </select>
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
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer la catégorie</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Êtes-vous sûr de vouloir supprimer "{{ deleteCategory?.name }}" ?</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="deleteCategoryAction" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { categoriesAPI } from '../../services/api';

const categories = ref([]);
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingCategory = ref(null);
const deleteCategory = ref(null);
const saving = ref(false);
const loading = ref(true);
const modalError = ref('');
const form = ref({ name: '', icon: '📁', is_active: true });

const catColors = ['bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400', 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400', 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400', 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400', 'bg-pink-50 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400', 'bg-teal-50 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400'];

function catColor(name) {
  let hash = 0;
  for (let i = 0; i < (name || '').length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
  return catColors[Math.abs(hash) % catColors.length];
}

async function fetchCategories() {
  loading.value = true;
  try {
    const { data } = await categoriesAPI.allAdmin();
    categories.value = data.data || data || [];
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

function openCreateModal() {
  editingCategory.value = null;
  form.value = { name: '', icon: '📁', is_active: true };
  modalError.value = '';
  showModal.value = true;
}

function openEditModal(cat) {
  editingCategory.value = cat;
  form.value = { name: cat.name, icon: cat.icon || '📁', is_active: cat.is_active ?? true };
  modalError.value = '';
  showModal.value = true;
}

async function handleSave() {
  saving.value = true;
  modalError.value = '';
  try {
    if (editingCategory.value) {
      await categoriesAPI.update(editingCategory.value.id, form.value);
    } else {
      await categoriesAPI.create(form.value);
    }
    showModal.value = false;
    await fetchCategories();
  } catch (e) {
    modalError.value = e.response?.data?.message || 'Erreur lors de l\'enregistrement';
  } finally { saving.value = false; }
}

function confirmDelete(cat) {
  deleteCategory.value = cat;
  showDeleteModal.value = true;
}

async function deleteCategoryAction() {
  try {
    await categoriesAPI.delete(deleteCategory.value.id);
    categories.value = categories.value.filter(c => c.id !== deleteCategory.value.id);
    showDeleteModal.value = false;
    deleteCategory.value = null;
  } catch (e) { console.error(e); }
}

onMounted(fetchCategories);
</script>

<template>
  <div>
    <div v-if="loading" class="space-y-6">
      <div class="flex items-center space-x-4 animate-pulse mb-6">
        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 animate-pulse border border-gray-100 dark:border-gray-700">
            <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
          </div>
        </div>
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 animate-pulse border border-gray-100 dark:border-gray-700">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-4"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full mb-2"></div>
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-2/3"></div>
          </div>
        </div>
      </div>
    </div>

    <template v-else-if="error">
      <div class="flex flex-col items-center justify-center py-20">
        <svg class="w-16 h-16 text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ error }}</p>
        <router-link to="/admin/books" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium">Retour aux livres</router-link>
      </div>
    </template>

    <template v-else-if="book">
      <div class="flex items-center justify-between mb-6">
        <div>
          <router-link to="/admin/books" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour aux livres
          </router-link>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ book.title }}</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">par {{ book.user?.name }} · {{ book.category || 'Non catégorisé' }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <router-link :to="'/admin/books/' + book.id + '/edit'" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Modifier
          </router-link>
          <button @click="confirmDelete" class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Supprimer
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <img :src="book.cover_image || 'https://ui-avatars.com/api/?name=' + book.title + '&background=6366f1&color=fff&size=400'" class="w-full rounded-lg shadow-md object-cover" />
          </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informations</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div><span class="text-gray-500 dark:text-gray-400">Auteur</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">{{ book.author_name || book.user?.name || '-' }}</p></div>
              <div><span class="text-gray-500 dark:text-gray-400">Catégorie</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">{{ book.category_name || book.category || '-' }}</p></div>
              <div><span class="text-gray-500 dark:text-gray-400">Stock</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">{{ book.quantity_available ?? '-' }} / {{ book.quantity_total ?? '-' }}</p></div>
              <div><span class="text-gray-500 dark:text-gray-400">Statut</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">
                <span class="px-2 py-0.5 text-xs rounded-md" :class="book.is_published ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700'">{{ book.is_published ? 'Publié' : 'Brouillon' }}</span>
              </p></div>
              <div><span class="text-gray-500 dark:text-gray-400">ISBN</span><p class="text-gray-900 dark:text-white font-medium mt-0.5 font-mono">{{ book.isbn || '-' }}</p></div>
              <div><span class="text-gray-500 dark:text-gray-400">Langue</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">{{ book.language || '-' }}</p></div>
              <div><span class="text-gray-500 dark:text-gray-400">Ajouté le</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">{{ formatDate(book.created_at) }}</p></div>
              <div><span class="text-gray-500 dark:text-gray-400">Avis</span><p class="text-gray-900 dark:text-white font-medium mt-0.5">{{ book.reviews_count || 0 }}</p></div>
            </div>
            <div v-if="book.description" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
              <span class="text-sm text-gray-500 dark:text-gray-400">Description</span>
              <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">{{ book.description }}</p>
            </div>
          </div>

          <div v-if="book.content" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contenu</h3>
            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ book.content }}</div>
          </div>
        </div>
      </div>
    </template>

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDeleteModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer le livre</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Êtes-vous sûr de vouloir supprimer "{{ book?.title }}" ?</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="deleteBook" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { adminBooksAPI } from '../../services/api';

const route = useRoute();
const router = useRouter();
const book = ref(null);
const loading = ref(true);
const error = ref('');
const showDeleteModal = ref(false);

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

function confirmDelete() {
  showDeleteModal.value = true;
}

async function deleteBook() {
  try {
    await adminBooksAPI.delete(book.value.id);
    router.push('/admin/books');
  } catch (e) { console.error(e); }
}

onMounted(async () => {
  try {
    const { data } = await adminBooksAPI.show(route.params.id);
    book.value = data.book || data.data || data;
  } catch (e) {
    error.value = 'Impossible de charger le livre';
  } finally { loading.value = false; }
});
</script>

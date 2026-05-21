<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Livres</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez le catalogue de la bibliothèque</p>
      </div>
          <router-link to="/admin/books/create" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter un livre
          </router-link>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
          <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-wrap gap-3">
            <div class="relative flex-1 min-w-[200px]">
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              <input v-model="filters.search" @input="debouncedSearch" type="text" placeholder="Rechercher un livre..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm" />
            </div>
          </div>

      <div v-if="loading" class="p-8">
        <div class="space-y-4">
          <div v-for="i in 5" :key="i" class="flex items-center space-x-4 animate-pulse">
            <div class="w-10 h-14 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="books.length === 0" class="flex flex-col items-center justify-center py-16">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun livre trouvé</p>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Ajoutez votre premier livre pour commencer</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Titre</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Auteur</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Stock</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Statut</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Date</th>
              <th class="text-right px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="book in books" :key="book.id" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-4 py-3">
                <router-link :to="'/admin/books/' + book.id" class="text-gray-900 dark:text-white font-medium hover:text-indigo-600 dark:hover:text-indigo-400">{{ book.title }}</router-link>
              </td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.author_name || book.user?.name || '-' }}</td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.quantity_available ?? '-' }}/{{ book.quantity_total ?? '-' }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 text-xs rounded-md" :class="book.is_published ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400'">{{ book.is_published ? 'Publié' : 'Brouillon' }}</span>
              </td>
              <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-sm">{{ book.created_at ? new Date(book.created_at).toLocaleDateString('fr-FR') : '-' }}</td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <router-link :to="'/admin/books/' + book.id" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Voir">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </router-link>
                  <router-link :to="'/admin/books/' + book.id + '/edit'" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Modifier">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </router-link>
                  <button @click="confirmDelete(book)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Supprimer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Pagination v-if="meta" :currentPage="meta.current_page" :totalPages="meta.last_page" :from="meta.from" :to="meta.to" :total="meta.total" @page="goToPage" />

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDeleteModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer le livre</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Êtes-vous sûr de vouloir supprimer "{{ deleteBook?.title }}" ? Cette action est irréversible.</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="deleteBookAction" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { adminBooksAPI } from '../../services/api';
import Pagination from '../../components/Pagination.vue';

const books = ref([]);
const categories = ref([]);
const meta = ref(null);
const loading = ref(false);
const showDeleteModal = ref(false);
const deleteBook = ref(null);
const filters = reactive({ search: '', category_id: '', status: '', language: '' });
let searchTimeout;
const currentPage = ref(1);

function debouncedSearch() {
  clearTimeout(searchTimeout);
  currentPage.value = 1;
  searchTimeout = setTimeout(() => fetchBooks(1), 300);
}

async function fetchBooks(page) {
  loading.value = true;
  if (page) currentPage.value = page;
  try {
    const params = Object.fromEntries(Object.entries(filters).filter(([_, v]) => v !== ''));
    params.page = currentPage.value;
    const { data } = await adminBooksAPI.all(params);
    books.value = data.data || data;
    meta.value = data.meta || null;
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

function goToPage(page) {
  fetchBooks(page);
}

function confirmDelete(book) {
  deleteBook.value = book;
  showDeleteModal.value = true;
}

async function deleteBookAction() {
  try {
    await adminBooksAPI.delete(deleteBook.value.id);
    books.value = books.value.filter(b => b.id !== deleteBook.value.id);
    showDeleteModal.value = false;
    deleteBook.value = null;
  } catch (e) { console.error(e); }
}

onMounted(() => {
  fetchBooks();
});
</script>

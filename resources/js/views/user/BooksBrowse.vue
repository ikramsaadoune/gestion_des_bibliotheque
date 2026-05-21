<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
      <h1 class="text-xl font-bold text-slate-900">Explorer</h1>
      <div class="relative w-full sm:w-72">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input v-model="filters.search" @input="debouncedSearch" type="text" placeholder="Rechercher un livre..." class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
      <select v-model="filters.category_id" @change="applyFilters" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        <option value="">Toutes les catégories</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <select v-model="filters.sort" @change="applyFilters" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        <option value="newest">Plus récents</option>
        <option value="popularity">Populaires</option>
        <option value="title">Titre</option>
      </select>
      <button @click="clearFilters" class="px-3 py-2 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50">Réinitialiser</button>
    </div>

    <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="i in 12" :key="i" class="bg-white rounded-xl border border-slate-200 overflow-hidden animate-pulse">
        <div class="aspect-[3/4] bg-slate-200"></div>
        <div class="p-3 space-y-2">
          <div class="h-3 bg-slate-200 rounded w-3/4"></div>
          <div class="h-2.5 bg-slate-200 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <p class="text-red-600">{{ error }}</p>
      <button @click="fetchBooks" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Réessayer</button>
    </div>

    <div v-else-if="books.length === 0" class="text-center py-16">
      <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      </div>
      <h3 class="text-lg font-semibold text-slate-700 mb-1">Aucun livre trouvé</h3>
      <p class="text-sm text-slate-500">Essayez de modifier votre recherche.</p>
      <button @click="clearFilters" class="mt-4 px-4 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50">Réinitialiser</button>
    </div>

    <div v-else>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <router-link v-for="book in books" :key="book.id" :to="`/books/${book.id}`" class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">
          <div class="aspect-[3/4] bg-gradient-to-br from-blue-50 to-sky-50 flex items-center justify-center">
            <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover" />
            <span v-else class="text-3xl">📖</span>
          </div>
          <div class="p-3">
            <h3 class="font-semibold text-sm text-slate-900 truncate">{{ book.title }}</h3>
            <p class="text-xs text-slate-500 truncate mt-0.5">{{ book.author_name || 'Auteur inconnu' }}</p>
            <span v-if="book.quantity_available > 0" class="inline-block mt-1.5 text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Disponible</span>
            <span v-else class="inline-block mt-1.5 text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Indisponible</span>
          </div>
        </router-link>
      </div>

      <div v-if="lastPage > 1" class="flex items-center justify-center mt-8 space-x-2">
        <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1" class="px-3 py-2 text-sm rounded-lg border border-slate-300 disabled:opacity-50 hover:bg-slate-50 text-slate-700 transition-colors">Précédent</button>
        <span class="text-sm text-slate-500">Page {{ currentPage }} / {{ lastPage }}</span>
        <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= lastPage" class="px-3 py-2 text-sm rounded-lg border border-slate-300 disabled:opacity-50 hover:bg-slate-50 text-slate-700 transition-colors">Suivant</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { booksAPI, categoriesAPI } from '../../services/api';

const route = useRoute();
const books = ref([]);
const categories = ref([]);
const loading = ref(true);
const error = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

const filters = ref({
  search: route.query.search || '',
  category_id: route.query.category_id || '',
  sort: route.query.sort || 'newest',
});

let debounceTimer;
function debouncedSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(applyFilters, 300);
}

function applyFilters() {
  currentPage.value = 1;
  fetchBooks();
}

function clearFilters() {
  filters.value = { search: '', category_id: '', sort: 'newest' };
  applyFilters();
}

function goToPage(page) {
  if (page < 1 || page > lastPage.value) return;
  currentPage.value = page;
  fetchBooks();
}

async function fetchBooks() {
  loading.value = true; error.value = '';
  try {
    const params = { page: currentPage.value, per_page: 12, ...filters.value };
    if (params.sort === 'newest') { params.sort_by = 'created_at'; params.sort_dir = 'desc'; }
    else if (params.sort === 'popularity') { params.sort_by = 'popularity'; params.sort_dir = 'desc'; }
    else if (params.sort === 'title') { params.sort_by = 'title'; params.sort_dir = 'asc'; }
    delete params.sort;
    Object.keys(params).forEach(k => { if (!params[k]) delete params[k]; });
    const { data } = await booksAPI.all(params);
    books.value = data.data || data;
    currentPage.value = data.current_page || data.meta?.current_page || 1;
    lastPage.value = data.last_page || data.meta?.last_page || 1;
  } catch (e) {
    error.value = 'Impossible de charger les livres.';
    books.value = [];
  } finally { loading.value = false; }
}

async function fetchCategories() {
  try {
    const { data } = await categoriesAPI.all();
    categories.value = data.data || data;
  } catch (e) { /* ignore */ }
}

onMounted(() => { fetchBooks(); fetchCategories(); });
</script>

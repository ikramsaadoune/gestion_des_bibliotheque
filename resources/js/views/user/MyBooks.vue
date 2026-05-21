<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl font-bold text-slate-900">Mes livres</h1>
      <router-link to="/my-books/create" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors flex items-center space-x-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Publier un livre</span>
      </router-link>
    </div>

    <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="i in 8" :key="i" class="bg-white rounded-xl p-4 animate-pulse border border-slate-200">
        <div class="aspect-[3/4] bg-slate-200 rounded-lg mb-3"></div>
        <div class="h-4 bg-slate-200 rounded w-3/4 mb-2"></div>
        <div class="h-3 bg-slate-200 rounded w-1/2"></div>
      </div>
    </div>

    <div v-else-if="books.length === 0" class="text-center py-16">
      <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      </div>
      <h2 class="text-lg font-semibold text-slate-700">Vous n'avez pas encore publié de livre</h2>
      <p class="text-slate-500 text-sm mt-1">Commencez à écrire votre histoire dès maintenant !</p>
      <router-link to="/my-books/create" class="inline-block mt-4 px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700">Publier un livre</router-link>
    </div>

    <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="book in books" :key="book.id" class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-all">
        <router-link :to="`/books/${book.id}`">
          <div class="aspect-[3/4] bg-gradient-to-br from-blue-50 to-sky-50 flex items-center justify-center overflow-hidden">
            <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover" />
            <span v-else class="text-3xl">📖</span>
          </div>
        </router-link>
        <div class="p-3">
          <router-link :to="`/books/${book.id}`">
            <h3 class="font-semibold text-sm text-slate-900 truncate">{{ book.title }}</h3>
          </router-link>
          <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">Publié</span>
          <div class="flex flex-wrap items-center gap-1 mt-2 pt-2 border-t border-slate-100">
            <router-link :to="`/books/${book.id}/read`" class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center space-x-1 px-2 py-1 rounded hover:bg-green-50">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
              <span>Contenu</span>
            </router-link>
            <router-link :to="`/my-books/${book.id}/edit`" class="text-xs text-blue-600 hover:text-blue-700 font-medium flex items-center space-x-1 px-2 py-1 rounded hover:bg-blue-50">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
              <span>Modifier</span>
            </router-link>
            <button @click="confirmDelete(book)" class="text-xs text-red-600 hover:text-red-700 font-medium flex items-center space-x-1 px-2 py-1 rounded hover:bg-red-50">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              <span>Supprimer</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDeleteModal = false">
      <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-900">Supprimer le livre</h3>
        <p class="text-sm text-slate-600 mt-2">Êtes-vous sûr de vouloir supprimer "{{ bookToDelete?.title }}" ? Cette action est irréversible.</p>
        <div class="flex items-center justify-end space-x-3 mt-5">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium">Annuler</button>
          <button @click="deleteBook" :disabled="deleting" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium disabled:opacity-50">
            {{ deleting ? 'Suppression...' : 'Supprimer' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { booksAPI } from '../../services/api';

const loading = ref(true);
const books = ref([]);
const showDeleteModal = ref(false);
const bookToDelete = ref(null);
const deleting = ref(false);

onMounted(fetchBooks);

async function fetchBooks() {
  loading.value = true;
  try {
    const res = await booksAPI.myBooks();
    books.value = res.data?.data || res.data || [];
  } catch (e) { console.error(e); }
  finally { loading.value = false; }
}

function confirmDelete(book) {
  bookToDelete.value = book;
  showDeleteModal.value = true;
}

async function deleteBook() {
  if (!bookToDelete.value) return;
  deleting.value = true;
  try {
    await booksAPI.delete(bookToDelete.value.id);
    books.value = books.value.filter(b => b.id !== bookToDelete.value.id);
    showDeleteModal.value = false;
    bookToDelete.value = null;
  } catch (e) { console.error(e); }
  finally { deleting.value = false; }
}
</script>

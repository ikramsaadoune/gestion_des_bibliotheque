<template>
  <div>
    <h1 class="text-xl font-bold text-slate-900 mb-6">Mes favoris</h1>

    <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="i in 8" :key="i" class="bg-white rounded-xl border border-slate-200 overflow-hidden animate-pulse">
        <div class="aspect-[3/4] bg-slate-200"></div>
        <div class="p-3 space-y-2">
          <div class="h-3 bg-slate-200 rounded w-3/4"></div>
          <div class="h-2.5 bg-slate-200 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <p class="text-red-600">{{ error }}</p>
      <button @click="fetchFavorites" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Réessayer</button>
    </div>

    <div v-else-if="favorites.length === 0" class="text-center py-16">
      <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
      </div>
      <h3 class="text-lg font-semibold text-slate-700 mb-1">Aucun favori</h3>
      <p class="text-sm text-slate-500">Vous n'avez pas encore ajouté de livres à vos favoris.</p>
      <router-link to="/books" class="mt-4 inline-block px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Explorer les livres</router-link>
    </div>

    <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <div v-for="fav in favorites" :key="fav.id" class="group bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all relative">
        <router-link :to="`/books/${fav.book_id || fav.book?.id || fav.id}`">
          <div class="aspect-[3/4] bg-gradient-to-br from-blue-50 to-sky-50 flex items-center justify-center overflow-hidden">
            <img v-if="fav.book?.cover_image || fav.cover_image" :src="fav.book?.cover_image || fav.cover_image" :alt="fav.book?.title || 'Livre'" class="w-full h-full object-cover" />
            <span v-else class="text-3xl">📖</span>
          </div>
          <div class="p-3">
            <h3 class="font-semibold text-sm text-slate-900 truncate group-hover:text-blue-600">{{ fav.book?.title || 'Livre inconnu' }}</h3>
            <p class="text-xs text-slate-500 truncate mt-0.5">{{ fav.book?.user?.name || '' }}</p>
          </div>
        </router-link>
        <button @click="removeFavorite(fav)" :disabled="removingId === fav.id" class="absolute top-2 right-2 p-2 bg-white/80 backdrop-blur-sm rounded-full shadow opacity-0 group-hover:opacity-100 transition-all hover:bg-blue-50">
          <svg v-if="removingId !== fav.id" class="w-4 h-4 text-blue-600 fill-current" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          <svg v-else class="w-4 h-4 text-slate-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
        </button>
      </div>
    </div>

    <div v-if="showUndo && undoneBook" class="fixed bottom-6 right-6 bg-white rounded-xl shadow-xl border border-slate-200 p-4 max-w-sm z-50">
      <p class="text-sm text-slate-700">"{{ undoneBook }}" retiré des favoris</p>
      <button @click="undoRemove" class="mt-2 text-sm font-medium text-blue-600 hover:text-blue-700">Annuler</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { favoritesAPI } from '../../services/api';

const favorites = ref([]);
const loading = ref(true);
const error = ref('');
const removingId = ref(null);
const showUndo = ref(false);
const undoneBook = ref(null);
let removedFav = null;

async function fetchFavorites() {
  loading.value = true; error.value = '';
  try {
    const { data } = await favoritesAPI.all();
    favorites.value = data.data || data;
  } catch (e) {
    error.value = 'Impossible de charger les favoris.';
    favorites.value = [];
  } finally { loading.value = false; }
}

async function removeFavorite(fav) {
  const bookId = fav.book_id || fav.book?.id || fav.id;
  removingId.value = fav.id;
  removedFav = fav;
  try {
    await favoritesAPI.toggle(bookId);
    favorites.value = favorites.value.filter(f => f.id !== fav.id);
  } catch (e) {}
  finally { removingId.value = null; }
}

function undoRemove() {
  if (!removedFav) return;
  const bookId = removedFav.book_id || removedFav.book?.id || removedFav.id;
  favoritesAPI.toggle(bookId).then(() => {
    favorites.value.push(removedFav);
    showUndo.value = false;
    undoneBook.value = null;
    removedFav = null;
  });
}

onMounted(fetchFavorites);
</script>

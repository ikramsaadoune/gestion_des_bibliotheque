<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Mes Emprunts</h1>

    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
      <nav class="flex space-x-6">
        <button @click="activeTab = 'active'; fetchBorrowings()" :class="['pb-3 text-sm font-medium border-b-2 transition-colors', activeTab === 'active' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300']">
          En cours
          <span v-if="activeCount > 0" class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400">{{ activeCount }}</span>
        </button>
        <button @click="activeTab = 'history'; fetchBorrowings()" :class="['pb-3 text-sm font-medium border-b-2 transition-colors', activeTab === 'history' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300']">Historique</button>
      </nav>
    </div>

    <div v-if="loading" class="space-y-4">
      <div v-for="i in 3" :key="i" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5 animate-pulse">
        <div class="flex items-center gap-4">
          <div class="w-14 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <p class="text-red-500 dark:text-red-400">{{ error }}</p>
      <button @click="fetchBorrowings" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Réessayer</button>
    </div>

    <div v-else-if="borrowings.length === 0" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">{{ emptyTitle }}</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ emptyDesc }}</p>
      <router-link v-if="activeTab === 'active'" to="/books" class="mt-4 inline-block px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Parcourir le catalogue</router-link>
    </div>

    <div v-else class="space-y-3">
      <div v-for="b in borrowings" :key="b.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
          <router-link :to="`/books/${b.book?.id || b.book_id}`" class="shrink-0">
            <div class="w-14 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-lg overflow-hidden flex items-center justify-center shadow-sm">
              <img v-if="b.book?.cover_image" :src="b.book.cover_image" :alt="b.book?.title" class="w-full h-full object-cover" />
              <span v-else class="text-2xl">📖</span>
            </div>
          </router-link>
          <div class="flex-1 min-w-0">
            <router-link :to="`/books/${b.book?.id || b.book_id}`" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">{{ b.book?.title || 'Livre inconnu' }}</router-link>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500 dark:text-gray-400">
              <span v-if="b.borrowed_at">Demandé : {{ formatDate(b.borrowed_at) }}</span>
              <span v-if="b.due_at">Retour prévu : {{ formatDate(b.due_at) }}</span>
              <span v-if="b.returned_at">Retourné : {{ formatDate(b.returned_at) }}</span>
            </div>
            <div class="flex items-center mt-2.5 gap-2 flex-wrap">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="statusClass(b.status)">{{ statusLabel(b.status) }}</span>
              <span v-if="b.days_overdue && b.days_overdue > 0" class="text-xs text-red-600 dark:text-red-400 font-medium">⚠ {{ b.days_overdue }} jours de retard</span>
              <span v-if="b.fine_amount && b.fine_amount > 0" class="text-xs text-red-600 dark:text-red-400 font-medium">Amende : {{ b.fine_amount }} MAD</span>
              <span v-if="b.fine_status" class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full" :class="b.fine_status === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400'">{{ b.fine_status === 'paid' ? 'Payée' : 'Non payée' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { borrowingsAPI } from '../../services/api';

const activeTab = ref('active');
const borrowings = ref([]);
const loading = ref(true);
const error = ref('');

const activeCount = computed(() => borrowings.value.filter(b => b.status === 'borrowed').length);

const emptyTitle = computed(() => {
  if (activeTab.value === 'active') return 'Aucun emprunt en cours';
  return 'Aucun historique';
});

const emptyDesc = computed(() => {
  if (activeTab.value === 'active') return "Vous n'avez pas encore emprunté de livre.";
  return "Votre historique d'emprunts est vide.";
});

function statusLabel(status) {
  return { pending: 'En attente', borrowed: 'En cours', returned: 'Retourné', rejected: 'Refusé', overdue: 'En retard' }[status] || status;
}

function statusClass(status) {
  return {
    pending: 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
    borrowed: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    returned: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    overdue: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
  }[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
}

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchBorrowings() {
  loading.value = true;
  error.value = '';
  try {
    let response;
    if (activeTab.value === 'active') {
      const { data } = await borrowingsAPI.myBorrowings();
      const items = data.data || data || [];
      response = items.map(b => ({
        ...b,
        status: b.status === 'borrowed' && b.due_at && new Date(b.due_at) < new Date() ? 'overdue' : b.status,
      }));
    } else {
      const { data } = await borrowingsAPI.myHistory();
      response = data.data || data;
    }
    borrowings.value = response;
  } catch (e) {
    error.value = 'Impossible de charger les emprunts.';
    borrowings.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(fetchBorrowings);
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Mes Amendes</h1>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Total des amendes impayées</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ totalUnpaid }} MAD</p>
        </div>
        <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
          <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
      </div>
    </div>

    <div v-if="successMsg" class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-xl text-sm">{{ successMsg }}</div>

    <div v-if="loading" class="space-y-3">
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
      <button @click="fetchFines" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Réessayer</button>
    </div>

    <div v-else-if="fines.length === 0" class="text-center py-16">
      <div class="w-20 h-20 mx-auto mb-4 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Aucune amende</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400">Vous n'avez aucune amende. Parfait !</p>
    </div>

    <div v-else class="space-y-3">
      <div v-for="f in fines" :key="f.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 hover:shadow-md transition-all">
        <div class="flex items-center gap-4">
          <router-link :to="`/books/${f.book_id || f.book?.id}`" class="shrink-0">
            <div class="w-14 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-lg overflow-hidden flex items-center justify-center shadow-sm">
              <img v-if="f.book?.cover_image" :src="f.book.cover_image" :alt="f.book?.title" class="w-full h-full object-cover" />
              <span v-else class="text-2xl">📖</span>
            </div>
          </router-link>
          <div class="flex-1 min-w-0">
            <router-link :to="`/books/${f.book_id || f.book?.id}`" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400">{{ f.book?.title || 'Livre inconnu' }}</router-link>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ f.reason || '-' }}</p>
            <div class="flex items-center mt-2 gap-2 flex-wrap">
              <span v-if="f.status === 'unpaid'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Impayée</span>
              <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Payée</span>
              <span class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(f.created_at || f.fined_date) }}</span>
            </div>
          </div>
          <div class="text-right shrink-0">
            <p class="text-lg font-bold" :class="f.status === 'unpaid' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">{{ f.amount }} MAD</p>
            <button v-if="f.status === 'unpaid'" @click="payFineItem(f)" :disabled="payingId === f.id" class="mt-2 px-4 py-2 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
              <svg v-if="payingId === f.id" class="w-3.5 h-3.5 animate-spin inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
              {{ payingId === f.id ? 'Paiement...' : 'Payer' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { finesAPI } from '../../services/api';

const fines = ref([]);
const loading = ref(true);
const error = ref('');
const successMsg = ref('');
const payingId = ref(null);

const totalUnpaid = computed(() => {
  return fines.value.filter(f => f.status === 'unpaid').reduce((sum, f) => sum + parseFloat(f.amount || 0), 0).toFixed(2);
});

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchFines() {
  loading.value = true;
  error.value = '';
  try {
    const { data } = await finesAPI.myFines();
    fines.value = data.data || data;
  } catch (e) {
    error.value = 'Impossible de charger les amendes.';
    fines.value = [];
  } finally {
    loading.value = false;
  }
}

async function payFineItem(f) {
  payingId.value = f.id;
  successMsg.value = '';
  try {
    const { data } = await finesAPI.pay(f.id);
    f.status = 'paid';
    successMsg.value = 'Amende payée. Le livre est maintenant disponible.';
    await fetchFines();
  } catch (e) {
    error.value = 'Erreur lors du paiement.';
  }
  finally { payingId.value = null; }
}

onMounted(fetchFines);
</script>

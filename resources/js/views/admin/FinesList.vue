<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Amendes</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les amendes et pénalités</p>
      </div>
    </div>

    <div v-if="error" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm">{{ error }}</div>
    <div v-if="successMsg" class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-xl text-sm">{{ successMsg }}</div>

    <div v-if="loading && !stats.unpaid_count && !stats.paid_count" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div v-for="i in 3" :key="i" class="bg-white dark:bg-gray-800 rounded-xl p-6 animate-pulse border border-gray-100 dark:border-gray-700">
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-3"></div>
        <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total collecté</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ stats.total_collected || 0 }} MAD</p>
          </div>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <div class="p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">En attente</p>
            <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ stats.pending || 0 }} MAD</p>
          </div>
        </div>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center space-x-3">
          <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          </div>
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total amendes</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ stats.total_count || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
      <div v-if="loading" class="p-8">
        <div class="space-y-4">
          <div v-for="i in 5" :key="i" class="flex items-center space-x-4 animate-pulse">
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="fines.length === 0" class="flex flex-col items-center justify-center py-16">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucune amende</p>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Toutes les amendes sont payées</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Utilisateur</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Livre</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Montant</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Raison</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Statut</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Date</th>
              <th class="text-right px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fine in fines" :key="fine.id" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ fine.user?.name }}</td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-[150px] truncate">{{ fine.book?.title || '-' }}</td>
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ fine.amount }} MAD</td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-xs truncate">{{ fine.reason || '-' }}</td>
              <td class="px-4 py-3">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="fine.status === 'paid' ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400'">{{ fine.status === 'paid' ? 'Payée' : 'En attente' }}</span>
              </td>
              <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ formatDate(fine.created_at) }}</td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center gap-1.5 justify-end">
                  <button v-if="fine.status !== 'paid'" @click="markPaid(fine)" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Payer
                  </button>
                  <button @click="openDeleteModal(fine)" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                  </button>
                  <button @click="toggleUserActive(fine)" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-lg" :class="fine.user?.is_active ? 'bg-orange-50 text-orange-700 hover:bg-orange-100 dark:bg-orange-900/20 dark:text-orange-400' : 'bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400'">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    {{ fine.user?.is_active ? 'Suspendre' : 'Activer' }}
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
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer l'amende</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Êtes-vous sûr de vouloir supprimer cette amende de {{ deleteTarget?.user?.name }} ({{ deleteTarget?.amount }} MAD) ?</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="confirmDelete" :disabled="deleting" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium disabled:opacity-50">
            {{ deleting ? 'Suppression...' : 'Supprimer' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { finesAPI, adminUsersAPI } from '../../services/api';
import Pagination from '../../components/Pagination.vue';

const fines = ref([]);
const meta = ref(null);
const loading = ref(false);
const error = ref('');
const successMsg = ref('');
const stats = ref({});
const currentPage = ref(1);

const showDeleteModal = ref(false);
const deleteTarget = ref(null);
const deleting = ref(false);

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

async function fetchFines(page = 1) {
  loading.value = true;
  error.value = '';
  successMsg.value = '';
  currentPage.value = page;
  try {
    const { data } = await finesAPI.all({ page });
    fines.value = data.data || data;
    meta.value = data.meta || null;
  } catch (e) {
    console.error(e);
    error.value = 'Impossible de charger les amendes';
  }
  try {
    const { data } = await finesAPI.stats();
    stats.value = data || {};
  } catch (e) {
    console.error(e);
  }
  loading.value = false;
}

function goToPage(page) { fetchFines(page); }

async function markPaid(fine) {
  try {
    await finesAPI.payAdmin(fine.id);
    fine.status = 'paid';
    await fetchFines(currentPage.value);
  } catch (e) {
    error.value = 'Erreur lors du paiement.';
  }
}

function openDeleteModal(fine) {
  deleteTarget.value = fine;
  showDeleteModal.value = true;
}

async function confirmDelete() {
  if (!deleteTarget.value) return;
  deleting.value = true;
  try {
    await finesAPI.delete(deleteTarget.value.id);
    successMsg.value = 'Amende supprimée.';
    showDeleteModal.value = false;
    deleteTarget.value = null;
    await fetchFines(currentPage.value);
  } catch (e) {
    error.value = 'Erreur lors de la suppression.';
  } finally {
    deleting.value = false;
  }
}

async function toggleUserActive(fine) {
  if (!fine.user?.id) return;
  try {
    const { data } = await adminUsersAPI.toggleActive(fine.user.id);
    successMsg.value = data.message || 'Statut utilisateur modifié.';
  } catch (e) {
    error.value = "Erreur lors de la suspension de l'utilisateur.";
  }
}

onMounted(() => fetchFines());
</script>

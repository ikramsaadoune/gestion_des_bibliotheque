<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Avis</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Modérez les avis des lecteurs</p>
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

      <div v-else-if="reviews.length === 0" class="flex flex-col items-center justify-center py-16">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun avis</p>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Les avis des lecteurs apparaîtront ici</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Utilisateur</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Livre</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Note</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Commentaire</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Date</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Statut</th>
              <th class="text-right px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="review in reviews" :key="review.id" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ review.user?.name }}</td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ review.book?.title }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center space-x-0.5">
                  <svg v-for="s in 5" :key="s" class="w-4 h-4" :class="s <= review.rating ? 'text-yellow-400' : 'text-gray-200 dark:text-gray-600'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
              </td>
              <td class="px-4 py-3 max-w-xs truncate text-gray-600 dark:text-gray-400">{{ review.comment || '-' }}</td>
              <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ formatDate(review.created_at) }}</td>
              <td class="px-4 py-3">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="statusClass(review.status)">{{ statusLabel(review.status) }}</span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <button v-if="review.status === 'pending'" @click="approveReview(review)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approuver
                  </button>
                  <button v-if="review.status === 'pending'" @click="rejectReview(review)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Rejeter
                  </button>
                  <button @click="confirmDelete(review)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="Supprimer">
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
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer l'avis</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Êtes-vous sûr de vouloir supprimer cet avis ?</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="deleteReviewAction" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { reviewsAPI } from '../../services/api';
import Pagination from '../../components/Pagination.vue';

const reviews = ref([]);
const meta = ref(null);
const loading = ref(false);
const showDeleteModal = ref(false);
const deleteReviewItem = ref(null);

function statusClass(status) {
  const map = { approved: 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400', rejected: 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400', pending: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400' };
  return map[status] || 'bg-gray-50 text-gray-700';
}

function statusLabel(status) {
  const map = { approved: 'Approuvé', rejected: 'Rejeté', pending: 'En attente' };
  return map[status] || status;
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

async function fetchReviews() {
  loading.value = true;
  try {
    const { data } = await reviewsAPI.all();
    reviews.value = data.data || data;
    meta.value = data.meta || null;
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

function goToPage(page) { fetchReviews(); }

async function approveReview(review) {
  try { await reviewsAPI.updateAdmin(review.id, { status: 'approved' }); review.status = 'approved'; } catch (e) { console.error(e); }
}

async function rejectReview(review) {
  try { await reviewsAPI.updateAdmin(review.id, { status: 'rejected' }); review.status = 'rejected'; } catch (e) { console.error(e); }
}

function confirmDelete(review) {
  deleteReviewItem.value = review;
  showDeleteModal.value = true;
}

async function deleteReviewAction() {
  try {
    await reviewsAPI.deleteAdmin(deleteReviewItem.value.id);
    reviews.value = reviews.value.filter(r => r.id !== deleteReviewItem.value.id);
    showDeleteModal.value = false;
    deleteReviewItem.value = null;
  } catch (e) { console.error(e); }
}

onMounted(fetchReviews);
</script>

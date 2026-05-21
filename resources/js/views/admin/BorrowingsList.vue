<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emprunts</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les demandes, emprunts et retours</p>
      </div>
      <button @click="showBorrowForm = true" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Nouvel emprunt
      </button>
    </div>

    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
      <nav class="flex space-x-6">
        <button @click="activeTab = 'pending'; fetchList()" :class="['pb-3 text-sm font-medium border-b-2 transition-colors relative', activeTab === 'pending' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300']">
          En attente
          <span v-if="pendingCount > 0" class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">{{ pendingCount }}</span>
        </button>
        <button @click="activeTab = 'active'; fetchList()" :class="['pb-3 text-sm font-medium border-b-2 transition-colors', activeTab === 'active' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300']">En cours</button>
        <button @click="activeTab = 'overdue'; fetchList()" :class="['pb-3 text-sm font-medium border-b-2 transition-colors', activeTab === 'overdue' ? 'text-red-600 border-red-600 dark:text-red-400 dark:border-red-400' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300']">En retard</button>
        <button @click="activeTab = 'history'; fetchList()" :class="['pb-3 text-sm font-medium border-b-2 transition-colors', activeTab === 'history' ? 'text-indigo-600 border-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300']">Historique</button>
      </nav>
    </div>

    <div v-if="loading" class="space-y-3">
      <div v-for="i in 4" :key="i" class="bg-white dark:bg-gray-800 rounded-xl p-5 animate-pulse border border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center py-20">
      <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
      </svg>
      <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">
        {{ activeTab === 'pending' ? 'Aucune demande en attente' : activeTab === 'active' ? 'Aucun emprunt en cours' : activeTab === 'overdue' ? 'Aucun emprunt en retard' : 'Aucun historique' }}
      </p>
    </div>

    <div v-else class="space-y-3">
      <div v-for="b in items" :key="b.id" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 transition-all hover:shadow-md">
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold shrink-0" :class="avatarBg(b.status)">
            {{ (b.user?.name || '?')[0] }}
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-start justify-between gap-2">
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ b.user?.name || 'Inconnu' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ b.book?.title || 'Livre inconnu' }}</p>
              </div>
              <span class="shrink-0 px-2.5 py-1 text-xs font-medium rounded-full" :class="statusClass(b.status)">{{ statusLabel(b.status) }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-xs text-gray-500 dark:text-gray-400">
              <span v-if="b.borrowed_at">Emprunt : {{ formatDate(b.borrowed_at) }}</span>
              <span class="flex items-center gap-1">
                Retour prévu :
                <span v-if="editingDueDate === b.id" class="inline-flex items-center gap-1">
                  <input v-model="dueDateInput" type="date" class="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-xs dark:bg-gray-700 dark:text-white w-36" />
                  <button @click="saveDueDate(b)" class="p-1 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded" title="Enregistrer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </button>
                  <button @click="editingDueDate = null" class="p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded" title="Annuler">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </span>
                <span v-else>
                  {{ formatDate(b.due_at) }}
                  <button v-if="['pending', 'borrowed'].includes(b.status)" @click="startEditDueDate(b)" class="ml-1.5 px-2 py-0.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded transition-colors">Modifier</button>
                </span>
              </span>
              <span v-if="b.returned_at">Retourné : {{ formatDate(b.returned_at) }}</span>
              <span v-if="b.days_overdue && b.days_overdue > 0" class="text-red-600 dark:text-red-400 font-medium">⚠ {{ b.days_overdue }}j de retard</span>
              <span v-if="b.fine_amount" class="text-red-600 dark:text-red-400 font-medium">Amende : {{ b.fine_amount }} MAD</span>
              <span v-if="b.fine_status" class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full" :class="b.fine_status === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400'">{{ b.fine_status === 'paid' ? 'Payée' : 'Non payée' }}</span>
            </div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <!-- Pending actions -->
            <template v-if="b.status === 'pending'">
              <button @click="openConfirmModal(b)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 transition-colors">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Confirmer
              </button>
              <button @click="openRefuseModal(b)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Refuser
              </button>
            </template>
            <!-- Active actions -->
            <template v-if="b.status === 'borrowed'">
              <button @click="confirmReturn(b)" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-green-50 text-green-700 hover:bg-green-100 dark:bg-green-900/20 dark:text-green-400 transition-colors">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Retourner
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <Pagination v-if="meta" :currentPage="meta.current_page" :totalPages="meta.last_page" :from="meta.from" :to="meta.to" :total="meta.total" @page="goToPage" />

    <!-- Nouvel emprunt modal -->
    <div v-if="showBorrowForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showBorrowForm = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-lg w-full mx-4 shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nouvel emprunt</h3>
          <button @click="showBorrowForm = false" class="p-1 text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div v-if="borrowError" class="mb-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg text-sm">{{ borrowError }}</div>
        <div v-if="borrowSuccess" class="mb-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg text-sm">{{ borrowSuccess }}</div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Utilisateur</label>
            <select v-model="borrowForm.user_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
              <option value="">Sélectionner un utilisateur</option>
              <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }} ({{ u.email }})</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Livre</label>
            <select v-model="borrowForm.book_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
              <option value="">Sélectionner un livre</option>
              <option v-for="l in availableBooks" :key="l.id" :value="l.id">{{ l.title }} ({{ l.quantity_available ?? '?' }} dispo)</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de retour prévue</label>
            <input v-model="borrowForm.due_at" type="date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optionnel)</label>
            <textarea v-model="borrowForm.notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="Notes..."></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-2">
            <button @click="showBorrowForm = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 font-medium">Annuler</button>
            <button @click="submitBorrow" :disabled="savingBorrow" class="px-4 py-2 text-sm bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium disabled:opacity-50">
              <svg v-if="savingBorrow" class="animate-spin -ml-1 mr-2 h-4 w-4 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ savingBorrow ? 'Enregistrement...' : "Enregistrer l'emprunt" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirm modal -->
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showConfirmModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 mx-auto mb-4">
          <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Confirmer l'emprunt</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Confirmer la demande de <strong>{{ confirmTarget?.user?.name }}</strong> pour <strong>{{ confirmTarget?.book?.title }}</strong></p>
        <div class="space-y-4 mb-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'emprunt</label>
            <input v-model="confirmBorrowedAt" type="date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date prévue de retour</label>
            <input v-model="confirmDueAt" type="date" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-green-500" />
          </div>
        </div>
        <div class="flex justify-end space-x-3">
          <button @click="showConfirmModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="submitConfirm" :disabled="savingConfirm" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium disabled:opacity-50">
            {{ savingConfirm ? 'Traitement...' : 'Confirmer' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Refuse modal -->
    <div v-if="showRefuseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showRefuseModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
          <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Refuser la demande</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">Refuser la demande de <strong>{{ refuseTarget?.user?.name }}</strong> pour <strong>{{ refuseTarget?.book?.title }}</strong> ?</p>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motif (optionnel)</label>
          <textarea v-model="refuseReason" rows="3" class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-red-500" placeholder="Expliquez le motif du refus..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button @click="showRefuseModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="submitRefuse" :disabled="savingRefuse" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium disabled:opacity-50">
            {{ savingRefuse ? 'Traitement...' : 'Refuser' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Return modal -->
    <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showReturnModal = false">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 mx-auto mb-4">
          <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Confirmer le retour</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-2">Retour de <strong>{{ returnTarget?.book?.title }}</strong> par {{ returnTarget?.user?.name }} ?</p>
        <p v-if="returnTarget?.due_at && isOverdue(returnTarget.due_at)" class="text-sm text-red-600 text-center mb-4">
          ⚠ Retard de {{ overdueDays(returnTarget.due_at) }} jour(s) — une amende sera générée.
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showReturnModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="submitReturn" :disabled="savingReturn" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium disabled:opacity-50">
            {{ savingReturn ? 'Traitement...' : 'Confirmer le retour' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Result modal -->
    <div v-if="resultMsg" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="resultMsg = null">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full mx-auto mb-4" :class="resultType === 'success' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
          <svg v-if="resultType === 'success'" class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <svg v-else class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">{{ resultType === 'success' ? 'Succès' : 'Erreur' }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">{{ resultMsg }}</p>
        <button @click="resultMsg = null" class="mt-4 w-full px-4 py-2.5 text-sm rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">Fermer</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { adminUsersAPI, adminBooksAPI, adminBorrowingsAPI } from '../../services/api';
import Pagination from '../../components/Pagination.vue';

const activeTab = ref('pending');
const items = ref([]);
const meta = ref(null);
const loading = ref(false);

const users = ref([]);
const availableBooks = ref([]);
const showBorrowForm = ref(false);
const borrowForm = ref({ user_id: '', book_id: '', due_at: '', notes: '' });
const savingBorrow = ref(false);
const borrowError = ref('');
const borrowSuccess = ref('');

const editingDueDate = ref(null);
const dueDateInput = ref('');

const showConfirmModal = ref(false);
const confirmTarget = ref(null);
const confirmBorrowedAt = ref('');
const confirmDueAt = ref('');
const savingConfirm = ref(false);

const showRefuseModal = ref(false);
const refuseTarget = ref(null);
const refuseReason = ref('');
const savingRefuse = ref(false);

const showReturnModal = ref(false);
const returnTarget = ref(null);
const savingReturn = ref(false);

const resultMsg = ref(null);
const resultType = ref('success');

const pendingCount = computed(() => items.value.filter(b => b.status === 'pending').length);

function statusLabel(status) {
  return { pending: 'En attente', borrowed: 'En cours', returned: 'Retourné', rejected: 'Refusé', overdue: 'En retard' }[status] || status;
}

function statusClass(status) {
  return {
    pending: 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
    borrowed: 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
    returned: 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400',
    rejected: 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400',
    overdue: 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400',
  }[status] || 'bg-gray-50 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
}

function avatarBg(status) {
  return {
    pending: 'bg-amber-500',
    borrowed: 'bg-blue-500',
    returned: 'bg-green-500',
    rejected: 'bg-red-500',
    overdue: 'bg-red-500',
  }[status] || 'bg-gray-500';
}

function formatDate(date) {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

function isOverdue(date) {
  return new Date(date) < new Date();
}

function overdueDays(date) {
  return Math.ceil((new Date() - new Date(date)) / (1000 * 60 * 60 * 24));
}

async function fetchList() {
  loading.value = true;
  try {
    const statusMap = { pending: 'pending', active: 'borrowed', overdue: 'overdue', history: '' };
    const params = { status: statusMap[activeTab.value], per_page: 20 };

    let res;
    if (activeTab.value === 'history') {
      const { data } = await adminBorrowingsAPI.history(params);
      res = data;
    } else {
      const { data } = await adminBorrowingsAPI.all(params);
      res = data;
    }
    items.value = (res.data || []).map(b => ({
      ...b,
      status: b.status === 'borrowed' && b.due_at && new Date(b.due_at) < new Date() ? 'overdue' : b.status,
    }));
    meta.value = res.meta || null;
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

function goToPage(page) { fetchList(); }

async function submitBorrow() {
  if (!borrowForm.value.user_id || !borrowForm.value.book_id || !borrowForm.value.due_at) {
    borrowError.value = 'Veuillez remplir tous les champs obligatoires.';
    return;
  }
  savingBorrow.value = true; borrowError.value = ''; borrowSuccess.value = '';
  try {
    await adminBorrowingsAPI.create(borrowForm.value);
    borrowSuccess.value = 'Emprunt enregistré.';
    borrowForm.value = { user_id: '', book_id: '', due_at: '', notes: '' };
    setTimeout(() => { showBorrowForm.value = false; borrowSuccess.value = ''; fetchList(); }, 1200);
  } catch (e) {
    borrowError.value = e.response?.data?.message || "Erreur lors de l'enregistrement";
  } finally { savingBorrow.value = false; }
}

function openConfirmModal(b) {
  confirmTarget.value = b;
  const today = new Date().toISOString().substring(0, 10);
  confirmBorrowedAt.value = today;
  confirmDueAt.value = b.due_at ? b.due_at.substring(0, 10) : today;
  showConfirmModal.value = true;
}

async function submitConfirm() {
  if (!confirmTarget.value || !confirmBorrowedAt.value || !confirmDueAt.value) return;
  savingConfirm.value = true;
  try {
    const { data } = await adminBorrowingsAPI.confirm(confirmTarget.value.id, {
      borrowed_at: confirmBorrowedAt.value,
      due_at: confirmDueAt.value,
    });
    resultType.value = 'success';
    resultMsg.value = data.message || 'Emprunt confirmé.';
    showConfirmModal.value = false;
    fetchList();
  } catch (e) {
    resultType.value = 'error';
    resultMsg.value = e.response?.data?.message || "Erreur lors de la confirmation.";
  } finally { savingConfirm.value = false; }
}

function openRefuseModal(b) {
  refuseTarget.value = b;
  refuseReason.value = '';
  showRefuseModal.value = true;
}

async function submitRefuse() {
  if (!refuseTarget.value) return;
  savingRefuse.value = true;
  try {
    const { data } = await adminBorrowingsAPI.refuse(refuseTarget.value.id, { reason: refuseReason.value });
    resultType.value = 'success';
    resultMsg.value = data.message || 'Demande refusée.';
    showRefuseModal.value = false;
    fetchList();
  } catch (e) {
    resultType.value = 'error';
    resultMsg.value = e.response?.data?.message || "Erreur lors du refus.";
  } finally { savingRefuse.value = false; }
}

function startEditDueDate(b) {
  editingDueDate.value = b.id;
  dueDateInput.value = b.due_at ? b.due_at.substring(0, 10) : '';
}

async function saveDueDate(b) {
  if (!dueDateInput.value) return;
  try {
    const { data } = await adminBorrowingsAPI.updateDueDate(b.id, { due_at: dueDateInput.value });
    b.due_at = data.borrowing?.due_at || dueDateInput.value;
    resultType.value = 'success';
    resultMsg.value = data.message || 'Date de retour modifiée.';
    editingDueDate.value = null;
  } catch (e) {
    resultType.value = 'error';
    resultMsg.value = e.response?.data?.message || "Erreur lors de la modification.";
  }
}

function confirmReturn(b) {
  returnTarget.value = b;
  showReturnModal.value = true;
}

async function submitReturn() {
  if (!returnTarget.value) return;
  savingReturn.value = true;
  try {
    const { data } = await adminBorrowingsAPI.return(returnTarget.value.id);
    resultType.value = 'success';
    resultMsg.value = data.message || 'Retour enregistré.';
    showReturnModal.value = false;
    fetchList();
  } catch (e) {
    resultType.value = 'error';
    resultMsg.value = e.response?.data?.message || "Erreur lors du retour.";
  } finally { savingReturn.value = false; }
}

async function fetchUsersAndBooks() {
  try {
    const [usersRes, booksRes] = await Promise.all([
      adminUsersAPI.all({ per_page: 100 }),
      adminBooksAPI.all({ per_page: 100 }),
    ]);
    users.value = usersRes.data?.data || usersRes.data || [];
    availableBooks.value = (booksRes.data?.data || booksRes.data || []).filter(b => b.is_published && b.quantity_available > 0);
  } catch (e) { console.error(e); }
}

onMounted(() => { fetchList(); fetchUsersAndBooks(); });
</script>

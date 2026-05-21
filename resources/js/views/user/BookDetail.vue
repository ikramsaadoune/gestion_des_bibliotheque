<template>
  <div>
    <div v-if="loading" class="animate-pulse space-y-6">
      <div class="h-5 bg-slate-200 rounded w-24"></div>
      <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-64 aspect-[3/4] bg-slate-200 rounded-2xl"></div>
        <div class="flex-1 space-y-4">
          <div class="h-8 bg-slate-200 rounded w-3/4"></div>
          <div class="h-4 bg-slate-200 rounded w-1/2"></div>
          <div class="h-24 bg-slate-200 rounded"></div>
        </div>
      </div>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <p class="text-red-600 font-medium">{{ error }}</p>
      <router-link to="/books" class="mt-3 inline-block text-sm text-blue-600 hover:underline">← Retour à l'exploration</router-link>
    </div>

    <template v-else-if="book">
      <router-link to="/books" class="inline-flex items-center text-sm text-slate-500 hover:text-blue-600 mb-6 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour à l'exploration
      </router-link>

      <div class="flex flex-col md:flex-row gap-8">
        <div class="w-full md:w-64 shrink-0">
          <div class="aspect-[3/4] bg-gradient-to-br from-blue-50 to-sky-50 rounded-2xl overflow-hidden flex items-center justify-center shadow-xl border border-slate-200">
            <img v-if="book.cover_image" :src="book.cover_image" :alt="book.title" class="w-full h-full object-cover" />
            <span v-else class="text-6xl">📖</span>
          </div>

          <div class="mt-4 space-y-2">
            <div v-if="authStore.isAuthenticated" class="space-y-2">
              <button @click="toggleFavorite" :disabled="togglingFav" class="w-full px-4 py-2.5 border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors text-sm flex items-center justify-center space-x-2">
                <svg v-if="isFav" class="w-5 h-5 text-blue-600 fill-current" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                <svg v-else class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span>{{ isFav ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span>
              </button>

              <div v-if="borrowMsg" class="mt-2 px-4 py-2.5 rounded-xl text-sm flex items-center justify-center space-x-2" :class="borrowMsg.type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'">
                <svg v-if="borrowMsg.type === 'success'" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg v-else class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ borrowMsg.text }}</span>
                <button v-if="borrowMsg.type === 'success'" @click="router.push('/my-borrowings')" class="ml-2 text-xs font-medium underline hover:no-underline">Suivre ma demande</button>
              </div>
              <div v-else-if="hasPendingRequest" class="mt-2 px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-700 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Demande en attente de confirmation</span>
              </div>
              <div v-else-if="hasActiveBorrow" class="mt-2 px-4 py-2.5 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Emprunt en cours</span>
              </div>
              <div v-else-if="borrowing" class="mt-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm text-center animate-pulse">Envoi de la demande...</div>
              <button v-else-if="book.quantity_available > 0" @click="borrowBook" class="mt-2 w-full px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Demander un emprunt</span>
              </button>
              <div v-else class="mt-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500 flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Indisponible</span>
              </div>
            </div>

            <div v-if="book.pdf_file" class="mt-2">
              <a :href="book.pdf_file" target="_blank" class="w-full flex items-center justify-center space-x-1 px-4 py-2 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Télécharger le PDF</span>
              </a>
            </div>
          </div>

        </div>

        <div class="flex-1 min-w-0">
          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ book.title }}</h1>
          <p class="mt-1 text-base text-slate-500">{{ book.author_name || 'Auteur inconnu' }}</p>

          <div v-if="book.description" class="mt-6">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-2">À propos</h3>
            <p class="text-sm text-slate-600 leading-relaxed">{{ book.description }}</p>
          </div>

          <div v-if="chapters.length > 0" class="mt-8 border-t border-slate-200 pt-6">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider mb-4">Chapitres</h3>
            <div class="space-y-2">
              <router-link v-for="(ch, i) in chapters" :key="ch.id" :to="`/books/${book.id}/read?chapter=${ch.id}`" class="flex items-center p-3 rounded-lg hover:bg-slate-50 border border-slate-200 transition-colors">
                <span class="w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold mr-3">{{ i + 1 }}</span>
                <span class="text-sm font-medium text-slate-900">{{ ch.title }}</span>
                <svg class="w-4 h-4 ml-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </router-link>
            </div>
          </div>

          <div class="mt-10 border-t border-slate-200 pt-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-5">Avis ({{ reviews.length }})</h3>

            <div v-if="authStore.isAuthenticated" class="bg-slate-50 rounded-xl p-5 mb-6 border border-slate-200">
              <h4 class="text-sm font-medium text-slate-900 mb-3">Donner mon avis</h4>
              <div class="flex items-center mb-3">
                <span class="text-sm text-slate-600 mr-2">Ma note :</span>
                <div class="flex text-blue-500">
                  <button v-for="i in 5" :key="i" @click="newReview.rating = i" type="button" class="focus:outline-none hover:scale-110 transition-transform">
                    <svg v-if="i <= newReview.rating" class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <svg v-else class="w-6 h-6 text-slate-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                  </button>
                </div>
              </div>
              <textarea v-model="newReview.comment" rows="3" placeholder="Partagez votre avis sur ce livre..." class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
              <div class="mt-3 flex items-center justify-between">
                <span v-if="reviewMsg" class="text-sm" :class="reviewMsg.type === 'success' ? 'text-green-600' : 'text-red-600'">{{ reviewMsg.text }}</span>
                <button @click="submitReview" :disabled="submittingReview || !newReview.rating || !newReview.comment.trim()" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 text-sm font-medium transition-colors">
                  {{ submittingReview ? 'Envoi...' : 'Publier' }}
                </button>
              </div>
            </div>

            <div v-if="reviews.length === 0" class="text-center py-10 text-slate-500">
              <p class="text-sm">Aucun avis pour le moment. Soyez le premier !</p>
            </div>

            <div v-else class="space-y-4">
              <div v-for="review in reviews" :key="review.id" class="bg-white rounded-xl p-4 border border-slate-200">
                <div class="flex items-start gap-3">
                  <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">{{ (review.user?.name || 'A')[0] }}</div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between flex-wrap gap-1">
                      <p class="text-sm font-medium text-slate-900">{{ review.user?.name || 'Anonyme' }}</p>
                      <span class="text-xs text-slate-400">{{ formatDate(review.created_at) }}</span>
                    </div>
                    <div class="flex text-blue-500 mt-1">
                      <span v-for="i in 5" :key="i">
                        <svg v-if="i <= review.rating" class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg v-else class="w-4 h-4 text-slate-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                      </span>
                    </div>
                    <p v-if="review.comment" class="mt-2 text-sm text-slate-600">{{ review.comment }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>

  </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { booksAPI, reviewsAPI, favoritesAPI, borrowingsAPI } from '../../services/api';
import api from '../../services/api';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const book = ref(null);
const reviews = ref([]);
const chapters = ref([]);
const loading = ref(true);
const error = ref('');
const togglingFav = ref(false);
const isFav = ref(false);
const newReview = ref({ rating: 0, comment: '' });
const submittingReview = ref(false);
const reviewMsg = ref(null);
const borrowing = ref(false);
const hasActiveBorrow = ref(false);
const hasPendingRequest = ref(false);
const borrowMsg = ref(null);

function formatDate(date) {
  if (!date) return '';
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
}

async function fetchBook() {
  loading.value = true; error.value = '';
  try {
    const { data } = await booksAPI.show(route.params.id);
    book.value = data.book || data.data || data;
    reviews.value = book.value.reviews || [];
  } catch (e) {
    error.value = 'Impossible de charger les détails du livre.';
  } finally { loading.value = false; }
}

async function fetchChapters() {
  try {
    const res = await api.get(`/books/${route.params.id}/chapters`);
    chapters.value = res.data.chapters || [];
  } catch (e) { chapters.value = []; }
}

async function checkFavorite() {
  if (!authStore.isAuthenticated) return;
  try {
    const { data } = await favoritesAPI.check(route.params.id);
    isFav.value = data.is_favorited;
  } catch (e) {}
}

async function toggleFavorite() {
  togglingFav.value = true;
  try {
    const { data } = await favoritesAPI.toggle(book.value.id);
    isFav.value = data.is_favorited;
  } catch (e) {}
  finally { togglingFav.value = false; }
}

async function submitReview() {
  submittingReview.value = true; reviewMsg.value = null;
  try {
    const { data } = await reviewsAPI.create({ book_id: book.value.id, rating: newReview.value.rating, comment: newReview.value.comment });
    reviews.value.unshift(data.data || data);
    newReview.value = { rating: 0, comment: '' };
    reviewMsg.value = { type: 'success', text: 'Avis publié avec succès !' };
  } catch (e) {
    reviewMsg.value = { type: 'error', text: e.response?.data?.message || 'Échec de la publication.' };
  } finally { submittingReview.value = false; }
}

async function checkActiveBorrow() {
  if (!authStore.isAuthenticated || !book.value) return;
  try {
    const { data } = await borrowingsAPI.myBorrowings({ per_page: 50 });
    const items = data.data || data || [];
    hasActiveBorrow.value = items.some(b =>
      b.status === 'borrowed' && (b.book?.id === book.value.id || b.book_id === book.value.id)
    );
    hasPendingRequest.value = items.some(b =>
      b.status === 'pending' && (b.book?.id === book.value.id || b.book_id === book.value.id)
    );
  } catch (e) {}
}

async function borrowBook() {
  borrowing.value = true;
  borrowMsg.value = null;
  try {
    const { data } = await borrowingsAPI.borrow({ book_id: book.value.id });
    hasPendingRequest.value = true;
    borrowMsg.value = { type: 'success', text: data.message || 'Demande d\'emprunt envoyée.' };
  } catch (e) {
    borrowMsg.value = { type: 'error', text: e.response?.data?.message || "Erreur lors de la demande." };
  } finally { borrowing.value = false; }
}

onMounted(() => { fetchBook(); fetchChapters(); checkFavorite(); });
watch(() => book.value, (b) => { if (b) { checkActiveBorrow(); } });
</script>

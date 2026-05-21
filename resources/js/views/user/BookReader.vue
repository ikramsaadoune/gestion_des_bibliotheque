<template>
  <div class="min-h-screen bg-white">
    <header class="sticky top-0 z-20 bg-white border-b border-slate-200">
      <div class="max-w-4xl mx-auto px-4 flex items-center justify-between h-14">
        <div class="flex items-center space-x-3">
          <router-link :to="`/books/${book?.id}`" class="p-2 -ml-2 text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </router-link>
          <div v-if="book">
            <h1 class="text-sm font-semibold text-slate-900 truncate max-w-[200px] sm:max-w-xs">{{ book.title }}</h1>
            <p class="text-xs text-slate-500">{{ book.user?.name }}</p>
          </div>
        </div>
        <div v-if="isAuthor" class="flex items-center space-x-2">
          <button @click="showChapterForm = true" class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Chapitre</span>
          </button>
          <router-link :to="`/my-books/${book.id}/edit`" class="p-2 text-slate-400 hover:text-blue-600 rounded-lg hover:bg-slate-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
          </router-link>
        </div>
      </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 py-6">
      <div v-if="loading" class="text-center py-16">
        <div class="w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="text-sm text-slate-500 mt-3">Chargement...</p>
      </div>

      <div v-else-if="error" class="text-center py-16">
        <p class="text-red-600 font-medium">{{ error }}</p>
      </div>

      <template v-else>
        <div v-if="chapters.length > 0" class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Chapitres</h2>
            <span class="text-xs text-slate-400">{{ chapters.length }} chapitre{{ chapters.length > 1 ? 's' : '' }}</span>
          </div>
          <div class="space-y-2">
            <div v-for="(ch, i) in chapters" :key="ch.id"
              class="flex items-center gap-2 p-2 rounded-lg border transition-colors cursor-pointer"
              :class="activeChapter?.id === ch.id ? 'bg-blue-50 border-blue-300' : 'border-slate-200 hover:bg-slate-50'"
              @click="selectChapter(ch)">
              <span class="w-7 h-7 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold shrink-0">{{ i + 1 }}</span>
              <span class="text-sm font-medium text-slate-900 flex-1 truncate">{{ ch.title }}</span>
              <div v-if="isAuthor" class="flex items-center space-x-1 shrink-0" @click.stop>
                <button @click="editChapter(ch)" class="p-1.5 text-slate-400 hover:text-blue-600 rounded hover:bg-white transition-colors" title="Modifier">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button @click="confirmDeleteChapter(ch)" class="p-1.5 text-slate-400 hover:text-red-600 rounded hover:bg-white transition-colors" title="Supprimer">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-if="activeChapter" class="prose prose-slate max-w-none">
          <h2 class="text-xl font-bold text-slate-900 mb-4">{{ activeChapter.title }}</h2>
          <div class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ activeChapter.content }}</div>
        </div>

        <div v-else-if="book?.content" class="prose prose-slate max-w-none">
          <div class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ book.content }}</div>
        </div>

        <div v-else class="text-center py-16 text-slate-400">
          <p>Aucun contenu disponible pour ce livre.</p>
          <p v-if="isAuthor" class="text-sm mt-2">Cliquez sur "+ Chapitre" pour commencer à écrire.</p>
        </div>
      </template>
    </div>

    <div v-if="showChapterForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showChapterForm = false">
      <div class="bg-white rounded-xl p-6 max-w-lg w-full mx-4 shadow-xl max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-slate-900">{{ editingChapter ? 'Modifier le chapitre' : 'Ajouter un chapitre' }}</h3>
          <button @click="closeChapterForm" class="p-1 text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <div v-if="chapterError" class="mb-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ chapterError }}</div>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Titre du chapitre</label>
            <input v-model="chapterForm.title" type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Chapitre 1 : Le début" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Contenu</label>
            <textarea v-model="chapterForm.content" rows="12" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Écrivez votre chapitre ici..." required></textarea>
          </div>
          <div class="flex justify-end space-x-3 pt-2">
            <button @click="closeChapterForm" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium">Annuler</button>
            <button @click="submitChapter" :disabled="savingChapter" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50">
              <svg v-if="savingChapter" class="animate-spin -ml-1 mr-2 h-4 w-4 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              {{ savingChapter ? 'Enregistrement...' : (editingChapter ? 'Enregistrer' : 'Ajouter le chapitre') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showDeleteChapterModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showDeleteChapterModal = false">
      <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-900">Supprimer le chapitre</h3>
        <p class="text-sm text-slate-600 mt-2">Êtes-vous sûr de vouloir supprimer "{{ deleteChapterTarget?.title }}" ? Cette action est irréversible.</p>
        <div class="flex items-center justify-end space-x-3 mt-5">
          <button @click="showDeleteChapterModal = false" class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium">Annuler</button>
          <button @click="deleteChapterAction" :disabled="deletingChapter" class="px-4 py-2 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium disabled:opacity-50">
            {{ deletingChapter ? 'Suppression...' : 'Supprimer' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { booksAPI } from '../../services/api';
import api from '../../services/api';

const route = useRoute();
const authStore = useAuthStore();
const book = ref(null);
const chapters = ref([]);
const activeChapter = ref(null);
const loading = ref(true);
const error = ref('');
const showChapterForm = ref(false);
const chapterForm = ref({ title: '', content: '' });
const editingChapter = ref(null);
const savingChapter = ref(false);
const chapterError = ref('');
const showDeleteChapterModal = ref(false);
const deleteChapterTarget = ref(null);
const deletingChapter = ref(false);

const isAuthor = computed(() => authStore.isAuthenticated && book.value && book.value.user_id === authStore.user?.id);

function selectChapter(ch) {
  activeChapter.value = ch;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editChapter(ch) {
  editingChapter.value = ch;
  chapterForm.value = { title: ch.title, content: ch.content };
  showChapterForm.value = true;
  chapterError.value = '';
}

function closeChapterForm() {
  showChapterForm.value = false;
  chapterForm.value = { title: '', content: '' };
  editingChapter.value = null;
  chapterError.value = '';
}

function confirmDeleteChapter(ch) {
  deleteChapterTarget.value = ch;
  showDeleteChapterModal.value = true;
}

async function deleteChapterAction() {
  if (!deleteChapterTarget.value) return;
  deletingChapter.value = true;
  try {
    await api.delete(`/books/${book.value.id}/chapters/${deleteChapterTarget.value.id}`);
    chapters.value = chapters.value.filter(c => c.id !== deleteChapterTarget.value.id);
    if (activeChapter.value?.id === deleteChapterTarget.value.id) {
      activeChapter.value = null;
    }
    showDeleteChapterModal.value = false;
    deleteChapterTarget.value = null;
  } catch (e) {
    chapterError.value = 'Erreur lors de la suppression';
  } finally { deletingChapter.value = false; }
}

async function submitChapter() {
  if (!chapterForm.value.title.trim() || !chapterForm.value.content.trim()) return;
  savingChapter.value = true;
  chapterError.value = '';
  try {
    if (editingChapter.value) {
      const { data } = await api.put(`/books/${book.value.id}/chapters/${editingChapter.value.id}`, chapterForm.value);
      const updated = data.chapter || data;
      const idx = chapters.value.findIndex(c => c.id === editingChapter.value.id);
      if (idx !== -1) {
        chapters.value[idx] = updated;
      }
      if (activeChapter.value?.id === editingChapter.value.id) {
        activeChapter.value = updated;
      }
    } else {
      const { data } = await api.post(`/books/${book.value.id}/chapters`, chapterForm.value);
      const newChapter = data.chapter || data;
      chapters.value.push(newChapter);
      selectChapter(newChapter);
    }
    chapterForm.value = { title: '', content: '' };
    showChapterForm.value = false;
    editingChapter.value = null;
  } catch (e) {
    chapterError.value = e.response?.data?.message || "Erreur lors de l'enregistrement du chapitre";
  } finally { savingChapter.value = false; }
}

onMounted(async () => {
  try {
    const [bookRes, chaptersRes] = await Promise.all([
      booksAPI.show(route.params.id),
      api.get(`/books/${route.params.id}/chapters`).then(r => r.data),
    ]);
    book.value = bookRes.data.book || bookRes.data.data || bookRes.data;
    chapters.value = chaptersRes.chapters || [];

    const chapterId = route.query.chapter;
    if (chapterId && chapters.value.length > 0) {
      const found = chapters.value.find(c => String(c.id) === String(chapterId));
      if (found) activeChapter.value = found;
    }
  } catch (e) {
    error.value = 'Impossible de charger le livre.';
  } finally { loading.value = false; }
});
</script>
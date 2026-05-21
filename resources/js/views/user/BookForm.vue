<template>
  <div>
    <div class="mb-6">
      <h1 class="text-xl font-bold text-slate-900">{{ isEdit ? 'Modifier le livre' : 'Publier un livre' }}</h1>
      <p class="text-sm text-slate-500 mt-1">{{ isEdit ? 'Modifiez les informations de votre livre' : 'Partagez votre histoire avec le monde' }}</p>
    </div>

    <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">{{ error }}</div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
      <form @submit.prevent="confirmPublish" class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Image de couverture</label>
            <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-blue-400 transition-colors cursor-pointer" @click="$refs.fileInput.click()">
              <img v-if="previewUrl" :src="previewUrl" class="mx-auto w-32 h-44 object-cover rounded-lg shadow-md mb-2" />
              <div v-else class="py-6">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-xs text-slate-500">Ajouter une image</p>
              </div>
              <input ref="fileInput" type="file" @change="handleFileUpload" accept="image/*" class="hidden" />
            </div>
          </div>
          <div class="md:col-span-2 space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Titre</label>
              <input v-model="form.title" type="text" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Titre de votre livre" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
              <textarea v-model="form.description" rows="3" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" placeholder="Une courte description de votre livre..."></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Contenu</label>
              <textarea v-model="form.content" rows="12" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono" placeholder="Écrivez votre histoire ici..." required></textarea>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-5 border-t border-slate-100">
          <router-link to="/my-books" class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 text-sm">Annuler</router-link>
          <button type="submit" :disabled="submitting" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50 flex items-center text-sm">
            <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ submitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Publier') }}
          </button>
        </div>
      </form>
    </div>

    <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showConfirmModal = false">
      <div class="bg-white rounded-xl p-6 max-w-sm mx-4 shadow-xl">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mx-auto mb-4">
          <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900 text-center mb-2">{{ isEdit ? 'Confirmer la modification' : 'Confirmer la publication' }}</h3>
        <p class="text-sm text-slate-600 text-center mb-6">{{ isEdit ? 'Êtes-vous sûr de vouloir modifier ce livre ?' : 'Êtes-vous sûr de vouloir publier ce livre ? Il sera visible par tous les utilisateurs.' }}</p>
        <div class="flex justify-end space-x-3">
          <button @click="showConfirmModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50 font-medium">Annuler</button>
          <button @click="handleSubmit" :disabled="submitting" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium disabled:opacity-50">
            {{ submitting ? 'En cours...' : 'Confirmer' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { booksAPI } from '../../services/api';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => !!route.params.id);
const submitting = ref(false);
const error = ref('');
const previewUrl = ref(null);
const coverFile = ref(null);
const showConfirmModal = ref(false);
const form = reactive({ title: '', description: '', content: '' });

function handleFileUpload(e) {
  const file = e.target.files[0];
  if (file) {
    coverFile.value = file;
    previewUrl.value = URL.createObjectURL(file);
  }
}

onMounted(async () => {
  if (isEdit.value) {
    try {
      const { data } = await booksAPI.show(route.params.id);
      const b = data.book || data.data || data;
      form.title = b.title || '';
      form.description = b.description || '';
      form.content = b.content || '';
      if (b.cover_image) previewUrl.value = b.cover_image;
    } catch (e) { error.value = 'Impossible de charger le livre'; }
  }
});

function confirmPublish() {
  showConfirmModal.value = true;
}

async function handleSubmit() {
  submitting.value = true; error.value = '';
  try {
    const payload = new FormData();
    payload.append('title', form.title);
    payload.append('description', form.description);
    payload.append('content', form.content);
    if (coverFile.value) payload.append('cover_image', coverFile.value);
    if (isEdit.value) {
      payload.append('_method', 'PUT');
      await booksAPI.update(route.params.id, payload);
    } else {
      await booksAPI.create(payload);
    }
    router.push('/my-books');
  } catch (e) {
    error.value = e.response?.data?.message || "Erreur lors de l'enregistrement";
  } finally { submitting.value = false; showConfirmModal.value = false; }
}
</script>

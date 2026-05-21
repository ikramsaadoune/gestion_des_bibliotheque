<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isEdit ? 'Modifier le livre' : 'Ajouter un livre' }}</h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ isEdit ? 'Modifiez les informations du livre' : 'Ajoutez un nouveau livre au catalogue' }}</p>
    </div>

    <div v-if="error" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-xl text-sm">{{ error }}</div>
    <div v-if="validationErrors.length" class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
      <p v-for="(err, i) in validationErrors" :key="i" class="text-sm text-red-600 dark:text-red-400">{{ err }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 lg:p-8">
      <form @submit.prevent="handleSubmit">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left: Cover + PDF -->
          <div class="lg:col-span-1 space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Image de couverture</label>
              <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-400 cursor-pointer" @click="$refs.fileInput.click()">
                <img v-if="previewUrl" :src="previewUrl" class="mx-auto w-40 h-56 object-cover rounded-lg shadow-md mb-3" />
                <div v-else class="py-8">
                  <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Cliquez pour ajouter une image</p>
                </div>
                <input ref="fileInput" type="file" @change="handleFileUpload" accept="image/*" class="hidden" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Fichier PDF</label>
              <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 text-center hover:border-indigo-400 cursor-pointer" @click="$refs.pdfInput.click()">
                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ pdfFile ? pdfFile.name : 'Ajouter un PDF' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF uniquement, max 100 Mo</p>
                <input ref="pdfInput" type="file" @change="e => pdfFile = e.target.files[0]" accept=".pdf" class="hidden" />
              </div>
            </div>
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
              <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Publié</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Visible par les utilisateurs</p>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="form.is_published" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
              </label>
            </div>
          </div>

          <!-- Right: Fields -->
          <div class="lg:col-span-2 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Titre *</label>
                <input v-model="form.title" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Auteur</label>
                <input v-model="form.author_name" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" placeholder="Nom de l'auteur" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catégorie</label>
                <select v-model="form.category_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                  <option value="">Sélectionner une catégorie</option>
                  <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">ISBN</label>
                <input v-model="form.isbn" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" placeholder="978-3-16-148410-0" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Langue</label>
                <select v-model="form.language" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                  <option value="">Sélectionner</option>
                  <option value="Français">Français</option>
                  <option value="Anglais">Anglais</option>
                  <option value="Arabe">Arabe</option>
                  <option value="Espagnol">Espagnol</option>
                  <option value="Allemand">Allemand</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Éditeur</label>
                <input v-model="form.publisher" type="text" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Année de publication</label>
                <input v-model="form.publication_year" type="number" min="1000" max="2099" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre de pages</label>
                <input v-model="form.pages" type="number" min="1" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantité totale</label>
                <input v-model="form.quantity_total" type="number" min="1" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
              <textarea v-model="form.description" rows="4" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" placeholder="Description du livre..."></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contenu</label>
              <textarea v-model="form.content" rows="6" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" placeholder="Contenu du livre..."></textarea>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-8 border-t border-gray-100 dark:border-gray-700 mt-8">
          <router-link to="/admin/books" class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">Annuler</router-link>
          <button type="submit" :disabled="submitting" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium disabled:opacity-50 flex items-center text-sm">
            <svg v-if="submitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            {{ submitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer le livre') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { adminBooksAPI, categoriesAPI } from '../../services/api';

const route = useRoute();
const router = useRouter();
const isEdit = computed(() => !!route.params.id);
const submitting = ref(false);
const error = ref('');
const validationErrors = ref([]);
const previewUrl = ref(null);
const coverFile = ref(null);
const pdfFile = ref(null);
const categories = ref([]);
const form = reactive({
  title: '', author_name: '', category_id: '', isbn: '', language: 'Français',
  publisher: '', publication_year: '', pages: '', quantity_total: 1,
  is_published: false, description: '', content: '',
});

function handleFileUpload(e) {
  const file = e.target.files[0];
  if (file) { coverFile.value = file; previewUrl.value = URL.createObjectURL(file); }
}

onMounted(async () => {
  try {
    const { data: catData } = await categoriesAPI.allAdmin({ per_page: 200 });
    categories.value = catData?.data || catData || [];
  } catch (e) { console.error(e); }

  if (isEdit.value) {
    try {
      const { data } = await adminBooksAPI.show(route.params.id);
      const book = data.book || data.data || data;
      Object.keys(form).forEach(k => { if (book[k] !== undefined) form[k] = book[k]; });
      if (book.cover_image) previewUrl.value = book.cover_image;
    } catch (e) { error.value = 'Impossible de charger le livre'; }
  }
});

async function handleSubmit() {
  submitting.value = true; error.value = ''; validationErrors.value = [];
  try {
    const payload = new FormData();
    Object.entries(form).forEach(([k, v]) => {
      if (v !== '' && v !== null) payload.append(k, v);
    });
    if (coverFile.value) payload.append('cover_image', coverFile.value);
    if (pdfFile.value) payload.append('pdf_file', pdfFile.value);
    if (isEdit.value) {
      payload.append('_method', 'PUT');
      await adminBooksAPI.update(route.params.id, payload);
    } else {
      await adminBooksAPI.create(payload);
    }
    router.push('/admin/books');
  } catch (e) {
    if (e.response?.status === 422) {
      validationErrors.value = Object.values(e.response.data.errors).flat();
    } else {
      error.value = e.response?.data?.message || "Erreur lors de l'enregistrement";
    }
  } finally { submitting.value = false; }
}
</script>

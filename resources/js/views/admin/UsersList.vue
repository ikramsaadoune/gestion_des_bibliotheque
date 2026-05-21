<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Utilisateurs</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez les comptes utilisateurs</p>
      </div>
      <router-link to="/admin/users/create" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Ajouter un utilisateur
      </router-link>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
      <div class="p-4 border-b border-gray-100 dark:border-gray-700">
        <div class="relative w-full sm:w-72">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input v-model="search" @input="debouncedSearch" type="text" placeholder="Rechercher un utilisateur..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm" />
        </div>
      </div>

      <div v-if="loading" class="p-8">
        <div class="space-y-4">
          <div v-for="i in 5" :key="i" class="flex items-center space-x-4 animate-pulse">
            <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
              <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4"></div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="users.length === 0" class="flex flex-col items-center justify-center py-16">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">Aucun utilisateur trouvé</p>
        <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Ajoutez votre premier utilisateur</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Utilisateur</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Email</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Rôle</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Statut</th>
              <th class="text-left px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Inscription</th>
              <th class="text-right px-4 py-3.5 text-gray-500 dark:text-gray-400 font-medium">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center space-x-3">
                  <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + (user.first_name || user.name) + '&background=6366f1&color=fff'" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700" />
                  <span class="font-medium text-gray-900 dark:text-white">{{ user.first_name ? user.first_name + ' ' + user.last_name : user.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ user.email }}</td>
              <td class="px-4 py-3">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="user.role === 'admin' ? 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400'">{{ user.role === 'admin' ? 'Admin' : 'Utilisateur' }}</span>
              </td>
              <td class="px-4 py-3">
                <span class="px-2.5 py-1 text-xs font-medium rounded-full" :class="user.is_active ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400'">{{ user.is_active ? 'Actif' : 'Bloqué' }}</span>
              </td>
              <td class="px-4 py-3 text-gray-400 dark:text-gray-500">{{ user.created_at ? new Date(user.created_at).toLocaleDateString('fr-FR') : '-' }}</td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <button @click="toggleActive(user)" class="p-2 rounded-lg transition-colors" :class="user.is_active ? 'text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20' : 'text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'" :title="user.is_active ? 'Bloquer' : 'Activer'">
                    <svg v-if="user.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                  <router-link :to="'/admin/users/' + user.id + '/edit'" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20" title="Modifier">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </router-link>
                  <button @click="confirmDelete(user)" class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="Supprimer">
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
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center mb-2">Supprimer l'utilisateur</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Êtes-vous sûr de vouloir supprimer "{{ deleteUser?.first_name ? deleteUser.first_name + ' ' + deleteUser.last_name : deleteUser?.name }}" ?</p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium">Annuler</button>
          <button @click="deleteUserAction" class="flex-1 px-4 py-2.5 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 font-medium">Supprimer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { adminUsersAPI } from '../../services/api';
import Pagination from '../../components/Pagination.vue';

const users = ref([]);
const meta = ref(null);
const search = ref('');
const loading = ref(false);
const showDeleteModal = ref(false);
const deleteUser = ref(null);
let searchTimeout;

function debouncedSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(fetchUsers, 300);
}

async function fetchUsers(page = 1) {
  loading.value = true;
  try {
    const { data } = await adminUsersAPI.all({ search: search.value, page });
    users.value = data.data || data;
    meta.value = data.meta || null;
  } catch (e) { console.error(e); } finally { loading.value = false; }
}

function goToPage(page) {
  fetchUsers(page);
}

async function toggleActive(user) {
  try {
    await adminUsersAPI.toggleActive(user.id);
    user.is_active = !user.is_active;
  } catch (e) { console.error(e); }
}

function confirmDelete(user) {
  deleteUser.value = user;
  showDeleteModal.value = true;
}

async function deleteUserAction() {
  try {
    await adminUsersAPI.delete(deleteUser.value.id);
    users.value = users.value.filter(u => u.id !== deleteUser.value.id);
    showDeleteModal.value = false;
    deleteUser.value = null;
  } catch (e) { console.error(e); }
}

onMounted(fetchUsers);
</script>

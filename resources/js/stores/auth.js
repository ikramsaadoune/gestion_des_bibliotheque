import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { authAPI } from '../services/api';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token') || null);
  const loading = ref(false);
  const initialized = ref(false);

  const isAuthenticated = computed(() => !!token.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  function setAuth(data) {
    token.value = data.token;
    localStorage.setItem('token', data.token);
    user.value = data.user;
  }

  function clearAuth() {
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
  }

  async function login(credentials) {
    loading.value = true;
    try {
      const { data } = await authAPI.login(credentials);
      setAuth(data);
      return data;
    } finally {
      loading.value = false;
    }
  }

  async function register(userData) {
    loading.value = true;
    try {
      const { data } = await authAPI.register(userData);
      setAuth(data);
      return data;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      await authAPI.logout();
    } catch {
      // ignore
    }
    clearAuth();
  }

  async function fetchUser() {
    if (!token.value) {
      initialized.value = true;
      return;
    }
    loading.value = true;
    try {
      const { data } = await authAPI.user();
      user.value = data.user;
    } catch {
      clearAuth();
    } finally {
      loading.value = false;
      initialized.value = true;
    }
  }

  async function updateProfile(profileData) {
    const { data } = await authAPI.updateProfile(profileData);
    user.value = data.user;
    return data;
  }

  async function forgotPassword(email) {
    return await authAPI.forgotPassword({ email });
  }

  async function resetPassword(form) {
    return await authAPI.resetPassword(form);
  }

  return {
    user,
    token,
    loading,
    initialized,
    isAuthenticated,
    isAdmin,
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
    forgotPassword,
    resetPassword,
  };
});

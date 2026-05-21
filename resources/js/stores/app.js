import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useAppStore = defineStore('app', () => {
  const sidebarOpen = ref(false);
  const darkMode = ref(localStorage.getItem('darkMode') === 'true');

  const isDarkMode = computed(() => darkMode.value);

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
  }

  function toggleDarkMode() {
    darkMode.value = !darkMode.value;
    localStorage.setItem('darkMode', darkMode.value);
    if (darkMode.value) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }

  return {
    sidebarOpen,
    darkMode,
    isDarkMode,
    toggleSidebar,
    toggleDarkMode,
  };
});

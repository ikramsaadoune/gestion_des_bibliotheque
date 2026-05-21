<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 transition-all hover:shadow-md">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ label }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ value }}</p>
      </div>
      <div :class="[colorClasses[color] || colorClasses.blue]" class="p-3 rounded-lg">
        <span v-html="icons[icon] || icons.default" class="w-6 h-6"></span>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  label: String, value: [String, Number], color: { type: String, default: 'blue' }, icon: { type: String, default: 'default' }
});
const colorClasses = {
  blue: 'bg-blue-100 text-blue-600 dark:bg-blue-900/50 dark:text-blue-400',
  indigo: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400',
  amber: 'bg-amber-100 text-amber-600 dark:bg-amber-900/50 dark:text-amber-400',
  purple: 'bg-purple-100 text-purple-600 dark:bg-purple-900/50 dark:text-purple-400',
  green: 'bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400',
  red: 'bg-red-100 text-red-600 dark:bg-red-900/50 dark:text-red-400',
  teal: 'bg-teal-100 text-teal-600 dark:bg-teal-900/50 dark:text-teal-400',
  pink: 'bg-pink-100 text-pink-600 dark:bg-pink-900/50 dark:text-pink-400',
};
const icons = {
  users: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
  book: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
  default: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
};
</script>

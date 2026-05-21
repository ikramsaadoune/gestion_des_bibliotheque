import { createRouter, createWebHashHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/auth/Login.vue'),
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/auth/Register.vue'),
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: () => import('../views/auth/ForgotPassword.vue'),
  },
  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: () => import('../views/auth/ResetPassword.vue'),
  },
  {
    path: '/',
    meta: { requiresAuth: true },
    component: () => import('../layouts/UserLayout.vue'),
    children: [
      {
        path: '',
        redirect: '/books',
      },
      {
        path: 'books',
        name: 'BooksBrowse',
        component: () => import('../views/user/BooksBrowse.vue'),
      },
      {
        path: 'books/:id',
        name: 'UserBookDetail',
        component: () => import('../views/user/BookDetail.vue'),
      },
      {
        path: 'books/:id/read',
        name: 'BookReader',
        component: () => import('../views/user/BookReader.vue'),
      },
      {
        path: 'profile',
        name: 'UserProfile',
        component: () => import('../views/user/UserProfile.vue'),
      },
      {
        path: 'favorites',
        name: 'MyFavorites',
        component: () => import('../views/user/MyFavorites.vue'),
      },
      {
        path: 'my-borrowings',
        name: 'MyBorrowings',
        component: () => import('../views/user/MyBorrowings.vue'),
      },
      {
        path: 'my-books',
        redirect: '/my-borrowings',
      },
      {
        path: 'my-fines',
        name: 'MyFines',
        component: () => import('../views/user/MyFines.vue'),
      },
      {
        path: 'notifications',
        name: 'UserNotifications',
        component: () => import('../views/user/UserNotifications.vue'),
      },
    ],
  },
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    meta: { requiresAdmin: true },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: () => import('../views/admin/AdminDashboard.vue'),
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: () => import('../views/admin/UsersList.vue'),
      },
      {
        path: 'users/create',
        name: 'AdminUserCreate',
        component: () => import('../views/admin/UserForm.vue'),
      },
      {
        path: 'users/:id/edit',
        name: 'AdminUserEdit',
        component: () => import('../views/admin/UserForm.vue'),
      },
      {
        path: 'books',
        name: 'AdminBooks',
        component: () => import('../views/admin/BooksList.vue'),
      },
      {
        path: 'books/create',
        name: 'AdminBookCreate',
        component: () => import('../views/admin/BookForm.vue'),
      },
      {
        path: 'books/:id',
        name: 'AdminBookDetail',
        component: () => import('../views/admin/BookDetail.vue'),
      },
      {
        path: 'books/:id/edit',
        name: 'AdminBookEdit',
        component: () => import('../views/admin/BookForm.vue'),
      },
      {
        path: 'borrowings',
        name: 'AdminBorrowings',
        component: () => import('../views/admin/BorrowingsList.vue'),
      },
      {
        path: 'categories',
        name: 'AdminCategories',
        component: () => import('../views/admin/CategoriesList.vue'),
      },
      {
        path: 'reviews',
        name: 'AdminReviews',
        component: () => import('../views/admin/ReviewsList.vue'),
      },
      {
        path: 'fines',
        name: 'AdminFines',
        component: () => import('../views/admin/FinesList.vue'),
      },
      {
        path: 'notifications',
        name: 'AdminNotifications',
        component: () => import('../views/admin/NotificationsList.vue'),
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: () => import('../views/admin/Settings.vue'),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  if (!authStore.initialized) {
    await authStore.fetchUser();
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return next('/login');
  }

  if (to.meta.requiresAdmin) {
    if (!authStore.isAuthenticated || !authStore.isAdmin) {
      return next('/login');
    }
  }

  if (to.name === 'Login' && authStore.isAuthenticated) {
    return next(authStore.isAdmin ? '/admin' : '/');
  }

  next();
});

export default router;
